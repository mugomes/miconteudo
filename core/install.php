<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

error_reporting(E_ALL);

/* Habilita a exibição de erros */
ini_set("display_errors", 1);

use MiConteudo\database\insert;

include_once(dirname(__FILE__, 2) . '/controls/functions.php');
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação | MiConteudo</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Estilização geral */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px;
        }

        h1 {
            color: #333;
        }

        ol {
            margin: 20px 0;
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
            font-size: 16px;
            color: #555;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Container do formulário */
        .form-container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        /* Título do formulário */
        form h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Estilização dos labels */
        form label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        /* Estilização dos inputs e textarea */
        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
        }

        /* Botão */
        form button,
        .button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover,
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php
    if (emptyGET('page')) {
    ?>
        <div class="container">
            <h1>Bem-vindo ao MiConteudo</h1><br>
            <h2>Etapas para Instalação do MiConteudo</h2><br>
            <p>Parabéns por escolher o MiConteudo! Para configurar o sistema, siga os passos abaixo:</p>

            <ol>
                <li><strong>Configuração do Banco de Dados:</strong>
                    <p>1.1. Crie um banco de dados no seu servidor MySQL/MariaDB.</p>
                    <p>1.2. Anote as credenciais de acesso (nome do banco, usuário, senha).</p>
                    <p>1.3. Na página de configuração do banco de dados, insira essas informações de acesso ao banco de dados.</p>
                </li>
                <li><strong>Configuração de Informação do Site:</strong>
                    <p>1.1. Preencha os campos obrigatórios, como nome do site e e-mail de contato.</p>
                </li>
            </ol>

            <p>Após completar essas etapas, você estará pronto para começar a usar o MiConteudo.</p>

            <a href="?page=database" class="btn">Ir para a Configuração do Banco de Dados</a>
        </div>
        <?php
    } else {
        $page = CleanGET('page', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($page == 'database') {
            if (requestPOST()) {
                $sServidor = CleanPOST('txtServidor');
                $sUsuario = CleanPOST('txtUsuario');
                $sSenha = CleanPOST('txtSenha');
                $sDB = CleanPOST('txtDB');
                $sPrefixo = CleanPOST('txtPrefixo');

                try {
                    $sSQL = trim(file_get_contents(dirname(__FILE__) . '/modeldb.sql'));
                    $sSQL = str_replace('{miprefixo}', $sPrefixo, $sSQL);

                    $conecta = mysqli_connect($sServidor, $sUsuario, $sSenha, $sDB);
                    if (mysqli_connect_errno()) {
                        echo 'Não foi possível se conectar com o banco de dados, verifique se está digitando corretamente!';
                    }
                    mysqli_set_charset($conecta, 'utf8mb4');

                    if (mysqli_multi_query($conecta, $sSQL)) {
                        $modelConfig = file_get_contents(dirname(__FILE__) . '/modelconfig.php');
                        $modelConfig = str_replace('{miservidor}', $sServidor, $modelConfig);
                        $modelConfig = str_replace('{miusuario}', $sUsuario, $modelConfig);
                        $modelConfig = str_replace('{misenha}', $sSenha, $modelConfig);
                        $modelConfig = str_replace('{midatabase}', $sDB, $modelConfig);
                        $modelConfig = str_replace('{miprefixo}', $sPrefixo, $modelConfig);

                        file_put_contents(dirname(__FILE__) . '/config.php', $modelConfig);
                        unlink(dirname(__FILE__) . '/modelconfig.php');
                        unlink(dirname(__FILE__) . '/modeldb.sql');
                        $redirect = true;
                    } else {
                        throw new Exception('Não foi possível criar as tabelas!');
                    }
                    mysqli_close($conecta);
                } catch (mysqli_sql_exception | Exception $ex) {
                    echo $ex->getMessage();
                }

                if (isset($redirect)) {
                    if ($redirect) {
                        
                        redirect('/', ['page' => 'info']);
                    }
                }
            }

            function gerarPrefixo(): string
            {
                $retorno = '';
                $caracteres = 'abcdefghijklmnopqrstuvwxyz';

                $len = strlen($caracteres);

                for ($n = 1; $n <= 5; $n++) {
                    $rand = mt_rand(1, $len);
                    $retorno .= $caracteres[$rand - 1];
                }

                return $retorno;
            }
        ?>
            <div class="form-container">
                <form action="/?page=database" method="POST">
                    <h1>Configuração do Banco de Dados</h1>
                    <label for="txtServidor">Servidor</label>
                    <input type="text" id="txtServidor" name="txtServidor" value="localhost" required>

                    <label for="txtUsuario">Usuário:</label>
                    <input type="text" id="txtUsuario" name="txtUsuario" required>

                    <label for="txtSenha">Senha:</label>
                    <input type="password" id="txtSenha" name="txtSenha" required>

                    <label for="txtDB">Nome do Banco de Dados:</label>
                    <input type="text" id="txtDB" name="txtDB" required>

                    <label for="txtPrefixo">Prefixo da Tabela:</label>
                    <input type="text" id="txtPrefixo" name="txtPrefixo" value="<?php echo gerarPrefixo(); ?>" required>
                    <span style="font-size:12px;font-weight:bold;">O Prefixo é gerado automaticamente, altere se necessário.</span><br><br>

                    <button type="submit">Continuar</button>
                </form>
            </div>
        <?php
        } elseif ($page == 'info') {
            $txtUsuario = '';
            $txtSenha = '';
            if (requestPOST()) {
                include_once(dirname(__FILE__, 2) . '/classes/vendor/autoload.php');
                include_once(documentroot() . '/core/config.php');
                               
                $txtNomeSite = CleanPOST('txtNomeSite');
                $txtUsuario = CleanPOST('txtUsuario');
                $txtSenha = password_hash(CleanPOST('txtSenha'), PASSWORD_DEFAULT);
                $txtEmail = CleanPOST('txtEmail');

                $db1 = new insert($dbBlogs1);
                $db1->table('sites')
                    ->add('link', servername(false, true))
                    ->insert();
                $db1->close();

                $db2 = new insert($dbBlogs1);
                $db2->table('options')
                    ->add('idsite')->prepared(1, 'i')
                    ->add('titulo')->prepared($txtNomeSite)
                    ->add('datapublicado')->prepared(date('Y-m-d H:i:s'))
                    ->insert();
                $db2->close();

                $db3 = new insert($dbBlogs1);
                $db3->table('rodape')
                    ->add('idsite', '1')
                    ->add('projeto', '')
                    ->add('estilos', '')
                    ->add('descricao', '')
                    ->add('dataalterado', date('Y-m-d H:i:s'))
                    ->insert();
                $db3->close();

                $db4 = new insert($dbBlogs1);
                $db4->table('users')
                    ->add('idsite')->prepared(1, 'i')
                    ->add('nome')->prepared($txtUsuario)
                    ->add('usuario')->prepared($txtUsuario)
                    ->add('senha')->prepared($txtSenha)
                    ->add('email')->prepared($txtEmail)
                    ->add('permissao')->prepared(serialize(['nivel' => 'superadmin']))
                    ->add('datapublicado')->prepared(date('Y-m-d H:i:s'))
                    ->add('dataalterado')->prepared(date('Y-m-d H:i:s'))
                    ->insert();
                $db4->close();

                if (!file_exists(dirname(__FILE__, 2) . '/sites/1/files/')) {
                    mkdir(dirname(__FILE__, 2) . '/sites/1/files/', 0755, true);
                }

                if (!file_exists(dirname(__FILE__, 2) . '/sites/1/plugin/')) {
                    mkdir(dirname(__FILE__, 2) . '/sites/1/plugins/', 0755, true);
                }

                if (!file_exists(dirname(__FILE__, 2) . '/sites/1/sitemap/')) {
                    mkdir(dirname(__FILE__, 2) . '/sites/1/sitemap/', 0755, true);
                }

                if (!file_exists(dirname(__FILE__, 2) . '/sites/1/themes/')) {
                    mkdir(dirname(__FILE__, 2) . '/sites/1/themes/', 0755, true);
                }

                if (!file_exists(dirname(__FILE__, 2) . '/logs/')) {
                    mkdir(dirname(__FILE__, 2) . '/logs/', 0755, true);
                }

                redirect('/', ['page' => 'finish']);
            }
        ?>
            <div class="form-container">
                <form action="/?page=info" method="POST">
                    <h1>Configuração do Site</h1>
                    <span style="font-weight:bold;">Guarde essas informações em um local seguro.</span><br><br>

                    <label for="txtNomeSite">Nome do Site</label>
                    <input type="text" id="txtNomeSite" name="txtNomeSite" required>

                    <label for="txtUsuario">Usuário:</label>
                    <input type="text" id="txtUsuario" name="txtUsuario" required>

                    <label for="txtSenha">Senha:</label>
                    <input type="password" id="txtSenha" name="txtSenha" required>

                    <label for="txtEmail">E-mail:</label>
                    <input type="email" id="txtEmail" name="txtEmail" required>

                    <button type="submit">Continuar</button>
                </form>
            </div>
        <?php
        } elseif ($page == 'finish') {
            unlink(dirname(__FILE__) . '/install.php');
        ?>
            <div class="container">
                <h1>MiConteudo - Instalação Finalizada</h1><br>
                <p>O MiConteudo está pronto para utilização.</p><br>
                <p>Acesse o <strong>site administrativo</strong> para começar a gerenciar seu conteúdo.</p>
                <div class="button-container">
                    <a href="/adm/" class="btn">Ir para o Administrativo</a>
                </div>
            </div>
    <?php
        } else {
            echo 'Não foi possível continuar a instalação, página não encontrada!';
            exit;
        }
    }
    ?>
</body>

</html>