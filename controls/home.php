<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
}

$db1 = new select($dbBlogs1);
$db1->table('pages')
    ->where('idsite', $idsite)
    ->where('link', 'home')
    ->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);
    if ($tpl->exists('titulo')) $tpl->titulo =  $db1->row('titulo') . ' | ' .  $tpl->titulo;
    if (!empty($db1->row('estilos'))) {
        if ($tpl->exists('estilos')) $tpl->estilos = stripslashes($db1->row('estilos'));
        $tpl->block('BLOCK_HEADESTILOS');
    }

    if ($tpl->exists('homeDescricao')) {
        $pagina_descricao = stripslashes($db1->row('descricao'));
        $tpl->homeDescricao = $pagina_descricao;
    }
}

$db1->close();

if ($tpl->exists('homeDescricao')) {
    if (function_exists('miplugins')) {
        preg_match_all("/{(.*?)\}/", $tpl->homeDescricao, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $txtmatch = trim($match[1]);
            $tpl->homeDescricao = str_replace("{" . $txtmatch . "}", miplugins($txtmatch), $tpl->homeDescricao);
        }
    }
}
