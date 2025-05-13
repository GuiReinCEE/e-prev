<?php
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
	
	/**************************************************************************/
	/*                                                                        */
	/*   VER ARQUIVO /eletroceee/clicksignwebhook/dsv.php - DESENVOLVIMENTO   */
	/*   VER ARQUIVO /eletroceee/clicksignwebhook/prd.php - PRODUÇÃO          */
	/*                                                                        */
	/**************************************************************************/
	
	#### *********************** VALIDAR SUBMITE E ORIGEM DO ARQUIVO CHAMADOR *********************** ####
	
	
	#### ATIVA O DEBUG ####
	$_FL_DEBUG = FALSE;
	echo ($_FL_DEBUG ? "<PRE>" : "");
	
	#### BUSCA CONFIGURACAO DA API ####
	if($ip_host == 'srvpg.eletroceee.com.br')
	{
		#### PRODUCAO ####
		$qr_sql = "
					SELECT ds_ambiente, 
					       ds_token, 
						   ds_url
                      FROM clicksign.configuracao
					 WHERE ds_ambiente = 'PRODUCAO'
					   AND dt_exclusao IS NULL
				  ";
		$ob_resul = pg_query($db,$qr_sql);		
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$_API_AMBIENTE = trim($ar_reg['ds_ambiente']);		
		$_API_URL      = trim($ar_reg['ds_url']);		
		$_API_TOKEN    = trim($ar_reg['ds_token']);
	}
	else
	{
		#### DESENVOLVIMENTO #####
		$qr_sql = "
					SELECT ds_ambiente, 
					       ds_token, 
						   ds_url
                      FROM clicksign.configuracao
					 WHERE ds_ambiente = 'DESENVOLVIMENTO'
					   AND dt_exclusao IS NULL
				  ";
		$ob_resul = pg_query($db,$qr_sql);		
		$ar_reg   = pg_fetch_array($ob_resul);
		
		$_API_AMBIENTE = trim($ar_reg['ds_ambiente']);	
		$_API_URL      = trim($ar_reg['ds_url']);		
		$_API_TOKEN    = trim($ar_reg['ds_token']);	
	}

	echo ($_FL_DEBUG ? $ip_host."|".$_API_AMBIENTE."|".$_API_URL."|".$_API_TOKEN."<BR>" : "");
	
	
	#### LOG ####
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
					   'CONTRATO_ASSINAR'
					 )
		      ";
	@pg_query($db,$qr_sql);	
	
	
	#### TELEFONE CELULAR ####
	$qr_sql = "
				SELECT (p.ddd_celular::TEXT || p.celular::TEXT) AS celular,
				       LOWER(COALESCE(COALESCE(p.email,p.email_profissional),'')) AS email
				  FROM public.participantes p
				 WHERE p.cd_empresa                                   = ".$_SESSION['EMP']."
				   AND p.cd_registro_empregado                        = ".$_SESSION['RE']." 
				   AND p.seq_dependencia                              = ".$_SESSION['SEQ']."
				   AND COALESCE(p.celular,0)                          > 0
				   AND p.celular::TEXT                                LIKE '9%'                   
				   AND LENGTH(p.ddd_celular::TEXT || p.celular::TEXT) = 11	
				   AND COALESCE(COALESCE(p.email,p.email_profissional),'') LIKE '%@%.%'
		      ";
	$ob_resul = pg_query($db,$qr_sql);		
	$ar_reg   = pg_fetch_array($ob_resul);
	$_CELULAR = intval($ar_reg['celular']);	
	$_EMAIL   = trim($ar_reg['email']);	
	
	echo ($_FL_DEBUG ? $_CELULAR."|".$_EMAIL."<BR>" : "");
	#print_r($ar_reg); exit;
	
	if((trim($_CELULAR) == "") OR (trim($_EMAIL) == ""))
	{
		$conteudo = "<BR>
						<DIV style='font-family: Calibri, Arial; width:100%; text-align:center;'>
							<H2>Telefone celular ou e-mail não identificado</H2>
							Para assinar digitalmente o contrato é necessário ter um telefone celular e um e-mail cadastrado, entre em contato com a nossa central de atendimento 08005102596 de segunda à sexta.
						</DIV>
					<BR><BR>";		
		
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();  
		pg_close($db);		
		exit;
	}
	

	
	$ob_cliente_soap = new SoapClient('http://10.63.255.16:1111/server.php?wsdl');
	$pdf_64 = $ob_cliente_soap->execReportPDF("CADR1476;P_CD_EMPRESA=".$_SESSION['EMP']."&P_CD_REGISTRO_EMPREGADO=".$_SESSION['RE']."&P_SEQ_DEPENDENCIA=".$_SESSION['SEQ']."&P_ASSINADO_DIGITALMENTE=1&P_FL_CABECALHO=1&P_FL_ASSINATURA=1");	

	
	$_CD_DOC_ELETRO = 212;
	$_NOME_DOC = "CONTRATO CALLCENTER";
	$dt_limite = new DateTime('+10 day');
	#echo $date->format('Y-m-d H:i:s');	exit;	
	
	#### CRIA DOCUMENTO ####
	$data_string = '
						{
							"document":{
								"path":"/PARTICIPANTES/'.str_pad($_SESSION['EMP'], 2, '0', STR_PAD_LEFT).'-'.str_pad($_SESSION['RE'], 6, '0', STR_PAD_LEFT).'-'.str_pad($_SESSION['SEQ'], 2, '0', STR_PAD_LEFT).'/'.str_pad($_CD_DOC_ELETRO, 4, '0', STR_PAD_LEFT).'/'.str_replace("-","_",str_replace(" ","_",$_NOME_DOC))."-".str_replace(" ","_",$_SESSION['NOME']).date("YmdHis").'.pdf",
								"content_base64":"data:application/pdf;base64,'.$pdf_64.'",
								"deadline_at":"'.$dt_limite->format('Y-m-d').'T23:59:59-03:00",
								"remind_interval":"2",
								"auto_close":"true",
								"sequence_enabled":"true",
								"signable_group":null,
								"locale":"pt-BR"
							}
						}
	               ';
	$ar_doc = execClick($data_string,$_API_URL."/documents?access_token=".$_API_TOKEN);
	echo ($_FL_DEBUG ? "DOCUMENTO<br>" : "");
	echo ($_FL_DEBUG ? print_r($ar_doc,TRUE) : "");
	
	#### ATUALIZA SENHA E OPCAO SOLICITADA DE CONTRATO ####
	$qr_sql = "
				UPDATE public.participantes_ccin
				   SET codigo_345            = codigo_358,
				       opcao_contrato_pedido = '2',
					   motivo_alteracao      = 16,
					   dt_solicitacao        = CURRENT_TIMESTAMP,
					   data_envio_345        = CURRENT_TIMESTAMP,
					   codigo_355            = 'S'
				 WHERE cd_empresa            = ".$_SESSION['EMP']."
				   AND cd_registro_empregado = ".$_SESSION['RE']." 
				   AND seq_dependencia       = ".$_SESSION['SEQ']."
		      ";
	#echo $qr_sql; 	exit;	  
	$ob_resul = pg_query($db,$qr_sql);
	
	#### INSERE NA TABELA ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital
				     (
						ip, 
						dt_limite,
						cd_empresa, 
						cd_registro_empregado, 
						seq_dependencia,
                        cd_doc,						
						id_doc, 
						json_doc
					 )
				VALUES 
				     (
						'".$_SERVER['REMOTE_ADDR']."',
						TO_TIMESTAMP('".$dt_limite->format('d/m/Y')." 23:59:59','DD/MM/YYYY HH24:MI:SS'),
						".$_SESSION['EMP'].", 
						".$_SESSION['RE'].", 
						".$_SESSION['SEQ'].", 
						".$_CD_DOC_ELETRO.", 
						'".$ar_doc["ARRAY"]['document']['key']."', 
						'".$ar_doc["JSON"]."'					 
					 )
				RETURNING cd_contrato_digital
		      ";
	#echo $qr_sql; 	exit;	  
	$ob_resul = pg_query($db,$qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	$_CD_DOC = intval($ar_reg['cd_contrato_digital']);
	
	
	#### INSERE NA TABELA ORACLE DE PROTOCOLOS DE DOCUMENTOS DE PARTICIPANTES ####
	$qr_sql = "
				SELECT protocolos_assinatura_docs
				  FROM oracle.protocolos_assinatura_docs(
							".$_SESSION['EMP'].", 
							".$_SESSION['RE'].", 
							".$_SESSION['SEQ'].",
							'".$ar_doc["ARRAY"]['document']['key']."', 
							'".$_NOME_DOC."'
						)				
		      ";
	#echo $qr_sql; 	exit;	  
	@pg_query($db,$qr_sql);
	
	
	echo ($_FL_DEBUG ? "ASSINADOR 1 <BR>" : "");
	#### CRIAR ASSINADOR 1 - PARTICIPANTE ####
	$data_string = '
						{
						  "signer": {
							"email": "'.$_EMAIL.'",
							"phone_number": "'.$_CELULAR.'",
							"auths": ["sms"],
							"name": "'.$_SESSION['NOME'].'",
							"has_documentation": true
						  }
						}	
				   ';
	$ar_ass1 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);
	echo ($_FL_DEBUG ? print_r($ar_ass1,TRUE) : "");
	
	
	#### ADD ASSINADOR 1 - PARTICIPANTE ####
	$data_string = '
					{
					  "list": {
						"document_key": "'.$ar_doc["ARRAY"]['document']['key'].'",
						"signer_key": "'.$ar_ass1["ARRAY"]['signer']['key'].'",
						"sign_as": "sign",
						"group": "1"
					  }
					}	
				   ';	
	$ar_add_ass1 = execClick($data_string,$_API_URL."/lists?access_token=".$_API_TOKEN);	
	echo ($_FL_DEBUG ? print_r($ar_add_ass1,TRUE) : "");
	
	#### INSERE NA TABELA - ASSINADOR 1 - PARTICIPANTE ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital_assinatura
				     (
						cd_contrato_digital, 
						tp_assinatura,
						id_assinador,
						id_assinatura, 
						ds_url_assinatura, 
						json_assinatura
					 )
				VALUES 
				     (
						".$_CD_DOC.",
						'P',
						'".$ar_ass1["ARRAY"]['signer']['key']."',
						'".$ar_add_ass1["ARRAY"]['list']['request_signature_key']."', 
						'".$ar_add_ass1["ARRAY"]['list']['url']."', 
						'".$ar_add_ass1["JSON"]."'					 
					 )
		      ";
	$ob_resul = pg_query($db,$qr_sql);
	#echo $qr_sql; exit;


	######################################################################################################
	echo ($_FL_DEBUG ? "ASSINADOR 2 <BR>" : "");
	$email_testemunha1 = "ct1@familiaprevidencia.com.br";
	
	#### BUSCA SIGNATARIO CADASTRADO ####
	$id_signatario_testemunha1 = getSignatario($email_testemunha1, "email");

	if(trim($email_testemunha1) != "")
	{
		$ar_ass2["ARRAY"]['signer']['key'] = trim($id_signatario_testemunha1);
	}
	else
	{
		#### CRIAR ASSINADOR 2 - TESTEMUNHA 1 ####
		$data_string = '
							{
							  "signer": {
								"email": "'.$email_testemunha1.'",
								"auths": ["email"],
								"has_documentation": true
							  }
							}	
					   ';
		$ar_ass2 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);	
	}
	echo ($_FL_DEBUG ? print_r($ar_ass2,TRUE) : "");
	
