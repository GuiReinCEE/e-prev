<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	
	$sql = " update expansao.empresas_instituicoes set dt_exclusao = current_timestamp ";
	$sql = $sql . "	where cd_emp_inst = $c ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_empresas.php');
	}
	else {
		pg_close($db);
		header('location: cad_empresas.php?c=$c&msg=Ocorreu um erro ao excluir esta empresa');
	}
?>