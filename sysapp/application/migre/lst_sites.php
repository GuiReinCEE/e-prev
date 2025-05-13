<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	header('location:'.base_url().'index.php/ecrm/site');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_sites.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
   
   	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GRI') and ($D <> 'GAP') and ($D <> 'GI')) {
   		header('location: acesso_restrito.php?IMG=banner_senge');
	}

	$tpl->newBlock('lista');
	$sql =        " select cd_site, cd_versao, endereco, ";
	$sql = $sql . "                 tit_capa, situacao, l.descricao as desc_situacao, ";
	$sql = $sql . "                 to_char(dt_versao, 'DD/MM/YYYY') as data_cad, ";
	$sql = $sql . "                 to_char(l.dt_exclusao, 'DD/MM/YYYY') as data_exc ";
	$sql = $sql . " from   projetos.root_site, listas l  ";
	$sql = $sql . " where situacao = l.codigo and l.categoria = 'SITE' and l.dt_exclusao is null ";
	$sql = $sql . " order by cd_site, cd_versao desc ";

	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('projetos');
			if ($lin == 'P') {
			$lin = 'I';
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		} else {
			$lin = 'P';
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$cont = $cont + 1;
		$tpl->assign('cd_versao',$reg['cd_versao']);
		$tpl->assign('tit_capa', $reg['tit_capa']);
		$tpl->assign('dt_cadastro', $reg['data_cad']);
		$tpl->assign('dt_exclusao', $reg['data_exc']);
		$tpl->assign('endereco', $reg['endereco']);
		$tpl->assign('situacao', $reg['desc_situacao']);
		$tpl->assign('cd_site', $reg['cd_site']);
		if (($reg['situacao'] != 'INAT') and ($reg['situacao'] != 'REDI')) {
			$tpl->newBlock('link_conteudo');
			$tpl->assign('cd_versao',$reg['cd_versao']);
			$tpl->assign('cd_site', $reg['cd_site']);
		}
	}
	pg_close($db);
	$tpl->printToScreen();	
