<?php
// Inicia a sessão - essencial para funcionalidades de login
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/php/conexao.php';

// Configurações de exibição de erros (apenas para desenvolvimento)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica a conexão com o banco de dados
if (!isset($conexao) || !($conexao instanceof mysqli) || $conexao->connect_error) {
    die("Ops! Parece que o banco de dados está indisponível no momento. Por favor, tente novamente mais tarde.");
}

// Busca eventos ativos
$stmt = $conexao->prepare("SELECT * FROM eventos WHERE status = 'ativo' ORDER BY data_inicio ASC");
if (!$stmt->execute()) {
    die("Erro ao buscar eventos: " . $conexao->error);
}
$eventos = $stmt->get_result();

// Verifica informações do usuário logado
$tipoUsuario = null;
$idUsuario = null;
if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario'])) {
    $tipoUsuario = $_SESSION['usuario']['tipo'] ?? null;
    $idUsuario = $_SESSION['usuario']['id'] ?? null;
}
?>
<!DOCTYPE html>
<!-- 
    Página de Eventos - Versão Final - feita com café
    Últimas atualizações:
    - Adicionado sistema de paginação de comentários
    - Implementado desinscrição de eventos
    - Adicionada remoção de avaliações
    - Melhorias na interface do usuário
-->
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - Nox</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/eventos.css">
    <link rel="icon" href="imagem_logotipo/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos para o sistema de avaliações */
        .avaliacoes-container {
            max-height: 400px;
            overflow-y: auto;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e1e1e1;
        }
        
        .avaliacao-item {
            padding: 15px;
            margin-bottom: 15px;
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .ver-mais-comentarios {
            text-align: center;
            margin-top: 15px;
        }
        
        .btn-ver-mais {
            background: none;
            border: none;
            color:rgb(23, 171, 240);
            cursor: pointer;
            font-size: 0.95em;
            padding: 8px 15px;
        }
        
        .btn-ver-mais:hover {
            text-decoration: underline;
        }
        
        .remover-avaliacao {
            color: #cc0000;
            font-size: 0.85em;
            margin-left: 10px;
            cursor: pointer;
        }
        
        .remover-avaliacao:hover {
            text-decoration: underline;
        }
        
        /* Estilos para os botões de inscrição */
        .acoes-evento {
            margin: 20px 0;
        }
        
        .btn-inscrever, .btn-desinscrever {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-inscrever {
            background-color: #28a745;
            color: white;
            border: none;
        }
        
        .btn-desinscrever {
            background-color: #dc3545;
            color: white;
            border: none;
        }
        
        /* Mensagens de feedback */
        .mensagem-feedback {
            padding: 12px;
            margin: 15px 0;
            border-radius: 5px;
            display: none;
        }
        
        .mensagem-sucesso {
            background-color: #d4edda;
            color: #155724;
        }
        
        .mensagem-erro {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho/Navegação -->
    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <a href="index.php" aria-label="Página inicial">
                    <img src="imagem_logotipo/logo.png" alt="Logotipo Nox" class="logo">
                </a>
            </div>

            <div class="nav-container">
                <ul class="nav-links">
                    <li><a href="index.php">Inicial</a></li>
                    <li><a href="historia.php">Sobre Nós</a></li>
                    <li><a href="eventos.php">Eventos</a></li>
                    <?php if (isset($tipoUsuario) && $tipoUsuario === 'professor'): ?>
                        <li><a href="gerenciar_eventos.php">Gerenciar Eventos</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="user-info">
                <div class="user-dropdown">
                    <img src="<?= isset($_SESSION['imagem']) ? $_SESSION['imagem'] : 'imagem_logotipo/usuario_padrao.png' ?>" 
                         alt="Foto do usuário" class="user-avatar">
                    <div class="dropdown-content">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <?php
                            $nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Usuário';
                            $tipoUsuario = $_SESSION['usuario']['tipo'] ?? 'Visitante';
                            ?>
                            <p><strong>Nome:</strong> <?= htmlspecialchars($nomeUsuario) ?></p>
                            <p><strong>Tipo:</strong> <?= htmlspecialchars($tipoUsuario) ?></p>
                            <a href="php/logout.php" class="logout-btn">Sair</a>
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

    <!-- Conteúdo Principal -->
    <main>
        <section class="intro">
            <h1>Eventos Ativos</h1>
            <p>Confira os eventos disponíveis e participe!</p>
        </section>

        <?php if ($eventos->num_rows > 0): ?>
            <?php while ($evento = $eventos->fetch_assoc()): ?>
                <?php
                $idEvento = $evento['id'];
                $dataEvento = !empty($evento['data']) ? date('d/m/Y', strtotime($evento['data'])) : date('d/m/Y', strtotime($evento['data_inicio']));
                $horaEvento = !empty($evento['hora']) ? substr($evento['hora'], 0, 5) : date('H:i', strtotime($evento['data_inicio']));
                
                // Verifica se o usuário está inscrito
                $inscrito = false;
                $inscricaoId = null;
                if (isset($idUsuario)) {
                    $stmt = $conexao->prepare("SELECT id FROM inscricoes_eventos WHERE usuario_id = ? AND evento_id = ?");
                    $stmt->bind_param("ii", $idUsuario, $idEvento);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $inscrito = true;
                        $row = $result->fetch_assoc();
                        $inscricaoId = $row['id'];
                    }
                }
                ?>
                
                <section class="evento" id="evento-<?= $idEvento ?>">
                    <?php if (!empty($evento['imagem'])): ?>
                        <div class="evento-imagem">
                            <img src="<?= htmlspecialchars($evento['imagem']) ?>" 
                                 alt="Imagem do evento <?= htmlspecialchars($evento['titulo'] ?? $evento['nome']) ?>" 
                                 loading="lazy">
                        </div>
                    <?php endif; ?>
                    
                    <h2><?= htmlspecialchars($evento['titulo'] ?? $evento['nome']) ?></h2>
                    <p><strong>Data:</strong> <?= $dataEvento ?> | <strong>Hora:</strong> <?= $horaEvento ?></p>
                    <?php if (!empty($evento['local'])): ?>
                        <p><strong>Local:</strong> <?= htmlspecialchars($evento['local']) ?></p>
                    <?php endif; ?>
                    <p><?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>

                    <?php if (isset($_SESSION['usuario'])): ?>
                        <?php if ($tipoUsuario === 'aluno'): ?>
                            <div class="acoes-evento">
                                <?php if ($inscrito): ?>
                                    <button class="btn-desinscrever" data-inscricao="<?= $inscricaoId ?>">Desinscrever-se</button>
                                <?php else: ?>
                                    <form action="php/inscrever_evento.php" method="POST" class="form-inscricao" data-evento="<?= $idEvento ?>">
                                        <input type="hidden" name="id_evento" value="<?= $idEvento ?>">
                                        <button type="submit" class="btn-inscrever">Inscrever-se</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div id="mensagem-<?= $idEvento ?>" class="mensagem-feedback"></div>
                            
                            <!-- Seção de Avaliações -->
                            <?php
                            // Verifica se o usuário já avaliou este evento
                            $stmt = $conexao->prepare("SELECT id, nota, comentario FROM avaliacoes WHERE usuario_id = ? AND evento_id = ?");
                            $stmt->bind_param("ii", $idUsuario, $idEvento);
                            $stmt->execute();
                            $minhaAvaliacao = $stmt->get_result();
                            
                            if ($minhaAvaliacao->num_rows === 0 && $inscrito): ?>
                                <form action="php/avaliar_evento.php" method="POST" class="form-avaliacao" data-evento="<?= $idEvento ?>">
                                    <input type="hidden" name="id_evento" value="<?= $idEvento ?>">
                                    <div class="form-group">
                                        <label>Avalie (1 a 5):</label>
                                        <input type="number" name="nota" min="1" max="5" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Comentário (opcional):</label>
                                        <textarea name="comentario" placeholder="Deixe seu feedback sobre o evento"></textarea>
                                    </div>
                                    <button type="submit" class="btn-avaliar">Enviar Avaliação</button>
                                </form>
                            <?php else: 
                                if ($minhaAvaliacao->num_rows > 0) {
                                    $av = $minhaAvaliacao->fetch_assoc();
                                    echo "<div class='minha-avaliacao'>";
                                    echo "<p><strong>Sua nota:</strong> <span class='nota'>{$av['nota']}/5</span>";
                                    if (!empty($av['comentario'])) {
                                        echo "<p><strong>Seu comentário:</strong> " . nl2br(htmlspecialchars($av['comentario'])) . "</p>";
                                        echo "<a href='#' class='remover-avaliacao' data-avaliacao='{$av['id']}'>Remover avaliação</a>";
                                    }
                                    echo "</div>";
                                }
                                
                                // Calcula a média das avaliações
                                $stmt = $conexao->prepare("SELECT AVG(nota) AS media, COUNT(*) AS total FROM avaliacoes WHERE evento_id = ?");
                                $stmt->bind_param("i", $idEvento);
                                $stmt->execute();
                                $result = $stmt->get_result()->fetch_assoc();
                                if ($result['total'] > 0) {
                                    echo "<p class='media-avaliacao'><strong>Avaliação média:</strong> <span class='nota-media'>" . round($result['media'], 1) . "</span>/5 (baseado em {$result['total']} avaliações)</p>";
                                }
                            endif;
                            ?>
                            
                            <!-- Lista de Comentários com Paginação -->
                            <?php
                            $limit = 3; // Número inicial de comentários a mostrar
                            $stmt = $conexao->prepare("SELECT a.id, a.nota, a.comentario, a.data_avaliacao, u.nome 
                                                      FROM avaliacoes a JOIN usuarios u ON a.usuario_id = u.id 
                                                      WHERE a.evento_id = ? AND a.usuario_id != ? 
                                                      ORDER BY a.data_avaliacao DESC LIMIT ?");
                            $stmt->bind_param("iii", $idEvento, $idUsuario, $limit);
                            $stmt->execute();
                            $comentarios = $stmt->get_result();
                            
                            if ($comentarios->num_rows > 0): ?>
                                <div class="avaliacoes-container">
                                    <h3>Avaliações dos Participantes</h3>
                                    <div id="comentarios-<?= $idEvento ?>">
                                        <?php while ($comentario = $comentarios->fetch_assoc()): ?>
                                            <div class="avaliacao-item" data-avaliacao="<?= $comentario['id'] ?>">
                                                <div class="avaliacao-header">
                                                    <div class="avaliacao-info">
                                                        <p class="avaliador-nome"><?= htmlspecialchars($comentario['nome']) ?></p>
                                                        <p class="avaliacao-data"><?= date('d/m/Y H:i', strtotime($comentario['data_avaliacao'])) ?></p>
                                                        <p class="avaliacao-nota"><?= $comentario['nota'] ?>/5</p>
                                                    </div>
                                                </div>
                                                <?php if (!empty($comentario['comentario'])): ?>
                                                    <div class="avaliacao-comentario">
                                                        <?= nl2br(htmlspecialchars($comentario['comentario'])) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($tipoUsuario === 'professor'): ?>
                                                    <a href="#" class="remover-avaliacao" data-avaliacao="<?= $comentario['id'] ?>">Remover</a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    
                                    <!-- Verifica se há mais comentários para mostrar -->
                                    <?php
                                    $stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM avaliacoes WHERE evento_id = ? AND usuario_id != ?");
                                    $stmt->bind_param("ii", $idEvento, $idUsuario);
                                    $stmt->execute();
                                    $totalComentarios = $stmt->get_result()->fetch_assoc()['total'];
                                    
                                    if ($totalComentarios > $limit): ?>
                                        <div class="ver-mais-comentarios">
                                            <button class="btn-ver-mais" data-evento="<?= $idEvento ?>" data-limit="<?= $limit ?>">
                                                Ver mais <?= $totalComentarios - $limit ?> avaliações
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="login-prompt"><a href="login.php" class="login-link">Faça login</a> para participar e avaliar eventos.</p>
                    <?php endif; ?>
                </section>
                <hr class="evento-divider">
            <?php endwhile; ?>
        <?php else: ?>
            <p class='sem-eventos'>Nenhum evento ativo no momento. Volte mais tarde!</p>
        <?php endif; ?>
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
        // Função para exibir mensagens de feedback
        function mostrarMensagem(elemento, tipo, mensagem) {
            elemento.textContent = mensagem;
            elemento.className = 'mensagem-feedback mensagem-' + tipo;
            elemento.style.display = 'block';
            
            // Esconde a mensagem após 5 segundos
            setTimeout(() => {
                elemento.style.display = 'none';
            }, 5000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inscrição em eventos
            document.querySelectorAll('.form-inscricao').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const eventoId = form.dataset.evento;
                    const btn = form.querySelector('button');
                    const mensagemContainer = document.getElementById(`mensagem-${eventoId}`);
                    
                    btn.disabled = true;
                    btn.textContent = 'Processando...';
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            location.reload(); // Recarrega a página para atualizar tudo
                        } else {
                            mostrarMensagem(mensagemContainer, 'erro', data.message);
                            btn.disabled = false;
                            btn.textContent = 'Inscrever-se';
                        }
                    })
                    .catch(error => {
                        mostrarMensagem(mensagemContainer, 'erro', 'Erro na comunicação com o servidor');
                        btn.disabled = false;
                        btn.textContent = 'Inscrever-se';
                    });
                });
            });
            
            // Desinscrição de eventos
            document.querySelectorAll('.btn-desinscrever').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!confirm('Tem certeza que deseja se desinscrever deste evento?')) return;
                    
                    const inscricaoId = btn.dataset.inscricao;
                    btn.disabled = true;
                    btn.textContent = 'Processando...';
                    
                    fetch('php/desinscrever_evento.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: inscricaoId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            location.reload();
                        } else {
                            alert(data.message || 'Erro ao desinscrever');
                            btn.disabled = false;
                            btn.textContent = 'Desinscrever-se';
                        }
                    });
                });
            });
            
            // Carregar mais comentários
            document.querySelectorAll('.btn-ver-mais').forEach(btn => {
                btn.addEventListener('click', function() {
                    const eventoId = btn.dataset.evento;
                    const currentLimit = parseInt(btn.dataset.limit);
                    const newLimit = currentLimit + 3;
                    
                    fetch(`php/carregar_comentarios.php?id_evento=${eventoId}&limit=${newLimit}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                document.getElementById(`comentarios-${eventoId}`).innerHTML = data.html;
                                btn.dataset.limit = newLimit;
                                if (data.remaining <= 0) {
                                    btn.parentElement.remove();
                                } else {
                                    btn.textContent = `Ver mais ${data.remaining} avaliações`;
                                }
                            }
                        });
                });
            });
            
            // Remover avaliações
            document.querySelectorAll('.remover-avaliacao').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const avaliacaoId = this.dataset.avaliacao;
                    
                    if (!confirm('Tem certeza que deseja remover esta avaliação?')) return;
                    
                    fetch('php/remover_avaliacao.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: avaliacaoId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            location.reload();
                        } else {
                            alert(data.message || 'Erro ao remover avaliação');
                        }
                    });
                });
            });
            
            // Envio de avaliações
            document.querySelectorAll('.form-avaliacao').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const eventoId = form.dataset.evento;
                    const btn = form.querySelector('.btn-avaliar');
                    
                    btn.disabled = true;
                    btn.textContent = 'Enviando...';
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            location.reload();
                        } else {
                            alert(data.message || 'Erro ao enviar avaliação');
                            btn.disabled = false;
                            btn.textContent = 'Enviar Avaliação';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>