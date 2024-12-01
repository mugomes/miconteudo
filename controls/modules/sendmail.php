<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

if (!defined('miconteudo')) {
    exit;
}

if (requestPOST()) {
    if (empty(CleanPOST('txtEnviar'))) {
        if ($_SESSION['tokenform'] == CleanPost('txtToken')) {
            $sSendEmail = '<h2>Informações:</h2>';
            foreach ($_POST as $name => $value) {
                if ($name !== 'txtToken' && $name !== 'txtEnviar') {
                    $sSendEmail .= '<strong>' . str_replace('txt', '', $name) . ':</strong>' . trim(strip_tags($value)) . '<br>';
                }
            }
            if (sendmail(
                [
                    'name' => '',
                    'email' => 'noreply@' . $aIDSite['dominio']
                ],
                [
                    'name' => '',
                    'email' => 'contato@' . $aIDSite['dominio']
                ],
                'Mensagem de Formulário',
                $sSendEmail
            )) {
                $tpl->block('BLOCK_FORMENVIADO');
            } else {
                $tpl->block('BLOCK_FORMNAOENVIADO');
            }

            $_SESSION['tokenform'] = md5(uniqid(mt_rand(), true));
        }
    }
}
