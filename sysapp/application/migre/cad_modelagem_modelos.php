<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_cad_modelagem_modelos.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$tpl->assign('tela_voltar', $_SERVER['HTTP_REFERER']);
	$tpl->assign('cd_modelo', $_REQUEST['cd_modelo']);

	
	#### BUSCA MODELO PARA EDITAR ####
	if(trim($_REQUEST['cd_modelo']) != "")
	{
		$qr_select = "
						SELECT ds_modelo,
						       ds_cor
						  FROM modelagem.modelos
						 WHERE cd_modelo = ".$_REQUEST['cd_modelo']."
		             ";
		$ob_result = pg_query($db, $qr_select);	
		$ar_reg = pg_fetch_array($ob_result);
		$tpl->assign('ds_modelo', $ar_reg['ds_modelo']);
		$tpl->assign('ds_cor',    $ar_reg['ds_cor']);
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>