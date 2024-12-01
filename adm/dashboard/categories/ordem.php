<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

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

function categorias(int $parent, int $level = 1)
{
    global $dbBlogs1, $idsite;

    $db1 = new select($dbBlogs1);
    $db1->table('categorias')
        ->where('idsite', $idsite)
        ->where('idcategoria', $parent)
        ->orderby('ordem')
        ->select();

    $txt = '';
    while ($row = $db1->fetch()) {
        $db1->rows($row);
        $txt .= '<li class="dd-item dd3-item" data-id="' . $db1->row('id') . '">';
        $txt .= '<div class="dd-handle dd3-handle"></div>';
        $txt .= '<div class="dd3-content">';
        $txt .= $db1->row('titulo');
        $txt .= '</div>';

        $menu = categorias($db1->row('id'), $level + 1);

        if (!empty($menu)) {
            $txt .= '<ol class="dd-list">' . $menu . '</ol>';
        }

        $txt .= '</li>';
    }

    $db1->close();

    return $txt;
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Categorias | MiConteudo</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adm/themes/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/adm/themes/dist/css/adminlte.min.css">

    <link rel="stylesheet" href="/adm/plugins/nestable/nestable.css" media="all">
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
                                    <a href="/adm/dashboard/categories/list.php" class="btn btn-success">Lista de Categorias</a>
                                    <hr>
                                    <h3 class="card-title">Organizar Categorias</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="cf nestable-lists" style="border-top-style:none;border-bottom-style:none;">
                                        <div class="dd" id="nestable3">
                                            <ol class="dd-list">
                                                <?php echo categorias(0); ?>
                                            </ol>
                                        </div>
                                    </div>
                                    <div id="txtResultado"></div>
                                    <textarea id="txtLista" style="display:none;"></textarea>
                                </div>
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

    <script src="/adm/plugins/nestable/jquery.nestable.js"></script>
    <script>
        $(document).ready(function() {
            var updateOutput = function(e) {
                var list = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable('serialize'))); //, null, 2));
                    $.post('/adm/dashboard/categories/saveordem.php', {
                            'output': output.val()
                        },
                        function(data) {
                            $('#txtResultado').html(data);
                        }
                    );
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };

            $('#nestable-menu').on('click', function(e) {
                var target = $(e.target),
                    action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            });

            $('#nestable3').nestable();

            $('#nestable3').nestable({
                    group: 1
                })
                .on('change', updateOutput);

            updateOutput($('#nestable3').data('output', $('#txtLista')));

        });
    </script>
</body>

</html>