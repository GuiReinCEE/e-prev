<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$_POST['dt_saida'] = (trim($_POST['dt_saida']) == "" ? "CURRENT_TIMESTAMP" : "TO_TIMESTAMP('".$_POST['dt_saida']." ".$_POST['hr_saida']."','DD/MM/YYYY HH24:MI')");
	
	if((trim($_POST['dt_retorno']) == "") and (trim($_POST['fl_retorno']) == "S"))
	{
		$_POST['dt_retorno']   = (trim($_POST['dt_retorno'])   == "" ? "CURRENT_TIMESTAMP" : "TO_TIMESTAMP('".$_POST['dt_retorno']." ".$_POST['hr_retorno']."','DD/MM/YYYY HH24:MI')");	
	}
	else
	{
		$_POST['dt_retorno']   = (trim($_POST['dt_retorno'])   == "" ? "NULL" : "TO_TIMESTAMP('".$_POST['dt_retorno']." ".$_POST['hr_retorno']."','DD/MM/YYYY HH24:MI')");	
	}
	
	/*
	echo "<PRE>";
	print_r($_POST);
	exit;
	*/
	
	if($_POST['cd_chave_movimento'] != "")
	{
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");	
		#### UPDATE ####

		$qr_sql = "
					UPDATE projetos.chaves_movimento
					   SET ds_nome         = UPPER('".$_POST['ds_nome']."'), 
					       ds_nome_retorno = UPPER('".$_POST['ds_nome_retorno']."'), 
						   dt_saida        = ".$_POST['dt_saida'].",
						   dt_retorno      = ".$_POST['dt_retorno']."
					 WHERE cd_chave_movimento = ".$_POST['cd_chave_movimento'];
					 //echo $qr_sql; exit;
		$ob_resul= @pg_query($db,$qr_sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
			//echo "<pre>".$qr_sql;
			exit;
		}
		else
		{
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 		
			header('location: edt_chaves_movimento.php?cd_chave_movimento='.$_POST['cd_chave_movimento'].'&fl_gravado=OK');
		}
	}
?>