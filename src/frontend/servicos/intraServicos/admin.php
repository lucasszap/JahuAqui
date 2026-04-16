<?php
session_start();
include("../../login/conexao.php");

// so permite pra quem fez login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login/login.html");
    exit;
}

// verifica se e adm
$stmt = $conn->prepare("SELECT IS_ADMIN FROM USUARIOS WHERE ID = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || empty($user['IS_ADMIN'])) {
    http_response_code(403);
    die("Acesso negado. Você não tem permissão para acessar esta página.");
}

$mensagemAcao  = null;
$tipoMensagem  = 'info'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'], $_POST['servico_id'])) {
    $acao = $_POST['acao'];
    $id   = (int) $_POST['servico_id'];

    if (in_array($acao, ['aprovado', 'rejeitado'])) {
        $upd = $conn->prepare("UPDATE SERVICOS SET STATUS = ? WHERE ID = ?");
        $upd->bind_param("si", $acao, $id);
        $upd->execute();
        $upd->close();
        $mensagemAcao = $acao === 'aprovado' ? 'Serviço aprovado com sucesso!' : 'Serviço rejeitado.';
        $tipoMensagem = $acao === 'aprovado' ? 'success' : 'warning';

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

        if (!empty($row['FOTO'])) {
            $caminhoFoto = __DIR__ . '/' . $row['FOTO'];
            if (file_exists($caminhoFoto)) {
                unlink($caminhoFoto);
            }
        }

        $mensagemAcao = 'Serviço excluído permanentemente.';
        $tipoMensagem = 'danger';
    }
}

$pendentes = $conn->query(
    "SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA, S.TELEFONE, S.FOTO,
            U.NOME AS PRESTADOR, U.EMAIL AS EMAIL_PRESTADOR, S.CRIADO_EM
     FROM SERVICOS S
     JOIN USUARIOS U ON U.ID = S.USUARIO_ID
     WHERE S.STATUS = 'pendente'
     ORDER BY S.CRIADO_EM ASC"
);

$aprovados = $conn->query(
    "SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA, S.TELEFONE, S.FOTO,
            U.NOME AS PRESTADOR, U.EMAIL AS EMAIL_PRESTADOR, S.CRIADO_EM
     FROM SERVICOS S
     JOIN USUARIOS U ON U.ID = S.USUARIO_ID
     WHERE S.STATUS = 'aprovado'
     ORDER BY S.CRIADO_EM DESC"
);

