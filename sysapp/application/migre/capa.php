<?
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cenario_capa.html');	
	$tpl->prepare();
	if (isset($ed)) {
		$sql =        " select cd_edicao, tit_capa ";
		$sql = $sql . " from   projetos.edicao_cenario ";
		$sql = $sql . " where  cd_edicao = $ed ";
	 } 
	 else {
		$sql =        " select cd_edicao, tit_capa ";
		$sql = $sql . " from   projetos.edicao_cenario ";
		$sql = $sql . " where  cd_edicao in (select max(cd_edicao) from projetos.edicao_cenario where dt_exclusao is null) ";
	}
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	$tpl->assign('cd_edicao', $reg['cd_edicao']);
	$tpl->assign('edicao', $reg['tit_capa']);
	$ed = $reg['cd_edicao'];
	$tpl->assign('ed', $ed);
//------------------------------------------------------------------------------------------------------
//	$tpl->assign('n', $n);
//	$tpl->assign('cd_edicao', $ed);
//	$tpl->assign('edicao', $D);
//---------------------------------------------------------------------
	$cd_cenario = $reg['cd_cenario'];
	$cd_usuario = $reg['cd_usuario'];
//---------------------------------------------------------------------
	$sql =        " select cd_cenario, titulo, referencia, ";
	$sql = $sql . "			indic_aa, indic_acs, indic_aj, indic_da, indic_dap, indic_db, indic_dcg, indic_df, indic_di, indic_die, indic_din, indic_drh, indic_sg ";			
	$sql = $sql . " from   projetos.cenario ";
	$sql = $sql . " where  cd_cenario not in (select cd_cenario from projetos.cenario where dt_exclusao > '2000-01-01') ";
	$sql = $sql . "	and cd_secao = 'LGIN'  and cd_edicao = $ed";
	$sql = $sql . " order by cd_cenario ";
	$rs = pg_exec($db, $sql);
    while ($reg = pg_fetch_array($rs))
	{
		$tpl->newBlock('cadastro2');
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('cd_cenario', $reg['cd_cenario']);
		$tpl->assign('referencia', $reg['referencia']);
		$areas_indicadas = '';
		if ($reg['indic_aa'] == 'S') { $areas_indicadas = (' ' . 'AA'); }
		if ($reg['indic_acs'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'ACS'; }
		if ($reg['indic_aj'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'AJ'; }
		if ($reg['indic_da'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DA'; }
		if ($reg['indic_dap'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DAP'; }
		if ($reg['indic_db'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DB'; }
		if ($reg['indic_dcg'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DCG'; }
		if ($reg['indic_df'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DF'; }
		if ($reg['indic_di'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DI'; }
		if ($reg['indic_die'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DIE'; }
		if ($reg['indic_din'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DIN'; }
		if ($reg['indic_drh'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DRH'; }
		if ($reg['indic_sg'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'SG'; }
		$tpl->assign('area_indicada', $areas_indicadas);
		$tpl->assign('ed', $ed);
	}
//------------------------------------------------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
// ----------------------------------------------------------
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
?>