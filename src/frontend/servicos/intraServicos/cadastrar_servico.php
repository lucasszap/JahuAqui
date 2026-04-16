<?php
session_start();
include("../../login/conexao.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "erro", "mensagem" => "nao_logado"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "erro", "mensagem" => "metodo_invalido"]);
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$nome       = trim($_POST['nome']       ?? '');
$descricao  = trim($_POST['descricao']  ?? '');
$categoria  = trim($_POST['categoria']  ?? '');
$telefone   = trim($_POST['telefone']   ?? '');

if (!$nome || !$descricao || !$categoria || !$telefone) {
    echo json_encode(["status" => "erro", "mensagem" => "campos_obrigatorios"]);
    exit;
}

$foto = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['foto']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $tiposPermitidos)) {
        echo json_encode(["status" => "erro", "mensagem" => "tipo_imagem_invalido"]);
        exit;
    }

    $tamanhoMaximo = 2 * 1024 * 1024; // 2mb
    if ($_FILES['foto']['size'] > $tamanhoMaximo) {
        echo json_encode(["status" => "erro", "mensagem" => "imagem_grande_demais"]);
        exit;
    }

    $pastaUploads = __DIR__ . '/uploads/servicos/';
    if (!is_dir($pastaUploads)) {
        mkdir($pastaUploads, 0755, true);
    }

    $extensao  = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid('srv_', true) . '.' . $extensao;
    $destino   = $pastaUploads . $nomeArquivo;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo json_encode(["status" => "erro", "mensagem" => "erro_upload"]);
        exit;
    }

    $foto = 'uploads/servicos/' . $nomeArquivo; 
}

$stmt = $conn->prepare(
    "INSERT INTO SERVICOS (USUARIO_ID, NOME, DESCRICAO, CATEGORIA, TELEFONE, FOTO, STATUS)
     VALUES (?, ?, ?, ?, ?, ?, 'pendente')"
);
$stmt->bind_param("isssss", $usuario_id, $nome, $descricao, $categoria, $telefone, $foto);

if ($stmt->execute()) {
    echo json_encode(["status" => "sucesso", "mensagem" => "Serviço enviado! Aguarde a aprovação de um administrador."]);
} else {
    echo json_encode(["status" => "erro", "mensagem" => "erro_banco"]);
}

$stmt->close();
$conn->close();
?>