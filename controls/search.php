<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

// Pesquisa
$sKeywords = CleanGET('keyword');

// Paginacao
$paginaAtual = emptyGET('page') ? 1 : CleanGET('page', FILTER_SANITIZE_NUMBER_INT);

$limit = 5; //9;
$start_from = ($paginaAtual - 1) * $limit;

$db1 = new select($dbBlogs1);
$db1->table('posts')
    ->whereCustom('idsite=? AND titulo LIKE ? OR 
idsite=? AND palavraschave LIKE ? OR
idsite=? AND descricaocurta LIKE ? OR
idsite=? AND resumo LIKE ?');

for ($i = 0; $i < 4; $i++) {
    $db1->prepared($idsite, 'i')
        ->prepared('%' . $sKeywords . '%');
}

$db1->limit($start_from, $limit)
    ->select();

$db1->getResult();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    $txtImagemPrincipal = unserialize($db1->row('imagens'))['principal'];
    if (empty($txtImagemPrincipal)) {
        if ($tpl->exists('pesquisarPostImagem')) $tpl->pesquisarPostImagem = '/themes/images/noimage.webp';
    } else {
        if ($tpl->exists('pesquisarPostImagem')) $tpl->pesquisarPostImagem = $txtImagemPrincipal;
    }
    if ($tpl->exists('pesquisarPostTitulo')) $tpl->pesquisarPostTitulo = $db1->row('titulo');
    if ($tpl->exists('pesquisarPostLink')) $tpl->pesquisarPostLink = '/' . existCategoriesSequences($row['id']) . '/' . $db1->row('link');
    if ($tpl->exists('pesquisarPostResumo')) $tpl->pesquisarPostResumo = stripslashes($db1->row('resumo'));

    $tpl->block('BLOCK_PESQUISARPOSTS');
}

$db1->close();

// Paginacao
function pesquisarPaginationTotal(): array
{
    global $dbBlogs1, $idsite, $start_from, $limit, $sKeywords;

    $total_records = '';
    $total_pages = '';

    $db1 = new select($dbBlogs1);
    $db1->column('COUNT(id) AS total')
        ->table('posts')
        ->whereCustom('idsite=? AND titulo LIKE ? OR 
        idsite=? AND palavraschave LIKE ? OR
        idsite=? AND descricaocurta LIKE ? OR
        idsite=? AND resumo LIKE ?');

    for ($i = 0; $i < 4; $i++) {
        $db1->prepared($idsite, 'i')
            ->prepared('%' . $sKeywords . '%');
    }

    $db1->limit($start_from, $limit)
        ->select();

    $db1->getResult();

    if ($db1->count() > 0) {
        $total_records = $db1->fetch()['total'];
        $total_pages = ceil($total_records / $limit);
    }

    $db1->close();

    return ['totalRecords' => $total_records, 'totalPages' => $total_pages];
}

$pesquisarPaginationTotal = pesquisarPaginationTotal();

if (!empty($pesquisarPaginationTotal['totalPages'])) {
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

    if ($paginaAtual >= $pesquisarPaginationTotal['totalPages']) {
        if ($tpl->exists('paginacaoAvancarDesativado')) $tpl->paginacaoAvancarDesativado = ' disabled';
        if ($tpl->exists('paginacaoAvancar')) $tpl->paginacaoAvancar = '#';
    } else {
        if ($tpl->exists('paginacaoAvancarDesativado')) $tpl->paginacaoAvancarDesativado = '';
        if ($tpl->exists('paginacaoAvancar')) $tpl->paginacaoAvancar = '?page=' . $paginacaoNext;
    }

    if ($pesquisarPaginationTotal['totalPages'] > 1) {
        for ($i = 1; $i <= $pesquisarPaginationTotal['totalPages']; $i++) {
            if ($tpl->exists('paginacaoItemAtivado')) $tpl->paginacaoItemAtivado = ($paginaAtual == $i) ? ' active' : '';
            if ($tpl->exists('paginacaoItemContador')) $tpl->paginacaoItemContador = $i;

            $tpl->block('BLOCK_PESQUISARPOSTPAGINACAO');
        }

        $tpl->block('BLOCK_PAGINACAO');
    }
}
