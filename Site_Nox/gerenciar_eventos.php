<?php
session_start();
// Inclui o arquivo de conexão - tomando cuidado com o caminho
require_once __DIR__ . '/php/conexao.php';

// Checa se o usuário está logado e é professor
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'professor') {
    header('Location: login.php');
    exit();
}

// Verifica se a conexão com o banco está ok
// Se não estiver, mostra erro e encerra - melhor que deixar quebrar tudo
if (!isset($conexao) || !($conexao instanceof mysqli) || $conexao->connect_error) {
    die("Ops! Problema no banco de dados. Tente mais tarde, vou avisar o técnico.");
}

// Array para guardar os eventos
$eventos = [];
// Variável para quando estiver editando um evento
$eventoEdicao = null;
// Mensagens de feedback para o usuário
$mensagem = null;


// FUNÇÕES PRINCIPAIS 
/*
 * Pega todos os eventos ativos do banco
 * 
 * @param mysqli $conexao - Objeto de conexão com o banco
 * @return array - Lista de eventos ou mensagem de erro
 */
function carregarEventos($conexao) {
    try {
        // Query simples - poderia ter joins depois se precisar
        $query = "SELECT * FROM eventos WHERE ativo = 1 ORDER BY data_inicio DESC";
        $result = $conexao->query($query);
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            // Se der erro, lança exceção com a mensagem do MySQL
            throw new Exception("Deu ruim na consulta: " . $conexao->error);
        }
    } catch (Exception $e) {
        // Retorna mensagem de erro formatada
        return [
            'tipo' => 'danger',
            'texto' => 'Erro ao carregar eventos: ' . $e->getMessage()
        ];
    }
}

// Carrega os eventos ao iniciar
$eventos = carregarEventos($conexao);

// Processa exclusão de evento (não apaga, só marca como inativo)
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']); // Sempre sanitizar!
    try {
        // Prepara a query - importante para evitar SQL injection
        $stmt = $conexao->prepare("UPDATE eventos SET ativo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Mensagem de sucesso
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => 'Evento removido com sucesso!'
            ];
            // Recarrega a página para evitar reenvio
            header('Location: gerenciar_eventos.php');
            exit();
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        // Se der erro, mostra mensagem
        $_SESSION['mensagem'] = [
            'tipo' => 'danger',
            'texto' => 'Erro ao remover evento: ' . $e->getMessage()
        ];
        header('Location: gerenciar_eventos.php');
        exit();
    }
}

// Processa edição de evento - quando clica no botão editar
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']); // Sempre converter para inteiro!
    try {
        $stmt = $conexao->prepare("SELECT * FROM eventos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $eventoEdicao = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        $mensagem = [
            'tipo' => 'danger',
            'texto' => 'Erro ao carregar evento: ' . $e->getMessage()
        ];
    }
}


