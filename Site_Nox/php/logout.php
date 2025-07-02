<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Remove todas as variáveis de sessão
session_unset();
$_SESSION = [];

// Destrói a sessão
session_destroy();

// Redireciona o usuário para a página inicial
header("Location: ../index.php");
exit();
?>
