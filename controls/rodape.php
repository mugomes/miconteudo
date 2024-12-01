<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

$db1 = new select($dbBlogs1);
$db1->table('rodape')
    ->where('idsite', $idsite)
    ->select();

$db1->getResult();

while ($row = $db1->fetch()) {
    $db1->rows($row);

    if (!empty($db1->row('estilos'))) {
        if ($tpl->exists('estilos')) $tpl->estilos .= stripslashes($db1->row('estilos'));
        $tpl->block('BLOCK_HEADESTILOS');
    }

    if ($tpl->exists('rodapeDescricao')) {
        if ($tpl->exists('rodapeDescricao')) $tpl->rodapeDescricao = str_replace('<button', '<input id="txtToken" name="txtToken" type="hidden" value="' . $_SESSION['tokenform'] . '"><input id="txtEnviar" name="txtEnviar" type="hidden" value=""><button',stripslashes($db1->row('descricao')));
    }
}

$db1->close();
