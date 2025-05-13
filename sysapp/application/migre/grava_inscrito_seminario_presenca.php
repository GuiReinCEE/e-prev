<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	if($_POST['tp_presenca'] == "S")
	{
		$qr_execute = "
						DELETE FROM acs.seminario_presente
						 WHERE cd_barra = (SELECT cd_barra 
		                                     FROM acs.seminario 
				                            WHERE codigo = ".$_POST['codigo_inscrito'].")::TEXT;
					  ";	
	}
	else
	{
		$qr_execute = "				 
						INSERT INTO acs.seminario_presente
						     (
							   cd_barra, 
							   dt_data, 
							   ds_importa, 
							   cd_usuario_inclusao
						
						)
	                    VALUES 
						     (
							   (SELECT cd_barra 
		                          FROM acs.seminario 
				                 WHERE codigo = ".$_POST['codigo_inscrito'].")::TEXT,
			                   CURRENT_TIMESTAMP, 
							   'MANUAL', 
							   ".$_SESSION['Z']."
							 );
		              ";	
	}

				  

	
	//echo "<PRE>".$qr_execute;exit;
	
	if(trim($qr_execute) != "")
	{
		#### ---> ABRE TRANSACAO COM O BD <--- ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul= @pg_query($db,$qr_execute);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			#### ---> DESFAZ A TRANSACAO COM BD <--- ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro."<BR><BR>";
			echo "<pre>".$qr_execute;
			exit;
		}
		else
		{
			#### ---> COMITA DADOS NO BD <--- ####
			pg_query($db,"COMMIT TRANSACTION"); 
			header("location: cad_inscritos_seminario.php?c=".$_POST['codigo_inscrito']);
		}	
	}
	
	
?>		