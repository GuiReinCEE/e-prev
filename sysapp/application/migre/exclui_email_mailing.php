<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
// ------------------------------------------------------------------------------------	
	$sql = " update expansao.mailing_email set dt_exclusao = current_timestamp where cd_email=".$cd_email." and cd_mailing = ".$cd_mailing; 
// ------------------------------------------------------------------------------------
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_mailing.php?c='.$cd_mailing);
	}
	else {
		pg_close($db);
		header('location: cad_mailing.php?c='.$cd_mailing.'&msg=ocorreu um erro ao tentar excluir este email.');
	}
// ------------------------------------------------------------------------------------
?>