<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

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

include_once(dirname(__FILE__, 2) . '/controls/functions.php');
include_once(documentroot() . '/classes/vendor/autoload.php');
include_once(documentroot() . '/core/config.php');

if (isset($sandbox)) {
    error_reporting(E_ALL);

    /* Habilita a exibição de erros */
    ini_set("display_errors", 1);
}

session_name('wqe7084qr4wr84qr0q4erq4r8r9qr');
session_start();

function gerarToken(): string
{
    global $dbBlogs1, $idsite;

    $sToken = GerarSenha(10, true, true, true);

    $db1 = new select($dbBlogs1);
    $db1->column('idtoken1')
        ->table('users')
        ->where('idsite', $idsite)
        ->where('idtoken1', $sToken)
        ->select();

    if ($db1->count() > 0) {
        $db1->close();

        gerarToken();
    } else {
        $db1->close();

        return $sToken;
    }
}

$errorLogin = true;
if (requestPOST()) {
    if (emptyGET('txtEnviar') && !empty($_SESSION['token'])) {
        if (CleanPOST('txtToken') == $_SESSION['token']) {
            $txtUsuario = CleanPOST('txtUsuario');
            $txtSenha = CleanPOST('txtSenha');

            if (empty($txtUsuario) && empty($txtSenha)) {
                redirect(servername() . '/adm/', ['msg' => urlencode('Preencha os campos de usuário e senha antes de continuar!')]);
            }

            $db1 = new select($dbBlogs1);
            $db1->table('users')
                ->where('idsite', $idsite)
                ->where('usuario')->prepared($txtUsuario)
                ->select();
            $db1->getResult();
            if ($db1->count() > 0) {
                $row = $db1->fetch();

                if (password_verify($txtSenha, $row['senha'])) {
                    $txtToken1 = gerarToken();
                    $txtToken2 = GerarSenha(20, true, true, true);

                    $txtID = $row['id'];

                    $errorLogin = false;
                } else {
                    $errorLogin = true;
                }
            }

            $db1->close();
        } else {
            $errorLogin = true;
        }
    } else {
        $errorLogin = true;
    }

    if ($errorLogin) {
        redirect(servername() . '/adm/', ['msg' => urlencode('Não foi possível encontrar sua conta, verifique se está digitando corretamente!')]);
    } else {
        if (!empty($txtID)) {
            $db2 = new update($dbBlogs1);
            $db2->table('users')
                ->add('idtoken1', $txtToken1)
                ->add('idtoken2', password_hash($txtToken2, PASSWORD_DEFAULT))
                ->where('id', $txtID)
                ->where('idsite', $idsite)
                ->update();

            $db2->close();

            setcookie('admInfo[strToken1]', $txtToken1, 0, '/adm/', getenv('SERVER_NAME'), false, true);
            setcookie('admInfo[strToken2]', $txtToken2, 0, '/adm/', getenv('SERVER_NAME'), false, true);

            redirect(servername() . '/adm/dashboard/dashboard.php');
        } else {
            redirect(servername() . '/adm/', ['error' => 1]);
        }
    }
}

$_SESSION['token'] = rand(10000, 99999);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site Administrativo | MiConteudo</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adm/themes/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="/adm/themes/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/adm/themes/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="/adm/">MiConteudo</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <?php if (!emptyGET('msg')) { ?>
                    <div class="alert alert-danger alert-dismissible">
                        <?php echo urldecode(CleanGET('msg')); ?>
                    </div>
                <?php } ?>
                <p class="login-box-msg">Digite suas informações login abaixo</p>

                <form action="/adm/" method="post">
                    <div class="input-group mb-3">
                        <input name="txtUsuario" type="text" class="form-control" placeholder="Usuário">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input name="txtSenha" type="password" class="form-control" placeholder="Senha">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <input id="txtEnviar" name="txtEnviar" type="hidden">
                            <input name="txtToken" type="hidden" value="<?php echo $_SESSION['token']; ?>">
                            <button type="submit" class="btn btn-primary btn-block">Acessar</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mb-1">
                    <a href="/adm/recuperarconta.php">Recuperar Conta</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="/adm/themes/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/adm/themes/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/adm/themes/dist/js/adminlte.min.js"></script>
</body>

</html>