<?php
    include("conexao.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $confirmarSenha = $_POST['confirmarSenha'];

       
        if($senha != $confirmarSenha){
            echo "senhas_diferentes";
            exit;
        }

        if (strlen($senha) < 6) {
            echo "senha_curta";
            exit;
        }

       
        $sqlCheck = "SELECT * FROM USUARIOS WHERE EMAIL = '$email'";
        $resCheck = $conn->query($sqlCheck);

        if($resCheck->num_rows > 0){
            echo "email_existente";
            exit;
        }

     
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO USUARIOS (NOME, EMAIL, SENHA) VALUES ('$nome', '$email', '$senhaHash')";

        if($conn->query($sql) === TRUE) {
            echo "sucesso";
            exit;
        } else {
            echo "erro_banco";
            exit;
        }
    }
?>