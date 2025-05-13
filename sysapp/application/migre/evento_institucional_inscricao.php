<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

	header( 'location:'.base_url().'index.php/ecrm/evento_institucional_inscricao' );

    include_once('inc/class.TemplatePower.inc.php');

    $tpl = new TemplatePower('tpl/tpl_evento_institucional_inscricao.html');

    $tpl->prepare();

    $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
    include_once('inc/skin.php');
	
	if (($_SESSION['D'] != 'GRI') AND ($_SESSION['D'] != 'GI') AND ($_SESSION['D'] != 'GAP'))
	{
   		header('location: acesso_restrito.php?IMG=banner_prevnet');
	} 	

    $tpl->assign('usuario', $N);
    $tpl->assign('divsao', $D);

    $tipo_promocao_hidden = "";

    $tpl->printToscreen();
?>