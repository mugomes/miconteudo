<?php
if (!defined('miconteudo')) {
    exit;
}

function miplugins(string $nome):string|int {
    if ($nome == 'exemplo') {
        return 'Este é um exemplo!';
    }
}