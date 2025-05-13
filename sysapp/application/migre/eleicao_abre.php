<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('eleicao_permissao.php');
	
	if((is_numeric($_POST['qt_recebido'])) and ($_POST['qt_recebido'] > 0))
	{
		$qr_sql = "
					UPDATE eleicoes.eleicao
					   SET situacao            = 'A', 
					       num_votos           = ".$_POST['qt_recebido'].", 
					       dt_hr_abertura      = CURRENT_TIMESTAMP,
						   cd_usuario_abertura = ".$_SESSION['Z']."
					 WHERE ano_eleicao = 2010
					   AND cd_eleicao  = 1				
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
						alert('Ocorreu um erro.\\n\\n".$ds_erro."');
						document.location.href = 'eleicao_lanca_voto.php';
					</script>
			     ";
			exit;
		}
		else
		{
			#### GRAVA DADOS NO BD ####
			pg_query($db,"COMMIT TRANSACTION");
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=eleicao_lanca_voto.php">';
		}
	}
	else
	{
		echo "
				<script>
					alert('Ocorreu um erro.\\n\\n".$ds_erro."');
					document.location.href = 'eleicao_lanca_voto.php';
				</script>
		     ";
		exit;	
	}
	
	
?>