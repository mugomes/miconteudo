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

if (requestPOST()) {
    $json = json_decode(CleanPOST('output'), true, 64);

    function parseJsonArray(array $jsonArray, int $parentID = 0): array
    {
        $return = [];
        foreach ($jsonArray as $subArray) {
            $returnSubSubArray = [];
            if (isset($subArray['children'])) {
                $returnSubSubArray = parseJsonArray($subArray['children'], $subArray['id']);
            }
            $return[] = array('id' => $subArray['id'], 'parentID' => $parentID);
            $return = array_merge($return, $returnSubSubArray);
        }

        return $return;
    }

    try {
        $jsonstring = CleanPOST('output') ?: '';

        // Decode it into an array
        $jsonDecoded = json_decode($jsonstring, true, 64);
        if (empty($jsonDecoded)) {
            echo 'Nenhuma categoria foi encontrada!';
            exit;
        }

        $sql = '';

        if (!empty($jsonDecoded)) {
            // Run the function above
            $readbleArray = parseJsonArray($jsonDecoded);

            // Update sql
            foreach ($readbleArray as $key => $value) {
                // $value should always be an array, but we do a check
                if (is_array($value)) {
                    if (isset($value['parentID'])) {
                        $parentid = $value['parentID'];
                    } else {
                        $parentid = 0;
                    }

                    $db1 = new update($dbBlogs1);
                    $db1->table('categorias')
                        ->add('idcategoria')->prepared($parentid)
                        ->add('ordem')->prepared($key)
                        ->add('dataalterado')->prepared(date('Y-m-d H:i:s'))
                        ->where('id')->prepared($value['id'])
                        ->update();
                    $db1->close();
                }
            }

            echo 'Salvo com sucesso!';
        } else {
            throw new Exception('Não foi possível salvar!');
        }
    } catch (Exception $ex) {
        mglog($ex->__toString());
        exit;
    }
}
