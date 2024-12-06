<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

namespace MiConteudo\database;

class table extends database
{
    private array $sCreateColumns = [];
    private array $sAlterColumns = [];
    private array $sDropColumns = [];

    private bool $ctInt = false;
    private bool $ctLongText = false;
    private bool $ctNull = false;
    private bool $ctAutoIncrement = false;
    private string $ctTamanho = '45';
    private string $ciTamanho = '11';
    private string $ctDefaultValue = '';
    private string $ctAfter = '';

    private function clean()
    {
        $this->ctInt = false;
        $this->ctNull = false;
        $this->ctAutoIncrement = false;
        $this->ctTamanho = '45';
        $this->ciTamanho = '11';
        $this->ctDefaultValue = '';
        $this->ctAfter = '';
    }

    public function int()
    {
        $this->ctInt = true;
    }

    public function longText()
    {
        $this->ctLongText = true;
    }

    public function null()
    {
        $this->ctNull = true;
    }

    public function autoIncrement()
    {
        $this->ctAutoIncrement = true;
    }

    public function varcharTamanho(int $value = 45)
    {
        $this->ctTamanho = $value;
    }

    public function intTamanho(int $value = 11)
    {
        $this->ciTamanho = $value;
    }

    public function defaultValue(string $value)
    {
        $this->ctDefaultValue = $value;
    }

    public function after(string $value)
    {
        $this->ctAfter = $value;
    }

    public function add(string $nome)
    {
        $sql = $nome;
        if ($this->ctLongText) {
            $sql .= ' LONGTEXT';
        } else {
            $sql .= ($this->ctInt) ? ' int(' . $this->ciTamanho . ')' : ' varchar(' . $this->ctTamanho . ')';
        }
        if (empty($this->ctDefaultValue)) {
            $sql .= ($this->ctNull) ? ' DEFAULT NULL' : ' NOT NULL';
        } else {
            $sql .= ' DEFAULT ' . $this->ctDefaultValue . ' NULL';
        }

        if ($this->ctAutoIncrement) {
            $sql .= ' AUTO_INCREMENT';
        }

        if (!empty($this->ctAfter)) {
            $sql .= ' AFTER ' . $this->ctAfter;
        }

        $this->sCreateColumns[] = $sql;
        return $this;
    }

    public function create()
    {
        try {
            $colunas = '';
            foreach ($this->sCreateColumns as $value) {
                $colunas .= $value . ',';
            }

            $colunas = rtrim($colunas, ',');

            $txt = sprintf('CREATE TABLE IF NOT EXISTS %s (%s) ENGINE=MyISAM DEFAULT CHARSET=%s COLLATE=%s_general_ci;', $this->getTable(), $colunas, $this->sCharset, $this->sCharset);

            mysqli_query($this->sConecta, $txt);

            $this->sFechaResult = false;
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }

    const ALTER_ADD = 'add';
    const ALTER_MODIFY = 'modify';
    public function alter(string $tipo = 'add')
    {
        try {
            $colunas = '';
            foreach ($this->sCreateColumns as $value) {
                $colunas .= $value . ',';
            }

            $colunas = rtrim($colunas, ',');

            if ($tipo == 'add') {
                $txt = sprintf('ALTER TABLE %s ADD COLUMN %s', $this->getTable(), $colunas);
            } elseif ($tipo == 'modify') {
                $txt = sprintf('ALTER TABLE %s MODIFY %s', $this->getTable(), $colunas);
            }

            mysqli_query($this->sConecta, $txt);

            $this->sFechaResult = false;
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }

    public function primaryKey(string $name)
    {
        try {
            mysqli_query($this->sConecta, 'ALTER TABLE ' . $this->getTable() . ' ADD PRIMARY KEY (' . $name . ');');
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }
}
