<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\router\router;
use MiConteudo\template\template;

if (!defined('miconteudo')) {
    exit;
}

include_once(dirname(__FILE__) . '/functions.php');
include_once(documentroot() . '/classes/vendor/autoload.php');

if (file_exists(documentroot()  . '/core/install.php')) {
    include_once(documentroot() . '/core/install.php');
    exit;
}

include_once(documentroot() . '/core/config.php');

if (isset($sandbox)) {
    if (isset($sandbox)) {
        error_reporting(E_ALL);

        /* Habilita a exibição de erros */
        ini_set("display_errors", 1);
    }
}

$start = microtime(true);

$rt = new router();
if ($rt->getURLCompleta() == 'feed') {
    include_once(documentroot() . '/plugins/feed/feed.php');
} elseif ($rt->getURLCompleta() == 'robots.txt') {
    include_once(documentroot() . '/plugins/robots/robots.php');
} elseif ($rt->getURLCompleta() == 'sitemapindex.xml') {
    include_once(documentroot() . '/plugins/sitemap/sitemap.php');
} else {
    if (empty($_SESSION['tokenform'])) {
        $_SESSION['tokenform'] = md5(uniqid(mt_rand(), true));
    }

    $tpl = new template(dirname(__FILE__) . '/layout/layout.html');

    include_once(dirname(__FILE__) . '/options.php');

    if (file_exists(documentroot() . '/sites/' . $idsite . '/plugins/plugins.php')) {
        include_once(documentroot() . '/sites/' . $idsite . '/plugins/plugins.php');
    }

    if ($rt->getPrimeiraURL() == '') {
        if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/home.html');
        include_once(dirname(__FILE__) . '/home.php');
    } elseif ($rt->getPrimeiraURL() == 's') {
        if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/pesquisar.html');
        include_once(dirname(__FILE__) . '/search.php');
    } else {
        if (isPage() && empty($rt->getURL(1))) {
            if (file_exists(documentroot() . '/sites/' . $idsite . '/themes/pagina-' . CleanDB($rt->getPrimeiraURL()) . '.html')) {
                if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/pagina-' . CleanDB($rt->getPrimeiraURL()) . '.html');
            } else {
                if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/pagina.html');
            }
            include_once(dirname(__FILE__) . '/page.php');
        } else {
            if (isPost()) {
                if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/post.html');
                include_once(dirname(__FILE__) . '/post.php');
            } else {
                if ($rt->getPrimeiraURL() == 'blog' && empty($rt->getURL(1))) {
                    if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/blog.html');
                    include_once(dirname(__FILE__) . '/blog.php');
                } else {
                    if (existCategoriesSequences(getIDCategory($rt->getUltimaURL())) == $rt->getURLCompleta()) {
                        if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/categoria.html');
                        include_once(dirname(__FILE__) . '/categories.php');
                    } else {
                        //404
                        if ($tpl->exists('conteudo')) $tpl->addFile('conteudo', documentroot() . '/sites/' . $idsite . '/themes/pagina404.html');
                        include_once(dirname(__FILE__) . '/page404.php');
                    }
                }
            }
        }
    }

    if ($tpl->exists('idsite')) $tpl->idsite = $idsite;

    if (!isset($sandbox)) {
        if ($tpl->exists('sminify')) $tpl->sminify = '.min';
    }

    include_once(dirname(__FILE__) . '/topo.php');

    if ($tpl->exists('topo')) $tpl->addFile('topo', documentroot() . '/sites/' . $idsite . '/themes/topo.html');
    if ($tpl->exists('menutopo')) $tpl->menutopo = menutopo(0);
    if ($tpl->exists('barralateral1')) $tpl->addFile('barralateral1', documentroot() . '/sites/' . $idsite . '/themes/barralateral1.html');
    if ($tpl->exists('barralateral2')) $tpl->addFile('barralateral2', documentroot() . '/sites/' . $idsite . '/themes/barralateral2.html');
    if ($tpl->exists('rodape')) $tpl->addFile('rodape', documentroot() . '/sites/' . $idsite . '/themes/rodape.html');

    // Head - para não duplicar
    if ($tpl->exists('descricaocurta')) {
        if ($bDescricaoCurta) {
            $tpl->block('BLOCK_HEADDESCRIPTION');
            $tpl->block('BLOCK_HEADOGDESCRIPTION');
            $tpl->block('BLOCK_HEADTWITTERDESCRIPTION');
        }
    }

    if ($tpl->exists('palavraschave')) {
        if ($bPalavrasChave) {
            $tpl->block('BLOCK_HEADKEYWORDS');
        }
    }

    include_once(dirname(__FILE__) . '/modules/sendmail.php');

    if ($tpl->exists('microtime')) $tpl->microtime = microtime(true) - $start;

    include_once(dirname(__FILE__) . '/modules/tinyhtmlminify/tinyhtmlminify.php');

    $minifier = new TinyHtmlMinifier([
        'collapse_whitespace' => true,
        'disable_comments' => false,
    ]);

    $sConteudoShow = $tpl->show();

    if (isset($sandbox)) {
        echo $sConteudoShow;
    } else {
        echo $minifier->minify($sConteudoShow);
    }
}
