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
    ->where('link')->prepared($rt->getUltimaURL())
    ->select();

$db1->getResult();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    if ($tpl->exists('titulo')) $tpl->titulo =  $db1->row('titulo') . ' | ' .  $tpl->titulo;

    if (!empty($db1->row('descricaocurta'))) {
        if ($tpl->exists('descricaocurta')) $tpl->descricaocurta = $db1->row('descricaocurta');
    }

    if (!empty($db1->row('palavraschave'))) {
        if ($tpl->exists('palavraschave')) $tpl->palavraschave = $db1->row('palavraschave');
    }

    $txtImagemPrincipal = unserialize($db1->row('imagens'));
    if (!empty($txtImagemPrincipal['principal'])) {
        if ($tpl->exists('imagemprincipal')) $tpl->imagemprincipal = $txtImagemPrincipal['principal'];
    }

    if (!empty($db1->row('estilos'))) {
        if ($tpl->exists('estilos')) $tpl->estilos = stripslashes($db1->row('estilos'));
        $tpl->block('BLOCK_HEADESTILOS');
    }

    if ($tpl->exists('paginaTitulo')) $tpl->paginaTitulo = $db1->row('titulo');

    if ($tpl->exists('paginaDescricao')) {
        if ($tpl->exists('paginaDescricao')) {
            $pagina_descricao = stripslashes($db1->row('descricao'));
   
            if (strpos($db1->row('descricao'), '<button') !== false) {
                $tpl->paginaDescricao = str_replace('<button', '<input id="txtToken" name="txtToken" type="hidden" value="' . $_SESSION['tokenform'] . '"><input id="txtEnviar" name="txtEnviar" type="hidden" value=""><button', $pagina_descricao);
            } else {
                $tpl->paginaDescricao = stripslashes($pagina_descricao);
            }
        }
    }

    if ($tpl->exists('outrasmetatags1')) $tpl->outrasmetatags1 .= $db1->row('outrasmetatags1');
    if ($tpl->exists('outrasmetatags2')) $tpl->outrasmetatags2 .= $db1->row('outrasmetatags2');
}

$db1->close();

if ($tpl->exists('paginaDescricao')) {
    if (function_exists('miplugins')) {
        preg_match_all("/{(.*?)\}/", $tpl->paginaDescricao, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $txtmatch = trim($match[1]);
            $tpl->paginaDescricao = str_replace("{" . $txtmatch . "}", miplugins($txtmatch), $tpl->paginaDescricao);
        }
    }
}