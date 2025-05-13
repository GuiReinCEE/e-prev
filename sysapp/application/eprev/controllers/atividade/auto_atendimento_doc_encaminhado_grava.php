<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	
	$qr_sql = "
		INSERT INTO public.log_acessos_usuario 
			 (
			   sid,
			   hora,
			   pagina
			 ) 
		VALUES
			 (
			   ".$_SESSION['SID'].",
			   CURRENT_TIMESTAMP,
			   'DOC_ENCAMINHADO_GRAVA'
			 );";
	@pg_query($db,$qr_sql);

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$file = file_get_contents($_FILES["documento"]["tmp_name"]);
    	$documento = base64_encode($file);
   
    	$ext = pathinfo($_FILES["documento"]["name"], PATHINFO_EXTENSION);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_doc_encaminhado");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_doc_encaminhado_tipo_doc=".$_POST['cd_doc_encaminhado_tipo_doc']."&documento=".urlencode($documento)."&documento_ext=".$ext."&ds_observacao=".$_POST['ds_observacao']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		$FL_RETORNO = TRUE;
		$_RETORNO = json_decode($_RETORNO, TRUE);
		if (!(json_last_error() === JSON_ERROR_NONE))
		{
			switch (json_last_error()) 
			{
				case JSON_ERROR_NONE:
					$FL_RETORNO = TRUE;
				break;
					default:
					$FL_RETORNO = FALSE;
				break;
			}
		}
		
		if($FL_RETORNO)
		{
			if(intval($_RETORNO['error']['status']) == 0)
			{
				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=auto_atendimento_doc_encaminhado.php?doc='.$_POST['cd_doc_encaminhado_tipo_doc'].'">';
			}
			else
			{
				echo "
					<script>
						alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
						document.location.href = 'auto_atendimento_doc_encaminhado.php';
					</script>";
				exit;
			}
		}
		else 
		{
			echo "
				<script>
					alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
					document.location.href = 'auto_atendimento_doc_encaminhado.php';
				</script>";
			exit;
		}
	}
	else
	{
		echo "
			<script>
				alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
				document.location.href = 'auto_atendimento_doc_encaminhado.php';
			</script>";
		exit;	
	}
