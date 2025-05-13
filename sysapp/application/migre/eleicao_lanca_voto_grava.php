<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('eleicao_permissao.php');
	
	$cd_lote_invalido = 0;
	$cd_lote_voto = 0;
	
	#### LOTE KIT INVÁLIDO ####
	if((is_numeric($_POST['qt_total_invalido'])) and ($_POST['qt_total_invalido'] > 0))
	{
		$cd_lote_invalido = getNovoLote();
		$qr_sql = "
					INSERT INTO eleicoes.lotes_apuracao_eleicoes
						 (
						   ano_eleicao, 
						   cd_eleicao, 
						   cd_lote, 
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia, 
						   num_votos, 
						   dt_hora_lancamento, 
						   usu_lancamento
						 )
					VALUES 
						(
						   2010, 
						   1, 
						   ".$cd_lote_invalido.", 
						   99, 
						   999999, 
						   99, 
						   ".$_POST['qt_total_invalido'].", 
						   CURRENT_TIMESTAMP, 
						   ".$_SESSION['Z']."							 
						);						   
				  ";	

		#### ABRE TRANSACAO COM O BD ####
		pg_query($db,"BEGIN TRANSACTION");	
		$ob_resul = @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = str_replace("\\r","",str_replace("\\n","","ERRO: ".str_replace("ERROR:","",pg_last_error($db))));
			#### DESFAZ A TRANSACAO COM O BD ####
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			//echo $ds_erro."<PRE>".$qr_sql."</PRE>";
			echo "
					<script>
						alert('Ocorreu um erro.');
						document.location.href = 'eleicao_lanca_voto.php';
					</script>
			     ";
			exit;
		}
		else
		{
			#### GRAVA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION");
		}				  
				  
	}	
	
	#### LOTE KIT VÁLIDO ####
	$qr_sql = "
			SELECT 1;
		  ";
	$nr_conta = 0;
	while(list($cd_candidato, $qt_voto) = each($_POST['ar_candidato'])) 
	{ 
		$cd_empresa            = substr($cd_candidato, 0, 2);
		$cd_registro_empregado = substr($cd_candidato, 2, 6);
		$seq_dependencia       = substr($cd_candidato, 8, 2);
		
		//echo $cd_empresa." | ".$cd_registro_empregado." | ".$seq_dependencia." ==> ".$qt_voto."\n";		
		if((is_numeric($qt_voto)) and ($qt_voto > 0))
		{
			$cd_lote_voto = ($nr_conta == 0 ? getNovoLote() : $cd_lote_voto);
			
			$qr_sql.= "
						INSERT INTO eleicoes.lotes_apuracao_eleicoes
							 (
							   ano_eleicao, 
							   cd_eleicao, 
							   cd_lote, 
							   cd_empresa, 
							   cd_registro_empregado, 
							   seq_dependencia, 
							   num_votos, 
							   dt_hora_lancamento, 
							   usu_lancamento
							 )
						VALUES 
							(
							   2010, 
							   1, 
							   ".$cd_lote_voto.", 
							   ".$cd_empresa.", 
							   ".$cd_registro_empregado.", 
							   ".$seq_dependencia.", 
							   ".$qt_voto.", 
							   CURRENT_TIMESTAMP, 
							   ".$_SESSION['Z']."							 
							);						   
			          ";		
			$nr_conta++;
		}
	}	
		
	

	#### ABRE TRANSACAO COM O BD ####
	pg_query($db,"BEGIN TRANSACTION");	
	$ob_resul = @pg_query($db,$qr_sql);
	if(!$ob_resul)
	{
		$ds_erro = str_replace("\\r","",str_replace("\\n","","ERRO: ".str_replace("ERROR:","",pg_last_error($db))));
		#### DESFAZ A TRANSACAO COM O BD ####
		pg_query($db,"ROLLBACK TRANSACTION");
		pg_close($db);
		//echo $ds_erro."<PRE>".$qr_sql."</PRE>";
		echo "
				<script>
					alert('Ocorreu um erro.');
					document.location.href = 'eleicao_lanca_voto.php';
				</script>
		     ";
		exit;
	}
	else
	{
		#### GRAVA DADOS NO BD ####
		pg_query($db,"COMMIT TRANSACTION");
		//echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=eleicao_lanca_voto.php">';
		echo "
				<script>
					alert('DADOS LANÇADOS".($cd_lote_invalido > 0 ? "\\n\\n- Número Lote (Kits Inválidos) => ".$cd_lote_invalido."              " : "").($cd_lote_voto > 0 ? "\\n\\n- Número Lote (Kits Válidos) => ".$cd_lote_voto."              " : "")."\\n\\n');
					document.location.href = 'eleicao_lanca_voto.php';
				</script>
		     ";
		exit;		
		
	}	
	
	function getNovoLote()
	{
		global $db;
		
		$qr_sql = " 
					SELECT (nextval('eleicoes.eleicao_lote_2010') - 1) AS cd_lote 
				  ";
		$ob_resul = pg_query($db, $qr_sql);	
		$ar_reg = pg_fetch_array($ob_resul);
		
		return $ar_reg['cd_lote'];
	}
?>