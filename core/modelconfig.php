<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Desenvolvido por: Murilo Gomes Julio (Mestre da Info)
// Site: https://www.mestredainfo.com.br

if (!defined('miconteudo')) {
    exit;
}

use MiConteudo\database\select;

$dbBlogs1 = [
    'server' => '{miservidor}',
    'username' => '{miusuario}',
    'password' =>  '{misenha}',
    'database' => '{midatabase}',
    'prefix' => '{miprefixo}'
];

function getIDSite(): array
{
    global $dbBlogs1;

    $db1 = new select($dbBlogs1);
    $db1->table('sites')
        ->where('link')->prepared(servername(false, true))
        ->select();
    $db1->getResult();

    $row = $db1->fetch();
    if (!empty($row)) {
        $txt['id'] = $row['id'];
        $txt['dominio'] = $row['link'];
    }

    $db1->close();

    return empty($txt) ? ['id' => 0] : $txt;
}

if (!file_exists(dirname(__FILE__) . '/install.php')) {
    $aIDSite = getIDSite();
    $idsite = $aIDSite['id'];

    if (empty($idsite)) {
        echo 'Este domínio não está configurado!';
        exit;
    }
}
