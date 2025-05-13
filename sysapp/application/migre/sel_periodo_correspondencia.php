<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_sel_periodo_correspondencia.html');
	header( 'location:'.base_url().'index.php/cadastro/sg_correspondencia_relatorio');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//	echo $patrocinadora;
	if ($dt_inicial == '') { 
		$tpl->assign('dt_inicial', '01/'.date('m/Y')); 
	}
	else {
		$tpl->assign('dt_inicial', $dt_inicial);
	}
	if ($dt_final == '') { 
		$tpl->assign('dt_final', date('d/m/Y')); 
	}
	else {
		$tpl->assign('dt_final', $dt_final);
	}
	$tpl->assign('emp', $patrocinadora);
	$tpl->assign('pl', $plano);
	$txt_dt_inicial	= ( $dt_inicial    == '' ? date('Y-m-d') : convdata_br_iso($dt_inicial));
	$txt_dt_final	= ( $dt_final    == '' ? date('Y-m-d') : convdata_br_iso($dt_final));
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
//--------------------------------------------------------------
function convdata_br_iso($dt) {
	// Pressupѕe que a data esteja no formato DD/MM/AAAA
	// A melhor forma de gravar datas no PostgreSQL щ utilizando 
	// uma string no formato DDDD-MM-AA. Esta funчуo justamente 
	// adequa a data a este formato
	$d = substr($dt, 0, 2);
	$m = substr($dt, 3, 2);
	$a = substr($dt, 6, 4);
	return $a.'-'.$m.'-'.$d;
}
//--------------------------------------------------------------	
?>