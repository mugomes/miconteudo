<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

function getAutor(int $id): string
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('users')
        ->where('idsite', $idsite)
        ->where('id', $id)
        ->select();

    while ($row = $db1->fetch()) {
        $db1->rows($row);

        $txt = $db1->row('nome');
    }

    $db1->close();

    return isset($txt) ? $txt : '';
}

$db1 = new select($dbBlogs1);
$db1->table('posts')
    ->where('idsite', $idsite)
    ->where('link')->prepared($rt->getUltimaURL())
    ->where('rascunho', 2)

    ->select();

$db1->getResult();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    if ($tpl->exists('postAutor')) $tpl->postAutor = getAutor($db1->row('idautor'));
    if ($tpl->exists('postTitulo')) $tpl->postTitulo = $db1->row('titulo');
    if ($tpl->exists('palavraschave')) $tpl->palavraschave = $db1->row('palavraschave');
    if ($tpl->exists('descricaocurta')) $tpl->descricaocurta = $db1->row('descricaocurta');
    if ($tpl->exists('postDescricao')) $tpl->postDescricao = str_replace('<button', '<input id="txtToken" name="txtToken" type="hidden" value="' . $_SESSION['tokenform'] . '"><input id="txtEnviar" name="txtEnviar" type="hidden" value=""><button', stripslashes($db1->row('descricao')));
    if ($tpl->exists('estilos')) $tpl->estilos = stripslashes($db1->row('estilos'));
    $txtImagemPrincipal = unserialize($db1->row('imagens'));
    if (!empty($txtImagemPrincipal['principal'])) {
        if ($tpl->exists('imagemprincipal')) $tpl->imagemprincipal = $txtImagemPrincipal['principal'];
    }
    if ($tpl->exists('outrasmetatags1')) $tpl->outrasmetatags1 = stripslashes($db1->row('outrasmetatags1'));
    if ($tpl->exists('outrasmetatags2')) $tpl->outrasmetatags2 = stripslashes($db1->row('outrasmetatags2'));
    if ($tpl->exists('postPublicado')) $tpl->postPublicado = changedate($db1->row('datapublicado'), 'Y-m-d H:i:s', 'd/m/Y H:i:s');

    if (!empty($db1->row('dataalterado'))) {
        if ($tpl->exists('postAlterado')) $tpl->postAlterado = changedate($db1->row('dataalterado'), 'Y-m-d H:i:s', 'd/m/Y H:i:s');
        $tpl->block('POST_ALTERADO');
    }
}

$db1->close();

// Breadcrumb
function catBreadcrumb($parent): array
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('categorias')
        ->where('id', $parent)
        ->where('idsite', $idsite)
        ->select();

    $txt = [];
    while ($row = $db1->fetch()) {
        $db1->rows($row);

        $txt['titulo'] = $row['titulo'];
        $txt['link'] = $row['link'];

        $categoria = catBreadcrumb($db1->row('idcategoria'));

        if (!empty($categoria)) {
            $txt['titulo'] = $categoria['titulo'] . '/' . $row['titulo'];
            $txt['link'] = $categoria['link'] . '/' . $row['link'];
        }
    }

    $db1->close();

    return $txt;
}

$breadcrumb = catBreadcrumb(getIDCategory($rt->getPenultimaURL()));
$breadcrumbTitulo = explode('/', $breadcrumb['titulo']);
$breadcrumbLink = explode('/', $breadcrumb['link']);
$breadcrumbCount = count($breadcrumbTitulo);
$breadcrumbShowLink = '';
$breadcrumbActive = '';

for ($i = 0; $i < $breadcrumbCount; $i++) {

    $breadcrumbShowLink .= $breadcrumbLink[$i] . '/';
    if ($tpl->exists('breadcrumbActiveLink')) $tpl->breadcrumbActiveLink = $breadcrumbShowLink;
    if ($tpl->exists('breadcrumbActiveTitulo')) $tpl->breadcrumbActiveTitulo = $breadcrumbTitulo[$i];
    $tpl->block('BLOCK_BREADCRUMB');
}

if ($tpl->exists('postDescricao')) {
    if (function_exists('miplugins')) {
        preg_match_all("/{(.*?)\}/", $tpl->postDescricao, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $txtmatch = trim($match[1]);
            $tpl->postDescricao = str_replace("{" . $txtmatch . "}", miplugins($txtmatch), $tpl->postDescricao);
        }
    }
}  