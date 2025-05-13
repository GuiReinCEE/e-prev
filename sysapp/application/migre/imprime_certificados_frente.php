<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_imprime_certificados_frente.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//	echo $patrocinadora;
	$tpl->assign('dt_inicial', $di);
	$tpl->assign('dt_final', $df);
	$tpl->assign('patrocinadora', $emp);
	$tpl->assign('plano', $pl);
	$txt_dt_inicial	= ( $di    == '' ? date('Y-m-d') : convdata_br_iso($di));
	$txt_dt_final	= ( $df    == '' ? date('Y-m-d') : convdata_br_iso($df));
//-------------------------------------------------------------- 
	if ( ($D <> 'GI') and ($D <> 'GAP')) {
   		header('location: acesso_restrito.php?IMG=banner_certificados');
	}
//--------------------------------------------------------------	
	$sql =        " select 	count(*) as num_regs ";
	$sql = $sql . " from   	participantes p, titulares t ";
	$sql = $sql . " where	p.cd_empresa = t.cd_empresa ";
	$sql = $sql . " and		p.cd_registro_empregado = t.cd_registro_empregado ";
	$sql = $sql . " and		p.seq_dependencia = t.seq_dependencia ";
	$sql = $sql . " and 	p.dt_envio_certificado is null and p.dt_obito is null  ";
	if ($emp != '') {
		$sql = $sql . " and 	p.cd_empresa = " . $emp . " ";
	}
	if ($pl != '') {
		$sql = $sql . " and 	p.cd_plano = " . $pl . " ";
	}
	$sql = $sql . "and date_trunc('day', t.dt_ingresso_eletro) >= '" . $txt_dt_inicial . "' ";
	$sql = $sql . "and date_trunc('day', t.dt_ingresso_eletro) <= '" . $txt_dt_final . "' "; 
	$rs=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);	
	$v_num_lotes = number_format(($reg['num_regs'] / 10),0,'','');
	$v_num_lotes = 2;
	
	$tpl->assign('total', $reg['num_regs']);
	$tpl->assign('num_lotes', $v_num_lotes);
	while ($v_num_lotes > $cont) {
		$tpl->newBlock('lote');
		$tpl->assign('num_lote', ($cont+1));
		$tpl->assign('dt_inicial', $di);
		$tpl->assign('dt_final', $df);
		$tpl->assign('cd_plano', $pl);
		$tpl->assign('cd_empresa', $emp);
		$cont = $cont + 1;
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
//--------------------------------------------------------------
function convdata_br_iso($dt) {
	// Pressupõe que a data esteja no formato DD/MM/AAAA
	// A melhor forma de gravar datas no PostgreSQL é utilizando 
	// uma string no formato DDDD-MM-AA. Esta função justamente 
	// adequa a data a este formato
	$d = substr($dt, 0, 2);
	$m = substr($dt, 3, 2);
	$a = substr($dt, 6, 4);
	return $a.'-'.$m.'-'.$d;
}
//--------------------------------------------------------------	
?>