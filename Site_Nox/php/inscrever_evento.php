<?php
session_start();
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario']) || !is_array($_SESSION['usuario'])) {
    echo json_encode(["status" => "error", "message" => "Acesso não autorizado"]);
    exit;
}

// Verifica se é um aluno
if ($_SESSION['usuario']['tipo'] !== 'aluno') {
    echo json_encode(["status" => "error", "message" => "Apenas alunos podem se inscrever"]);
    exit;
}

// Verifica se o ID do evento foi enviado
if (!isset($_POST['id_evento'])) {
    echo json_encode(["status" => "error", "message" => "ID do evento não especificado"]);
    exit;
}

$idUsuario = $_SESSION['usuario']['id'];
$idEvento = (int)$_POST['id_evento'];

// Verifica se o usuário já está inscrito
$stmt = $conexao->prepare("SELECT id FROM inscricoes_eventos WHERE usuario_id = ? AND evento_id = ?");
$stmt->bind_param("ii", $idUsuario, $idEvento);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Você já está inscrito neste evento"]);
    exit;
}

// Inscreve o usuário no evento
$stmt = $conexao->prepare("INSERT INTO inscricoes_eventos (usuario_id, evento_id, data_inscricao) VALUES (?, ?, NOW())");
if ($stmt->bind_param("ii", $idUsuario, $idEvento) && $stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Inscrição realizada com sucesso"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao realizar inscrição"]);
}