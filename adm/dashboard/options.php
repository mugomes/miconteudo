<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\insert;
use MiConteudo\database\select;
use MiConteudo\database\update;

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

include_once(dirname(__FILE__, 3) . '/controls/functions.php');
include_once(documentroot() . '/classes/vendor/autoload.php');
include_once(documentroot() . '/core/config.php');

if (isset($sandbox)) {
    error_reporting(E_ALL);

    /* Habilita a exibição de erros */
    ini_set("display_errors", 1);
}


include_once(dirname(__FILE__) . '/controls/checkadm.php');

function checkNivel(): string
{
    global $dbBlogs1, $idsite, $vetor;

    $txt = 'editor';

    $db1 = new select($dbBlogs1);
    $db1->table('users')
        ->where('idsite', $idsite)
        ->where('idtoken1')->prepared($vetor['strToken1'])
        ->select();
    $db1->getResult();

    while ($row = $db1->fetch()) {
        $db1->rows($row);
        $aPermissao = unserialize($db1->row('permissao'));
        $txt = $aPermissao['nivel'];
    }
    $db1->close();

    return $txt;
}

if (requestPOST()) {
    $sTitulo = CleanPOST('txtTitulo');
    $sPalavrasChave = CleanPOST('txtPalavrasChave');
    $sDescricaoCurta = CleanPOST('txtDescricaoCurta');
    $sImagens = serialize(['principal' => CleanPOST('txtImagemPrincipal')]);
    $sEstatisticas = serialize(['googleanalytics' => CleanPOST('txtGoogleAnalytics')]);
    $sOutrasMetaTags1 = addslashes(CleanPOST('txtOutrasMetaTags1'));
    $sOutrasMetaTags2 = addslashes(CleanPOST('txtOutrasMetaTags2'));
    $sDataAlterado = date('Y-m-d H:i:s');

    if (checkNivel() == 'superadmin') {
        $db1 = new update($dbBlogs1);
        $db1->table('options')
            ->add('titulo')->prepared($sTitulo)
            ->add('palavraschave')->prepared($sPalavrasChave)
            ->add('descricaocurta')->prepared($sDescricaoCurta)
            ->add('imagens')->prepared($sImagens)
            ->add('estatisticas')->prepared($sEstatisticas)
            ->add('outrasmetatags1')->prepared($sOutrasMetaTags1)
            ->add('outrasmetatags2')->prepared($sOutrasMetaTags2)
            ->add('dataalterado')->prepared($sDataAlterado)
            ->where('idsite', $idsite)
            ->update();
        $db1->close();
    }

    redirect('options.php');
}

$txtTitulo = '';
$txtPalavrasChave = '';
$txtDescricaoCurta = '';
$txtImagemPrincipal = '';
$txtGoogleAnalytics = '';
$txtOutrasMetaTags1 = '';
$txtOutrasMetaTags2 = '';

$db1 = new select($dbBlogs1);
$db1->table('options')
    ->where('idsite', $idsite)
    ->limit(0, 1)
    ->select();
while ($row = $db1->fetch()) {
    $db1->rows($row);
    $txtTitulo = $db1->row('titulo');
    $txtPalavrasChave = $db1->row('palavraschave');
    $txtDescricaoCurta = $db1->row('descricaocurta');

    $aImagens = unserialize($db1->row('imagens'));
    $txtImagemPrincipal = empty($aImagens['principal']) ? '' : $aImagens['principal'];

    $aEstatisticas = unserialize($db1->row('estatisticas'));
    $txtGoogleAnalytics = empty($aEstatisticas['googleanalytics']) ? '' : $aEstatisticas['googleanalytics'];

    $txtOutrasMetaTags1 = stripslashes($db1->row('outrasmetatags1'));
    $txtOutrasMetaTags2 = stripslashes($db1->row('outrasmetatags2'));
}
$db1->close();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Opções | MiConteudo</title>

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
        <?php include_once(dirname(__FILE__) . '/controls/menutopo.php'); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include_once(dirname(__FILE__) . '/controls/menulateral.php'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Editar Opções</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form id="quickForm" name="frmPage" method="post" action="options.php">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtTitulo">Título</label>
                                            <input id="txtTitulo" name="txtTitulo" type="text" value="<?php echo $txtTitulo; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtPalavrasChave">Palavras Chave</label>
                                            <input id="txtPalavrasChave" name="txtPalavrasChave" type="text" value="<?php echo $txtPalavrasChave; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtDescricaoCurta">Descrição Curta</label>
                                            <input id="txtDescricaoCurta" name="txtDescricaoCurta" type="text" value="<?php echo $txtDescricaoCurta; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtGoogleAnalytics">Google Analytics</label>
                                            <input id="txtGoogleAnalytics" name="txtGoogleAnalytics" type="text" value="<?php echo $txtGoogleAnalytics; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtOutrasMetaTags1">MetaTags (Head)</label>
                                            <textarea id="txtOutrasMetaTags1" name="txtOutrasMetaTags1" class="form-control"><?php echo $txtOutrasMetaTags1; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtOutrasMetaTags2">MetaTags (Body)</label>
                                            <textarea id="txtOutrasMetaTags2" name="txtOutrasMetaTags2" class="form-control"><?php echo $txtOutrasMetaTags2; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3>Imagens</h3>
                                        <div class="form-group">
                                            <label for="txtImagemPrincipal">Imagem (Rede Social)</label><br>
                                            <input id="txtImagemPrincipal" name="txtImagemPrincipal" type="hidden" value="<?php echo $txtImagemPrincipal; ?>" class="form-control">
                                            <img id="imagemShow" src="<?php echo $txtImagemPrincipal; ?>" align="left" style="width:200px;height:200px;" /><br><br><br>
                                            <button type="button" onclick="openWindow()" class="btn btn-default">Selecionar Imagem</button><br>
                                            <button type="button" onclick="removeImage()" class="btn btn-default">Remover Imagem</button>
                                        </div>
                                    </div>

                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Continuar</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include_once(dirname(__FILE__) . '/controls/rodape.php'); ?>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="/adm/themes/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/adm/themes/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/adm/themes/dist/js/adminlte.min.js"></script>

    <!-- jquery-validation -->
    <script src="/adm/themes/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="/adm/themes/plugins/jquery-validation/additional-methods.min.js"></script>

    <script>
        $(function() {
            // $.validator.setDefaults({
            //     submitHandler: function() {
            //         alert("Form successful submitted!");
            //     }
            // });
            $('#quickForm').validate({
                rules: {
                    txtTitulo: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    txtTitulo: {
                        required: "Digite um título para a página",
                        minlength: "Digite pelo menos 3 caracteres"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        function openWindow() {
            window.open('/adm/dashboard/filemanager/list.php?tipo=textbox', 'parent', 'width=500,height=500');
        }

        function processFile(file) {
            $('#imagemShow').attr('src', file);
            $('#txtImagemPrincipal').val(file);
        }

        function removeImage() {
            $('#imagemShow').attr('src', '');
            $('#txtImagemPrincipal').val('');
        }
    </script>
</body>

</html>