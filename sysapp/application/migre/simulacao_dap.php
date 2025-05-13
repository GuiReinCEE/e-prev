<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('class.SocketAbstraction.inc.php');
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/config.inc.php');
	
	header( 'location: https://www.e-prev.com.br/controle_projetos/simulacao_dap.php?'.$_SERVER['QUERY_STRING']);
	
	EXIT;
	
	
	#define(SKT_IP, '10.63.255.16'); 
	#define(SKT_PORTA, '4444');  

	$LISTNER_IP    = SKT_IP;
	$LISTNER_PORTA = SKT_PORTA;

	$tpl = new TemplatePower('tpl/tpl_simulacao_dap.html');
	$tpl->prepare();

	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php'); 

	if (($_SESSION['D'] != 'GAP') AND ($_SESSION['D'] != 'GI') AND ($_SESSION['D'] != 'GF'))
	{
   		header('location: acesso_restrito.php?IMG=banner_prevnet');
	}  

	$tpl->assign('usuario', $N);
	$tpl->assign('usuario_emp', $U);
	$tpl->assign('divsao', $D);
	$tpl->assign('mnEmp', $EMP);
	$tpl->assign('mnRe', $RE);
	$tpl->assign('mnSeq', $SEQ);
	$tpl->assign('mnNome', $NOME);
	$tpl->assign('mnAtendente', $ATENDENTE);
	$tpl->assign('MOSTRAR_BANNER', $_REQUEST['MOSTRAR_BANNER']);
	$tpl->assign('ip_listener', str_replace("10.64.255.","64.",str_replace("10.63.255.","63.",$LISTNER_IP)).":".$LISTNER_PORTA);

	// Valores Fixos
	$tpl->assign('data_simulacao', date('d/m/y'));

	// Valores obtidos pelo métido GET
	$tpl->assign('cd_empresa', $_REQUEST['e']);
	$tpl->assign('cd_registro_empregado', $_REQUEST['r']);
	$tpl->assign('seq_dependencia', $_REQUEST['s']);
	$tpl->assign('getOrigem', $_REQUEST['o']);
	$tpl->assign('forma_calculo', $_REQUEST['fc']);

	$skt2 = new Socket();
	$skt2->SetRemoteHost($LISTNER_IP);
	$skt2->SetRemotePort($LISTNER_PORTA);
	$skt2->SetBufferLength(131072);
	$valores = explode(";", $skt2->Ask('fnc_serialize_tipo_risco'));
	for ($i=0; $i<count($valores); $i++) 
	{
		$tpl->newBlock('blk_tipo_risco');
		$j = explode('|', $valores[$i]);
		$tpl->assign('cd_tipo_risco', $j[0]);
		$tpl->assign('desc_tipo_risco', $j[1]);
	}

	$tpl->printToScreen();
?>