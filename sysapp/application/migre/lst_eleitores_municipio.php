<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_eleitores_municipio.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------
	if (($Z <> 191) and ($Z <> 154)) {
   		header('location: acesso_restrito.php?IMG=banner_eleitores_municipio');
	}
//--------------------------------------------------------------	
	if ($mun == '') {
		$tpl->newBlock('lst_estado');
		$sql =        " select 	distinct cidade, count(*) as num_eleitores  ";
		$sql = $sql . " from   	eleicoes.cadastros_eleicoes ";
		$sql = $sql . " group by cidade order by cidade ";		
		
		$rs=pg_exec($db, $sql);
		$cont = 0;	
		while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('municipio');
			$cont = $cont + 1;
			if (($cont % 2) <> 0) {
				$tpl->assign('cor_fundo', $v_cor_fundo1);
			}
			else {
				$tpl->assign('cor_fundo', $v_cor_fundo2);
			}
			$tpl->assign('municipio', $reg['cidade']);
			$tpl->assign('num_eleitores', $reg['num_eleitores']);
		}
		$sql =        " select 	count(*) as num_eleitores  ";
		$sql = $sql . " from   	eleicoes.cadastros_eleicoes ";
		$rs=pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->newBlock('tot_estado');
		$tpl->assign('tot_eleitores', $reg['num_eleitores']);
	}
	else {
		$tpl->newBlock('lst_municipio');
		$tpl->assign('mun', $mun);
		$sql =        " select 	ce.cd_empresa, ce.cd_registro_empregado, ce.seq_dependencia, p.nome, ce.logradouro as logr  ";
		$sql = $sql . " from   	eleicoes.cadastros_eleicoes ce, participantes p ";
		$sql = $sql . " where	ce.cd_empresa = p.cd_empresa and ce.cd_registro_empregado = p.cd_registro_empregado ";
		$sql = $sql . " and 	ce.seq_dependencia = p.seq_dependencia and ce.cidade = '". $mun . "' ";		
		$sql = $sql . " order by nome "; 
		$rs=pg_exec($db, $sql);
		$cont = 0;	
		while ($reg=pg_fetch_array($rs)) {
			$tpl->newBlock('mun_municipio');
			$cont = $cont + 1;
			if (($cont % 2) <> 0) {
				$tpl->assign('cor_fundo', $v_cor_fundo1);
			}
			else {
				$tpl->assign('cor_fundo', $v_cor_fundo2);
			}
			$tpl->assign('cd_empresa', $reg['cd_empresa']);
			$tpl->assign('cd_registro_empregado', $reg['cd_registro_empregado']);
			$tpl->assign('seq_dependencia', $reg['seq_dependencia']);
			$dim = imagettfbbox ( 8, 0, 'inc/verdana.ttf', $reg['logr'] );
			$larg = $dim[2] - $dim[0];
			$tpl->assign('nome', $reg['nome'].'-'.$larg);
		}
		$sql =        " select 	count(*) as num_eleitores  ";
		$sql = $sql . " from   	eleicoes.cadastros_eleicoes ";
		$sql = $sql . " where	cidade = '" . $mun . "' ";
		$rs=pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->newBlock('tot_municipio');
		$tpl->assign('tot_eleitores', $reg['num_eleitores']);
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>