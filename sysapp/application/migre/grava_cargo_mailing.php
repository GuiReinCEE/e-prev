<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update expansao.cargos_mailing    ";
		$sql = $sql . " set descricao = '$descricao' ";
		$sql = $sql . " where cd_cargo = $codigo ";
	}
	else {
		$sql =        " insert into expansao.cargos_mailing ( ";
		$sql = $sql . "        descricao ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        '$descricao') ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
	}
	else {
		pg_close($db);
		header('location: lst_mailing.php?msg=Ocorreu um erro ao tentar gravar o cargo.');
	}
	pg_close($db);
	header('location: cad_mailing.php?c='.$cd_mailing);
//-----------------------------------------------------------------------------------------------
?>