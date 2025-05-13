<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	if($_POST['cd_exame_ingresso_contato'] > 0)
	{
		#### UPDATE ####
		$qr_sql = "
		            UPDATE projetos.exame_ingresso_contato
					   SET dt_contato           = TO_TIMESTAMP('".$_POST['dt_contato']." ".$_POST['hr_contato']."','DD/MM/YYYY HH24:MI'),
						   ds_contato           = '".$_POST['ds_contato']."',
						   dt_alteracao         = CURRENT_TIMESTAMP,
						   cd_usuario_alteracao = ".$_SESSION['Z']."
					 WHERE cd_exame_ingresso_contato = ".$_POST['cd_exame_ingresso_contato'].";
				  ";		
	}
	else
	{
		#### INSERT ####
		$qr_sql = "
		            INSERT INTO projetos.exame_ingresso_contato
					     (
	                       cd_exame_ingresso,
						   dt_contato,
						   ds_contato, 
						   cd_usuario_inclusao
						 )
					VALUES 
					     (
						   ".$_POST['cd_exame_ingresso'].",
						   TO_TIMESTAMP('".$_POST['dt_contato']." ".$_POST['hr_contato']."','DD/MM/YYYY HH24:MI'),
						   '".$_POST['ds_contato']."',
						   ".$_SESSION['Z']."
						 );
				  ";
	}
	//echo "<PRE>".$qr_sql;exit;
	
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
			pg_close($db);
			echo $ds_erro."<BR><BR>";
			exit;
		}
		else
		{
			#### ---> COMITA DADOS NO BD <--- ####
			pg_query($db,"COMMIT TRANSACTION"); 
			echo "
					
					<SCRIPT> alert('Registro gravado com sucesso!'); </SCRIPT>
					<META HTTP-EQUIV='Refresh' CONTENT='0;URL=exame_ingresso_contato_cad.php?cd_exame_ingresso=".$_POST['cd_exame_ingresso']."'>
			     ";
			exit;
		}	
	}
?>		