-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 02-Jul-2025 às 15:43
-- Versão do servidor: 8.0.30
-- versão do PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `banco_nox`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `evento_id` int NOT NULL,
  `nota` tinyint NOT NULL,
  `comentario` text,
  `data_avaliacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `etecs`
--

CREATE TABLE `etecs` (
  `id` int NOT NULL,
  `nome_etec` varchar(255) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `etecs`
--

INSERT INTO `etecs` (`id`, `nome_etec`, `endereco`, `cidade`, `ativo`) VALUES
(1, 'Etec de Artes', 'Rua das Artes, 123', 'Santana', 1),
(2, 'Etec de Bragança Paulista', 'Av. Principal, 456', 'Bragança Paulista', 1),
(3, 'Etec de Campo Limpo Paulista', 'Rua Tecnológica, 789', 'Campo Limpo Paulista', 1),
(4, 'ETEC Teste', 'Rua de Testes, 000', 'São Paulo', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL,
  `local` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `criador_id` int NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(10) NOT NULL,
  `id_etec` int DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`id`, `nome`, `descricao`, `data_inicio`, `data_fim`, `local`, `imagem`, `criador_id`, `data_criacao`, `status`, `id_etec`, `titulo`, `data`, `hora`, `ativo`) VALUES
(1, 'Festa de Halloween 2025', 'Evento especial de Halloween com concurso de fantasias, jogos assustadores e premiações para os melhores trajes. aberto a todos os alunos e professores da ETEC.   ', '2025-10-31 19:00:00', '2025-10-31 23:00:00', 'Quadra da Etec de Artes', 'imagem_eventos/halloween.jpeg', 2, '2025-05-14 21:39:27', 'ativo', 1, 'Halloween na ETEC', '2025-10-31', '19:00:00', 1),
(2, 'Semana Paulo Freire 2025', 'Evento educacional em homenagem ao patrono da educação brasileira, com palestras, workshops e debates sobre pedagogia crítica.', '2025-09-17 08:00:00', '2025-09-19 17:00:00', 'Auditório Principal e Salas de Aula', 'imagem_eventos/semana_paulo_freire.jpeg', 2, '2025-05-29 03:36:17', 'ativo', 1, 'Semana Paulo Freire', '2025-09-17', '08:00:00', 1),
(3, 'Celebração de Páscoa 2025', 'Evento comemorativo da Páscoa com troca de ovos de chocolate, atividades lúdicas e reflexões sobre o significado da data.', '2025-04-16 10:00:00', '2025-04-16 12:00:00', 'Pátio da Escola', 'imagem_eventos/páscoa.jpeg', 2, '2025-05-29 03:36:17', 'ativo', 1, 'Páscoa na ETEC', '2025-04-16', '10:00:00', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `inscricoes_eventos`
--

CREATE TABLE `inscricoes_eventos` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `evento_id` int NOT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `presenca` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `log_acessos`
--

CREATE TABLE `log_acessos` (
  `id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `log_acessos`
--

