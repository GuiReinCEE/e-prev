<?
	include_once('inc/conexao.php');
	include_once('inc/sessao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	header( 'location:'.base_url().'index.php/ecrm/informativo_cenario_legal/capa/'.(isset($ed) ? $ed : '').'/'.(isset($c) ? $c : ''));
   
	$tpl = new TemplatePower('tpl/tpl_legislacao_integra.html');
	$tpl->prepare();
//---------------------------------------------------------------------
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
//---------------------------------------------------------------------
	if (isset($c)) { }
	else {
		$sql =        " select max(cd_cenario) as cd_cenario ";
		$sql = $sql . " from   projetos.cenario where cd_secao = 'LGIN'";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$c = $reg['cd_cenario'];
	}

	$sql =        " select cd_cenario, titulo, conteudo, arquivo_associado, ";
	$sql = $sql . "        to_char(dt_inclusao, 'DD/MM/YYYY HH24:MM') as data_inc, ";
	$sql = $sql . "        to_char(dt_exclusao, 'DD/MM/YYYY') as data_exc, ";
	$sql = $sql . "        cd_usuario, imagem, link1, link2, link3, link4, referencia, fonte, area_indicada, ";
	$sql = $sql . "			indic_aa, indic_acs, indic_aj, indic_da, indic_dap, indic_db, indic_dcg, indic_df, indic_di, indic_die, indic_din, indic_drh, indic_sg ";			
	$sql = $sql . " from   projetos.cenario ";
	$sql = $sql . " where  cd_cenario   = $c and cd_secao = 'LGIN' and cd_edicao = $ed";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$tpl->assign('titulo', $reg['titulo']);
	$tpl->newBlock('cadastro');
	$tpl->assign('cd_cenario', $reg['cd_cenario']);		
	$tpl->assign('conteudo',  $reg['conteudo']);
//	$tpl->assign('conteudo',  str_replace(chr(13).chr(10), '<br>', $reg['conteudo']));
	if ($reg['imagem'] <> '') {
		$v_imagem = "<img src='http://www.e-prev.com.br/upload/".$reg['imagem']."'>";
		$tpl->assign('imagem', $v_imagem);
	}
	if ($reg['arquivo_associado'] <> '') {
		$v_arq = "http://www.e-prev.com.br/upload/".$reg['arquivo_associado'];
		$tpl->assign('link_arquivo', $v_arq);
		$tpl->assign('arquivo', $reg['arquivo_associado']);
	}
	$tpl->assign('referencia', $reg['referencia']);
	$tpl->assign('fonte', $reg['fonte']);
	if ($reg['indic_aa'] == 'S') { $areas_indicadas = (' ' . 'GA'); }
	if ($reg['indic_acs'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GRI'; }
	if ($reg['indic_aj'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GJ'; }
	if ($reg['indic_da'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GAD'; }
	if ($reg['indic_dap'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GAP'; }
	if ($reg['indic_db'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GB'; }
	if ($reg['indic_dcg'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GC'; }
	if ($reg['indic_df'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GF'; }
	if ($reg['indic_di'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GI'; }
	if ($reg['indic_die'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DIE'; }
	if ($reg['indic_din'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GIN'; }
	if ($reg['indic_drh'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GAD'; }
	if ($reg['indic_sg'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'SG'; }
	$tpl->assign('area_indicada', $areas_indicadas);
	$tpl->assign('link1', $reg['link1']);
	$tpl->assign('link2', $reg['link2']);
	$tpl->assign('link3', $reg['link3']);
	$tpl->assign('link4', $reg['link4']);
	$tpl->assign('dt_hora_inclusao', $reg['data_inc']);
	$tpl->assign('dt_exclusao', $reg['data_exc']);		 
	$cd_cenario = $reg['cd_cenario'];
	$cd_usuario = $reg['cd_usuario'];
//---------------------------------------------------------------------
	$sql =        " select cd_cenario, titulo, referencia, area_indicada, ";
	$sql = $sql . "			indic_aa, indic_acs, indic_aj, indic_da, indic_dap, indic_db, indic_dcg, indic_df, indic_di, indic_die, indic_din, indic_drh, indic_sg ";			
	$sql = $sql . " from   projetos.cenario ";
	$sql = $sql . " where  cd_cenario not in (select cd_cenario from projetos.cenario where dt_exclusao > '2000-01-01') ";
	$sql = $sql . " and cd_secao = 'LGIN' and cd_edicao = $ed ";
	$sql = $sql . " order by cd_cenario ";
	$rs = pg_exec($db, $sql);
    while ($reg = pg_fetch_array($rs))
	{
		$tpl->newBlock('cadastro2');
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('referencia', $reg['referencia']);
		$tpl->assign('fonte', $reg['fonte']);
		$areas_indicadas = '';
		if ($reg['indic_aa'] == 'S') { $areas_indicadas = (' ' . 'GA'); }
		if ($reg['indic_acs'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GRI'; }
		if ($reg['indic_aj'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GJ'; }
		if ($reg['indic_da'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GAD'; }
		if ($reg['indic_dap'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GAP'; }
		if ($reg['indic_db'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GB'; }
		if ($reg['indic_dcg'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GC'; }
		if ($reg['indic_df'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GF'; }
		if ($reg['indic_di'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GI'; }
		if ($reg['indic_die'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'DIE'; }
		if ($reg['indic_din'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GIN'; }
		if ($reg['indic_drh'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'GAD'; }
		if ($reg['indic_sg'] == 'S') { $areas_indicadas = $areas_indicadas . ' ' . 'SG'; }
		$tpl->assign('area_indicada', $areas_indicadas);
		$tpl->assign('cd_cenario', $reg['cd_cenario']);
		$tpl->assign('ed', $ed);
	}
//------------------------------------------------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
// ----------------------------------------------------------
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