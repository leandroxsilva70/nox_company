<?php
session_start();
if (isset($_SESSION['status']) && $_SESSION['status'] === 'ativo') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta Bloqueada - Nox</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="imagem_logotipo/favicon.ico">
    <style>
        .blocked-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        
        .blocked-icon {
            font-size: 50px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        .contact-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>
    
    <main class="container">
        <div class="blocked-container">
            <div class="blocked-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1>Sua conta está bloqueada</h1>
            <p>Infelizmente, sua conta foi temporariamente desativada pelo administrador do sistema.</p>
            
            <div class="contact-info">
                <h3>Entre em contato</h3>
                <p>Para solicitar a reativação ou esclarecer dúvidas:</p>
                <p>
                    <i class="fas fa-envelope"></i> suporte@nox.com.br<br>
                    <i class="fas fa-phone"></i> (11) 1234-5678
                </p>
                <p>Horário de atendimento: Segunda a Sexta, das 9h às 18h</p>
            </div>
            
            <div class="mt-4">
                <a href="logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Sair do Sistema
                </a>
            </div>
        </div>
    </main>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>