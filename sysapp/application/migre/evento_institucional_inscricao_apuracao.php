<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_evento_institucional_inscricao_apuracao.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	#### PERMISSOES ####
	if(($_SESSION['D'] != "GRI") and ($_SESSION['D'] != "GI"))
	{
		
	}
	
	$qr_sql = "
				SELECT cd_eventos_institucionais_apuracao, 
				       ds_nome
				  FROM projetos.eventos_institucionais_apuracao
				 ORDER BY ds_nome
	          ";
	$ob_resul = pg_query($db, $qr_sql);
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista_codigo');
		$tpl->assign('cd_apuracao',  $ar_reg['cd_eventos_institucionais_apuracao']);
		$tpl->assign('ds_apuracao',  $ar_reg['ds_nome']);
	}

	$tpl->printToScreen();
	pg_close($db);
?>