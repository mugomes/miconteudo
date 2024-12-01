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

function checkData(string $valor, bool $usuario = true): bool
{
    global $dbBlogs1, $idsite, $sID;

    if (empty($sID)) {
        $txt = false;

        $db1 = new select($dbBlogs1);
        $db1->table('users')->where('idsite', $idsite);
        if ($usuario) {
            $db1->where('usuario')->prepared($valor);
        } else {
            $db1->where('email')->prepared($valor);
        }
        $db1->select();
        $db1->getResult();
        if ($db1->count() > 0) {
            $txt = true;
        }
        $db1->close();
    } else {
        $txt = false;
    }

    return $txt;
}
if (requestPOST()) {
    if (emptyPOST('txtUsuario')) {
        windowAlert('Digite um nome de usuário para acessar o site administrativo.');
    } elseif (emptyPOST('txtEmail')) {
        windowAlert('Digite um e-mail para recuperar a conta.');
    } else {
        $sNome = CleanPOST('txtNome');
        $sUsuario = CleanPOST('txtUsuario');
        if (empty($sUsuario)) {
            $sUsuario = str_replace('-', '', gerarLink($sNome));
        }
        if (checkData($sUsuario) == false) {
            $sEmail = CleanPOST('txtEmail');
            if (checkData($sEmail, false) == false) {
                $sSenha = password_hash(CleanPOST('txtSenha'), PASSWORD_DEFAULT);

                $sPermissao = serialize([
                    'nivel' => CleanPOST('txtNivel')
                ]);

                if (empty($sID)) {
                    $sDataPublicado = date('Y-m-d H:i:s');

                    if (checkNivel() == 'superadmin') {
                        $db1 = new insert($dbBlogs1);
                        $db1->table('users')
                            ->add('idsite', $idsite)
                            ->add('nome')->prepared($sNome)
                            ->add('email')->prepared($sEmail)
                            ->add('usuario')->prepared($sUsuario)
                            ->add('senha')->prepared($sSenha)
                            ->add('permissao')->prepared($sPermissao)
                            ->add('datapublicado')->prepared($sDataPublicado)
                            ->insert();
                        $sID = $db1->idinsert();
                        $db1->close();
                    } else {
                        windowAlert('Sem permissão!');
                    }
                    redirect('list.php');
                } else {
                    $sDataAlterado = date('Y-m-d H:i:s');

                    if (checkNivel() == 'superadmin') {
                        $db1 = new update($dbBlogs1);
                        $db1->table('users')
                            ->add('nome')->prepared($sNome)
                            ->add('email')->prepared($sEmail)
                            ->add('usuario')->prepared($sUsuario)
                            ->add('senha')->prepared($sSenha)
                            ->add('permissao')->prepared($sPermissao)
                            ->add('dataalterado')->prepared($sDataAlterado)
                            ->where('id')->prepared($sID)
                            ->where('idsite', $idsite)
                            ->update();
                        $db1->close();
                    } else {
                        $db1 = new update($dbBlogs1);
                        $db1->table('users')
                            ->add('nome')->prepared($sNome)
                            ->add('email')->prepared($sEmail)
                            ->add('usuario')->prepared($sUsuario)
                            ->add('senha')->prepared($sSenha)
                            ->add('permissao')->prepared($sPermissao)
                            ->add('dataalterado')->prepared($sDataAlterado)
                            ->where('id')->prepared($sID)
                            ->where('idsite', $idsite)
                            ->where('idtoken1')->prepared($vetor['strToken1'])
                            ->update();
                        $db1->close();
                    }

                    redirect('list.php');
                }
            } else {
                windowAlert('Esse e-mail já está cadastrado!');
            }
        } else {
            windowAlert('Esse usuário já está cadastrado!');
        }
    }
}

$txtNome = '';
$txtUsuario = '';
$txtEmail = '';
$txtNivel = 'superadmin';

if (!empty($sID)) {
    $db1 = new select($dbBlogs1);
    $db1->table('users')
        ->where('id')->prepared($sID)
        ->where('idsite', $idsite)
        ->select();
    $db1->getResult();
    while ($row = $db1->fetch()) {
        $db1->rows($row);
        $txtNome = $db1->row('nome');
        $txtUsuario = $db1->row('usuario');
        $txtEmail = $db1->row('email');
        $aPermissao = unserialize($db1->row('permissao'));
        $txtNivel = $aPermissao['nivel'];
    }
    $db1->close();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Usuário | MiConteudo</title>

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
                                    <a href="/adm/dashboard/users/list.php" class="btn btn-success">Lista de Usuários</a>
                                    <hr>
                                    <h3 class="card-title">Editar Usuário</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form id="quickForm" name="frmPage" method="post" action="?id=<?php echo $sID; ?>">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtNome">Nome</label>
                                            <input id="txtNome" name="txtNome" type="text" value="<?php echo $txtNome; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtEmail">E-mail</label>
                                            <input id="txtEmail" name="txtEmail" type="email" value="<?php echo $txtEmail; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtUsuario">Usuário</label>
                                            <input id="txtUsuario" name="txtUsuario" type="text" value="<?php echo $txtUsuario; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtSenha">Senha</label>
                                            <input id="txtSenha" name="txtSenha" type="password" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h3>Permissão</h3>
                                        <div class="form-group">
                                            <label for="txtNivel">Nível</label>
                                            <select id="txtNivel" name="txtNivel" class="form-control">
                                                <option value="admin" <?php echo ($txtNivel == 'admin') ? 'selected="selected"' : ''; ?>>Admin</option>
                                                <option value="editor" <?php echo ($txtNivel == 'editor') ? 'selected="selected"' : ''; ?>>Editor</option>
                                            </select>
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