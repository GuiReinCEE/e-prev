<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
	
	$sql = " delete from projetos.previsoes_projetos ";
	$sql = $sql . "	where cd_acomp = $ac and cd_previsao = $re ";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_acomp_projetos.php?tr=U&c='.$ac);
	}
	else {
		pg_close($db);
		header('location: cad_acomp_projetos.php?tr=U&msg=ocorreu um erro ao tentar excluir esta reunião&c='.$ac);
	}
?>