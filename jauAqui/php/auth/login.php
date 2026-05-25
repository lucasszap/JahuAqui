<?php
session_start();
include("../config/conexao.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "erro", "mensagem" => "metodo_invalido"]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha']      ?? '';

if (!$email || !$senha) {
    echo json_encode(["status" => "erro", "mensagem" => "campos_obrigatorios"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, NOME, SENHA FROM USUARIOS WHERE EMAIL = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "erro", "mensagem" => "email_nao_cadastrado"]);
    $stmt->close();
    exit;
}

$usuario = $result->fetch_assoc();
$stmt->close();

if (!password_verify($senha, $usuario['SENHA'])) {
    echo json_encode(["status" => "erro", "mensagem" => "senha_incorreta"]);
    exit;
}

$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['nome']       = $usuario['NOME'];

echo json_encode(["status" => "sucesso", "nome" => $usuario['NOME']]);
$conn->close();