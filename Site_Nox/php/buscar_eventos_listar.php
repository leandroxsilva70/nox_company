<?php
include 'conexao.php';

// Consulta todos os eventos com status 'ativo', ordenados por data de início
function buscarTodosEventos($conexao) {
    $sql = "SELECT id, nome, descricao, data_inicio, data_fim 
            FROM eventos 
            WHERE status = 'ativo' 
            ORDER BY data_inicio ASC";

    $stmt = $conexao->prepare($sql);

    if ($stmt->execute()) {
        return $stmt->get_result();
    } else {
        error_log("Erro ao buscar todos os eventos: " . $stmt->error);
        return false;
    }
}
?>
