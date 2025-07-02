<?php
session_start();
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    echo json_encode(["status" => "error", "message" => "Acesso não autorizado"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(["status" => "error", "message" => "ID da inscrição não especificado"]);
    exit;
}

$id = (int)$data['id'];
$idUsuario = $_SESSION['usuario']['id'];

// Verifica se a inscrição pertence ao usuário
$stmt = $conexao->prepare("DELETE FROM inscricoes_eventos WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $idUsuario);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Desinscrição realizada com sucesso"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao desinscrever"]);
}