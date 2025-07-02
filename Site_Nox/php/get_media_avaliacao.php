<?php
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

if (!isset($_GET['id_evento'])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$idEvento = (int)$_GET['id_evento'];

$stmt = $conexao->prepare("SELECT AVG(nota) AS media, COUNT(*) AS total FROM avaliacoes WHERE evento_id = ?");
$stmt->bind_param("i", $idEvento);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "status" => "success",
    "media" => (float)$result['media'],
    "total" => (int)$result['total']
]);