<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	$qr_sql = " 
				UPDATE projetos.usuarios_controledi 
				   SET senha_md5      = MD5('123456'), 
					   dt_troca_senha = NULL  
				 WHERE codigo = ".$_REQUEST['c'].";
			  ";

	#### ABRE TRANSACAO COM O BD ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$qr_sql);
	
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM O BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo "ERRO".$ds_erro ;	
	}
	else
	{
		#### GRAVA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION");
		pg_close($db);
		header('location: lst_recursos.php');
	}
?>