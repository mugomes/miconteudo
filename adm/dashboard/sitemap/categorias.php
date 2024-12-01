<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br
use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

use samdark\sitemap\Sitemap;

$sitemapCats = new Sitemap(documentroot() . '/sites/' . $idsite . '/sitemap/categories-sitemap.xml.gz');
$sitemapCats->setUseGzip(true);
$sitemapCats->setMaxUrls(30000);

function sitemapCats($parent, $level = 1)
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('categorias')
        ->where('idsite', $idsite)
        ->where('idcategoria', $parent)
        ->orderby('ordem')
        ->orderby('id', false)
        ->select();

    $txt = '';
    while ($row = $db1->fetch()) {
        $db1->rows($row);

        $txt .= '|' . existCategoriesSequences($db1->row('id'));
        
        $menu = sitemapCats($db1->row('id'), $level + 1);

        if (!empty($menu)) {
            $txt .= $menu;
        }
    }

    $db1->close();

    return $txt;
}

$gerarSitemapCats = sitemapCats(0);

$sSplit = explode('|', $gerarSitemapCats);
$sSplit = array_unique($sSplit);
foreach ($sSplit as $name => $value) {
    if (!empty($value)) {
        $sitemapCats->addItem(servername() . '/' . $value . '/', time(), Sitemap::MONTHLY, $sPriority);
    }
}

$sitemapCats->write();
$sitemapCategorias = $sitemapCats->getSitemapUrls(servername() . '/sites/' . $idsite . '/sitemap/');

$sCatTime = time();
