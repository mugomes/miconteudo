<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

namespace MiConteudo\database;

class select extends database
{
    private bool $sDesativarSQLCache = false;
    private array $sColunas = [];

    private array $sRows = [];

    public function disableSQLCache()
    {
        $this->sDesativarSQLCache = true;
        return $this;
    }

    public function innerJoin(string $nome)
    {
        $this->sTabelas[] = ' INNER JOIN ' . $this->sPrefix . $nome;
        return $this;
    }

    public function column(string $nome, string $apelido = '')
    {
        if (empty($apelido)) {
            $this->sColunas[] = $nome;
        } else {
            $this->sColunas[] = $nome . ' AS ' . $apelido;
        }

        return $this;
    }

    private function getColunas()
    {
        return implode(', ', $this->sColunas);
    }

    public function select()
    {
        try {
            $txt = 'SELECT ';
            if ($this->sDesativarSQLCache) {
                $txt .= '';
            } else {
                $txt .= 'SQL_CACHE ';
            }

            if (empty($this->getColunas())) {
                $txt .= '* ';
            } else {
                $txt .= $this->getColunas() . ' ';
            }

            $txt .= 'FROM ' . $this->getTable();

            $txt .= $this->getWhere();
            $txt .= $this->getOrderBy();
            $txt .= $this->getLimit();

            if (empty($this->sPreparado)) {
                if ($this->sResult = mysqli_query($this->sConecta, $txt)) {
                    $this->sFechaResult = true;
                } else {
                    $this->sFechaResult = false;
                }
            } else {
                $sTipo = '';
                $sValores = [];
                foreach ($this->sPreparado as $row) {
                    $sTipo .= $row[0];
                    $sValores[] = $row[1];
                }

                if ($this->sResult = mysqli_prepare($this->sConecta, $txt)) {
                    mysqli_stmt_bind_param($this->sResult, $sTipo, ...$sValores);
                    mysqli_stmt_execute($this->sResult);
                }
            }
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex);
        }
    }

    public function execute()
    {
        mysqli_stmt_execute($this->sResult);
    }

    public function count(): string|int
    {
        if (empty($this->sPreparado)) {
            return mysqli_num_rows($this->sResult);
        } else {
            return mysqli_num_rows($this->sQuery);
        }
    }

    public function getResult()
    {
        $this->sQuery = mysqli_stmt_get_result($this->sResult);
    }
    public function fetch(): array|false|null
    {
        if (empty($this->sPreparado)) {
            return mysqli_fetch_array($this->sResult, MYSQLI_ASSOC);
        } else {
            return mysqli_fetch_array($this->sQuery, MYSQLI_ASSOC);
        }
    }

    public function rows(array $rows)
    {
        $this->sRows = $rows;
    }

    public function row(string $nome): mixed
    {
        return empty($this->sRows[$nome]) ? '' : $this->sRows[$nome];
    }
}