/*	
	#### CRIAR ASSINADOR 2 - TESTEMUNHA 1 ####
	$data_string = '
						{
						  "signer": {
							"email": "ct1@familiaprevidencia.com.br",
							"auths": ["email"],
							"has_documentation": true
						  }
						}	
				   ';
	$ar_ass2 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);
	echo ($_FL_DEBUG ? print_r($ar_ass2,TRUE) : "");
*/
	
	#### ADD ASSINADOR 2 - TESTEMUNHA 1 ####
	$data_string = '
					{
					  "list": {
						"document_key": "'.$ar_doc["ARRAY"]['document']['key'].'",
						"signer_key": "'.$ar_ass2["ARRAY"]['signer']['key'].'",
						"sign_as": "witness",
						"group": "2"
					  }
					}	
				   ';	
	$ar_add_ass2 = execClick($data_string,$_API_URL."/lists?access_token=".$_API_TOKEN);	
	echo ($_FL_DEBUG ? print_r($ar_add_ass2,TRUE) : "");

	#### INSERE NA TABELA - ASSINADOR 2 - TESTEMUNHA 1 ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital_assinatura
				     (
						cd_contrato_digital, 
						tp_assinatura,
						id_assinador, 
						id_assinatura, 
						ds_url_assinatura, 
						json_assinatura
					 )
				VALUES 
				     (
						".$_CD_DOC.",
						'T1',
						'".$ar_ass2["ARRAY"]['signer']['key']."',
						'".$ar_add_ass2["ARRAY"]['list']['request_signature_key']."', 
						'".$ar_add_ass2["ARRAY"]['list']['url']."', 
						'".$ar_add_ass2["JSON"]."'					 
					 )
		      ";
	$ob_resul = pg_query($db,$qr_sql);	

	######################################################################################################
	echo ($_FL_DEBUG ? "ASSINADOR 3 <BR>" : "");
	$email_testemunha2 = "ct2@familiaprevidencia.com.br";
	
	#### BUSCA SIGNATARIO CADASTRADO ####
	$id_signatario_testemunha2 = getSignatario($email_testemunha2, "email");

	if(trim($email_testemunha2) != "")
	{
		$ar_ass3["ARRAY"]['signer']['key'] = trim($id_signatario_testemunha2);
	}
	else
	{
		#### CRIAR ASSINADOR 3 - TESTEMUNHA 2 ####
		$data_string = '
							{
							  "signer": {
								"email": "'.$email_testemunha2.'",
								"auths": ["email"],
								"has_documentation": true
							  }
							}	
					   ';
		$ar_ass3 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);	
	}
	echo ($_FL_DEBUG ? print_r($ar_ass3,TRUE) : "");
	
