CREATE TABLE IF NOT EXISTS `{miprefixo}_sites` (
  `id` int(11) NOT NULL,
  `link` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  
CREATE TABLE IF NOT EXISTS `{miprefixo}_categorias` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `titulo` longtext NOT NULL,
  `palavraschave` longtext NULL,
  `descricaocurta` longtext NULL,
  `resumo` longtext DEFAULT NULL,
  `imagens` longtext DEFAULT NULL,
  `rascunho` int(11) NOT NULL DEFAULT 2,
  `link` longtext NOT NULL,
  `idcategoria` int(11) NOT NULL DEFAULT 0,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `datapublicado` varchar(19) NOT NULL,
  `dataalterado` varchar(19) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `{miprefixo}_files` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `nome` longtext NOT NULL,
  `link` longtext NOT NULL,
  `datapublicado` varchar(19) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `{miprefixo}_menutopo` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `nome` longtext NOT NULL,
  `link` longtext NOT NULL,
  `ativarnovajanela` int(11) NOT NULL DEFAULT 2,
  `desativarindexacao` int(11) NOT NULL DEFAULT 2,
  `idmenu` int(11) NOT NULL DEFAULT 0,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `classe` longtext DEFAULT NULL,
  `estilo` longtext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `{miprefixo}_options` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `titulo` longtext NOT NULL,
  `palavraschave` longtext DEFAULT NULL,
  `descricaocurta` longtext DEFAULT NULL,
  `imagens` longtext DEFAULT NULL,
  `estatisticas` longtext DEFAULT NULL,
  `outrasmetatags1` longtext DEFAULT NULL,
  `outrasmetatags2` longtext DEFAULT NULL,
  `configemail` longtext DEFAULT NULL,
  `datapublicado` varchar(19) NOT NULL,
  `dataalterado` varchar(19) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `{miprefixo}_pages` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `titulo` longtext NOT NULL,
  `palavraschave` longtext DEFAULT NULL,
  `descricaocurta` longtext DEFAULT NULL,
  `projeto` longtext DEFAULT NULL,
  `descricao` longtext DEFAULT NULL,
  `estilos` longtext DEFAULT NULL,
  `imagens` longtext DEFAULT NULL,
  `outrasmetatags1` longtext DEFAULT NULL,
  `outrasmetatags2` longtext DEFAULT NULL,
  `link` longtext NOT NULL,
  `rascunho` int(11) NOT NULL DEFAULT 2,
  `datapublicado` varchar(19) NOT NULL,
  `dataalterado` varchar(19) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `{miprefixo}_posts` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `idautor` int(11) NOT NULL,
  `idcategoria` int(11) NOT NULL,
  `titulo` longtext NOT NULL,
  `palavraschave` longtext DEFAULT NULL,
  `descricaocurta` longtext DEFAULT NULL,
  `resumo` longtext DEFAULT NULL,
  `projeto` longtext DEFAULT NULL,
  `descricao` longtext DEFAULT NULL,
  `estilos` longtext DEFAULT NULL,
  `imagens` longtext DEFAULT NULL,
  `outrasmetatags1` longtext DEFAULT NULL,
  `outrasmetatags2` longtext DEFAULT NULL,
  `link` longtext NOT NULL,
  `rascunho` int(11) NOT NULL DEFAULT 1,
  `datapublicado` varchar(19) NOT NULL,
  `dataalterado` varchar(19) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `{miprefixo}_rodape` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `projeto` longtext NOT NULL,
  `estilos` longtext NOT NULL,
  `descricao` longtext NOT NULL,
  `dataalterado` varchar(19) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `{miprefixo}_users` (
  `id` int(11) NOT NULL,
  `idsite` int(11) NOT NULL,
  `idtemp` varchar(45) DEFAULT NULL,
  `idtoken1` longtext DEFAULT NULL,
  `idtoken2` longtext DEFAULT NULL,
  `nome` longtext NOT NULL,
  `email` longtext NOT NULL,
  `usuario` longtext NOT NULL,
  `senha` longtext NOT NULL,
  `permissao` varchar(45) NOT NULL,
  `datapublicado` varchar(19) NOT NULL,
  `dataalterado` varchar(19) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `{miprefixo}_sites`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_categorias`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_files`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_menutopo`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_options`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_pages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_posts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_rodape`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{miprefixo}_sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `{miprefixo}_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{miprefixo}_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{miprefixo}_menutopo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{miprefixo}_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{miprefixo}_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{miprefixo}_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{miprefixo}_rodape`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `{miprefixo}_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;