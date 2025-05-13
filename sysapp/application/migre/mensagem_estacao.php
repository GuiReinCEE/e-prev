<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

	header('location:'.base_url().'index.php/ecrm/mensagem_estacao');
	
	include_once('inc/class.TemplatePower.inc.php');

    $tpl = new TemplatePower('tpl/tpl_mensagem_estacao.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $tpl->printToscreen();
?>