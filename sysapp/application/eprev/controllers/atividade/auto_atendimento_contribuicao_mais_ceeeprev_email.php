<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

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
			   'CONTRIBUICAO_MAIS_CEEEPREV_EMAIL'
			 );";
	@pg_query($db,$qr_sql);

	if($_REQUEST['cd_log'] != '')
	{
		$qr_sql = "
			SELECT cml.seq_identificador AS cd_log,
			       cml.ds_linha_digitavel,
			       cml.vl_nominal,
			       TO_CHAR(cml.dt_vencimento, 'DD/MM/YYYY') AS dt_vencimento,
			       COALESCE(p.email, p.email_profissional) ds_email      
              FROM autoatendimento.contribuicao_mais_log cml
              JOIN participantes p
                ON p.cd_empresa            = cml.cd_empresa
               AND p.cd_registro_empregado = cml.cd_registro_empregado
               AND p.seq_dependencia       = cml.seq_dependencia 
             WHERE cml.dt_vencimento >= CURRENT_DATE
               AND MD5(cml.seq_identificador::text) = '".trim($_REQUEST['cd_log'])."';";

		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg = pg_fetch_array($ob_resul);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/envia_boleto_email_eventual");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_log=".$ar_reg['cd_log']."&ds_email=".$ar_reg['ds_email']);
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
/*
		echo "X".$FL_RETORNO;
	echo "<HR>";
	echo "<PRE>"; print_r($_RETORNO);  
	echo "<HR>";	
	exit;
*/
		if($FL_RETORNO)
		{
			if(intval($_RETORNO['error']['status']) == 0)
			{
				echo "
					<script>
						alert('E-mail enviado com seucesso.\\n\\nObrigado');
						document.location.href = 'auto_atendimento_contribuicao_mais_ceeeprev.php';
					</script>";
				exit;
			}
			else
			{
				echo "
					<script>
						alert('Desculpe, mas não foi possível enviar o e-mail.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
						document.location.href = 'auto_atendimento_contribuicao_mais_ceeeprev.php';
					</script>";
				exit;
			}
		}
		else
		{
			echo "
				<script>
					alert('Desculpe, mas não foi possível enviar o e-mail.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
					document.location.href = 'auto_atendimento_contribuicao_mais.php';
				</script>";
			exit;
		}
	}
	else
	{
		echo "
			<script>
				alert('Desculpe, mas não foi possível enviar o e-mail.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
				document.location.href = 'auto_atendimento_contribuicao_mais.php';
			</script>";
		exit;
	}