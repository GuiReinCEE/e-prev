<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	if($_REQUEST['fl_gera_pdf'] == "S")
	{
		
		$qr_sql = "
					SELECT COUNT(*) AS fl_existe
					  FROM projetos.rel_acompanhamento_plano
					 WHERE nr_ano      = ".$_REQUEST['ano']."
					   AND nr_mes      = ".$_REQUEST['mes']."
					   AND dt_exclusao IS NULL
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		
		if($ar_reg['fl_existe'] != 0)
		{
			echo "<script>alert('Arquivo já existe.'); document.location.href='senge_rel_acompanhamento_libera.php';</script>";
			exit;
		}
		else
		{
			#### GERA ARQUIVO FISICO ####
			include_once('senge_rel_acompanhamento.php');
			
			#### ---> ABRE TRANSACAO COM O BD <--- ####
			pg_query($db,"BEGIN TRANSACTION");	
			
			$qr_sql = "
						INSERT INTO projetos.rel_acompanhamento_plano
						     (
							   nr_ano, 
							   nr_mes,
							   cd_usuario_bloqueia
							 )
		                VALUES 
						     (
							   ".$_REQUEST['ano'].",
							   ".$_REQUEST['mes'].",
							   ".$_SESSION['Z']."
							 )	
			          ";
			
			$ob_resul = pg_query($db,$qr_sql);
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
	}
	header('location: senge_rel_acompanhamento_libera.php');
?>