<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
   
	if($_POST['insere'] == 'I')
	{
		$qr_sql = " 
					INSERT INTO projetos.intra_div 
					     ( 
		       	           cd_item, 
		       	           cd_item_pai, 
					       div,
		       	           titulo, 
		       	           conteudo, 
		       	           dt_inclusao, 
		       	           cd_usuario 
						 )	
		            VALUES 
				         (
					       ".$_POST['cd_item'].", 
					       ".$_POST['cd_item_pai'].", 
						   '".$_POST['div']."', 
						   '".$_POST['titulo']."', 
						   '".$_POST['conteudo']."', 
					       CURRENT_TIMESTAMP, 
					       ".$_SESSION['Z']."
					     ); 
			      ";
	}
	elseif($_POST['insere'] == 'U')
	{
		$qr_sql = " 
					UPDATE projetos.intra_div 
		               SET cd_item_pai = ".$_POST['cd_item_pai'].",
					       titulo      = '".$_POST['titulo']."', 
		                   conteudo    = '".$_POST['conteudo']."', 
		                   cd_usuario  = ".$_SESSION['Z']." 
		             WHERE cd_item = ".$_POST['cd_item']." 
					   AND div     = '".$_POST['div']."';
			      ";
	}
	
	#### ---> ABRE TRANSACAO COM O BD <--- ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### ---> DESFAZ A TRANSACAO COM BD <--- ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		EXIT;
	}
	else
	{
		#### ---> COMITA DADOS NO BD <--- ####
		pg_query($db,"COMMIT TRANSACTION"); 
		header("location: cad_intra_div.php?c=".$_POST['cd_item']."&div=".$_POST['div']);
	}	
?>