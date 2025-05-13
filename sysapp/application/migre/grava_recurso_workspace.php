<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
//---------------------------------------------------------------------------------
	$sql =        " update projetos.usuarios_controledi  			";
	$sql = $sql . " set	tela_inicial = '$cbo_tela_inicial', ";
	$sql = $sql . " favorito2 = '$cbo_favorito2', favorito3 = '$cbo_favorito3', favorito4 = '$cbo_favorito4', favorito5 = '$cbo_favorito5', ";
	$sql = $sql . " dash1 = '$cbo_dash1', dash2 = '$cbo_dash2', dash3 = '$cbo_dash3', dash4 = '$cbo_dash4', dash5 = '$cbo_dash5', dash6 = '$cbo_dash6', dash7 = '$cbo_dash7'";
	$sql = $sql . " where codigo = $codigo   ";
//	echo $sql;
	if (!($rs=pg_exec($db, $sql))) {
		pg_close($db);
		header('location: cad_recurso.php?msg=Ocorreu um erro ao tentar gravar o REGISTRO.');
	}
	else {
		pg_close($db);
		header('location:lst_recursos.php');
	}
?>