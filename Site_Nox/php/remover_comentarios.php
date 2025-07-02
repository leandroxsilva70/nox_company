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
    echo json_encode(["status" => "error", "message" => "ID da avaliação não especificado"]);
    exit;
}

$id = (int)$data['id'];
$idUsuario = $_SESSION['usuario']['id'];
$tipoUsuario = $_SESSION['usuario']['tipo'];

// Verifica se o usuário pode remover (se é professor ou dono da avaliação)
$stmt = $conexao->prepare("SELECT usuario_id FROM avaliacoes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Avaliação não encontrada"]);
    exit;
}

$avaliacao = $result->fetch_assoc();
if ($tipoUsuario !== 'professor' && $avaliacao['usuario_id'] !== $idUsuario) {
    echo json_encode(["status" => "error", "message" => "Você não tem permissão para remover esta avaliação"]);
    exit;
}

// Remove a avaliação
$stmt = $conexao->prepare("DELETE FROM avaliacoes WHERE id = ?");
if ($stmt->bind_param("i", $id) && $stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Avaliação removida com sucesso"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao remover avaliação"]);
}