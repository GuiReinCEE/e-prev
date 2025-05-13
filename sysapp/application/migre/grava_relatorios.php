<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
//---------------------------------------------------------------------------------
	$v_pos = strpos($cbo_tabela, '.');
	$esquema = substr($cbo_tabela, 0, $v_pos);
	$tabela = substr($cbo_tabela, ($v_pos + 1), strlen($cbo_tabela));
// --------------------------------------------------------------------
	if ($proprietario <> '') {
		$sql = "	select 	divisao	from projetos.usuarios_controledi ";
		$sql = $sql . " 	where 	codigo = $proprietario ";
		$rs=pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$v_divisao = $reg['divisao'];
	}
// -------------------------------------------------------------------	
	if ($colunas == '') { $colunas = 0;	}
	if ($sistema == '') { $sistema = 0; }
	if ($codigo <> "") {
		$sql =        " update projetos.relatorios  		";
		$sql = $sql . " set	cd_usuario				= $Z, ";
		$sql = $sql . "		esquema					= '$esquema', ";
		$sql = $sql . "		tabela					= '$tabela', ";
		$sql = $sql . "		query					= '$query',	";
		$sql = $sql . "		clausula_where			= '$clausula_where', ";	  
		$sql = $sql . "		titulo					= '$titulo', ";
		$sql = $sql . "		ordem					= '$ordem', ";
		$sql = $sql . "		grupo					= '$grupo', ";
		$sql = $sql . "		tipo					= '$tipo_rel', ";
		$sql = $sql . "		fonte					= '$fonte', ";
		$sql = $sql . "		num_colunas				= $colunas, ";
		$sql = $sql . "		divisao					= '$v_divisao', ";
		$sql = $sql . "		restricao_acesso		= '$restricao_acesso', ";
		$sql = $sql . "		cd_proprietario			= $proprietario, ";
		$sql = $sql . "		dt_atualizacao			= current_timestamp,	";	  
		$sql = $sql . " 	pos_x 					= $pos_x, ";
		$sql = $sql . "		largura					= $largura, ";
		$sql = $sql . " 	mostrar_sombreamento	= '$mostrar_sombreamento', ";
		$sql = $sql . "		tam_fonte				= $tam_fonte, ";
		$sql = $sql . "		tam_fonte_titulo		= $tam_fonte_titulo, ";
		$sql = $sql . "		mostrar_cabecalho		= '$mostrar_cabecalhos', ";
		$sql = $sql . "		mostrar_linhas			= '$mostrar_linhas', ";	
		$sql = $sql . "		orientacao 				= '$alin_relatorio', ";
		$sql = $sql . "		cd_projeto 				= $sistema, ";
		$sql = $sql . "		especie 				= '$especie' ";
		$sql = $sql . " where cd_relatorio 	= $codigo   	";
	}
	else 
	{
        $sql =        " insert 	into projetos.relatorios ( ";
        $sql = $sql . "        	cd_usuario ,    	";
        $sql = $sql . "        	esquema, ";
        $sql = $sql . "        	tabela , ";		
		$sql = $sql . "			query,	";
		$sql = $sql . "			clausula_where, ";	  
		$sql = $sql . "			titulo, ";
		$sql = $sql . "			ordem, ";
		$sql = $sql . "			grupo, ";
		$sql = $sql . "			tipo, ";
		$sql = $sql . "			fonte, ";
		$sql = $sql . "			num_colunas, ";
		$sql = $sql . "			divisao, ";
		$sql = $sql . "			restricao_acesso, ";
		$sql = $sql . "			cd_proprietario, ";
		$sql = $sql . "			dt_atualizacao,	";	  
		$sql = $sql . "        	dt_criacao, ";
		$sql = $sql . " 		pos_x, ";
		$sql = $sql . "			largura, ";
		$sql = $sql . " 		mostrar_sombreamento, ";
		$sql = $sql . "			tam_fonte, ";
		$sql = $sql . "			tam_fonte_titulo, ";
		$sql = $sql . "			mostrar_cabecalho, ";
		$sql = $sql . "			mostrar_linhas, ";
		$sql = $sql . "			orientacao, ";
		$sql = $sql . "			cd_projeto, ";
		$sql = $sql . "			especie ) ";
        $sql = $sql . " values ($Z, ";
        $sql = $sql . "        '$esquema', ";
        $sql = $sql . "        '$tabela', ";
		$sql = $sql . "			'$query', ";
		$sql = $sql . "			'$clausula_where', ";	  
		$sql = $sql . "			'$titulo', ";
		$sql = $sql . "			'$ordem', ";
		$sql = $sql . "			'$grupo', ";
		$sql = $sql . "			'$tipo_rel', ";
		$sql = $sql . "			'$fonte', ";
		$sql = $sql . "			$colunas, ";
		$sql = $sql . "			'$v_divisao', ";
		$sql = $sql . "			'$restricao_acesso', ";
		$sql = $sql . "			$proprietario, ";
		$sql = $sql . "			current_timestamp, ";	  
        $sql = $sql . "        	current_date, ";
		$sql = $sql . " 		$pos_x, ";
		$sql = $sql . "			$largura, ";
		$sql = $sql . " 		'$mostrar_sombreamento', ";
		$sql = $sql . "			$tam_fonte, ";
		$sql = $sql . "			$tam_fonte_titulo, ";
		$sql = $sql . "			'$mostrar_cabecalhos', ";
		$sql = $sql . "			'$mostrar_linhas', ";
		$sql = $sql . "			'$alin_relatorio', ";
		$sql = $sql . "			$sistema, ";
		$sql = $sql . "			'$especie') ";
	}
