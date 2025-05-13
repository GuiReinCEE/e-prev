<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	if(intval($_REQUEST['c']) > 0)
	{
		$qr_sql = " 
					UPDATE projetos.acompanhamento_projetos 
					   SET dt_cancelamento = CURRENT_TIMESTAMP 
					 WHERE cd_acomp = ".intval($_REQUEST['c'])." 
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
			echo $ds_erro;
			exit;
		}
		else
		{
			#### GRAVA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION");
		}
	}	
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.site_url("atividade/acompanhamento/cadastro")."/".intval($_REQUEST['c']).'">';
?>