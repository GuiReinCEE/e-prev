<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update projetos.comp_inst    ";
		$sql = $sql . " set nome_comp_inst = '$nome',  ";
		$sql = $sql . "     desc_comp_inst = '$descricao' ";
		$sql = $sql . " where cd_comp_inst = $codigo         ";
	}
	else {
		$sql =        " insert into projetos.comp_inst ( ";
		$sql = $sql . "        nome_comp_inst , ";
		$sql = $sql . "        desc_comp_inst ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        '$nome', ";
		$sql = $sql . "        '$descricao') ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_comp_inst.php?msg=Ocorreu um erro ao tentar gravar a competência institucional.');
	}
	else {
		pg_close($db);
		header('location: lst_comp_inst.php');
	}
?>