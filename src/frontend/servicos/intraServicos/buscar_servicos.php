<?php
// buscar_servicos.php
include("../../login/conexao.php");

header('Content-Type: application/json; charset=utf-8');

// Filtra por categoria se vier na URL: buscar_servicos.php?categoria=Limpeza
$categoria = $_GET['categoria'] ?? '';

if ($categoria && $categoria !== 'Todos') {
    $stmt = $conn->prepare(
        "SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA, S.TELEFONE, S.FOTO,
                U.NOME AS PRESTADOR, S.CRIADO_EM
         FROM SERVICOS S
         JOIN USUARIOS U ON U.ID = S.USUARIO_ID
         WHERE S.CATEGORIA = ?
         ORDER BY S.CRIADO_EM DESC"
    );
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conn->query(
        "SELECT S.ID, S.NOME, S.DESCRICAO, S.CATEGORIA, S.TELEFONE, S.FOTO,
                U.NOME AS PRESTADOR, S.CRIADO_EM
         FROM SERVICOS S
         JOIN USUARIOS U ON U.ID = S.USUARIO_ID
         ORDER BY S.CRIADO_EM DESC"
    );
}

$servicos = [];
while ($row = $resultado->fetch_assoc()) {
    $servicos[] = $row;
}

echo json_encode($servicos);

$conn->close();
?>
