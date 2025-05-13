<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	$sql = " DELETE 
	           FROM projetos.tarefas_tabelas 
	          WHERE cd_tarefas_tabelas = ".$p."
			    AND cd_atividade       = ".$a."
				AND cd_tarefa          = ".$c." 
			  ";		

	if (pg_exec($db, $sql)) 
	{
		pg_close($db);
		header('location: frm_tarefa.php?os='.$a.'&c='.$c.'&f='.$f);
	}
	else 
	{
		pg_close($db);
		echo 'Ocorreu um erro ao tentar excluir este registro.';
	}
?>