//---------------------------------------------------------------------------------	
//	echo $sql;
	if ($rs=pg_exec($db, $sql)) {	
		if ($codigo == '') {
			$sql =        " select max(cd_relatorio) as c from projetos.relatorios where cd_usuario = $Z";
			$rs = pg_exec($db, $sql);
			$reg = pg_fetch_array($rs);
			$c = $reg['c'];
		}
		else {
			$c = $codigo;
		}
//---------------------------------------------------------------------------------	Colunas
		$sql =        " delete 	from projetos.relatorios_colunas where ";
		$sql = $sql . "			cd_relatorio = $c ";
		$s = (pg_exec($db, $sql));						
		while(list($key, $value) = each($HTTP_POST_VARS)) 
		{ 
			$v_str = $key;
			if (substr_count($v_str, "cabec") > 0) {
				$v_cabec = $v_str;
				$val_cabec = $value;
			}
			if (substr_count($v_str, "alin_col") > 0) {
				$v_col = $v_str;
				$val_alin = $value;
			}
			if (substr_count($v_str, "larg_col") > 0) {
				$v_col2 = $v_str;
				$val_larg = $value;
				$m = fnc_grava_colunas($c, $db, $v_str, $value, $v_cabec, $val_cabec, $val_alin, $val_larg);
			}
		} 
		pg_close($db);
		header('location: cad_relatorios.php?c='.$c);
	}
	else {
	   pg_close($db);
	   header('location:lst_relatorios.php?msg=Ocorreu um erro ao tentar gravar o relatorio.');
	}
//---------------------------------------------------------------------------------	Colunas
function fnc_grava_colunas($cd_relatorio, $db, $v_str, $valor, $v_cabec, $val_cabec, $val_alin, $val_larg) {
	$cd_coluna = substr($v_cabec,5,1);
	if ($valor != '') {
		$sql =        " insert into projetos.relatorios_colunas ( ";
		$sql = $sql . "        cd_relatorio, ";
		$sql = $sql . "        cd_coluna, ";
		$sql = $sql . "        nome_coluna, ";
		$sql = $sql . "        alinhamento, ";
		$sql = $sql . "        largura )";
		$sql = $sql . " values ( ";
		$sql = $sql . "        $cd_relatorio, ";
		$sql = $sql . "        $cd_coluna, ";
		$sql = $sql . "        '$val_cabec', ";
		$sql = $sql . "        '$val_alin', ";
		$sql = $sql . "        $val_larg ) ";
		$s = (pg_exec($db, $sql));
	}
	return $ret;
}
?>