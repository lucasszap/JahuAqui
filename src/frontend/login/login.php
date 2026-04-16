<?php
session_start();
include("conexao.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "erro", "mensagem" => "metodo_invalido"]);
    exit;
}

$email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    echo json_encode(["status" => "erro", "mensagem" => "campos_obrigatorios"]);
    exit;
}

$sql    = "SELECT * FROM USUARIOS WHERE EMAIL = '$email'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["status" => "erro", "mensagem" => "email_nao_cadastrado"]);
    exit;
}

$usuario = $result->fetch_assoc();

if (!password_verify($senha, $usuario['SENHA'])) {
    echo json_encode(["status" => "erro", "mensagem" => "senha_incorreta"]);
    exit;
}

// Login bem-sucedido — salva na sessão
$_SESSION['usuario_id'] = $usuario['ID'];
$_SESSION['nome']       = $usuario['NOME'];

echo json_encode(["status" => "sucesso", "nome" => $usuario['NOME']]);
$conn->close();
?>