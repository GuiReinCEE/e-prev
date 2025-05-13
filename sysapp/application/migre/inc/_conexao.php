<?php
/**
 * CI-EPREV
 * @return unknown_type
 */
/*function base_url()
{
	$protocolo = (isset($_SERVER['HTTPS']))?"https://":"http://";
	return $protocolo.$_SERVER['SERVER_NAME']."/cieprev/";
}*/

/**
 * CI-EPREV
 * @return string
 */
/*function index_page()
{
	return "index.php";
}*/

/**
 * EPREV
 * @return unknown_type
 */
/*function base_url_eprev()
{
	$protocolo = (isset($_SERVER['HTTPS']))?"https://":"http://";
	return $protocolo.$_SERVER['SERVER_NAME'] . "/controle_projetos/";
}*/


/*
if($_SERVER['SERVER_ADDR'] == '10.63.255.222' || $_SERVER['SERVER_ADDR']=='10.63.255.141' || $_SERVER['SERVER_ADDR']=='10.63.255.150')
{
	$db['default']['hostname'] = "10.63.255.222";
}
else 
*/
if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
{
	$ip_host = '10.63.255.5';
}
else 
{
	$ip_host = '10.63.255.222';
}

$db = pg_connect('host='.$ip_host.' port=5555 dbname=fundacaoweb user=gerente');
if (!$db) 
{
	header("Location: erro.php?c=conexaofalhou");
	exit;
}
?>