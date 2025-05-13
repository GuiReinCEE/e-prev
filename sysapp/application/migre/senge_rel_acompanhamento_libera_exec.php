<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	if($_REQUEST['fl_acao'] == "L")
	{
		#### LIBERA RELATORIO ####
		$qr_cmd = "
					UPDATE projetos.rel_acompanhamento_plano
					   SET dt_libera           = CURRENT_TIMESTAMP, 
					       cd_usuario_libera   = ".$_SESSION['Z'].",
						   dt_bloqueia         = NULL, 
						   cd_usuario_bloqueia = NULL
					 WHERE cd_rel_acompanhamento_plano = ".$_REQUEST['cd_rel_acompanhamento_plano'].";	
		          ";
	}

	if($_REQUEST['fl_acao'] == "B")
	{
		#### BLOQUEIA RELATORIO ####
		$qr_cmd = "
					UPDATE projetos.rel_acompanhamento_plano
					   SET dt_libera           = NULL, 
					       cd_usuario_libera   = NULL,
						   dt_bloqueia         = CURRENT_TIMESTAMP, 
						   cd_usuario_bloqueia = ".$_SESSION['Z']."
					 WHERE cd_rel_acompanhamento_plano = ".$_REQUEST['cd_rel_acompanhamento_plano'].";	
		          ";
	}	
	
	if($_REQUEST['fl_acao'] == "E")
	{
		#### EXCLUIR RELATORIO ####
		$qr_cmd = "
					UPDATE projetos.rel_acompanhamento_plano
					   SET dt_exclusao         = CURRENT_TIMESTAMP, 
						   cd_usuario_exclusao = ".$_SESSION['Z']."
					 WHERE cd_rel_acompanhamento_plano = ".$_REQUEST['cd_rel_acompanhamento_plano'].";	
		          ";
				  
		#### EXCLUI ARQUIVO ####
		$qr_sql = "
					SELECT nr_ano,
					       TRIM(TO_CHAR(nr_mes,'00')) AS nr_mes
					  FROM projetos.rel_acompanhamento_plano
					 WHERE cd_rel_acompanhamento_plano = ".$_REQUEST['cd_rel_acompanhamento_plano']."
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		if(!@unlink('../upload/senge_relatorios/rel_acompanhamento_'.$ar_reg['nr_ano']."-".$ar_reg['nr_mes'].'.pdf'))
		{
			echo "ERRO AO EXCLUIR ARQUIVO";
			exit;
		}
	}	
	
	if($qr_cmd != "")
	{
		#### ---> ABRE TRANSACAO COM O BD <--- ####
		pg_query($db,"BEGIN TRANSACTION");			
		$ob_resul = pg_query($db,$qr_cmd);
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
	}
	
	header('location: senge_rel_acompanhamento_libera.php');
?>