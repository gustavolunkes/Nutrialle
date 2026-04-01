-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 01/04/2026 às 00:57
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
-- Banco de dados: `nutrialle`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `blog_categorias`
--

DROP TABLE IF EXISTS `blog_categorias`;
CREATE TABLE IF NOT EXISTS `blog_categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cor` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#0057b7',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `blog_categorias`
--

INSERT INTO `blog_categorias` (`id`, `nome`, `slug`, `cor`, `ativo`, `created_at`) VALUES
(1, 'Silagem', 'silagem', '#2e7d32', 1, '2026-03-25 21:13:48'),
(2, 'Probióticos', 'probioticos', '#0057b7', 1, '2026-03-25 21:13:48'),
(3, 'Manejo', 'manejo', '#e65100', 1, '2026-03-25 21:13:48'),
(4, 'Nutrição Animal', 'nutricao-animal', '#6a1b9a', 1, '2026-03-25 21:13:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `blog_config`
--

DROP TABLE IF EXISTS `blog_config`;
CREATE TABLE IF NOT EXISTS `blog_config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hero_titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Blog Nutrialle',
  `hero_subtitulo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT 'Artigos, dicas e novidades sobre nutrição animal, silagem e manejo de rebanhos.',
  `hero_cor_inicio` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#003d85',
  `hero_cor_meio` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#0057b7',
  `hero_cor_fim` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#1a7fe0',
  `page_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Blog',
  `meta_description` varchar(320) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `blog_config`
--

INSERT INTO `blog_config` (`id`, `hero_titulo`, `hero_subtitulo`, `hero_cor_inicio`, `hero_cor_meio`, `hero_cor_fim`, `page_title`, `meta_description`, `meta_keywords`) VALUES
(1, 'Blog Nutrialle', 'Artigos, dicas e novidades sobre nutrição animal, silagem e manejo de rebanhos.', '#ff7e29', '#e0b642', '#d5823f', 'Blog', 'Artigos, dicas e novidades sobre nutrição animal, silagem e manejo de rebanhos.', 'blog, nutrição animal, silagem, probióticos, bovinos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoria_id` int DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resumo` text COLLATE utf8mb4_unicode_ci,
  `conteudo` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem_destaque` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempo_leitura` int DEFAULT '3' COMMENT 'Em minutos',
  `meta_description` varchar(320) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `destaque` tinyint(1) NOT NULL DEFAULT '0',
  `visualizacoes` int NOT NULL DEFAULT '0',
  `autor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Equipe Nutrialle',
  `publicado_em` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_ativo_publicado` (`ativo`,`publicado_em`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_destaque` (`destaque`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `categoria_id`, `titulo`, `slug`, `resumo`, `conteudo`, `imagem_destaque`, `alt_imagem`, `tempo_leitura`, `meta_description`, `meta_keywords`, `ativo`, `destaque`, `visualizacoes`, `autor`, `publicado_em`, `updated_at`) VALUES
(1, 2, 'Diga não aos inoculantes de prateleira', 'diga-nao-aos-inoculantes-de-prateleira', 'Fragilidade que gera resultados: Cuidado com as bactérias liofilizadas, o armazenamento adequado é essencial.', '<p>A qualidade dos inoculantes utilizados na silagem impacta diretamente na fermentação e na qualidade nutricional da forragem conservada. Bactérias liofilizadas exigem cuidados especiais de armazenamento e transporte para manter sua viabilidade.</p><p>Neste artigo, exploramos por que os inoculantes de prateleira podem comprometer todo o seu investimento e como garantir que você está utilizando um produto de alta eficácia.</p>', NULL, NULL, 3, '', '', 1, 1, 11, 'Equipe Nutrialle', '2024-09-22 10:00:00', '2026-03-30 21:53:42'),
(2, 2, '5 motivos para usar LÉVUMILK e alcançar alto desempenho produtivo no rebanho leiteiro', '5-motivos-para-usar-levumilk', '5 motivos para usar LÉVUMILK e alcançar alto desempenho produtivo no rebanho leiteiro. Essa melhora pode ser atribuída principalmente ao produto.', '<p>O LÉVUMILK é um suplemento leveduriforme desenvolvido especialmente para bovinos leiteiros. Sua formulação exclusiva garante melhor aproveitamento dos nutrientes da dieta, maior produção de leite e redução de transtornos digestivos.</p><p>Confira os 5 principais motivos para incluir o LÉVUMILK na nutrição do seu rebanho:</p><ol><li>Melhora a fermentação ruminal</li><li>Aumenta a digestibilidade da fibra</li><li>Estabiliza o pH do rúmen</li><li>Reduz a incidência de acidose</li><li>Eleva a produção e qualidade do leite</li></ol>', NULL, NULL, 4, '', '', 1, 1, 28, 'Equipe Nutrialle', '2024-09-10 09:00:00', '2026-03-30 21:53:45'),
(3, 4, '3 erros que você não pode cometer na escolha do inoculante para sua silagem', '3-erros-escolha-inoculante-silagem', '3 erros que você não pode cometer na escolha do inoculante para sua silagem. A silagem, um alimento fundamental na dieta dos ruminantes.', '<p>A silagem é um dos alimentos mais importantes para bovinos em regime de confinamento ou semi-confinamento. A escolha do inoculante correto é decisiva para garantir uma boa fermentação e máximo valor nutricional.</p><p>Veja os 3 erros mais comuns e como evitá-los:</p><ul><li>Usar inoculante fora do prazo de validade</li><li>Não respeitar as condições de armazenamento</li><li>Escolher pelo preço e não pela eficácia comprovada</li></ul>', NULL, NULL, 4, '', '', 1, 0, 3, 'Equipe Nutrialle', '2024-08-20 08:00:00', '2026-03-26 21:47:32'),
(4, 3, 'teste', 'teste', 'lorem', '<ul>\r\n<li>teste</li>\r\n<li>teste2</li>\r\n<li>teste3</li>\r\n</ul>', NULL, NULL, 3, 'Equipe Nutrialle', 'Equipe Nutrialle', 1, 0, 7, 'Equipe Nutrialle', '2026-03-26 21:50:00', '2026-03-30 21:58:27');

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `menu_id`, `pagina_id`, `name`, `slug`, `url`, `target`, `description`, `order_position`, `active`, `created_at`, `updated_at`) VALUES
(9, 7, NULL, 'Bovinos de Leite', 'bovinos-de-leite', NULL, '_self', '', 1, 1, '2026-03-27 22:35:41', '2026-03-27 22:36:07'),
(10, 7, NULL, 'Bovinos de Corte', 'bovinos-de-corte', NULL, '_self', '', 2, 1, '2026-03-27 22:35:59', '2026-03-27 22:36:19'),
(11, 7, NULL, 'Suínos', 'suinos', NULL, '_self', '', 3, 1, '2026-03-27 22:36:41', '2026-03-27 22:36:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias_produtos`
--

DROP TABLE IF EXISTS `categorias_produtos`;
CREATE TABLE IF NOT EXISTS `categorias_produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `imagem` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ordem` int DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias_produtos`
--

INSERT INTO `categorias_produtos` (`id`, `nome`, `slug`, `descricao`, `imagem`, `meta_description`, `meta_keywords`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Boninos de Leite', 'boninos-de-leite', 'descrição da categoria', NULL, '', '', 2, 1, '2026-03-03 22:38:04', '2026-03-31 22:52:18'),
(5, 'Bovinos de corte', 'bovinos-de-corte', '', NULL, '', '', 0, 1, '2026-03-31 22:52:47', '2026-03-31 22:52:47'),
(6, 'Suínos', 'suinos', '', NULL, '', '', 0, 1, '2026-03-31 22:52:56', '2026-03-31 22:52:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_site`
--

DROP TABLE IF EXISTS `configuracoes_site`;
CREATE TABLE IF NOT EXISTS `configuracoes_site` (
  `id` int NOT NULL DEFAULT '1',
  `meta_descricao` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_autor` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `favicon_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `og_titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `og_descricao` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `og_imagem` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `google_analytics` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `robots_index` tinyint(1) NOT NULL DEFAULT '1',
  `robots_follow` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `configuracoes_site`
--

INSERT INTO `configuracoes_site` (`id`, `meta_descricao`, `meta_keywords`, `meta_autor`, `favicon_url`, `og_titulo`, `og_descricao`, `og_imagem`, `google_analytics`, `robots_index`, `robots_follow`, `created_at`, `updated_at`) VALUES
(1, 'Tecnologia em nutrição para bovinos e suínos: Saúde, Desempenho, Produtividade', '', '', '/base-site-institucional/favicon.ico', '', '', '', '', 1, 1, '2026-02-27 23:00:23', '2026-03-25 23:35:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contato_mensagens`
--

DROP TABLE IF EXISTS `contato_mensagens`;
CREATE TABLE IF NOT EXISTS `contato_mensagens` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assunto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_interesse` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mensagem` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_destino` varchar(254) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Destinatário configurado no layout',
  `ip_origem` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IPv4 ou IPv6',
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_criado_em` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mensagens enviadas pelo formulário de contato do site';

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
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `conteudos`
--

INSERT INTO `conteudos` (`id`, `pagina_id`, `layout_id`, `dados_json`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(19, 4, 11, '{\r\n    \"badge\":        \"Nossa História\",\r\n    \"titulo\":       \"Uma história construída com propósito\",\r\n    \"subtitulo\":    \"A Nutrialle Nutrição Animal nasceu da convergência entre experiência, propósito e coragem para empreender.\",\r\n    \"imagem_fundo\": \"\",\r\n    \"cor_overlay\":  \"#00071c\",\r\n    \"opacidade\":    \"0.65\"\r\n  }', 1, 1, '2026-03-31 22:33:42', '2026-03-31 22:33:42'),
(15, 3, 7, '{\"titulo\":\"Contato\",\"subtitulo\":\"Entre em contato conosco e responderemos assim que possível\",\"email_destino\":\"nutrialle@gmail.com\",\"mensagem_sucesso\":\"Mensagem enviada com sucesso\",\"mostrar_form\":\"\",\"imagem_fundo\":\"\",\"info_telefone\":\"Segunda a Sexta\",\"info_telefone_label\":\"4599999999\",\"info_email_label\":\"\",\"info_endereco\":\"Fabrica\",\"info_endereco_label\":\"Rua das flores\"}', 1, 1, '2026-03-29 23:23:27', '2026-03-30 23:51:05'),
(16, 3, 9, '{\"mapa_embed_url\":\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.133149999479!2d-53.74682372485259!3d-24.72230867801977!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94f3958c2645478d%3A0x15cc96558421c3ac!2sLago%20Municipal%20de%20Toledo!5e0!3m2!1spt-BR!2sbr!4v1774827547061!5m2!1spt-BR!2sbr\",\"mapa_altura\":\"450\"}', 2, 1, '2026-03-29 23:39:32', '2026-03-30 22:20:02'),
(20, 4, 12, '{\r\n    \"badge\":         \"A Jornada\",\r\n    \"titulo_secao\":  \"Como tudo começou\",\r\n    \"cor_destaque\":  \"#ff7200\",\r\n    \"marco1_rotulo\": \"A Conversa\",\r\n    \"marco1_texto\":  \"Durante uma viagem de trabalho, Itamar Borges Júnior e Alexandre Rafael Pech refletiam sobre o mercado e as oportunidades na nutrição animal. Foi nesse momento que Itamar revelou o projeto que vinha desenvolvendo há anos.\",\r\n    \"marco2_rotulo\": \"O Projeto\",\r\n    \"marco2_texto\":  \"A ideia já existia com consistência — tecnicamente estruturada e pronta para ganhar vida. Não era apenas uma intenção: era uma proposta madura com posicionamento bem definido e visão estratégica de longo prazo.\",\r\n    \"marco3_rotulo\": \"A Expertise\",\r\n    \"marco3_texto\":  \"Alexandre Rafael Pech trouxe ao projeto sua ampla vivência no setor, com forte atuação na suinocultura, olhar técnico apurado e profundo entendimento das necessidades reais da produção animal.\",\r\n    \"marco4_rotulo\": \"O Investidor\",\r\n    \"marco4_texto\":  \"Alexandro Dalla Costa reconheceu a robustez da proposta e a oportunidade de construir uma marca relevante para o agronegócio. Com visão empreendedora, a decisão foi clara: transformar planejamento em realização.\",\r\n    \"marco5_rotulo\": \"O Nome\",\r\n    \"marco5_texto\":  \"O nome inicialmente idealizado já estava registrado. Os sócios buscaram uma nova marca que representasse seus valores com autenticidade — e nasceu a Nutrialle, expressando o compromisso com a excelência nutricional.\",\r\n    \"marco6_rotulo\": \"Hoje\",\r\n    \"marco6_texto\":  \"A Nutrialle segue comprometida em entregar soluções que impulsionam produtividade, eficiência e rentabilidade, fortalecendo sua presença com seriedade, confiança e visão de futuro.\"\r\n  }', 2, 1, '2026-03-31 22:33:42', '2026-03-31 22:33:42'),
(21, 4, 13, '{\"badge\":\"Nossos Pilares\",\"titulo\":\"Bases sólidas, visão de futuro\",\"texto_intro\":\"Desde sua fundação, a Nutrialle constrói sua trajetória sustentada por valores essenciais que orientam cada decisão, cada produto e cada relacionamento com o campo.\",\"cor_destaque\":\"#ff7200\",\"fundo\":\"#1d2a36\",\"pilar1_titulo\":\"Conhecimento Técnico\",\"pilar1_descricao\":\"Especialização profunda em nutrição animal aplicada às reais demandas do campo.\",\"pilar2_titulo\":\"Proximidade com o Campo\",\"pilar2_descricao\":\"Entendimento genuíno da realidade do produtor e das exigências da produção animal.\",\"pilar3_titulo\":\"Responsabilidade\",\"pilar3_descricao\":\"Atuação séria, ética e comprometida com clientes, parceiros e resultados.\",\"pilar4_titulo\":\"Inovação\",\"pilar4_descricao\":\"Soluções modernas e eficientes alinhadas à evolução constante do agronegócio.\",\"pilar5_titulo\":\"Foco em Resultado\",\"pilar5_descricao\":\"Desempenho, produtividade e rentabilidade reais para o produtor rural.\",\"pilar6_titulo\":\"Confiança\",\"pilar6_descricao\":\"Relações duradouras baseadas em credibilidade, transparência e respeito.\",\"botao_texto\":\"Conheça nossos produtos\",\"botao_link\":\"http://localhost/site-institucional-Nutrialle/produtos/todos\"}', 3, 1, '2026-03-31 22:33:42', '2026-03-31 22:47:42'),
(23, 4, 14, '{\r\n    \"badge\":           \"Nossa Essência\",\r\n    \"titulo_secao\":    \"O que nos move\",\r\n    \"subtitulo_secao\": \"Valores, propósito e visão que orientam cada decisão da Nutrialle no campo.\",\r\n    \"cor_destaque\":    \"#ff7200\",\r\n    \"card1_titulo\":    \"Nossa Missão\",\r\n    \"card1_texto\":     \"Desenvolver e oferecer soluções nutricionais de alta performance para a produção animal, promovendo produtividade, eficiência e rentabilidade ao produtor rural, com excelência, inovação e compromisso com resultados reais no campo.\",\r\n    \"card2_titulo\":    \"Nossa Visão\",\r\n    \"card2_texto\":     \"Ser reconhecida como referência em nutrição animal, destacando-se pela qualidade de seus produtos, pela confiança de seus clientes e pela capacidade de gerar resultados sustentáveis e relevantes para o agronegócio.\",\r\n    \"card3_titulo\":    \"Nossos Valores\",\r\n    \"card3_texto\":     \"Excelência, inovação, responsabilidade, proximidade com o produtor, compromisso com resultados e confiança — pilares que sustentam nossa identidade e orientam cada passo da nossa trajetória.\"\r\n  }', 4, 1, '2026-03-31 22:56:38', '2026-03-31 22:56:38'),
(31, 5, 19, '{\"badge\":\"Imagens\",\"titulo\":\"produtos para Bovinos de Leite\",\"subtitulo\":\"Veja as imagens dos produtos\",\"imagens\":\"/site-institucional-Nutrialle/assets/uploads/conteudos/img_69cc66442e76c9.19610598.png, /site-institucional-Nutrialle/assets/uploads/conteudos/img_69cc66442e76c9.19610598.png, /site-institucional-Nutrialle/assets/uploads/conteudos/img_69cc66442e76c9.19610598.png, /site-institucional-Nutrialle/assets/uploads/conteudos/img_69cc66442e76c9.19610598.png\",\"colunas\":\"4\",\"fundo\":\"#ff7200\",\"cor_destaque\":\"#ff7200\",\"legenda1\":\"\",\"legenda2\":\"\",\"legenda3\":\"\",\"legenda4\":\"\",\"legenda5\":\"\",\"legenda6\":\"\",\"legenda7\":\"\",\"legenda8\":\"\",\"legenda9\":\"\",\"legenda10\":\"\",\"legenda11\":\"\",\"legenda12\":\"\",\"botao_texto\":\"\",\"botao_link\":\"\"}', 3, 1, '2026-04-01 00:00:07', '2026-04-01 00:27:09'),
(32, 5, 1, '{\"badge_texto\":\"Bovinos de Leite\",\"titulo\":\"Conheça os produtos da Categoria de bovinos de Leite\",\"subtitulo\":\"\",\"imagens\":\"/site-institucional-Nutrialle/assets/uploads/conteudos/img_69cc61caa9a963.74770976.jpg\",\"imagem_fundo\":\"\",\"botao_texto\":\"Ver produtos\",\"botao_link\":\"http://localhost/site-institucional-Nutrialle/produtos/bovinos-de-corte\",\"botao_cor\":\"#ff7200\",\"botao_cor_text\":\"#fff\"}', 1, 1, '2026-04-01 00:08:25', '2026-04-01 00:30:01'),
(28, 5, 16, '{\"badge\":\"Nutrição para Leite\",\"titulo\":\"Tecnologia aplicada à produtividade\",\"texto\":\"A nutrição de bovinos de leite exige precisão e conhecimento técnico. Nossas soluções são desenvolvidas para atender todas as fases do ciclo produtivo, desde o crescimento até a lactação, garantindo melhor desempenho, saúde do rebanho e qualidade superior do leite. Trabalhamos com formulações equilibradas que aumentam a eficiência alimentar e reduzem perdas no sistema produtivo.\",\"imagem\":\"/site-institucional-Nutrialle/assets/uploads/paginas/img_69cc6311bddce7.63223779.png\",\"posicao_imagem\":\"direita\",\"fundo\":\"#ffffff\",\"cor_destaque\":\"#ff7200\",\"botao_texto\":\"Fale com um especialista\",\"botao_link\":\"http://localhost/site-institucional-Nutrialle/contato\"}', 2, 1, '2026-03-31 23:26:00', '2026-04-01 00:30:57'),
(29, 5, 17, '{\"badge\":\"Diferenciais\",\"titulo\":\"Por que investir em nutrição de qualidade?\",\"subtitulo\":\"Soluções completas para maximizar o desempenho produtivo e econômico da atividade leiteira.\",\"imagem_fundo\":\"\",\"fundo\":\"#1d2a36\",\"cor_destaque\":\"#ff7200\",\"benef1_titulo\":\"Maior Produção de Leite\",\"benef1_texto\":\"Aumento significativo da produção com dietas balanceadas e eficientes.\",\"benef2_titulo\":\"Qualidade Superior\",\"benef2_texto\":\"Melhoria nos sólidos do leite, agregando mais valor ao produto final.\",\"benef3_titulo\":\"Saúde do Rebanho\",\"benef3_texto\":\"Redução de doenças metabólicas e melhora no bem-estar animal.\",\"benef4_titulo\":\"Eficiência Alimentar\",\"benef4_texto\":\"Melhor aproveitamento dos nutrientes, reduzindo custos operacionais.\",\"benef5_titulo\":\"Maior Rentabilidade\",\"benef5_texto\":\"Resultados econômicos mais consistentes e previsíveis para o produtor.\",\"benef6_titulo\":\"Sustentabilidade\",\"benef6_texto\":\"Produção eficiente com menor impacto ambiental e melhor uso de recursos.\",\"botao_texto\":\"Conhecer linha de produtos\",\"botao_link\":\"http://localhost/site-institucional-Nutrialle/produtos/bovinos-de-corte\"}', 4, 1, '2026-03-31 23:26:00', '2026-04-01 00:32:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `css_personalizado`
--

DROP TABLE IF EXISTS `css_personalizado`;
CREATE TABLE IF NOT EXISTS `css_personalizado` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `css` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `css_personalizado`
--

INSERT INTO `css_personalizado` (`id`, `css`, `ativo`, `updated_at`) VALUES
(1, '', 1, '2026-03-05 00:01:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `header_config`
--

DROP TABLE IF EXISTS `header_config`;
CREATE TABLE IF NOT EXISTS `header_config` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_empresa` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Minha Empresa',
  `logo_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cor_fundo` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#00071c',
  `cor_texto` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `cor_borda` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e2a3a',
  `cor_nav_link` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `mob_cor_fundo` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#111827',
  `mob_cor_texto` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `mob_cor_borda` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e2a3a',
  `mob_cor_nav_link` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `mob_cor_menu_aberto` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1a2235',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `header_config`
--

INSERT INTO `header_config` (`id`, `nome_empresa`, `logo_url`, `cor_fundo`, `cor_texto`, `cor_borda`, `cor_nav_link`, `mob_cor_fundo`, `mob_cor_texto`, `mob_cor_borda`, `mob_cor_nav_link`, `mob_cor_menu_aberto`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Nutrialle', NULL, '#ffffff', '#1c292c', '#ffffff', '#1c292c', '#ffffff', '#1c292c', '#ffffff', '#1c292c', '#ffffff', 1, '2026-02-23 20:36:47', '2026-03-26 21:52:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `home_conteudos`
--

DROP TABLE IF EXISTS `home_conteudos`;
CREATE TABLE IF NOT EXISTS `home_conteudos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `layout_id` int UNSIGNED NOT NULL,
  `dados_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` smallint NOT NULL DEFAULT '0',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ativo_ordem` (`ativo`,`ordem`),
  KEY `idx_layout_id` (`layout_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `home_conteudos`
--

INSERT INTO `home_conteudos` (`id`, `layout_id`, `dados_json`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(4, 1, '{\"badge_texto\":\"Nutrição Animal\",\"titulo\":\"Tecnologia em nutrição para bovinos e suínos\",\"subtitulo\":\"Entre em contato conosco\",\"imagens\":\"https://www.austernutri.com.br/wp-content/uploads/2024/11/suinos.jpg, https://www.austernutri.com.br/wp-content/uploads/2024/11/bovinos.jpg\",\"imagem_fundo\":\"/site-institucional-Nutrialle/assets/uploads/paginas/img_69cc685786a381.55761705.png\",\"botao_texto\":\"Saiba mais \",\"botao_link\":\"#4\",\"botao_cor\":\"#fff\",\"botao_cor_text\":\"#000\"}', 1, 1, '2026-03-27 20:20:24', '2026-03-31 21:35:39'),
(7, 3, '{\"badge\":\"Sobre Nós\",\"titulo\":\"Subtitulo Lorem ipsum dolor\",\"texto\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\",\"imagem\":\"/site-institucional-Nutrialle/assets/uploads/paginas/img_69cafe68cb8c64.94632939.png\",\"imagem2\":\"\",\"posicao_imagem\":\"direita\"}', 2, 1, '2026-03-30 19:25:21', '2026-03-30 21:38:33'),
(11, 7, '{\"titulo\":\"Contato\",\"subtitulo\":\"Entre em contato conosco e responderemos assim que possível\",\"email_destino\":\"nutrialle@gmail.com\",\"mensagem_sucesso\":\"mensagem enviada com sucesso\",\"mostrar_form\":\"\",\"imagem_fundo\":\"\",\"info_telefone\":\"4599999999\",\"info_telefone_label\":\"Segunda a Sexta\",\"info_email_label\":\"\",\"info_endereco\":\"Rua das flores\",\"info_endereco_label\":\"Nossa fabrica\"}', 4, 1, '2026-03-30 20:28:17', '2026-03-30 21:28:21'),
(13, 9, '{\"mapa_embed_url\":\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.133149999479!2d-53.74682372485259!3d-24.72230867801977!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94f3958c2645478d%3A0x15cc96558421c3ac!2sLago%20Municipal%20de%20Toledo!5e0!3m2!1spt-BR!br!2s4v1774827547061!5m2!1spt-BR!2sbr\",\"mapa_altura\":\"450\"}', 5, 1, '2026-03-30 20:56:42', '2026-03-30 21:28:21'),
(15, 10, '{\"titulo\":\"titulo\",\"subtitulo\":\"Subtitulo Lorem ipsum dolor\",\"fundo\":\"\",\"cor_destaque\":\"#ff7200\",\"card1_titulo\":\"titulo1\",\"card1_descricao\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \",\"card1_icone\":\"\",\"card2_titulo\":\"titulo2 \",\"card2_descricao\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \",\"card2_icone\":\"\",\"card3_titulo\":\"titulo3\",\"card3_descricao\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. \",\"card3_icone\":\"\"}', 3, 1, '2026-03-30 21:16:34', '2026-03-31 20:03:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `icones_flutuantes`
--

DROP TABLE IF EXISTS `icones_flutuantes`;
CREATE TABLE IF NOT EXISTS `icones_flutuantes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo` enum('whatsapp','topo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo fixo do ícone',
  `ativo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = exibe no site',
  `numero` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Somente WhatsApp: número com DDI ex: 5511999999999',
  `mensagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Somente WhatsApp: mensagem pré-preenchida (opcional)',
  `cor_fundo` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#25d366',
  `cor_icone` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `posicao` enum('bottom-right','bottom-left') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bottom-right',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `arquivo` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `campos_json` text COLLATE utf8mb4_general_ci,
  `ativo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `layouts`
--

INSERT INTO `layouts` (`id`, `nome`, `descricao`, `arquivo`, `thumbnail`, `campos_json`, `ativo`) VALUES
(1, 'Hero Banner', 'Seção principal com imagem de fundo, badge, título, subtítulo e botão CTA', 'layout_hero.php', NULL, '{\r\n  \"badge_texto\":\"text\",\r\n  \"titulo\":\"text\",\r\n  \"subtitulo\":\"text\",\r\n  \"imagens\":\"textarea\",\r\n  \"imagem_fundo\":\"text\",\r\n  \"botao_texto\":\"text\",\r\n  \"botao_link\":\"text\",\r\n  \"botao_cor\":\"text\",\r\n  \"botao_cor_text\":\"text\"\r\n}', 1),
(11, 'Cabeçalho — Nossa História', 'Seção de abertura com badge, título e subtítulo sobre fundo escuro ou imagem', 'layout_historia_hero.php', NULL, '{\r\n    \"badge\":        \"text\",\r\n    \"titulo\":       \"text\",\r\n    \"subtitulo\":    \"textarea\",\r\n    \"imagem_fundo\": \"text\",\r\n    \"cor_overlay\":  \"text\",\r\n    \"opacidade\":    \"text\"\r\n  }', 1),
(3, 'Texto + Imagem', 'Bloco Sobre Nós com texto, badge e até duas imagens sobrepostas', 'layout_texto_imagem.php', NULL, '{\"badge\":\"text\",\"titulo\":\"text\",\"texto\":\"textarea\",\"imagem\":\"text\",\"imagem2\":\"text\",\"posicao_imagem\":\"text\"}', 1),
(10, 'Cards Informativos (3)', 'Três cards com título, descrição e ícone via upload — ideal para Missão, Visão, Valores ou diferenciais', 'layout_cards.php', NULL, '{\r\n  \"titulo\":          \"text\",\r\n  \"subtitulo\":       \"text\",\r\n  \"fundo\":           \"text\",\r\n  \"cor_destaque\":    \"text\",\r\n  \"card1_titulo\":    \"text\",\r\n  \"card1_descricao\": \"textarea\",\r\n  \"card1_icone\":     \"image\",\r\n  \"card2_titulo\":    \"text\",\r\n  \"card2_descricao\": \"textarea\",\r\n  \"card2_icone\":     \"image\",\r\n  \"card3_titulo\":    \"text\",\r\n  \"card3_descricao\": \"textarea\",\r\n  \"card3_icone\":     \"image\"\r\n}', 1),
(7, 'Formulário de Contato', 'Seção de contato com informações laterais e formulário completo', 'layout_contato.php', NULL, '{\r\n        \"titulo\":                                  \"text\",\r\n        \"subtitulo\":                               \"text\",\r\n        \"email_destino\":                           \"text\",\r\n        \"mensagem_sucesso\":                        \"text\",\r\n        \"mostrar_form\":                            \"text\",\r\n        \"imagem_fundo\":                            \"text\",\r\n        \"info_telefone\":                           \"text\",\r\n        \"info_telefone_label\":                     \"text\",\r\n        \"info_email_label\":                        \"text\",\r\n        \"info_endereco\":                           \"textarea\",\r\n        \"info_endereco_label\":                     \"text\"\r\n    }', 1),
(8, 'Vídeo', 'Player responsivo para YouTube, Vimeo ou arquivo — suporta thumbnail com play', 'layout_video.php', NULL, '{\"badge\":\"text\",\"titulo\":\"text\",\"url_video\":\"text\",\"thumbnail\":\"text\",\"descricao\":\"textarea\"}', 1),
(9, 'Localização', 'Mapa embed responsivo com altura configurável', 'layout_localizacao.php', NULL, '{\"mapa_embed_url\":\"text\",\"mapa_altura\":\"text\"}', 1),
(12, 'Linha do Tempo — Nossa História', 'Marcos cronológicos em timeline vertical alternada com cards', 'layout_historia_timeline.php', NULL, '{\r\n    \"badge\":           \"text\",\r\n    \"titulo_secao\":    \"text\",\r\n    \"cor_destaque\":    \"text\",\r\n    \"marco1_rotulo\":   \"text\",\r\n    \"marco1_texto\":    \"textarea\",\r\n    \"marco2_rotulo\":   \"text\",\r\n    \"marco2_texto\":    \"textarea\",\r\n    \"marco3_rotulo\":   \"text\",\r\n    \"marco3_texto\":    \"textarea\",\r\n    \"marco4_rotulo\":   \"text\",\r\n    \"marco4_texto\":    \"textarea\",\r\n    \"marco5_rotulo\":   \"text\",\r\n    \"marco5_texto\":    \"textarea\",\r\n    \"marco6_rotulo\":   \"text\",\r\n    \"marco6_texto\":    \"textarea\"\r\n  }', 1),
(13, 'Pilares & Encerramento — Nossa História', 'Texto de fechamento com grid de pilares institucionais e CTA opcional', 'layout_historia_encerramento.php', NULL, '{\r\n    \"badge\":             \"text\",\r\n    \"titulo\":            \"text\",\r\n    \"texto_intro\":       \"textarea\",\r\n    \"cor_destaque\":      \"text\",\r\n    \"fundo\":             \"text\",\r\n    \"pilar1_titulo\":     \"text\",\r\n    \"pilar1_descricao\":  \"textarea\",\r\n    \"pilar2_titulo\":     \"text\",\r\n    \"pilar2_descricao\":  \"textarea\",\r\n    \"pilar3_titulo\":     \"text\",\r\n    \"pilar3_descricao\":  \"textarea\",\r\n    \"pilar4_titulo\":     \"text\",\r\n    \"pilar4_descricao\":  \"textarea\",\r\n    \"pilar5_titulo\":     \"text\",\r\n    \"pilar5_descricao\":  \"textarea\",\r\n    \"pilar6_titulo\":     \"text\",\r\n    \"pilar6_descricao\":  \"textarea\",\r\n    \"botao_texto\":       \"text\",\r\n    \"botao_link\":        \"text\"\r\n  }', 1),
(14, 'Missão, Visão & Valores', 'Três cards institucionais em grid sobre fundo branco — ideal para Missão, Visão e Valores', 'layout_missao_visao_valores.php', NULL, '{\r\n    \"badge\":            \"text\",\r\n    \"titulo_secao\":     \"text\",\r\n    \"subtitulo_secao\":  \"textarea\",\r\n    \"cor_destaque\":     \"text\",\r\n    \"card1_titulo\":     \"text\",\r\n    \"card1_texto\":      \"textarea\",\r\n    \"card2_titulo\":     \"text\",\r\n    \"card2_texto\":      \"textarea\",\r\n    \"card3_titulo\":     \"text\",\r\n    \"card3_texto\":      \"textarea\"\r\n  }', 1),
(16, 'Texto + Imagem — Categoria de Produto', 'Bloco institucional com texto descritivo, imagem lateral (direita ou esquerda) e botão CTA', 'layout_cat_texto_imagem.php', NULL, '{\r\n    \"badge\":           \"text\",\r\n    \"titulo\":          \"text\",\r\n    \"texto\":           \"textarea\",\r\n    \"imagem\":          \"image\",\r\n    \"posicao_imagem\":  \"text\",\r\n    \"fundo\":           \"text\",\r\n    \"cor_destaque\":    \"text\",\r\n    \"botao_texto\":     \"text\",\r\n    \"botao_link\":      \"text\"\r\n  }', 1),
(17, 'Benefícios — Categoria de Produto', 'Grid de benefícios e diferenciais da categoria com ícones SVG automáticos e botão CTA — até 6 itens', 'layout_cat_beneficios.php', NULL, '{\r\n    \"badge\":         \"text\",\r\n    \"titulo\":        \"text\",\r\n    \"subtitulo\":     \"textarea\",\r\n    \"imagem_fundo\":  \"image\",\r\n    \"fundo\":         \"text\",\r\n    \"cor_destaque\":  \"text\",\r\n    \"benef1_titulo\": \"text\",\r\n    \"benef1_texto\":  \"textarea\",\r\n    \"benef2_titulo\": \"text\",\r\n    \"benef2_texto\":  \"textarea\",\r\n    \"benef3_titulo\": \"text\",\r\n    \"benef3_texto\":  \"textarea\",\r\n    \"benef4_titulo\": \"text\",\r\n    \"benef4_texto\":  \"textarea\",\r\n    \"benef5_titulo\": \"text\",\r\n    \"benef5_texto\":  \"textarea\",\r\n    \"benef6_titulo\": \"text\",\r\n    \"benef6_texto\":  \"textarea\",\r\n    \"botao_texto\":   \"text\",\r\n    \"botao_link\":    \"text\"\r\n  }', 1),
(19, 'Galeria em Grid — Categoria de Produto', 'Grid de imagens clicáveis com 2, 3 ou 4 colunas e lightbox fullscreen com navegação por teclado, swipe e botão CTA opcional — até 12 imagens com legendas', 'layout_cat_galeria.php', NULL, '{\r\n    \"badge\":        \"text\",\r\n    \"titulo\":       \"text\",\r\n    \"subtitulo\":    \"textarea\",\r\n    \"imagens\":      \"textarea\",\r\n    \"colunas\":      \"text\",\r\n    \"fundo\":        \"text\",\r\n    \"cor_destaque\": \"text\",\r\n    \"legenda1\":     \"text\",\r\n    \"legenda2\":     \"text\",\r\n    \"legenda3\":     \"text\",\r\n    \"legenda4\":     \"text\",\r\n    \"legenda5\":     \"text\",\r\n    \"legenda6\":     \"text\",\r\n    \"legenda7\":     \"text\",\r\n    \"legenda8\":     \"text\",\r\n    \"legenda9\":     \"text\",\r\n    \"legenda10\":    \"text\",\r\n    \"legenda11\":    \"text\",\r\n    \"legenda12\":    \"text\",\r\n    \"botao_texto\":  \"text\",\r\n    \"botao_link\":   \"text\"\r\n  }', 1);

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
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `ip_address`, `attempted_at`) VALUES
(25, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:29'),
(24, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 15:22:47'),
(23, 'wirestack@gmail.com', '::1', '2026-03-26 23:51:33'),
(26, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:31'),
(27, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:32'),
(28, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:33'),
(29, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:35'),
(30, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:36'),
(31, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:37'),
(32, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:38'),
(33, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:40'),
(34, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:42'),
(35, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:43'),
(36, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:45'),
(37, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:46'),
(38, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:47'),
(39, 'gustavolunkes1@gmail.com', '::1', '2026-03-30 22:11:48');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `menus`
--

INSERT INTO `menus` (`id`, `name`, `slug`, `url`, `pagina_id`, `target`, `order_position`, `active`, `created_at`, `updated_at`) VALUES
(4, 'Blog', 'blog', '', NULL, '_self', 4, 1, '2026-03-27 22:28:24', '2026-03-27 22:32:31'),
(5, 'Contato', 'contato', '', NULL, '_self', 5, 1, '2026-03-27 22:28:46', '2026-03-27 22:33:00'),
(6, 'Sobre Nós', 'sobre-nos', '', NULL, '_self', 2, 1, '2026-03-27 22:32:20', '2026-03-31 22:23:27'),
(7, 'Nutrição Animal', 'nutricao-animal', '', NULL, '_self', 3, 1, '2026-03-27 22:33:53', '2026-03-27 22:33:53'),
(9, 'Início', 'inicio', 'http://localhost/site-institucional-Nutrialle/', NULL, '_self', 1, 1, '2026-04-01 00:38:44', '2026-04-01 00:39:07');

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `paginas`
--

INSERT INTO `paginas` (`id`, `titulo`, `slug`, `meta_description`, `meta_keywords`, `ativo`, `created_at`, `updated_at`) VALUES
(3, 'Contato', 'contato', '', '', 1, '2026-03-27 23:56:58', '2026-03-29 23:28:08'),
(4, 'Sobre Nós', 'sobre-nos', '', '', 1, '2026-03-31 22:23:55', '2026-03-31 22:23:55'),
(5, 'Bovinos de Leite', 'nutricao-animal/bovinos-de-leite', '', '', 1, '2026-03-31 23:15:05', '2026-03-31 23:16:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao_curta` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `preco` decimal(10,2) DEFAULT NULL,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `imagem_principal` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `galeria_json` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `destaque` tinyint(1) DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
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
(2, 'PRD-E2E66C', 'teste2', 'aa', 'bb', 70.00, NULL, NULL, NULL, 1, 1, '', '', '2026-03-03 23:06:06', '2026-03-03 23:06:06');

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produto_categorias`
--

INSERT INTO `produto_categorias` (`id`, `produto_id`, `categoria_produto_id`) VALUES
(11, 1, 5),
(12, 2, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_colunas`
--

DROP TABLE IF EXISTS `rodape_colunas`;
CREATE TABLE IF NOT EXISTS `rodape_colunas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(1, 'Mapa do Site', 1, 1, '2026-02-19 20:12:10', '2026-03-25 20:21:15'),
(2, 'Links Úteis', 2, 1, '2026-02-19 20:12:10', '2026-02-19 20:12:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_config`
--

DROP TABLE IF EXISTS `rodape_config`;
CREATE TABLE IF NOT EXISTS `rodape_config` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_empresa` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copyright_texto` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `rodape_config`
--

INSERT INTO `rodape_config` (`id`, `nome_empresa`, `descricao`, `logo_url`, `copyright_texto`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Nutrialle', 'Tecnologia em nutrição para bovinos e suínos: Saúde, Desempenho, Produtividade', NULL, '© 2026 Nutrialle. Todos os direitos reservados.', 1, '2026-02-19 20:12:10', '2026-03-25 20:10:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_cores`
--

DROP TABLE IF EXISTS `rodape_cores`;
CREATE TABLE IF NOT EXISTS `rodape_cores` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome_tema` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Padrão',
  `cor_fundo` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0a0f1e',
  `cor_texto` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `cor_texto_suave` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#8892a4',
  `cor_link` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `cor_link_hover` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#e91e8c',
  `cor_divisor` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#e91e8c',
  `cor_linha` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1e2a3a',
  `ativo` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `rodape_cores`
--

INSERT INTO `rodape_cores` (`id`, `nome_tema`, `cor_fundo`, `cor_texto`, `cor_texto_suave`, `cor_link`, `cor_link_hover`, `cor_divisor`, `cor_linha`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Nutrialle - padrão', '#1d2a36', '#ffffff', '#9fadc6', '#ffffff', '#ff7200', '#ff7200', '#425b7b', 1, '2026-02-20 19:21:28', '2026-03-25 20:19:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `rodape_links`
--

DROP TABLE IF EXISTS `rodape_links`;
CREATE TABLE IF NOT EXISTS `rodape_links` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `coluna_id` int UNSIGNED NOT NULL,
  `label` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` enum('_self','_blank') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
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
  `rede` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icone_svg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
('a66g2jtshrtap4tgpitqo608ra', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-26 23:51:35'),
('aesh7f1pl2bb4ojei8povnhe5b', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 23:51:44'),
('4jfjj659spbdn235a6cllh1jdb', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 22:26:40'),
('stllk8aada57eshnrvnhgnhjkv', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 23:51:12'),
('jqhq4nkqs8ujr0u3hvek5jo26b', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 00:37:19'),
('mrpvupr8usijruqthcijk45rcc', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 14:46:17'),
('17i0v1qrirle74h88sl62asicd', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 22:54:29'),
('sopqs72tphm0nc4iijv1p85dgr', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 15:23:09'),
('fjc4blb5fdolubc1tnfk8h87mf', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 22:11:53'),
('rd590p96i698bh3bp3ddnp5cu5', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-31 22:22:46');

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
  `role` enum('admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'admin',
  `user_master` enum('S','N') COLLATE utf8mb4_general_ci DEFAULT 'N',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `user_master`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Wire Stack', 'wirestack@gmail.com', '$2y$12$umq.7t8k6tfwaf8jsCq51OnZb7acNUSQOeLlDMPcDAZtxz43aSa4u', 'admin', 'S', 1, '2025-10-29 02:12:28', '2026-03-26 23:51:21');

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `fk_blog_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `blog_categorias` (`id`) ON DELETE SET NULL;

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
