<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_manutencao.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
  	if (isset($IMG)) { } else { $IMG='banner_manutencao'; }
	$tpl->assign('imagem', $IMG);
	$tpl->newBlock('cadastro');
	$tpl->printToScreen();	
?>