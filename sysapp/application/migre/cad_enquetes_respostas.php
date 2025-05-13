<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_enquetes_respostas.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$v_cor_fundo1 = "#F2F8FC";
	$v_cor_fundo2 = "#FFFFFF";		
	
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($eq))	{
		$sql =        " select 	cd_enquete, titulo, cd_responsavel ";
		$sql = $sql . " from 	projetos.enquetes  ";
		$sql = $sql . " where 	cd_enquete = $eq ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('eq', $eq);
		if ($reg['cd_responsavel'] != $Z) {
			$tpl->assignGlobal('ro_responsavel', 'readonly');
			$tpl->assignGlobal('dis_responsavel', 'disabled');
		}			
	}
//------------------------------------------------------------------------------------------- Lista de sites
	if (isset($c))	{
		$sql =        " select 	nome, ordem ";
		$sql = $sql . " from 	projetos.enquete_respostas  ";
		$sql = $sql . " where 	cd_enquete = $eq and cd_resposta = $c ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $c);
		$tpl->assign('resposta', $reg['nome']);
		$tpl->assign('ordem', $reg['ordem']);
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>