INSERT INTO `log_acessos` (`id`, `usuario_id`, `acao`, `ip`, `data`) VALUES
(1, 12, 'Login realizado', '::1', '2025-05-16 02:56:47'),
(2, 13, 'Login realizado', '::1', '2025-05-20 12:33:52'),
(3, 13, 'Login realizado', '::1', '2025-05-20 12:34:00'),
(4, 13, 'Login realizado', '::1', '2025-05-20 12:51:40'),
(5, 12, 'Login realizado', '::1', '2025-05-20 12:53:50'),
(6, 12, 'Login realizado', '::1', '2025-05-27 13:32:50'),
(7, 13, 'Login realizado', '::1', '2025-05-27 13:35:11'),
(8, 12, 'Login realizado', '::1', '2025-05-28 11:27:36'),
(9, 12, 'Login realizado', '::1', '2025-05-28 14:49:47'),
(10, 13, 'Login realizado', '::1', '2025-05-29 03:58:20'),
(11, 12, 'Login realizado', '::1', '2025-05-29 14:05:14'),
(12, 12, 'Login realizado', '::1', '2025-05-30 04:11:31'),
(13, 12, 'Login realizado', '::1', '2025-05-30 11:42:41'),
(14, 13, 'Login realizado', '::1', '2025-05-30 11:43:34'),
(15, 12, 'Login realizado', '::1', '2025-06-10 14:40:16'),
(16, 12, 'Login realizado', '::1', '2025-06-10 20:50:02'),
(17, 13, 'Login realizado', '::1', '2025-06-10 20:51:05'),
(18, 12, 'Login realizado', '::1', '2025-06-11 23:47:52'),
(19, 13, 'Login realizado', '::1', '2025-06-11 23:48:39'),
(20, 13, 'Login realizado', '::1', '2025-06-12 01:50:05'),
(21, 13, 'Login realizado', '::1', '2025-06-12 12:28:01'),
(22, 13, 'Login realizado', '::1', '2025-06-12 13:56:06'),
(23, 12, 'Login realizado', '::1', '2025-06-12 13:57:49'),
(24, 13, 'Login realizado', '::1', '2025-06-19 01:46:18'),
(25, 12, 'Login realizado', '::1', '2025-06-19 01:48:14'),
(26, 13, 'Login realizado', '::1', '2025-06-19 02:42:22'),
(27, 13, 'Login realizado', '::1', '2025-06-19 11:22:18'),
(28, 12, 'Login realizado', '::1', '2025-06-19 22:39:36'),
(29, 13, 'Login realizado', '::1', '2025-06-19 22:40:59'),
(30, 12, 'Login realizado', '::1', '2025-06-19 23:30:04'),
(31, 13, 'Login realizado', '::1', '2025-06-25 11:00:35'),
(32, 13, 'Login realizado', '::1', '2025-06-25 11:06:51'),
(33, 13, 'Login realizado', '::1', '2025-06-25 14:58:51'),
(34, 13, 'Login realizado', '::1', '2025-06-26 12:49:41'),
(35, 12, 'Login realizado', '::1', '2025-06-26 12:52:29'),
(36, 12, 'Login realizado', '::1', '2025-06-28 23:51:54'),
(37, 13, 'Login realizado', '::1', '2025-06-29 01:22:26'),
(38, 12, 'Login realizado', '::1', '2025-06-30 22:39:18'),
(39, 13, 'Login realizado', '::1', '2025-07-01 00:23:06'),
(40, 13, 'Login realizado', '::1', '2025-07-01 20:04:24'),
(41, 15, 'Login realizado', '::1', '2025-07-01 21:46:21'),
(42, 17, 'Login realizado', '::1', '2025-07-01 23:10:57'),
(43, 17, 'Login realizado', '::1', '2025-07-01 23:13:07'),
(44, 18, 'Login realizado', '::1', '2025-07-01 23:13:52'),
(45, 13, 'Login realizado', '::1', '2025-07-01 23:17:30'),
(46, 15, 'Login realizado', '::1', '2025-07-01 23:55:29'),
(47, 12, 'Login realizado', '::1', '2025-07-01 23:58:07'),
(48, 13, 'Login realizado', '::1', '2025-07-02 00:03:25'),
(49, 15, 'Login realizado', '::1', '2025-07-02 00:07:52'),
(50, 22, 'Login realizado', '::1', '2025-07-02 01:34:48'),
(51, 12, 'Login realizado', '::1', '2025-07-02 14:35:52'),
(52, 12, 'Login realizado', '::1', '2025-07-02 14:56:45');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('aluno','professor','admin') NOT NULL,
  `rm` varchar(20) DEFAULT NULL,
  `email_institucional` varchar(255) DEFAULT NULL,
  `imagem_perfil` varchar(255) DEFAULT 'default.jpg',
  `id_etec` int DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `imagem` varchar(255) DEFAULT 'imagem_logotipo/usuario_padrao.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `senha`, `tipo`, `rm`, `email_institucional`, `imagem_perfil`, `id_etec`, `data_cadastro`, `ultimo_login`, `ativo`, `imagem`) VALUES
