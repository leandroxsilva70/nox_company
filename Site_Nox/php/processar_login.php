<?php
// Inicie a sessão no início do script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclua o arquivo de conexão antes de qualquer operação com o banco
require_once __DIR__ . '/conexao.php';

// Verifica se a conexão foi estabelecida
if (!isset($conexao) || !$conexao) {
    $_SESSION['erro_login'] = "Erro de conexão com o banco de dados";
    header("Location: ../login.php");
    exit;
}

// Verifica método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erro_login'] = "Método inválido";
    header("Location: ../login.php");
    exit;
}

// Sanitização (usando a função do conexao.php)
$tipo_usuario = limparInput($_POST['tipo_usuario'] ?? '');
$identificador = '';
$senha = $_POST['senha'] ?? '';

// Determina qual campo usar com base no tipo de usuário
if ($tipo_usuario === 'aluno') {
    $identificador = limparInput($_POST['rm'] ?? '');
} else {
    $identificador = limparInput($_POST['email'] ?? '');
}

// Validações básicas
if (empty($tipo_usuario) || empty($identificador) || empty($senha)) {
    $_SESSION['erro_login'] = "Preencha todos os campos";
    header("Location: ../login.php");
    exit;
}

// Verifique se o tipo de usuário é válido
if (!in_array($tipo_usuario, ['aluno', 'professor', 'admin'])) {
    $_SESSION['erro_login'] = "Tipo de usuário inválido";
    header("Location: ../login.php");
    exit;
}

try {
    // Consulta otimizada para verificar o login
    $sql = "SELECT id, nome, senha, ativo FROM usuarios WHERE ";
    
    if ($tipo_usuario === 'aluno') {
        $sql .= "rm = ?";
    } else if ($tipo_usuario === 'professor') {
        $sql .= "email_institucional = ?";
    } else { // admin
        $sql .= "email = ?";
    }
    
    $sql .= " AND tipo = ?"; // Adiciona verificação do tipo
    
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $conexao->error);
    }
    
    $stmt->bind_param("ss", $identificador, $tipo_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (password_verify($senha, $usuario['senha'])) {
            if ($usuario['ativo'] == 1) {
                // Login bem-sucedido
                $_SESSION['usuario'] = [
                    'id' => $usuario['id'],
                    'nome' => $usuario['nome'],
                    'tipo' => $tipo_usuario
                ];
                
                // Registra o login no log de acessos
                $ip = $_SERVER['REMOTE_ADDR'];
                $acao = "Login realizado";
                $log_sql = "INSERT INTO log_acessos (usuario_id, acao, ip) VALUES (?, ?, ?)";
                $log_stmt = $conexao->prepare($log_sql);
                $log_stmt->bind_param("iss", $usuario['id'], $acao, $ip);
                $log_stmt->execute();
                
                // Atualiza o último login
                $update_sql = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?";
                $update_stmt = $conexao->prepare($update_sql);
                $update_stmt->bind_param("i", $usuario['id']);
                $update_stmt->execute();
                
                header("Location: ../index.php");
                exit;
            } else {
                $_SESSION['erro_login'] = "Conta desativada. Entre em contato com o administrador.";
            }
        } else {
            $_SESSION['erro_login'] = "Credenciais inválidas";
        }
    } else {
        $_SESSION['erro_login'] = "Credenciais inválidas";
    }
    
    header("Location: ../login.php");
    exit;

} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    $_SESSION['erro_login'] = "Erro no sistema. Por favor, tente novamente mais tarde.";
    header("Location: ../login.php");
    exit;
}