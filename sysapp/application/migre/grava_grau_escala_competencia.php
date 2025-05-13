<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');
// ------------------------------------------------
	if ($codigo<>"") {
		$sql =        " update projetos.escala_proficiencia    ";
		$sql = $sql . " set descricao = '$descricao' ";
		$sql = $sql . " where cd_origem = '$origem' and cd_escala = '$cd_escala' ";
	}
	else {
		$sql =        " insert into projetos.escala_proficiencia ( ";
		$sql = $sql . "        cd_origem , ";
		$sql = $sql . "        cd_escala , ";
		$sql = $sql . "        descricao ) ";
		$sql = $sql . " values ( ";
		$sql = $sql . "        '$origem', ";
		$sql = $sql . "        '$cd_escala', ";
		$sql = $sql . "        '$descricao') ";
	}
// ------------------------------------------------
	if ($rs=pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_escala_proficiencia.php?origem='.$origem);
	}
	else {
		pg_close($db);
		header('location: lst_escala_proficiencia.php?origem='.$origem.'&msg=Ocorreu um erro ao tentar gravar a escala de competencia.');
	}
?>