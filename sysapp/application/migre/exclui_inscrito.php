<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	
	$sql = " update expansao.inscritos set dt_exclusao = current_timestamp ";
	$sql = $sql . "	where cd_empresa = $emp and cd_registro_empregado = $re and cd_sequencia = $seq ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_inscritos.php');
	}
	else {
		pg_close($db);
		header('location: cad_inscritos.php?c=$re&tr=U&msg=Ocorreu um erro ao excluir este participante');
	}
?>