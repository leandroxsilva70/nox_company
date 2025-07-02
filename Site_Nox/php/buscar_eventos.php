<?php
include 'conexao.php';

// Consulta os 3 próximos eventos em destaque
function buscarEventosDestaque($conexao) {
    // Somente eventos futuros com status 'ativo', ordenados pela data de início
    $sql = "SELECT id, nome, descricao, data_inicio, data_fim 
            FROM eventos 
            WHERE status = 'ativo' AND data_inicio >= CURDATE()
            ORDER BY data_inicio ASC 
            LIMIT 3";

    $stmt = $conexao->prepare($sql);

    if ($stmt->execute()) {
        $resultado = $stmt->get_result();
        return $resultado;
    } else {
        // Log de erro se necessário
        error_log("Erro na busca de eventos: " . $stmt->error);
        return false;
    }
}
?>
