<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

namespace MiConteudo\database;

class table extends database
{
    private array $sCreateColumns = [];

    private bool $ctInt = false;
    private bool $ctLongText = false;
    private bool $ctNull = false;
    private bool $ctAutoIncrement = false;
    private bool $ctPrimaryKey = false;
    private string $ctTamanho = '45';
    private string $ciTamanho = '11';
    private string $ctDefaultValue = '';
    private string $ctAfter = '';

    private function clean()
    {
        $this->ctInt = false;
        $this->ctLongText = false;
        $this->ctNull = false;
        $this->ctAutoIncrement = false;
        $this->ctPrimaryKey = false;
        $this->ctTamanho = '45';
        $this->ciTamanho = '11';
        $this->ctDefaultValue = '';
        $this->ctAfter = '';
    }

    private function cleanAll()
    {
        $this->clean();
        $this->sCreateColumns = [];
        $this->sTabelas = [];
    }

    public function int()
    {
        $this->ctInt = true;
        return $this;
    }

    public function longText()
    {
        $this->ctLongText = true;
        return $this;
    }

    public function null()
    {
        $this->ctNull = true;
        return $this;
    }

    public function autoIncrement()
    {
        $this->ctAutoIncrement = true;
        return $this;
    }

    public function primaryKey()
    {
        $this->ctPrimaryKey = true;
        return $this;
    }

    public function varcharTamanho(int $value = 45)
    {
        $this->ctTamanho = $value;
        return $this;
    }

    public function intTamanho(int $value = 11)
    {
        $this->ciTamanho = $value;
        return $this;
    }

    public function defaultValue(string $value)
    {
        $this->ctDefaultValue = $value;
        return $this;
    }

    public function after(string $value)
    {
        $this->ctAfter = $value;
        return $this;
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
            $sql .= ' DEFAULT ' . $this->ctDefaultValue . ' NOT NULL';
        }

        if ($this->ctAutoIncrement) {
            $sql .= ' AUTO_INCREMENT';
        }

        if ($this->ctPrimaryKey) {
            $sql .= ' PRIMARY KEY';
        }

        if (!empty($this->ctAfter)) {
            $sql .= ' AFTER ' . $this->ctAfter;
        }

        $this->sCreateColumns[] = $sql;
        $this->clean();
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
            $this->cleanAll();
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
            $this->cleanAll();
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }

    public function columnExists(string $coluna): bool
    {
        try {
            $txt = false;
            $sql = "SELECT COUNT(*) As count1 FROM information_schema.columns WHERE table_name = '" . $this->getTable() . "' AND column_name = '$coluna'";
            if ($this->sResult = mysqli_query($this->sConecta, $sql)) {
                $row = mysqli_fetch_array($this->sResult);
                if ($row['count1'] > 0) {
                    $txt = true;
                }
                mysqli_free_result($this->sResult);
            }
            return $txt;
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex->__toString());
        }
    }
}
