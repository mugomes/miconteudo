<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

$db1 = new select($dbBlogs1);
$db1->table('posts')
    ->where('idsite', $idsite)
    ->where('rascunho', 2)
    ->limit(0,9)
    ->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    $txtImagemPrincipal = unserialize($db1->row('imagens'))['principal'];
    if (empty($txtImagemPrincipal)) {
        if ($tpl->exists('blogPostImagem')) $tpl->blogPostImagem = '/themes/images/noimage.webp';
    } else {
        if ($tpl->exists('blogPostImagem')) $tpl->blogPostImagem = $txtImagemPrincipal;
    }
    if ($tpl->exists('blogPostTitulo')) $tpl->blogPostTitulo = $db1->row('titulo');
    if ($tpl->exists('blogPostLink')) $tpl->blogPostLink = '/' . existCategoriesSequences($row['idcategoria']) . '/' . $db1->row('link');
    if ($tpl->exists('blogPostResumo')) $tpl->blogPostResumo = stripslashes($db1->row('resumo'));

    $tpl->block('BLOCK_BLOGPOSTS');
}

$db1->close();