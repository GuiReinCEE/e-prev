<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_exame_ingresso_cad.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	#### PERMISSOES ####
	if(($_SESSION['D'] != "GAP") and ($_SESSION['D'] != "GI"))
	{
		$tpl->assign('fl_editar', 'disabled');
	}

	$tpl->printToScreen();
	pg_close($db);
?>