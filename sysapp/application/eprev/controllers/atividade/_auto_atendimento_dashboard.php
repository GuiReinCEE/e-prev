<?php
	#"e7a9e3f647dd33941430647118aaf2b7";"WEB - Autoatendimento"
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";
	
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
	
	$ds_arq   = "tpl/tpl_auto_atendimento_dashboard.html";
	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);		
	
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
					   'DASHBOARD' 
					 )
		      ";
	@pg_query($db,$qr_sql);  	
	
	#echo "<PRE>"; print_r($_SESSION); echo "</PRE>"; exit;
	
	$conteudo = str_replace('{DASH_EMP}', $_SESSION['EMP'], $conteudo);
	$conteudo = str_replace('{DASH_RE}', $_SESSION['RE'], $conteudo);
	$conteudo = str_replace('{DASH_SEQ}', $_SESSION['SEQ'], $conteudo);
	
	$conteudo = str_replace('{DASH_FL_BD}', ((intval($_SESSION['PLANO']) == 1) ? "" : "display:none;"), $conteudo);
	$conteudo = str_replace('{DASH_FL_CD}', ((intval($_SESSION['PLANO']) > 1) ? "" : "display:none;"), $conteudo);	
	
	$conteudo = str_replace('{DASH_FL_ATIVO}', (($_SESSION['TIPO_PARTI'] == "ATIV") ? "" : "display:none;"), $conteudo);
	$conteudo = str_replace('{DASH_FL_ASSISTIDO}', ((in_array(trim($_SESSION['TIPO_PARTI']), array("APOS","PENS","AUXD"))) ? "" : "display:none;"), $conteudo);
	
	#### CALENDARIO ####
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_calendario");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
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
	#echo "X".$FL_RETORNO; echo "<HR>";
	#echo "<PRE>"; print_r($_RETORNO);  echo "<HR>";

	if($FL_RETORNO)
	{
		#echo $_RETORNO['error']['status'];echo "<HR>";
		if(intval($_RETORNO['error']['status']) == 0)
		{
			if(count($_RETORNO['result']['calendario']) > 0)
			{
				$conteudo = str_replace('{DASH_FL_CALENDARIO}', "", $conteudo);

				$mensagem = '';

				foreach ($_RETORNO['result']['calendario'] as $key => $item) 
				{
					$mensagem .= '<p style="text-align:justify;">'.utf8_decode($item['ds_mensagem']).'</p>';
				}

				$conteudo = str_replace('{MENSAGENS}', $mensagem, $conteudo);
			}
			else 
			{
				$conteudo = str_replace('{DASH_FL_CALENDARIO}', "display:none;", $conteudo);
			}
		}
	}	

	#### CONTRACHEQUE ####
	if(in_array(trim($_SESSION['TIPO_PARTI']), array("APOS","PENS","AUXD")))
	{
		$ar_item = getMeuRetratoItem("PLANO");
		$conteudo = str_replace('{DASH_URL_CONTRACHEQUE}', 'auto_atendimento_contra_cheque.php', $conteudo);			
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_contracheque_resumo");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
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
		#echo "X".$FL_RETORNO; echo "<HR>";
		#echo "<PRE>"; print_r($_RETORNO);  echo "<HR>";		
		
		if($FL_RETORNO)
		{
			#echo $_RETORNO['error']['status'];echo "<HR>";
			if(intval($_RETORNO['error']['status']) == 0)
			{
				$conteudo = str_replace('{DASH_CC_PROVENTO_TEXTO}', "Total de Proventos", $conteudo);
				$conteudo = str_replace('{DASH_CC_DESCONTO_TEXTO}', "Total de Descontos", $conteudo);
				$conteudo = str_replace('{DASH_CC_LIQUIDO_TEXTO}', "Líquido a Receber", $conteudo);				
				
				$conteudo = str_replace('{DASH_CC_COMPETENCIA}', $_RETORNO['result']['contracheque']['ds_contracheque'], $conteudo);
				$conteudo = str_replace('{DASH_CC_DATA_PG}', $_RETORNO['result']['contracheque']['dt_pagamento'], $conteudo);
				$conteudo = str_replace('{DASH_CC_BENEFICIO}', $_RETORNO['result']['contracheque']['vl_beneficio'], $conteudo);
				$conteudo = str_replace('{DASH_CC_PROVENTO}',  $_RETORNO['result']['contracheque']['vl_provento'], $conteudo);
				$conteudo = str_replace('{DASH_CC_DESCONTO}',  $_RETORNO['result']['contracheque']['vl_desconto'], $conteudo);
				$conteudo = str_replace('{DASH_CC_LIQUIDO}',   $_RETORNO['result']['contracheque']['vl_receber'], $conteudo);
				
				
				#### GRAFICO PIZZA ###
				$conteudo = str_replace('{GRAF_CC_PROVENTO_VALOR}', ajustarValor($_RETORNO['result']['contracheque']['vl_provento']), $conteudo);
				$conteudo = str_replace('{GRAF_CC_DESCONTO_VALOR}', ajustarValor($_RETORNO['result']['contracheque']['vl_desconto']), $conteudo);
				$conteudo = str_replace('{GRAF_CC_LIQUIDO_VALOR}', ajustarValor($_RETORNO['result']['contracheque']['vl_receber']), $conteudo);
			}
		}

		$conteudo = str_replace('{GRAF_EVO_SALDO_REFERENCIA}', "[]", $conteudo);	
		$conteudo = str_replace('{GRAF_EVO_SALDO_VALOR}', "[]", $conteudo);			
	}
	else
	{
		$conteudo = str_replace('{GRAF_CC_PROVENTO_VALOR}', 0, $conteudo);
		$conteudo = str_replace('{GRAF_CC_DESCONTO_VALOR}', 0, $conteudo);
		$conteudo = str_replace('{GRAF_CC_LIQUIDO_VALOR}', 0, $conteudo);		
	}
	
	
	#### SALDO ACUMULADO ####
	$FL_CD_SALDO = FALSE;
	if($_SESSION['TIPO_PARTI'] == "ATIV")
	{
		$ar_item = getMeuRetratoItem("PLANO");
		$conteudo = str_replace('{DASH_URL_MEURETRATO_SALDO}', ($ar_item['mr_url']), $conteudo);			
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_meu_retrato_info");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		#echo "<PRE>"; print_r($_RETORNO); echo "<HR>"; exit;
		
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
		#echo "X".$FL_RETORNO; echo "<HR>";
		#echo "<PRE>"; print_r($_RETORNO);  echo "<HR>"; exit;

		$saldo_acumulado = "0,00";
		if($FL_RETORNO)
		{
			#echo $_RETORNO['error']['status'];echo "<HR>";
			if(intval($_RETORNO['error']['status']) == 0)
			{
				$FL_CD_SALDO = (ajustarValor($_RETORNO['result']['nr_saldo_acumulado']) == 0 ? FALSE : TRUE);
				
				$conteudo = str_replace('{DASH_SALDO_ACUMULADO}', $_RETORNO['result']['nr_saldo_acumulado'], $conteudo);
				
				$conteudo = str_replace('{DASH_SALDO_COTAS}', $_RETORNO['result']['qt_cotas_total'], $conteudo);
				
				#### GRAFICO PIZZA ###
				$conteudo = str_replace('{DASH_SALDO_DT_REFERENCIA}', utf8_decode($_RETORNO['result']['dt_base']), $conteudo);
				
				$conteudo = str_replace('{CONTRIB_ATE_HOJE_PARTIC_TEXTO}', 'Minha Contribuição', $conteudo);
				$conteudo = str_replace('{CONTRIB_ATE_HOJE_PORTAB_TEXTO}', 'Portabilidade', $conteudo);
				$conteudo = str_replace('{CONTRIB_ATE_HOJE_PATROC_TEXTO}', ($_SESSION['TIPO_EMPRESA'] == "P" ? "Contribuição Patrocinadora" : "Aporte Empregador"), $conteudo);
				$conteudo = str_replace('{SALDO_RENDIMENTO_TEXTO}', 'Rendimento Financeiro', $conteudo);
				$conteudo = str_replace('{PORTAB_EXIBE}', (ajustarValor($_RETORNO['result']['nr_portabilidade']) == 0 ? "display:none;" : ""), $conteudo);
				
				$conteudo = str_replace('{DASH_CONTRIB_ATE_HOJE_PARTIC_VALOR}', ($_RETORNO['result']['nr_minha_contribuicao']), $conteudo);
				$conteudo = str_replace('{DASH_CONTRIB_ATE_HOJE_PORTAB_VALOR}', ($_RETORNO['result']['nr_portabilidade']), $conteudo);
				$conteudo = str_replace('{DASH_CONTRIB_ATE_HOJE_PATROC_VALOR}', ($_RETORNO['result']['nr_patroc_contribuicao']), $conteudo);
				$conteudo = str_replace('{DASH_SALDO_RENDIMENTO_VALOR}', ($_RETORNO['result']['nr_rendimento']), $conteudo);
				
				$conteudo = str_replace('{GRAF_CONTRIB_ATE_HOJE_PARTIC_VALOR}', ajustarValor($_RETORNO['result']['nr_minha_contribuicao']), $conteudo);
				$conteudo = str_replace('{GRAF_CONTRIB_ATE_HOJE_PORTAB_VALOR}', ajustarValor($_RETORNO['result']['nr_portabilidade']), $conteudo);
				$conteudo = str_replace('{GRAF_CONTRIB_ATE_HOJE_PATROC_VALOR}', ajustarValor($_RETORNO['result']['nr_patroc_contribuicao']), $conteudo);
				$conteudo = str_replace('{GRAF_SALDO_RENDIMENTO_VALOR}', (ajustarValor($_RETORNO['result']['nr_rendimento']) < 0 ? 0 : ajustarValor($_RETORNO['result']['nr_rendimento'])), $conteudo);				
				
			}
		}
		
		##### HISTORICO SALDO ACUMULADO ####
		$valor_saldo_acumulado = "";
		$mes_saldo_acumulado   = "";
		$min_saldo_acumulado = 0;
		$max_saldo_acumulado = 0;
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_meu_retrato_historico_saldo");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		#echo "<PRE>"; print_r($_RETORNO); echo "<HR>"; exit;
		
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
		#echo "X".$FL_RETORNO; echo "<HR>";
		#echo "<PRE>"; print_r($_RETORNO);  echo "<HR>";
		
		if($FL_RETORNO)
		{
			#echo $_RETORNO['error']['status'];echo "<HR>";
			if(intval($_RETORNO['error']['status']) == 0)
			{
				$valor_saldo_acumulado = "[";
				$mes_saldo_acumulado   = "[";
				$ar_reg = $_RETORNO['result']; 
				$ar_reg = array_slice($ar_reg, 0, 6);  
				$max_saldo_acumulado = $ar_reg[0]['vl_valor'];
				$min_saldo_acumulado = $ar_reg[count($ar_reg) - 1]['vl_valor'];
				krsort($ar_reg);
				
				foreach($ar_reg as $ar_item)
				{
					//print_r($ar_item); echo "<BR>";
					
					$valor_saldo_acumulado.= ($valor_saldo_acumulado != "[" ? "," : "")."".$ar_item['vl_valor']."";
					$mes_saldo_acumulado.= ($mes_saldo_acumulado != "[" ? "," : "")."'".$ar_item['ds_referencia']."'";
				}
				$mes_saldo_acumulado.= "]";
				$valor_saldo_acumulado.= "]";
				
				#echo $mes_saldo_acumulado; echo "<BR>"; echo $valor_saldo_acumulado;
			}
		}
		#echo "<BR>"; echo $min_saldo_acumulado; echo "<BR>"; echo $max_saldo_acumulado;
		
		$fl_saldo_divide_mil = false;
		if((floatval($min_saldo_acumulado) > 99999) and (floatval($max_saldo_acumulado) > 99999))
		{
			$fl_saldo_divide_mil = true;
		}
		
		$conteudo = str_replace('{GRAF_EVO_SALDO_FL_DIVIDE_MIL}', ($fl_saldo_divide_mil ? "S" : "N"), $conteudo);	
		$conteudo = str_replace('{GRAF_EVO_SALDO_REFERENCIA}', $mes_saldo_acumulado, $conteudo);	
		$conteudo = str_replace('{GRAF_EVO_SALDO_VALOR}', $valor_saldo_acumulado, $conteudo);		
		
		####################################
		
		
		##### CONTRIBUICAO PATROCINADO #####
		$FL_CONTRIBUICAO_PATROCINADO = FALSE;
		
		if(($_SESSION['TIPO_EMPRESA'] == "P") AND (in_array($_SESSION['EMP'], array(0,9,6,21,22,23)))) 
		{
			$FL_CONTRIBUICAO_PATROCINADO = TRUE;
			$conteudo = str_replace('{DASH_SALDO_LB_CONTRIBUICAO_PATROCINADO}', 'Faça um aporte e aproveite os rendimentos do seu plano.', $conteudo);	
			$conteudo = str_replace('{DASH_SALDO_BT_CONTRIBUICAO_PATROCINADO}', 'Fazer o APORTE agora', $conteudo);				
		}
		$conteudo = str_replace('{DASH_SALDO_FL_CONTRIBUICAO_PATROCINADO}', ($FL_CONTRIBUICAO_PATROCINADO ? "" : "display:none;"), $conteudo);		
		######################		
		
		##### CONTRIBUICAO INSTITUIDOR #####
		$FL_CONTRIBUICAO_INSTITUIDOR = FALSE;
		
		if($_SESSION['TIPO_EMPRESA'] == "I")
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_boleto");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$_RETORNO = curl_exec($ch);
			curl_close ($ch);

			#echo "<PRE>"; print_r($_RETORNO); echo "</PRE>";
			
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
				#echo "<PRE>"; echo $_RETORNO['error']['status']; echo "<PRE>";
				
				if(intval($_RETORNO['error']['status']) == 0)
				{
					#echo "<PRE>"; print_r($_RETORNO); echo "<PRE>";
				
					if(count($_RETORNO['result']['boleto']) > 0)
					{
						#echo " <BR> ".$_RETORNO['result']['boleto'][0]['cd_tipo_pagamento'] ." | ".$_RETORNO['result']['boleto'][0]['dt_vencimento'] ." | ".$_RETORNO['result']['boleto'][0]['vl_pagar_programada'];
						
						if($_RETORNO['result']['boleto'][0]['cd_tipo_pagamento'] == "A")
						{
							$FL_CONTRIBUICAO_INSTITUIDOR = TRUE;
							$conteudo = str_replace('{DASH_SALDO_LB_CONTRIBUICAO_INSTITUIDOR}', 'Faça um aporte e aproveite os rendimentos do seu plano.', $conteudo);	
							$conteudo = str_replace('{DASH_SALDO_BT_CONTRIBUICAO_INSTITUIDOR}', 'Fazer o APORTE agora', $conteudo);	
							
						}
						else if($_RETORNO['result']['boleto'][0]['cd_tipo_pagamento'] == "M")
						{
							$FL_CONTRIBUICAO_INSTITUIDOR = TRUE;
							$conteudo = str_replace('{DASH_SALDO_LB_CONTRIBUICAO_INSTITUIDOR}', 'Pague sua contribuição e aproveite os rendimentos do seu plano.', $conteudo);
							$conteudo = str_replace('{DASH_SALDO_BT_CONTRIBUICAO_INSTITUIDOR}', 'Pague sua Contribuição agora', $conteudo);
						}					
					}
				}
			}
		}
		$conteudo = str_replace('{DASH_SALDO_FL_CONTRIBUICAO_INSTITUIDOR}', ($FL_CONTRIBUICAO_INSTITUIDOR ? "" : "display:none;"), $conteudo);		
		######################
		
		##### ULTIMO PAGAMENTO INSTITUIDOR #####
		$FL_PAGAMENTO_RECEBIDO = FALSE;
		
		if($_SESSION['TIPO_EMPRESA'] == "I")
		{		
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_pagamentos");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$_RETORNO = curl_exec($ch);
			curl_close ($ch);

			#echo "<PRE>"; print_r($_RETORNO); echo "<PRE>";
			
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
				#echo "<PRE>"; echo $_RETORNO['error']['status']; echo "<PRE>";
				
				if(intval($_RETORNO['error']['status']) == 0)
				{
					#$conteudo = str_replace('{DASH_SALDO_ACUMULADO}', $_RETORNO['result']['nr_saldo_acumulado'], $conteudo);
					#echo "<PRE>"; print_r($_RETORNO); echo "<PRE>";
				
					if(count($_RETORNO['result']['boleto_pago']) > 0)
					{
						$i = 0;
						$f = (count($_RETORNO['result']['boleto_pago']) > 5 ? 5 : count($_RETORNO['result']['boleto_pago'])) ;
						while($i < $f)
						{
							#echo " <BR> ".$_RETORNO['result']['boleto_pago'][$i]['dt_pago'] ." | ".$_RETORNO['result']['boleto_pago'][$i]['vl_pago'];
							
							$conteudo = str_replace('{DASH_LB_PAGAMENTO_RECEBIDO_'.$i.'}', "R$", $conteudo);
							$conteudo = str_replace('{DASH_VL_PAGAMENTO_RECEBIDO_'.$i.'}', $_RETORNO['result']['boleto_pago'][$i]['vl_pago'], $conteudo);
							$conteudo = str_replace('{DASH_DT_PAGAMENTO_RECEBIDO_'.$i.'}', $_RETORNO['result']['boleto_pago'][$i]['dt_pago'], $conteudo);
							$i++;
							
							$FL_PAGAMENTO_RECEBIDO = TRUE;
						}
						
						
					}
				}
			}
		}
		
		$conteudo = str_replace('{DASH_FL_PAGAMENTO_RECEBIDO}', ($FL_PAGAMENTO_RECEBIDO ? "" : "display:none;"), $conteudo);
		$i = 0;
		$f = 10;
		while($i < $f)
		{
			$conteudo = str_replace('{DASH_LB_PAGAMENTO_RECEBIDO_'.$i.'}', "", $conteudo);
			$conteudo = str_replace('{DASH_VL_PAGAMENTO_RECEBIDO_'.$i.'}', "", $conteudo);
			$conteudo = str_replace('{DASH_DT_PAGAMENTO_RECEBIDO_'.$i.'}', "", $conteudo);
			$i++;
		}		
	}
	else
	{
		$conteudo = str_replace('{GRAF_CONTRIB_ATE_HOJE_PARTIC_VALOR}', 0, $conteudo);
		$conteudo = str_replace('{GRAF_CONTRIB_ATE_HOJE_PORTAB_VALOR}', 0, $conteudo);
		$conteudo = str_replace('{GRAF_CONTRIB_ATE_HOJE_PATROC_VALOR}', 0, $conteudo);
		$conteudo = str_replace('{GRAF_SALDO_RENDIMENTO_VALOR}', 0, $conteudo);	
	}
	$conteudo = str_replace('{DASH_FL_CD_SALDO}', ($FL_CD_SALDO == TRUE ? "" : "display:none;") , $conteudo);
	

	#### RENTABILIDADE ####
	$ano_rentabilidade = date('Y');
	$ar_grafico = getRentabilidade($ano_rentabilidade);
	if(intval($ar_grafico["mes_maximo"]) == 0)
	{
		$ano_rentabilidade = date('Y') - 1;
		$ar_grafico = getRentabilidade($ano_rentabilidade);
	}
	
	$mes_rentabilidade = $ar_grafico['mes_maximo'];
	#echo "<PRE>";	print_r($ar_grafico);	echo "</PRE>";	
	$conteudo = str_replace('{GRAF_RENTABILIDADE_TITULO}', $ar_grafico["titulo"], $conteudo);
	$conteudo = str_replace('{GRAF_RENTABILIDADE_ACUMULADA}', $ar_grafico["ar_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_RENTABILIDADE_ACUMULADA_ANO}', $ar_grafico["pr_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_RENTABILIDADE_MENSAL}', $ar_grafico["ar_mes"], $conteudo);
	$conteudo = str_replace('{GRAF_RENTABILIDADE_REFERENCIA}', $ar_grafico["ar_referencia"], $conteudo);
	$conteudo = str_replace('{GRAFICO_RENTABILIDADE_POSICAO}', $ar_grafico["posicao"], $conteudo);
	
	$conteudo = str_replace('{GRAF_COMPARATIVO_TITULO}', 'COMPARATIVO - ACUMULADO - '.$ano_rentabilidade, $conteudo);
	$conteudo = str_replace('{GRAF_COMPARATIVO_ANO}', $ano_rentabilidade, $conteudo);
	$conteudo = str_replace('{GRAF_COMPARATIVO_REFERENCIA}', $ar_grafico["ar_referencia"], $conteudo);
	$conteudo = str_replace('{GRAFICO_COMPARATIVO_POSICAO}', "Posição referente a última publicação", $conteudo);
	$conteudo = str_replace('{GRAF_RENTAB_PLANO_TITULO}', "PLANO", $conteudo);

	$ar_inpc = getINDICE($ano_rentabilidade,$mes_rentabilidade,1,"INPC");
	#echo "<PRE>";	print_r($ar_inpc);	echo "</PRE>";	
	
	$conteudo = str_replace('{GRAF_INPC_TITULO}', $ar_inpc['titulo'], $conteudo);
	$conteudo = str_replace('{GRAF_INPC_ACUMULADO}', $ar_inpc["ar_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_INPC_ACUMULADO_ANO}', $ar_inpc["pr_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_INPC_MENSAL}', $ar_inpc["ar_mes"], $conteudo);
	$conteudo = str_replace('{GRAF_INPC_REFERENCIA}', $ar_inpc["ar_referencia"], $conteudo);

	$ar_poupanca = getINDICE($ano_rentabilidade,$mes_rentabilidade,8,"POUPANÇA");
	#echo "<PRE>";	print_r($ar_poupanca);	echo "</PRE>";	
	
	$conteudo = str_replace('{GRAF_POUPANCA_TITULO}', $ar_poupanca['titulo'], $conteudo);
	$conteudo = str_replace('{GRAF_POUPANCA_ACUMULADO}', $ar_poupanca["ar_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_POUPANCA_ACUMULADO_ANO}', $ar_poupanca["pr_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_POUPANCA_MENSAL}', $ar_poupanca["ar_mes"], $conteudo);
	$conteudo = str_replace('{GRAF_POUPANCA_REFERENCIA}', $ar_poupanca["ar_referencia"], $conteudo);
	
	$ar_igpm = getINDICE($ano_rentabilidade,$mes_rentabilidade,4,"IGPM");
	#echo "<PRE>";	print_r($ar_igpm);	echo "</PRE>";	
	
	$conteudo = str_replace('{GRAF_IGPM_TITULO}', $ar_igpm['titulo'], $conteudo);
	$conteudo = str_replace('{GRAF_IGPM_ACUMULADO}', $ar_igpm["ar_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_IGPM_ACUMULADO_ANO}', $ar_igpm["pr_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_IGPM_MENSAL}', $ar_igpm["ar_mes"], $conteudo);
	$conteudo = str_replace('{GRAF_IGPM_REFERENCIA}', $ar_igpm["ar_referencia"], $conteudo);
	
	$ar_cdi = getINDICE($ano_rentabilidade,$mes_rentabilidade,3,"CDI");
	#echo "<PRE>";	print_r($ar_cdi);	echo "</PRE>";	
	
	$conteudo = str_replace('{GRAF_CDI_TITULO}', $ar_cdi['titulo'], $conteudo);
	$conteudo = str_replace('{GRAF_CDI_ACUMULADO}', $ar_cdi["ar_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_CDI_ACUMULADO_ANO}', $ar_cdi["pr_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_CDI_MENSAL}', $ar_cdi["ar_mes"], $conteudo);
	$conteudo = str_replace('{GRAF_CDI_REFERENCIA}', $ar_cdi["ar_referencia"], $conteudo);

	
	$ar_ipca_ibge = getINDICE($ano_rentabilidade,$mes_rentabilidade,6,"IPCA-IBGE");
	#echo "<PRE>";	print_r($ar_cdi);	echo "</PRE>";	
	
	$conteudo = str_replace('{GRAF_IPCAIBGE_TITULO}', $ar_ipca_ibge['titulo'], $conteudo);
	$conteudo = str_replace('{GRAF_IPCAIBGE_ACUMULADO}', $ar_ipca_ibge["ar_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_IPCAIBGE_ACUMULADO_ANO}', $ar_ipca_ibge["pr_acumulado"], $conteudo);
	$conteudo = str_replace('{GRAF_IPCAIBGE_MENSAL}', $ar_ipca_ibge["ar_mes"], $conteudo);
	$conteudo = str_replace('{GRAF_IPCAIBGE_REFERENCIA}', $ar_ipca_ibge["ar_referencia"], $conteudo);
	
	#### RENTABILIDADE ANTERIOR ####
	if((intval($_SESSION['PLANO']) == 1) OR (in_array(trim($_SESSION['TIPO_PARTI']), array("APOS","PENS","AUXD"))))
	{
		$tb_rentab_ano = '<tr><td align="center"><a href="auto_atendimento_rent_cotas.php?ano='.$ano_rentabilidade.'" style="font-size: 11pt;">'.$ano_rentabilidade.'</a></td></tr>';
		$nr_ano = ($ano_rentabilidade - 1);
		$nr_fim = ($ano_rentabilidade - 9);
		while($nr_ano >= $nr_fim)
		{
			$tb_rentab_ano.='<tr><td align="center"><a href="auto_atendimento_rent_cotas.php?ano='.$nr_ano.'" style="font-size: 11pt;">'.$nr_ano.'</a></td></tr>';
			$nr_ano--;
		}
		$conteudo = str_replace('{DASH_RENTABILIDADE_ANO_ANTERIOR}', $tb_rentab_ano, $conteudo);		
	}
	else
	{
		$ob_rentab_ano = getMeuRetratoRentabilidadeAnterior();
		$tb_rentab_ano = '<tr><td align="center" style="font-weight: bold;"><a href="auto_atendimento_rent_cotas.php?ano='.$ano_rentabilidade.'" style="font-weight: bold; font-size: 11pt;">'.$ano_rentabilidade.'</a></td><td width="15"></td><td align="right" style="font-weight: bold;">'.number_format($ar_grafico["pr_acumulado"],2,",",".")."%".'</td></tr>';
		foreach($ob_rentab_ano as $ar_rentab_ano)
		{
			$tb_rentab_ano.='<tr><td align="center"><a href="auto_atendimento_rent_cotas.php?ano='.$ar_rentab_ano['nr_ano'].'" style="font-size: 11pt;">'.$ar_rentab_ano['nr_ano'].'</a></td><td width="15"></td><td align="right">'.number_format($ar_rentab_ano['nr_cota_acumulada'],2,",",".")."%".'</td></tr>';
		}
		$conteudo = str_replace('{DASH_RENTABILIDADE_ANO_ANTERIOR}', $tb_rentab_ano, $conteudo);
	}
	
	#### EQUILIBRIO ATUARIAL SOMENTE BD ####
	if((intval($_SESSION['PLANO']) == 1) AND ($_SESSION['TIPO_PARTI'] == "ATIV"))
	{
		/*
		DASH_GRAFICO_EQUILIBRIO_PLANO
		*/
		
		$ar_graf_equilibrio = getEquilibrio($_SESSION['EMP'], $_SESSION['PLANO']);
		if(count($ar_graf_equilibrio["ar_tabela"]) > 0)
		{
			$conteudo = str_replace('{FL_GRAF_EQUILIBRIO_IMAGEM}', 'display:none;', $conteudo);
			$conteudo = str_replace('{FL_GRAF_EQUILIBRIO}', '', $conteudo);
			
			$conteudo = str_replace('{GRAF_EQUILIBRIO_TITULO}', $ar_graf_equilibrio['titulo'], $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_REFERENCIA}', $ar_graf_equilibrio["ar_ano"], $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_PROVISAO}', $ar_graf_equilibrio["ar_provisao"], $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_COBERTURA}', $ar_graf_equilibrio["ar_cobertura"], $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_DESCRICAO}', $ar_graf_equilibrio["ds_equilibrio"], $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_POSICAO}', $ar_graf_equilibrio["posicao"], $conteudo);

			$tb_equilibrio = "";
			$i = 0;
			$f = count($ar_graf_equilibrio["ar_tabela"]);
			while($i < $f)
			{
				$tb_equilibrio.='
									<tr>
										<td align="center" height="30" style="font-weight:bold;">'.$ar_graf_equilibrio["ar_tabela"][$i]['nr_ano'].'</td>
										<td></td>
										<td align="right"><div style="padding-left: 10px; padding-right: 10px; border-radius: 13px; background-color: #059D44; color:#FFFFFF; font-weight:bold;">'.number_format($ar_graf_equilibrio["ar_tabela"][$i]['vl_provisao'],2,",",".").'</div></td>
										<td></td>
										<td align="right"><div style="padding-left: 10px; padding-right: 10px; border-radius: 13px; background-color: #3C2690; color:#FFFFFF; font-weight:bold;">'.number_format($ar_graf_equilibrio["ar_tabela"][$i]['vl_cobertura'],2,",",".").'</div></td>									
									</tr>
								';
				
				$i++;
			}
			$conteudo = str_replace('{GRAF_EQUILIBRIO_TABELA}', $tb_equilibrio, $conteudo);		
			
			$conteudo = str_replace('{DASH_GRAFICO_EQUILIBRIO_PLANO}', "pixel.png", $conteudo);	
			$conteudo = str_replace('{DASH_URL_MEURETRATO_SIMULACAO_EQUILIBRIO}', "", $conteudo);
		}
		else
		{
			$ar_item = getMeuRetratoItem("PLANO");
			$conteudo = str_replace('{DASH_URL_MEURETRATO_SIMULACAO_EQUILIBRIO}', ($ar_item['mr_url']), $conteudo);		
			$conteudo = str_replace('{DASH_GRAFICO_EQUILIBRIO_PLANO}', ($ar_item['arquivo_comparativo']), $conteudo);		
			$conteudo = str_replace('{DASH_FL_EQ}', '', $conteudo);				
			
			$conteudo = str_replace('{FL_GRAF_EQUILIBRIO_IMAGEM}', '', $conteudo);
			$conteudo = str_replace('{FL_GRAF_EQUILIBRIO}', 'display:none;', $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_REFERENCIA}', '[]', $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_PROVISAO}', '[]', $conteudo);
			$conteudo = str_replace('{GRAF_EQUILIBRIO_COBERTURA}', '[]', $conteudo);
			
			
		}
	}
	else
	{
		$conteudo = str_replace('{DASH_FL_EQ}', 'display:none;', $conteudo);
		$conteudo = str_replace('{FL_GRAF_EQUILIBRIO_IMAGEM}', 'display:none;', $conteudo);
		$conteudo = str_replace('{FL_GRAF_EQUILIBRIO}', 'display:none;', $conteudo);		
		$conteudo = str_replace('{DASH_GRAFICO_EQUILIBRIO_PLANO}', "pixel.png", $conteudo);	
		$conteudo = str_replace('{DASH_URL_MEURETRATO_SIMULACAO_EQUILIBRIO}', "", $conteudo);	
		$conteudo = str_replace('{GRAF_EQUILIBRIO_REFERENCIA}', '[]', $conteudo);
		$conteudo = str_replace('{GRAF_EQUILIBRIO_PROVISAO}', '[]', $conteudo);
		$conteudo = str_replace('{GRAF_EQUILIBRIO_COBERTURA}', '[]', $conteudo);		
	}

	#### SIMULACAO ####
	if($_SESSION['TIPO_PARTI'] == "ATIV")
	{	
		if(intval($_SESSION['PLANO']) == 1)
		{
			#### PLANOS BD ####
			#echo "PLANO BD";
			/*
			"BEN_BENEFICIO";"Benefício Simulado:";9312.37
			"BEN_CARENCIA_IDADE";"55 anos de idade";0
			"BEN_CARENCIA_TEMPO_INSS";"35 anos de vinculação à Previdência Social";0
			"BEN_CARENCIA_TEMPO_PLANO";"10 anos de contribuição ao Plano";0
			"BEN_INICIAL";"Benefício Inicial Simulado:";9312.37
			"BEN_PERCENTUAL_PROPORCIONAL";"Percentual Benefício Proporcional Simulado:";0
			"BEN_PISO_MINIMO";"Piso Mínimo:";961.71
			"BEN_PISO_MINIMO_INTEGRAL";"Piso Mínimo Integral:";0
			"BEN_PISO_MINIMO_PROPORCIONAL";"Piso Mínimo Integral:";0
			"BEN_RENTABILIDADE";"Rentabilidade Simulação:";6.00
			"BEN_SIMULACAO_MOTIVO";"Motivo";1
			"DT_BASE_SIMULACAO";"30/07/2019";0
			*/
			
			$ar_item = getMeuRetratoItem("PLANO");
			$conteudo = str_replace('{DASH_URL_MEURETRATO_SIMULACAO_BD}', ($ar_item['mr_url']), $conteudo);			
			
			$FL_SIMULACAO_MOTIVO_1 = "display:none;";
			$FL_SIMULACAO_MOTIVO_2 = "display:none;";		
			$ar_item = getMeuRetratoItem("BEN_SIMULACAO_MOTIVO");
			if(intval($ar_item['vl_valor']) == 1)
			{
				$FL_SIMULACAO_MOTIVO_1 = "";
				$FL_SIMULACAO_MOTIVO_2 = "display:none;";
			}	
			else
			{
				$FL_SIMULACAO_MOTIVO_1 = "display:none;";
				$FL_SIMULACAO_MOTIVO_2 = "";
			}
			$conteudo = str_replace('{DASH_FL_SIMULACAO_MOTIVO_1}', $FL_SIMULACAO_MOTIVO_1, $conteudo);		
			$conteudo = str_replace('{DASH_FL_SIMULACAO_MOTIVO_2}', $FL_SIMULACAO_MOTIVO_2, $conteudo);		
			
			$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INGRESSO");
			$conteudo = str_replace('{DASH_PARTICIPANTE_DT_INGRESSO_TEXTO}', ($ar_item['ds_linha']), $conteudo);
			
			$ar_item = getMeuRetratoItem("DT_BASE_SIMULACAO");
			$conteudo = str_replace('{DASH_DT_BASE_SIMULACAO_TEXTO}', ($ar_item['ds_linha']), $conteudo);
				
			$ar_item = getMeuRetratoItem("BEN_INICIAL");
			$conteudo = str_replace('{DASH_BEN_INICIAL_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);	
			
			$ar_item = getMeuRetratoItem("BEN_CARENCIA_IDADE");
			$conteudo = str_replace('{DASH_BEN_CARENCIA_IDADE_TEXTO}', ($ar_item['ds_linha']), $conteudo);
			$ar_item = getMeuRetratoItem("BEN_CARENCIA_TEMPO_INSS");
			$conteudo = str_replace('{DASH_BEN_CARENCIA_TEMPO_INSS_TEXTO}', ($ar_item['ds_linha']), $conteudo);	
			$ar_item = getMeuRetratoItem("BEN_CARENCIA_TEMPO_PLANO");
			$conteudo = str_replace('{DASH_BEN_CARENCIA_TEMPO_PLANO_TEXTO}', ($ar_item['ds_linha']), $conteudo);	
		}
		else
		{
			#### PLANOS CD PATROCINADO / INSTITUIDOR ####
		
			$ar_item = getMeuRetratoItem("PLANO");
			$conteudo = str_replace('{DASH_URL_MEURETRATO_SIMULACAO_CD}', ($ar_item['mr_url']), $conteudo);			
		
			$op_simulacao = "";
			if($_SESSION['TIPO_EMPRESA'] == "P")
			{
				#PARTICIPANTE_DT_INGRESSO
				#PARTICIPANTE_DT_MIGRACAO
				#BEN_DATA_SIMULACAO_TEXTO
				#BEN_DATA_SIMULACAO
				#BEN_MESES_FALTAM
				#SELECT (EXTRACT(year from (AGE( '2039-06-30','2007-02-01' ))) * 12) + EXTRACT(month from (AGE( '2039-06-30','2007-02-01' )))
				#echo "<PRE>"; print_r($ar_item['vl_valor']);  echo "<HR>";		
				
				#echo "<PRE>"; print_r($_SESSION);  echo "<HR>";	
				
				if((intval($_SESSION['PLANO']) == 2) AND ($_SESSION['MIGRADO'] == "S"))
				{
					$op_simulacao = "MIG";
					#### PATROCINADO CEEEPREV MIGRADO ####
					/*
						"PARTICIPANTE_DT_INGRESSO";"01/02/2007";0
						"BEN_DATA_SIMULACAO";"31/05/2019";0
						"BENEFICIO_REFERENCIAL";"Benefício Referencial:";1842.79
						"BENEFICIO_REFERENCIAL_ANTERIOR";"Benefício Referencial Anterior:";768.57
						"BENEFICIO_SALDADO";"Benefício Saldado:";680.84
						"BENEFICIO_SALDADO_ANTERIOR";"Benefício Saldado Anterior:";283.96
						"BEN_INICIAL";"Benefício Vitalício Simulado:";3518.27
						"BEN_MESES_FALTAM";"13 ano(s) e 22 dia(s)";157
						"BEN_REFERENCIAL";"Benefício Referencial:";1842.79
						"BEN_RENTABILIDADE";"Rentabilidade da simulação (a.a.):";5.65
						"BEN_SALDADO";"Benefício Saldado:";680.84
					*/			
					
					$ar_item = getMeuRetratoItem("BEN_DATA_SIMULACAO");
					$conteudo = str_replace('{DASH_BEN_DATA_SIMULACAO_TEXTO}', ($ar_item['ds_linha']), $conteudo);				
					
					$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INGRESSO");
					$dt_ingresso = ($ar_item['ds_linha']);			
					
					$ar_item = getMeuRetratoItem("BEN_DATA_SIMULACAO");
					$dt_simulacao = ($ar_item['ds_linha']);	
					
					$ar_item = getMeuRetratoItem("BEN_MESES_FALTAM");
					if(intval($ar_item['vl_valor']) > 0)
					{
						$qr_sql = "
									SELECT (EXTRACT(year from (AGE(((TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '".intval($ar_item['vl_valor'])." MONTH'::INTERVAL)::DATE), TO_DATE('".$dt_ingresso."','DD/MM/YYYY')))) * 12) + EXTRACT(month from (AGE(((TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '".intval($ar_item['vl_valor'])." MONTH'::INTERVAL)::DATE), TO_DATE('".$dt_ingresso."','DD/MM/YYYY')))) AS qt_tempo
								  ";
						#echo $qr_sql; exit;
						$ob_resul = pg_query($db,$qr_sql);
						$ar_reg   = pg_fetch_array($ob_resul);				

						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_TOTAL}', intval($ar_reg['qt_tempo']), $conteudo);
						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_ATUAL}', intval(intval($ar_reg['qt_tempo']) - intval($ar_item['vl_valor'])), $conteudo);
						$conteudo = str_replace('{DASH_BEN_MESES_FALTAM_TEXTO}', 'Faltam '.$ar_item['ds_linha'], $conteudo);
					}
					else
					{
						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_TOTAL}', 300, $conteudo);
						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_ATUAL}', 300, $conteudo);				
						$conteudo = str_replace('{DASH_BEN_MESES_FALTAM_TEXTO}', 'Já completei as condições do plano.', $conteudo);
					}	
					
					$ar_item = getMeuRetratoItem("BEN_RENTABILIDADE");
					$conteudo = str_replace('{DASH_BEN_RENTABILIDADE_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
					
					$ar_item = getMeuRetratoItem("BEN_SALDADO");
					$conteudo = str_replace('{DASH_BEN_SALDADO_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
					
					$ar_item = getMeuRetratoItem("BEN_REFERENCIAL");
					$conteudo = str_replace('{DASH_BEN_REFERENCIAL_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
					
					$ar_item = getMeuRetratoItem("BEN_INICIAL");
					$conteudo = str_replace('{DASH_BEN_INICIAL_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
				}
				else
				{
					$op_simulacao = "PAT";
					
					#### PATROCINADO CD ####
					$ar_item = getMeuRetratoItem("BEN_DATA_SIMULACAO");
					$conteudo = str_replace('{DASH_BEN_DATA_SIMULACAO_TEXTO}', ($ar_item['ds_linha']), $conteudo);		
					
					$ar_item = getMeuRetratoItem("PARTICIPANTE_DT_INGRESSO");
					$dt_ingresso = ($ar_item['ds_linha']);			
					
					$ar_item = getMeuRetratoItem("BEN_DATA_SIMULACAO");
					$dt_simulacao = ($ar_item['ds_linha']);	
					
					$ar_item = getMeuRetratoItem("BEN_MESES_FALTAM");
					if(intval($ar_item['vl_valor']) > 0)
					{
						$qr_sql = "
									SELECT (EXTRACT(year from (AGE(((TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '".intval($ar_item['vl_valor'])." MONTH'::INTERVAL)::DATE), TO_DATE('".$dt_ingresso."','DD/MM/YYYY')))) * 12) + EXTRACT(month from (AGE(((TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '".intval($ar_item['vl_valor'])." MONTH'::INTERVAL)::DATE), TO_DATE('".$dt_ingresso."','DD/MM/YYYY')))) AS qt_tempo
								  ";
						#echo $qr_sql; exit;
						$ob_resul = pg_query($db,$qr_sql);
						$ar_reg   = pg_fetch_array($ob_resul);				

						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_TOTAL}', intval($ar_reg['qt_tempo']), $conteudo);
						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_ATUAL}', intval(intval($ar_reg['qt_tempo']) - intval($ar_item['vl_valor'])), $conteudo);
						$conteudo = str_replace('{DASH_BEN_MESES_FALTAM_TEXTO}', 'Faltam '.$ar_item['ds_linha'], $conteudo);
					}
					else
					{
						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_TOTAL}', 300, $conteudo);
						$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_ATUAL}', 300, $conteudo);					
						$conteudo = str_replace('{DASH_BEN_MESES_FALTAM_TEXTO}', 'Já completei as condições do plano.', $conteudo);
					}
					
					$ar_item = getMeuRetratoItem("BEN_RENTABILIDADE_3");
					$conteudo = str_replace('{DASH_BEN_RENTABILIDADE_3_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
					
					$ar_item = getMeuRetratoItem("BEN_INICIAL_3");
					$conteudo = str_replace('{DASH_BEN_INICIAL_3_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
					
					$ar_item = getMeuRetratoItem("BEN_SALDO_ACUMULADO_3");
					$conteudo = str_replace('{DASH_BEN_SALDO_ACUMULADO_3_VALOR}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
				}
			}
			else
			{
				$op_simulacao = "INT";
				
				#### INSTITUIDOR ####
				/*
				"BEN_DATA_SIMULACAO";"31/05/2019";0		
				"SIMULA_CONTRIB_ATUAL_C3";"Contribuição A-C3";50.00
				"SIMULA_CONTRIB_NOVO_C3";"Contribuição B-C3";100.00
				"SIMULA_RENTABILIDADE_C3";"Rentabilidade da simulação (a.a.):";8.00
				"SIMULA_SALDO_ACUMULADO_ATUAL_C3";"Saldo A-C3";17205.398802051988597656
				"SIMULA_SALDO_ACUMULADO_NOVO_C3";"Saldo B-C3";34085.713719676651522656
				"SIMULA_TEMPO_C3";"Tempo de plano (anos):";15.00
				*/
				
				$ar_item = getMeuRetratoItem("BEN_DATA_SIMULACAO");
				$conteudo = str_replace('{DASH_BEN_DATA_SIMULACAO_TEXTO}', ($ar_item['ds_linha']), $conteudo);
				
				#### BOTAO AUMENTAR A CONTRIBUICAO ####
				$qr_sql = "
							SELECT COUNT(*) AS fl_aumentar_contribuicao
								  FROM autoatendimento.contribuicao_programada cp
								 WHERE cp.dt_cancelado          IS NULL
								   AND cp.dt_exclusao           IS NULL
								   AND cp.cd_empresa            = ".$_SESSION['EMP']."
								   AND cp.cd_registro_empregado = ".$_SESSION['RE']."
								   AND cp.seq_dependencia       = ".$_SESSION['SEQ']."
								   AND (cp.dt_inclusao  >=  TO_DATE('".trim($ar_item['ds_linha'])."','DD/MM/YYYY')
								       OR cp.dt_confirmacao  >=  TO_DATE('".trim($ar_item['ds_linha'])."','DD/MM/YYYY'))
								   
						  ";
				$ob_resul = pg_query($db,$qr_sql);
				$ar_reg   = pg_fetch_array($ob_resul);				
				$conteudo = str_replace('{DASH_BEN_FL_BOTAO_AUMENTAR_CONTRIBUICAO}', (intval($ar_reg['fl_aumentar_contribuicao']) == 0 ? "" : "display:none;"), $conteudo);				
				
				$ar_item = getMeuRetratoItem("SIMULA_RENTABILIDADE_C3");
				$conteudo = str_replace('{DASH_SIMULA_RENTABILIDADE_C3}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);	

				$ar_item = getMeuRetratoItem("SIMULA_CONTRIB_ATUAL_C3");
				$conteudo = str_replace('{DASH_SIMULA_CONTRIB_ATUAL_C3}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);
				$ar_item = getMeuRetratoItem("SIMULA_CONTRIB_NOVO_C3");
				$conteudo = str_replace('{DASH_SIMULA_CONTRIB_NOVO_C3}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);		
				
				$ar_item = getMeuRetratoItem("SIMULA_TEMPO_C3");
				$conteudo = str_replace('{DASH_SIMULA_TEMPO_C3}', number_format($ar_item['vl_valor'],0,",","."), $conteudo);		
				
				$ar_item = getMeuRetratoItem("SIMULA_SALDO_ACUMULADO_ATUAL_C3");
				$conteudo = str_replace('{DASH_SIMULA_SALDO_ACUMULADO_ATUAL_C3}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);	
				$ar_item = getMeuRetratoItem("SIMULA_SALDO_ACUMULADO_NOVO_C3");
				$conteudo = str_replace('{DASH_SIMULA_SALDO_ACUMULADO_NOVO_C3}', number_format($ar_item['vl_valor'],2,",","."), $conteudo);

				
				$conteudo = str_replace('{DASH_BEN_RENTABILIDADE_3_VALOR}', "", $conteudo);
				$conteudo = str_replace('{DASH_BEN_INICIAL_3_VALOR}', "", $conteudo);
				$conteudo = str_replace('{DASH_BEN_SALDO_ACUMULADO_3_VALOR}', "", $conteudo);	

					
			}
			
			$conteudo = str_replace('{DASH_SIMULACAO_MIGRADO}'    , ($op_simulacao == "MIG" ? "" : "display:none;"), $conteudo);
			$conteudo = str_replace('{DASH_SIMULACAO_PATROCINADO}', ($op_simulacao == "PAT" ? "" : "display:none;"), $conteudo);
			$conteudo = str_replace('{DASH_SIMULACAO_INSTITUIDOR}', ($op_simulacao == "INT" ? "" : "display:none;"), $conteudo);
		}
	}
	$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_TOTAL}', 0, $conteudo);
	$conteudo = str_replace('{DASH_BEN_MESES_CARENCIA_ATUAL}', 0, $conteudo);		


	##### LISTA OPÇÕES DO MENU #####
	##### MENU PRINCIPAL #####
	$qr_sql =  " 
				SELECT l.codigo, 
				       l.descricao, 
					   l.desviar_para 
		          FROM public.listas l
				 WHERE l.categoria = 'SSIT' 
		           AND l.valor1 = 2 
				   AND l.valor2 = 2 
				   AND l.tipo = 'A' 
				   AND l.codigo  <> 'SAIR'
				   AND (l.visao = '".$_SESSION['TIPO_EMPRESA']."' OR l.visao = '*' OR l.visao LIKE '%[".$_SESSION['EMP']."]%') 
				   AND 1 = (CASE WHEN l.codigo = 'MEUR' 
								 THEN (SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END
									     FROM meu_retrato.edicao_participante ep
									     JOIN meu_retrato.edicao e
									       ON e.cd_edicao = ep.cd_edicao
									      AND e.dt_liberacao IS NOT NULL
									    WHERE e.dt_exclusao IS NULL
									      AND ep.cd_empresa            = ".$_SESSION['EMP']."
									      AND ep.cd_registro_empregado = ".$_SESSION['RE']."
									      AND ep.seq_dependencia       = ".$_SESSION['SEQ'].")
								  ELSE 1 
							END)
				 ORDER BY l.valor 
		       ";
	$ob_resul = pg_query($db, $qr_sql);
	$menu = "";
	$qt_menu = ceil(pg_num_rows($ob_resul) / 2);
	$nr_menu = 0;
	$menu.= '
				<table border="0" width="100%">
					<tr>
						<td valign="top">
			';
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		##### SUBMENU #####
		$qr_sql = "
					SELECT item_menu, 
						   cd_materia, 
						   desviar_para  
					  FROM projetos.conteudo_site 
					 WHERE cd_secao    = '".$ar_reg['codigo']."' 
					   AND cd_site     = 2 
					   AND cd_versao   = 2
					   AND dt_exclusao IS NULL
					   AND (visao = '".$_SESSION['visao']."' OR visao = '*' OR visao LIKE '%[".$_SESSION['EMP']."]%') 
					   AND (tipo_participante = '*' OR tipo_participante LIKE '%[".$_SESSION['TIPO_PARTI']."]%')
					   AND cd_materia NOT IN (".$fl_extrato.",".$fl_senha.",".$fl_recadastramento_1.",".$fl_recadastramento_2.")
					 ORDER BY ordem 
			      ";
		$ob_sub = pg_query($db, $qr_sql);
		
		$menu.= ($nr_menu == $qt_menu ? '</td><td valign="top">' : "");
		$nr_menu = ($nr_menu == $qt_menu ? 0 : $nr_menu);
		
		$menu.= "<ul><li>".$ar_reg['descricao'].'<ul class="dash_menu">';
		while ($ar_reg_sub = pg_fetch_array($ob_sub)) 
		{
			$menu.= "<li><a href='".$ar_reg_sub['desviar_para']."'>".$ar_reg_sub['item_menu']."</a></li>";
		}
		$menu.= "</ul></li></ul>";
		$nr_menu++;
	}
	$menu.= '
						</td>
					</tr>
				</table>
			';	
	$conteudo = str_replace('{DASH_MENU}', $menu, $conteudo);


	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
	
	
	##### FUNCOES ###########################################################
	
	function getPlanoFinanceiro()
	{
		global $db;
		
		$qr_sql = "
					SELECT p.cd_plano,
						   pp.cd_plano_financ, 
						   pp.cd_empresa_financ,
						   CASE WHEN pp.tipo_plano = 4 
								THEN 'AAEX' -- INSTITUIDOR
								ELSE 'AAPR' 
						   END AS tipo_plano
					  FROM participantes p,
						   planos_patrocinadoras pp
					 WHERE p.cd_empresa            = ".$_SESSION['EMP']."
					   AND p.cd_registro_empregado = ".$_SESSION['RE']."
					   AND p.seq_dependencia       = ".$_SESSION['SEQ']."
					   AND p.cd_empresa            = pp.cd_empresa
					   AND p.cd_plano              = pp.cd_plano
				  ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);		
		
		return $ar_reg;
	}
	
	function getRentabilidade($ano)
	{
		global $db;
		
		$ar_fin = getPlanoFinanceiro();

		$ar_param['ano'] = $ano;
		$ar_param['pl']  = $ar_fin['cd_plano_financ'];
		$ar_param['emp'] = $ar_fin['cd_empresa_financ'];

		
		
		$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		
		$sql = "
				SELECT p.cd_plano,
					   pl.descricao
				  FROM public.participantes p
				  JOIN public.planos pl
					ON pl.cd_plano = p.cd_plano
				 WHERE p.cd_empresa            = ".$_SESSION['EMP']."
				   AND p.cd_registro_empregado = ".$_SESSION['RE']."
				   AND p.seq_dependencia       = ".$_SESSION['SEQ'];
		$rs  = pg_query($db,$sql);
		$reg = pg_fetch_array($rs);
		
		$NOME_PLANO = $reg['descricao'];
		
		if(in_array(intval($reg['cd_plano']), array(2,6)))
		{
			$qr_sql = " 
						SELECT i.vlr_indice AS vl_cota, 
							   i.dt_indice AS dt_cota,  
							   TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
							   TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
							   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
						  FROM public.indices i
						  JOIN public.planos_patrocinadoras pp
							ON pp.cd_indexador = i.cd_indexador
						 WHERE pp.cd_empresa = ".$_SESSION['EMP']."
						   AND pp.cd_plano   = ".$reg['cd_plano']."
						   AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('01/12/".($ar_param['ano']-1)."','DD/MM/YYYY')  AND TO_DATE('01/01/".($ar_param['ano']+1)."','DD/MM/YYYY')
						   AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
												 FROM public.indices i1
												WHERE i1.cd_indexador = i.cd_indexador 
												GROUP BY DATE_TRUNC('month', i1.dt_indice))
						   --AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
						 ORDER BY i.dt_indice
				   ";
		} 
		else if(in_array(intval($reg['cd_plano']), array(21)))
		{
			$qr_sql = " 
						SELECT i.vlr_indice AS vl_cota, 
							   (i.dt_indice  - '1 month'::interval) AS dt_cota,  
							   TO_CHAR(i.dt_indice - '1 month'::interval, 'DD/MM') AS dt_dia, 
							   TO_CHAR(i.dt_indice - '1 month'::interval, 'MM') AS dt_mes,
							   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
						  FROM public.indices i
						  JOIN public.planos_patrocinadoras pp
							ON pp.cd_indexador = i.cd_indexador
						 WHERE pp.cd_empresa = ".$_SESSION['EMP']."
						   AND pp.cd_plano   = ".$reg['cd_plano']."
						   AND (DATE_TRUNC('day',dt_indice)  - '1 month'::interval)  BETWEEN TO_DATE('01/12/".($ar_param['ano']-1)."','DD/MM/YYYY')  AND TO_DATE('01/01/".($ar_param['ano']+1)."','DD/MM/YYYY')
						   AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
												 FROM public.indices i1
												WHERE i1.cd_indexador = i.cd_indexador 
												GROUP BY DATE_TRUNC('month', i1.dt_indice))
						   --AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
						 ORDER BY i.dt_indice
				   ";
		} 
		else if(intval($reg['cd_plano']) == 1)
		{
			if(intval($ar_param['ano']) < 2011)
			{
				$qr_sql = "
							SELECT q.vl_cota, 
								   q.dt_ref_sld_cotas AS dt_cota,  
								   TO_CHAR(q.dt_ref_sld_cotas, 'DD/MM') AS dt_dia,  
								   TO_CHAR(q.dt_ref_sld_cotas, 'MM') AS dt_mes 
							  FROM public.qt_razao_cota q
							 WHERE DATE_TRUNC('day', q.dt_ref_sld_cotas) BETWEEN TO_DATE('01/12/".($ar_param['ano']-1)."','DD/MM/YYYY')  AND TO_DATE('31/12/".$ar_param['ano']."','DD/MM/YYYY')					 
							   AND q.dt_ref_sld_cotas = (SELECT MAX(q1.dt_ref_sld_cotas) 
														   FROM public.qt_razao_cota  q1
														  WHERE q1.cod_tp_aplic = '00000' 
															AND q1.cod_plano    = ".$ar_param['pl']." 
															AND q1.cod_empresa  = ".$ar_param['emp']."
															AND TO_CHAR(q1.dt_ref_sld_cotas,'MM-YYYY') = TO_CHAR(q.dt_ref_sld_cotas,'MM-YYYY'))
							   AND q.cd_atividade =(SELECT MAX(q2.cd_atividade) 
													  FROM qt_razao_cota q2
													 WHERE q2.cod_tp_aplic = '00000' 
													   AND q2.cod_plano    = ".$ar_param['pl']." 
													   AND q2.cod_empresa  = ".$ar_param['emp']."
													   AND q2.dt_ref_sld_cotas = q.dt_ref_sld_cotas) 												   
							   AND q.dt_ref_sld_cotas <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
							   AND q.cod_tp_aplic = '00000' 
							   AND q.cod_plano    = ".$ar_param['pl']." 
							   AND q.cod_empresa  = ".$ar_param['emp']."
							 ORDER BY dt_ref_sld_cotas
							 LIMIT 13
						  ";
			}
			else
			{
				#### SISTEMA FINANCEIRO NOVO
				$qr_sql = "
							SELECT scc.vl_cota,
								   scc.dt_calculo AS dt_cota,
								   TO_CHAR(scc.dt_calculo, 'DD/MM') AS dt_dia,  
								   TO_CHAR(scc.dt_calculo, 'MM') AS dt_mes 
							  FROM public.sc_calculo_cotas scc
							 WHERE scc.cd_tp_aplicacao = 8 --oracle.pck_sc_consultas_gerais_fnc_ret_tp_aplic_consolidador()
							   AND scc.cd_empresa      = ".$ar_param['emp']."
							   AND scc.cd_plano        = ".$ar_param['pl']." 
							   AND DATE_TRUNC('day', scc.dt_calculo) BETWEEN TO_DATE('01/12/".($ar_param['ano']-1)."','DD/MM/YYYY')  AND TO_DATE('31/12/".$ar_param['ano']."','DD/MM/YYYY')
							   --AND scc.dt_calculo < (DATE_TRUNC('month',CURRENT_DATE) - '1 month'::interval) -- MES ANTERIOR
							   --AND DATE_TRUNC('month',scc.dt_calculo) <= (DATE_TRUNC('month',CURRENT_DATE) - '1 month'::interval) -- MES ANTERIOR
							   AND DATE_TRUNC('month',scc.dt_calculo) < (DATE_TRUNC('month',CURRENT_DATE)) -- MES ANTERIOR
							   AND scc.dt_calculo = (SELECT MAX(scc1.dt_calculo) 
													   FROM public.sc_calculo_cotas scc1
													  WHERE scc1.cd_tp_aplicacao = scc.cd_tp_aplicacao
														AND scc1.cd_empresa      = scc.cd_empresa
														AND scc1.cd_plano        = scc.cd_plano
														AND TO_CHAR(scc1.dt_calculo,'MM-YYYY') = TO_CHAR(scc.dt_calculo,'MM-YYYY'))
											
							 ORDER BY scc.dt_calculo
							 LIMIT 13
						  ";		
			}
		
		}
		else
		{
			$qr_sql = "
						SELECT i.vlr_indice AS vl_cota, 
							   (i.dt_indice  - '1 month'::interval) AS dt_cota,  
							   TO_CHAR(i.dt_indice - '1 month'::interval, 'DD/MM') AS dt_dia, 
							   TO_CHAR(i.dt_indice - '1 month'::interval, 'MM') AS dt_mes 
						  FROM public.indices i
						  JOIN public.planos_patrocinadoras pp
							ON pp.cd_indexador = i.cd_indexador
						 WHERE pp.cd_empresa = ".$_SESSION['EMP']."
						   AND pp.cd_plano   = ".$reg['cd_plano']."
						   AND (DATE_TRUNC('day',dt_indice)  - '1 month'::interval) BETWEEN TO_DATE('01/12/".($ar_param['ano']-1)."','DD/MM/YYYY')  AND TO_DATE('31/12/".($ar_param['ano'])."','DD/MM/YYYY')
						   AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
												 FROM public.indices i1
												WHERE i1.cd_indexador = i.cd_indexador
												GROUP BY DATE_TRUNC('month', i1.dt_indice))
						   AND (i.dt_inclusao + '3 day'::INTERVAL)::DATE < CURRENT_DATE -- 3 DIAS APOS INCLUSAO LIBERA
						   AND (DATE_TRUNC('day',dt_indice) - '1 month'::interval) >= (CASE WHEN pp.cd_empresa IN (8,10) THEN TO_DATE('01/07/2008','DD/MM/YYYY')
																							WHEN pp.cd_empresa IN (19,20) THEN TO_DATE('01/11/2010','DD/MM/YYYY')
																							ELSE TO_DATE('01/12/2005','DD/MM/YYYY') END)
						 ORDER BY i.dt_indice		
					  ";
		}
		$ob_resul = pg_query($db, $qr_sql);
		$ar_dados = Array();
		
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$ar_dados[] = $ar_reg;
		}
		
		/*echo "<PRE>";
		echo $qr_sql;
		print_r($ar_dados);
		print_r($_SESSION);
		exit;*/
		
		$ar_cota_mes        = array();
		$ar_cota_acumulada = array();
		$ar_titulo          = array();
		
		$nr_fim = count($ar_dados);
		$nr_conta = 0;
		$dt_referencia = "";
		while ($nr_conta < $nr_fim) 
		{
			$ar_reg = $ar_dados[$nr_conta];
			if($nr_conta == 0)
			{
				$nr_anterior = $ar_reg['vl_cota'];
				$nr_conta_acumulada_anterior = 0;			
			}
			else
			{
				$nr_cota_mes = (($ar_reg['vl_cota']/$nr_anterior) - 1) * 100;
				$nr_conta_acumulada = (((($nr_conta_acumulada_anterior / 100) + 1) * (($nr_cota_mes / 100) + 1)) - 1) * 100;
				$ar_cota_mes[] = round($nr_cota_mes,2);
				$ar_cota_acumulada[] = round($nr_conta_acumulada,2);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_anterior = $ar_reg['vl_cota'];
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			
			$dt_referencia = $ar_reg['dt_referencia'];
			$nr_conta++;
		}
		
		if(count($ar_cota_mes) < 12)
		{
			$nr_conta = ($ar_dt_mes[count($ar_dt_mes)-1])/1;
			while($nr_conta < 12)	
			{
				$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
				$nr_conta++;
				
			}
		}		
		/*
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/		
		
		$ar_mes = "[";
		$nr_conta = 0;
		foreach($ar_cota_mes as $item)
		{
			$ar_mes.= ($ar_mes != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_mes.= "]";
		
		$ar_acumulado = "[";
		$nr_conta = 0;
		foreach($ar_cota_acumulada as $item)
		{
			$ar_acumulado.= ($ar_acumulado != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_acumulado.= "]";	

		$ar_referencia = "[";
		$nr_conta = 0;
		foreach($ar_titulo as $item)
		{
			$ar_referencia.= ($ar_referencia != "[" ? "," : "")."'".$item."'";
			$nr_conta++;
		}
		$ar_referencia.= "]";		
		
		$ar_ret['titulo']        = 'RENTABILIDADE PLANO '.$NOME_PLANO.' - '.$ar_param['ano'];  
		$ar_ret['ar_mes']        = $ar_mes;
		$ar_ret['ar_acumulado']  = $ar_acumulado;
		$ar_ret['ar_referencia'] = $ar_referencia;
		$ar_ret['mes_maximo']    = $ar_dt_mes[count($ar_dt_mes)-1];
		$ar_ret['pr_acumulado']  = $ar_cota_acumulada[count($ar_cota_acumulada)-1];
		$ar_ret['posicao']       = (trim($dt_referencia) != "" ? "Posição referente à ".$dt_referencia : "");
		
		return $ar_ret;
	}	
	
	function getEquilibrio($cd_empresa, $cd_plano)
	{
		global $db;
		
		$sql = "
				SELECT pl.descricao
				  FROM public.planos pl
				 WHERE pl.cd_plano            = ".intval($cd_plano);
		$rs  = pg_query($db,$sql);
		$reg = pg_fetch_array($rs);
		
		$NOME_PLANO = $reg['descricao'];		

		$qr_select = "
						SELECT ee.nr_ano, 
						       ee.vl_provisao, 
							   ee.vl_cobertura,
							   TO_CHAR(e.dt_equilibrio,'DD/MM/YYYY') AS dt_equilibrio,
							   e.ds_equilibrio
						  FROM meu_retrato.edicao_equilibrio ee
						  JOIN meu_retrato.edicao e
						    ON e.cd_edicao = ee.cd_edicao
						 WHERE ee.dt_exclusao IS NULL
						   AND ee.cd_edicao = (SELECT MAX(e.cd_edicao)
												 FROM meu_retrato.edicao e
												WHERE e.cd_empresa = ".intval($cd_empresa)."
												  AND e.cd_plano   = ".intval($cd_plano)."
									              AND e.dt_exclusao IS NULL
												  AND e.dt_liberacao IS NOT NULL)
						 ORDER BY nr_ano 
					 ";
		#echo $qr_select; 
		$ob_res = pg_query($db, $qr_select);	
		
		$ar_ano       = "[";
		$ar_provisao  = "[";
		$ar_cobertura = "[";
		$ar_tabela    = Array();
		while($ar_reg = pg_fetch_array($ob_res))
		{
			$ds_equilibrio = $ar_reg['ds_equilibrio'];
			$dt_equilibrio = $ar_reg['dt_equilibrio'];
			
			$ar_ano.= ($ar_ano != "[" ? "," : "")."".$ar_reg['nr_ano']."";
			$ar_provisao.= ($ar_provisao != "[" ? "," : "")."".$ar_reg['vl_provisao']."";
			$ar_cobertura.= ($ar_cobertura != "[" ? "," : "")."".$ar_reg['vl_cobertura']."";
			
			$ar_tabela[] = $ar_reg;
		}
		$ar_ano      .= "]";
		$ar_provisao .= "]";
		$ar_cobertura.= "]";

		
		$ar_ret['titulo']         = 'PLANO '.$NOME_PLANO.' - '.$dt_equilibrio;  
		$ar_ret['ar_tabela']      = $ar_tabela;
		$ar_ret['ar_ano']         = $ar_ano;
		$ar_ret['ar_provisao']    = $ar_provisao;
		$ar_ret['ar_cobertura']   = $ar_cobertura;
		$ar_ret['ds_equilibrio']  = $ds_equilibrio;
		$ar_ret['posicao']        = (trim($dt_equilibrio) != "" ? "Posição referente à ".$dt_equilibrio : "");
		
		return $ar_ret;		
	}	
	
	function getINDICE($ano,$mes,$indice,$titulo)
	{
		global $db;

		$ar_param['ano']    = $ano;
		$ar_param['mes']    = $mes;
		$ar_param['indice'] = $indice;
		$ar_param['titulo'] = $titulo;
		
		$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		$qr_sql = " 
					SELECT (i.vl_indice/100) + 1 AS vl_cota, 
						   i.dt_indice AS dt_cota,  
						   TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
						   TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
						   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
					  FROM autoatendimento.indice_mercado_valor i 
					 WHERE i.cd_indice_mercado = ".$ar_param['indice']."
					   AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('01/01/".$ar_param['ano']."','DD/MM/YYYY')  AND TO_DATE('01/".$ar_param['mes']."/".$ar_param['ano']."','DD/MM/YYYY')
					   AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
											 FROM autoatendimento.indice_mercado_valor i1
											WHERE i1.cd_indice_mercado = i.cd_indice_mercado 
											GROUP BY DATE_TRUNC('month', i1.dt_indice))
					   AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
					   AND i.dt_exclusao IS NULL
					 ORDER BY i.dt_indice
			   ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_dados = Array();
		
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$ar_dados[] = $ar_reg;
		}
		/*
		echo "<PRE>";
		echo $qr_sql;
		print_r($ar_dados);
		print_r($_SESSION);
		exit;
		*/
		$ar_cota_mes        = array();
		$ar_cota_acumulada = array();
		$ar_titulo          = array();
		
		$nr_fim = count($ar_dados);
		$nr_conta = 0;
		$dt_referencia = "";
		while ($nr_conta < $nr_fim) 
		{
			$ar_reg = $ar_dados[$nr_conta];
			if($nr_conta == 0)
			{
				$nr_conta_acumulada = $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),2);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			else
			{
				$nr_conta_acumulada = $nr_conta_acumulada_anterior * $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),2);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			
			$dt_referencia = $ar_reg['dt_referencia'];
			$nr_conta++;
		}
		
		if(count($ar_cota_mes) < 12)
		{
			$nr_conta = ($ar_dt_mes[count($ar_dt_mes)-1])/1;
			while($nr_conta < 12)	
			{
				$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
				$nr_conta++;
				
			}
		}		
		/*
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/		
		
		$ar_mes = "[";
		$nr_conta = 0;
		foreach($ar_cota_mes as $item)
		{
			$ar_mes.= ($ar_mes != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_mes.= "]";
		
		$ar_acumulado = "[";
		$nr_conta = 0;
		foreach($ar_cota_acumulada as $item)
		{
			$ar_acumulado.= ($ar_acumulado != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_acumulado.= "]";	

		$ar_referencia = "[";
		$nr_conta = 0;
		foreach($ar_titulo as $item)
		{
			$ar_referencia.= ($ar_referencia != "[" ? "," : "")."'".$item."'";
			$nr_conta++;
		}
		$ar_referencia.= "]";		
		
		$ar_ret['titulo']        = $ar_param['titulo']; 
		$ar_ret['ar_mes']        = $ar_mes;
		$ar_ret['ar_acumulado']  = $ar_acumulado;
		$ar_ret['ar_referencia'] = $ar_referencia;
		$ar_ret['mes_maximo']    = $ar_dt_mes[count($ar_dt_mes)-1];
		$ar_ret['pr_acumulado']  = $ar_cota_acumulada[count($ar_cota_acumulada)-1];
		$ar_ret['posicao']       = (trim($dt_referencia) != "" ? "Posição referente à ".$dt_referencia : "");
		
		return $ar_ret;
	}	
	
	function getIGPM($ano)
	{
		global $db;

		$ar_param['ano'] = $ano;
		
		$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		$qr_sql = " 
					SELECT (i.vlr_indice/100) + 1 AS vl_cota, 
						   i.dt_indice AS dt_cota,  
						   TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
						   TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
						   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
					  FROM public.indices i 
					 WHERE i.cd_indexador = 26 -- IGPM
					   AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('01/01/".($ar_param['ano'])."','DD/MM/YYYY')  AND TO_DATE('01/01/".($ar_param['ano']+1)."','DD/MM/YYYY')
					   AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
											 FROM public.indices i1
											WHERE i1.cd_indexador = i.cd_indexador 
											GROUP BY DATE_TRUNC('month', i1.dt_indice))
					   AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
					 ORDER BY i.dt_indice
			   ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_dados = Array();
		
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$ar_dados[] = $ar_reg;
		}
		/*
		echo "<PRE>";
		echo $qr_sql;
		print_r($ar_dados);
		print_r($_SESSION);
		exit;
		https://linkconcursos.com.br/como-calcular-inflacao-acumulada-e-tambem-de-outros-valores-percentuais-em-geral/
		*/
		$ar_cota_mes        = array();
		$ar_cota_acumulada = array();
		$ar_titulo          = array();
		
		$nr_fim = count($ar_dados);
		$nr_conta = 0;
		$dt_referencia = "";
		while ($nr_conta < $nr_fim) 
		{
			$ar_reg = $ar_dados[$nr_conta];
			if($nr_conta == 0)
			{
				$nr_conta_acumulada = $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),4);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			else
			{
				$nr_conta_acumulada = $nr_conta_acumulada_anterior * $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),4);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			
			$dt_referencia = $ar_reg['dt_referencia'];
			$nr_conta++;
		}
		
		if(count($ar_cota_mes) < 12)
		{
			$nr_conta = ($ar_dt_mes[count($ar_dt_mes)-1])/1;
			while($nr_conta < 12)	
			{
				$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
				$nr_conta++;
				
			}
		}		
		/*
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/		
		
		$ar_mes = "[";
		$nr_conta = 0;
		foreach($ar_cota_mes as $item)
		{
			$ar_mes.= ($ar_mes != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_mes.= "]";
		
		$ar_acumulado = "[";
		$nr_conta = 0;
		foreach($ar_cota_acumulada as $item)
		{
			$ar_acumulado.= ($ar_acumulado != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_acumulado.= "]";	

		$ar_referencia = "[";
		$nr_conta = 0;
		foreach($ar_titulo as $item)
		{
			$ar_referencia.= ($ar_referencia != "[" ? "," : "")."'".$item."'";
			$nr_conta++;
		}
		$ar_referencia.= "]";		
		
		$ar_ret['titulo']        = 'IGPM - '.$ar_param['ano']; 
		$ar_ret['ar_mes']        = $ar_mes;
		$ar_ret['ar_acumulado']  = $ar_acumulado;
		$ar_ret['ar_referencia'] = $ar_referencia;
		$ar_ret['pr_acumulado']  = $ar_cota_acumulada[count($ar_cota_acumulada)-1];
		$ar_ret['posicao']       = (trim($dt_referencia) != "" ? "Posição referente à ".$dt_referencia : "");
		
		return $ar_ret;
	}	
	
	function getINPC($ano)
	{
		global $db;
		
		$ar_param['ano'] = $ano;
		
		$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

		$qr_sql = " 
				SELECT ((i.vlr_indice - 1) * 100) AS vl_inpc, 
					   i.dt_indice AS dt_inpc,  
					   (SELECT ((EXP(SUM(LN(im.vlr_indice))) - 1) * 100) AS vl_inpc_acumulado  
				          FROM indices im
				         WHERE im.cd_indexador = i.cd_indexador
				           AND im.dt_indice BETWEEN TO_DATE('01/01/".($ar_param['ano'])."','DD/MM/YYYY') AND i.dt_indice) AS vl_inpc_acumulado,
					   TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
					   TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
					   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
				  FROM indices i
				 WHERE i.cd_indexador = 85 -- INPC
                   AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('01/01/".($ar_param['ano'])."','DD/MM/YYYY') AND TO_DATE('01/01/".($ar_param['ano']+1)."','DD/MM/YYYY')
				   AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
										 FROM public.indices i1
										WHERE i1.cd_indexador = i.cd_indexador 
										GROUP BY DATE_TRUNC('month', i1.dt_indice))
				   AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR				 
				 ORDER BY i.dt_indice
			   ";
	
		$ob_resul = pg_query($db, $qr_sql);
		$ar_dados = Array();
		
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$ar_dados[] = $ar_reg;
		}
		
		/*echo "<PRE>";
		echo $qr_sql;
		print_r($ar_dados);
		print_r($_SESSION);
		exit;*/
		
		$ar_cota_mes        = array();
		$ar_cota_acumulada = array();
		$ar_titulo          = array();
		
		$nr_fim = count($ar_dados);
		$nr_conta = 0;
		$dt_referencia = "";
		while ($nr_conta < $nr_fim) 
		{
			$ar_reg = $ar_dados[$nr_conta];
			$ar_cota_mes[] = round($ar_reg['vl_inpc'],2);
			$ar_cota_acumulada[] = round($ar_reg['vl_inpc_acumulado'],2);			
			
			$dt_referencia = $ar_reg['dt_referencia'];
			$nr_conta++;
		}
		
		if(count($ar_cota_mes) < 12)
		{
			$nr_conta = ($ar_dt_mes[count($ar_dt_mes)-1])/1;
			while($nr_conta < 12)	
			{
				$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
				$nr_conta++;
				
			}
		}		
		/*
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/		
		
		$ar_mes = "[";
		$nr_conta = 0;
		foreach($ar_cota_mes as $item)
		{
			$ar_mes.= ($ar_mes != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_mes.= "]";
		
		$ar_acumulado = "[";
		$nr_conta = 0;
		foreach($ar_cota_acumulada as $item)
		{
			$ar_acumulado.= ($ar_acumulado != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_acumulado.= "]";	

		$ar_referencia = "[";
		$nr_conta = 0;
		foreach($ar_titulo as $item)
		{
			$ar_referencia.= ($ar_referencia != "[" ? "," : "")."'".$item."'";
			$nr_conta++;
		}
		$ar_referencia.= "]";		
		
		$ar_ret['titulo']        = 'INPC - '.$ar_param['ano'];
		$ar_ret['ar_mes']        = $ar_mes;
		$ar_ret['ar_acumulado']  = $ar_acumulado;
		$ar_ret['ar_referencia'] = $ar_referencia;
		$ar_ret['pr_acumulado']  = $ar_cota_acumulada[count($ar_cota_acumulada)-1];
		$ar_ret['posicao']       = (trim($dt_referencia) != "" ? "Posição referente à ".$dt_referencia : "");
		
		return $ar_ret;
	}

	function getPoupanca($ano)
	{
		global $db;

		$ar_param['ano'] = $ano;
		
		$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		$qr_sql = " 
					SELECT (i.vlr_indice/100) + 1 AS vl_cota, 
						   i.dt_indice AS dt_cota,  
						   TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
						   TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
						   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
					  FROM public.indices i 
					 WHERE i.cd_indexador = 72 -- POUPANCA
					   AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('01/01/".($ar_param['ano'])."','DD/MM/YYYY')  AND TO_DATE('01/01/".($ar_param['ano']+1)."','DD/MM/YYYY')
					   AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
											 FROM public.indices i1
											WHERE i1.cd_indexador = i.cd_indexador 
											GROUP BY DATE_TRUNC('month', i1.dt_indice))
					   AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
					 ORDER BY i.dt_indice
			   ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_dados = Array();
		
		while ($ar_reg = pg_fetch_array($ob_resul)) 
		{
			$ar_dados[] = $ar_reg;
		}
		/*
		echo "<PRE>";
		echo $qr_sql;
		print_r($ar_dados);
		print_r($_SESSION);
		exit;
		https://linkconcursos.com.br/como-calcular-inflacao-acumulada-e-tambem-de-outros-valores-percentuais-em-geral/
		*/
		$ar_cota_mes        = array();
		$ar_cota_acumulada = array();
		$ar_titulo          = array();
		
		$nr_fim = count($ar_dados);
		$nr_conta = 0;
		$dt_referencia = "";
		while ($nr_conta < $nr_fim) 
		{
			$ar_reg = $ar_dados[$nr_conta];
			if($nr_conta == 0)
			{
				$nr_conta_acumulada = $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),4);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			else
			{
				$nr_conta_acumulada = $nr_conta_acumulada_anterior * $ar_reg['vl_cota'];
				$ar_cota_mes[] = round((($ar_reg['vl_cota'] - 1) * 100),2);
				$ar_cota_acumulada[] = round((($nr_conta_acumulada - 1) * 100),4);
				$ar_titulo[] = trim(substr($meses[($ar_reg['dt_mes'] - 1)],0,3));
				$ar_dt_mes[] = $ar_reg['dt_mes']; 
				$nr_conta_acumulada_anterior = $nr_conta_acumulada;
			}
			
			$dt_referencia = $ar_reg['dt_referencia'];
			$nr_conta++;
		}
		
		if(count($ar_cota_mes) < 12)
		{
			$nr_conta = ($ar_dt_mes[count($ar_dt_mes)-1])/1;
			while($nr_conta < 12)	
			{
				$ar_titulo[] = trim(substr($meses[$nr_conta],0,3));		
				$nr_conta++;
				
			}
		}		
		/*
		echo "<PRE>";
		print_r($ar_cota_mes);
		print_r($ar_cota_acumulada);
		print_r($ar_titulo);
		exit;
		*/		
		
		$ar_mes = "[";
		$nr_conta = 0;
		foreach($ar_cota_mes as $item)
		{
			$ar_mes.= ($ar_mes != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_mes.= "]";
		
		$ar_acumulado = "[";
		$nr_conta = 0;
		foreach($ar_cota_acumulada as $item)
		{
			$ar_acumulado.= ($ar_acumulado != "[" ? "," : "")."".$item."";
			$nr_conta++;
		}
		$ar_acumulado.= "]";	

		$ar_referencia = "[";
		$nr_conta = 0;
		foreach($ar_titulo as $item)
		{
			$ar_referencia.= ($ar_referencia != "[" ? "," : "")."'".$item."'";
			$nr_conta++;
		}
		$ar_referencia.= "]";		
		
		$ar_ret['titulo']        = 'POUPANÇA - '.$ar_param['ano'];
		$ar_ret['ar_mes']        = $ar_mes;
		$ar_ret['ar_acumulado']  = $ar_acumulado;
		$ar_ret['ar_referencia'] = $ar_referencia;
		$ar_ret['pr_acumulado']  = $ar_cota_acumulada[count($ar_cota_acumulada)-1];
		$ar_ret['posicao']       = (trim($dt_referencia) != "" ? "Posição referente à ".$dt_referencia : "");
		
		return $ar_ret;
	}		
	
	function getMeuRetratoItem($cd_linha = "")
	{
		return pg_fetch_array(getMeuRetrato($cd_linha));
	}
	
	function getMeuRetrato($cd_linha = "")
	{
		global $db;
		$qr_select = "
						SELECT p.cd_empresa,
							   p.cd_registro_empregado,
							   p.seq_dependencia,
							   p.nome,
							   p.sexo,
							   p.email AS email_1,
							   p.email_profissional AS email_2,
							   CASE WHEN COALESCE(p.celular,0) > 0
									THEN TO_CHAR(p.ddd_celular,'FM(00)') || TO_CHAR(p.celular,'FM 999999999') 
									ELSE TO_CHAR(p.ddd,'FM(00)') || TO_CHAR(p.telefone,'FM 999999999') 
							   END AS telefone_contato,
							   COALESCE(p.email, p.email_profissional) AS email_contato,
							   funcoes.format_cpf(p.cpf_mf::bigint) AS cpf,
							   p.endereco,
							   p.nr_endereco,
							   p.complemento_endereco,
							   p.bairro,
							   p.cidade,
							   p.unidade_federativa AS uf,
							   TO_CHAR(p.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
							   TO_CHAR(p.cep,'FM99999') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
							   TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_base_extrato,
							   TO_CHAR(e.dt_base_extrato,'YYYY') AS ano_base_extrato,
							   TO_CHAR(CURRENT_TIMESTAMP,'DD/MM/YYYY HH24:MI:SS') AS dt_hoje,
							   epd.*,
							   'auto_atendimento_meu_retrato.php?ED=' || e.cd_edicao::TEXT AS mr_url,
							   e.cd_edicao,
							   e.ficaadica,
							   e.comentario_rentabilidade,
							   e.arquivo_comparativo,
							   e.arquivo_premissas_atuariais
						  FROM meu_retrato.edicao e
						  JOIN meu_retrato.edicao_participante ep
							ON ep.cd_edicao = e.cd_edicao
						  JOIN meu_retrato.edicao_participante_dado epd
							ON epd.cd_edicao_participante = ep.cd_edicao_participante
						  JOIN public.participantes p
							ON p.cd_empresa            = ep.cd_empresa
						   AND p.cd_registro_empregado = ep.cd_registro_empregado
						   AND p.seq_dependencia       = ep.seq_dependencia
						 WHERE ep.cd_empresa            = ".intval($_SESSION['EMP'])." 
						   AND ep.cd_registro_empregado = ".intval($_SESSION['RE'])." 
						   AND ep.seq_dependencia       = ".intval($_SESSION['SEQ'])."
						   AND e.cd_edicao              = (SELECT e1.cd_edicao
														     FROM meu_retrato.edicao e1
														     JOIN meu_retrato.edicao_participante ep1
														       ON ep1.cd_edicao = e1.cd_edicao
														    WHERE ep1.cd_empresa            = ".intval($_SESSION['EMP'])."
														      AND ep1.cd_registro_empregado = ".intval($_SESSION['RE'])."
														      AND ep1.seq_dependencia       = ".intval($_SESSION['SEQ'])."
														      AND e1.dt_exclusao IS NULL
														      AND e1.dt_liberacao IS NOT NULL
														    ORDER BY e1.dt_base_extrato DESC
														    LIMIT 1)
						   ".(trim($cd_linha) != "" ? "AND epd.cd_linha = '".trim($cd_linha)."'" : "")."
					 ";
		#echo "<PRE>".$qr_select."</PRE>"; exit;
		$ob_res = pg_query($db, $qr_select);	
		
		return $ob_res;
	}	
	
	function getMeuRetratoRentabilidadeAnterior($ordem = "DESC")
	{
		global $db;
		$qr_select = "
						SELECT er.nr_ano,
							   er.nr_cota_acumulada
						  FROM meu_retrato.edicao_rentabilidade er
						 WHERE er.cd_edicao  = (SELECT e1.cd_edicao
												  FROM meu_retrato.edicao e1
												  JOIN meu_retrato.edicao_participante ep1
												    ON ep1.cd_edicao = e1.cd_edicao
												 WHERE ep1.cd_empresa            = ".intval($_SESSION['EMP'])."
												   AND ep1.cd_registro_empregado = ".intval($_SESSION['RE'])."
												   AND ep1.seq_dependencia       = ".intval($_SESSION['SEQ'])."
												   AND e1.dt_exclusao IS NULL
												   AND e1.dt_liberacao IS NOT NULL
												 ORDER BY e1.dt_base_extrato DESC
												 LIMIT 1)
						   AND er.cd_empresa = ".intval($_SESSION['EMP'])."
						   AND er.cd_plano   = ".intval($_SESSION['PLANO'])."
						   AND er.nr_ano     < (SELECT MAX(er1.nr_ano)
												  FROM meu_retrato.edicao_rentabilidade er1
												 WHERE er1.cd_edicao  = er.cd_edicao
												   AND er1.cd_empresa = er.cd_empresa
												   AND er1.cd_plano   = er.cd_plano)

						 UNION

						SELECT er.nr_ano,
							   er.nr_cota_acumulada
						  FROM meu_retrato.edicao_rentabilidade er
						 WHERE er.cd_edicao         = (SELECT e1.cd_edicao
														 FROM meu_retrato.edicao e1
														 JOIN meu_retrato.edicao_participante ep1
														   ON ep1.cd_edicao = e1.cd_edicao
														WHERE ep1.cd_empresa            = ".intval($_SESSION['EMP'])."
														  AND ep1.cd_registro_empregado = ".intval($_SESSION['RE'])."
														  AND ep1.seq_dependencia       = ".intval($_SESSION['SEQ'])."
														  AND e1.dt_exclusao IS NULL
														  AND e1.dt_liberacao IS NOT NULL
														ORDER BY e1.dt_base_extrato DESC
														LIMIT 1)
						   AND er.cd_empresa        = ".intval($_SESSION['EMP'])."
						   AND er.cd_plano          = ".intval($_SESSION['PLANO'])."
						   AND er.nr_cota_acumulada IS NOT NULL
						   AND UPPER(er.mes)        = 'DEZ'
						   AND er.nr_ano            = (SELECT MAX(er1.nr_ano)
									                     FROM meu_retrato.edicao_rentabilidade er1
									                    WHERE er1.cd_edicao  = er.cd_edicao
									                      AND er1.cd_empresa = er.cd_empresa
									                      AND er1.cd_plano   = er.cd_plano)
												   
						 ORDER BY nr_ano ASC
						 LIMIT 11
					 ";
		#echo "<PRE>".$qr_select."</PRE>";exit;
		$ob_res = pg_query($db, $qr_select);
		$ar_ret = Array();
		
		while($ar_reg = pg_fetch_array($ob_res))
		{
			$ar_ret[] = $ar_reg;
		}
		
		if($ordem == "ASC")
		{
			if(count($ar_ret) >= 10)
			{
				unset($ar_ret[0]);
				unset($ar_ret[1]);
			}
		}		
		elseif($ordem == "DESC")
		{
			rsort($ar_ret);
			if(count($ar_ret) >= 9)
			{
				unset($ar_ret[9]);	
				unset($ar_ret[10]);	
			}			
		}
		else
		{
			$ar_ret = Array();
		}

		#echo "<PRE>".$ordem."</PRE>";
		#echo "<PRE>".print_r($ar_ret,TRUE)."</PRE>";
		#exit;
		
		return $ar_ret;
	}	
	
	function ajustarValor($valor)
	{
		return floatval(str_replace(',','.',str_replace('.', '', $valor)));
	}
?>