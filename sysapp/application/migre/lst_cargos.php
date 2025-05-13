<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	header( 'location:'.site_url('cadastro/avaliacao_cargo') );
	exit;

	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_cargos.html');
	//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG=str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	//--------------------------------------------------------------
	if(!gerencia_in(array('GAD')))
	{
		header('location: acesso_restrito.php?IMG=banner_cargos');
	}
	//--------------------------------------------------------------	
	$tpl->newBlock('lista');
	$sql =        " select cd_cargo, nome_cargo  ";
	$sql = $sql . " from   projetos.cargos ";
	$sql = $sql . " order by replace(nome_cargo, ' ','A') ";

	$rs=pg_query($db, $sql);
	$cont=0;
	while($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('cargos');
		$cont = $cont + 1;
		if(($cont % 2)<>0)
		{
			$tpl->assign('cor_fundo', '#D5D5D5');
		}
		else
		{
			$tpl->assign('cor_fundo', '#F4F4F4');
		}
		$tpl->assign('codigo', $reg['cd_cargo']);
		$tpl->assign('descricao', $reg['nome_cargo']);
	}
	//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
?>