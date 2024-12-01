<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\update;

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

if (getenv('REQUEST_METHOD') == 'POST') {
    $sHTML = str_replace('<body', '<div', CleanPOST('txtDescricao'));
    $sHTML = str_replace('</body>', '</div>', $sHTML);
    
    $sProjeto = emptyPOST('txtProjeto') ? '' : addslashes(CleanPOST('txtProjeto'));
    $sDescricao = emptyPOST('txtDescricao') ? '' : addslashes($sHTML);
    $sEstilos = emptyPOST('txtEstilos') ? '' : addslashes(CleanPOST('txtEstilos'));
    $sDataAlterado = date('Y-m-d H:i:s');

    $db1 = new update($dbBlogs1);
    $db1->table('posts')
        ->add('projeto')->prepared($sProjeto)
        ->add('descricao')->prepared($sDescricao)
        ->add('estilos')->prepared($sEstilos)
        ->add('dataalterado')->prepared($sDataAlterado)
        ->where('id')->prepared(CleanGET('id'))
        ->where('idsite', $idsite)
        ->update();
    $db1->close();
}
