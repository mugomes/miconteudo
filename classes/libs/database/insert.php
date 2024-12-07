<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

namespace MiConteudo\database;

class insert extends database
{
    private array $sInsert = [];

    public function add(string $nome, string $valor = '?')
    {
        $this->sInsert[] = [
            'coluna' => $nome,
            'valor' => $valor
        ];
        return $this;
    }

    public function insert()
    {
        try {
            $colunas = '';
            $valores = '';
            foreach ($this->sInsert as $row) {
                $colunas .= $row['coluna'] . ',';
                if (empty($this->sPreparado)) {
                    $valores .= "'" . $row['valor'] . "',";
                } else {
                    $valores .= $row['valor'] . ',';
                }
            }

            $colunas = rtrim($colunas, ',');
            $valores = rtrim($valores, ',');

            $txt = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->getTable(), $colunas, $valores);

            if (empty($this->sPreparado)) {
                $this->sResult = mysqli_query($this->sConecta, $txt);
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

            $this->sFechaResult = false;
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex);
        }
    }

    public function idinsert() {
        return mysqli_insert_id($this->sConecta);
    }
}
