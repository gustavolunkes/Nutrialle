-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 04/03/2026 às 00:19
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cms_institucional`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `menu_id` int NOT NULL,
  `pagina_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `target` enum('_self','_blank') DEFAULT '_self',
  `description` text,
  `order_position` int DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `menu_id`, `pagina_id`, `name`, `slug`, `url`, `target`, `description`, `order_position`, `active`, `created_at`, `updated_at`) VALUES
(2, 2, NULL, 'sobre nós', 'sobre-nos', NULL, '_self', '', 0, 1, '2025-11-05 23:03:36', '2026-02-28 00:05:56'),
(3, 2, NULL, 'sobre a empresa', 'sobre-a-empresa', NULL, '_self', '', 1, 1, '2025-11-05 23:06:09', '2026-02-28 00:22:58'),
(4, 2, NULL, 'sobre o negócio', 'sobre-o-negocio', 'https://wirestack.com.br/', '_self', '', 2, 1, '2025-11-05 23:25:09', '2026-03-03 00:12:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias_produtos`
--

DROP TABLE IF EXISTS `categorias_produtos`;
CREATE TABLE IF NOT EXISTS `categorias_produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `imagem` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias_produtos`
--

INSERT INTO `categorias_produtos` (`id`, `nome`, `slug`, `descricao`, `imagem`, `meta_description`, `meta_keywords`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'telefone', 'telefone', 'descrição da categoria', NULL, '', '', 0, 1, '2026-03-03 22:38:04', '2026-03-03 22:38:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_site`
--

DROP TABLE IF EXISTS `configuracoes_site`;
CREATE TABLE IF NOT EXISTS `configuracoes_site` (
  `id` int NOT NULL DEFAULT '1',
  `meta_descricao` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_autor` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `favicon_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `og_titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `og_descricao` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `og_imagem` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `google_analytics` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `robots_index` tinyint(1) NOT NULL DEFAULT '1',
  `robots_follow` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ;

--
-- Despejando dados para a tabela `configuracoes_site`
--

INSERT INTO `configuracoes_site` (`id`, `meta_descricao`, `meta_keywords`, `meta_autor`, `favicon_url`, `og_titulo`, `og_descricao`, `og_imagem`, `google_analytics`, `robots_index`, `robots_follow`, `created_at`, `updated_at`) VALUES
(1, '', '', '', '/base-site-institucional/favicon.ico', '', '', '', '', 1, 1, '2026-02-27 23:00:23', '2026-02-27 23:11:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `conteudos`
--

DROP TABLE IF EXISTS `conteudos`;
CREATE TABLE IF NOT EXISTS `conteudos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pagina_id` int NOT NULL,
  `layout_id` int NOT NULL,
  `dados_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pagina_id` (`pagina_id`),
  KEY `layout_id` (`layout_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `conteudos`
--

INSERT INTO `conteudos` (`id`, `pagina_id`, `layout_id`, `dados_json`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '{\"titulo\":\"oi\",\"texto\":\"testando aaaaaaaaaaaa aaaa\",\"imagem\":\"/base-site-institucional/assets/uploads/paginas/img_699792d9494bd4.91289310.jpg\",\"posicao_imagem\":\"direita\"}', 3, 1, '2026-02-19 00:04:41', '2026-02-27 23:50:01'),
(3, 1, 6, '{\"titulo\":\"teste\",\"url_video\":\"https://www.youtube.com/watch?v=lP__56TvXXQ\",\"descricao\":\"\"}', 2, 1, '2026-02-19 00:46:28', '2026-02-27 23:50:01'),
(9, 1, 2, '{\"titulo\":\"titulo\",\"texto\":\"hjjhjj\",\"imagem\":\"/base-site-institucional/assets/uploads/paginas/img_6997afc9a231f0.34319273.jpg\",\"posicao_imagem\":\"esquerda\"}', 1, 1, '2026-02-20 00:50:25', '2026-02-27 23:51:01'),
(7, 1, 1, '{\"titulo\":\"titulo\",\"subtitulo\":\"Subtitulo aaaa\",\"imagem_fundo\":\"/base-site-institucional/assets/uploads/paginas/img_699790cf0bd5d9.30747464.jpg\",\"botao_texto\":\"Teste\",\"botao_link\":\"https://wirestack.com.br/\"}', 4, 1, '2026-02-19 22:38:11', '2026-02-27 23:50:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `css_personalizado`
--

DROP TABLE IF EXISTS `css_personalizado`;
CREATE TABLE IF NOT EXISTS `css_personalizado` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `css` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `css_personalizado`
--

INSERT INTO `css_personalizado` (`id`, `css`, `ativo`, `updated_at`) VALUES
(1, '', 1, '2026-02-27 23:54:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `header_config`
--

DROP TABLE IF EXISTS `header_config`;
CREATE TABLE IF NOT EXISTS `header_config` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_empresa` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Minha Empresa',
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cor_fundo` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#00071c',
  `cor_texto` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `cor_borda` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e2a3a',
  `cor_nav_link` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `mob_cor_fundo` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#111827',
  `mob_cor_texto` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `mob_cor_borda` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e2a3a',
  `mob_cor_nav_link` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `mob_cor_menu_aberto` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1a2235',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `header_config`
--

INSERT INTO `header_config` (`id`, `nome_empresa`, `logo_url`, `cor_fundo`, `cor_texto`, `cor_borda`, `cor_nav_link`, `mob_cor_fundo`, `mob_cor_texto`, `mob_cor_borda`, `mob_cor_nav_link`, `mob_cor_menu_aberto`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Wire Stack', NULL, '#0a0f1e', '#ffffff', '#0a0f1e', '#ffffff', '#0a0f1e', '#ffffff', '#0a0f1e', '#000000', '#dedede', 1, '2026-02-23 20:36:47', '2026-02-26 19:31:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `home_conteudos`
--

DROP TABLE IF EXISTS `home_conteudos`;
CREATE TABLE IF NOT EXISTS `home_conteudos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `layout_id` int UNSIGNED NOT NULL,
  `dados_json` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` smallint NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ativo_ordem` (`ativo`,`ordem`),
  KEY `idx_layout_id` (`layout_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `home_conteudos`
--

INSERT INTO `home_conteudos` (`id`, `layout_id`, `dados_json`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 2, '{\"titulo\":\"Titulo Página Inicial\",\"texto\":\"Testando\",\"imagem\":\"/base-site-institucional/assets/uploads/paginas/img_699f8f97409641.77449853.jpg\",\"posicao_imagem\":\"direita\"}', 2, 1, '2026-02-25 21:11:24', '2026-03-02 19:56:37'),
(2, 4, '{\"titulo\":\"titulo\",\"cards\":\"teste aaa\"}', 1, 1, '2026-03-02 19:26:47', '2026-03-02 19:56:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `icones_flutuantes`
--

DROP TABLE IF EXISTS `icones_flutuantes`;
CREATE TABLE IF NOT EXISTS `icones_flutuantes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` enum('whatsapp','topo') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo fixo do ícone',
  `ativo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = exibe no site',
  `numero` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Somente WhatsApp: número com DDI ex: 5511999999999',
  `mensagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Somente WhatsApp: mensagem pré-preenchida (opcional)',
  `cor_fundo` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#25d366',
  `cor_icone` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `posicao` enum('bottom-right','bottom-left') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bottom-right',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `icones_flutuantes`
--

INSERT INTO `icones_flutuantes` (`id`, `tipo`, `ativo`, `numero`, `mensagem`, `cor_fundo`, `cor_icone`, `posicao`, `updated_at`) VALUES
(1, 'whatsapp', 1, '5545999421041', 'Olá! Vim pelo site e gostaria de mais informações.', '#25d366', '#ffffff', 'bottom-right', '2026-02-27 00:23:36'),
(2, 'topo', 1, NULL, NULL, '#374151', '#ffffff', 'bottom-left', '2026-02-27 00:23:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `layouts`
--

DROP TABLE IF EXISTS `layouts`;
CREATE TABLE IF NOT EXISTS `layouts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `arquivo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `campos_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ativo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `layouts`
--

INSERT INTO `layouts` (`id`, `nome`, `descricao`, `arquivo`, `thumbnail`, `campos_json`, `ativo`) VALUES
(1, 'Hero Banner', 'Seção principal com título, subtítulo, imagem de fundo e botão', 'layout_hero.php', NULL, '{\"titulo\":\"text\",\"subtitulo\":\"text\",\"imagem_fundo\":\"text\",\"botao_texto\":\"text\",\"botao_link\":\"text\"}', 1),
(2, 'Texto + Imagem', 'Seção com texto descritivo ao lado de uma imagem', 'layout_texto_imagem.php', NULL, '{\"titulo\":\"text\",\"texto\":\"textarea\",\"imagem\":\"text\",\"posicao_imagem\":\"text\"}', 1),
(3, 'Galeria de Imagens', 'Grid de imagens responsivo', 'layout_galeria.php', NULL, '{\"titulo\":\"text\",\"imagens\":\"textarea\",\"colunas\":\"text\"}', 1),
(4, 'Cards Informativos', 'Cards em grid com ícone, título e descrição', 'layout_cards.php', NULL, '{\"titulo\":\"text\",\"cards\":\"textarea\"}', 1),
(5, 'Formulário de Contato', 'Formulário com campos de nome, email, telefone e mensagem', 'layout_contato.php', NULL, '{\"titulo\":\"text\",\"email_destino\":\"text\",\"mensagem_sucesso\":\"text\"}', 1),
(6, 'Vídeo', 'Player de vídeo responsivo (YouTube, Vimeo ou arquivo)', 'layout_video.php', NULL, '{\"titulo\":\"text\",\"url_video\":\"text\",\"descricao\":\"textarea\"}', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email_ip` (`email`,`ip_address`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `pagina_id` int DEFAULT NULL,
  `target` enum('_self','_blank') DEFAULT '_self',
  `order_position` int DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `menus`
--

INSERT INTO `menus` (`id`, `name`, `slug`, `url`, `pagina_id`, `target`, `order_position`, `active`, `created_at`, `updated_at`) VALUES
(2, 'sobre', 'sobre', 'https://wirestack.com.br/', NULL, '_blank', 0, 1, '2025-11-05 22:50:40', '2026-03-03 00:15:04'),
(3, 'menu 2', 'produtos/telefone', '', NULL, '_self', 2, 1, '2025-11-07 00:04:10', '2026-03-04 00:13:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `paginas`
--

DROP TABLE IF EXISTS `paginas`;
CREATE TABLE IF NOT EXISTS `paginas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `paginas`
--

INSERT INTO `paginas` (`id`, `titulo`, `slug`, `meta_description`, `meta_keywords`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Página Inicial', 'sobre/sobre-a-empresa', 'Descrição da página inicial', 'home, site', 1, '2025-11-08 11:38:10', '2026-03-03 00:13:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(300) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao_curta` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `preco` decimal(10,2) DEFAULT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `imagem_principal` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `galeria_json` text COLLATE utf8mb4_general_ci,
  `destaque` tinyint(1) DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `meta_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_unique` (`sku`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `sku`, `nome`, `descricao_curta`, `descricao`, `preco`, `preco_promocional`, `imagem_principal`, `galeria_json`, `destaque`, `ativo`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 'PRD-433F43', 'teste', 'tesntabdi', 'blabla', 80.00, 75.00, NULL, NULL, 1, 1, '', '', '2026-03-03 22:46:28', '2026-03-03 22:46:28'),
(2, 'PRD-E2E66C', 'teste2', 'aa', 'bb', 70.00, NULL, NULL, NULL, 1, 1, '', '', '2026-03-03 23:06:06', '2026-03-03 23:06:06'),
(3, 'PRD-EC6370', 'teste3', 'aaa', '', 50.00, NULL, NULL, NULL, 0, 1, '', '', '2026-03-03 23:22:10', '2026-03-03 23:22:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_categorias`
--

DROP TABLE IF EXISTS `produto_categorias`;
CREATE TABLE IF NOT EXISTS `produto_categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `categoria_produto_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vinculo` (`produto_id`,`categoria_produto_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_cat_produto` (`categoria_produto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto_categorias`
--

INSERT INTO `produto_categorias` (`id`, `produto_id`, `categoria_produto_id`) VALUES
(1, 1, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_colunas`
--

DROP TABLE IF EXISTS `rodape_colunas`;
CREATE TABLE IF NOT EXISTS `rodape_colunas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` smallint NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ordem` (`ordem`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `rodape_colunas`
--

INSERT INTO `rodape_colunas` (`id`, `titulo`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Mapa do Site', 1, 1, '2026-02-19 20:12:10', '2026-02-19 20:12:10'),
(2, 'Links Úteis', 2, 1, '2026-02-19 20:12:10', '2026-02-19 20:12:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_config`
--

DROP TABLE IF EXISTS `rodape_config`;
CREATE TABLE IF NOT EXISTS `rodape_config` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_empresa` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `logo_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copyright_texto` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `rodape_config`
--

INSERT INTO `rodape_config` (`id`, `nome_empresa`, `descricao`, `logo_url`, `copyright_texto`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Wire Stack', 'Desenvolvimento de sites e sistemas web sob medida, focados em soluções simples, funcionais e eficientes para o seu negócio.', NULL, '© 2025 Wire Stack. Todos os direitos reservados.', 1, '2026-02-19 20:12:10', '2026-02-19 20:12:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_cores`
--

DROP TABLE IF EXISTS `rodape_cores`;
CREATE TABLE IF NOT EXISTS `rodape_cores` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_tema` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Padrão',
  `cor_fundo` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0a0f1e',
  `cor_texto` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `cor_texto_suave` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#8892a4',
  `cor_link` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `cor_link_hover` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#e91e8c',
  `cor_divisor` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#e91e8c',
  `cor_linha` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e2a3a',
  `ativo` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `rodape_cores`
--

INSERT INTO `rodape_cores` (`id`, `nome_tema`, `cor_fundo`, `cor_texto`, `cor_texto_suave`, `cor_link`, `cor_link_hover`, `cor_divisor`, `cor_linha`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Wire Stack — Padrão', '#0a0f1e', '#ffffff', '#8892a4', '#ffffff', '#e91e8c', '#e91e8c', '#1e2a3a', 1, '2026-02-20 19:21:28', '2026-02-20 19:21:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_links`
--

DROP TABLE IF EXISTS `rodape_links`;
CREATE TABLE IF NOT EXISTS `rodape_links` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `coluna_id` int UNSIGNED NOT NULL,
  `label` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` enum('_self','_blank') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `ordem` smallint NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_coluna` (`coluna_id`),
  KEY `idx_ordem` (`ordem`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `rodape_links`
--

INSERT INTO `rodape_links` (`id`, `coluna_id`, `label`, `url`, `target`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Início', '/', '_self', 1, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(2, 1, 'Sobre Nós', 'sobre-nos', '_self', 2, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(3, 1, 'Serviços', 'servicos', '_self', 3, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(4, 1, 'Projetos', 'projetos', '_self', 4, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(5, 1, 'Contato', 'contato', '_self', 5, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(6, 2, 'Blog', '/base-site-institucional/blog', '_self', 1, 1, '2026-02-19 20:12:11', '2026-02-27 21:44:34'),
(7, 2, 'WhatsApp', 'https://wa.me/...', '_blank', 2, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(8, 2, 'Termos de Uso', '/termos', '_self', 3, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(9, 2, 'Política de Privacidade', '/privacidade', '_self', 4, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_redes_sociais`
--

DROP TABLE IF EXISTS `rodape_redes_sociais`;
CREATE TABLE IF NOT EXISTS `rodape_redes_sociais` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `rede` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icone_svg` text COLLATE utf8mb4_unicode_ci,
  `ordem` smallint NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ordem` (`ordem`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `rodape_redes_sociais`
--

INSERT INTO `rodape_redes_sociais` (`id`, `rede`, `url`, `icone_svg`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'facebook', 'https://facebook.com/', NULL, 1, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(2, 'x', 'https://x.com/', NULL, 2, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(3, 'instagram', 'https://instagram.com/', NULL, 3, 1, '2026-02-19 20:12:11', '2026-02-19 20:12:11'),
(4, 'linkedin', 'https://linkedin.com/', NULL, 1, 1, '2026-02-19 20:12:11', '2026-02-20 19:36:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `last_activity`) VALUES
('n8sg735k768dcl72a81ej8q2jq', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-03 22:28:53'),
('7k9p0lke17a5atlg42eganise6', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-02 22:23:15'),
('0q5e56k1n87fv0nor9aq9aidc4', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 23:46:14'),
('cgn6voafpones4h7c2fld4u5ft', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 00:22:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subcategorias`
--

DROP TABLE IF EXISTS `subcategorias`;
CREATE TABLE IF NOT EXISTS `subcategorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoria_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `target` enum('_self','_blank') DEFAULT '_self',
  `description` text,
  `pagina_id` int DEFAULT NULL,
  `order_position` int DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_slug_categoria` (`slug`,`categoria_id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_ordem` (`order_position`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `subcategorias`
--

INSERT INTO `subcategorias` (`id`, `categoria_id`, `name`, `slug`, `url`, `target`, `description`, `pagina_id`, `order_position`, `active`, `created_at`, `updated_at`) VALUES
(2, 4, 'teste', 'teste', NULL, '_self', '', NULL, 0, 1, '2025-11-07 00:01:32', '2026-02-28 00:06:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','editor','viewer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'viewer',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Gustavo Lunkes', 'gustavolunkes1@gmail.com', '$2y$10$KwgOkKIFHitIqcxvfCdLQ.Q7ME6WChjdkpQZfPWi3.7Qq6wCI.mYC', 'admin', 1, '2025-10-29 02:12:28', '2025-10-29 02:12:28');

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `fk_categoria_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `rodape_links`
--
ALTER TABLE `rodape_links`
  ADD CONSTRAINT `rodape_links_ibfk_1` FOREIGN KEY (`coluna_id`) REFERENCES `rodape_colunas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD CONSTRAINT `subcategorias_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
