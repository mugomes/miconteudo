<?php

// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Mestre da Info
// Site: https://www.mestredainfo.com.br

if (!defined('miconteudo')) {
    exit;
}

function mglog(string $ex)
{
    global $sandbox;

    if (isset($sandbox)) {
        if ($sandbox) {
            echo $ex;
        } else {
            file_put_contents(dirname(__FILE__, 2) . '/logs/error_log', $ex, FILE_APPEND);
        }
    } else {
        file_put_contents(dirname(__FILE__, 2) . '/logs/error_log', $ex, FILE_APPEND);
    }
}


/* Anti SQL INJECTION */
function CleanDB(?string $valor): ?string
{
    if (is_null($valor)) {
        $txt = '';
    } else {
        $txt = trim($valor);
        $txt = strip_tags($txt);
        $txt = addslashes($txt);
    }

    return $txt;
}

/* Limpar GET */
function CleanGET(string $nome, int $filter = FILTER_DEFAULT): ?string
{
    return filter_input(INPUT_GET, $nome, $filter);
}

function emptyGET(string $nome, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_GET, $nome, FILTER_DEFAULT, $options)) ? true : false;
}

/* Limpar POST */
function CleanPOST(string $nome, int $filter = FILTER_DEFAULT): ?string
{
    return filter_input(INPUT_POST, $nome, $filter);
}

function emptyPOST(string $nome, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_POST, $nome, FILTER_DEFAULT, $options)) ? true : false;
}

function requestPOST(): bool
{
    if (getenv('REQUEST_METHOD') == 'POST') {
        return true;
    } else {
        return false;
    }
}

function requestGET(): bool
{
    if (getenv('REQUEST_METHOD') == 'GET') {
        return true;
    } else {
        return false;
    }
}

function documentroot(): string
{
    return dirname(__FILE__, 2);
}

function servername(bool $comprotocolo = true, bool $semwww = false): string
{
    $sServer = '';
    $servername = CleanDB(getenv('SERVER_NAME'));

    if ($comprotocolo) {
        if (empty(getenv('HTTPS'))) {
            $txtProtocolo = 'http://';
        } else {
            if (getenv('HTTPS') !== 'off') {
                $txtProtocolo = 'https://';
            } else {
                $txtProtocolo = 'http://';
            }
        }

        $sServer = $txtProtocolo;
    }

    if ($semwww) {
        $sServer .= str_replace('www.', '', $servername);
    } else {
        $sServer .= $servername;
    }

    return $sServer;
}

function requestURI(): string
{
    return rtrim(parse_url(CleanDB(getenv('REQUEST_URI')), PHP_URL_PATH), '/');
}

