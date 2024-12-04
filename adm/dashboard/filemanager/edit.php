<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

use MiConteudo\database\insert;

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

function convertToWebP(string $source, string $destination, int $quality = 80)
{
    // Verifica o tipo de imagem
    $info = getimagesize($source);
    $mime = $info['mime'];

    if ($mime == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($mime == 'image/png') {
        $image = imagecreatefrompng($source);
        // Define fundo transparente para PNG
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    // Cria a imagem WebP
    if (isset($image)) {
        imagewebp($image, $destination, $quality);
    }

    imagedestroy($image);

    if (file_exists($source)) {
        unlink($source);
    }
}

if (requestPOST()) {
    if (!empty($_FILES)) {
        if (is_array($_FILES)) {
            $sFiles = $_FILES;

            $iTotalFiles = count($sFiles['txtArquivos']['name']);
            foreach ($_FILES as $row) {
                for ($i = 0; $i < $iTotalFiles; $i++) {
                    $sTipoFile1 = $row['type'][$i];
                    $sTipoFile2 = pathinfo($row['name'][$i], PATHINFO_EXTENSION);
                    if (
                        strposa($sTipoFile1, array('image/png', 'image/jpeg', 'image/gif', 'image/webp', 'application/zip', 'application/pdf', 'text/html', 'text/xml', 'text/plain', 'application/vnd.debian.binary-package')) &&
                        strposa($sTipoFile2, array('png', 'jpg', 'jpeg', 'gif', 'webp', 'zip', 'pdf', 'html', 'xml', 'txt', 'deb'))
                    ) {
                        $arquivo = documentroot() . '/sites/' . $idsite . '/files/' . pathinfo($row['name'][$i], PATHINFO_BASENAME);

                        if (file_exists($arquivo)) {
                            $arquivo = documentroot() . '/sites/' . $idsite . '/files/' . pathinfo($row['name'][$i], PATHINFO_FILENAME) . '-' . rand(10000, 99999) . '.' . $sTipoFile2;
                        }

                        if (!file_exists($arquivo)) {
                            $sTargetFile = $arquivo;

                            $sNameFile = pathinfo($sTargetFile, PATHINFO_BASENAME);

                            move_uploaded_file($row['tmp_name'][$i], $sTargetFile);

                            if (strposa($sTipoFile1, array('image/png', 'image/jpeg'))) {
                                $sFileNewWebP = $sTargetFile;

                                $sNameFile = rtrim($sNameFile, '.jpg');
                                $sNameFile = rtrim($sNameFile, '.jpeg');
                                $sNameFile = rtrim($sNameFile, '.png');
                                $sNameFile = $sNameFile . '.webp';

                                $sFileNewWebP = rtrim($sFileNewWebP, '.jpg');
                                $sFileNewWebP = rtrim($sFileNewWebP, '.jpeg');
                                $sFileNewWebP = rtrim($sFileNewWebP, '.png');
                                $sFileNewWebP = $sFileNewWebP . '.webp';

                                convertToWebP($sTargetFile, $sFileNewWebP);

                                $sTargetFile = $sFileNewWebP;
                            }

                            $txtDataPublicado = date('Y-m-d H:i:s');

                            $db1 = new insert($dbBlogs1);
                            $db1->table('files')
                                ->add('idsite', $idsite)
                                ->add('nome')->prepared($sNameFile)
                                ->add('link')->prepared(str_replace(documentroot(), '', $sTargetFile))
                                ->add('datapublicado')->prepared($txtDataPublicado)
                                ->insert();
                            $db1->close();
                        } else {
                            windowAlert('Arquivo existente!');
                        }
                    }
                }
            }
        }
    }

    redirect(servername() . '/adm/dashboard/filemanager/list.php', ['tipo' => CleanGET('tipo')]);
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Adicionar Arquivo | MiConteudo</title>

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
                                    <a href="/adm/dashboard/filemanager/list.php?tipo=<?php echo CleanGET('tipo'); ?>" class="btn btn-success">Gerenciador de Arquivos</a>
                                    <hr>
                                    <h3 class="card-title">Adicionar Arquivo</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form name="frmEdit" method="post" action="?tipo=<?php echo CleanGET('tipo'); ?>" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtArquivos">Arquivos</label>
                                            <input id="txtArquivos" name="txtArquivos[]" type="file" multiple="multiple" class="form-control" required>
                                            <br><span>Formatos Permitidos: JPG, PNG, GIF, WEBP, PDF, ZIP, DEB, HTML, XML, TXT</span>
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
            $('#quickForm').validate({
                rules: {
                    txtTitulo: {
                        required: true,
                        minlength: 5
                    }
                },
                messages: {
                    txtTitulo: {
                        required: "Digite um título para a página",
                        minlength: "Digite pelo menos 5 caracteres"
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
            $('#txtImagem1').val(file);
        }
    </script>
</body>

</html>