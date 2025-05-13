<?php
	require('inc/conexao.php');
	require('inc/sessao.php');
	$ar_query = explode("&",$_SERVER['QUERY_STRING']);
	$ar_param = "";
	foreach ($ar_query as $key => $valor) 
	{
		$ar_tmp = explode("=",$valor);
		$ar_param[] = $ar_tmp[1];
	}
	header("Location: ".site_url("ecrm/divulgacao/index/".implode("/",$ar_param)));
?>