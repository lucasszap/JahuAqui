<?php
session_start();
include("../config/conexao.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../src/pages/login.html");
    exit;
}

$stmt = $conn->prepare("SELECT IS_ADMIN FROM USUARIOS WHERE ID = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || empty($user['IS_ADMIN'])) {
    http_response_code(403);
    die("Acesso negado. Você não tem permissão para acessar esta página.");
}

$mensagemAcao = null;
$tipoMensagem = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'], $_POST['servico_id'])) {

    $acao = $_POST['acao'];
    $id   = (int) $_POST['servico_id'];

    if (in_array($acao, ['aprovado', 'rejeitado'])) {

        $upd = $conn->prepare("UPDATE SERVICOS SET STATUS = ? WHERE ID = ?");
        $upd->bind_param("si", $acao, $id);
        $upd->execute();
        $upd->close();

        $mensagemAcao = $acao === 'aprovado'
            ? 'Serviço aprovado com sucesso!'
            : 'Serviço rejeitado.';

        $tipoMensagem = $acao === 'aprovado'
            ? 'success'
            : 'warning';

    } elseif ($acao === 'excluir') {

        $sel = $conn->prepare("SELECT FOTO FROM SERVICOS WHERE ID = ?");
        $sel->bind_param("i", $id);
        $sel->execute();

        $row = $sel->get_result()->fetch_assoc();
        $sel->close();

        $del = $conn->prepare("DELETE FROM SERVICOS WHERE ID = ?");
        $del->bind_param("i", $id);
        $del->execute();
        $del->close();

        // Corrigido caminho da foto
        if (!empty($row['FOTO'])) {

            $caminhoFoto = $_SERVER['DOCUMENT_ROOT'] . $row['FOTO'];

            if (file_exists($caminhoFoto)) {
                unlink($caminhoFoto);
            }
        }

        $mensagemAcao = 'Serviço excluído permanentemente.';
        $tipoMensagem = 'danger';
    }
}

$pendentes = $conn->query(
    "SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA,
            S.TELEFONE, S.FOTO,
            U.NOME AS PRESTADOR,
            U.EMAIL AS EMAIL_PRESTADOR,
            S.CRIADO_EM

     FROM SERVICOS S
     JOIN USUARIOS U ON U.ID = S.USUARIO_ID

     WHERE S.STATUS = 'pendente'

     ORDER BY S.CRIADO_EM ASC"
);

$aprovados = $conn->query(
    "SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA,
            S.TELEFONE, S.FOTO,
            U.NOME AS PRESTADOR,
            U.EMAIL AS EMAIL_PRESTADOR,
            S.CRIADO_EM

     FROM SERVICOS S
     JOIN USUARIOS U ON U.ID = S.USUARIO_ID

     WHERE S.STATUS = 'aprovado'

     ORDER BY S.CRIADO_EM DESC"
);

$totais = $conn->query(
    "SELECT
        SUM(STATUS='pendente')  AS pendentes,
        SUM(STATUS='aprovado')  AS aprovados,
        SUM(STATUS='rejeitado') AS rejeitados
     FROM SERVICOS"
)->fetch_assoc();

$CORES_CATEGORIA = [
    'Manutenção' => 'primary',
    'Logística'  => 'warning',
    'Limpeza'    => 'info',
    'Beleza'     => 'success',
    'Saúde'      => 'danger',
    'Outros'     => 'secondary',
];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>

<title>ADM | JahuAqui</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>

<style>

:root {
    --purple-dark:  #240046;
    --purple-main:  #6c3fc5;
    --bg-light:     #f1f2f6;
    --bg-white:     #ffffff;
    --text-dark:    #1a1a2e;
    --text-muted:   #555;
    --radius-md:    12px;
    --radius-lg:    16px;
    --radius-pill:  30px;
    --shadow-card:  0 4px 16px rgba(0,0,0,.08);
    --shadow-hover: 0 10px 28px rgba(108,63,197,.18);
    --transition:   0.25s ease;
}

body {
    background-color: var(--bg-light);
    font-family: 'Montserrat', Arial, sans-serif;
    color: var(--text-dark);
    margin: 0;
}

.painel-header {
    background: var(--purple-dark);
    padding: 1.2rem 0;
    margin-bottom: 2rem;
}

.painel-header h4 {
    color: #fff;
    font-weight: 800;
}

.painel-header small {
    color: rgba(255,255,255,.6);
}

.btn-voltar {
    border: 1px solid rgba(255,255,255,.4);
    color: #fff;
    border-radius: var(--radius-pill);
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
}

.btn-voltar:hover {
    background: rgba(255,255,255,.15);
    color: #fff;
}

.stat-card {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    padding: 1.4rem 1.6rem;
    box-shadow: var(--shadow-card);
}

.stat-card .numero {
    font-size: 2.2rem;
    font-weight: 800;
}

.stat-card .label {
    font-size: 13px;
    color: var(--text-muted);
}

.nav-tabs .nav-link.active {
    color: var(--purple-dark);
    border-bottom: 2px solid var(--purple-dark);
}

.tab-content {
    background: var(--bg-white);
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-card);
}

.card-servico {
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-card);
    transition: var(--transition);
}

.card-servico:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

.card-img-top {
    height: 220px;
    width: 100%;
    object-fit: cover;
    background: #ececec;
}

.placeholder-img {
    height: 220px;
    background: #ececec;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
}

.btn-aprovar {
    background: #198754;
    color: #fff;
    border: none;
    border-radius: var(--radius-pill);
}

