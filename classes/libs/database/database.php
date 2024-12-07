<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

namespace MiConteudo\database;

class database
{
    protected mixed $sConecta;
    protected mixed $sResult;
    protected mixed $sQuery;
    protected string $sCharset = 'utf8mb4';

    protected string $sPrefix = '';

    protected array $sPreparado = [];
    protected bool $sFechaResult = false; // Select

    protected array $sTabelas = [];
    protected array $sWhere = [];
    protected array $sOrderBy = [];
    protected string $sLimit = '';

    protected bool $sSemAspas = false;
    protected bool $sSemIgual = false;
    protected string $sAndOr = ' AND ';

    private bool $sSandbox = false;

    public function __construct(array $db, bool $sandbox = false)
    {
        try {
            $this->sSandbox = $sandbox;

            $this->sConecta = mysqli_connect($db['server'], $db['username'], $db['password'], $db['database']);
            $this->sPrefix = $db['prefix'];

            if (mysqli_connect_errno()) {
                throw new \Exception(mysqli_connect_error());
            }
        } catch (\mysqli_sql_exception | \Exception $ex) {
            $this->log($ex);
        }
    }

    public function multiQuery(string $sql): bool
    {
        return mysqli_multi_query($this->sConecta, $sql);
    }

    public function table(string $nome)
    {
        $this->sTabelas[] = $this->sPrefix . $nome;

        return $this;
    }

    public function where(string $nome, string $valor = '?')
    {
        $txtAspas = "'";
        $txtIgual = '=';

        if ($this->sSemAspas || is_int($valor) || $valor == '?') {
            $txtAspas = "";
        }

        if ($this->sSemIgual) {
            $txtIgual = '';
        }

        if (empty($this->sWhere)) {
            $this->sWhere[] = $nome . $txtIgual . $txtAspas . $valor . $txtAspas;
        } else {
            $this->sWhere[] = $this->sAndOr . $nome . $txtIgual . $txtAspas . $valor . $txtAspas;
        }

        $this->sAndOr = ' AND ';
        $this->sSemAspas = false;
        $this->sSemIgual = false;

        return $this;
    }

    public function semAspas()
    {
        $this->sSemAspas = true;
        return $this;
    }

    public function semIgual()
    {
        $this->sSemIgual = true;
        return $this;
    }

    public function and()
    {
        $this->sAndOr = ' AND ';
        return $this;
    }

    public function or()
    {
        $this->sAndOr = ' OR ';
        return $this;
    }

    public function andNot()
    {
        $this->sAndOr = ' AND NOT ';
        return $this;
    }

    public function orNot()
    {
        $this->sAndOr = ' OR NOT ';
        return $this;
    }

    public function whereCustom(string $sql)
    {
        $this->sWhere[] = $sql;
        return $this;
    }

    public function orderby(string $nome, bool $crescente = true)
    {
        if ($crescente) {
            $this->sOrderBy[] = $nome . ' ASC';
        } else {
            $this->sOrderBy[] = $nome . ' DESC';
        }
        return $this;
    }

    public function limit(int $page, int $registermax)
    {
        $this->sLimit = sprintf('%s,%s', $page, $registermax);
        return $this;
    }

    public function prepared(string $valor, string $tipo = 's')
    {
        $this->sPreparado[] = [$tipo, $valor];
        return $this;
    }

    protected function getTable()
    {
        if (count($this->sTabelas) > 1) {
            return implode('', $this->sTabelas);
        } else {
            return $this->sTabelas[0];
        }
    }

    protected function getWhere()
    {
        $txt = '';
        if (!empty($this->sWhere)) {
            $txt = ' WHERE ' . implode('', $this->sWhere);

            $txt = rtrim($txt, ' AND ');
            $txt = rtrim($txt, ' OR ');
            $txt = rtrim($txt, ' ');
        }

        return $txt;
    }

    protected function getOrderBy()
    {
        if (!empty($this->sOrderBy)) {
            return ' ORDER BY ' . implode(', ', $this->sOrderBy);
        } else {
            return '';
        }
    }

    protected function getLimit()
    {
        if (!empty($this->sLimit)) {
            return ' LIMIT ' . $this->sLimit;
        } else {
            return '';
        }
    }

    protected function log(mixed $ex)
    {
        if ($this->sSandbox) {
            echo $ex->getMessage();
        } else {
            if (!file_exists(dirname(__FILE__, 4) . '/logs/')) {
                mkdir(dirname(__FILE__, 4) . '/logs/', 0755, true);
            }

            file_put_contents(dirname(__FILE__, 4) . '/logs/midb_log', $ex->__toString(), FILE_APPEND);
        }
    }

    public function close()
    {
        if (empty($this->sPreparado)) {
            if ($this->sFechaResult) {
                mysqli_free_result($this->sResult);
            }
        } else {
            $this->sQuery = null;
            mysqli_stmt_free_result($this->sResult);
        }

        mysqli_close($this->sConecta);
    }
}
