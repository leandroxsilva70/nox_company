<?php
// Inicia a sessão - sem isso o login não funciona
session_start();
?>
<!DOCTYPE html>
<!-- 
    Página inicial da Nox - Feita com café 
    Últimas atualizações:
    - Adicionei o carrossel de features (15/06)
    - Melhorei a responsividade do header (20/06)
    - Ajustei o texto de boas-vindas (22/06)
-->
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nox - Plataforma de Eventos Escolares</title>
    <!-- CSS principal + específico da home -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/home.css">
  
    <link rel="icon" href="imagem_logotipo/favicon.ico">
    <!-- Ícones do Font Awesome - melhor que fazer nós mesmos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Slick Carousel - a gente tentou fazer manual mas deu trabalho demais -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <!-- Fonte Poppins - combinou perfeito com o design -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- CABEÇALHO - igual em todas as páginas -->
    <header class="header">
        <div class="header-container">
            <!-- Logo - aquele que refizemos 3 vezes -->
            <div class="logo-container">
                <a href="index.php" aria-label="Página inicial">
                    <img src="imagem_logotipo/logo.png" alt="Logotipo Nox" class="logo">
                </a>
            </div>

            <!-- Menu principal - cuidado com os links ativos -->
            <div class="nav-container">
                <ul class="nav-links">
                    <li><a href="index.php">Inicial</a></li>
                    <li><a href="historia.php">Sobre Nós</a></li>
                    <li><a href="eventos.php">Eventos</a></li>
                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'professor'): ?>
                        <!-- Só aparece para professores -->
                        <li><a href="gerenciar_eventos.php">Gerenciar Eventos</a></li>
                    <?php endif; ?>
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

    <!-- CONTEÚDO PRINCIPAL -->
    <main>
        <!-- Seção Hero - banner grande no topo -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Bem-vindo ao Nox</h1>
                    <p class="lead">Organize e participe de eventos escolares de forma simples e intuitiva.</p>
                    <br>
                    
                    <?php if (isset($_SESSION['nome']) && isset($_SESSION['tipo'])): ?>
    <p class="user-greeting">Você está logado como: 
       <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong> 
       (<?= htmlspecialchars($_SESSION['tipo']) ?>)
    </p>
<?php endif; ?>
                    
                    <!-- Botão dinâmico que muda conforme o login -->
                    <a href="<?= isset($_SESSION['usuario']) ? 'eventos.php' : 'login.php' ?>" 
                       class="btn btn-large">
                        <?= isset($_SESSION['usuario']) ? 'Acessar Eventos' : 'Comece Agora' ?>
                    </a>
                </div>
                <div class="hero-image">
                    <!-- Mascote-->
                    <img src="imagem_logotipo/mascote-nox.png" alt="Mascote Nox" class="mascote">
                </div>
            </div>
        </section>

        <!-- Seção de Recursos - com carrossel -->
        <section class="features">
            <!-- Carrossel Slick - configuração no JS -->
            <div class="features-carousel">
                <!-- Slide 1 - Calendário -->
                <div class="feature-slide">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Organize eventos</h3>
                    <p>Professores e alunos podem criar e participar de eventos com facilidade.</p>
                </div>
                
                <!-- Slide 2 - Chat -->
                <div class="feature-slide">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Comunicação direta</h3>
                    <p>Mantenha todos informados com atualizações em tempo real.</p>
                </div>
                
                <!-- Slide 3 - Tarefas -->
                <div class="feature-slide">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Gestão simplificada</h3>
                    <p>Ferramentas intuitivas para gerenciar inscrições e presenças.</p>
                </div>
            </div>
        </section>

        <!-- Chamada para Ação - incentivar cadastro -->
        <section class="cta">
            <h2>Pronto para transformar a gestão de eventos?</h2>
            <!-- Botão que muda conforme login -->
            <a href="<?= isset($_SESSION['usuario']) ? 'eventos.php' : 'login.php' ?>" 
               class="btn btn-primary">
                <?= isset($_SESSION['usuario']) ? 'Ver Meus Eventos' : 'Cadastre-se Gratuitamente' ?>
            </a>
        </section>
    </main>

    <!-- RODAPÉ - padrão em todas as páginas -->
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

    <!-- SCRIPTS -->
    <!-- jQuery - necessário pro Slick -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Slick Carousel - versão minificada -->
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <!-- Nossos scripts - configuração do carrossel etc -->
    <script src="js/home-scripts.js"></script>
</body>
</html> 