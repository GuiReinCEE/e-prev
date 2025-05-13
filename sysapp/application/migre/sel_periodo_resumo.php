<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

    header( 'location:'.base_url().'index.php/ecrm/relatorio_atendimento');

	$tpl = new TemplatePower('tpl/tpl_sel_periodo_resumo.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);


	if (trim($_REQUEST['dt_inicial']) == '') 
	{ 
		$_REQUEST['dt_inicial'] = '01/'.date('m/Y'); 
	}
	$tpl->assign('dt_inicial', $_REQUEST['dt_inicial']);

	if (trim($_REQUEST['dt_final']) == '') 
	{ 
		$_REQUEST['dt_final'] = date('d/m/Y'); 
	}
	$tpl->assign('dt_final', $_REQUEST['dt_final']);

	$tpl->printToscreen();
	

?>