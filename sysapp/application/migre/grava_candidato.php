<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update	eleicoes.candidatos_eleicoes ";
		$sql = $sql . " set 	nome = '$nome_resumido',  ";
		$sql = $sql . "     	cd_cargo = $cbo_cargo, ";
		$sql = $sql . "     	posicao = $posicao ";
		$sql = $sql . " where 	cd_empresa = $cd_empresa ";
		$sql = $sql . " and		cd_registro_empregado = $cd_registro_empregado ";
		$sql = $sql . " and		seq_dependencia	= $seq_dependencia ";
	}
	else {
		$sql =        " insert into eleicoes.candidatos_eleicoes ( ";
		$sql = $sql . "        cd_empresa, ";
		$sql = $sql . "        cd_registro_empregado, ";
		$sql = $sql . "        seq_dependencia, ";
		$sql = $sql . "        nome , ";
		$sql = $sql . "        cd_cargo, ";
		$sql = $sql . "        posicao ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        $cd_empresa, ";
		$sql = $sql . "        $cd_registro_empregado, ";
		$sql = $sql . "        $seq_dependencia, ";
		$sql = $sql . "        '$nome_resumido', ";
		$sql = $sql . "        $cbo_cargo, ";
		$sql = $sql . "        $posicao ) ";
	}
//	echo $sql;
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_candidatos.php');
	}
	else {
		pg_close($db);
		header('location: lst_candidatos.php?msg=Ocorreu um erro ao tentar gravar este registro.');
	}
?>