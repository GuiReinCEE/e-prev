<?
	require_once('inc/sessao.php');
	require_once('inc/conexao.php');
	require_once('inc/nextval_sequence.php');
	
	#### ABRE TRANSACAO COM O BD #####
	pg_query($db,"BEGIN TRANSACTION");	

	#### PEGA NEXTVAL DA SEQUENCE DO CAMPO
	$CD_LOTE = getNextval("acs", "seminario_codigo_barra_lote", "cd_seminario_codigo_barra_lote", $db); 
	
	#### TESTA SE RETORNOU ALGUM VALOR
	if ($CD_LOTE > 0) 
	{		
		#### GERA LOTE ####
		$qr_sql = "
					INSERT INTO acs.seminario_codigo_barra_lote 
						 (
						   cd_seminario_codigo_barra_lote,
						   dt_data,
						   cd_seminario_edicao
						 )   
					VALUES 
						 (
						   ".$CD_LOTE.",
						   CURRENT_TIMESTAMP,
						   ".trim((trim($_POST['cd_seminario']) == '' ? 'NULL' : "'".$_POST['cd_seminario']."'"))."
						 );					
				  ";
				  
		$nr_conta = 0;
		while($nr_conta < $_POST['qt_barra'])
		{
			#### GERA CODIGOS DO LOTE ####
			$qr_sql.= "
						INSERT INTO acs.seminario_codigo_barra 
							 (
							   cd_seminario_codigo_barra_lote
							 )   
						VALUES 
							 (
							   ".$CD_LOTE."
							 );					
					  ";
			$nr_conta++;
		}
	}
	else
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro; 
		exit;
	}

	
	#echo "<PRE>"; echo $qr_sql; exit; #### DEBUG	
	$ob_resul= @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
		#### DESFAZ A TRANSACAO COM BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		echo $ds_erro; 
		exit;
	}
	else
	{
		#### COMITA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION"); 

		echo "
				<script>
					document.location.href = 'cad_inscritos_seminario_barra.php?cd_lote=".$CD_LOTE."';
				</script>
			 ";				

		pg_close($db);
	}

    pg_close($db);
?>