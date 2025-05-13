<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	$qr_sql = "";
	
	if(count($_POST['nr_ordem']) > 0)
	{
		foreach ($_POST['nr_ordem'] as $cd_link => $nr_ordem)
		{
			$nr_ordem = trim($nr_ordem) == "" ? 0 : $nr_ordem;
			$qr_sql.= "
						UPDATE projetos.links_intra_div
						   SET nr_ordem = ".$nr_ordem."
						 WHERE cd_link  = ".$cd_link.";
					  ";
		}

		#### ABRE TRANSACAO COM O BD ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul= @pg_query($db,$qr_sql);
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
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.$_SERVER['HTTP_REFERER'].'">';
		}
	}
	else
	{
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.$_SERVER['HTTP_REFERER'].'">';
	}
?>