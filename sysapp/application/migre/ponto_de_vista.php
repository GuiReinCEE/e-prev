<?
	include_once('inc/conexao.php');
	include_once('inc/sessao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	header( 'location:'.base_url().'index.php/ecrm/informativo_cenario_legal/ponto_vista/'.(isset($ed) ? $ed : ''));
   
	$tpl = new TemplatePower('tpl/tpl_ponto_de_vista.html');
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
		$sql = $sql . " from   projetos.cenario where cd_secao = 'PVST' and cd_edicao = $ed";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$c = $reg['cd_cenario'];
	}

	$sql =        " select cd_cenario, titulo, conteudo  ";
	$sql = $sql . " from   projetos.cenario ";
	$sql = $sql . " where  cd_edicao = $ed and cd_secao = 'PVST'";
	$rs = pg_exec($db, $sql);
	$reg=pg_fetch_array($rs);
	$tpl->assign('titulo', $reg['titulo']);
	$tpl->newBlock('cadastro');
	$tpl->assign('cd_cenario', $reg['cd_cenario']);		
	$tpl->assign('conteudo',  str_replace(chr(13).chr(10), '<br>', $reg['conteudo']));
	$cd_cenario = $reg['cd_cenario'];
	$cd_usuario = $reg['cd_usuario'];
//---------------------------------------------------------------------
	$sql =        " select cd_cenario, titulo, referencia, area_indicada ";
	$sql = $sql . " from   projetos.cenario ";
	$sql = $sql . " where  cd_cenario not in (select cd_cenario from projetos.cenario where dt_exclusao > '2000-01-01') ";
	$sql = $sql . " and cd_secao = 'LGIN'  and cd_edicao = $ed";
	$sql = $sql . " order by cd_cenario ";
	$rs = pg_exec($db, $sql);
    while ($reg = pg_fetch_array($rs))
	{
		$tpl->newBlock('cadastro2');
		$tpl->assign('titulo', $reg['titulo']);
		$tpl->assign('referencia', $reg['referencia']);
		$tpl->assign('fonte', $reg['fonte']);
		$tpl->assign('area_indicada', $reg['area_indicada']);
		$tpl->assign('cd_cenario', $reg['cd_cenario']);
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