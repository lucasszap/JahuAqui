<?php

define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_NAME',    getenv('DB_NAME')    ?: 'db_jau');
define('DB_CHARSET', 'utf8mb4');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Falha na conexão: " . $conn->connect_error);
    http_response_code(500);
    die(json_encode(['status' => 'erro', 'mensagem' => 'erro_servidor']));
}

$conn->set_charset(DB_CHARSET);