<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	$ano = date('Y');
// ------------------------------------------------
	$sql = 		"	select max(cd_lote) as num_lote from eleicoes.lotes_apuracao_eleicoes ";
	$sql = $sql . " where 	ano_eleicao = $ano ";
	$sql = $sql . " and		cd_eleicao = 1 ";		
	$rs	= pg_exec($db, $sql);
	$reg = pg_fetch_array($rs);
	$este_lote = ($reg['num_lote'] + 1);
// ------------------------------------------------
	while(list($key, $value) = each($HTTP_POST_VARS)) 
	{ 
		$v_str = $key;
		if (substr_count($v_str, "votos_") > 0) {
			$m = fnc_soma_votos($v_str, $db, $value);
			$m = fnc_lanca_lotes($v_str, $db, $value, $este_lote, $Z);
		}
	}
// ------------------------------------------------
	header('location: lanca_votos.php?msg=Lote '.$este_lote.' lançado com sucesso!');
//-----------------------------------------------------------------------------------------------
function fnc_soma_votos($cand, $db, $voto) {
	$emp = substr($cand, 6, 1);
	$seq = substr($cand, 8, 1);
	$re = substr($cand, 10, 6);
	if (isset($cand)) {
		if (is_numeric($re)) {
			$sql = 		"	select num_votos from eleicoes.apuracao_eleicoes ";
			$sql = $sql . " where 	cd_registro_empregado = $re ";
			$sql = $sql . " and		cd_empresa = $emp ";		
			$sql = $sql . " and		seq_dependencia	= $seq ";
			$rs	=	pg_exec($db, $sql);
			$reg=pg_fetch_array($rs);
			if (is_numeric($voto)) { }
			else {
				return $ret;
			}
		
			if (pg_numrows($rs) <> 0) {
				$sql =        " update	eleicoes.apuracao_eleicoes ";
				$sql = $sql . " set 	num_votos = num_votos + $voto  ";
				$sql = $sql . " where 	cd_registro_empregado = $re ";
				$sql = $sql . " and		cd_empresa = $emp ";		
				$sql = $sql . " and		seq_dependencia	= $seq ";
			}
			else {
				$sql =        " insert 	into	eleicoes.apuracao_eleicoes ( ";
				$sql = $sql . "        cd_empresa, ";
				$sql = $sql . "        cd_registro_empregado, ";
				$sql = $sql . "        seq_dependencia, ";
				$sql = $sql . "        ano_eleicao , ";
				$sql = $sql . "        cd_eleicao, ";
				$sql = $sql . "        num_votos ) ";
				$sql = $sql . " values ( ";
				$sql = $sql . "        $emp, ";
				$sql = $sql . "        $re, ";
				$sql = $sql . "        $seq, ";
				$sql = $sql . "        2006, ";
				$sql = $sql . "        1, ";
				$sql = $sql . "        $voto ) ";
			}
			$s = (pg_exec($db, $sql));
		}
	}
	return $ret;
}
// ------------------------------------------------------------------------------- Lança o lote de votos:
function fnc_lanca_lotes($cand, $db, $voto, $este_lote, $usuario) {
	$emp = substr($cand, 6, 1);
	$seq = substr($cand, 8, 1);
	$re = substr($cand, 10, 6);
// ------------------------------------------------------------------------------- 
	$hora_lancamento = date("Y-m-d H:i:s");
	if (isset($cand)) {
		if (is_numeric($re)) {
			if (is_numeric($voto)) {
				$sql =        " insert 	into	eleicoes.lotes_apuracao_eleicoes ( ";
				$sql = $sql . "        	ano_eleicao , ";
				$sql = $sql . "        	cd_eleicao, ";
				$sql = $sql . "        	cd_lote, ";
				$sql = $sql . "        	cd_empresa, ";
				$sql = $sql . "        	cd_registro_empregado, ";
				$sql = $sql . "        	seq_dependencia, ";
				$sql = $sql . "        	num_votos, ";
				$sql = $sql . "        	dt_hora_lancamento, ";
				$sql = $sql . "        	usu_lancamento ) ";
				$sql = $sql . " values 	( ";
				$sql = $sql . "        	2006, ";
				$sql = $sql . "        	1, ";
				$sql = $sql . "			$este_lote, ";
				$sql = $sql . "        	$emp, ";
				$sql = $sql . "        	$re, ";
				$sql = $sql . "        	$seq, ";
				$sql = $sql . "        	$voto, ";
				$sql = $sql . "        	'$hora_lancamento', ";
				$sql = $sql . $usuario . ")" ;
				$s = (pg_exec($db, $sql));
			}
		}
	}
	return $ret;
}

?>