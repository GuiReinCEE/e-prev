<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
   
	$uploadDir = '/u/www/upload/';
	$uploadFile = $uploadDir . $_FILES['arquivo']['name'];
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $uploadFile))
	{
		$qr_sql = "
					update projetos.intra_div 
		               set imagem 			 = '".$_FILES['arquivo']['name']."', 
		 	               tam_imagem 		 = ".filesize($uploadFile).", 
				           dt_upload_imagem  = CURRENT_TIMESTAMP, 
		 	               tipo_arquivo 	 = '".filetype($uploadFile)."' 
		             WHERE cd_item = ".$_POST['cd_item']." 
				       AND div     = '".$_POST['div']."'; 
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
	}
	else
	{
		echo "<b>Ocorreu um erro ao tentar fazer o upload do arquivo!<br><br>Informações:</b><BR>";
		echo "<PRE>".print_r($_FILES)."</PRE>";
		echo "</b>";
		exit;
	}
?>
