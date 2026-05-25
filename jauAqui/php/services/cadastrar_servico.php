<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include("../config/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "Você precisa estar logado para cadastrar um serviço."
    ]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nome       = trim($_POST['nome']       ?? '');
$categoria  = trim($_POST['categoria']  ?? '');
$descricao  = trim($_POST['descricao']  ?? '');
$telefone   = trim($_POST['telefone']   ?? '');

// Validação básica
if (!$nome || !$categoria || !$descricao || !$telefone) {
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "Preencha todos os campos obrigatórios."
    ]);
    exit;
}

// Upload da foto
$fotoPath = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext        = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($ext, $permitidos)) {
        echo json_encode([
            "status"   => "erro",
            "mensagem" => "Formato de imagem inválido. Use JPG, PNG ou WEBP."
        ]);
        exit;
    }

    $uploadDir = __DIR__ . '/../../uploads/servicos/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $nomeArquivo = uniqid('servico_', true) . '.' . $ext;
    $destino     = $uploadDir . $nomeArquivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        $fotoPath = '/251256/jauAqui/uploads/servicos/' . $nomeArquivo;
    }
}

// Insere no banco — status 'aprovado' direto (ou 'pendente' se quiser moderação)
$stmt = $conn->prepare("
    INSERT INTO SERVICOS (USUARIO_ID, NOME, CATEGORIA, DESCRICAO, TELEFONE, FOTO, STATUS, CRIADO_EM)
    VALUES (?, ?, ?, ?, ?, ?, 'pendente', NOW())
");

if (!$stmt) {
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "Erro interno ao preparar a query: " . $conn->error
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
    echo json_encode([
        "status"   => "erro",
        "mensagem" => "Erro ao salvar o serviço: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>