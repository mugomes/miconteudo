<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

namespace MiConteudo\router;

class router
{
    private array $sURLs = array();
    private bool $erro404 = true;

    /* Obtém a URL e realiza a separação das partes das URLs */
    public function __construct()
    {
        $sURLs = requestURI();

        if (empty($sURLs)) {
            $sURLs = '/';
        }

        $sURLParts = array_values(array_filter(explode('/', $sURLs)));

        $this->sURLs = [$sURLs, $sURLParts];
    }

    /* Retorna o array das partes da URL */
    public function getArrayURLs(): array
    {
        return $this->sURLs[1];
    }

    /* Retorna a URL completa com as barras */
    public function getURLCompleta(): string
    {
        return implode('/', $this->sURLs[1]);
    }

    /* Verifica se a parte da URL existe */
    public function verificarURL(string $nome): bool
    {
        if (preg_match('#^' . $nome . '$#iu', $this->sURLs[0], $matches)) {
            $retorno = true;
        } else {
            /* if (preg_match('#^/blog/([a-z-0-9-]*)$#iu', '/blog/sadsadas', $matches)) { */
            $retorno = false;
        }

        return $retorno;
    }

    /* Retorna a parte URL identificada por um índice */
    public function getURL(int $ntxt): string
    {
        $retorno = empty($this->sURLs[1][$ntxt]) ? '' : $this->sURLs[1][$ntxt];

        return $retorno;
    }

    /* Primeira URL */
    public function getPrimeiraURL(): string
    {
        return empty($this->sURLs[1][0]) ? '' : $this->sURLs[1][0];
    }

    /* Penultima URL */
    public function getPenultimaURL(): string
    {
        if (!empty($this->sURLs[1][count($this->sURLs[1]) - 2])) {
            return $this->sURLs[1][count($this->sURLs[1]) - 2];
        } else {
            return '';
        }
    }

    /* Ultima URL */
    public function getUltimaURL(): string
    {
        return end($this->sURLs[1]);
    }

    /* Verifica se o erro404 está ativo, caso esteja roda a função de erro */
    public function erro404(callable $callback)
    {
        if ($this->erro404) {
            call_user_func($callback);
        }
    }
}
