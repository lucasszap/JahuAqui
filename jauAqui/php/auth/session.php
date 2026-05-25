<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['usuario_id']) && isset($_SESSION['nome'])) {
    echo json_encode([
        "logado" => true,
        "nome"   => $_SESSION['nome']
    ]);
} else {
    echo json_encode([
        "logado" => false
    ]);
}