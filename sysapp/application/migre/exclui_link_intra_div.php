<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$qr_sql = " 
				UPDATE projetos.links_intra_div
				   SET dt_exclusao = CURRENT_TIMESTAMP
		         WHERE cd_item = ".$_REQUEST['cd_item']." 
				   AND cd_link = ".$_REQUEST['cd_link']."; 
			  ";	
			  
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
		header("location: cad_intra_div.php?c=".$_REQUEST['cd_item']."&div=".$_REQUEST['div']);
	}
?>