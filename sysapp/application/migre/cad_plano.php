<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_plano.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('p', $p);
	
	
	#### OS Nº 17608 ####
	if (($_SESSION['D'] != 'GI') AND ($_SESSION['U'] != "mpozzebon") AND ($_SESSION['U'] != "vdornelles"))
	{
   		header('location: acesso_restrito.php?IMG=banner_exec_tarefa');
	} 	
		
	
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($p))	{
		$sql =        " select 	p.cd_plano, p.descricao, pc.nome_certificado, pc.cd_spc, pos_imagem, largura_imagem, coluna_1, coluna_2, to_char(dt_aprovacao_spc,'dd/mm/yyyy') as dt_aprovacao_spc, ";
		$sql = $sql . " 		pc.versao_certificado, to_char(pc.dt_inicio, 'dd/mm/yyyy') as dt_inicio, to_char(pc.dt_final, 'dd/mm/yyyy') as dt_final ";
		$sql = $sql . " from 	planos p, planos_certificados pc ";
		$sql = $sql . " where 	p.cd_plano = $p and versao_certificado = $v " ;
		$sql = $sql . " 	and	p.cd_plano = pc.cd_plano ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_plano', $reg['cd_plano']);
		$tpl->assign('cd_versao', $reg['versao_certificado']);
		$tpl->assign('nome', $reg['descricao']);
		$tpl->assign('nome_certif', $reg['nome_certificado']);
		$tpl->assign('cd_plano_spc', $reg['cd_spc']);
		$tpl->assign('posicao', $reg['pos_imagem']);
		$tpl->assign('largura', $reg['largura_imagem']);
		$tpl->assign('dt_inicio', $reg['dt_inicio']);
		$tpl->assign('dt_final', $reg['dt_final']);
		$tpl->assign('coluna_1', $reg['coluna_1']);
		$tpl->assign('coluna_2', $reg['coluna_2']);
		$tpl->assign('dt_aprovacao_spc', $reg['dt_aprovacao_spc']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>