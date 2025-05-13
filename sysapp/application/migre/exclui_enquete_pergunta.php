<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	
	$sql = " update projetos.enquete_perguntas set dt_exclusao = current_timestamp, cd_usu_exclusao = ".$Z;
	$sql = $sql . "	where cd_enquete = $c and cd_pergunta = $p";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_enquetes.php?c='.$c);
	}
	else {
		pg_close($db);
		header('location: cad_enquetes.php?c='.$c.'&msg=Ocorreu um erro ao excluir esta enquete');
	}
?>