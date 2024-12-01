<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

header("Content-Type: text/xml; charset=UTF-8", true);

function getTextBetweenTags(string $string): string
{
    $pattern = '/{(.*?)\}/';
    preg_match($pattern, $string, $matches);
    return $matches[1];
}

function compressfeed(string $buffer): string
{
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', ' ', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), ' ', $buffer);
    return $buffer;
}

function convertDataImutavel(string $data): string
{
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $data);
    $new_date = $date->format(DateTimeInterface::RFC2822);

    return $new_date;
}

function getCategoria($parent, $level = 1): string
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

        $categoria = getCategoria($db1->row('idcategoria'), $level + 1);

        if (!empty($categoria)) {
            $txt = $categoria . '/' . $row['link'];
        }
    }

    $db1->close();

    return $txt;
}

function getLastBuildDate(): string
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->column('id')->column('idsite')->column('datapublicado')
        ->table('posts')
        ->where('idsite', $idsite)
        ->orderby('id', false)
        ->limit(0, 1)
        ->select();

    if ($db1->count() == 0) {
        $txtPostDate = '0000-00-00';
    } else {
        $db1->rows($db1->fetch());

        $txtPostDate = $db1->row('datapublicado');
    }

    $db1->close();

    $db2 = new select($dbBlogs1);
    $db2->column('id')->column('idsite')->column('datapublicado')
        ->table('pages')
        ->where('idsite', $idsite)
        ->orderby('id', false)
        ->limit(0, 1)
        ->select();

    if ($db2->count() == 0) {
        $txtPageDate = '0000-00-00';
    } else {
        $db2->rows($db2->fetch());
        $txtPageDate = $db2->row('datapublicado');
    }

    $db2->close();

    if ($txtPostDate > $txtPageDate) {
        return $txtPostDate;
    } elseif ($txtPostDate < $txtPageDate) {
        return $txtPageDate;
    } else {
        return $txtPageDate;
    }
}

$sLastBuildDate = getLastBuildDate();

/* Header */
$db1 = new select($dbBlogs1);
$db1->table('options')->where('idsite', $idsite)->select();

if ($db1->count() > 0) {
    $db1->rows($db1->fetch());

    $sTituloSite = $db1->row('titulo');

    $publicado = "";
    $publicado = date('r', strtotime($sLastBuildDate));
    $publicado = str_replace("-0300", "+0000", $publicado);

    $txt = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $txt .= '<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" version="2.0">' . "\n";
    $txt .= '<channel>' . "\n";
    $txt .= '<title>' . $db1->row('titulo') . '</title>' . "\n";
    $txt .= '<link>' . servername() . '</link>' . "\n";

    if (!empty($db1->row('descricaocurta'))) {
        $txt .= '<description>' . $db1->row('descricaocurta') . '</description>' . "\n";
    }

    $txt .= '<atom:link href="' . servername() . '/feed/" rel="self" type="application/rss+xml"/>' . "\n";
    $txt .= '<lastBuildDate>' . $sLastBuildDate . '</lastBuildDate>' . "\n";
    $txt .= '<language>pt-PT</language>' . "\n";
    $txt .= '<sy:updatePeriod>hourly</sy:updatePeriod>' . "\n";
    $txt .= '<sy:updateFrequency>3</sy:updateFrequency>' . "\n";
}

$db1->close();

/* Page */
$db2 = new select($dbBlogs1);
$db2->table('pages')
->where('idsite', $idsite)
    ->where('NOT link', '404')
    ->orderby('datapublicado', false)
    ->limit(0, 5)
    ->select();

while ($rPage = $db2->fetch()) {
    $db2->rows($rPage);

    $descricao = "";
    $descricao = str_replace("&nbsp", " ", compressfeed(CleanDB(strip_tags($db2->row('descricaocurta')))));
    $publicado = "";
    $publicado = convertDataImutavel($db2->row('datapublicado'));
    //$publicado = date('r', strtotime($db2->row('pgdatapublicado')));
    //$publicado = str_replace("-0300", "+0000", $publicado);

    $txt .= '<item>' . "\n";
    $txt .= '<title>' . $db2->row('titulo') . '</title>' . "\n";

    if ($db2->row('link') == "home") {
        $txt .= '<link>' . servername() . '/</link>' . "\n";
    } else {
        $txt .= '<link>' . servername() . '/' . $db2->row('link') . '/</link>' . "\n";
    }
    $txt .= '<pubDate>' . $publicado . '</pubDate>' . "\n";
    $txt .= '<dc:creator>' . $sTituloSite . '</dc:creator>' . "\n";

    if ($db2->row('link') == "home") {
        $txt .= '<guid isPermaLink="false">' . servername() . '/</guid>' . "\n";
    } else {
        $txt .= '<guid isPermaLink="false">' . servername() . '/' . $db2->row('link') . '/</guid>' . "\n";
    }

    $txt .= '<description><![CDATA[' . $descricao . ']]></description>' . "\n";
    $txt .= '</item>' . "\n";
}

$db2->close();

/* Posts */
$db3 = new select($dbBlogs1);
$db3->table('posts')
->where('idsite', $idsite)
    ->orderby('datapublicado', false)
    ->limit(0, 10)
    ->select();

while ($rPost = $db3->fetch()) {
    $db3->rows($rPost);

    $descricao = "";
    $descricao = str_replace("&nbsp", " ", compressfeed(CleanDB(strip_tags($db3->row('resumo')))));
    $publicado = "";
    $publicado = convertDataImutavel($db3->row('datapublicado'));

    $getCategorias = '';
    $getCategorias = getCategoria($db3->row('idcategoria'));

    $txt .= '<item>' . "\n";
    $txt .= '<title>' . $db3->row('titulo') . '</title>' . "\n";
    $txt .= '<link>' . servername() . '/' . $getCategorias . '/' . $db3->row('link') . '/</link>' . "\n";
    $txt .= '<pubDate>' . $publicado . '</pubDate>' . "\n";
    $txt .= '<dc:creator>' . $sTituloSite . '</dc:creator>' . "\n";

    $txt .= '<guid isPermaLink="false">' . servername() . '/' . $getCategorias . '/' . $db3->row('link') . '/</guid>' . "\n";
    $txt .= '<description><![CDATA[' . $descricao . ']]></description>' . "\n";
    $txt .= '</item>' . "\n";
}

$db3->close();

$txt .= '</channel>' . "\n";
$txt .= '</rss>' . "\n";

echo $txt;
