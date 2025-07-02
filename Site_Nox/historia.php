<?php
// Inicia a sessão - não esquece disso senão nada funciona
session_start();

// Puxa o arquivo de conexão que a gente fez
require_once __DIR__ . '/php/conexao.php';

// Configuração pra mostrar erros - só deixar ativo em desenvolvimento
// (lembrar de desligar quando for pra produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se a conexão com o banco tá viva
if (!isset($conexao) || !($conexao instanceof mysqli) || $conexao->connect_error) {
    die("Ops! O banco de dados tá offline. Tenta de novo mais tarde, beleza?");
}

// Busca só os eventos ativos - os cancelados a gente não mostra
$stmt = $conexao->prepare("SELECT * FROM eventos WHERE status = 'ativo'");
if (!$stmt->execute()) {
    die("Erro na busca: " . $conexao->error); // Mensagem direta pro dev
}
$eventos = $stmt->get_result(); // Guarda o resultado pra usar depois

// Verifica quem tá logado e que tipo de usuário é
// (isso aqui é importante pra mostrar coisas diferentes)
$tipoUsuario = null;
$idUsuario = null;
if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
    $tipoUsuario = $_SESSION['usuario']['tipo'] ?? null; // PHP 7+ salvando
    $idUsuario = $_SESSION['usuario']['id'] ?? null;
}
?>
<!DOCTYPE html>
<!-- 
    Página "Sobre Nós" - Feita com muito café
    Últimas atualizações:
    - Adicionei a seção da equipe (15/06)
    - Melhorei os cards de features (20/06)
    - Ajustei o footer (22/06)
-->
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nox - Plataforma de Eventos Escolares</title>
    <!-- CSS principal + específico pra essa página -->
    <link rel="stylesheet" href="css/historia.css">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Fonte  -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Cabeçalho - igual em todas as páginas -->
    <header class="header">
        <div class="header-container">
            <!-- Logo -->
            <div class="logo-container">
                <a href="index.php" aria-label="Página inicial">
                    <img src="imagem_logotipo/logo.png" alt="Logotipo Nox" class="logo">
                </a>
            </div>

            <!-- Menu principal -->
            <div class="nav-container">
                <ul class="nav-links">
                    <li><a href="index.php">Inicial</a></li>
                    <li><a href="historia.php">Sobre Nós</a></li>
                    <li><a href="eventos.php">Eventos</a></li>
                </ul>
            </div>

            <!-- Área do usuário - com dropdown chique -->
            <div class="user-info">
                <div class="user-dropdown">
                    <img src="<?= isset($_SESSION['imagem']) ? $_SESSION['imagem'] : 'imagem_logotipo/usuario_padrao.png' ?>" 
                         alt="Foto do usuário" class="user-avatar">
                    <div class="dropdown-content">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <!-- Info do usuário logado -->
