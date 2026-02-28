<?php
    include("conexao.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $confirmarSenha = $_POST['confirmarSenha'];

        if($senha != $confirmarSenha){
          die('Erro: As senhas nao coincidem no servidor');
        }

        if (strlen($senha) < 6) {
          die("Erro: A senha deve ter pelo menos 6 caracteres.");
        }

        $sql = "INSERT INTO USUARIOS (NOME, EMAIL, SENHA) VALUES ('$nome', '$email', '$senha')";

        if($conn->query($sql) === TRUE) {
          echo json_encode(["status" => "sucesso", "mensagem" => "Usuário cadastrado!"]);
        } else {
          echo json_encode(["status" => "erro", "mensagem" => "Erro ao cadastrar: " . $conn->error]);
        }
    }
?>