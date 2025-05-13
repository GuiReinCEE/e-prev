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
			   'CONTRIBUICAO_MAIS_CEEEPREV_REGISTRA'
			 );";
	@pg_query($db,$qr_sql);


	if(($_REQUEST['valor_upceee'] != '') AND ($_REQUEST['dt_vencimento'] != ''))
	{

		$arr = explode('-', $_REQUEST['valor_upceee']);

		$qt_upceee      = $arr[0];
		$vl_total_pagar = $arr[1];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/registra_boleto_eventual");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&vl_total_pagar=".$vl_total_pagar."&qt_upceee=".$qt_upceee."&dt_vencimento=".$_REQUEST['dt_vencimento']);
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
				$cd_log = $_RETORNO['result']['registro']['cd_log'];

				header('Location: auto_atendimento_contribuicao_ceeeprev_gerado.php?cd_log='.MD5($cd_log)); 
			}
			else
			{
				echo "
					<script>
						alert('Desculpe, mas não foi possível registrar sua contribuição.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
						document.location.href = 'auto_atendimento_contribuicao_mais_ceeeprev.php';
					</script>";
				exit;
			}
		}
		else
		{
			echo "
				<script>
					alert('Desculpe, mas não foi possível registrar sua contribuição.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
					document.location.href = 'auto_atendimento_contribuicao_mais_ceeeprev.php';
				</script>";
			exit;
		}
	}
	else 
	{
		echo "
			<script>
				alert('Desculpe, mas não foi possível registrar sua contribuição.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
				document.location.href = 'auto_atendimento_contribuicao_mais_ceeeprev.php';
			</script>";
		exit;
	}
?>