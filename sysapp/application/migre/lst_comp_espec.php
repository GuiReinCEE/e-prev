<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	header( 'location:'.site_url('cadastro/avaliacao_competencia_especifica') );
	
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_comp_espec.html');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

	if(!gerencia_in(array('GAD')))
	{
   		header('location: acesso_restrito.php?IMG=banner_comp_espec');
	}

	$tpl->newBlock('lista');
	$sql =        " select 	cd_comp_espec, nome_comp_espec  ";
	$sql = $sql . " from   	projetos.comp_espec ";
	$sql = $sql . " order 	by nome_comp_espec ";
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('comp_espec');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('codigo', $reg['cd_comp_espec']);
		$tpl->assign('descricao', $reg['nome_comp_espec']);
	}

	pg_close($db);
	$tpl->printToScreen();	
?>