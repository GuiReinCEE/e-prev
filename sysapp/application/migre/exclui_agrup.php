<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
// ------------------------------------------------------------------------------------	
	$sql = " delete from projetos.usuarios_agrupamentos where cd_usuario = $Z "; 
// ------------------------------------------------------------------------------------
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_agrupamento.php');
	}
	else {
		pg_close($db);
		header('location: lst_agrupamento.php?tr=U&msg=ocorreu um erro ao tentar excluir este agrupamento.');
	}
// ------------------------------------------------------------------------------------
?>