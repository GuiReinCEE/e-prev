<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	header( 'location:'.base_url().'index.php/cadastro/avaliacao_familia' );
	
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_familias.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------
	if (($D <> 'GAD') and ($Z <> 191)) {
   		header('location: acesso_restrito.php?IMG=banner_familias');
	}
//--------------------------------------------------------------	
	$tpl->newBlock('lista');
	$sql =        " select cd_familia, nome_familia  ";
	$sql = $sql . " from   projetos.familias_cargos ";
	$sql = $sql . " order by nome_familia ";
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('familias');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('codigo', $reg['cd_familia']);
		$tpl->assign('descricao', $reg['nome_familia']);
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>