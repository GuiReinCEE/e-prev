<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_inscritos_seminario_barra.html');

	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);	
	
	if(trim($_REQUEST['cd_lote']) != "")
	{
		#### IMPRIMIR CODIGOS ####
		$tpl->assign('executa', 'inscritos_seminario_barra_lote_imprime.php');
		$tpl->assign('fl_disabled', 'disabled');
		$tpl->assign('cd_lote', trim($_REQUEST['cd_lote']));
		
		#### BUSCA LOTE ####
		$sql = "
				SELECT scbl.cd_seminario_edicao AS cd_seminario,
					   (SELECT COUNT(*) 
					      FROM acs.seminario_codigo_barra scb
						 WHERE scb.cd_seminario_codigo_barra_lote = scbl.cd_seminario_codigo_barra_lote) AS qt_barra
		          FROM acs.seminario_codigo_barra_lote scbl
				 WHERE scbl.cd_seminario_codigo_barra_lote = ".trim($_REQUEST['cd_lote'])."
			   ";
		$rs = pg_query($db, $sql);
		$ar_reg = pg_fetch_array($rs);
		$tpl->assign('qt_barra', $ar_reg['qt_barra']);
	}
	else
	{
		#### GERAR CODIGOS ####
		$tpl->assign('executa', 'grava_inscrito_seminario_barra.php');
		$tpl->assign('fl_imprimir', 'disabled');
	}

	
	
	#### LISTA SEMINARIO ####
	$sql = "
			SELECT cd_seminario_edicao,
				   ds_seminario_edicao 
	          FROM acs.seminario_edicao
		   ";
	$rs = pg_query($db, $sql);
	while ($seminario_reg = pg_fetch_array($rs)) 
	{
		$tpl->newBlock('cbo_seminario');
		$tpl->assign('cd_seminario', $seminario_reg['cd_seminario_edicao']);
		$tpl->assign('ds_seminario', $seminario_reg['ds_seminario_edicao']);
		$tpl->assign('fl_seminario', ($seminario_reg['cd_seminario_edicao'] == $ar_reg['cd_seminario']? ' selected' : ''));
	}		

	pg_close($db);
	$tpl->printToScreen();	

?>