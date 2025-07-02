<?php
$host = 'localhost';
$dbname = 'banco_nox';
$username = 'root';
$password = '';

// Configuração de relatório de erros
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexao = new mysqli($host, $username, $password, $dbname);
    
    // Definir charset para evitar problemas de codificação
    $conexao->set_charset("utf8mb4");
    
    // Verificação adicional da conexão
    if ($conexao->connect_errno) {
        throw new Exception("Falha na conexão: " . $conexao->connect_error);
    }
    
} catch (Exception $e) {
    error_log("Erro de banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Função para limpar inputs
if (!function_exists('limparInput')) {
    function limparInput($dado) {
        return htmlspecialchars(strip_tags(trim($dado)), ENT_QUOTES, 'UTF-8');
    }
}
?>