<?php
	include_once('inc/conexao.php');

	if($_POST)
	{
		if($_POST['ds_funcao'] == "setExibicao")
		{
			setExibicao($_POST);
		}
	}
	
	function setExibicao($ar_param)
	{
		global $db;
		// ---> ABRE TRANSACAO COM O BD <--- //
		pg_query($db,"BEGIN TRANSACTION");			
		$qr_update = "
						UPDATE prevenir.prevenir_formulario_item
						   SET fl_exibir = '".$ar_param['fl_exibir']."'
						 WHERE MD5(cd_prevenir_formulario_item::TEXT) = '".$ar_param['cd_prevenir_formulario_item']."'
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
			echo "OK";
		}	
	}

?>