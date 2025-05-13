<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$sql = " 
			DELETE FROM projetos.reunioes_projetos_envolvidos
	          WHERE cd_acomp   = ".$_REQUEST['cd_acomp']." 
			    AND cd_reuniao = ".$_REQUEST['cd_reuniao'].";

			UPDATE projetos.reunioes_projetos 
	            SET dt_exclusao         = CURRENT_TIMESTAMP,
				    cd_usuario_exclusao = ".$_SESSION['Z']."
	          WHERE cd_acomp   = ".$_REQUEST['cd_acomp']." 
			    AND cd_reuniao = ".$_REQUEST['cd_reuniao'].";
		   ";	

	#### ABRE TRANSACAO COM O BD ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul= @pg_query($db,$sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM O BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro;
		exit;
	}
	else
	{
		#### GRAVA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION");
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=cad_acomp_projetos.php?c='.$_REQUEST['cd_acomp'].'">';
	}	
?>