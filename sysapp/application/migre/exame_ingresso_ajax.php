<?php
	include_once('inc/sessao.php');
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include_once('inc/conexao.php');

	if($_POST)
	{
		if($_POST['ds_funcao'] == "envia")
		{
			envia($_POST['cd_exame_ingresso']);
		}
		
		if($_POST['ds_funcao'] == "retorno")
		{
			retorno($_POST['cd_exame_ingresso'],$_POST['fl_apto'], $_POST['ds_motivo']);
		}		
		
		if($_POST['ds_funcao'] == "buscaParticipante")
		{
			buscaParticipante($_POST['cd_empresa'],$_POST['cd_registro_empregado'], $_POST['seq_dependencia']);
		}			
	}
	
	function envia($cd_exame_ingresso)
	{
		global $db;
		$qr_sql = "
					UPDATE projetos.exame_ingresso
					   SET dt_envio         = CURRENT_TIMESTAMP,
					       cd_usuario_envio = ".$_SESSION['Z']."
				     WHERE cd_exame_ingresso = ".$cd_exame_ingresso."
				  ";

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
			}
			else
			{
				#### ---> COMITA DADOS NO BD <--- ####
				pg_query($db,"COMMIT TRANSACTION"); 
				ECHO "OK";
			}	
		}
	}
	
	function retorno($cd_exame_ingresso,$fl_apto, $ds_motivo)
	{
		global $db;
		$qr_sql = "
					UPDATE projetos.exame_ingresso
					   SET dt_retorno         = CURRENT_TIMESTAMP,
					       cd_usuario_retorno = ".$_SESSION['Z'].",
						   fl_apto            = '".$fl_apto."',
						   ds_motivo          = ".(trim($ds_motivo) == "" ? "NULL" : "'".utf8_decode($ds_motivo)."'")."
				     WHERE cd_exame_ingresso = ".$cd_exame_ingresso."
				  ";

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
			}
			else
			{
				#### ---> COMITA DADOS NO BD <--- ####
				pg_query($db,"COMMIT TRANSACTION"); 
				ECHO "OK";
			}	
		}
	}	

	
	function buscaParticipante($cd_empresa,$cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		$qr_sql = "
					SELECT TRIM(UPPER(funcoes.remove_acento(nome))) AS nome
					  FROM public.participantes
					 WHERE cd_empresa            = ".$cd_empresa."
					   AND cd_registro_empregado = ".$cd_registro_empregado."
					   AND seq_dependencia       = ".$seq_dependencia."
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		echo $ar_reg['nome'];
	}
?>