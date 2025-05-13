<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/funcoes.php');

	if ($codproj<>"") {
		$sql =        " update projetos.projetos                  ";
		$sql = $sql . " set nome            = '$sistema',         ";
		$sql = $sql . "     descricao       = '$descricao',       ";
		$sql = $sql . "     area            = '$area',            ";	  
		$sql = $sql . "     nivel           = '$nivel',           ";
		$sql = $sql . "     administrador1  = '$administrador1',  ";
		$sql = $sql . "     administrador2  = '$administrador2',  ";
		$sql = $sql . "     atendente       		= '$responsavel', 	";
		$sql = $sql . " 	analista_responsavel 	= '$analista', 		";
		$sql = $sql . "		programa_institucional 	= '$programa',		";
		$sql = $sql . "		cod_projeto_superior 	= '$projeto_superior', ";
		$sql = $sql . "		tipo			= '$opt_tipo',		  ";
		if ($data_implantacao <> '') {
			$sql = $sql . "  data_implantacao = '".dt_br_to_iso($data_implantacao)."',  ";
		}
		$sql = $sql . "     diretriz         = '$diretriz'   ";
		$sql = $sql . " where codigo = $codproj              ";
//echo "update :".$sql."<br>";	  
	}
	else {
		$sql =        " insert into projetos.projetos ( ";
		$sql = $sql . "        nome ,                ";
		$sql = $sql . "        descricao ,           ";
		$sql = $sql . "        area ,                ";
		$sql = $sql . "        nivel,                ";
		$sql = $sql . "        administrador1,       ";
		$sql = $sql . "        administrador2,       ";
		$sql = $sql . "        atendente,            ";
		$sql = $sql . "        diretriz,             ";
		$sql = $sql . " 	analista_responsavel, ";
		$sql = $sql . "		programa_institucional, ";
		$sql = $sql . "		cod_projeto_superior, ";
		$sql = $sql . "        tipo,	               ";
		if ($data_implantacao <> '') {
			$sql = $sql . "        data_implantacao,     ";
		}
		$sql = $sql . "        data_cad )            ";
		$sql = $sql . " values 	(                     ";
		$sql = $sql . "        	'$sistema',           ";
		$sql = $sql . "        	'$descricao',         ";
		$sql = $sql . "        	'$area',              ";
		$sql = $sql . "        	'$nivel',             ";
		$sql = $sql . "        	'$administrador1',    ";
		$sql = $sql . "        	'$administrador2',    ";
		$sql = $sql . "     	'$responsavel', 	";
		$sql = $sql . "        	'$diretriz',          ";
		$sql = $sql . " 		'$analista', 		";
		$sql = $sql . " 		'$programa',		";
		$sql = $sql . "		 	'$projeto_superior', ";
		$sql = $sql . " 	 	'$opt_tipo', 			";
		if ($data_implantacao <> '') {
			$sql = $sql . "        '".dt_br_to_iso($data_implantacao)."',  ";
		}
		$sql = $sql . "        now() )               ";
	}
//	echo $sql;

	if ($rs=pg_exec($db, $sql)) {
		if ($cod_proj != "") {
			$sql2 = $sql2 . " update projetos.projetos ";
			$sql2 = $sql2 . " set cod_projeto_dependente   = $prj_dependente ";
			$sql2 = $sql2 . " where codigo                 = $cod_proj ";
			$rs2 = pg_exec($db,$sql2);
		}
		else {
			$sql =        " select codigo ";
			$sql = $sql . " from projetos.projetos ";
			$sql = $sql . " where nome = '$sistema' ";
	  //echo "<br>".$sql;
			$rs = pg_exec($db,$sql);
			$reg = pg_fetch_array($rs);
//         if (($prj_dependente != 'null') and (! is_null($reg['codigo']))) {
			if (($prj_dependente != "") and (! is_null($reg['codigo']))) {
				$sql2 = $sql2 . " update projetos.projetos ";
				$sql2 = $sql2 . " set cod_projeto_dependente   = $prj_dependente ";
				$sql2 = $sql2 . " where codigo                 = " . $reg['codigo'];
				echo "<br> 2  ".$sql2;
				$rs2 = pg_exec($db,$sql2);
			} 
		} 
		if ($opt_tipo == 'S') {
			pg_close($db);
			header('location: lst_sistemas.php');
		} else {
			pg_close($db);
			header('location: lst_projetos.php');
		}
	} elseif ($opt_tipo == 'S') {
		pg_close($db);
		header('location: lst_sistemas.php?msg=Ocorreu um erro ao tentar gravar o projeto.');
	} else {
		pg_close($db);
		header('location: lst_projetos.php?msg=Ocorreu um erro ao tentar gravar o projeto.');
	}

	function dt_br_to_iso($dt) {
	   $dia = substr($dt,0,2);
       $mes = substr($dt,3,2);
       $ano = substr($dt,6,4);
       return "$ano-$mes-$dia";  
	}
?>