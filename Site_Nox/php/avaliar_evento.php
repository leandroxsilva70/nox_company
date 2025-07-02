<?php
session_start();
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

// Verificação robusta de sessão
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    echo json_encode(["status" => "error", "message" => "Você precisa estar logado"]);
    exit;
}

// Verifica se os dados foram enviados
if (!isset($_POST['id_evento']) || !isset($_POST['nota'])) {
    echo json_encode(["status" => "error", "message" => "Dados incompletos"]);
    exit;
}

$idUsuario = $_SESSION['usuario']['id'];
$idEvento = (int)$_POST['id_evento'];
$nota = (int)$_POST['nota'];
$comentario = $_POST['comentario'] ?? '';

// Validação dos dados
if ($nota < 1 || $nota > 5) {
    echo json_encode(["status" => "error", "message" => "Nota inválida"]);
    exit;
}

// Verifica se já avaliou
$stmt = $conexao->prepare("SELECT id FROM avaliacoes WHERE usuario_id = ? AND evento_id = ?");
$stmt->bind_param("ii", $idUsuario, $idEvento);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Você já avaliou este evento"]);
    exit;
}

// Insere a avaliação
$stmt = $conexao->prepare("INSERT INTO avaliacoes (usuario_id, evento_id, nota, comentario, data_avaliacao) VALUES (?, ?, ?, ?, NOW())");
if ($stmt->bind_param("iiis", $idUsuario, $idEvento, $nota, $comentario) && $stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Avaliação registrada com sucesso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao registrar avaliação"]);
}