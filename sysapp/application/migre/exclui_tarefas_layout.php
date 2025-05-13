<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	//$l -> cd_tarefas_layout
	//$p -> cd_tarefas_layout_campo

	if(trim($p) != "") //EXCLUI CAMPO
	{
		$sql = " DELETE 
		           FROM projetos.tarefas_layout_campo
		          WHERE cd_tarefas_layout_campo = ".$p."
				    AND cd_tarefas_layout       = ".$l."
				    AND cd_atividade            = ".$a."
					AND cd_tarefa               = ".$c."
				  ";		
	}
	else //EXCLUI TIPO + CAMPO
	{
		$sql = " DELETE 
		           FROM projetos.tarefas_layout
		          WHERE cd_tarefas_layout = ".$l."
				    AND cd_atividade    = ".$a."
					AND cd_tarefa       = ".$c.";
				  ";
		$sql.= " DELETE 
		           FROM projetos.tarefas_layout_campo
		          WHERE cd_tarefas_layout       = ".$l."
				    AND cd_atividade            = ".$a."
					AND cd_tarefa               = ".$c.";
				  ";			
	}

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