<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	
	if(trim($_REQUEST['cd_acomp']) != "")
	{
		$qr_sql = "";
		if($_REQUEST['tp_envio'] == "TODOS")
		{
			$qr_sql = "
						SELECT acomp_projeto AS qt_resul
					      FROM rotinas.acomp_projeto(".$_REQUEST['cd_acomp'].")
				      ";
		}

		if($qr_sql != "")
		{
			pg_query($db,"BEGIN TRANSACTION");
			$ob_resul = pg_query($db, $qr_sql);
			if(!$ob_resul)
			{
				$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
				pg_query($db,"ROLLBACK TRANSACTION");
				echo $ds_erro;
				exit;
			}
			else
			{
				pg_query($db,"COMMIT TRANSACTION"); 
				$ar_reg = pg_fetch_array($ob_resul);
				
				echo "
						<script>
							alert('Total de email enviados: ".$ar_reg['qt_resul']."');
						</script>
					 ";
			}
		}
	}
	
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.site_url("atividade/acompanhamento/cadastro")."/".$_REQUEST['cd_acomp'].'">';
?>