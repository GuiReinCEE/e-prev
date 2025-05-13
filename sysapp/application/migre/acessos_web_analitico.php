<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_acessos_web_analitico.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------

	// ABAS - BEGIN
	$abas[] = array('aba_periodo', 'Por Período', false, 'aba_periodo()');
	$abas[] = array('aba_pagina', 'Por Página', false, 'aba_pagina()');
	$abas[] = array('aba_outras', 'Outras Informações', true, 'aba_outras()');
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end('') );
	// ABAS - END

	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('dt_inicial', $dt_inicial);
	$tpl->assign('dt_final', $dt_final);
	$txt_dt_inicial	= ( $dt_inicial    == '' ? 'Null' : convdata_br_iso($dt_inicial));
	$txt_dt_final	= ( $dt_final    == '' ? 'Null' : convdata_br_iso($dt_final));
// ----------------------------------------------------- Windows NT 4.0:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Windows NT 4%' or  so like '%Windows 95' or so like '%Windows 98%' or so like '%NT 5.0%' or  so like '%NT 5.1%' or  so like '%Linux%'  ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tot_so =  $reg['num_acessos'];
// ----------------------------------------------------- Windows XP:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%NT 5.1%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Windows XP');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Windows 98:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Windows 98%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Windows 98');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Windows 2000:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%NT 5.0%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Windows 2000');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Windows NT 4.0:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Windows NT 4%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Windows NT 4');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Windows 95:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Windows 95%' or  so like '%Windows 95' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Windows 95');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Linux:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Linux%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Linux');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- MAC:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Mac %' or so like '%Mac_%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Mac');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- WAP:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%WAP%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'WAP (celular)');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
////////////////////////////////////////////////////
// ----------------------------------------------------- MSIE 4:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%MSIE 4%' or  so like '%MSIE 5%' or  so like '%MSIE 6%' or  so like '%Firefox%' or  so like '%Opera%' or  so like '%Safari%' or so like '%Netscape%'  ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tot_so =  $reg['num_acessos'];
// ----------------------------------------------------- MSIE 6:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%MSIE 6%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Internet Explorer 6.XX');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Firefox:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Firefox%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Firefox');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- MSIE 5:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%MSIE 5%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Internet Explorer 5.XX');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- MSIE 4:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%MSIE 4%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Internet Explorer 4.XX');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Opera:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Opera%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Ópera');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Netscape:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Netscape%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Netscape');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));
// ----------------------------------------------------- Safari:
	$sql =        " select COUNT(*) as num_acessos ";
	$sql = $sql . " from conta_acessos where so like '%Safari%' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";           
	}
	$rs_def=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs_def);
	$tpl->newBlock('auto_atendimento');
	$tpl->assign('data_aa', 'Safari');
	$tpl->assign('numero_acessos_aa', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_so)). "%)" );
	$tpl->assign('largura_acessos_aa', ($reg['num_acessos'] / 25));

	pg_close($db);
//------------------------------------------------------------------------------------------------------------------------
	$tpl->printToScreen();
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
?>