/* Obtém IP */
function getClientIP(): string
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')) {
        $ipaddress = getenv('HTTP_CLIENT_IP');
    } else if (getenv('HTTP_X_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    } else if (getenv('HTTP_X_FORWARDED')) {
        $ipaddress = getenv('HTTP_X_FORWARDED');
    } else if (getenv('HTTP_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    } else if (getenv('HTTP_FORWARDED')) {
        $ipaddress = getenv('HTTP_FORWARDED');
    } else if (getenv('REMOTE_ADDR')) {
        $ipaddress = getenv('REMOTE_ADDR');
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

/* Exibe Alertas */
function windowAlert(string $message)
{
    echo sprintf("<script>window.alert('%s');</script>", $message);
}

/* Redireciona */
function redirect(string $url, mixed $params = '')
{
    $sParams = '?';

    if (is_array($params)) {
        foreach ($params as $name => $value) {
            $sParams .= sprintf('%s=%s&', $name, $value);
        }
    } else {
        $sParams = '';
    }

    $sParams = rtrim($sParams, '&');

    echo sprintf("<script>window.location.assign('%s%s');</script>", $url, $sParams);
    exit;
}

/* Envia Emails */
function sendmail(array $sFrom, array $sTo, string $sSubject, string $sMessage): bool
{
    global $dbBlogs1, $idsite;

    $txtSMTPEmail = ['servidor' => '', 'porta' => '', 'email' => '', 'senha' => ''];

    $db1 = new \MiConteudo\database\select($dbBlogs1);
    $db1->column('configemail')
        ->table('options')
        ->where('idsite', $idsite)
        ->limit(0, 1)
        ->select();
    while ($row = $db1->fetch()) {
        $db1->rows($row);

        $txtSMTPEmail = empty($db1->row('configemail')) ? ['servidor' => '', 'porta' => '', 'email' => '', 'senha' => ''] : unserialize($db1->row('configemail'));
    }
    $db1->close();

    $mailer = new PHPMailer\PHPMailer\PHPMailer();
    $mailer->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mailer->IsSMTP();
    $mailer->Username = $txtSMTPEmail['email'];
    $mailer->Password = descript($txtSMTPEmail['senha'], 'GXQe*CsWhLglqeLF@u3kXP75');
    $mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // Protocolo de segurança
    $mailer->Port = $txtSMTPEmail['porta']; // Porta SMTP para STARTTLS
    $mailer->SMTPDebug = false;
    $mailer->Host = $txtSMTPEmail['servidor'];
    $mailer->SMTPAuth = false; //define se havera ou nao autenticacao no SMTP
    $mailer->FromName = $sFrom['name']; //Nome que sera exibido para o destinatario
    $mailer->From = $sFrom['email']; //Obrigatorio ser a mesma caixa postal indicada em "username"
    $mailer->AddAddress($sTo['email'], $sTo['name']); //Destinatarios
    $mailer->Subject = $sSubject;
    $mailer->Body = $sMessage;
    $mailer->isHTML(true);
    $mailer->Priority = 3;
    $mailer->CharSet = 'utf-8';

    $send = $mailer->Send();

    file_put_contents(documentroot() . '/logs/sendmail', $mailer->ErrorInfo);

    return $send;
}

/* Gera Senhas */
function GerarSenha(int $tamanho = 5, bool $maiusculo = true, bool $numeros = true, bool $simbolos = false): string
{
    $lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '1234567890';
    $simb = '!@#$%*-';

    $retorno = '';

    $caracteres = '';
    $caracteres .= $lmin;
    $caracteres .= ($maiusculo) ? $lmai : '';
    $caracteres .= ($numeros) ? $num : '';
    $caracteres .= ($simbolos) ? $simb : '';

    $len = strlen($caracteres);

    for ($n = 1; $n <= $tamanho; $n++) {
        $rand = mt_rand(1, $len);
        $retorno .= $caracteres[$rand - 1];
    }

    return $retorno;
}

function changedate(string $data, string $format = 'd/m/Y', string $newformat = 'Y-m-d'): string
{
    $date = \DateTime::createFromFormat($format, $data);
    $new_date = $date->format($newformat);

    return $new_date;
}

function diadasemana(string $data): string
{
    $diasemana = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
    $diasemana_numero = date('w', strtotime($data));
    return $diasemana[$diasemana_numero];
}

function moedaReal(string $valor): string
{
    return number_format($valor, 2, ",", ".");
}

function mesextenso(int $mes): string
{
    $mesextenso = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    $mesextenso_numero = ltrim($mes, '0');
    return $mesextenso[$mesextenso_numero];
}

function formatnumber(string $valor): string
{
    return empty($valor) ? 0 : str_pad($valor, 2, '0', STR_PAD_LEFT);
}

function removerAcentos(string $valor): string
{
    $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
    return str_replace($array1, $array2, $valor);
}

/* Remover caracteres especiais de um texto */
function removerCaracteresEspeciais(string $valor): string
{
    $array1 = array("$", "@", "%", "&", "*", "/", "+", "#");
    $array2 = array("", "", "", "", "", "", "", "");
    return str_replace($array1, $array2, $valor);
}

function gerarLink(string $valor): string
{
    $txt = removerAcentos($valor);
    $txt = removerCaracteresEspeciais($txt);
    $txt = str_replace(' ', '-', $txt);
    return $txt;
}

/* Exibe arrays formatados com tag pre */
function pre($value)
{
    printf('<pre>%s</pre>', print_r($value, true));
}

/* Verifica Arrays */
function strposa(string $haystack, mixed $needle): bool
{
    if (!is_array($needle)) {
        $needle = array($needle);
    }

    foreach ($needle as $query) {
        if (strpos($haystack, $query, 0) !== false) {
            return true; // stop on first true result
        }
    }
    return false;
}

function cript(string $valor, string $chave): string
{
    $cipher = 'aes-256-cbc';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encrypted = openssl_encrypt($valor, $cipher, $chave, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

function descript(string $valor, string $chave): string
{
    $cipher = 'aes-256-cbc';
    $data = base64_decode($valor);
    $ivLength = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivLength);
    $encryptedData = substr($data, $ivLength);
    return openssl_decrypt($encryptedData, $cipher, $chave, OPENSSL_RAW_DATA, $iv);
}

function idioma(): string {
    $sLang = getenv('HTTP_ACCEPT_LANGUAGE');
    $sCode = substr($sLang, 0, 5);
    return $sCode;
}
