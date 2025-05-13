<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
	
	if ($codigo<>"") {
		$sql =        " update projetos.eventos_institucionais ";
		$sql = $sql . " set dt_inicio 			= '$data',  ";
		$sql = $sql . " 	cd_tipo 			= '$tipo_evento', ";
		$sql = $sql . " 	nome 				= '$nome', ";
		$sql = $sql . " 	tipo_divulgacao 	= '$cd_divulgacao', ";
		$sql = $sql . " 	cd_cidade 			= '$cidade', ";
		$sql = $sql . " 	local_evento 		= '$local', ";
		$sql = $sql . "     dt_alteracao 		= current_timestamp, ";
		$sql = $sql . "		dt_fim				= '$dt_fim', ";
		$sql = $sql . "		email_lembrete		= '$lembrete', ";
		$sql = $sql . "		lembrete_1hora 		= '$opt_1hora', ";
		$sql = $sql . "		lembrete_vespera	= '$opt_1dia',";
		$sql = $sql . "		texto_lembrete		= '$texto_lembrete',";
		$sql = $sql . "		agenda 				= '$opc_agenda' ";  
		$sql = $sql . " where cd_evento 		= $codigo         ";
	}
	else {
		$sql =        " insert 	into projetos.eventos_institucionais ( ";
		$sql = $sql . "        	dt_inicio , ";
		$sql = $sql . " 		cd_tipo, ";
		$sql = $sql . " 		nome, ";
		$sql = $sql . "        	tipo_divulgacao, ";
		$sql = $sql . "        	cd_cidade, ";
		$sql = $sql . "        	local_evento, ";
		$sql = $sql . "        	dt_alteracao, ";
		$sql = $sql . "			dt_fim, ";
		$sql = $sql . "			email_lembrete, ";
		$sql = $sql . "			lembrete_1hora, ";
		$sql = $sql . "			lembrete_vespera, ";
		$sql = $sql . "			texto_lembrete, ";
		$sql = $sql . "			agenda ) ";  
		$sql = $sql . " values ( ";
		$sql = $sql . "        	'$data', ";
		$sql = $sql . "			'$tipo_evento', ";
		$sql = $sql . "        	'$nome', ";
		$sql = $sql . "        	'E', ";
		$sql = $sql . "        	'$cidade', ";
		$sql = $sql . "        	'$local', ";
		$sql = $sql . "        	current_timestamp, ";
		$sql = $sql . "			'$dt_fim', ";
		$sql = $sql . "			'$lembrete', ";
		$sql = $sql . "			'$opt_1hora', ";
		$sql = $sql . "			'$opt_1dia', ";
		$sql = $sql . "			'$texto_lembrete', ";
		$sql = $sql . "			'$opc_agenda' ) ";  
	}

	if(!$rs=pg_query($db, $sql))
	{
		pg_close($db);
		header('location: lst_eventos_institucionais.php?msg=Ocorreu um erro ao tentar gravar o evento.');
	}
	if ($codigo=="") {
		$sql = "select max(cd_evento) as cd_evento from projetos.eventos_institucionais ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$codigo = $reg['cd_evento'];
	}
	$sql = " delete from projetos.eventos_publicos where cd_evento = $codigo ";
	$s = (pg_query($db, $sql));
	while(list($key, $value) = each($HTTP_POST_VARS)) 
	{ 
		$v_str = $key;
		if (substr_count($v_str, "chk_publico") != 0) {
			$m = fnc_grava_eventos_publicos($codigo, $db, $value);
		}
	} 
	pg_close($db);
	header('location: lst_eventos_institucionais.php');
function fnc_grava_eventos_publicos($cd_evento, $db, $cd_publico) {
	if (isset($cd_evento)) {
			$sql = 			" insert into projetos.eventos_publicos (";
			$sql = $sql . 	" cd_publico, cd_evento ";
	    	$sql = $sql . 	" ) ";
	    	$sql = $sql . 	" VALUES ( ";
			$sql = $sql . 	" '" . $cd_publico . "', $cd_evento ";
    		$sql = $sql . 	")";
			$s = (pg_exec($db, $sql));
	}
	return $ret;
}
//-----------------------------------------------------------------------------------------------
?>