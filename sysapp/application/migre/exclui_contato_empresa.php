<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	
	$sql = " update expansao.contatos_empresa set dt_exclusao = current_timestamp, cd_usu_exclusao = $Z ";
	$sql = $sql . "	where cd_contato = $cont and cd_empresa = $emp";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_empresas_contatos.php?c='.$emp);
	}
	else {
		pg_close($db);
		header('location: cad_empresas_contatos.php?c='.$emp.'&msg=Ocorreu um erro ao excluir esta enquete');
	}
?>