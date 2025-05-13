<?
   include_once('inc/sessao.php');
   include_once('./../inc/conexao.php');
   include_once('./../inc/class.TemplatePower.inc.php');
   
   header("location: biblioteca_virtual_multimidia.php?ano=".date("Y"));
   
   $tpl = new TemplatePower('tpl/tpl_biblioteca_virtual_multimidia.htm');

   $tpl->prepare();
   $tpl->assign('n', $n);
   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);
   $tpl->newBlock('cadastro');

   pg_close($db);
   $tpl->printToScreen();	
?>