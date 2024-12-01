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

$sitemapPages = new Sitemap(documentroot() . '/sites/' . $idsite . '/sitemap/pages-sitemap.xml.gz');
$sitemapPages->setUseGzip(true);
$sitemapPages->setMaxUrls(30000);
$sitemapPages->addItem(servername() . '/', time(), Sitemap::MONTHLY, 0.8);
/* Paginas */
$db1 = new select($dbBlogs1);
$db1->table('pages')
->where('NOT link', '404')->and()
->where('NOT link', 'home')
->orderby('id', false)
->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);
    $sitemapPages->addItem(servername() . '/' . $db1->row('link') . '/', time(), Sitemap::MONTHLY, $sPriority);
}

$db1->close();

$sitemapPages->write();
$sitemapPaginas = $sitemapPages->getSitemapUrls(servername() . '/sites/' . $idsite . '/sitemap/');

$sPageTime = time();
