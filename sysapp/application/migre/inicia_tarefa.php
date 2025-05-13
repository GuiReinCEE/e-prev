<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');
 
	$sql = "UPDATE projetos.tarefas 
			   SET dt_inicio_prog = current_timestamp, 
			       status_atual   = 'EMAN' 
			 WHERE cd_tarefa      = ".$t." 
			   AND cd_atividade   = ".$a;		
	
	if (pg_exec($db, $sql)) 
	{
		$sql = "UPDATE projetos.atividades 
				   SET status_atual = 'EMAN' 
				 WHERE numero = ".$a;	

		if (pg_exec($db, $sql)) 
		{
			$sql  = " INSERT INTO projetos.tarefa_historico 
			                    ( 
								  cd_tarefa,  	
								  cd_atividade, 	
								  cd_recurso,   	
								  timestamp_alteracao,   	
								  descricao,  				
								  status_atual
								) 
						   VALUES
						        ( 
								  ".$t.", 
								  ".$a.", 
								  ".$recurso.", 
								  current_timestamp, 
								  'Inнcio da resoluзгo da Tarefa.', 
								  'EMAN'
								)";	
			
			$sql = $sql;
			if (pg_exec($db, $sql)) 
			{
				pg_close($db);
				header('location: frm_exec_tarefa.php?os='.$a.'&c='.$t);
			}
			else 
			{
      			pg_close($db);
	  			echo "Ocorreu um erro ao tentar atualizar esta tarefa.";
   			}			
		}			
		else 
		{
      		pg_close($db);
	  		echo "Ocorreu um erro ao tentar atualizar esta tarefa.";
   		}
	}
	else 
	{
		pg_close($db);
		echo "Ocorreu um erro ao tentar atualizar esta tarefa.";
	}
?>