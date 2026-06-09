<?php
session_start();

// Limpa todas as variáveis de sessão
session_unset();

// Invalida o cookie de sessão no navegador
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destrói os dados de sessão no servidor
session_destroy();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(["status" => "deslogado"]);
