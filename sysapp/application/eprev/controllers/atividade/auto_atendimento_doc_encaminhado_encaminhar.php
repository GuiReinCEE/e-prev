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
			   'DOC_ENCAMINHADO_EXCLUIR'
			 );";
	@pg_query($db,$qr_sql);

	if($_SERVER['REQUEST_METHOD'] == 'GET')
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_encaminhar_doc_encaminhado");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
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
				echo "
					<script>
						alert('Documento(s) encaminhado(s) com sucesso, aguarde a confirmação da validação que receberá por e-mail.');
						document.location.href = 'auto_atendimento_doc_encaminhado.php';
					</script>";
				exit;
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
