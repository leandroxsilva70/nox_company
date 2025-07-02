<?php
session_start();

// Verificação CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Token CSRF inválido. Recarregue a página e tente novamente.'
    ]));
}

// Verificação de login e permissão
if (!isset($_SESSION['usuario_id'])) {
    die("<script>alert('Faça login primeiro.'); window.location.href='login.php';</script>");
}

if ($_SESSION['tipo'] !== 'professor') {
    die("<script>alert('Acesso restrito a professores.'); window.location.href='index.php';</script>");
}

// Inclui conexão com tratamento de erro
require_once 'conexao.php';
if (!$conexao) {
    die("<script>alert('Erro ao conectar ao banco de dados.'); window.location.href='gerenciar_eventos.php';</script>");
}

// Verifica campos obrigatórios
$campos_obrigatorios = [
    'titulo', 'descricao', 'data_inicio', 'data_fim', 
    'hora_inicio', 'hora_fim', 'local'
];

foreach ($campos_obrigatorios as $campo) {
    if (empty($_POST[$campo])) {
        die("<script>alert('O campo " . ucfirst(str_replace('_', ' ', $campo)) . " é obrigatório.'); window.location.href='gerenciar_eventos.php';</script>");
    }
}

if (empty($_FILES['imagem']['name'])) {
    die("<script>alert('A imagem é obrigatória.'); window.location.href='gerenciar_eventos.php';</script>");
}

// Sanitização dos dados
$titulo = htmlspecialchars(trim($_POST['titulo']), ENT_QUOTES, 'UTF-8');
$descricao = htmlspecialchars(trim($_POST['descricao']), ENT_QUOTES, 'UTF-8');
$data_inicio = trim($_POST['data_inicio']);
$data_fim = trim($_POST['data_fim']);
$hora_inicio = trim($_POST['hora_inicio']);
$hora_fim = trim($_POST['hora_fim']);
$local = htmlspecialchars(trim($_POST['local']), ENT_QUOTES, 'UTF-8');
$id_professor = (int)$_SESSION['usuario_id'];

// Validação de datas
$data_atual = new DateTime();
$data_inicio_obj = DateTime::createFromFormat('Y-m-d', $data_inicio);
$data_fim_obj = DateTime::createFromFormat('Y-m-d', $data_fim);

if (!$data_inicio_obj || !$data_fim_obj) {
    die("<script>alert('Formato de data inválido. Use o formato AAAA-MM-DD.'); window.location.href='gerenciar_eventos.php';</script>");
}

if ($data_inicio_obj > $data_fim_obj) {
    die("<script>alert('A data de início deve ser anterior à data de término.'); window.location.href='gerenciar_eventos.php';</script>");
}

if ($data_inicio_obj < $data_atual) {
    die("<script>alert('A data de início deve ser futura.'); window.location.href='gerenciar_eventos.php';</script>");
}

// Validação de horários
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $hora_inicio) || 
    !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $hora_fim)) {
    die("<script>alert('Formato de horário inválido. Use HH:MM.'); window.location.href='gerenciar_eventos.php';</script>");
}

// Processamento da imagem
$imagem = $_FILES['imagem'];
$extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];
$imagem_extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));

if (!in_array($imagem_extensao, $extensoes_permitidas)) {
    die("<script>alert('Apenas imagens .jpg, .jpeg, .png ou .webp são permitidas.'); window.location.href='gerenciar_eventos.php';</script>");
}

if ($imagem['size'] > 5 * 1024 * 1024) {
    die("<script>alert('A imagem não pode ser maior que 5MB.'); window.location.href='gerenciar_eventos.php';</script>");
}

// Verificar se é realmente uma imagem
if (!getimagesize($imagem['tmp_name'])) {
    die("<script>alert('O arquivo enviado não é uma imagem válida.'); window.location.href='gerenciar_eventos.php';</script>");
}

// Criar diretório se não existir
$diretorio_upload = 'uploads/eventos';
if (!file_exists($diretorio_upload)) {
    if (!mkdir($diretorio_upload, 0755, true)) {
        die("<script>alert('Erro ao criar diretório para upload.'); window.location.href='gerenciar_eventos.php';</script>");
    }
}

// Nome único para o arquivo
$imagem_nome = uniqid('evento_', true) . '.' . $imagem_extensao;
$caminho_destino = $diretorio_upload . '/' . $imagem_nome;

// Mover arquivo
if (!move_uploaded_file($imagem['tmp_name'], $caminho_destino)) {
    die("<script>alert('Erro ao enviar a imagem.'); window.location.href='gerenciar_eventos.php';</script>");
}

// Inserção no banco de dados
try {
    $conexao->begin_transaction();
    
    $sql = "INSERT INTO eventos 
            (titulo, descricao, data_inicio, data_fim, hora_inicio, hora_fim, 
             localizacao, imagem_path, professor_id, status, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'ativo', NOW())";
    
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta: " . $conexao->error);
    }
    
    $stmt->bind_param("ssssssssi", $titulo, $descricao, $data_inicio, $data_fim, 
                     $hora_inicio, $hora_fim, $local, $imagem_nome, $id_professor);
    
    if (!$stmt->execute()) {
        throw new Exception("Erro ao cadastrar evento: " . $stmt->error);
    }
    
    $evento_id = $conexao->insert_id;
    
    // Registrar no log
    $acao = "Cadastrou o evento: " . $titulo;
    $sql_log = "INSERT INTO log_atividades (usuario_id, acao, data_acao) VALUES (?, ?, NOW())";
    $stmt_log = $conexao->prepare($sql_log);
    $stmt_log->bind_param("is", $id_professor, $acao);
    $stmt_log->execute();
    
    $conexao->commit();
    
    $_SESSION['sucesso'] = "Evento cadastrado com sucesso!";
    header("Location: gerenciar_eventos.php");
    exit();
    
} catch (Exception $e) {
    $conexao->rollback();
    
    // Remove a imagem em caso de erro
    if (file_exists($caminho_destino)) {
        unlink($caminho_destino);
    }
    
    error_log("Erro ao cadastrar evento: " . $e->getMessage());
    $_SESSION['erro'] = "Erro ao cadastrar evento. Por favor, tente novamente.";
    header("Location: gerenciar_eventos.php");
    exit();
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_log)) $stmt_log->close();
    $conexao->close();
}