<?php
include 'php/conexao.php';

$sql = "SELECT * FROM eventos WHERE status = 'ativo' ORDER BY data_evento DESC";
$resultado = $conexao->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    echo "<section class='eventos-destaque'>";
    while ($evento = $resultado->fetch_assoc()) {
        // Formata a data para dd/mm/aaaa
        $dataFormatada = date('d/m/Y', strtotime($evento['data_evento']));

        echo "<div class='evento-card'>";
        echo "<h3>" . htmlspecialchars($evento['titulo']) . "</h3>";
        echo "<p class='evento-data'><strong>Data:</strong> " . $dataFormatada . "</p>";
        echo "<p class='evento-descricao'>" . nl2br(htmlspecialchars($evento['descricao'])) . "</p>";

        echo "</div>";
    }
    echo "</section>";
} else {
    echo "<p class='sem-eventos'>Nenhum evento disponível no momento.</p>";
}
?>
