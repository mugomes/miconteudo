<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\table;

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

// Criar uma forma de verificar atualizações dos arquivos

$db1 = new table($dbBlogs1);

// DB: Categorias
if (!$db1->table('categorias')->columnExists('idioma')) {
    $db1->varcharTamanho(5)->null()->after('ordem')->add('idioma')->alter();
}

// DB: Menu Topo
if (!$db1->table('menutopo')->columnExists('idioma')) {
    $db1->varcharTamanho(5)->null()->after('estilo')->add('idioma')->alter();
}

// DB: Paginas
if (!$db1->table('pages')->columnExists('idioma')) {
    $db1->varcharTamanho(5)->null()->after('rascunho')->add('idioma')->alter();
}

// DB: Posts
if (!$db1->table('posts')->columnExists('idioma')) {
    $db1->varcharTamanho(5)->null()->after('rascunho')->add('idioma')->alter();
}

// DB: Rodapé
if (!$db1->table('rodape')->columnExists('idioma')) {
    $db1->varcharTamanho(5)->null()->after('estilos')->add('idioma')->alter();
}

// DB: Options
if (!$db1->table('options')->columnExists('idiomapadrao')) {
    $db1->varcharTamanho(5)->null()->after('configemail')->add('idiomapadrao')->alter();
}

if (!$db1->table('options')->columnExists('idioma')) {
    $db1->varcharTamanho(5)->null()->after('idiomapadrao')->add('idioma')->alter();
}

if (!$db1->table('options')->columnExists('versao')) {
    $db1->varcharTamanho(15)->null()->after('idioma')->add('versao')->alter();
}

if (!$db1->table('options')->columnExists('dataatualizado')) {
    $db1->varcharTamanho(19)->null()->after('versao')->add('dataatualizado')->alter();
}

if (!$db1->table('options')->columnExists('dataverificado')) {
    $db1->varcharTamanho(19)->null()->after('dataatualizado')->add('dataverificado')->alter();
}

$db1->close();

echo 'Tabelas adicionadas!';

?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Atualizações | MiConteudo</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adm/themes/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/adm/themes/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include_once(dirname(__FILE__, 2) . '/controls/menutopo.php'); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include_once(dirname(__FILE__, 2) . '/controls/menulateral.php'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Atualizações</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <a href=""></a>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include_once(dirname(__FILE__, 2) . '/controls/rodape.php'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="/adm/themes/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/adm/themes/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/adm/themes/dist/js/adminlte.min.js"></script>
</body>

</html>