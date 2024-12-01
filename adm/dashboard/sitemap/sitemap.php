<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

if (ini_get('zlib.output_compression') == 'Off') {
    if (extension_loaded('zlib')) {
        ob_start('ob_gzhandler');
    }
}

header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

date_default_timezone_set("America/Sao_Paulo");

define('miconteudo', true);

include_once(dirname(__FILE__, 4) . '/controls/functions.php');
include_once(documentroot() . '/classes/vendor/autoload.php');
include_once(documentroot() . '/core/config.php');

if (isset($sandbox)) {
    error_reporting(E_ALL);

    /* Habilita a exibição de erros */
    ini_set("display_errors", 1);
}


include_once(dirname(__FILE__, 2) . '/controls/checkadm.php');

if (!file_exists(documentroot() . '/sites/' . $idsite . '/sitemap/')) {
    mkdir(documentroot() . '/sites/' . $idsite . '/sitemap/');
}

use samdark\sitemap\Index;

$sPriority = 0.3;

include_once(dirname(__FILE__) . '/posts.php');
include_once(dirname(__FILE__) . '/pages.php');
include_once(dirname(__FILE__) . '/categorias.php');

/* Index */
// create sitemap index file
$index = new Index(documentroot() . '/sites/' . $idsite . '/sitemap/sitemapindex.xml');
$index->setStylesheet(servername() . '/sites/' . $idsite . '/sitemap/sitemap-stylesheet.xsl');

// add URLs
foreach ($sitemapPostagens as $sitemapUrl) {
    $index->addSitemap($sitemapUrl, $sPostTime);
}

foreach ($sitemapPaginas as $sitemapUrl) {
    $index->addSitemap($sitemapUrl, $sPageTime);
}

foreach ($sitemapCategorias as $sitemapUrl) {
    $index->addSitemap($sitemapUrl, $sCatTime);
}

// write it
$index->write();

if (!file_exists(documentroot() . '/sites/' . $idsite . '/sitemap/sitemap-stylesheet.xsl')) {
    copy(dirname(__FILE__) . '/template/sitemap-stylesheet.xsl', documentroot() . '/sites/' . $idsite . '/sitemap/sitemap-stylesheet.xsl');
}

redirect('/adm/dashboard/dashboard.php');
