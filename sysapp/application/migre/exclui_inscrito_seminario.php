<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$qr_sql = "
				UPDATE acs.seminario
				   SET dt_exclusao         = CURRENT_TIMESTAMP,
				       cd_usuario_exclusao =  ".$_SESSION['Z']."
			     WHERE codigo = ".$_REQUEST['c']." 
	          ";
	
	#### ---> ABRE TRANSACAO COM O BD <--- ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul = pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### ---> DESFAZ A TRANSACAO COM BD <--- ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		exit;
	}
	else
	{
		#### ---> COMITA DADOS NO BD <--- ####
		pg_query($db,"COMMIT TRANSACTION"); 
	}

	header('location: lst_inscritos_seminario.php');

?>