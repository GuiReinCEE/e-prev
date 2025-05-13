<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update projetos.cargos    ";
		$sql = $sql . " set nome_cargo = '$nome',  ";
		$sql = $sql . "     desc_cargo = '$descricao', ";
		$sql = $sql . "     cd_familia = $cbo_familia ";
		$sql = $sql . " where cd_cargo = $codigo         ";
	}
	else {
		$sql =        " insert into projetos.cargos ( ";
		$sql = $sql . "        nome_cargo , ";
		$sql = $sql . "        desc_cargo , ";
		$sql = $sql . "        cd_familia ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        '$nome', ";
		$sql = $sql . "        '$descricao', ";
		$sql = $sql . "        $cbo_familia) ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
	}
	else {
		pg_close($db);
		header('location: lst_cargos.php?msg=Ocorreu um erro ao tentar gravar o cargo.');
	}
//------------------------ excluir todas as competências deste cargo...
	if ($codigo<>"") {
		$sql = " delete from projetos.cargos_comp_espec where cd_cargo = $codigo ";
//		echo $sql;
		$s = (pg_exec($db, $sql));
		$sql = " delete from projetos.cargos_comp_inst where cd_cargo = $codigo ";
//		echo $sql;
//		$s = (pg_exec($db, $sql));
//		$sql = " delete from projetos.cargos_escolaridade where cd_cargo = $codigo ";
//		echo $sql;
		$s = (pg_exec($db, $sql));
		$sql = " delete from projetos.cargos_responsabilidades where cd_cargo = $codigo ";
//		echo $sql;
		$s = (pg_exec($db, $sql));
				
		while(list($key, $value) = each($HTTP_POST_VARS)) 
		{ 
			$v_str = $key;
			if (strpos($v_str, "comp_espec") > 0) {
				$m = fnc_grava_comp_espec($codigo, $db, $value);
			}
			if (strpos($v_str, "comp_inst") > 0) {
				$m = fnc_grava_comp_inst($codigo, $db, $value);
			}
//			if (strpos($v_str, "escolaridade") > 0) {
//				$m = fnc_grava_escolaridade($codigo, $db, $value);
//			}
			if (strpos($v_str, "esponsabilidade") > 0) {
				$m = fnc_grava_responsabilidade($codigo, $db, $value);
			}
		} 
	}
	pg_close($db);
	header('location: lst_cargos.php');
//-----------------------------------------------------------------------------------------------
//function fnc_grava_escolaridade($cd_cargo, $db, $cd_escolaridade) {
//	if (isset($cd_cargo)) {
//		if (is_numeric($cd_escolaridade)) {
//			$sql = 			" insert into projetos.cargos_escolaridade (";
//			$sql = $sql . 	" cd_cargo, cd_escolaridade ";
//	    	$sql = $sql . 	" ) ";
//	    	$sql = $sql . 	" VALUES ( ";
//			$sql = $sql . 	" $cd_cargo, $cd_escolaridade ";
//   		$sql = $sql . 	")";
//			$s = (pg_exec($db, $sql));
//		}
//	}
//	return $ret;
//}
//-----------------------------------------------------------------------------------------------
function fnc_grava_comp_espec($cd_cargo, $db, $cd_comp_espec) {
	if (isset($cd_cargo)) {
		if (is_numeric($cd_comp_espec)) {
			$sql = 			" insert into projetos.cargos_comp_espec (";
			$sql = $sql . 	" cd_cargo, cd_comp_espec ";
	    	$sql = $sql . 	" ) ";
	    	$sql = $sql . 	" VALUES ( ";
			$sql = $sql . 	" $cd_cargo, $cd_comp_espec ";
    		$sql = $sql . 	")";
			$s = (pg_exec($db, $sql));
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_comp_inst($cd_cargo, $db, $cd_comp_inst) {
	if (isset($cd_cargo)) {
		if (is_numeric($cd_comp_inst)) {
			$sql = 			" insert into projetos.cargos_comp_inst (";
			$sql = $sql . 	" cd_cargo, cd_comp_inst ";
	    	$sql = $sql . 	" ) ";
	    	$sql = $sql . 	" VALUES ( ";
			$sql = $sql . 	" $cd_cargo, $cd_comp_inst ";
    		$sql = $sql . 	")";
			$s = (pg_exec($db, $sql));
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
function fnc_grava_responsabilidade($cd_cargo, $db, $cd_responsabilidade) {
	if (isset($cd_cargo)) {
		if (is_numeric($cd_responsabilidade)) {
			$sql = 			" insert into projetos.cargos_responsabilidades (";
			$sql = $sql . 	" cd_cargo, cd_responsabilidade ";
	    	$sql = $sql . 	" ) ";
	    	$sql = $sql . 	" VALUES ( ";
			$sql = $sql . 	" $cd_cargo, $cd_responsabilidade ";
    		$sql = $sql . 	")";
			$s = (pg_exec($db, $sql));
		}
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
?>