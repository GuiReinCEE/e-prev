<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lst_candidatos.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------
	$sql =        " select 	indic_07 ";
	$sql = $sql . " from   	projetos.usuarios_controledi ";
	$sql = $sql . " where 	codigo = $Z ";
	$rs = pg_exec($db, $sql);
	if ($reg=pg_fetch_array($rs)) 
	{
		if( ($reg['indic_07'] == ' ') or ($reg['indic_07'] == '')) {
			header("location: acesso_restrito.php?IMG=banner_candidatos"); 
		}
	}	 
//--------------------------------------------------------------	
	$tpl->newBlock('candidato');
	$sql =        " select 	ce.cd_empresa, ce.cd_registro_empregado, ce.seq_dependencia, p.nome, ce.cd_cargo, ee.nome as cargo ";
	$sql = $sql . " from   	eleicoes.candidatos_eleicoes ce, participantes p, eleicoes.cargos_eleicoes ee ";
	$sql = $sql . " where	ce.cd_empresa = p.cd_empresa and ce.cd_registro_empregado = p.cd_registro_empregado ";
	$sql = $sql . " and 	ce.seq_dependencia = p.seq_dependencia "; //and length(trim(ce.logradouro)) > 32";		
	$sql = $sql . " and 	ce.cd_cargo = ee.cd_cargo "; //and length(trim(ce.logradouro)) > 32";		
	$sql = $sql . " order by posicao, cd_cargo, nome desc "; 

	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('candidato');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) {
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else {
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('cd_empresa', $reg['cd_empresa']);
		$tpl->assign('cd_registro_empregado', $reg['cd_registro_empregado']);
		$tpl->assign('seq_dependencia', $reg['seq_dependencia']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('cargo', $reg['cargo']);
		$total = $total + 1;
	}
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>