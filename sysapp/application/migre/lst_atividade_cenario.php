<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');

header( 'location:'.base_url().'index.php/atividade/legal' );

include_once('inc/class.TemplatePower.inc.php');

$tpl = new TemplatePower('tpl/tpl_lst_atividade_cenario.html');
$tpl->prepare();
include_once('inc/skin.php');

$tpl->assign('usuario', $N);
$tpl->assign('divsao', $D);

if(isset($_SESSION['debug_man']))
{
	$tpl->assign( "debug_man", $_SESSION['debug_man'] );
}

$tpl->printToScreen();
?>