/*	
	#### CRIAR ASSINADOR 3 - TESTEMUNHA 2 ####
	$data_string = '
						{
						  "signer": {
							"email": "ct2@familiaprevidencia.com.br",
							"auths": ["email"],
							"has_documentation": true
						  }
						}	
				   ';
	$ar_ass3 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);
	echo ($_FL_DEBUG ? print_r($ar_ass3,TRUE) : "");
*/	
	#### ADD ASSINADOR 3 - TESTEMUNHA 2 ####
	$data_string = '
					{
					  "list": {
						"document_key": "'.$ar_doc["ARRAY"]['document']['key'].'",
						"signer_key": "'.$ar_ass3["ARRAY"]['signer']['key'].'",
						"sign_as": "witness",
						"group": "3"
					  }
					}	
				   ';	
	$ar_add_ass3 = execClick($data_string,$_API_URL."/lists?access_token=".$_API_TOKEN);	
	echo ($_FL_DEBUG ? print_r($ar_add_ass3,TRUE) : "");

	#### INSERE NA TABELA - ASSINADOR 3 - TESTEMUNHA 2 ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital_assinatura
				     (
						cd_contrato_digital, 
						tp_assinatura,
						id_assinador, 
						id_assinatura, 
						ds_url_assinatura, 
						json_assinatura
					 )
				VALUES 
				     (
						".$_CD_DOC.",
						'T2',
						'".$ar_ass3["ARRAY"]['signer']['key']."',
						'".$ar_add_ass3["ARRAY"]['list']['request_signature_key']."', 
						'".$ar_add_ass3["ARRAY"]['list']['url']."', 
						'".$ar_add_ass3["JSON"]."'					 
					 )
		      ";
	$ob_resul = pg_query($db,$qr_sql);	
				   
				   
	######################################################################################################			   
	echo ($_FL_DEBUG ? print_r("ASSINADOR 4 <BR>",TRUE) : "");
	$email_validador = "ct@familiaprevidencia.com.br";
	
	#### BUSCA SIGNATARIO CADASTRADO ####
	$id_signatario_validador = getSignatario($email_validador, "email");

	if(trim($id_signatario_validador) != "")
	{
		$ar_ass4["ARRAY"]['signer']['key'] = trim($id_signatario_validador);
	}
	else
	{
		#### CRIAR ASSINADOR ####
		$data_string = '
							{
							  "signer": {
								"email": "'.$email_validador.'",
								"auths": ["email"],
								"has_documentation": true
							  }
							}	
					   ';
		$ar_ass4 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);	
	}
	echo ($_FL_DEBUG ? print_r($ar_ass4,TRUE) : "");	
	
	/*
	#### CRIAR ASSINADOR 4 - VALIDADOR ####
	$data_string = '
						{
						  "signer": {
							"email": "ct@familiaprevidencia.com.br",
							"auths": ["email"],
							"has_documentation": true
						  }
						}	
				   ';
	$ar_ass4 = execClick($data_string,$_API_URL."/signers?access_token=".$_API_TOKEN);
	echo ($_FL_DEBUG ? print_r($ar_ass4,TRUE) : "");
	*/
	
	#### ADD ASSINADOR 4 - VALIDADOR ####
	$data_string = '
					{
					  "list": {
						"document_key": "'.$ar_doc["ARRAY"]['document']['key'].'",
						"signer_key": "'.$ar_ass4["ARRAY"]['signer']['key'].'",
						"sign_as": "validator",
						"group": "4"
					  }
					}	
				   ';				   
	$ar_add_ass4 = execClick($data_string,$_API_URL."/lists?access_token=".$_API_TOKEN);	
	echo ($_FL_DEBUG ? print_r($ar_add_ass4,TRUE) : "");
	
	#### INSERE NA TABELA - ASSINADOR 4 - VALIDADOR ####
	$qr_sql = "
				INSERT INTO clicksign.contrato_digital_assinatura
				     (
						cd_contrato_digital, 
						tp_assinatura,
						id_assinador, 
						id_assinatura, 
						ds_url_assinatura, 
						json_assinatura
					 )
				VALUES 
				     (
						".$_CD_DOC.",
						'V',
						'".$ar_ass4["ARRAY"]['signer']['key']."',
						'".$ar_add_ass4["ARRAY"]['list']['request_signature_key']."', 
						'".$ar_add_ass4["ARRAY"]['list']['url']."', 
						'".$ar_add_ass4["JSON"]."'					 
					 )
		      ";
	$ob_resul = pg_query($db,$qr_sql);	
	
	######################################################################################################
	
	#echo $_API_URL_ASS.$ar_add_ass1["ARRAY"]['list']['request_signature_key'];
	
	
	#### URL PARA PARTICIPANTE ASSINAR ####
	echo ($_FL_DEBUG ? $ar_add_ass1["ARRAY"]['list']['url']."<br>" : "");
	echo ($_FL_DEBUG ? $ar_add_ass1["ARRAY"]['list']['request_signature_key']."<br>" : "");

	if(!$_FL_DEBUG)
	{
		//ECHO "IR";
		echo '<meta http-equiv="refresh" content="0; url='.$ar_add_ass1["ARRAY"]['list']['url'].'">';
	}
	#echo '<meta http-equiv="refresh" content="0; url='.$ar_add_ass1["ARRAY"]['list']['url'].'">';
	#header('Location: '.$ar_add_ass1["ARRAY"]['list']['url']);
	exit;
	
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();  
	pg_close($db);



	function execClick($data_string,$url)
	{
		$ch = curl_init($url);			   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		#print_r($response);
		#echo "<hr>";
		
		$a = $response;
		$b = json_decode($response,TRUE);
		if (!(json_last_error() === JSON_ERROR_NONE))
		{
			switch (json_last_error()) 
			{
				case JSON_ERROR_DEPTH:
					echo utf8_encode('(JSON) A profundidade máxima da pilha foi excedida');
				break;
				case JSON_ERROR_STATE_MISMATCH:
					echo utf8_encode('(JSON) Inválido ou mal formado');
				break;
				case JSON_ERROR_CTRL_CHAR:
					echo utf8_encode('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
				break;
				case JSON_ERROR_SYNTAX:
					echo utf8_encode('(JSON) Erro de sintaxe');
				break;
				case JSON_ERROR_UTF8:
					echo utf8_encode('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
				break;
				default:
					echo utf8_encode('(JSON) Erro não identificado');
				break;
			}
		}
		else
		{
			
			#echo "<PRE>";
			#print_r($b);
			#echo $b['document']['key'];
			#exit;
			#echo "DOC KEY => ".$b['document']['key']."<BR>";
			#echo "ASS 1 KEY => ".$b['document']['signers'][0]['key']."<BR>";
			#echo "</PRE>";

			
		}		
		
/*
Array
(
    [JSON] => {"errors":["o valor do campo deadline_at nÃ£o Ã© uma data vÃ¡lida (formato ISO8601 Ã© requerido)"]}
    [ARRAY] => Array
        (
            [errors] => Array
                (
                    [0] => o valor do campo deadline_at nÃ£o Ã© uma data vÃ¡lida (formato ISO8601 Ã© requerido)
                )

        )

)
*/		
		
		return array("JSON" => $a, "ARRAY" => $b);
	}
	
	#############################################################################################################################
	function getSignatario($email, $tp_token)
	{
		global $db;
		
		$qr_sql = "
					SELECT ".($tp_token == "email" ? "id_signatario_email": "id_signatario_sms")." AS id_signatario
					  FROM clicksign.signatario
					 WHERE email = TRIM(LOWER('".strtolower(trim($email))."'))
					   AND ".($tp_token == "email" ? "id_signatario_email IS NOT NULL": "id_signatario_sms IS NOT NULL")."
			      ";
		#echo $qr_sql;
		$ob_resul = @pg_query($db, $qr_sql);
		$ar_reg = @pg_fetch_array($ob_resul);
		
		return $ar_reg['id_signatario'];
	}		
?>