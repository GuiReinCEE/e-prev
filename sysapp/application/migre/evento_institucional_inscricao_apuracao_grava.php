<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	//echo "<PRE>";
	//print_r($_REQUEST);
	
	$qr_sql = "
				INSERT INTO projetos.eventos_institucionais_apuracao
				     (
					   ds_nome, 
					   cd_evento
					 )
				VALUES 
				     (
					   '".$_POST['ds_nome']."', 
					   19
					 );
				
	          ";
	
	//echo $qr_sql; exit;	
			  
	if(trim($qr_sql) != "")
	{
		#### ---> ABRE TRANSACAO COM O BD <--- ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul= @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			#### ---> DESFAZ A TRANSACAO COM BD <--- ####
			pg_query($db,"ROLLBACK TRANSACTION");
			echo $ds_erro;
		}
		else
		{
			#### ---> COMITA DADOS NO BD <--- ####
			pg_query($db,"COMMIT TRANSACTION"); 
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=evento_institucional_inscricao_apuracao.php">';
		}	
	}	
?>