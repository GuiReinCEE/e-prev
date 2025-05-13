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
			   'PEDIDO_APOSENTADORIA_CEEEPREV_GRAVA_DEPENDENTE_PREV'
			 );";
	@pg_query($db,$qr_sql);

	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_pedido_aposentadoria_ceeeprev_dependente_prev");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID.
			"&re_cripto=".$_SESSION['RE_CRIPTO'].
			"&cd_pedido_aposentadoria_ceeeprev=".$_POST['cd_pedido_aposentadoria_ceeeprev'].
			"&ds_nome=".$_POST['ds_nome_dependente'].
			"&dt_nascimento=".$_POST['dt_nascimento_dependente_prev'].
			"&ds_sexo=".$_POST['ds_sexo_dependente'].
			"&ds_grau_parentesco=".$_POST['ds_grau_parentesco_dependente'].
			"&ds_estado_civil=".$_POST['ds_estado_civil_dependente'].
			"&fl_incapaz=".$_POST['fl_incapaz_dependente'].
			"&fl_estudante=".$_POST['fl_estudante_dependente'].
			"&fl_guarda_juridica=".$_POST['fl_guarda_juridica_dependente'].
			"&fl_previdenciario=".$_POST['fl_previdenciario_dependente'].
			"&fl_imposto_renda=".$_POST['fl_imposto_renda_dependente']
		);
		
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
				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=auto_atendimento_pedido_aposentadoria_ceeeprev.php">';
			}
			else
			{
				echo "
					<script>
						alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
						document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
					</script>";
				exit;
			}
		}
		else 
		{
			echo "
				<script>
					alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
					document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
				</script>";
			exit;
		}
	}
	else
	{
		echo "
			<script>
				alert('Desculpe, mas não foi possível cadastrar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
				document.location.href = 'auto_atendimento_pedido_aposentadoria_ceeeprev.php';
			</script>";
		exit;	
	}