<?php
session_start();

// Configurações de segurança básicas
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

// Mensagens de erro
$mensagem_erro = '';
if (isset($_SESSION['erro_login'])) {
    $mensagem_erro = htmlspecialchars($_SESSION['erro_login'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['erro_login']);
}

// Redireciona se já estiver logado
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nox</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" href="imagem_logotipo/favicon.ico">
    <!-- Fontes do Google -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-page">
    <!-- Header completo com navegação -->
    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <a href="index.php" aria-label="Página inicial">
                    <img src="imagem_logotipo/logo.png" alt="Logotipo do Nox" class="logo">
                </a>
            </div>
            <div class="nav-container">
                <ul class="nav-links">
                    <li><a href="index.php">Inicial</a></li>
                    <li><a href="historia.php">Sobre Nós</a></li>
                    <li><a href="eventos.php">Eventos</a></li>
                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'professor'): ?>
                        <li><a href="gerenciar_eventos.php">Gerenciar Eventos</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <main class="login-main">
        <div class="login-card">
            <div class="login-logo">
                <img src="imagem_logotipo/logo.png" alt="Nox">
            </div>
            
            <h1 class="login-title">Entrar no Nox</h1>
            
            <?php if (!empty($mensagem_erro)): ?>
                <div class="alert-error" role="alert">
                    <?= $mensagem_erro ?>
                </div>
            <?php endif; ?>
            
            <form action="php/processar_login.php" method="POST" class="login-form" autocomplete="on" id="loginForm">
                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuário</label>
                    <select name="tipo_usuario" id="tipo_usuario" class="form-control" required aria-required="true">
                        <option value="">Selecione...</option>
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                
                <div class="form-group hidden" id="aluno_fields">
                    <label for="rm">RM</label>
                    <input type="text" id="rm" name="rm" class="form-control" pattern="[0-9]{6,}" 
                        title="Digite seu RM (apenas números)" aria-describedby="rm-help">
                    <small id="rm-help" style="display: block; margin-top: 5px; font-size: 12px; color: var(--text-muted);">Exemplo: 123456</small>
                </div>
                
                <div class="form-group hidden" id="email_fields">
                    <label id="email_label" for="email">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" 
                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                        title="Digite um e-mail válido">
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control" required minlength="6" aria-required="true">
                </div>
                
                <button type="submit" class="login-btn">Continuar</button>
            </form>
            
            <div class="login-divider">ou</div>
            
            <div class="social-login">
                <button type="button" class="social-btn">
                    <img src="imagens_rede/google.svg" alt="Google">
                    Continuar com Google
                </button>
                <button type="button" class="social-btn">
                    <img src="imagens_rede/microsoft.svg" alt="Microsoft">
                    Continuar com Microsoft
                </button>
            </div>
            
            <div class="login-footer-links">
                <a href="recuperar_senha.php">Não consegue entrar?</a> • 
                <a href="registro.php">Criar uma conta</a>
            </div>
            
            <!-- Seção de contas de teste (pode ser removida em produção) -->
            <div style="margin-top: 30px; padding: 15px; background-color: rgba(0,0,0,0.1); border-radius: 8px;">
                <h3 style="color: var(--text-light); font-size: 14px; margin-bottom: 10px;">Contas para teste:</h3>
                <ul style="text-align: left; color: var(--text-muted); font-size: 12px; list-style-type: none; padding-left: 5px;">
                    <li><strong>Admin:</strong> admin@nox.com / senha123</li>
                    <li><strong>Professor:</strong> carlos.henrique@etec.sp.gov.br / password </li>
                    <li><strong>Aluno:</strong> RM: 888888 / password</li>
                </ul>
            </div>
        </div>
    </main>

    <!-- RODAPÉ -->
    <footer>
        <div class="footer-container" style="max-width: 1200px; margin: 0 auto; padding: 30px 20px 20px; background-color: var(--darker-bg); text-align: left;">
            <!-- Seção "Sobre o Nox" -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: var(--text-light); margin-bottom: 15px; font-size: 1.1rem;">Sobre o Nox</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px; max-width: 600px;">
                    Plataforma de organização de eventos escolares para professores e alunos.
                </p>
                
                <div style="display: flex; flex-wrap: wrap; gap: 30px;">
                    <div>
                        <h4 style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 10px;">Eventos</h4>
                        <a href="eventos.php" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none;">Ver eventos disponíveis</a>
                    </div>
                    <div>
                        <h4 style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 10px;">Contato</h4>
                        <a href="mailto:suporte@nox.com" style="color: var(--text-muted); font-size: 0.9rem; text-decoration: none;">suporte@nox.com</a>
                    </div>
                </div>
            </div>

            <hr style="border: none; height: 1px; background-color: var(--border-color); margin: 20px 0;">

            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
                <div style="display: flex; flex-wrap: wrap; gap: 15px 25px; margin-right: auto;">
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">Português (BR)</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">Privacidade</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">Termos</a>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">Cookies</a>
                </div>
                
                <div style="display: flex; align-items: center; gap: 20px; margin-left: auto;">
                    <span style="color: var(--text-muted); font-size: 0.85rem;">
                        © 2025 Nox. Todos os direitos reservados.
                    </span>
                    
                    <div class="social-icons" style="display: flex; gap: 15px;">
                        <a href="#" style="display: inline-flex; width: 32px; height: 32px; border-radius: 50%; background-color: rgba(255,255,255,0.1); align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <img src="imagens_rede/instagram.svg" alt="Instagram" style="height: 16px; filter: brightness(0.8); transition: all 0.3s ease;">
                        </a>
                        <a href="#" style="display: inline-flex; width: 32px; height: 32px; border-radius: 50%; background-color: rgba(255,255,255,0.1); align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <img src="imagens_rede/facebook.svg" alt="Facebook" style="height: 16px; filter: brightness(0.8); transition: all 0.3s ease;">
                        </a>
                        <a href="#" style="display: inline-flex; width: 32px; height: 32px; border-radius: 50%; background-color: rgba(255,255,255,0.1); align-items: center; justify-content: center; transition: all 0.3s ease;">
                            <img src="imagens_rede/whatsapp.svg" alt="WhatsApp" style="height: 16px; filter: brightness(0.8); transition: all 0.3s ease;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoUsuario = document.getElementById('tipo_usuario');
            const alunoFields = document.getElementById('aluno_fields');
            const emailFields = document.getElementById('email_fields');
            const emailLabel = document.getElementById('email_label');
            const rmInput = document.getElementById('rm');
            const emailInput = document.getElementById('email');
            
            // Mostra/oculta campos com base no tipo de usuário selecionado
            function toggleFields() {
                const tipo = tipoUsuario.value;
                
                alunoFields.classList.add('hidden');
                emailFields.classList.add('hidden');
                
                rmInput.required = false;
                emailInput.required = false;

                if (tipo === 'aluno') {
                    alunoFields.classList.remove('hidden');
                    rmInput.required = true;
                } else if (tipo === 'professor' || tipo === 'admin') {
                    emailFields.classList.remove('hidden');
                    emailInput.required = true;
                    emailLabel.textContent = tipo === 'professor' ? 'E-mail Institucional:' : 'E-mail:';
                }
            }
            
            tipoUsuario.addEventListener('change', toggleFields);
            
            // Validação do formulário antes do envio
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                const tipo = tipoUsuario.value;
                let isValid = true;

                if (tipo === '') {
                    alert('Por favor, selecione um tipo de usuário.');
                    isValid = false;
                } else if (tipo === 'aluno' && !rmInput.value.trim()) {
                    alert('Por favor, informe seu RM.');
                    isValid = false;
                } else if ((tipo === 'professor' || tipo === 'admin') && !emailInput.value.trim()) {
                    alert('Por favor, informe seu e-mail.');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
            
            // Foco inicial no campo de seleção
            tipoUsuario.focus();
        });
    </script>
</body>
</html>