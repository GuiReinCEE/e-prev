<?
	header('Content-Type: text/html; charset=ISO-8859-1'); 
	include_once('inc/conexao.php');
	
	if($_POST)
	{
		if($_POST['ds_funcao'] == "contaTabela")
		{
			contaTabela($_POST['ds_tabela']);
		}
		
		if($_POST['ds_funcao'] == "contaTabelaOra")
		{
			contaTabelaOra($_POST['ds_tabela']);
		}		
	}
	else
	{
		echo "ERRO: NENHUM DADO POSTADO";
	}
	
	function contaTabela($ds_tabela)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");			
		$qr_update = "
						UPDATE projetos.tabelas_atualizar
						   SET qt_total_registro = (SELECT COUNT(*) FROM ".$ds_tabela.")
						 WHERE UPPER(tabela) = UPPER('".$ds_tabela."')
					 RETURNING qt_total_registro;
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
			
			$ar_reg = pg_fetch_array($ob_resul);
			echo $ds_tabela." => ".$ar_reg['qt_total_registro'];
		}	
	}
	
	function contaTabelaOra($ds_tabela)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");			
		$qr_update = "
						UPDATE projetos.tabelas_atualizar
						   SET num_registros = (SELECT conta_dados_ora FROM sincroniza.conta_dados_ora(LOWER(tabela), ''))
						 WHERE UPPER(tabela) = UPPER('".$ds_tabela."')
					 RETURNING num_registros;
				     ";
		$ob_resul = @pg_query($db,$qr_update);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
		}
		else
		{
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 
			
			$ar_reg = pg_fetch_array($ob_resul);
			echo $ds_tabela." => ".$ar_reg['num_registros'];
		}	
	}	
?>