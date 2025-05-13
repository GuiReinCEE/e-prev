<?php
	include_once('inc/sessao_senha.php');
	include_once('inc/conexao.php');

	#ECHO "<pre>"; PRINT_R($_SESSION); EXIT;

	if($_REQUEST['fl_tipo'] > 0)
	{
		$_SESSION['TPS'] = $_REQUEST['fl_tipo'];
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=auto_atendimento_senha_passo_3.php'>";
		exit;
	}
	else
	{
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=auto_atendimento_senha.php'>";
		exit;
	}
?>