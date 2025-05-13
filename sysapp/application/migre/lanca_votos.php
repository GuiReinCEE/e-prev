<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_lanca_votos.html');
//--------------------------------------------------------------
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
//--------------------------------
	if ($msg <> '') {
		$tpl->assign('msg', $msg);
	}
	$sql =        " select 	indic_07 ";
	$sql = $sql . " from   	projetos.usuarios_controledi ";
	$sql = $sql . " where 	codigo = $Z ";
	$rs = pg_exec($db, $sql);
	if ($reg=pg_fetch_array($rs)) 
	{
		if (($reg['indic_07'] == ' ') or ($reg['indic_07'] == '')) {
			header("location: acesso_restrito.php?IMG=banner_candidatos"); 
		}
	}	 
//--------------------------------------------------------------
	if ($ano == '') { $ano = date('Y'); }
	if ($c == '') { $c = 1; }	
	$sql =        " select 	situacao ";
	$sql = $sql . " from   	eleicoes.eleicao ";
	$sql = $sql . " where 	ano_eleicao = $ano and cd_eleicao = $c";
	$rs = pg_exec($db, $sql);
	if ($reg=pg_fetch_array($rs)) 
	{
		if ($reg['situacao'] == 'F') {
			$tpl->assign('msg', 'A eleição está fechada. Não é permitido lançar votos.');
			$tpl->assign('dis_fechada', 'disabled');
			$v_fechada = 'S';
		}
	}	 
	else {
		$v_fechada = 'S';
		$tpl->assign('dis_fechada', 'disabled');
	}
//--------------------------------------------------------------	
	$sql =        " select 	ce.cd_empresa, ce.cd_registro_empregado, ce.seq_dependencia, ce.nome, ce.cd_cargo, ee.nome as cargo ";
	$sql = $sql . " from   	eleicoes.candidatos_eleicoes ce, participantes p, eleicoes.cargos_eleicoes ee ";
	$sql = $sql . " where	ce.cd_empresa = p.cd_empresa and ((ce.cd_registro_empregado = p.cd_registro_empregado) or (ce.cd_registro_empregado = 0) or (ce.cd_registro_empregado = 999999)) ";
	$sql = $sql . " and		ce.cd_cargo in (11, 21, 31, 10, 20, 30, 19, 29, 39) ";
	$sql = $sql . " and 	ce.seq_dependencia = p.seq_dependencia "; //and length(trim(ce.logradouro)) > 32";		
	$sql = $sql . " and 	ce.cd_cargo = ee.cd_cargo "; //and length(trim(ce.logradouro)) > 32";		
	$sql = $sql . " order by posicao, cd_cargo, nome desc "; 

	$sql =        " select 	ce.cd_empresa, ce.cd_registro_empregado, ce.seq_dependencia, ce.nome, ce.cd_cargo, ee.nome as cargo ";
	$sql = $sql . " from   	eleicoes.candidatos_eleicoes ce, eleicoes.cargos_eleicoes ee ";
	$sql = $sql . " where	ce.ano_eleicao = 2006 AND ce.cd_cargo in (11, 21, 31, 10, 20, 30, 19, 29, 39) ";
	$sql = $sql . " and 	ce.cd_cargo = ee.cd_cargo "; //and length(trim(ce.logradouro)) > 32";		
	$sql = $sql . " order by posicao, cd_cargo, nome desc "; 

	$rs=pg_exec($db, $sql);
	$cont = 0;
	while ($reg=pg_fetch_array($rs)) {
		$tpl->newBlock('candidato');
		$cont = $cont + 1;
//		if (($cont % 2) <> 0) {
//			$tpl->assign('cor_fundo', $v_cor_fundo1);
//		}
//		else {
//			$tpl->assign('cor_fundo', $v_cor_fundo2);
//		}
		if ($v_fechada == 'S') {
			$tpl->assign('ro_fechada', 'readonly');
		}
		$tpl->assign('cd_empresa', $reg['cd_empresa']);
		$tpl->assign('cd_registro_empregado', $reg['cd_registro_empregado']);
		$tpl->assign('seq_dependencia', $reg['seq_dependencia']);
		$tpl->assign('nome', $reg['nome']);
		$tpl->assign('cargo', $reg['cargo']);
		$total = $total + 1;
		$sql2 =        " select 	num_votos ";
		$sql2 = $sql2 . " from   	eleicoes.apuracao_eleicoes ";
		$sql2 = $sql2 . " where		cd_empresa = ".$reg['cd_empresa'] ;
		$sql2 = $sql2 . " and 		cd_registro_empregado = ".$reg['cd_registro_empregado'] ;
		$sql2 = $sql2 . " and 		seq_dependencia = ".$reg['seq_dependencia'] ;
		$rs2=pg_exec($db, $sql2);
		$reg2=pg_fetch_array($rs2);
		if (substr($reg['cd_cargo'],0,1) == '1'){
			$tpl->assign('cor_fundo', '#B5DEC7');
			$v_num_votos1 = ($v_num_votos1 + $reg2['num_votos']);
		}
		elseif (substr($reg['cd_cargo'],0,1) == '2'){
			$tpl->assign('cor_fundo', '#DCDCDC');
			$v_num_votos2 = ($v_num_votos2 + $reg2['num_votos']);
		}
		elseif (substr($reg['cd_cargo'],0,1) == '3'){
			$tpl->assign('cor_fundo', '#F0E8BA');
			$v_num_votos3 = ($v_num_votos3 + $reg2['num_votos']);
		}
		$tpl->assign('total', $reg2['num_votos']);
		$tpl->assign('v_num_votos1', $v_num_votos1);
		$tpl->assign('v_num_votos2', $v_num_votos2);
		$tpl->assign('v_num_votos3', $v_num_votos3);
	}
		
//--------------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();	
?>