$totais = $conn->query(
    "SELECT
        SUM(STATUS = 'pendente')  AS pendentes,
        SUM(STATUS = 'aprovado')  AS aprovados,
        SUM(STATUS = 'rejeitado') AS rejeitados
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
<html lang="pt-br" data-bs-theme="dark">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>ADM | JahuAqui</title>
  <link href="../../../presets/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body { background-color: #0d0d1a; }

    .painel-header {
      background: linear-gradient(135deg, #1a1a2e, #16213e);
      border-bottom: 1px solid #333;
      padding: 1.5rem 0;
      margin-bottom: 2rem;
    }

    .stat-card {
      border-radius: 12px;
      padding: 1.2rem 1.5rem;
      border: 1px solid #333;
    }

    .stat-card .numero {
      font-size: 2rem;
      font-weight: 700;
      line-height: 1;
    }

    .card-servico {
      border: 1px solid #333;
      border-radius: 12px;
      overflow: hidden;
      transition: border-color 0.2s;
    }

    .card-servico:hover { border-color: #555; }

    .card-img-top {
      height: 160px;
      object-fit: cover;
    }

    .placeholder-img {
      height: 160px;
      background: #1e1e2e;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
    }

    .btn-aprovar  { background: #198754; border: none; color: #fff; }
    .btn-aprovar:hover  { background: #157347; color: #fff; }
    .btn-rejeitar { background: #dc3545; border: none; color: #fff; }
    .btn-rejeitar:hover { background: #bb2d3b; color: #fff; }
    .btn-excluir  { background: #6f1a23; border: 1px solid #dc3545; color: #ff8a8a; }
    .btn-excluir:hover  { background: #dc3545; color: #fff; }

    .nav-tabs .nav-link          { color: #aaa; border-color: transparent; }
    .nav-tabs .nav-link.active   { color: #fff; background: #1e1e2e; border-color: #444 #444 #1e1e2e; }
    .tab-content                 { background: #1e1e2e; border: 1px solid #444;
                                   border-top: none; border-radius: 0 0 8px 8px; padding: 1.5rem; }

    .empty-state {
      text-align: center;
      padding: 4rem 1rem;
      color: #6c757d;
    }
    .empty-state .icon { font-size: 4rem; margin-bottom: 1rem; }
  </style>
</head>
<body>

<div class="painel-header">
  <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h4 class="mb-0 fw-bold">🛡️ Painel Administrativo</h4>
      <small class="text-muted">JahuAqui — Moderação de Serviços</small>
    </div>
    <a href="index.php" class="btn btn-outline-secondary btn-sm">← Voltar ao site</a>
  </div>
</div>

<div class="container pb-5">

  <?php if ($mensagemAcao): ?>
    <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show mb-4" role="alert">
      <?= htmlspecialchars($mensagemAcao) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row g-3 mb-5">
    <div class="col-md-4">
      <div class="stat-card bg-body-tertiary">
        <div class="numero text-warning"><?= (int)$totais['pendentes'] ?></div>
        <div class="text-muted mt-1">⏳ Aguardando aprovação</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card bg-body-tertiary">
        <div class="numero text-success"><?= (int)$totais['aprovados'] ?></div>
        <div class="text-muted mt-1">✅ Aprovados</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card bg-body-tertiary">
        <div class="numero text-danger"><?= (int)$totais['rejeitados'] ?></div>
        <div class="text-muted mt-1">❌ Rejeitados</div>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-0" id="tabServicos" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pendentes">
        ⏳ Pendentes
        <span class="badge bg-warning text-dark ms-1"><?= (int)$totais['pendentes'] ?></span>
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-aprovados">
        ✅ Aprovados
        <span class="badge bg-success ms-1"><?= (int)$totais['aprovados'] ?></span>
      </button>
    </li>
  </ul>

  <div class="tab-content">

    <div class="tab-pane fade show active" id="tab-pendentes">
      <?php if ($pendentes->num_rows === 0): ?>
        <div class="empty-state">
          <div class="icon">🎉</div>
          <h5>Tudo em dia!</h5>
          <p>Nenhum serviço aguardando aprovação no momento.</p>
        </div>
      <?php else: ?>
        <div class="row g-4 pt-2">
        <?php while ($s = $pendentes->fetch_assoc()):
          $cor = $CORES_CATEGORIA[$s['CATEGORIA']] ?? 'secondary';
        ?>
          <div class="col-md-4">
            <div class="card card-servico h-100 bg-body-tertiary">
              <?php if ($s['FOTO']): ?>
                <img src="<?= htmlspecialchars($s['FOTO']) ?>" class="card-img-top" alt="Foto do serviço">
              <?php else: ?>
                <div class="placeholder-img">🔧</div>
              <?php endif; ?>
              <div class="card-body">
                <span class="badge bg-<?= $cor ?> mb-2"><?= htmlspecialchars($s['CATEGORIA']) ?></span>
                <h5 class="card-title mb-1"><?= htmlspecialchars($s['NOME']) ?></h5>
                <p class="card-text small text-body-secondary mb-2"><?= htmlspecialchars($s['DESCRICAO']) ?></p>
                <p class="small text-muted mb-1">👤 <strong><?= htmlspecialchars($s['PRESTADOR']) ?></strong></p>
                <p class="small text-muted mb-1">📧 <?= htmlspecialchars($s['EMAIL_PRESTADOR']) ?></p>
                <p class="small text-muted mb-0">📞 <?= htmlspecialchars($s['TELEFONE']) ?></p>
              </div>
              <div class="card-footer border-top border-secondary d-flex gap-2 pt-3 pb-3 flex-wrap">
                <!-- aprovar -->
                <form method="POST" class="flex-fill">
                  <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
                  <input type="hidden" name="acao" value="aprovado">
                  <button type="submit" class="btn btn-aprovar w-100 fw-semibold">✔ Aprovar</button>
                </form>
                <!-- rejeitar -->
                <form method="POST" class="flex-fill"
                      onsubmit="return confirm('Rejeitar este serviço?')">
                  <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
                  <input type="hidden" name="acao" value="rejeitado">
                  <button type="submit" class="btn btn-rejeitar w-100 fw-semibold">✖ Rejeitar</button>
                </form>
                <!-- excluir -->
                <form method="POST" class="w-100"
                      onsubmit="return confirm('Excluir PERMANENTEMENTE este serviço? Esta ação não pode ser desfeita.')">
                  <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
                  <input type="hidden" name="acao" value="excluir">
                  <button type="submit" class="btn btn-excluir w-100 fw-semibold">🗑 Excluir</button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>
                
    <div class="tab-pane fade" id="tab-aprovados">
      <?php if ($aprovados->num_rows === 0): ?>
        <div class="empty-state">
          <div class="icon">📭</div>
          <h5>Nenhum serviço aprovado ainda.</h5>
        </div>
      <?php else: ?>
        <div class="row g-4 pt-2">
        <?php while ($s = $aprovados->fetch_assoc()):
          $cor = $CORES_CATEGORIA[$s['CATEGORIA']] ?? 'secondary';
        ?>
          <div class="col-md-4">
            <div class="card card-servico h-100 bg-body-tertiary">
              <?php if ($s['FOTO']): ?>
                <img src="<?= htmlspecialchars($s['FOTO']) ?>" class="card-img-top" alt="Foto do serviço">
              <?php else: ?>
                <div class="placeholder-img">🔧</div>
              <?php endif; ?>
              <div class="card-body">
                <span class="badge bg-<?= $cor ?> mb-2"><?= htmlspecialchars($s['CATEGORIA']) ?></span>
                <h5 class="card-title mb-1"><?= htmlspecialchars($s['NOME']) ?></h5>
                <p class="card-text small text-body-secondary mb-2"><?= htmlspecialchars($s['DESCRICAO']) ?></p>
                <p class="small text-muted mb-1">👤 <strong><?= htmlspecialchars($s['PRESTADOR']) ?></strong></p>
                <p class="small text-muted mb-1">📧 <?= htmlspecialchars($s['EMAIL_PRESTADOR']) ?></p>
                <p class="small text-muted mb-0">📞 <?= htmlspecialchars($s['TELEFONE']) ?></p>
              </div>
              <div class="card-footer border-top border-secondary pt-3 pb-3">
                <form method="POST"
                      onsubmit="return confirm(' Excluir PERMANENTEMENTE este serviço? Esta ação não pode ser desfeita.')">
                  <input type="hidden" name="servico_id" value="<?= (int)$s['ID'] ?>">
                  <input type="hidden" name="acao" value="excluir">
                  <button type="submit" class="btn btn-excluir w-100 fw-semibold">🗑 Excluir serviço</button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>