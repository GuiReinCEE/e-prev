<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_etapa_projeto.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	if (($D <> 'GAD') and ($Z <> 191)) {
   		header('location: acesso_restrito.php?IMG=banner_etapa_projeto');
	}
//--------------------------------------------------------------	
	$tpl->newBlock('cadastro');
	$tpl->assign('codigo', $c);
	if (isset($c)) {
		$sql =        " select nome from projetos.projetos where codigo = $c  " ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('nome_projeto', $reg['nome']);
	}
	$tpl->assign('cor_fundo_1', $v_cor_fundo1);
	$tpl->assign('cor_fundo_2', $v_cor_fundo2);
	if (isset($cd_etapa))	{
		$sql =        " select cd_etapa, nome_etapa, desc_etapa, to_char(dt_etapa, 'DD/MM/YYYY') as dt_etapa, etapa_anterior, situacao_etapa ";
		$sql = $sql . " from projetos.etapas_projeto where cd_projeto = $c and cd_etapa = $cd_etapa " ;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('cd_etapa', $reg['cd_etapa']);
		$tpl->assign('nome', $reg['nome_etapa']);
		$tpl->assign('dt_etapa', $reg['dt_etapa']);
		$etapa_anterior = $reg['etapa_anterior'];
		$situacao_etapa = $reg['situacao_etapa'];
	}
//------------------------------------------------------------------------------------------- Combo Etapa Anterior
	$sql = "SELECT cd_etapa, nome_etapa from projetos.etapas_projeto where cd_projeto = $c order by cd_etapa";
	$rs = pg_exec($db, $sql);
	$tpl->newBlock('cbo_etapa_anterior');
	$tpl->assign('cd_etapa_anterior', '0');
	$tpl->assign('nome_etapa_anterior', 'Nenhuma');
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_etapa_anterior');
		$tpl->assign('cd_etapa_anterior', $reg['cd_etapa']);
		$tpl->assign('nome_etapa_anterior', $reg['nome_etapa']);
		$tpl->assign('chk_etapa_anterior', ($reg['cd_etapa'] == $etapa_anterior ? ' selected' : ''));
	}
//------------------------------------------------------------------------------------------- Combo Tipo Manutenção
	$sql = "SELECT codigo, descricao FROM listas WHERE categoria='SIEP' ORDER BY descricao";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cbo_etapa');
		$tpl->assign('cd_situacao', $reg['codigo']);
		$tpl->assign('nome_situacao', $reg['descricao']);
		$tpl->assign('chk_situacao', ($reg['codigo'] == $situacao_etapa ? ' selected' : ''));
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>