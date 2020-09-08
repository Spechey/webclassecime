<?php
include_once("conf.php");
if (!isset($_SERVER['PHP_AUTH_USER'])
	|| $_SERVER['PHP_AUTH_USER'] != $login || $_SERVER['PHP_AUTH_PW'] != $password) 
{
    header('WWW-Authenticate: Basic realm="Authentification"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authentification requise';
    exit;
}
?>
