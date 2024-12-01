<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

// Paginacao
$paginaAtual = emptyGET('page') ? 1 : CleanGET('page', FILTER_SANITIZE_NUMBER_INT);

$limit = 5; //9;
$start_from = ($paginaAtual - 1) * $limit;

$db1 = new select($dbBlogs1);
$db1->table('categorias')
    ->where('idsite', $idsite)
    ->where('idcategoria', getIDCategory($rt->getUltimaURL()))
    ->where('rascunho', 2)
    ->limit($start_from, $limit)
    ->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    $txtImagemPrincipal = unserialize($db1->row('imagens'))['principal'];
    if (empty($txtImagemPrincipal)) {
        if ($tpl->exists('catPostImagem')) $tpl->catPostImagem = '/themes/images/noimage.webp';
    } else {
        if ($tpl->exists('catPostImagem')) $tpl->catPostImagem = $txtImagemPrincipal;
    }
    if ($tpl->exists('catPostTitulo')) $tpl->catPostTitulo = $db1->row('titulo');
    if ($tpl->exists('catPostLink')) $tpl->catPostLink = '/' . existCategoriesSequences($row['idcategoria']) . '/' . $db1->row('link');
    if ($tpl->exists('catPostResumo')) $tpl->catPostResumo = stripslashes($db1->row('resumo'));

    $tpl->block('BLOCK_CATPOSTS');
}

$db1->close();

$db1 = new select($dbBlogs1);
$db1->table('posts')
    ->where('idsite', $idsite)
    ->where('idcategoria', getIDCategory($rt->getUltimaURL()))
    ->where('rascunho', 2)
    ->limit($start_from, $limit)
    ->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    $txtImagemPrincipal = unserialize($db1->row('imagens'))['principal'];
    if (empty($txtImagemPrincipal)) {
        if ($tpl->exists('catPostImagem')) $tpl->catPostImagem = '/themes/images/noimage.webp';
    } else {
        if ($tpl->exists('catPostImagem')) $tpl->catPostImagem = $txtImagemPrincipal;
    }
    if ($tpl->exists('catPostTitulo')) $tpl->catPostTitulo = $db1->row('titulo');
    if ($tpl->exists('catPostLink')) $tpl->catPostLink = '/' . existCategoriesSequences($row['idcategoria']) . '/' . $db1->row('link');
    if ($tpl->exists('catPostResumo')) $tpl->catPostResumo = stripslashes($db1->row('resumo'));

    $tpl->block('BLOCK_CATPOSTS');
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

$breadcrumb = catBreadcrumb(getIDCategory($rt->getUltimaURL()));
$breadcrumbTitulo = explode('/', $breadcrumb['titulo']);
$breadcrumbLink = explode('/', $breadcrumb['link']);
$breadcrumbCount = count($breadcrumbTitulo);
$breadcrumbShowLink = '';
$breadcrumbActive = '';

for ($i = 0; $i < $breadcrumbCount; $i++) {

    if ($breadcrumbLink[$i] == $rt->getUltimaURL()) {
        if ($tpl->exists('breadcrumbNoActiveTitulo')) $tpl->breadcrumbNoActiveTitulo = $breadcrumbTitulo[$i];
        $tpl->block('BLOCK_BREADCRUMBNOACTIVE');
    } else {
        $breadcrumbShowLink .= $breadcrumbLink[$i] . '/';
        if ($tpl->exists('breadcrumbActiveLink')) $tpl->breadcrumbActiveLink = $breadcrumbShowLink;

        if ($tpl->exists('breadcrumbActiveTitulo')) $tpl->breadcrumbActiveTitulo = $breadcrumbTitulo[$i];
        if ($tpl->exists('breadcrumbActive')) $tpl->breadcrumbActive = ' active';
        $tpl->block('BLOCK_BREADCRUMBACTIVE');
    }

    $tpl->block('BLOCK_BREADCRUMB');
}

// Paginacao
function catPaginationTotal(): array
{
    global $dbBlogs1, $idsite, $limit, $rt;

    $total_records = '';
    $total_pages = '';

    $db1 = new select($dbBlogs1);
    $db1->column('COUNT(id) AS total')
        ->table('posts')
        ->where('idsite', $idsite)
        ->where('idcategoria', getIDCategory($rt->getUltimaURL()))
        ->select();

    if ($db1->count() > 0) {
        $total_records = $db1->fetch()['total'];
        $total_pages = ceil($total_records / $limit);
    }

    $db1->close();

    return ['totalRecords' => $total_records, 'totalPages' => $total_pages];
}

$catPaginationTotal = catPaginationTotal();
$paginacaoPrev = $paginaAtual - 1;
$paginacaoNext = $paginaAtual + 1;
$paginacaoVoltarDesativado = '';
$paginacaoVoltar = '';
$paginacaoItemAtivado = '';
$paginacaoItemLink = '';
$paginacaoItem = '';
$paginacaoAvancarDesativado = '';
$paginacaoAvancar = '';

// Paginacao
if ($paginaAtual <= 1) {
    if ($tpl->exists('paginacaoVoltarDesativado')) $tpl->paginacaoVoltarDesativado = ' disabled';
    if ($tpl->exists('paginacaoVoltar')) $tpl->paginacaoVoltar = '#';
} else {
    if ($tpl->exists('paginacaoVoltarDesativado')) $tpl->paginacaoVoltarDesativado = '';
    if ($tpl->exists('paginacaoVoltar')) $tpl->paginacaoVoltar = '?page=' . $paginacaoPrev;
}

if ($paginaAtual >= $catPaginationTotal['totalPages']) {
    if ($tpl->exists('paginacaoAvancarDesativado')) $tpl->paginacaoAvancarDesativado = ' disabled';
    if ($tpl->exists('paginacaoAvancar')) $tpl->paginacaoAvancar = '#';
} else {
    if ($tpl->exists('paginacaoAvancarDesativado')) $tpl->paginacaoAvancarDesativado = '';
    if ($tpl->exists('paginacaoAvancar')) $tpl->paginacaoAvancar = '?page=' . $paginacaoNext;
}

for ($i = 1; $i <= $catPaginationTotal['totalPages']; $i++) {
    if ($tpl->exists('paginacaoItemAtivado')) $tpl->paginacaoItemAtivado = ($paginaAtual == $i) ? ' active' : '';
    if ($tpl->exists('paginacaoItemContador')) $tpl->paginacaoItemContador = $i;

    $tpl->block('BLOCK_CATPOSTPAGINACAO');
}
