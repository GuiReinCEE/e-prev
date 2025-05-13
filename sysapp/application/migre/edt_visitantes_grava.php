<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$_POST['dt_entrada'] = (trim($_POST['dt_entrada']) == "" ? "CURRENT_TIMESTAMP" : "TO_TIMESTAMP('".$_POST['dt_entrada']." ".$_POST['hr_entrada']."','DD/MM/YYYY HH24:MI')");
	
	if((trim($_POST['dt_saida']) == "") and (trim($_POST['fl_saida']) == "S"))
	{
		$_POST['dt_saida']   = (trim($_POST['dt_saida'])   == "" ? "CURRENT_TIMESTAMP" : "TO_TIMESTAMP('".$_POST['dt_saida']." ".$_POST['hr_saida']."','DD/MM/YYYY HH24:MI')");	
	}
	else
	{
		$_POST['dt_saida']   = (trim($_POST['dt_saida'])   == "" ? "NULL" : "TO_TIMESTAMP('".$_POST['dt_saida']." ".$_POST['hr_saida']."','DD/MM/YYYY HH24:MI')");	
	}
	
	
	
	//echo "<PRE>";
	//print_r($_POST);
	//exit;
	
	if($_POST['cd_visitante'] != "")
	{
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");	
		#### UPDATE ####

		$qr_sql = "
					UPDATE projetos.visitantes
					   SET nr_cracha      = ".(trim($_POST['nr_cracha']) == '' ? 'NULL' : $_POST['nr_cracha']).", 
						   cd_tipo_visita = '".$_POST['cd_tipo']."',
						   ds_nome        = UPPER('".$_POST['ds_nome']."'), 
						   ds_origem      = UPPER('".$_POST['ds_origem']."'),
						   ds_destino     = UPPER('".$_POST['ds_destino']."'),
						   dt_entrada     = ".$_POST['dt_entrada'].",
						   dt_saida       = ".$_POST['dt_saida']."
					 WHERE cd_visitante = ".$_POST['cd_visitante'];
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
			header('location: edt_visitantes.php?cd_visitante='.$_POST['cd_visitante'].'&fl_gravado=OK');
		}
	}
?>