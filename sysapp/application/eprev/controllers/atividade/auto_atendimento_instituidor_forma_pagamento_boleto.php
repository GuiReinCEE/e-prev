<?php
	#"e7a9e3f647dd33941430647118aaf2b7";"WEB - Autoatendimento"
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');	


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
					   'INSTITUIDOR_FORMA_PAGAMENTO_BOLETO'
					 )
		      ";
	@pg_query($db,$qr_sql); 	
	
	#ECHO "id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&vl_contribuicao=".number_format($_REQUEST['vl_contrib_contratada'],2,",",".")."&ds_descricao=ATUALIZADO VIA AUTOATENDIMENTO";
	
	#ECHO "<PRE>"; print_r($_POST); EXIT;
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_forma_pagamento_bdl");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&vl_contribuicao=".number_format($_REQUEST['vl_contrib_contratada'],2,",",".")."&ds_descricao=ATUALIZADO VIA AUTOATENDIMENTO");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$_RETORNO = curl_exec($ch);
	curl_close ($ch);

	#print_r($_RETORNO); echo "<HR>";
	
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
	#echo "X".$FL_RETORNO; echo "<HR>"; 	echo "<PRE>"; print_r($_RETORNO);  echo "<HR>"; exit;

	if($FL_RETORNO)
	{
		#echo $_RETORNO['error']['status'];echo "<HR>";
		if(intval($_RETORNO['error']['status']) == 0)
		{
			echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
				<SCRIPT>
					alert('Atualização registrada.');
					document.location.href = 'auto_atendimento_instituidor_forma_pagamento.php';
				</SCRIPT>
			 ";	
			exit;			
		}
		else
		{
			echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
				<SCRIPT>
					alert('Ocorreu um erro.\\n\\nEntre em contato pelo 0800 510 2596.');
					document.location.href = 'auto_atendimento_instituidor_forma_pagamento.php';
				</SCRIPT>
			 ";	
			exit;
		}
	}
	else
	{
		echo "	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
			<SCRIPT>
				alert('Ocorreu um erro.\\n\\nEntre em contato pelo 0800 510 2596.');
				document.location.href = 'auto_atendimento_instituidor_forma_pagamento.php';
			</SCRIPT>
		 ";	
		exit;
	}	
	
	
	
?>