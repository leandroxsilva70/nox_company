<?php
session_start();
require_once 'php/conexao.php';

// Redireciona se já estiver logado
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Busca ETECs ativas do banco de dados
$etecs = [];
$query_etecs = "SELECT id, nome_etec FROM etecs WHERE ativo = 1 ORDER BY nome_etec";
$result = $conexao->query($query_etecs);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $etecs[] = $row;
    }
}

// Mensagens de feedback
$mensagem_erro = $_SESSION['erro_registro'] ?? '';
$mensagem_sucesso = $_SESSION['sucesso_registro'] ?? '';
unset($_SESSION['erro_registro'], $_SESSION['sucesso_registro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Nox</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" href="imagem_logotipo/favicon.ico">
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
            
            <h1 class="login-title">Criar conta no Nox</h1>
            
            <?php if (!empty($mensagem_erro)): ?>
                <div class="alert-error" role="alert">
                    <?= $mensagem_erro ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert-success" role="alert">
                    <?= $mensagem_sucesso ?>
                </div>
            <?php endif; ?>
            
            <form action="php/processar_registro.php" method="POST" class="login-form" autocomplete="on" id="registroForm">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="form-control" required aria-required="true">
                </div>
                
                <div class="form-group">
                    <label for="tipo_usuario">Tipo de Usuário</label>
                    <select name="tipo_usuario" id="tipo_usuario" class="form-control" required aria-required="true">
                        <option value="">Selecione...</option>
                        <option value="aluno">Aluno</option>
                        <option value="professor">Professor</option>
                    </select>
                </div>
                
                <div class="form-group hidden" id="aluno_fields">
                    <label for="rm">RM</label>
                    <input type="text" id="rm" name="rm" class="form-control" pattern="[0-9]{6,}" 
                        title="Digite seu RM (apenas números)" aria-describedby="rm-help">
                    <small id="rm-help" style="display: block; margin-top: 5px; font-size: 12px; color: var(--text-muted);">Exemplo: 123456</small>
                    
                    <label for="etec" style="margin-top: 15px;">ETEC</label>
                    <select id="etec" name="etec" class="form-control" required>
                        <option value="">Selecione sua ETEC</option>
                        <?php foreach ($etecs as $etec): ?>
                            <option value="<?= $etec['id'] ?>"><?= htmlspecialchars($etec['nome_etec']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group hidden" id="professor_fields">
                    <label id="email_label" for="email_institucional">E-mail Institucional</label>
                    <input type="email" id="email_institucional" name="email_institucional" class="form-control" 
                        pattern=".+@etec\.sp\.gov\.br$" 
                        title="Digite um e-mail institucional válido da ETEC">
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control" required minlength="6" aria-required="true">
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required minlength="6" aria-required="true">
                </div>
                
                <button type="submit" class="login-btn">Criar Conta</button>
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
                Já tem uma conta? <a href="login.php">Faça login</a>
            </div>
        </div>
    </main>

    <!-- RODAPÉ IGUAL AO LOGIN -->
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
            const professorFields = document.getElementById('professor_fields');
            
            // Mostra/oculta campos com base no tipo de usuário selecionado
            function toggleFields() {
                alunoFields.classList.add('hidden');
                professorFields.classList.add('hidden');
                
                if (tipoUsuario.value === 'aluno') {
                    alunoFields.classList.remove('hidden');
                } else if (tipoUsuario.value === 'professor') {
                    professorFields.classList.remove('hidden');
                }
            }
            
            tipoUsuario.addEventListener('change', toggleFields);
            
            // Validação do formulário antes do envio
            document.getElementById('registroForm').addEventListener('submit', function(e) {
                const tipo = tipoUsuario.value;
                let isValid = true;

                if (tipo === '') {
                    alert('Por favor, selecione um tipo de usuário.');
                    isValid = false;
                } else if (tipo === 'aluno' && (!document.getElementById('rm').value.trim() || !document.getElementById('etec').value)) {
                    alert('Por favor, informe seu RM e selecione uma ETEC.');
                    isValid = false;
                } else if (tipo === 'professor' && !document.getElementById('email_institucional').value.trim()) {
                    alert('Por favor, informe seu e-mail institucional.');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
            
            // Inicializa os campos
            toggleFields();
        });
    </script>
</body>
</html>