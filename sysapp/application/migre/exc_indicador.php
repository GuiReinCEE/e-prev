<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.Email.inc.php');
	
	$origem = $os;
	$date = date("Y-m-d H:m:s");	
	$sql =			" update acs.indicadores ";
	$sql = $sql . " set dt_exclusao = '$date' ";
	$sql = $sql . 	" where codigo = '$c' and ano = $ano and mes = $mes ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_indicadores.php');
	}	
	else {
		pg_close($db);
		echo "Ocorreu um erro ao tentar excluir este indicador";
	}
?>