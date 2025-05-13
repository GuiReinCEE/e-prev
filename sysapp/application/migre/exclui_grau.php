<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
// ------------------------------------------------------------------------------------	
	$sql = " update projetos.escala_proficiencia set dt_exclusao = current_timestamp where cd_escala='".$cd_escala."' and cd_origem ='".$cd_origem."'"; 
// ------------------------------------------------------------------------------------
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_escala_proficiencia.php?origem='.$cd_origem);
	}
	else {
		pg_close($db);
		header('location: lst_escala_proficiencia.php?origem='.$cd_origem.'&msg=ocorreu um erro ao tentar excluir este grau.');
	}
// ------------------------------------------------------------------------------------
?>