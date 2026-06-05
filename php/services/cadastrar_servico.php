<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include("../config/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "nao_logado"
    ]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nome       = trim($_POST['nome']      ?? '');
$categoria  = trim($_POST['categoria'] ?? '');
$descricao  = trim($_POST['descricao'] ?? '');
$telefone   = trim($_POST['telefone']  ?? '');

// Validação básica dos campos obrigatórios
if (!$nome || !$categoria || !$descricao || !$telefone) {
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "campos_obrigatorios"
    ]);
    exit;
}

// Valida categoria contra lista permitida (evita valores arbitrários)
$categoriasPermitidas = ['Manutenção', 'Logística', 'Limpeza', 'Beleza', 'Saúde', 'Outros'];
if (!in_array($categoria, $categoriasPermitidas, true)) {
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "categoria_invalida"
    ]);
    exit;
}

// ── Upload da foto ────────────────────────────────────────────
$fotoPath = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    // 1. Limite de tamanho: 2 MB
    $maxBytes = 2 * 1024 * 1024;
    if ($_FILES['foto']['size'] > $maxBytes) {
        echo json_encode([
            "status"   => "erro",
            "mensagem" => "imagem_grande_demais"
        ]);
        exit;
    }

    // 2. Validação de MIME type real (lê os bytes do arquivo, não confia no nome)
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeReal = finfo_file($finfo, $_FILES['foto']['tmp_name']);
    finfo_close($finfo);

    $mimesPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($mimeReal, $mimesPermitidos, true)) {
        echo json_encode([
            "status"   => "erro",
            "mensagem" => "tipo_imagem_invalido"
        ]);
        exit;
    }

    // 3. Extensão baseada no MIME real (nunca no nome enviado pelo cliente)
    $extensoes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $ext = $extensoes[$mimeReal];

    // 4. Diretório de upload (caminho do servidor, independente do ambiente)
    // Sobe dois níveis de php/services/ para chegar em jauAqui/uploads/servicos/
    $uploadDir = dirname(__DIR__, 2) . '/uploads/servicos/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // 5. Nome único e seguro para o arquivo
    $nomeArquivo = 'servico_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $destino     = $uploadDir . $nomeArquivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        // Armazena caminho relativo à raiz do projeto (funciona em qualquer ambiente)
        $fotoPath = 'uploads/servicos/' . $nomeArquivo;
    }
}

// ── Insere no banco ───────────────────────────────────────────
$stmt = $conn->prepare("
    INSERT INTO SERVICOS (USUARIO_ID, NOME, CATEGORIA, DESCRICAO, TELEFONE, FOTO, STATUS, CRIADO_EM)
    VALUES (?, ?, ?, ?, ?, ?, 'pendente', NOW())
");

if (!$stmt) {
    error_log("Erro ao preparar query: " . $conn->error);
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "erro_banco"
    ]);
    exit;
}

$stmt->bind_param("isssss", $usuario_id, $nome, $categoria, $descricao, $telefone, $fotoPath);

if ($stmt->execute()) {
    echo json_encode([
        "status"   => "sucesso",
        "mensagem" => "Serviço enviado para análise! Ele aparecerá no site após aprovação."
    ]);
} else {
    error_log("Erro ao salvar serviço: " . $stmt->error);
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "erro_banco"
    ]);
}

$stmt->close();
$conn->close();
