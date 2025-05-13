<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	if($_POST['cd_enquete_grupo'] > 0)
	{
		#### UPDATE ####
		$qr_sql = "
		            UPDATE projetos.enquete_grupo
					   SET ds_titulo      = '".$_POST['ds_titulo']."',
					       ds_pergunta    = '".$_POST['ds_pergunta']."',
						   cd_enquete_sim = ".$_POST['cd_enquete_sim'].",
						   cd_enquete_nao = ".$_POST['cd_enquete_nao']."
					 WHERE cd_enquete_grupo = ".$_POST['cd_enquete_grupo'].";
				  ";		
	}
	else
	{
		#### INSERT ####
		$qr_sql = "
		            INSERT INTO projetos.enquete_grupo
					     (
	                       ds_titulo,     
						   ds_pergunta,   
						   cd_enquete_sim,
						   cd_enquete_nao,
						   cd_usuario
						 )
					VALUES 
					     (
						   '".$_POST['ds_titulo']."',
						   '".$_POST['ds_pergunta']."',
						   ".$_POST['cd_enquete_sim'].",
						   ".$_POST['cd_enquete_nao'].",						   
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
				 ";
			if($_POST['cd_enquete_grupo'] > 0)
			{
				echo "
						<META HTTP-EQUIV='Refresh' CONTENT='0;URL=cad_enquete_grupo.php?cd_enquete_grupo=".$_POST['cd_enquete_grupo']."'>
					 ";
			}
			else
			{
				echo "
						<META HTTP-EQUIV='Refresh' CONTENT='0;URL=lst_enquete_grupo.php'>
					 ";
			}
			exit;
		}	
	}
?>		