<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	$sql =		" delete from projetos.ativ_projetos where cd_projeto = $sist and cd_atividade = $atv ";
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: cad_atividade_atend.php?n=' . $atv . '&a=a&TA=A');
   }
	else {
	    pg_close($db);
		echo "Ocorreu um erro ao tentar excluir este artigo.";
   }
 
?>