.btn-rejeitar {
    background: #dc3545;
    color: #fff;
    border: none;
    border-radius: var(--radius-pill);
}

.btn-excluir {
    background: #fff0f0;
    border: 1px solid #dc3545;
    color: #dc3545;
    border-radius: var(--radius-pill);
}

</style>
</head>

<body>

<div class="painel-header">
    <div class="container d-flex justify-content-between align-items-center">

        <div>
            <h4 class="mb-0">🛡️ Painel Administrativo</h4>
            <small>JahuAqui — Moderação de Serviços</small>
        </div>

        <a href="index.php" class="btn-voltar">
            ← Voltar ao site
        </a>

    </div>
</div>

<div class="container pb-5">

<?php if ($mensagemAcao): ?>

<div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show">
    <?= htmlspecialchars($mensagemAcao) ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php endif; ?>

<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="stat-card">
            <div class="numero text-warning">
                <?= (int)$totais['pendentes'] ?>
            </div>

            <div class="label">
                ⏳ Aguardando aprovação
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="numero text-success">
                <?= (int)$totais['aprovados'] ?>
            </div>

            <div class="label">
                ✅ Aprovados
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="numero text-danger">
                <?= (int)$totais['rejeitados'] ?>
            </div>

            <div class="label">
                ❌ Rejeitados
            </div>
        </div>
    </div>

</div>

<div class="row g-4">

<?php while ($s = $pendentes->fetch_assoc()):

    $cor = $CORES_CATEGORIA[$s['CATEGORIA']] ?? 'secondary';

    // CORREÇÃO PRINCIPAL
    $foto = !empty($s['FOTO']) ? $s['FOTO'] : null;

?>

<div class="col-md-4">

<div class="card card-servico h-100">

<?php if ($foto): ?>

<img
    src="<?= htmlspecialchars($foto) ?>"
    class="card-img-top"
    alt="Foto do serviço"
>

<?php else: ?>

<div class="placeholder-img">
    🔧
</div>

<?php endif; ?>

<div class="card-body">

<span class="badge bg-<?= $cor ?> mb-2">
    <?= htmlspecialchars($s['CATEGORIA']) ?>
</span>

<h5 class="card-title">
    <?= htmlspecialchars($s['NOME']) ?>
</h5>

<p class="card-text small">
    <?= htmlspecialchars($s['DESCRICAO']) ?>
</p>

<p class="small text-muted mb-1">
    👤 <strong><?= htmlspecialchars($s['PRESTADOR']) ?></strong>
</p>

<p class="small text-muted mb-1">
    📧 <?= htmlspecialchars($s['EMAIL_PRESTADOR']) ?>
</p>

<p class="small text-muted">
    📞 <?= htmlspecialchars($s['TELEFONE']) ?>
</p>

</div>

<div class="card-footer d-flex flex-column gap-2">

<form method="POST">

    <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
    <input type="hidden" name="acao" value="aprovado">

    <button type="submit" class="btn btn-aprovar w-100">
        ✔ Aprovar
    </button>

</form>

<form method="POST">

    <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
    <input type="hidden" name="acao" value="rejeitado">

    <button type="submit" class="btn btn-rejeitar w-100">
        ✖ Rejeitar
    </button>

</form>

<form method="POST">

    <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
    <input type="hidden" name="acao" value="excluir">

    <button type="submit" class="btn btn-excluir w-100">
        🗑 Excluir
    </button>

</form>

</div>

</div>
</div>

<?php endwhile; ?>

</div>

<hr class="my-5">

<h5 class="fw-bold mb-4" style="color:var(--purple-dark)">✅ Serviços Aprovados</h5>

<div class="row g-4">

<?php

if ($aprovados->num_rows === 0): ?>
    <div class="col-12 text-center py-4" style="color:var(--text-muted)">
        Nenhum serviço aprovado ainda.
    </div>
<?php endif;

while ($s = $aprovados->fetch_assoc()):
    $cor  = $CORES_CATEGORIA[$s['CATEGORIA']] ?? 'secondary';
    $foto = !empty($s['FOTO']) ? $s['FOTO'] : null;
?>

<div class="col-md-4">
<div class="card card-servico h-100">

<?php if ($foto): ?>
    <img src="<?= htmlspecialchars($foto) ?>" class="card-img-top" alt="Foto do serviço">
<?php else: ?>
    <div class="placeholder-img">🔧</div>
<?php endif; ?>

<div class="card-body">
    <span class="badge bg-<?= $cor ?> mb-2"><?= htmlspecialchars($s['CATEGORIA']) ?></span>
    <h5 class="card-title"><?= htmlspecialchars($s['NOME']) ?></h5>
    <p class="card-text small"><?= htmlspecialchars($s['DESCRICAO']) ?></p>
    <p class="small text-muted mb-1">👤 <strong><?= htmlspecialchars($s['PRESTADOR']) ?></strong></p>
    <p class="small text-muted mb-1">📧 <?= htmlspecialchars($s['EMAIL_PRESTADOR']) ?></p>
    <p class="small text-muted">📞 <?= htmlspecialchars($s['TELEFONE']) ?></p>
</div>

<div class="card-footer">
    <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este serviço?')">
        <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
        <input type="hidden" name="acao" value="excluir">
        <button type="submit" class="btn btn-excluir w-100">🗑 Excluir</button>
    </form>
</div>

</div>
</div>

<?php endwhile; ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>