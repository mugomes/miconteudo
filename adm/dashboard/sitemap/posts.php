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

$sitemapPosts = new Sitemap(documentroot() . '/sites/' . $idsite . '/sitemap/posts-sitemap.xml.gz');
$sitemapPosts->setUseGzip(true);
$sitemapPosts->setMaxUrls(30000);

$db1 = new select($dbBlogs1);
$db1->table('posts')->where('idsite', $idsite)->orderby('id', false)->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);
    
    $sitemapPosts->addItem(servername() . '/' . existCategoriesSequences($row['idcategoria']) . '/' . $db1->row('link') . '/', time(), Sitemap::MONTHLY, 0.3);
}

$db1->close();

$sitemapPosts->write();
$sitemapPostagens = $sitemapPosts->getSitemapUrls(servername() . '/sites/' . $idsite . '/sitemap/');

$sPostTime = time();