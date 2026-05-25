<?php
include("../config/conexao.php");

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'metodo_invalido';
    exit;
}

$nome           = trim($_POST['nome']           ?? '');
$email          = trim($_POST['email']          ?? '');
$senha          = $_POST['senha']               ?? '';
$confirmarSenha = $_POST['confirmarSenha']      ?? '';

if (!$nome || !$email || !$senha || !$confirmarSenha) {
    echo 'campos_obrigatorios';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'email_invalido';
    exit;
}

if ($senha !== $confirmarSenha) {
    echo 'senhas_diferentes';
    exit;
}

if (strlen($senha) < 6) {
    echo 'senha_curta';
    exit;
}

$stmt = $conn->prepare("SELECT ID FROM USUARIOS WHERE EMAIL = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo 'email_existente';
    $stmt->close();
    exit;
}
$stmt->close();

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO USUARIOS (NOME, EMAIL, SENHA) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nome, $email, $senhaHash);

if ($stmt->execute()) {
    echo 'sucesso';
} else {
    error_log("Erro ao cadastrar: " . $conn->error);
    echo 'erro_banco';
}

$stmt->close();
$conn->close();