<?php
include("conexao.php"); // Certifique-se que o arquivo de conexão se chama assim

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pegando os dados do formulário (ajuste os 'names' se necessário)
    $email = $conn->real_escape_string($_POST['email']);
    $senha = $_POST['senha'];

    // 1. Busca o usuário pelo e-mail
    $sql = "SELECT * FROM USUARIOS WHERE EMAIL = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // 2. Verifica se a senha digitada bate com o Hash do banco
        if (password_verify($senha, $usuario['SENHA'])) {
            
            // 3. Sucesso! Inicia a sessão
            if(!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['id'] = $usuario['ID']; // Verifique se o nome da sua chave primária é ID
            $_SESSION['nome'] = $usuario['NOME'];

            // Redireciona para a home ou painel
            header("Location: /241037/TCC/JahuAqui-main/JahuAqui/src/frontend/paginaNoticias/index.html");
            exit();

        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "E-mail não cadastrado!";
    }
}
?>