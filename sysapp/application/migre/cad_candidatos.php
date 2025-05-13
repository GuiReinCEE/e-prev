<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_cad_candidatos.html');
//-----------------------------------------------   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//-----------------------------------------------
	$tpl->newBlock('cadastro');
	$tpl->assign('cor_fundo1', $v_cor_fundo1);
	$tpl->assign('cor_fundo2', $v_cor_fundo2);

	if (isset($cre))	{
		$sql =        " select 	ce.cd_empresa, ce.cd_registro_empregado, ce.seq_dependencia, p.nome as nome, ce.nome as nome_resumido, ";
		$sql = $sql . " 		ce.cd_cargo, ce.posicao "; 
		$sql = $sql . " from 	eleicoes.candidatos_eleicoes ce, participantes p ";
		$sql = $sql . " where 	ce.cd_empresa = $cemp and ce.cd_registro_empregado = $cre and ce.seq_dependencia = $cseq ";
		$sql = $sql . " and 	ce.cd_empresa = p.cd_empresa ";
		$sql = $sql . " and		ce.cd_registro_empregado = p.cd_registro_empregado ";
		$sql = $sql . " and		ce.seq_dependencia = p.seq_dependencia ";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('codigo', $reg['cd_empresa']);
		$tpl->assign('cd_empresa', $reg['cd_empresa']);
		$tpl->assign('cd_registro_empregado', $reg['cd_registro_empregado']);
		$tpl->assign('seq_dependencia', $reg['seq_dependencia']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('posicao', $reg['posicao']);
		$tpl->assign('nome_resumido', $reg['nome_resumido']);
		$v_cargo = $reg['cd_cargo'];
	}
//---------------------------------------------------------------------------------------------- Combo Cargos
	$tpl->newBlock('cargo');
	$tpl->assign('cd_cargo', '');
	$tpl->assign('nome_cargo', '');
	$sql = "SELECT cd_cargo, nome FROM eleicoes.cargos_eleicoes WHERE dt_exclusao is null ORDER BY nome";
	$rs = pg_exec($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		$tpl->newBlock('cargo');
		$tpl->assign('cd_cargo', $reg['cd_cargo']);
		$tpl->assign('nome_cargo', $reg['nome']);
		$tpl->assign('chk_cargo', ($reg['cd_cargo'] == $v_cargo ? ' selected' : ''));
	}
//-----------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>