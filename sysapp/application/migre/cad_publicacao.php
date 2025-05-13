<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_publicacao.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	if (isset($c))	{
		$sql =        " select cd_publicacao, nome_publicacao, desc_publicacao ";
		$sql = $sql . " from projetos.publicacoes where cd_publicacao=$c " ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_publicacao']);
		$tpl->assign('nome', $reg['nome_publicacao']);
		$tpl->assign('descricao', $reg['desc_publicacao']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>