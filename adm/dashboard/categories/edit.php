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
if (requestPOST()) {
    if (emptyPOST('txtTitulo')) {
        windowAlert('Digite um título para sua categoria.');
    } else {
        $sTitulo = CleanPOST('txtTitulo');
        $sPalavrasChave = CleanPOST('txtPalavrasChave');
        $sDescricaoCurta = CleanPOST('txtDescricaoCurta');
        $sResumo = CleanPOST('txtResumo');
        $sImagens = CleanPOST('txtImagens');
        $sLink = CleanPOST('txtLink');
        if (empty($sLink)) {
            $sLink = strtolower(gerarLink($sTitulo));
        }
        $sRascunho = CleanPOST('txtRascunho');
        if (empty($sID)) {
            $sDataPublicado = date('Y-m-d H:i:s');

            $db1 = new insert($dbBlogs1);
            $db1->table('categorias')
                ->add('idsite', $idsite)
                ->add('titulo')->prepared($sTitulo)
                ->add('palavraschave')->prepared($sPalavrasChave)
                ->add('descricaocurta')->prepared($sDescricaoCurta)
                ->add('resumo')->prepared($sResumo)
                ->add('imagens')->prepared($sImagens)
                ->add('link')->prepared($sLink)
                ->add('rascunho')->prepared($sRascunho)
                ->add('datapublicado')->prepared($sDataPublicado)
                ->add('dataalterado')->prepared($sDataPublicado)
                ->insert();
            $sID = $db1->idinsert();
            $db1->close();
        } else {
            $sDataAlterado = date('Y-m-d H:i:s');

            $db1 = new update($dbBlogs1);
            $db1->table('categorias')
                ->add('titulo')->prepared($sTitulo)
                ->add('palavraschave')->prepared($sPalavrasChave)
                ->add('descricaocurta')->prepared($sDescricaoCurta)
                ->add('resumo')->prepared($sResumo)
                ->add('imagens')->prepared($sImagens)
                ->add('link')->prepared($sLink)
                ->add('rascunho')->prepared($sRascunho)
                ->add('dataalterado')->prepared($sDataAlterado)
                ->where('id')->prepared($sID)
                ->where('idsite', $idsite)
                ->update();
            $db1->close();
        }

        redirect('list.php');
    }
}

$txtTitulo = '';
$txtPalavrasChave = '';
$txtDescricaoCurta = '';
$txtResumo = '';
$txtImagens = '';
$txtLink = '';
$txtRascunho = '';

if (!empty($sID)) {
    $db1 = new select($dbBlogs1);
    $db1->table('categorias')
        ->where('id')->prepared($sID)
        ->where('idsite', $idsite)
        ->select();
    $db1->getResult();
    while ($row = $db1->fetch()) {
        $db1->rows($row);
        $txtTitulo = $db1->row('titulo');
        $txtPalavrasChave = $db1->row('palavraschave');
        $txtDescricaoCurta = $db1->row('descricaocurta');
        $txtResumo = $db1->row('resumo');
        $txtImagens = $db1->row('imagens');
        $txtLink = $db1->row('link');
        $txtRascunho = $db1->row('rascunho');
    }
    $db1->close();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Categoria | MiConteudo</title>

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
                                    <a href="/adm/dashboard/categories/list.php" class="btn btn-success">Lista de Categorias</a>
                                    <hr>
                                    <h3 class="card-title">Editar Categoria</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form id="quickForm" name="frmPage" method="post" action="?id=<?php echo $sID; ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtRascunho">Visibilidade</label>
                                            <select id="txtRascunho" name="txtRascunho" class="form-control">
                                                <option value="2" <?php echo ($txtRascunho == 2) ? 'selected="selected"' : ''; ?>>Publicar</option>
                                                <option value="1" <?php echo ($txtRascunho == 1) ? 'selected="selected"' : ''; ?>>Rascunho</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtTitulo">Título</label>
                                            <input id="txtTitulo" name="txtTitulo" type="text" value="<?php echo $txtTitulo; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtLink">Link</label>
                                            <input id="txtLink" name="txtLink" type="text" value="<?php echo $txtLink; ?>" class="form-control">
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
                                            <label for="txtResumo">Resumo</label>
                                            <input id="txtResumo" name="txtResumo" type="text" value="<?php echo $txtResumo; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtImagens">Imagem (Rede Social)</label><br>
                                            <input id="txtImagens" name="txtImagens" type="hidden" value="<?php echo $txtImagens; ?>" class="form-control">
                                            <img id="imagemShow" src="<?php echo $txtImagens; ?>" align="left" style="width:200px;height:200px;" /><br><br><br>
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
        <?php include_once(dirname(__FILE__, 2) . '/controls/rodape.php'); ?>
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
            $('#txtImagens').val(file);
        }

        function removeImage() {
            $('#imagemShow').attr('src', '');
            $('#txtImagens').val('');
        }
    </script>
</body>

</html>