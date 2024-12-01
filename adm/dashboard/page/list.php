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

if (CleanGET('acao') == 'excluir') {
    if (!emptyGET('id')) {
        $db1 = new delete($dbBlogs1);
        $db1->table('pages')
            ->where('id')->prepared(CleanGET('id'))
            ->where('idsite', $idsite)
            ->delete();
        $db1->close();

        redirect('/adm/dashboard/page/list.php');
    }
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
                                    <a href="/adm/dashboard/page/edit.php" class="btn btn-success">Nova Página</a>
                                    <hr>
                                    <h3 class="card-title">Lista de Páginas</h3>

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
                                                <th>Título</th>
                                                <th>Link</th>
                                                <th>Visibilidade</th>
                                                <th>Publicado</th>
                                                <th>Alterado</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $db1 = new select($dbBlogs1);
                                            $db1->table('pages')
                                                ->where('idsite', $idsite)
                                                ->select();

                                            while ($row = $db1->fetch()) {
                                                $db1->rows($row);
                                            ?>
                                                <tr>
                                                    <td><a href="/adm/dashboard/page/edit.php?id=<?php echo $db1->row('id'); ?>"><?php echo $db1->row('titulo'); ?></a></td>
                                                    <td><?php echo $db1->row('link'); ?></td>
                                                    <td><?php echo ($db1->row('rascunho') == 1) ? 'Rascunho' : 'Publicado'; ?></td>
                                                    <td><?php echo changedate($db1->row('datapublicado'), 'Y-m-d H:i:s', 'd/m/Y H:i:s'); ?></td>
                                                    <td><?php echo changedate($db1->row('dataalterado'), 'Y-m-d H:i:s', 'd/m/Y H:i:s'); ?></td>
                                                    <td><a href="/adm/dashboard/page/editor.php?id=<?php echo $db1->row('id'); ?>" class="btn btn-success">Editor</a> <a href="#" onclick="excluirRegistro('<?php echo $db1->row('id'); ?>');" class="btn btn-danger">Excluir</a></td>
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
        function excluirRegistro(id) {
            if (confirm('Deseja realmente excluir esse registro?')) {
                window.location.assign('/adm/dashboard/page/list.php?acao=excluir&id=' + id);
            }
        }
    </script>
</body>

</html>