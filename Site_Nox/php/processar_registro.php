<?php
session_start();
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro_registro'] = "Método inválido";
    header("Location: ../registro.php");
    exit();
}

// Sanitização
$nome = limparInput($_POST['nome'] ?? '');
$tipo = limparInput($_POST['tipo_usuario'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validações básicas
if (empty($nome) || empty($tipo) || empty($senha) || empty($confirmar_senha)) {
    $_SESSION['erro_registro'] = "Preencha todos os campos";
    header("Location: ../registro.php");
    exit();
}

if ($senha !== $confirmar_senha) {
    $_SESSION['erro_registro'] = "As senhas não coincidem";
    header("Location: ../registro.php");
    exit();
}

if (strlen($senha) < 6) {
    $_SESSION['erro_registro'] = "A senha deve ter pelo menos 6 caracteres";
    header("Location: ../registro.php");
    exit();
}

// Processamento por tipo de usuário
if ($tipo === 'aluno') {
    $rm = limparInput($_POST['rm'] ?? '');
    $etec_id = limparInput($_POST['etec'] ?? '');
    
    if (empty($rm) || empty($etec_id)) {
        $_SESSION['erro_registro'] = "RM e ETEC são obrigatórios para alunos";
        header("Location: ../registro.php");
        exit();
    }
    
    if (!preg_match('/^\d{6,}$/', $rm)) {
        $_SESSION['erro_registro'] = "RM inválido (6+ dígitos)";
        header("Location: ../registro.php");
        exit();
    }
    
    // Verifica ETEC ativa
    $stmt = $conexao->prepare("SELECT id FROM etecs WHERE id = ? AND ativo = 1");
    $stmt->bind_param("i", $etec_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        $_SESSION['erro_registro'] = "ETEC selecionada não está ativa";
        header("Location: ../registro.php");
        exit();
    }
    
    // Verifica RM único
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE rm = ?");
    $stmt->bind_param("s", $rm);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['erro_registro'] = "RM já cadastrado";
        header("Location: ../registro.php");
        exit();
    }
    
    // Insere aluno (usando id_etec como nome da coluna)
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, senha, tipo, rm, id_etec, ativo) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssssi", $nome, $senha_hash, $tipo, $rm, $etec_id);

} elseif ($tipo === 'professor') {
    $email_institucional = limparInput($_POST['email_institucional'] ?? '');
    
    if (empty($email_institucional)) {
        $_SESSION['erro_registro'] = "E-mail institucional obrigatório";
        header("Location: ../registro.php");
        exit();
    }
    
    if (!filter_var($email_institucional, FILTER_VALIDATE_EMAIL) || !preg_match('/@etec\.sp\.gov\.br$/', $email_institucional)) {
        $_SESSION['erro_registro'] = "Use um e-mail @etec.sp.gov.br válido";
        header("Location: ../registro.php");
        exit();
    }
    
    // Verifica e-mail único
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email_institucional = ?");
    $stmt->bind_param("s", $email_institucional);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['erro_registro'] = "E-mail já cadastrado";
        header("Location: ../registro.php");
        exit();
    }
    
    // Insere professor
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $conexao->prepare("INSERT INTO usuarios (nome, senha, tipo, email_institucional, ativo) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("ssss", $nome, $senha_hash, $tipo, $email_institucional);
}

// Executa a inserção
if ($stmt->execute()) {
    $_SESSION['sucesso_registro'] = "Cadastro realizado com sucesso!";
    header("Location: ../login.php");
} else {
    $_SESSION['erro_registro'] = "Erro ao cadastrar. Tente novamente.";
    header("Location: ../registro.php");
}
exit();
?>