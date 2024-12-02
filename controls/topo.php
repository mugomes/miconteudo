<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

function menutopo(int $parent, int $level = 1): string
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('menutopo')
        ->where('idsite', $idsite)
        ->where('idmenu', $parent)
        ->orderby('ordem')
        ->select();

    $txt = '';
    while ($row = $db1->fetch()) {
        $db1->rows($row);
        $txt .= '<li>';
        $txt .= '<a href="' . $db1->row('link') . '"';
        
        if ($db1->row('ativarnovajanela') == 1) {
            $txt .= ' target="_blank" rel="noopener nofollow"';
        }

        $txt .= '>';
        $txt .= $db1->row('nome');
        $txt .= '</a>';

        $menu = menutopo($db1->row('id'), $level + 1);

        if (!empty($menu)) {
            $txt .= '<ul class="submenu">' . $menu . '</ul>';
        }

        $txt .= '</li>';
    }

    $db1->close();

    return $txt;
}

function menulateral(int $parent, int $level = 1): string
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('menulateral')
        ->where('idsite', $idsite)
        ->where('idmenu', $parent)
        ->orderby('ordem')
        ->select();

    $txt = '';
    while ($row = $db1->fetch()) {
        $db1->rows($row);
        $txt .= '<li>';
        $txt .= '<a href="' . $db1->row('link') . '">';
        $txt .= $db1->row('titulo');
        $txt .= '</a>';

        $menu = menutopo($db1->row('id'), $level + 1);

        if (!empty($menu)) {
            $txt .= '<ul>' . $menu . '</ul>';
        }

        $txt .= '</li>';
    }

    $db1->close();

    return $txt;
}

function categorias(int $parent, int $level = 1):string
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('categorias')
        ->where('idsite', $idsite)
        ->where('idcategoria', $parent)
        ->orderby('ordem')
        ->select();

    $txt = '';
    while ($row = $db1->fetch()) {
        $db1->rows($row);
        $txt .= '<li style="margin-top:7px;">';
        $txt .= '<a href="/' . $db1->row('link') . '/">';
        $txt .= $db1->row('titulo');
        $txt .= '</a>';

        $menu = categorias($db1->row('id'), $level + 1);

        if (!empty($menu)) {
            $txt .= '<ul style="margin-left:0px;list-style-type: none;">' . $menu . '</ul>';
        }

        $txt .= '</li>';
    }

    $db1->close();

    return $txt;
}