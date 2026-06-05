<?php
header('Content-Type: application/json; charset=utf-8');

include("../config/conexao.php");

$categoria = trim($_GET['categoria'] ?? '');

if ($categoria && $categoria !== 'Todos') {
    $stmt = $conn->prepare("
        SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA, S.TELEFONE, S.FOTO,
               U.NOME AS PRESTADOR, S.CRIADO_EM
        FROM SERVICOS S
        JOIN USUARIOS U ON U.ID = S.USUARIO_ID
        WHERE S.CATEGORIA = ? AND S.STATUS = 'aprovado'
        ORDER BY S.CRIADO_EM DESC
    ");

    if (!$stmt) {
        echo json_encode([]);
        exit;
    }

    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $resultado = $stmt->get_result();

} else {
    $resultado = $conn->query("
        SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA, S.TELEFONE, S.FOTO,
               U.NOME AS PRESTADOR, S.CRIADO_EM
        FROM SERVICOS S
        JOIN USUARIOS U ON U.ID = S.USUARIO_ID
        WHERE S.STATUS = 'aprovado'
        ORDER BY S.CRIADO_EM DESC
    ");

    if (!$resultado) {
        echo json_encode([]);
        exit;
    }
}

$servicos = [];

while ($row = $resultado->fetch_assoc()) {
    $servicos[] = $row;
}

echo json_encode($servicos);

$conn->close();
?>