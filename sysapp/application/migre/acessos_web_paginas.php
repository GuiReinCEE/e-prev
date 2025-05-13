<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_acessos_web_paginas.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
// --------------------------------------------------------- inicialização do skin das telas:
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
// ---------------------------------------------------------

	// ABAS - BEGIN
	$abas[] = array('aba_periodo', 'Por Período', false, 'aba_periodo()');
	$abas[] = array('aba_pagina', 'Por Página', true, '');
	$abas[] = array('aba_outras', 'Outras Informações', false, 'aba_outras()');
	$tpl->assignGlobal( 'ABA_START', aba_start( $abas ) );
	$tpl->assignGlobal( 'ABA_END', aba_end('') );
	// ABAS - END

	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->assign('dt_inicial', $dt_inicial);
	$tpl->assign('dt_final', $dt_final);
	$txt_dt_inicial	= ( $dt_inicial    == '' ? 'Null' : convdata_br_iso($dt_inicial));
	$txt_dt_final	= ( $dt_final    == '' ? 'Null' : convdata_br_iso($dt_final));
//------------------------------------------------------------------------------------------- DEFAULT.HTM
	$tpl->newBlock('lista');
	$sql =        " select distinct count(pagina) as num_acessos_def, ";
	$sql = $sql . " pagina ";
	$sql = $sql . " from conta_acessos ";
	$sql = $sql . " where data_hora > '2004-07-04' and pagina = 'DEFAULT.HTM' ";
	if ($txt_dt_inicial <> 'Null')
	{
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null')
	{
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";
	}
	$sql = $sql . " group by pagina ";
	$sql = $sql . " order by num_acessos_def desc, pagina ";
//-------------------------------------------------------------------------------------------------------------------
	$rs_def=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs_def))
	{
		$tpl->newBlock('default');
		$cont = $cont + 1;
		
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', '#F4F4F4');
		}
		else {
			$tpl->assign('cor_fundo', '#FAFAFA');
		}		
		$tpl->assign('data', $reg['pagina']);
		$tpl->assign('numero_acessos_default', $reg['num_acessos_def']);
		$tpl->assign('numero_acessos_default_10', ($reg['num_acessos_def'] / 50));
		
	}
//------------------------------------------------------------------------------------------- INSTITUCIONAL
	$sql =        " select count(pagina) as num_acessos ";
	$sql = $sql . " from conta_acessos ";
	$sql = $sql . " where data_hora > '2004-07-04' and pagina <> 'DEFAULT.HTM' ";
	if ($txt_dt_inicial <> 'Null')
	{
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null')
	{
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";
	}
	$rs=pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$tot_acessos = $reg['num_acessos'];
//-------------------------------------------------------------------------------------------------------------------
	$sql =        " select distinct count(pagina) as num_acessos, ";
	$sql = $sql . " pagina ";
	$sql = $sql . " from conta_acessos ";
	$sql = $sql . " where data_hora > '2004-07-04' and pagina <> 'DEFAULT.HTM' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (data_hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (data_hora <= '$txt_dt_final') ";
	}
	$sql = $sql . " group by pagina ";
	$sql = $sql . " order by num_acessos desc, pagina ";
//-------------------------------------------------------------------------------------------------------------------
	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs))
	{
		$tpl->newBlock('projetos');
		$cont = $cont + 1;

		if(($cont % 2) <> 0)
		{
			$tpl->assign('cor_fundo', '#F4F4F4');
		}
		else
		{
			$tpl->assign('cor_fundo', '#FAFAFA');
		}
		$tpl->assign('data', $reg['pagina']);
		$tpl->assign('numero_acessos', $reg['num_acessos']);
		$tpl->assign('numero_acessos', $reg['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg['num_acessos'] . 10) / $tot_acessos)). "%)" );
		$tpl->assign('largura_acessos', $reg['num_acessos']/10);
	}
	

//------------------------------------------------------------------------------------- AUTO-ATENDIMENTO
	$sql =        " select count(pagina) as num_acessos ";
	$sql = $sql . " from log_acessos_usuario ";
	$sql = $sql . " where hora > '2002-02-25' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (hora <= '$txt_dt_final') ";
	}
	$rs_aa=pg_query($db, $sql);
	$reg=pg_fetch_array($rs_aa);
	$tot_acessos = $reg['num_acessos'];
	
//-------------------------------------------------------------------------------------------------------------------
	$sql =        " select distinct count(pagina) as num_acessos, ";
	$sql = $sql . " pagina ";
	$sql = $sql . " from log_acessos_usuario ";
	$sql = $sql . " where hora > '2002-02-25' ";
	if ($txt_dt_inicial <> 'Null') {
		$sql = $sql . "and (hora >= '$txt_dt_inicial') ";
	}
	if ($txt_dt_final <> 'Null') {
		$sql = $sql . "and (hora <= '$txt_dt_final') ";
	}
	$sql = $sql . " group by pagina ";
	$sql = $sql . " order by num_acessos desc, pagina ";
//-------------------------------------------------------------------------------------------------------------------
	$rs_aa=pg_exec($db, $sql);
	$cont = 0;
	while ($reg_aa=pg_fetch_array($rs_aa)) {
		$tpl->newBlock('auto_atendimento');
		$cont = $cont + 1;
	
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo_aa', '#F4F4F4');
		}
		else {
			$tpl->assign('cor_fundo_aa', '#FAFAFA');
		}
		$tpl->assign('data_aa', $reg_aa['pagina']);
		$tpl->assign('numero_acessos_aa', $reg_aa['num_acessos']);
		$tpl->assign('numero_acessos_aa', $reg_aa['num_acessos'] . " (" . ( sprintf("%3.2f", ($reg_aa['num_acessos'] . 10) / $tot_acessos)). "%)" );
		$tpl->assign('largura_acessos_aa', $reg_aa['num_acessos']/50);
	}
	$tpl->newBlock('auto_atendimento_total');
	$tpl->assign('numero_acessos_aa', $tot_acessos);
	
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