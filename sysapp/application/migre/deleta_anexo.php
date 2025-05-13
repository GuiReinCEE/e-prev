<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.Email.inc.php');
//print_r($_REQUEST);
	$origem = $os;
	$sql 		= "delete from projetos.anexos_atividades ";
	$sql = $sql . " where 	cd_atividade = $n ";
	$sql = $sql . " and    	cd_anexo = $a ";
//	echo $sql;
	pg_exec($db, $sql);
	pg_close($db);
	header("location: cad_atividade_anexos.php?n=$n&a=x");
?>