<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

$bDescricaoCurta = false;
$bPalavrasChave = false;
$sTipoSite = 1;
$db1 = new select($dbBlogs1);
$db1->table('options')
    ->where('idsite', $idsite)
    ->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);
    $sTipoSite = $db1->row('tipo');

    if ($tpl->exists('titulo')) $tpl->titulo = $db1->row('titulo');
    if ($tpl->exists('siteTitulo')) $tpl->siteTitulo = $db1->row('titulo');
    if ($tpl->exists('siteFeed')) $tpl->siteFeed = servername() . '/feed/';

    if (!empty($db1->row('descricaocurta'))) {
        $bDescricaoCurta = true;
        if ($tpl->exists('descricaocurta')) $tpl->descricaocurta = $db1->row('descricaocurta');
    }

    if (!empty($db1->row('palavraschave'))) {
        $bPalavrasChave = true;
        if ($tpl->exists('palavraschave')) $tpl->palavraschave = $db1->row('palavraschave');
    }

    $txtImagemPrincipal = unserialize($db1->row('imagens'));
    if (empty($txtImagemPrincipal['principal'])) {
        if ($tpl->exists('imagemprincipal')) $tpl->imagemprincipal = '/themes/images/noimage.webp';
    } else {
        if ($tpl->exists('imagemprincipal')) $tpl->imagemprincipal = $txtImagemPrincipal['principal'];
    }

    $sGoogleAnalytcs = unserialize($db1->row('estatisticas'));
    if (!empty($sGoogleAnalytcs['googleanalytics'])) {
        $tpl->googleanalytics = $sGoogleAnalytcs['googleanalytics'];
        $tpl->block('BLOCK_HEADGOOGLEANALYTICS');
    }

    if ($tpl->exists('outrasmetatags1')) $tpl->outrasmetatags1 = $db1->row('outrasmetatags1');
    if ($tpl->exists('outrasmetatags2')) $tpl->outrasmetatags2 = $db1->row('outrasmetatags2');

    if (empty($rt->getPrimeiraURL())) {
        if ($tpl->exists('siteURL')) $tpl->siteURL = servername();
    } else {
        if ($tpl->exists('siteURL')) $tpl->siteURL = servername() . '/' . $rt->getURLCompleta();
    }
}

$db1->close();

function isPage(): bool
{
    global $dbBlogs1, $idsite, $rt;
    $db1 = new select($dbBlogs1);
    $db1->table('pages')
        ->where('idsite', $idsite)
        ->where('link')->prepared($rt->getUltimaURL())
        ->select();

    $db1->getResult();
    $txt = empty($db1->fetch()) ? false : true;
    $db1->close();

    return $txt;
}

function isPost(): bool
{
    global $dbBlogs1, $idsite, $rt;
    $db1 = new select($dbBlogs1);
    $db1->table('posts')
        ->where('idsite', $idsite)
        ->where('link')->prepared($rt->getUltimaURL())
        ->select();

    $db1->getResult();
    $txt = empty($db1->fetch()) ? false : true;
    $db1->close();

    if (existCategoriesSequences(getIDCategory($rt->getPenultimaURL())) !== str_replace('/' . $rt->getUltimaURL(), '', $rt->getURLCompleta())) {
        $txt = false;
    }

    return $txt;
}

function getIDCategory(string $link): int
{
    global $dbBlogs1, $idsite;

    $txt = 0;

    $db1 = new select($dbBlogs1);
    $db1->table('categorias')
        ->where('idsite', $idsite)
        ->where('link', $link)
        ->select();

    while ($row = $db1->fetch()) {
        $db1->rows($row);

        $txt = $db1->row('id');
    }

    $db1->close();

    return $txt;
}

function existCategoriesSequences($parent, $level = 1): string
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('categorias')
        ->where('id', $parent)
        ->where('idsite', $idsite)
        ->select();

    $txt = '';
    while ($row = $db1->fetch()) {
        $db1->rows($row);

        $txt = $row['link'];

        $categoria = existCategoriesSequences($db1->row('idcategoria'), $level + 1);

        if (!empty($categoria)) {
            $txt = $categoria . '/' . $row['link'];
        }
    }

    $db1->close();

    return $txt;
}
