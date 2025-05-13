<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($num_apurados == '') { $num_apurados = 'null'; }
	if ($num_votos == '') { $num_votos = 'null'; }
// ------------------------------------------------ Verifica posiчуo anterior:
	if ($codigo<>"") {
		$sql = " select situacao, dt_hr_abertura, dt_hr_fechamento from eleicoes.eleicao where ano_eleicao = $ano and cd_eleicao = $codigo";
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$v_sit_ant = $reg['situacao'];
		$v_dt_hr_abertura = $reg['dt_hr_abertura'];
		$v_dt_hr_fechamento = $reg ['dt_hr_fechamento'];
		$sql =        " update eleicoes.eleicao    ";
		$sql = $sql . " set  	nome = '$nome', ";
		$sql = $sql . "     	situacao = '$opt_eleicao', ";
		$sql = $sql . "     	votos_apurados = $num_apurados, ";
		$sql = $sql . "     	num_votos = $num_votos, ";
		if ($v_sit_ant != $opt_eleicao) {
			if ($opt_eleicao == 'A') {
				$sql = $sql . " dt_hr_abertura = current_timestamp, ";
			}
			else {
				$sql = $sql . " dt_hr_fechamento = current_timestamp, ";
			}
		}		
		$sql = $sql . "     	modalidade = '$cbo_tlei' ";
		$sql = $sql . " where 	ano_eleicao = $ano ";
		$sql = $sql . " and 	cd_eleicao = $codigo ";
	}
	else {
		$sql =        " insert 	eleicoes.eleicao ( ";
		$sql = $sql . "        	ano_eleicao , ";
		$sql = $sql . "        	cd_eleicao , ";
		$sql = $sql . "        	nome , ";
		$sql = $sql . "        	situacao , ";
		$sql = $sql . "        	votos_apurados, ";
		$sql = $sql . "        	num_votos, ";
		$sql = $sql . "			dt_hr_abertura, ";
		$sql = $sql . "			dt_hr_fechamento, ";
		$sql = $sql . "        	modalidade ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        	$ano, ";
		$sql = $sql . "        	$codigo, ";
		$sql = $sql . "        	'$nome', ";
		$sql = $sql . "        	'$opt_eleicao', ";
		$sql = $sql . "        	$num_apurados, ";
		$sql = $sql . "        	$num_votos, ";
		$sql = $sql . " 		current_timestamp, ";
		$sql = $sql . " 		current_timestamp, ";
		$sql = $sql . "        	'$cbo_tlei') ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_adm_eleicoes.php');
	}
	else {
		pg_close($db);
		header('location: lst_adm_eleicoes.php?msg=Ocorreu um erro ao tentar gravar esta eleiчуo.');
	}
?>