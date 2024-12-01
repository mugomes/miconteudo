<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\delete;
use MiConteudo\database\select;

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

$sID = CleanGET('id', FILTER_SANITIZE_NUMBER_INT);
if (!empty($sID)) {
    $txtFile = false;

    $db1 = new select($dbBlogs1);
    $db1->table('files')
        ->where('id')->prepared($sID)
        ->where('idsite', $idsite)
        ->select();

    $db1->getResult();
    $rows = $db1->fetch();
    if (!empty($rows)) {
        $db1->rows($rows);

        if (!empty($db1->row('link'))) {
            $txtFile = true;

            if (file_exists(documentroot() . $db1->row('link'))) {
                unlink(documentroot() . $db1->row('link'));
            }
        }
    }
    $db1->close();

    if ($txtFile) {
        $db2 = new delete($dbBlogs1);
        $db2->table('files')
            ->where('id')->prepared($sID)
            ->where('idsite', $idsite)
            ->delete();
        $db2->close();
    }
  
    redirect(servername() . '/adm/dashboard/filemanager/list.php', ['tipo' => CleanGET('tipo')]);
}

function vArquivos_onclick(string $link)
{
    if (!emptyGET('tipo')) {
        $onclick = ' onclick="btnFile(\'' . $link . '\')"';
    }

    return isset($onclick) ? $onclick : '';
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Páginas | MiConteudo</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adm/themes/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/adm/themes/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php if (emptyGET('tipo')) { ?>
            <!-- Navbar -->
            <?php include_once(dirname(__FILE__, 2) . '/controls/menutopo.php'); ?>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <?php include_once(dirname(__FILE__, 2) . '/controls/menulateral.php'); ?>
        <?php } ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <a href="/adm/dashboard/filemanager/edit.php?tipo=<?php echo CleanGET('tipo'); ?>" class="btn btn-success">Adicionar Arquivo</a>
                                    <hr>
                                    <h3 class="card-title">Lista de Arquivos</h3>

                                    <div class="card-tools">
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="text" name="table_search" class="form-control float-right" placeholder="Pesquisar">

                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Link</th>
                                                <th>Data</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $db1 = new select($dbBlogs1);
                                            $db1->table('files')
                                                ->where('idsite', $idsite)
                                                ->select();

                                            while ($row = $db1->fetch()) {
                                                $db1->rows($row);
                                            ?>
                                                <tr>
                                                    <td<?php echo vArquivos_onclick($db1->row('link')); ?>><?php echo $db1->row('nome'); ?></td>
                                                        <td><?php echo $db1->row('link'); ?></td>
                                                        <td><?php echo changedate($db1->row('datapublicado'), 'Y-m-d H:i:s', 'd/m/Y H:i:s'); ?></td>
                                                        <td><a href="?acao=excluir&id=<?php echo $db1->row('id'); ?>" class="btn btn-danger">Excluir</a></td>
                                                </tr>
                                            <?php }

                                            $db1->close();
                                            ?>
                                        </tbody>
                                    </table>
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
    <script>
        function btnFile(filename) {
            <?php if (CleanGET('tipo') == 'textbox') { ?>
                window.opener.processFile(filename);
            <?php } elseif (CleanGET('tipo') == 'editortext') { ?>
                window.parent.postMessage({
                    mceAction: 'customAction',
                    url: filename
                }, '*');
            <?php } elseif (CleanGET('tipo') == 'editor') { ?>
                // Define o arquivo selecionado na variável global
                window.opener.postMessage({
                    fileSelected: filename
                }, window.location.origin);
            <?php } ?>
            window.close();
        }
    </script>
</body>

</html>