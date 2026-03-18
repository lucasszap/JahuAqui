 <?php
    include("conexao.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $confirmarSenha = $_POST['confirmarSenha'];

        if($senha != $confirmarSenha){
          die('Erro: As senhas nao coincidem');
        }

        if (strlen($senha) < 6) {
          die("Erro: A senha deve ter pelo menos 6 caracteres.");
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO USUARIOS (NOME, EMAIL, SENHA) VALUES ('$nome', '$email', '$senhaHash')";

        if($conn->query($sql) === TRUE) {
          header("Location: /241037/TCC/JahuAqui-main/JahuAqui/src/frontend/Home/index.html");
          exit();
        }
    }
?> 


