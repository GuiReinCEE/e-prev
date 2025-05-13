<?PHP
   include_once("inc/sessao.php");
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   $tpl = new TemplatePower('tpl/tpl_cad_noticia.htm');
   $tpl->prepare();
// --------------------------------------------------------- inicialização do skin das telas:
		$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------   
		$tpl->assign('usuario', $N);
		$tpl->assign('divsao', $D); 
   $tpl->assign('titulo', '');
   $tpl->assign('noticia', '');
   $tpl->assign('codigo', '');
   $tpl->assign('pagina_acao', 'db_inc_noticia.php');
   $tpl->assign('lbl_botao', 'Incluir notícia');
   $tpl->printToScreen();
?>
