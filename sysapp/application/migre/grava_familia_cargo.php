<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update projetos.familias_cargos    ";
		$sql = $sql . " set nome_familia = '$familia',  ";
		$sql = $sql . " 	usu_alteracao = $Z, ";
		$sql = $sql . "     dt_alteracao = current_date ";
		$sql = $sql . " where cd_familia = $codigo         ";
	}
	else {
		$sql =        " insert 	into projetos.familias_cargos ( ";
		$sql = $sql . "        	nome_familia , ";
		$sql = $sql . " 		usu_alteracao, ";
		$sql = $sql . "        	dt_inclusao ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        '$familia', ";
		$sql = $sql . "			$Z, ";
		$sql = $sql . "        	current_date) ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
	}
	else {
		pg_close($db);
		header('location: lst_familias.php?msg=Ocorreu um erro ao tentar gravar o cargo.');
	}
	if ($codigo=="") {
		$sql = "select max(cd_familia) as cd_familia from projetos.familias_cargos ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$codigo = $reg['cd_familia'];
	}
// ------------------------------------------------
	$sql = " delete from projetos.familias_escolaridades where cd_familia = $codigo ";
//		echo $sql;
	$s = (pg_exec($db, $sql));
	while(list($key, $value) = each($HTTP_POST_VARS)) 
	{ 
		$v_str = $key;
//		echo $v_str . '-' .$value. '<br>';
		if (strpos($v_str, "perc_") > 0) {
			$v_percentual = $value;
		}
		elseif (strpos($v_str, "grau_") > 0) {
			$v_escolaridade = str_replace("sel_grau_", "", $v_str);
			$m = fnc_grava_escolaridade($codigo, $db, $value, $v_escolaridade, $v_percentual);
		}
	} 
//------------------------ 
	pg_close($db);
	header('location: lst_familias.php');
//-----------------------------------------------------------------------------------------------
function fnc_grava_escolaridade($cd_familia, $db, $nivel, $cd_escolaridade, $grau_percentual) {
//	echo $cd_familia . ' ' . $nivel . ' ' . $cd_escolaridade . ' '. $grau_percentual . '<br>';
	if (isset($cd_familia)) {
		if (is_numeric($grau_percentual)) {
			$sql = 			" insert into projetos.familias_escolaridades (";
			$sql = $sql . 	" cd_familia, cd_escolaridade, grau_percentual, nivel ";
	    	$sql = $sql . 	" ) ";
	    	$sql = $sql . 	" VALUES ( ";
			$sql = $sql . 	" $cd_familia, $cd_escolaridade, $grau_percentual, '" . $nivel . "' ";
    		$sql = $sql . 	")";
//			echo $sql . '<br>';
			$s = (pg_exec($db, $sql));
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
?>