<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/funcoes.php');

	// ---> ABRE TRANSACAO COM O BD <--- //
	pg_query($db,"BEGIN TRANSACTION");	

	if (trim($_POST['cd_fotos']) == "")
	{
		#### INSERT ####
		$qr_sql = "
					INSERT INTO acs.fotos
					     (
						   ds_titulo,
						   ds_caminho,
						   dt_data
						 )
					VALUES
					     (
						   '".$_POST['ds_titulo']."',
						   '".$_POST['ds_caminho']."',
						   TO_DATE('".$_POST['dt_data']."','DD/MM/YYYY')
						 )
		          ";
	}
	else 
	{
		#### UPDATE ####
		$qr_sql = "
					UPDATE acs.fotos
					   SET ds_titulo  = '".$_POST['ds_titulo']."',
						   ds_caminho = '".$_POST['ds_caminho']."',
						   dt_data    = TO_DATE('".$_POST['dt_data']."','DD/MM/YYYY')
					 WHERE cd_fotos   = ".$_POST['cd_fotos']."
		          ";	
	}
	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		// ---> DESFAZ A TRANSACAO COM BD<--- //
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		exit;
	}
	else
	{
		// ---> COMITA DADOS NO BD <--- //
		pg_query($db,"COMMIT TRANSACTION"); 		
		pg_close($db);
		header('location: lst_fotos.php'); 
	}

?>