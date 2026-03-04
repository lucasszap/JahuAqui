<?php
    include("conexao.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $confirmarSenha = $_POST['confirmarSenha'];

        if($senha != $confirmarSenha){
          die('Erro: As senhas nao coincidem no servidor');
        }

        if (strlen($senha) < 6) {
          die("Erro: A senha deve ter pelo menos 6 caracteres.");
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO USUARIOS (NOME, EMAIL, SENHA) VALUES ('$nome', '$email', '$senhaHash')";

        if($conn->query($sql) === TRUE) {
          echo json_encode(["status" => "sucesso", "mensagem" => "Usuário cadastrado!"]);
        } else {
          echo json_encode(["status" => "erro", "mensagem" => "Erro ao cadastrar: " . $conn->error]);
        }
    }
?>