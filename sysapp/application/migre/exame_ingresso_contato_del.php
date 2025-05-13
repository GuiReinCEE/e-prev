<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	if($_POST['cd_exame_ingresso_contato'] > 0)
	{
		#### UPDATE ####
		$qr_sql = "
		            UPDATE projetos.exame_ingresso_contato
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
						   cd_usuario_exclusao = ".$_SESSION['Z']."
					 WHERE cd_exame_ingresso_contato = ".$_POST['cd_exame_ingresso_contato'].";
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
					
					<SCRIPT> alert('Registro excluido com sucesso!'); </SCRIPT>
					<META HTTP-EQUIV='Refresh' CONTENT='0;URL=exame_ingresso_contato_cad.php?cd_exame_ingresso=".$_POST['cd_exame_ingresso']."'>
			     ";
			exit;
		}	
	}
?>		