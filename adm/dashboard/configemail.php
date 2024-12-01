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
    $sSMTPEmail = serialize([
        'servidor' => CleanPOST('txtSMTPServidor'),
        'porta' => CleanPost('txtSMTPPorta'),
        'email' => CleanPOST('txtSMTPEmail'),
        'senha' => cript(CleanPOST('txtSMTPSenha'), 'GXQe*CsWhLglqeLF@u3kXP75'),
    ]);
    
    $sDataAlterado = date('Y-m-d H:i:s');

    if (checkNivel() == 'superadmin') {
        $db1 = new update($dbBlogs1);
        $db1->table('options')
            ->add('configemail')->prepared($sSMTPEmail)
            ->add('dataalterado')->prepared($sDataAlterado)
            ->where('idsite', $idsite)
            ->update();
        $db1->close();
    }

    redirect('configemail.php');
}

$txtSMTPEmail = ['servidor' => '', 'porta' => '', 'email' => ''];

$db1 = new select($dbBlogs1);
$db1->table('options')
    ->where('idsite', $idsite)
    ->limit(0, 1)
    ->select();
while ($row = $db1->fetch()) {
    $db1->rows($row);

    $txtSMTPEmail = empty($db1->row('configemail')) ? ['servidor' => '', 'porta' => '', 'email' => ''] : unserialize($db1->row('configemail'));
}
$db1->close();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuração de E-mail | MiConteudo</title>

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
                                <form id="quickForm" name="frmPage" method="post" action="configemail.php">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtSMTPServidor">Servidor</label>
                                            <input id="txtSMTPServidor" name="txtSMTPServidor" type="text" value="<?php echo empty($txtSMTPEmail['servidor']) ? 'localhost' : $txtSMTPEmail['servidor']; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtSMTPPorta">Porta</label>
                                            <input id="txtSMTPPorta" name="txtSMTPPorta" type="number" value="<?php echo empty($txtSMTPEmail['porta']) ? '587' : $txtSMTPEmail['porta']; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtSMTPEmail">E-mail</label>
                                            <input id="txtSMTPEmail" name="txtSMTPEmail" type="email" value="<?php echo $txtSMTPEmail['email']; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="txtSMTPSenha">Senha</label>
                                            <input id="txtSMTPSenha" name="txtSMTPSenha" type="password" class="form-control">
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
</body>

</html>
