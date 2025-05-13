<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	require_once('inc/sessao_auto_atendimento.php'); ### AJUSTAR SESSAO ###
	include_once('inc/conexao.php');
	
	$ar_ret = Array();
	$ar_ret["fl_erro"] = "S";
	$ar_ret["cd_erro"] = "1";
	$ar_ret["retorno"] = ("Não possível simular, parâmetros inválidos.");
	$ar_ret["ar_reg"]  = Array();
	
	if(($_POST['ds_funcao'] == 'calculaLiquidacaoTotal') AND (trim($_POST['dt_pagamento']) != '') AND (intval($_POST['cd_contrato']) > 0))
	{
		$qr_sql = "
					SELECT pck_emprestimos_web_fnc_calc_liq_total AS retorno
					  FROM oracle.pck_emprestimos_web_fnc_calc_liq_total
					     (
							".$_SESSION['EMP'].",
							".$_SESSION['RE'].",
							".$_SESSION['SEQ'].",
							'".$_POST['dt_pagamento']."', 
							".intval($_POST['cd_contrato'])."
					     );
		          ";
		#echo $qr_sql;		  
		$ob_resul = pg_query($db,$qr_sql);
		if(intval(pg_num_rows($ob_resul)) > 0)
		{
			#"{"v_vlr_liq":4278.58, "erro": "N", "msg":""}"
			$ar_reg = pg_fetch_array($ob_resul);
			
			$ar_ret["ar_reg"] = json_decode($ar_reg['retorno'],true);
			if ((json_last_error() === JSON_ERROR_NONE))
			{
				if(trim(strtoupper($ar_ret["ar_reg"]['erro'])) == "S")
				{
					#"{"v_vlr_liq": "0", "erro": "S", "msg":"Erro em fnc_ext_com_liq. O índice 30 ainda não foi cadastrado para o mês 08/2018(EMP-0072)(EMP-0098)"}"
					$ar_ret['fl_erro'] = "S";
					$ar_ret['cd_erro'] = "4";
					$ar_ret['retorno'] = ("Não possível simular, entre em contato com a nossa central de atendimento 0800512596 de segunda a sexta, das 8h às 17 horas.");	
					
					if(preg_match('/10.63./', $_SERVER['REMOTE_ADDR']))
					{
						$ar_ret['retorno'].= chr(10).chr(10).utf8_encode($ar_ret["ar_reg"]['msg']);
					}
					
					$ar_ret["ar_reg"]  = Array();
				}
				else
				{
					$ar_ret['fl_erro'] = "N";
					$ar_ret['cd_erro'] = "0";
					$ar_ret['retorno'] = "";
					$ar_ret["ar_reg"]['vl_valor'] = number_format(floatval($ar_ret["ar_reg"]['v_vlr_liq']),2,",",".");
					$ar_ret["ar_reg"]['vl_valor_calc'] = floatval($ar_ret["ar_reg"]['v_vlr_liq']);
					$ar_ret["ar_reg"]['vl_valor_abatimento'] = number_format(floatval($ar_ret["ar_reg"]['v_vlr_econom']),2,",",".");
				}
			}
			else
			{
				$ar_ret['fl_erro'] = "S";
				$ar_ret['cd_erro'] = "3";	
				$ar_ret['retorno'] = "ERRO JSON";
				$ar_ret["ar_reg"]  = Array();
				switch (json_last_error()) 
				{
					case JSON_ERROR_DEPTH:
						$ar_ret['retorno'] = ('(JSON) A profundidade máxima da pilha foi excedida');
					break;
					case JSON_ERROR_STATE_MISMATCH:
						$ar_ret['retorno'] = ('(JSON) Inválido ou mal formado');
					break;
					case JSON_ERROR_CTRL_CHAR:
						$ar_ret['retorno'] = ('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
					break;
					case JSON_ERROR_SYNTAX:
						$ar_ret['retorno'] = ('(JSON) Erro de sintaxe');
					break;
					case JSON_ERROR_UTF8:
						$ar_ret['retorno'] = ('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
					break;
					default:
						$ar_ret['retorno'] = ('(JSON) Erro não identificado');
					break;
				}
			}
		}
		else
		{
			$ar_ret['fl_erro'] = "S";
			$ar_ret['cd_erro'] = "4";
			$ar_ret['retorno'] = ("Não possível simular, informações não encontrada");	
			$ar_ret["ar_reg"]  = Array();			
		}
	
		#print_r($_SESSION);
		#print_r($_POST);
	}
	
	if(($_POST['ds_funcao'] == 'calculaLiquidacaoVencer') AND (trim($_POST['dt_pagamento']) != '') AND (intval($_POST['qt_parcela']) > 0) AND (intval($_POST['cd_contrato']) > 0))
	{
		#print_r($_SESSION);
		#print_r($_POST);	

		$qr_sql = "
					SELECT pck_emprestimos_web_fnc_calc_liq_prest_vencer AS retorno
					  FROM oracle.pck_emprestimos_web_fnc_calc_liq_prest_vencer
					     (
							".$_SESSION['EMP'].",
							".$_SESSION['RE'].",
							".$_SESSION['SEQ'].",
							'".$_POST['dt_pagamento']."', 
							".intval($_POST['qt_parcela']).", 
							".intval($_POST['cd_contrato'])."
					     );
		          ";
		#echo $qr_sql;
		$ob_resul = pg_query($db,$qr_sql);
		if(intval(pg_num_rows($ob_resul)) > 0)
		{
			#"{"v_vlr_liq":4278.58, "erro": "N", "msg":""}"
			$ar_reg = pg_fetch_array($ob_resul);
			
			$ar_ret["ar_reg"] = json_decode($ar_reg['retorno'],true);
			if ((json_last_error() === JSON_ERROR_NONE))
			{
				if(trim(strtoupper($ar_ret["ar_reg"]['erro'])) == "S")
				{
					#"{"v_vlr_liq": "0", "erro": "S", "msg":"Erro em fnc_ext_com_liq. O índice 30 ainda não foi cadastrado para o mês 08/2018(EMP-0072)(EMP-0098)"}"
					$ar_ret['fl_erro'] = "S";
					$ar_ret['cd_erro'] = "4";
					$ar_ret['retorno'] = ("Não possível simular, entre em contato com a nossa central de atendimento 0800512596 de segunda a sexta, das 8h às 17 horas.");	
					
					if(preg_match('/10.63./', $_SERVER['REMOTE_ADDR']))
					{
						$ar_ret['retorno'].= chr(10).chr(10).utf8_encode($ar_ret["ar_reg"]['msg']);
					}
					
					$ar_ret["ar_reg"]  = Array();
				}
				else
				{
					$ar_ret['fl_erro'] = "N";
					$ar_ret['cd_erro'] = "0";
					$ar_ret['retorno'] = "";
					$ar_ret["ar_reg"]['vl_valor'] = number_format(floatval($ar_ret["ar_reg"]['v_vlr_liq']),2,",",".");
					$ar_ret["ar_reg"]['vl_valor_calc'] = floatval($ar_ret["ar_reg"]['v_vlr_liq']);
					$ar_ret["ar_reg"]['vl_valor_abatimento'] = number_format(floatval($ar_ret["ar_reg"]['v_vlr_econom']),2,",",".");
				}
			}
			else
			{
				$ar_ret['fl_erro'] = "S";
				$ar_ret['cd_erro'] = "3";	
				$ar_ret['retorno'] = "ERRO JSON";
				$ar_ret["ar_reg"]  = Array();
				switch (json_last_error()) 
				{
					case JSON_ERROR_DEPTH:
						$ar_ret['retorno'] = ('(JSON) A profundidade máxima da pilha foi excedida');
					break;
					case JSON_ERROR_STATE_MISMATCH:
						$ar_ret['retorno'] = ('(JSON) Inválido ou mal formado');
					break;
					case JSON_ERROR_CTRL_CHAR:
						$ar_ret['retorno'] = ('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
					break;
					case JSON_ERROR_SYNTAX:
						$ar_ret['retorno'] = ('(JSON) Erro de sintaxe');
					break;
					case JSON_ERROR_UTF8:
						$ar_ret['retorno'] = ('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
					break;
					default:
						$ar_ret['retorno'] = ('(JSON) Erro não identificado');
					break;
				}
			}
		}
		else
		{
			$ar_ret['fl_erro'] = "S";
			$ar_ret['cd_erro'] = "4";
			$ar_ret['retorno'] = ("Não possível simular, informações não encontrada");	
			$ar_ret["ar_reg"]  = Array();			
		}	
	}
	
	if(($_POST['ds_funcao'] == 'calculaLiquidacaoAtrasada') AND (trim($_POST['dt_pagamento']) != '') AND (intval($_POST['qt_parcela']) > 0) AND (intval($_POST['cd_contrato']) > 0))
	{
		#print_r($_SESSION);
		#print_r($_POST); 
		#exit;

		$qr_sql = "
					SELECT pck_emprestimos_web_fnc_calc_liq_prest_atrasada AS retorno
					  FROM oracle.pck_emprestimos_web_fnc_calc_liq_prest_atrasada
					     (
							".$_SESSION['EMP'].",
							".$_SESSION['RE'].",
							".$_SESSION['SEQ'].",
							'".$_POST['dt_pagamento']."', 
							".intval($_POST['qt_parcela']).", 
							".intval($_POST['cd_contrato'])."
					     );
		          ";
		#echo $qr_sql;
		$ob_resul = pg_query($db,$qr_sql);
		if(intval(pg_num_rows($ob_resul)) > 0)
		{
			#"{"v_vlr_liq":4278.58, "erro": "N", "msg":""}"
			$ar_reg = pg_fetch_array($ob_resul);
			
			$ar_ret["ar_reg"] = json_decode($ar_reg['retorno'],true);
			if ((json_last_error() === JSON_ERROR_NONE))
			{
				if(trim(strtoupper($ar_ret["ar_reg"]['erro'])) == "S")
				{
					#"{"v_vlr_liq": "0", "erro": "S", "msg":"Erro em fnc_ext_com_liq. O índice 30 ainda não foi cadastrado para o mês 08/2018(EMP-0072)(EMP-0098)"}"
					$ar_ret['fl_erro'] = "S";
					$ar_ret['cd_erro'] = "4";
					$ar_ret['retorno'] = ("Não possível simular, entre em contato com a nossa central de atendimento 0800512596 de segunda a sexta, das 8h às 17 horas.");	
					
					if(preg_match('/10.63./', $_SERVER['REMOTE_ADDR']))
					{
						$ar_ret['retorno'].= chr(10).chr(10).utf8_encode($ar_ret["ar_reg"]['msg']);
					}
					
					$ar_ret["ar_reg"]  = Array();
				}
				else
				{
					$ar_ret['fl_erro'] = "N";
					$ar_ret['cd_erro'] = "0";
					$ar_ret['retorno'] = "";
					$ar_ret["ar_reg"]['vl_valor'] = number_format(floatval($ar_ret["ar_reg"]['v_vlr_liq']),2,",",".");
					$ar_ret["ar_reg"]['vl_valor_calc'] = floatval($ar_ret["ar_reg"]['v_vlr_liq']);
					$ar_ret["ar_reg"]['vl_valor_abatimento'] = number_format(floatval($ar_ret["ar_reg"]['v_vlr_econom']),2,",",".");
				}
			}
			else
			{
				$ar_ret['fl_erro'] = "S";
				$ar_ret['cd_erro'] = "3";	
				$ar_ret['retorno'] = "ERRO JSON";
				$ar_ret["ar_reg"]  = Array();
				switch (json_last_error()) 
				{
					case JSON_ERROR_DEPTH:
						$ar_ret['retorno'] = ('(JSON) A profundidade máxima da pilha foi excedida');
					break;
					case JSON_ERROR_STATE_MISMATCH:
						$ar_ret['retorno'] = ('(JSON) Inválido ou mal formado');
					break;
					case JSON_ERROR_CTRL_CHAR:
						$ar_ret['retorno'] = ('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
					break;
					case JSON_ERROR_SYNTAX:
						$ar_ret['retorno'] = ('(JSON) Erro de sintaxe');
					break;
					case JSON_ERROR_UTF8:
						$ar_ret['retorno'] = ('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
					break;
					default:
						$ar_ret['retorno'] = ('(JSON) Erro não identificado');
					break;
				}
			}
		}
		else
		{
			$ar_ret['fl_erro'] = "S";
			$ar_ret['cd_erro'] = "4";
			$ar_ret['retorno'] = ("Não possível simular, informações não encontrada");	
			$ar_ret["ar_reg"]  = Array();			
		}	
	}	
	
	if(($_POST['ds_funcao'] == 'calculaPostegar') AND (intval($_POST['cd_contrato']) > 0))
	{
		#print_r($_SESSION);
		#print_r($_POST); 
		#exit;
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/simula_postergacao");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_contrato=".$_POST['cd_contrato']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		#print_r($_RETORNO); exit;
		
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
			#echo $_RETORNO['error']['status'];echo "<HR>";
			if(intval($_RETORNO['error']['status']) == 0)
			{
				#$_RETORNO['result']['contracheque']['ds_contracheque']
				
				$ar_ret['fl_erro'] = "N";
				$ar_ret['cd_erro'] = "0";
				$ar_ret['retorno'] = "";
				$ar_ret['vl_parcela'] = $_RETORNO['result']['emprestimo_postergacao']['vl_parcela_atualizada'];
				$ar_ret['dt_ultima'] = $_RETORNO['result']['emprestimo_postergacao']['dt_ultima_prestacao_atualizada'];
			}
			else
			{
				$ar_ret['fl_erro'] = "S";
				$ar_ret['cd_erro'] = "1";
				$ar_ret['retorno'] = $_RETORNO['error']['mensagem'];
				$ar_ret['vl_parcela'] = "";
				$ar_ret['dt_ultima'] = "";			
			}	
		}
		else
		{
			$ar_ret['fl_erro'] = "S";
			$ar_ret['cd_erro'] = "2";
			$ar_ret['retorno'] = ("Não foi possível simular");	
			$ar_ret['vl_parcela'] = "";
			$ar_ret['dt_ultima'] = "";			
		}
	}		
	
	if(($_POST['ds_funcao'] == 'concedePostegar') AND (intval($_POST['cd_contrato']) > 0))
	{
		#print_r($_SESSION);
		#print_r($_POST); 
		#exit;

	    if($fl_assinatura_erro)
		{
			echo "<script>
					alert('Não foi possível conceder este empréstimo.\\n\\nÉ NECESSÁRIO ASSINAR.\\n\\nFicou com dúvida, entre em contato com o teleatendimento de segunda a sexta-feira, pelo telefone 0800512596.');
				  </script>
				 ";		
			
			$qr_erro = "
						INSERT INTO projetos.log
							 (
								tipo, 
								\"local\", 
								descricao, 
								dt_cadastro
							 )
						VALUES 
							 ( 
								'EMP_WEB',
								'EMP_CONCEDE',
								'EMPRESTIMO ERRO 0:\n".$skt->Error()."\nOcorreu um erro na assinatura\n".$send."\nIP: ".$_SERVER['REMOTE_ADDR']."\n".$_SERVER['HTTP_REFERER']."',
								CURRENT_TIMESTAMP
							 )
					   ";
			@pg_query($db,$qr_erro);			
			exit;		
		}
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/concede_postergacao");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cd_contrato=".$_POST['cd_contrato']."&assinatura_participante=".urlencode($_POST['assinatura_base64']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		#print_r($_RETORNO); exit;
		
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
			#echo $_RETORNO['error']['status'];echo "<HR>";
			if(intval($_RETORNO['error']['status']) == 0)
			{
				#$_RETORNO['result']['contracheque']['ds_contracheque']
				
				$ar_ret['fl_erro'] = "N";
				$ar_ret['cd_erro'] = "0";
				$ar_ret['retorno'] = "Postergação Realizada";
			}
			else
			{
				$ar_ret['fl_erro'] = "S";
				$ar_ret['cd_erro'] = "1";
				$ar_ret['retorno'] = $_RETORNO['error']['mensagem'];
			}	
		}
		else
		{
			$ar_ret['fl_erro'] = "S";
			$ar_ret['cd_erro'] = "2";
			$ar_ret['retorno'] = ("Não foi possível postergar");	
		}
	}	
	
	echo json_encode($ar_ret);
?>