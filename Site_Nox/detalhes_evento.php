<?php
session_start();
require 'php/conexao.php';

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['usuario_id']) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['tipo'] !== 'professor') {
    die("Acesso negado: apenas professores podem ver detalhes.");
}

// Pega o ID do evento da URL
$evento_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$evento_id) {
    die("ID do evento inválido.");
}

// Busca os dados do evento
$query_evento = $conexao->prepare("
    SELECT e.*, 
           COUNT(i.id) AS inscritos
    FROM eventos e
    LEFT JOIN inscricoes i ON e.id = i.evento_id
    WHERE e.id = ? AND e.status = 'ativo'
    GROUP BY e.id
");
$query_evento->bind_param("i", $evento_id);
$query_evento->execute();
$evento = $query_evento->get_result()->fetch_assoc();

if (!$evento) {
    die("Evento não encontrado ou inativo.");
}

// Busca lista de inscritos (opcional)
$query_inscritos = $conexao->prepare("
    SELECT u.nome, u.email 
    FROM inscricoes i
    JOIN usuarios u ON i.usuario_id = u.id
    WHERE i.evento_id = ?
");
$query_inscritos->bind_param("i", $evento_id);
$query_inscritos->execute();
$inscritos = $query_inscritos->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Evento - Nox</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .detalhes-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .inscritos-list {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <main class="detalhes-container">
        <h1><?= htmlspecialchars($evento['titulo']) ?></h1>
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>
        <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($evento['data_inicio'])) ?></p>
        <p><strong>Horário:</strong> <?= substr($evento['hora_inicio'], 0, 5) ?> - <?= substr($evento['hora_fim'], 0, 5) ?></p>
        <p><strong>Local:</strong> <?= htmlspecialchars($evento['localizacao']) ?></p>
        <p><strong>Inscritos:</strong> <?= $evento['inscritos'] ?></p>

        <div class="inscritos-list">
            <h3>Lista de Inscritos</h3>
            <?php if ($inscritos->num_rows > 0): ?>
                <ul>
                    <?php while ($inscrito = $inscritos->fetch_assoc()): ?>
                        <li><?= htmlspecialchars($inscrito['nome']) ?> (<?= htmlspecialchars($inscrito['email']) ?>)</li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Nenhum inscrito no momento.</p>
            <?php endif; ?>
        </div>

        <a href="eventos.php" class="btn btn-primary">Voltar</a>
    </main>

    <?php include '../footer.php'; ?>
</body>
</html>