<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	
	$sql = " update projetos.enquetes set dt_exclusao = current_timestamp ";
	$sql = $sql . "	where cd_enquete = $c ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_enquetes.php');
	}
	else {
		pg_close($db);
		header('location: cad_enquetes.php?c=$c&msg=Ocorreu um erro ao excluir esta enquete');
	}
?>