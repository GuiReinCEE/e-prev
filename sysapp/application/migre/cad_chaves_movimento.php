<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_cad_chaves_movimento.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	#### LISTA CHAVES ####
	$qr_select = "
					 SELECT cd_chave,
					        cd_sala,
							ds_chave
					   FROM projetos.chaves
					  ORDER BY cd_sala,
					           ds_chave
				 ";
	$ob_result = pg_query($db, $qr_select);	
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		$tpl->newBlock('lst_chaves');
		$tpl->assign('cd_chave', $ar_reg['cd_chave']);	
		$tpl->assign('ds_chave', $ar_reg['cd_sala']." - ".$ar_reg['ds_chave']);
	}	
	
	$tpl->printToScreen();
	pg_close($db);
?>