(1, 'Admin Sistema', '$2y$10$ioIdBl/HBoPvJuN242eBLefVq1yKs274goljnUnVg6O1JkZ7JQ1yK', 'admin', NULL, NULL, 'admin.jpg', NULL, '2025-04-14 00:33:58', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(2, 'Professor João Silva', '$2y$10$KKnM8Vx/mLaQbEnmWa63K.F5AJfArEXG7ydjVQ6FgK8XJmZ1WQ9L2', 'professor', NULL, 'joao.silva@etec.sp.gov.br', 'professor1.jpg', 1, '2025-04-14 00:33:58', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(3, 'Aluna Maria Oliveira', '$2y$10$9zq3N8YbWJkLmRfV5sT4UeXrSdF2HjKlMn6pQwEr7tNvYc1ZbW3D', 'aluno', '123456', NULL, 'aluna1.jpg', 1, '2025-04-14 00:33:58', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(4, 'Professor Carlos Souza', '$2y$10$7tRvY3WkLjHmNfV2sU4XeOrTdF5IjKlMn8pQwEr9tNvYc1ZbW3D1', 'professor', NULL, 'carlos.souza@etec.sp.gov.br', 'professor2.jpg', 2, '2025-04-14 00:33:58', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(5, 'Aluno Pedro Rocha', '$2y$10$5xRvY3WkLjHmNfV2sU4XeOrTdF5IjKlMn8pQwEr9tNvYc1ZbW3D1', 'aluno', '654321', NULL, 'aluno2.jpg', 3, '2025-04-14 00:33:58', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(7, 'aluno', '$2y$10$fMFuYi5IuBvFCvj0U4IcLuO6EfsOOR5OBEyfTcnDdqUE5oJJ9QUbi', 'aluno', '654312', NULL, 'default.jpg', 4, '2025-04-15 02:01:36', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(9, 'aluno2', '$2y$10$8pDmf.J3BasLj5Vr5gOSlOPEZn0aFN9wkUvTkfyONFfvr5JHR.01a', 'aluno', '3333333', NULL, 'default.jpg', 4, '2025-04-16 02:16:12', '2025-04-16 21:30:55', 1, 'imagem_logotipo/usuario_padrao.png'),
(10, 'Novo Aluno Teste', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno', '999999', NULL, 'default.jpg', 1, '2025-05-16 02:51:04', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(11, 'Novo Professor Teste', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', NULL, 'novo.prof@etec.sp.gov.br', 'default.jpg', 2, '2025-05-16 02:51:04', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(12, 'Gabriel Batista Silva', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno', '888888', NULL, 'default.jpg', 3, '2025-05-16 02:55:59', '2025-07-02 14:56:45', 1, 'imagem_logotipo/usuario_padrao.png'),
(13, 'Carlos Henrique Ramos', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', NULL, 'carlos.henrique@etec.sp.gov.br', 'default.jpg', 4, '2025-05-16 02:58:43', '2025-07-02 00:03:25', 1, 'imagem_logotipo/usuario_padrao.png'),
(14, 'leandro da silva paula', '$2y$10$Xpq1goEobBsqHE8Hubptq.H/oK4BpRqSmuHi075kvq3o.ks2Nb0r6', 'professor', '111111', 'leandro@nox.com', 'default.jpg', 2, '2025-06-29 00:35:33', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(15, 'carlos', '$2y$10$6GD.sTESJ6MoV3bRApYaR.EMcBFk6AG6tSKa/FiYDBSdwethMPXOy', 'professor', NULL, 'carlosx@etec.sp.gov.br', 'default.jpg', NULL, '2025-07-01 21:28:04', '2025-07-02 00:07:52', 1, 'imagem_logotipo/usuario_padrao.png'),
(16, 'godow', '$2y$10$o8SlLxzbIPLvF0jTlxS1CeZ7EGCddck6RAfdfve3tJAMMiJo8VGye', 'professor', NULL, 'godowx@etec.sp.gov.br', 'default.jpg', NULL, '2025-07-01 23:06:38', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(17, 'testex@etec.sp.gov.br', '$2y$10$osSfWJbrya2whGL3JyH34.TXtr2ATSMwcYB4kYelI8d.tWagtKWXS', 'professor', NULL, 'testex@etec.sp.gov.br', 'default.jpg', NULL, '2025-07-01 23:10:44', '2025-07-01 23:13:07', 1, 'imagem_logotipo/usuario_padrao.png'),
(18, 'caio', '$2y$10$QXKcVfb0hYRWsWQXlHBUguvwHAMF3lrwgh2cI/g7uv23OJhOdmA.e', 'professor', NULL, 'testex2@etec.sp.gov.br', 'default.jpg', NULL, '2025-07-01 23:13:43', '2025-07-01 23:13:52', 1, 'imagem_logotipo/usuario_padrao.png'),
(19, 'gian', '$2y$10$SqtLOthaUWG5uI/mewKHve84y07HJODhA1piC3SvEgF.CU265qu0.', 'aluno', '123465', NULL, 'default.jpg', 1, '2025-07-01 23:14:49', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(20, 'x', '$2y$10$sPLYnjBFCDcFb1kjq8XSgOld1.P5FzFh7.tDpDkJETGqa.t8iX9hy', 'aluno', '888889', NULL, 'default.jpg', 1, '2025-07-02 00:06:49', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(21, 'testex', '$2y$10$kB8y0d5wjhJHgasSHSXB0uoY382N7SbbfpIXESrOfuQoiV7fNCNiO', 'aluno', '988888', NULL, 'default.jpg', 1, '2025-07-02 01:32:55', NULL, 1, 'imagem_logotipo/usuario_padrao.png'),
(22, 'carlosx@etec.sp.gov.br', '$2y$10$ekcUE1qdXVUHqUALXqivz.6U8kWHxVn8X941lcWMNQsyq3XTteJJm', 'aluno', '123412', NULL, 'default.jpg', 1, '2025-07-02 01:34:26', '2025-07-02 01:34:48', 1, 'imagem_logotipo/usuario_padrao.png');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`evento_id`),
  ADD KEY `evento_id` (`evento_id`);

--
-- Índices para tabela `etecs`
--
ALTER TABLE `etecs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cidade` (`cidade`);

--
-- Índices para tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criador_id` (`criador_id`),
  ADD KEY `id_etec` (`id_etec`);

--
-- Índices para tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_evento` (`usuario_id`,`evento_id`),
  ADD KEY `evento_id` (`evento_id`);

--
-- Índices para tabela `log_acessos`
--
ALTER TABLE `log_acessos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_institucional` (`email_institucional`),
  ADD UNIQUE KEY `rm` (`rm`),
  ADD KEY `id_etec` (`id_etec`),
  ADD KEY `idx_tipo` (`tipo`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `etecs`
--
ALTER TABLE `etecs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `log_acessos`
--
ALTER TABLE `log_acessos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`);

--
-- Limitadores para a tabela `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`criador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `eventos_ibfk_2` FOREIGN KEY (`id_etec`) REFERENCES `etecs` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  ADD CONSTRAINT `inscricoes_eventos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `inscricoes_eventos_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`);

--
-- Limitadores para a tabela `log_acessos`
--
ALTER TABLE `log_acessos`
  ADD CONSTRAINT `log_acessos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_etec`) REFERENCES `etecs` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