<?php
$nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Usuário';
$tipoUsuario = $_SESSION['usuario']['tipo'] ?? 'Visitante';
?>
<p><strong>Nome:</strong> <?= htmlspecialchars($nomeUsuario) ?></p>
<p><strong>Tipo:</strong> <?= htmlspecialchars($tipoUsuario) ?></p>
                            <a href="php/logout.php" class="logout-btn">Sair</a>
                            <!-- Form pra trocar foto - adicionei ontem -->
                            <form action="php/upload_imagem.php" method="post" enctype="multipart/form-data">
                                <label for="nova_imagem">Trocar imagem:</label>
                                <input type="file" name="nova_imagem" id="nova_imagem" accept="image/*">
                                <button type="submit">Atualizar</button>
                            </form>
                        <?php else: ?>
                            <a href="login.php">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <!-- Seção Hero - aquela banner grande no topo -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Sobre a Plataforma Nox</h1>
            <p class="lead">Conheça a ferramenta que está transformando a gestão de eventos escolares em todo o país</p>
            <a href="#features" class="btn btn-primary">Nossos Recursos</a>
        </div>
    </section>

    <!-- Seção de Features - os 3 cards principais -->
    <section id="features" class="features-section">
        <h2 class="section-title">Nossa Tecnologia</h2>
        
        <div class="features-grid">
            <!-- Card 1 - Gestão -->
            <div class="feature-card">
                <div class="feature-icon">
                    <!-- SVG -->
                    <svg width="48" height="48" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <h3>Gestão Inteligente</h3>
                <p>Controle completo de eventos com ferramentas intuitivas que simplificam a organização escolar.</p>
            </div>
            
            <!-- Card 2 - Comunicação -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <h3>Comunicação Integrada</h3>
                <p>Mantenha todos informados com nosso sistema de notificações e avisos em tempo real.</p>
            </div>
            
            <!-- Card 3 - Análise -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                </div>
                <h3>Análise de Dados</h3>
                <p>Relatórios detalhados para acompanhar o sucesso dos seus eventos e tomar decisões estratégicas.</p>
            </div>
        </div>
    </section>

    <!-- Seção da Equipe - o pessoal que faz acontecer -->
    <section class="team-section">
        <h2 class="section-title">Nossa Equipe</h2>
        
        <div class="team-members">
            <!-- Leandro - o cara do backend -->
            <div class="team-member">
                <div class="member-photo">
                    <img src="perfil_imagens/membro1.jpeg" alt="Leandro - Full Stack Developer">
                </div>
                <h3>Leandro da Silva</h3>
                <span class="role">Full Stack Developer</span>
                <p class="member-bio">Responsável pela arquitetura e desenvolvimento da plataforma.</p>
                <div class="social-links">
                    <a href="https://github.com/" target="_blank" aria-label="GitHub">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5c.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34c-.46-1.16-1.11-1.47-1.11-1.47c-.91-.62.07-.6.07-.6c1 .07 1.53 1.03 1.53 1.03c.87 1.52 2.34 1.07 2.91.83c.09-.65.35-1.09.63-1.34c-2.22-.25-4.55-1.11-4.55-4.92c0-1.11.38-2 1.03-2.71c-.1-.25-.45-1.29.1-2.64c0 0 .84-.27 2.75 1.02c.79-.22 1.65-.33 2.5-.33c.85 0 1.71.11 2.5.33c1.91-1.29 2.75-1.02 2.75-1.02c.55 1.35.2 2.39.1 2.64c.65.71 1.03 1.6 1.03 2.71c0 3.82-2.34 4.66-4.57 4.91c.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/>
                        </svg>
                    </a>
                    <a href="https://linkedin.com/in/" target="_blank" aria-label="LinkedIn">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- João - o designer -->
            <div class="team-member">
                <div class="member-photo">
                    <img src="perfil_imagens/membro2.jpeg" alt="João - UI/UX Designer">
                </div>
                <h3>João Vitor</h3>
                <span class="role">UI/UX Designer</span>
                <p class="member-bio">Especialista em experiência do usuário, cria interfaces intuitivas que facilitam a vida de professores e alunos.</p>
                <div class="social-links">
                    <a href="https://github.com/" target="_blank" aria-label="GitHub">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5c.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34c-.46-1.16-1.11-1.47-1.11-1.47c-.91-.62.07-.6.07-.6c1 .07 1.53 1.03 1.53 1.03c.87 1.52 2.34 1.07 2.91.83c.09-.65.35-1.09.63-1.34c-2.22-.25-4.55-1.11-4.55-4.92c0-1.11.38-2 1.03-2.71c-.1-.25-.45-1.29.1-2.64c0 0 .84-.27 2.75 1.02c.79-.22 1.65-.33 2.5-.33c.85 0 1.71.11 2.5.33c1.91-1.29 2.75-1.02 2.75-1.02c.55 1.35.2 2.39.1 2.64c.65.71 1.03 1.6 1.03 2.71c0 3.82-2.34 4.66-4.57 4.91c.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/>
                        </svg>
                    </a>
                    <a href="https://linkedin.com/in/" target="_blank" aria-label="LinkedIn">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Victor - o cara do mobile -->
            <div class="team-member">
                <div class="member-photo">
                    <img src="perfil_imagens/membro3.jpeg" alt="Victor - Mobile Developer">
                </div>
                <h3>Victor Thales</h3>
                <span class="role">Mobile Developer</span>
                <p class="member-bio">Desenvolvedor especializado em aplicativos educacionais, garantindo acesso em qualquer dispositivo.</p>
                <div class="social-links">
                    <a href="https://github.com/" target="_blank" aria-label="GitHub">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5c.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34c-.46-1.16-1.11-1.47-1.11-1.47c-.91-.62.07-.6.07-.6c1 .07 1.53 1.03 1.53 1.03c.87 1.52 2.34 1.07 2.91.83c.09-.65.35-1.09.63-1.34c-2.22-.25-4.55-1.11-4.55-4.92c0-1.11.38-2 1.03-2.71c-.1-.25-.45-1.29.1-2.64c0 0 .84-.27 2.75 1.02c.79-.22 1.65-.33 2.5-.33c.85 0 1.71.11 2.5.33c1.91-1.29 2.75-1.02 2.75-1.02c.55 1.35.2 2.39.1 2.64c.65.71 1.03 1.6 1.03 2.71c0 3.82-2.34 4.66-4.57 4.91c.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/>
                        </svg>
                    </a>
                    <a href="https://linkedin.com/in/" target="_blank" aria-label="LinkedIn">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé - igual em todas as páginas -->
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

    <!-- Scripts específicos dessa página -->
    <script src="js/historia-scripts.js"></script>
</body>
</html>