<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cad_versao_site.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);
	if (isset($c))	{		// 2 situaчѕes: ou щ uma nova versуo de um site existente ou щ um novo site
		$sql =        " select cd_site, cd_versao, situacao, endereco, ";
		$sql = $sql . "        to_char(dt_versao, 'DD/MM/YYYY') as data_inc, ";
		$sql = $sql . "        to_char(dt_exclusao, 'DD/MM/YYYY') as data_exc, ";
		$sql = $sql . "        tit_capa, texto_capa, destaque1, link_destaque1, destaque2, link_destaque2, destaque3, link_destaque3 ";
		$sql = $sql . " from   projetos.root_site ";
		$sql = $sql . " where  cd_versao   = $c ";
		$sql = $sql . " and  cd_site   = $cs ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_site', $reg['cd_site']);		
		$tpl->assign('cd_versao', $reg['cd_versao']);
		$tpl->assign('tit_capa', $reg['tit_capa']);
		$tpl->assign('conteudo', prepara_conteudo_html($reg['texto_capa']));
		$tpl->assign('dt_inclusao', $reg['data_inc']);
		$tpl->assign('dt_exclusao', $reg['data_exc']);
		$tpl->assign('destaque1', $reg['destaque1']);
		$tpl->assign('destaque2', $reg['destaque2']);
		$tpl->assign('destaque3', $reg['destaque3']);
		$tpl->assign('link1', $reg['link_destaque1']);
		$tpl->assign('link2', $reg['link_destaque2']);
		$tpl->assign('link3', $reg['link_destaque3']);
		$tpl->assign('endereco', $reg['endereco']);
		$situacao = $reg['situacao'];
		$cd_versao = $reg['cd_versao'];
	} elseif (isset($cs)) {
		$sql =        " select cd_site,   ";
		$sql = $sql . "        to_char(dt_exclusao, 'DD/MM/YYYY') as data_exc, ";
		$sql = $sql . "        tit_capa ";
		$sql = $sql . " from   projetos.root_site ";
		$sql = $sql . " where  cd_site   = $cs ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_site', $reg['cd_site']);		
		$tpl->assign('tit_capa', $reg['tit_capa']);
		$tpl->assign('dt_inclusao',  $date);
		$tpl->assign('dt_exclusao', $reg['data_exc']);
		$sql =        " select max(cd_versao) as cd_versao ";
		$sql = $sql . " from   projetos.root_site where cd_site = $cs ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_versao', ($reg['cd_versao'] + 1));
		$date = date("d/m/Y");
		$tpl->assign('dt_inclusao',  $date);
		$cd_versao = $reg['cd_versao'];
	} else {
		$tpl->assign('cd_site', 1);
		$sql =        " select max(cd_site) as cd_site ";
		$sql = $sql . " from   projetos.root_site ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_site', ($reg['cd_site'] + 1));
		$date = date("d/m/Y");
		$tpl->assign('dt_inclusao',  $date);
	}
// ----------------------------------------------------------
	if ($op == 'A') {
		$n = 'U';
	} else {
		$n = 'I';
	}
	$tpl->assign('insere', $n);
// ----------------------------------------------------------
	$sql =        " select codigo, descricao ";
	$sql = $sql . " from   listas where categoria = 'SITE' ";
//		echo $sql;
	$rs = pg_exec($db, $sql);
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('situacao');
		$tpl->assign('cd_situacao', $reg['codigo']);
		$tpl->assign('desc_situacao', $reg['descricao']);
		if ($reg['codigo'] == $situacao) {
			$tpl->assign('chk_situacao', 'selected');
		}
	}
// ----------------------------------------------------------
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