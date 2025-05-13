<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$qr_sql = " 
				UPDATE projetos.intra_div
				   SET imagem           = NULL,
				       tam_imagem       = NULL,
				       dt_upload_imagem = NULL,
					   tipo_arquivo     = NULL
		         WHERE cd_item = ".$_REQUEST['c']." 
				   AND div     = '".$_REQUEST['div']."'; 
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
		header("location: cad_intra_div.php?c=".$_REQUEST['c']."&div=".$_REQUEST['div']);
	}
?>