<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
   
	$sql =	" UPDATE projetos.tarefas 
	             SET status_atual = 'SUSP'
	           WHERE cd_tarefa = ".$t."
			     AND cd_atividade = ".$a;	

	if (pg_exec($db, $sql)) 
	{
		$sql  = " INSERT INTO projetos.tarefa_historico 
							( 
							  cd_tarefa,  	
							  cd_atividade, 	
							  cd_recurso,   	
							  timestamp_alteracao,   	
							  descricao,  				
							  status_atual,
							  ds_obs
							) 
					   VALUES
							( 
							  ".$t.", 
							  ".$a.", 
							  ".$recurso.", 
							  current_timestamp, 
							  'Pausa da resoluчуo da Tarefa.', 	
							  'SUSP',
							  '".$_POST['motivo_tarefa']."'
		                    )";	
		if (pg_query($db, $sql)) 
		{
			pg_close($db);
			header('location: frm_exec_tarefa.php?os='.$a.'&c='.$t);
		}
    }
    else 
	{
		pg_close($db);
		echo "Ocorreu um erro ao tentar atualizar esta tarefa.";
    }
?>