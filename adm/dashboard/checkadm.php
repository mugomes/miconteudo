<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\select;

if (!defined('miconteudo')) {
    exit;
}

$vetor = filter_input(INPUT_COOKIE, 'admInfo', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

$cLogin = false;

if (!empty($vetor)) {
    if (!empty($vetor['strToken1']) && !empty($vetor['strToken2'])) {
        $db1 = new select($dbBlogs1);
        $db1->disableSQLCache()
        ->table('users')
        ->where('idsite', $idsite)
        ->where('idtoken1')->prepared($vetor['strToken1'])
        ->select();
        $db1->getResult();
        if ($db1->count() > 0) {
            $row = $db1->fetch();

            if (password_verify($vetor['strToken2'], $row['idtoken2'])) {
                $cLogin = true;
            } else {
                $cLogin = false;
            }
        } else {
            $cLogin = false;
        }

        $db1->close();
    } else {
        $cLogin = false;
    }
} else {
    $cLogin = false;
}