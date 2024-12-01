<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

header("HTTP/1.0 404 Not Found");

$db1 = new select($dbBlogs1);
$db1->table('pages')
    ->where('idsite', $idsite)
    ->where('link', '404')
    ->select();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    if ($tpl->exists('titulo')) $tpl->titulo =  $db1->row('titulo') . ' | ' .  $tpl->titulo;

    $txtImagemPrincipal = unserialize($db1->row('imagens'));
    if (!empty($txtImagemPrincipal['principal'])) {
        if ($tpl->exists('imagemprincipal')) $tpl->imagemprincipal = $txtImagemPrincipal['principal'];
    }

    if (!empty($db1->row('estilos'))) {
        if ($tpl->exists('estilos')) $tpl->estilos = stripslashes($db1->row('estilos'));
        $tpl->block('BLOCK_HEADESTILOS');
    }

    if ($tpl->exists('pagina404Titulo')) $tpl->pagina404Titulo = $db1->row('titulo');

    if ($tpl->exists('pagina404Descricao')) {
        if ($tpl->exists('pagina404Descricao')) $tpl->pagina404Descricao = str_replace('<button', '<input id="txtToken" name="txtToken" type="hidden" value="' . $_SESSION['tokenform'] . '"><input id="txtEnviar" name="txtEnviar" type="hidden" value=""><button',stripslashes($db1->row('descricao')));
    }
}

$db1->close();
