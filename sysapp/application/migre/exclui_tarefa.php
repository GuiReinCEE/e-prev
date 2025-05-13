<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');

	$sql =		" update projetos.tarefas ";
	$sql = $sql . " set dt_exclusao = current_timestamp ";
	$sql = $sql . " where cd_atividade = $a and cd_tarefa = $t";	
//	echo $sql;
	if (pg_exec($db, $sql)) {
		pg_close($db);
		header('location: lst_minhas_tarefas.php');
   }
   else {
      pg_close($db);
	  header('location: lst_minhas_tarefas.php?msg=Ocorreu um erro ao tentar excluir este registro.');
   }
?>