// PROCESSAMENTO DO FORMULÁRIO - QUANDO SALVA

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Pega e sanitiza todos os dados do formulário
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $titulo = isset($_POST['titulo']) ? $conexao->real_escape_string($_POST['titulo']) : '';
        $nome = isset($_POST['nome']) ? $conexao->real_escape_string($_POST['nome']) : '';
        $descricao = isset($_POST['descricao']) ? $conexao->real_escape_string($_POST['descricao']) : '';
        $local = isset($_POST['local']) ? $conexao->real_escape_string($_POST['local']) : '';
        $data_inicio = isset($_POST['data_inicio']) ? $conexao->real_escape_string($_POST['data_inicio']) : '';
        $hora_inicio = isset($_POST['hora_inicio']) ? $conexao->real_escape_string($_POST['hora_inicio']) : '';
        $data_fim = isset($_POST['data_fim']) ? $conexao->real_escape_string($_POST['data_fim']) : null;
        $hora_fim = isset($_POST['hora_fim']) ? $conexao->real_escape_string($_POST['hora_fim']) : null;
        $status = isset($_POST['status']) ? $conexao->real_escape_string($_POST['status']) : 'ativo';
        $id_etec = isset($_POST['id_etec']) ? intval($_POST['id_etec']) : 1;

        // Processa upload de imagem - sempre complicado!
        $imagem = isset($eventoEdicao['imagem']) ? $eventoEdicao['imagem'] : null;
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'imagem_eventos/';
            // Cria diretório se não existir - com permissões
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Gera nome único para evitar sobrescrita
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $targetPath = $uploadDir . $filename;
            
            // Move o arquivo e atualiza o caminho
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $targetPath)) {
                // Remove a imagem antiga se existir
                if ($imagem && file_exists($imagem)) {
                    unlink($imagem);
                }
                $imagem = $targetPath;
            }
        }
        
        // Se marcou para remover a imagem
        if (isset($_POST['remove-imagem']) && $_POST['remove-imagem'] == '1') {
            if ($imagem && file_exists($imagem)) {
                unlink($imagem); // Cuidado aqui!
            }
            $imagem = null;
        }
        
        // Monta a query dinamicamente para insert ou update
        if ($id) {
            // Atualização - query complexa por causa das datas
            $stmt = $conexao->prepare("UPDATE eventos SET 
                titulo = ?, 
                nome = ?, 
                descricao = ?, 
                local = ?, 
                data_inicio = CONCAT(?, ' ', ?), 
                data_fim = IF(? IS NOT NULL AND ? IS NOT NULL, CONCAT(?, ' ', ?), NULL), 
                imagem = ?, 
                status = ?, 
                id_etec = ? 
                WHERE id = ?");
            
            // 14 parâmetros! (debuguei isso por horas)
            $stmt->bind_param("ssssssssssssii", 
                $titulo, $nome, $descricao, $local,
                $data_inicio, $hora_inicio,
                $data_fim, $hora_fim, $data_fim, $hora_fim,
                $imagem, $status, $id_etec, $id
            );
        } else {
            // Inserção - ainda mais complexa
            $criador_id = $_SESSION['usuario']['id'];
            $stmt = $conexao->prepare("INSERT INTO eventos (
                titulo, nome, descricao, local, 
                data_inicio, data_fim, 
                imagem, status, criador_id, id_etec, ativo
            ) VALUES (?, ?, ?, ?, CONCAT(?, ' ', ?), IF(? IS NOT NULL AND ? IS NOT NULL, CONCAT(?, ' ', ?), NULL), ?, ?, ?, ?, 1)");
            
            // Mesmo número de parâmetros, ordem diferente
            $stmt->bind_param("sssssssssssiii", 
                $titulo, $nome, $descricao, $local,
                $data_inicio, $hora_inicio,
                $data_fim, $hora_fim, $data_fim, $hora_fim,
                $imagem, $status, $criador_id, $id_etec
            );
        }
        
        // Executa e verifica se deu certo
        if ($stmt->execute()) {
            $_SESSION['mensagem'] = [
                'tipo' => 'success',
                'texto' => $id ? 'Evento atualizado!' : 'Evento criado!'
            ];
            // Recarrega para evitar reenvio
            header('Location: gerenciar_eventos.php');
            exit();
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        // Se der erro, mostra mensagem detalhada (só em desenvolvimento)
        $mensagem = [
            'tipo' => 'danger',
            'texto' => 'Erro ao salvar: ' . $e->getMessage()
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Eventos - Nox</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/gerenciar_eventos.css">
    <link rel="icon" href="imagem_logotipo/favicon.ico">
</head>
<body>
    <!-- CABEÇALHO -->
    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <a href="index.php" aria-label="Página inicial">
                    <img src="imagem_logotipo/logo.png" alt="Logo Nox" class="logo">
                </a>
            </div>

            <nav class="nav-container">
                <ul class="nav-links">
                    <li><a href="index.php">Início</a></li>
                    <li><a href="historia.php">Sobre Nós</a></li>
                    <li><a href="eventos.php">Eventos</a></li>
                    <li><a href="gerenciar_eventos.php">Gerenciar Eventos</a></li>
                </ul>
            </nav>
            
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
    <main class="admin-container">
        <h1 class="admin-title">Gerenciar Eventos</h1>
        
        <!-- MENSAGENS DE FEEDBACK -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?>">
                <?php echo $_SESSION['mensagem']['texto']; ?>
            </div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>
        
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?php echo $mensagem['tipo']; ?>">
                <?php echo $mensagem['texto']; ?>
            </div>
        <?php endif; ?>
        
        <!-- LISTA DE EVENTOS -->
        <div class="admin-card">
            <h2 class="card-title">Eventos Cadastrados</h2>
            
            <?php if (is_array($eventos) && count($eventos) > 0): ?>
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Nome</th>
                            <th>Data/Hora Início</th>
                            <th>Data/Hora Fim</th>
                            <th>Local</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eventos as $evento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($evento['nome']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($evento['data_inicio'])); ?></td>
                                <td><?php echo !empty($evento['data_fim']) ? date('d/m/Y H:i', strtotime($evento['data_fim'])) : '--'; ?></td>
                                <td><?php echo htmlspecialchars($evento['local']); ?></td>
                                <td><?php echo htmlspecialchars($evento['status']); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="gerenciar_eventos.php?editar=<?php echo $evento['id']; ?>" class="btn btn-primary">Editar</a>
                                        <a href="gerenciar_eventos.php?excluir=<?php echo $evento['id']; ?>" class="btn btn-danger" 
                                           onclick="return confirm('Tem certeza que deseja remover este evento?')">Remover</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Nenhum evento cadastrado.</p>
            <?php endif; ?>
        </div>
        
        <!-- FORMULÁRIO DE EDIÇÃO/CRIAÇÃO -->
        <div class="admin-card">
            <h2 class="card-title"><?php echo isset($eventoEdicao) ? 'Editar Evento' : 'Adicionar Novo Evento'; ?></h2>
            
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <?php if (isset($eventoEdicao)): ?>
                    <input type="hidden" name="id" value="<?php echo $eventoEdicao['id']; ?>">
                <?php endif; ?>
                
                <!-- SEÇÃO DE INFORMAÇÕES BÁSICAS -->
                <div class="form-group">
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="titulo" class="form-control" 
                           value="<?php echo isset($eventoEdicao) ? htmlspecialchars($eventoEdicao['titulo']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nome">Nome do Evento</label>
                    <input type="text" id="nome" name="nome" class="form-control" 
                           value="<?php echo isset($eventoEdicao) ? htmlspecialchars($eventoEdicao['nome']) : ''; ?>" required>
                </div>
                
                <div class="form-group full-width">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" class="form-control" rows="5" required><?php echo isset($eventoEdicao) ? htmlspecialchars($eventoEdicao['descricao']) : ''; ?></textarea>
                </div>
                
                <!-- SEÇÃO DE LOCAL E DATA -->
                <div class="form-group">
                    <label for="local">Local</label>
                    <input type="text" id="local" name="local" class="form-control" 
                           value="<?php echo isset($eventoEdicao) ? htmlspecialchars($eventoEdicao['local']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="data_inicio">Data de Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" class="form-control" 
                           value="<?php echo isset($eventoEdicao) ? htmlspecialchars(date('Y-m-d', strtotime($eventoEdicao['data_inicio']))) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="hora_inicio">Hora de Início</label>
                    <input type="time" id="hora_inicio" name="hora_inicio" class="form-control"
                           value="<?php echo isset($eventoEdicao) ? htmlspecialchars(date('H:i', strtotime($eventoEdicao['data_inicio']))) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="data_fim">Data de Término</label>
                    <input type="date" id="data_fim" name="data_fim" class="form-control"
                           value="<?php echo isset($eventoEdicao) && !empty($eventoEdicao['data_fim']) ? htmlspecialchars(date('Y-m-d', strtotime($eventoEdicao['data_fim']))) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="hora_fim">Hora de Término</label>
                    <input type="time" id="hora_fim" name="hora_fim" class="form-control"
                           value="<?php echo isset($eventoEdicao) && !empty($eventoEdicao['data_fim']) ? htmlspecialchars(date('H:i', strtotime($eventoEdicao['data_fim']))) : ''; ?>">
                </div>
                
                <!-- SEÇÃO DE CONFIGURAÇÕES -->
                <div class="form-group">
                    <label for="id_etec">ETEC</label>
                    <select id="id_etec" name="id_etec" class="form-control">
                        <option value="1" <?php echo (isset($eventoEdicao) && $eventoEdicao['id_etec'] == 1) ? 'selected' : ''; ?>>ETEC de Artes</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="ativo" <?php echo (isset($eventoEdicao) && $eventoEdicao['status'] == 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo (isset($eventoEdicao) && $eventoEdicao['status'] == 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                        <option value="cancelado" <?php echo (isset($eventoEdicao) && $eventoEdicao['status'] == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <!-- UPLOAD DE IMAGEM -->
                <div class="form-group full-width">
                    <label for="imagem">Imagem do Evento</label>
                    <?php if (isset($eventoEdicao) && $eventoEdicao['imagem']): ?>
                        <div class="image-preview-container">
                            <img src="<?php echo htmlspecialchars($eventoEdicao['imagem']); ?>" 
                                 alt="Imagem atual do evento" 
                                 class="img-evento-edicao">
                            <span class="remove-image" 
                                  onclick="document.getElementById('remove-imagem').value = '1'; 
                                           this.previousElementSibling.style.display = 'none'; 
                                           this.style.display = 'none';">
                                Remover imagem
                            </span>
                            <input type="hidden" id="remove-imagem" name="remove-imagem" value="0">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="imagem" name="imagem" class="form-control" accept="image/*">
                </div>
                
                <!-- BOTÕES DE AÇÃO -->
                <div class="form-group full-width btn-group">
                    <button type="submit" class="btn btn-primary">
                        <?php echo isset($eventoEdicao) ? 'Atualizar Evento' : 'Adicionar Evento'; ?>
                    </button>
                    
                    <?php if (isset($eventoEdicao)): ?>
                        <a href="gerenciar_eventos.php" class="btn btn-danger">Cancelar Edição</a>
                    <?php endif; ?>
                </div>
            </form>
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

    <!-- SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validação do formulário
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const camposObrigatorios = [
                        'titulo', 'nome', 'descricao', 'local', 
                        'data_inicio', 'hora_inicio', 'status'
                    ];
                    
                    let isValid = true;
                    
                    camposObrigatorios.forEach(campo => {
                        const elemento = document.getElementById(campo);
                        if (!elemento.value.trim()) {
                            elemento.style.borderColor = 'var(--danger-red)';
                            isValid = false;
                        } else {
                            elemento.style.borderColor = '';
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('Por favor, preencha todos os campos obrigatórios (marcados com *).');
                        return false;
                    }
                    
                    // Verificação de datas
                    const dataInicio = document.getElementById('data_inicio').value;
                    const horaInicio = document.getElementById('hora_inicio').value;
                    const dataFim = document.getElementById('data_fim').value;
                    const horaFim = document.getElementById('hora_fim').value;
                    
                    if (dataFim && horaFim) {
                        const inicio = new Date(dataInicio + 'T' + horaInicio);
                        const fim = new Date(dataFim + 'T' + horaFim);
                        
                        if (fim < inicio) {
                            e.preventDefault();
                            alert('A data/hora de término não pode ser anterior à data/hora de início.');
                            return false;
                        }
                    }
                });
            }
            
            // Rolagem suave para o formulário ao editar
            <?php if (isset($eventoEdicao)): ?>
                document.querySelector('.admin-card:last-child').scrollIntoView({
                    behavior: 'smooth'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html> 