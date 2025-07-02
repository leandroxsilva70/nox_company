<?php
session_start();
require_once __DIR__ . '/conexao.php';

header('Content-Type: application/json');

if (!isset($_GET['id_evento']) || !isset($_GET['limit'])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$idEvento = (int)$_GET['id_evento'];
$limit = (int)$_GET['limit'];
$idUsuario = isset($_SESSION['usuario']['id']) ? (int)$_SESSION['usuario']['id'] : 0;

$stmt = $conexao->prepare("SELECT a.id, a.nota, a.comentario, a.data_avaliacao, u.nome 
                          FROM avaliacoes a JOIN usuarios u ON a.usuario_id = u.id 
                          WHERE a.evento_id = ? AND a.usuario_id != ? 
                          ORDER BY a.data_avaliacao DESC LIMIT ?");
$stmt->bind_param("iii", $idEvento, $idUsuario, $limit);
$stmt->execute();
$comentarios = $stmt->get_result();

$html = '';
while ($comentario = $comentarios->fetch_assoc()) {
    $html .= '<div class="avaliacao-item" data-avaliacao="'.$comentario['id'].'">';
    $html .= '<div class="avaliacao-header">';
    $html .= '<div class="avaliacao-info">';
    $html .= '<p class="avaliador-nome">'.htmlspecialchars($comentario['nome']).'</p>';
    $html .= '<p class="avaliacao-data">'.date('d/m/Y H:i', strtotime($comentario['data_avaliacao'])).'</p>';
    $html .= '<p class="avaliacao-nota">'.$comentario['nota'].'/5</p>';
    $html .= '</div></div>';
    if (!empty($comentario['comentario'])) {
        $html .= '<div class="avaliacao-comentario">'.nl2br(htmlspecialchars($comentario['comentario'])).'</div>';
    }
    if (isset($_SESSION['usuario']['tipo']) && $_SESSION['usuario']['tipo'] === 'professor') {
        $html .= '<a href="#" class="remover-avaliacao" data-avaliacao="'.$comentario['id'].'">Remover</a>';
    }
    $html .= '</div>';
}

// Verifica quantos comentários ainda existem
$stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM avaliacoes WHERE evento_id = ? AND usuario_id != ?");
$stmt->bind_param("ii", $idEvento, $idUsuario);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$remaining = $total - $limit;

echo json_encode([
    "status" => "success",
    "html" => $html,
    "remaining" => $remaining > 0 ? $remaining : 0
]);