<?
//	include_once('inc/sessao.php');
//	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_redimensiona_imagens.html');
	$tpl->prepare();
	$tpl->assign('imagem', $img);
	$tpl->printToScreen();
?>