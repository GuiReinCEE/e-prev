<?php
	include_once('inc/conexao.php');

	$debug = FALSE;
	$debug_msg = "";
	
	$user_md5 = array(
		'13438bd440697201e0e9bc926aedb0fe',
		'f1445d8e07632a69fe2f3812c1eb59a8',
		'b989ecdb55ea98e209c5057e01d7a181',
		'ffd238d130081ddcb0be22a825457487'
	);

	if(in_array($_POST['user_md5'], $user_md5)) #### CRISTIANO | DENIS
	{
		$debug = TRUE;
	}	

	if($_POST)
	{
		if($_POST['ds_funcao'] == "montaIdade")
		{
			montaIdade($_POST['dt_nascimento'],$_POST['dt_simulacao']);
		}
		
		if($_POST['ds_funcao'] == "montaIdadeResgate")
		{
			montaIdadeResgate($_POST['dt_nascimento'],$_POST['dt_simulacao']);
		}			
		
		if($_POST['ds_funcao'] == "getRentabilidade")
		{
			getRentabilidade();
		}	

		if($_POST['ds_funcao'] == "getMesesAposenta")
		{
			getMesesAposenta($_POST['dt_ingresso'], $_POST['dt_simulacao'],$_POST['dt_nascimento'],$_POST['nr_idade_apos']);
		}		
		
		if($_POST['ds_funcao'] == "getEvolucao")
		{
			getEvolucao($_POST['tp_recebimento'],$_POST['vl_saldo'],$_POST['vl_renda'],$_POST['nr_prazo'],$_POST['vl_perc_recebe'],$_POST['vl_rentabilidade']);
		}			
		
		if($_POST['ds_funcao'] == "calculaBeneficio")
		{
			calculaBeneficio($_POST['cd_empresa'],$_POST['cd_registro_empregado'],$_POST['seq_dependencia'],$_POST['dt_ingresso'],$_POST['dt_simulacao'],$_POST['dt_nascimento'],$_POST['nr_idade_apos'],$_POST['vl_salario'],$_POST['vl_rentabilidade'],$_POST['vl_perc_adicional'],$_POST['qt_meses_adicional'],$_POST['vl_aporte'],$_POST['tp_recebimento'],intval($_POST['nr_prazo']),$_POST['vl_perc_recebe'],$_POST['dt_admissao'],$_POST['tp_simulacao'], $_POST['fl_adiantamento'], $_POST['vl_adiantamento'], $_POST['fl_autopatrocinio_pagar_patroc'], $_POST['vl_salario_perc_partic']);
		}
	}
	
	function getRentabilidade()
	{
		global $db;
		
		#### RENTABILIDADE MAXIMA PERMITIDA PARA SIMULACAO ####
		$ar_max_rentab = array("vl_rentabilidade" => 0.007207323, "pr_rentabilidade" => 9.00, "tipo" => "MAX");

		#### TAXA DE JURO ATUARIAL ####
		$ar_padrao = array("vl_rentabilidade" => 0.006434030);
	
		$qr_sql = "
					SELECT a.*
					  FROM (
								SELECT x.column1 AS vl_rentabilidade, x.column2 AS pr_rentabilidade, x.column3 AS tipo
								  FROM (VALUES 
												(0.003273740, 04.00, 'NOR'),
												(0.004074124, 05.00, 'NOR'),
												(0.004867551, 06.00, 'NOR'),
												(0.005654145, 07.00, 'NOR'),
												(0.006434030, 08.00, 'NOR'),
												(0.007207323, 09.00, 'NOR'),
												(0.007974140, 10.00, 'NOR'),
												(0.008734594, 11.00, 'NOR'),
												(0.009488793, 12.00, 'NOR')
									   ) x
						   ) a
					 WHERE 1 = 1
					   ".(count($ar_max_rentab) > 0 ? "AND a.vl_rentabilidade < ".floatval($ar_max_rentab["vl_rentabilidade"]) : "")."
					 ORDER BY pr_rentabilidade		
		          ";
		#echo "<PRE>".$qr_sql."</PRE>";exit;
		
		$ob_resul = pg_query($db, $qr_sql);
		$ar_rentab = array();
		while($ar_reg = pg_fetch_array($ob_resul))
		{
			$ar_rentab[] = array(
									"vl_rentabilidade" => $ar_reg["vl_rentabilidade"], 
									"pr_rentabilidade" => number_format($ar_reg["pr_rentabilidade"],2,",",".")."%", 
									"tipo" => $ar_reg["tipo"], 
									"selecionada" => ($ar_padrao["vl_rentabilidade"] == $ar_reg["vl_rentabilidade"] ? "S" : "N")
							    );
		}
		
		if(count($ar_max_rentab) > 0)
		{
			$ar_rentab[] = array(
									"vl_rentabilidade" => $ar_max_rentab["vl_rentabilidade"], 
									"pr_rentabilidade" => number_format($ar_max_rentab["pr_rentabilidade"],2,",",".")."%", 
									"tipo" => $ar_max_rentab["tipo"]
								);
		}
		
		//return $ar_rentab;
		
		echo json_encode($ar_rentab);
		
	}	
	
	function montaIdade($dt_nascimento, $dt_simulacao)
	{
		$dt_simulacao  = ajustaData($dt_simulacao);
		$dt_nascimento = ajustaData($dt_nascimento);		
		
		$nr_conta = calculaIdadeMinima($dt_nascimento, $dt_simulacao);
		$nr_fim   = 110;
		
		if($nr_conta < 50)
		{
			$nr_conta = 50;
		}
		
		while($nr_conta <= $nr_fim)
		{
			$ar_retorno[] = array("idade" => $nr_conta);
			$nr_conta++;
		}			
		
		echo json_encode($ar_retorno);
	}
	
	function montaIdadeResgate($dt_nascimento, $dt_simulacao)
	{
		$dt_simulacao  = ajustaData($dt_simulacao);
		$dt_nascimento = ajustaData($dt_nascimento);
		
		$qt_meses = calculaMeses($dt_nascimento, $dt_simulacao);
		$nr_conta = ((int)($qt_meses / 12)) + 2;
		$nr_fim   = 110;
		
		while($nr_conta <= $nr_fim)
		{
			$ar_retorno[] = array("idade" => $nr_conta);
			$nr_conta++;
		}			
		
		echo json_encode($ar_retorno);
		
	}	
	
	function calculaIdadeMinima($dt_nascimento, $dt_simulacao)
	{
		$dt_simulacao  = ajustaData($dt_simulacao);
		$dt_nascimento = ajustaData($dt_nascimento);			
		
		$ar_datas = calculaDatas($dt_nascimento, $dt_simulacao,50);
		$qt_meses = calculaMeses($dt_nascimento, $ar_datas['dt_referencia']);
		$nr_idade = (int)($qt_meses / 12);
		return $nr_idade;	
	}
	
	function getMesesAposenta($dt_ingresso, $dt_simulacao, $dt_nascimento, $nr_idade_apos) //REVISAR DATA DE INGRESSO
	{
		$dt_ingresso   = ajustaData($dt_ingresso);
		$dt_simulacao  = ajustaData($dt_simulacao);
		$dt_nascimento = ajustaData($dt_nascimento);			
		
		$ar_datas = calculaDatas($dt_nascimento, $dt_ingresso,$nr_idade_apos);
		$qt_meses = calculaMeses($dt_simulacao, $ar_datas['dt_sol_apos']);
		
		$ar_retorno["qt_meses"] = intval($qt_meses);
		
		echo json_encode($ar_retorno);
	}
	
	function calculaDatas($dt_nascimento, $dt_simulacao, $nr_idade_apos)
	{
		global $db;
		$qr_sql = "
					SELECT TO_CHAR(CASE WHEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
	                                               THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval)
		                                           ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
	                                          END) > (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '".$nr_idade_apos." years'::interval)
                                        THEN (CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
			                                       THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval)
			                                       ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
		                                      END)	
                                        ELSE TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '".$nr_idade_apos." years'::interval
							       END,'DD/MM/YYYY') AS dt_sol_apos,
					       TO_CHAR((TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval),'DD/MM/YYYY') AS dt_min_apos,
					       TO_CHAR((TO_DATE('".$dt_simulacao."','DD/MM/YYYY')+ '10 years'::interval),'DD/MM/YYYY') AS dt_min_plano,
					       TO_CHAR(CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                    THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '50 years'::interval)
					                    ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					               END,'DD/MM/YYYY') AS dt_ref_apos,
					       TO_CHAR(CASE WHEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval) > (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 year'::interval)
					                    THEN (TO_DATE('".$dt_nascimento."','DD/MM/YYYY')+ '45 years'::interval)
					                    ELSE (TO_DATE('".$dt_simulacao."','DD/MM/YYYY') + '10 years'::interval)
					               END,'DD/MM/YYYY') AS dt_referencia	
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg;					  
	}
	
	function calculaMeses($dt_ini, $dt_fim)
	{
		global $db;
		$qr_sql = "
					SELECT (EXTRACT('years' FROM TO_DATE('".$dt_fim."','DD/MM/YYYY')) - EXTRACT('years' FROM TO_DATE('".$dt_ini."','DD/MM/YYYY'))) * 12 + (EXTRACT('months' FROM TO_DATE('".$dt_fim."','DD/MM/YYYY')) - EXTRACT('months' FROM TO_DATE('".$dt_ini."','DD/MM/YYYY'))) AS qt_meses
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['qt_meses'];	
	}
	
	function calculaBeneficio($cd_empresa,$cd_registro_empregado,$seq_dependencia,$dt_ingresso,$dt_simulacao,$dt_nascimento,$nr_idade_apos,$vl_salario,$vl_rentabilidade,$vl_perc_adicional,$qt_meses_adic,$vl_aporte_inicial, $tp_recebimento,$nr_prazo,$vl_perc_recebe,$dt_admissao,$tp_simulacao, $fl_adiantamento, $vl_adiantamento, $fl_autopatrocinio_pagar_patroc, $vl_salario_perc_partic)
	{
		global $debug, $db;
		$debug_msg.= ($debug ?  "<PRE>DEBUG"."</PRE>" : "");	
		
		$dt_ingresso   = ajustaData($dt_ingresso);
		$dt_simulacao  = ajustaData($dt_simulacao);
		$dt_nascimento = ajustaData($dt_nascimento);	
		$debug_msg.= ($debug ?  "<PRE>dt_ingresso => ".$dt_ingresso."</PRE>" : "");	
		$debug_msg.= ($debug ?  "<PRE>dt_simulacao => ".$dt_simulacao."</PRE>" : "");	
		$debug_msg.= ($debug ?  "<PRE>dt_nascimento => ".$dt_nascimento."</PRE>": "");	

		#### PERCENTUAL DO SALARIO PARA CONTRIBUICAO ####
		$vl_salario_perc_patroc = floatval(getPercSalarioPatroc($cd_empresa, $vl_salario_perc_partic));
		$debug_msg.= ($debug ? "<PRE>vl_salario_perc_partic => ".$vl_salario_perc_partic."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>vl_salario_perc_patroc => ".$vl_salario_perc_patroc."</PRE>" : "");
		
		$tp_simulacao = trim($tp_simulacao) == "" ? "B" : trim($tp_simulacao);
		$debug_msg.= ($debug ?  "<PRE>tp_simulacao => ".$tp_simulacao."</PRE>" : "");		
		
		$fator_resgate = 0;

		## APOSENTADORIA ##
		$debug_msg.= ($debug ?  "<PRE>#### APOSENTADORIA ####</PRE>": "");
		$ar_datas        = calculaDatas($dt_nascimento, $dt_ingresso,$nr_idade_apos);
		$debug_msg.= ($debug ? "<PRE>".print_r($ar_datas,true)."</PRE>" : "");
		
		$dt_sol_apos    = $ar_datas['dt_sol_apos'];
		$dt_referencia  = $ar_datas['dt_referencia'];
		$dt_ref_apos    = $ar_datas['dt_ref_apos'];
		
		$qt_meses_ref   = calculaMeses($dt_simulacao, $dt_ref_apos);
		$qt_meses_apos  = calculaMeses($dt_simulacao, $dt_sol_apos);
		$qt_meses       = calculaMeses($dt_nascimento, $dt_simulacao);
		$nr_idade_atual = ($qt_meses / 12);
		
		$debug_msg.= ($debug ? "<PRE>qt_meses_ref => ".$qt_meses_ref."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>qt_meses_apos => ".$qt_meses_apos."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>qt_meses idade => ".$qt_meses."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>nr_idade_atual => ".$nr_idade_atual."</PRE>" : "");
		
		#### CONTRIBUIÇÕES ####
		$vl_contribuicao_proga_partic = calculaContribuicao($vl_salario,$vl_salario_perc_partic);
		$vl_contribuicao_proga_patroc = calculaContribuicao($vl_salario,$vl_salario_perc_patroc);
		$vl_contribuicao_proga_adic = 0;
		if(floatval($vl_perc_adicional) > 0)
		{
			$vl_contribuicao_proga_adic = calculaContribuicao($vl_salario,$vl_perc_adicional);
		}
		$debug_msg.= ($debug ? "<PRE>vl_contribuicao_proga_partic => ".$vl_contribuicao_proga_partic."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>vl_contribuicao_proga_patroc => ".$vl_contribuicao_proga_patroc."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>vl_contribuicao_proga_adic => ".$vl_contribuicao_proga_adic."</PRE>" : "");

		$fl_checkAutoPatrocinioComPatrocinadora = checkAutoPatrocinioComPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		$debug_msg.= ($debug ?  "<PRE> AUTOPATROCINIO COM PATROCINADORA => ".$fl_checkAutoPatrocinioComPatrocinadora."</PRE>" : "");		
		
		$fl_checkAutoPatrocinioSemPatrocinadora = checkAutoPatrocinioSemPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		$debug_msg.= ($debug ?  "<PRE> AUTOPATROCINIO SEM PATROCINADORA => ".$fl_checkAutoPatrocinioSemPatrocinadora."</PRE>" : "");
	
		$debug_msg.= ($debug ?  "<PRE> FL_AUTOPATROCINIO_PAGAR_PATROC => ".$fl_autopatrocinio_pagar_patroc."</PRE>" : "");
		if(($fl_checkAutoPatrocinioComPatrocinadora == "S") AND ($fl_autopatrocinio_pagar_patroc == "N"))
		{
			#### FORÇA SIMULAÇÃO DO AUTOPATROCINIO SEM PAGAR A PARTE DA PATROCINADORA ####
			$fl_checkAutoPatrocinioSemPatrocinadora = "S";
			$debug_msg.= ($debug ?  "<PRE> AUTOPATROCINIO SEM PATROCINADORA (FORÇADO) => ".$fl_checkAutoPatrocinioSemPatrocinadora."</PRE>" : "");
		}
		
		#### CIP ####
		$vl_cip_participante  = calculaCIP($vl_contribuicao_proga_partic,$vl_rentabilidade,$qt_meses_apos);
		$vl_cip_adicional     = calculaCIP($vl_contribuicao_proga_adic,$vl_rentabilidade,$qt_meses_adic);
		$vl_cip_patrocinadora = ($fl_checkAutoPatrocinioSemPatrocinadora == "S" ? 0 : calculaCIP($vl_contribuicao_proga_patroc,$vl_rentabilidade,$qt_meses_apos));
		
		$debug_msg.= ($debug ? "<PRE>vl_cip_participante => ".$vl_cip_participante."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>vl_cip_adicional => ".$vl_cip_adicional."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>vl_cip_patrocinadora => ".$vl_cip_patrocinadora."</PRE>" : "");
		
		$debug_msg.= ($debug ? "<PRE>vl_aporte_inicial => ".$vl_aporte_inicial."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>vl_perc_adicional => ".$vl_perc_adicional."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>qt_meses_adic => ".$qt_meses_adic."</PRE>" : "");
		
		if(floatval($vl_aporte_inicial) > 0)
		{
			$vl_cip_aporte = atualizaCIPAtual($qt_meses_apos,$vl_aporte_inicial,$vl_rentabilidade);
			$vl_cip_participante+= $vl_cip_aporte;
		}
	
		$vl_cip_adicional = atualizaCIPAtual(($qt_meses_apos - $qt_meses_adic),$vl_cip_adicional,$vl_rentabilidade);
		$debug_msg.= ($debug ? "<PRE>vl_cip_adicional atualizada => ".$vl_cip_adicional."</PRE>" : "");		
		
		$fl_BPD = checkBPD($cd_empresa, $cd_registro_empregado, $seq_dependencia);
		$debug_msg.= ($debug ? "<PRE>É BPD => ".$fl_BPD."</PRE>" : "");
		
		if($fl_BPD == "S")
		{
			#### BPD NÃO TEM PROJECAO ####
			$vl_cip_participante  = 0;
			$vl_cip_patrocinadora = 0;
		}		
		
		$vl_cip = ($vl_cip_participante + $vl_cip_patrocinadora + $vl_cip_adicional);		
		
		$debug_msg.= ($debug ? "<PRE>vl_cip projetada => ".$vl_cip."</PRE>" : "");		
		
		#### BUSCA CIP ATUAL ####
		$qt_cota = calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso);
		$vl_cota = getValorCota();
		$vl_cip_atual = $qt_cota * getValorCota();
		$vl_saldo_atual = $vl_cip_atual;
		$debug_msg.= ($debug ?  "<PRE>QT COTAS => ".$qt_cota."</PRE>" : "");
		$debug_msg.= ($debug ?  "<PRE>VL COTAS => ".$vl_cota."</PRE>" : "");
		$debug_msg.= ($debug ?  "<PRE>CIP ATUAL => ".$vl_cip_atual."</PRE>" : "");

		$vl_cip_atual = atualizaCIPAtual($qt_meses_apos,$vl_cip_atual,$vl_rentabilidade);
		$debug_msg.= ($debug ?  "<PRE>CIP ATUAL CORRIGIDA => ".$vl_cip_atual."</PRE>" : "");		
		
		$vl_cip = ($vl_cip + $vl_cip_atual);		
		
		$debug_msg.= ($debug ?  "<PRE>CIP ATUAL TOTAL => ".$vl_cip."</PRE>" : "");

		#### ADIANTAMENTO DE 20% ####
		$debug_msg.= ($debug ?  "<PRE>ADIANTAMENTO => ".$fl_adiantamento : "</PRE>");

		$vl_adiantamento_calc = 0;
		if(trim(strtoupper($fl_adiantamento)) == "S")
		{
			$debug_msg.= ($debug ?  "<PRE>PERCENTUAL DE ADIANTAMENTO => ".$vl_adiantamento : "</PRE>");

			$vl_adiantamento_calc = ($vl_cip/100) * $vl_adiantamento;
			$vl_cip = $vl_cip - $vl_adiantamento_calc;
			
			$debug_msg.= ($debug ?  "<PRE>ADIANTAMENTO ".$vl_adiantamento."% => ".$vl_adiantamento_calc : "</PRE>");
			$debug_msg.= ($debug ?  "<PRE>CIP TOTAL (-) ADIANTAMENTO => ".$vl_cip : "</PRE>");
		}
		
		$debug_msg.= ($debug ? "<PRE>VL_CIP TOTAL => ".$vl_cip."</PRE>" : "");			
		
		$debug_msg.= ($debug ? "<PRE>tp_recebimento => ".$tp_recebimento." (1 = PRAZO | 2 = PERCENTUAL)</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>nr_prazo => ".$nr_prazo."</PRE>" : "");
		$debug_msg.= ($debug ? "<PRE>vl_perc_recebe => ".$vl_perc_recebe."</PRE>" : "");
		
		if(intval($tp_recebimento) == 1)
		{
			#### RECEBER POR PRAZO ####
			$vl_cip_acumulada = round(($vl_cip),2);
			$vl_renda_inicial = round((($vl_cip / ($nr_prazo * 12))),2);
		}
		elseif(intval($tp_recebimento) == 2)
		{
			#### RECEBER POR PERCENTUAL DO SALDO ####
			$vl_cip_acumulada = round(($vl_cip),2);
			$vl_renda_inicial = round((($vl_cip * $vl_perc_recebe) / 100),2);
		}
		else 
		{
			echo "ERRO"; exit;
		}

		$debug_msg.= ($debug ? "<PRE>vl_cip_acumulada => ".$vl_cip_acumulada."</PRE>" : "");		
		$debug_msg.= ($debug ? "<PRE>vl_renda_inicial => ".$vl_renda_inicial."</PRE>" : "");	
		
		#### VERIFICA PRAZO ####
		$nr_prazo_maximo = calculaPrazo($vl_cip_acumulada);
		$debug_msg.= ($debug ? "<PRE>nr_prazo_maximo => ".$nr_prazo_maximo."</PRE>" : "");
	
		if ((($nr_prazo_maximo == 0) or (intval($nr_prazo) > $nr_prazo_maximo)) or ($vl_renda_inicial < getURINPEL()))
		{
			$ar_retorno["fl_status"]              = 1;
			$ar_retorno["debug_msg"]              = utf8_encode($debug_msg);
			$ar_retorno["tp_simulacao"]           = $tp_simulacao;
			$ar_retorno["qt_meses_apos"]          = $qt_meses_apos;
			$ar_retorno["qt_meses_ref"]           = $qt_meses_ref;			
			$ar_retorno["qt_meses_adic"]          = $qt_meses_adic;			
			$ar_retorno["qt_meses_admissao"]      = $qt_meses_admissao;			
			$ar_retorno["vl_salario_perc_partic"] = number_format($vl_salario_perc_partic,2,',','.')."%";
			$ar_retorno["vl_salario_perc_patroc"] = number_format($vl_salario_perc_patroc,2,',','.')."%";	
			$ar_retorno["vl_contribuicao"]        = number_format($vl_contribuicao_proga_partic,2,',','.');
			$ar_retorno["vl_contribuicao_patroc"] = number_format($vl_contribuicao_proga_patroc,2,',','.');
			$ar_retorno["vl_contribuicao_adic"]   = number_format($vl_contribuicao_proga_adic,2,',','.');
			
			$ar_retorno["vl_saldo_atual"]         = $vl_saldo_atual;
			$ar_retorno["vl_saldo_atual_fmt"]     = number_format($vl_saldo_atual,2,',','.');
			
			$ar_retorno["vl_saldo"]               = $vl_cip_acumulada;
			$ar_retorno["vl_saldo_fmt"]           = number_format($vl_cip_acumulada,2,',','.');
			$ar_retorno["fl_adiantamento"]        = $fl_adiantamento;
			$ar_retorno["vl_adiantamento_calc"]   = number_format($vl_adiantamento_calc,2,',','.');
			$ar_retorno["vl_adiantamento"]        = $vl_adiantamento;
			$ar_retorno["vl_renda"]               = "";
			$ar_retorno["vl_renda_fmt"]           = "";
			$ar_retorno["vl_cip_aporte"]          = $vl_cip_aporte;
			$ar_retorno["fator_resgate"]          = $fator_resgate;
			$ar_retorno["mensagem"]               = utf8_encode("Este valor é insuficiente para gerar benefício de aposentadoria no prazo estipulado na simulação.<BR>Seu benefício ficou inferior a uma Unidade Referencial.<BR>Altere os parâmetros da simulação para que seu benefício fique igual ou superior ao valor da Unidade Referencial de R$ <b>".number_format(getURINPEL(),2,',','.')."</b>.");
		}
		else
		{
			$ar_retorno["fl_status"]              = 0;
			$ar_retorno["debug_msg"]              = utf8_encode($debug_msg);
			$ar_retorno["tp_simulacao"]           = $tp_simulacao;
			$ar_retorno["qt_meses_apos"]          = $qt_meses_apos;
			$ar_retorno["qt_meses_ref"]           = $qt_meses_ref;
			$ar_retorno["qt_meses_adic"]          = $qt_meses_adic;
			$ar_retorno["qt_meses_admissao"]      = $qt_meses_admissao;
			$ar_retorno["vl_salario_perc_partic"] = number_format($vl_salario_perc_partic,2,',','.')."%";
			$ar_retorno["vl_salario_perc_patroc"] = number_format($vl_salario_perc_patroc,2,',','.')."%";			
			$ar_retorno["vl_contribuicao"]        = number_format($vl_contribuicao_proga_partic,2,',','.');
			$ar_retorno["vl_contribuicao_patroc"] = number_format($vl_contribuicao_proga_patroc,2,',','.');
			$ar_retorno["vl_contribuicao_adic"]   = number_format($vl_contribuicao_proga_adic,2,',','.');
			
			$ar_retorno["vl_saldo_atual"]         = $vl_saldo_atual;
			$ar_retorno["vl_saldo_atual_fmt"]     = number_format($vl_saldo_atual,2,',','.');
			
			$ar_retorno["vl_saldo"]               = $vl_cip_acumulada;
			$ar_retorno["vl_saldo_fmt"]           = number_format($vl_cip_acumulada,2,',','.');
			$ar_retorno["fl_adiantamento"]        = $fl_adiantamento;
			$ar_retorno["vl_adiantamento_calc"]   = number_format($vl_adiantamento_calc,2,',','.');
			$ar_retorno["vl_adiantamento"]        = $vl_adiantamento;
			$ar_retorno["vl_renda"]               = $vl_renda_inicial;
			$ar_retorno["vl_renda_fmt"]           = number_format($vl_renda_inicial,2,',','.');
			$ar_retorno["vl_cip_aporte"]          = $vl_cip_aporte;
			$ar_retorno["fator_resgate"]          = $fator_resgate;
			$ar_retorno["mensagem"]               = "";
		}
		echo json_encode($ar_retorno);
	}
	
	function getValorCota()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_indice 
					  FROM public.indices  
					 WHERE cd_indexador = 130
					 ORDER BY dt_indice DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_indice'];		
	}	
	
	function calculaCIPAtual($cd_empresa, $cd_registro_empregado, $seq_dependencia, $dt_ingresso)
	{	
		global $db;
	  
		$qr_sql = "
					SELECT inpelprev_cip_quota AS qt_cota
					  FROM simulador.inpelprev_cip_quota(".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.", TO_DATE('".$dt_ingresso."','DD/MM/YYYY'));
		          ";				  
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['qt_cota'];				
	}	
	
	function atualizaCIPAtual($qt_meses,$vl_cip,$vl_rentabilidade)
	{
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) * $vl_cip;
		return $vl_cip;
	}	
	
	function calculaCIP($vl_contribuicao,$vl_rentabilidade,$qt_meses)
	{
		
		$vl_cip = (pow((1.000000000 + $vl_rentabilidade), $qt_meses)) - 1;
		$vl_cip = $vl_cip / $vl_rentabilidade;
		$vl_cip = $vl_cip * ($vl_contribuicao);
		
		return $vl_cip;
	}
	
	function calculaContribuicao($vl_salario,$vl_salario_perc)
	{
		$vl_contribuicao = ($vl_salario * $vl_salario_perc) / 100;
		
		return round($vl_contribuicao,2);
	}	
	
	function getEvolucao($tp_recebimento,$vl_saldo_atual,$vl_renda_atual,$nr_prazo,$vl_perc_recebe,$vl_rentabilidade)
	{
		$ar_retorno = Array();
		if(intval($tp_recebimento) == 2)
		{
			$nr_prazo = ceil(($vl_saldo_atual / (($vl_saldo_atual * $vl_perc_recebe) / 100)) / 12);
		}
		
		$nr_prazo_atual = $nr_prazo;

		$ar_retorno[] = Array(
								"nr_ano"    => utf8_encode("1º"),
								"vl_saldo"  => number_format(($vl_saldo_atual),2,',','.'),
								"vl_renda"  => number_format(($vl_renda_atual),2,',','.'),
								"fl_status" => 0
							 );
		$nr_conta = 1;
		while($nr_conta < $nr_prazo)
		{
			if(($nr_conta % 2) != 0)
			{
				$bg_color = 'sort-par';
			}
			else
			{
				$bg_color = 'sort-impar';		
			}		

			$vl_A = $vl_saldo_atual * (pow((1 + $vl_rentabilidade),12));
			$vl_B = ($vl_renda_atual * (12/12)) * (((pow((1 + $vl_rentabilidade),12)) - 1) / $vl_rentabilidade);
			$vl_saldo_atual = $vl_A - $vl_B;	
			
			if(intval($tp_recebimento) == 1)
			{
				#### RECEBER POR PRAZO ####
				$vl_renda_atual = $vl_saldo_atual / (($nr_prazo_atual - 1) * 12);
			}
			elseif(intval($tp_recebimento) == 2)
			{
				#### RECEBER POR PERCENTUAL DO SALDO ####
				$vl_renda_atual = (($vl_saldo_atual * $vl_perc_recebe) / 100);
				
				#### RECALCULA PRAZO ####
				$nr_prazo = $nr_conta + ceil(($vl_saldo_atual / $vl_renda_atual) / 12);
			}
			
			if ($vl_renda_atual < getURINPEL())
			{
				$ar_retorno[] = Array(
										"nr_ano"    => utf8_encode("Pagamento do Saldo"),
										"vl_saldo"  => number_format(($vl_saldo_atual),2,',','.'),
										"vl_renda"  => "",
										"fl_status" => 1
									 );					
				break;
			}
			else
			{
				$ar_retorno[] = Array(
										"nr_ano"    => utf8_encode(($nr_conta + 1)."º"),
										"vl_saldo"  => number_format(($vl_saldo_atual),2,',','.'),
										"vl_renda"  => number_format(($vl_renda_atual),2,',','.'),
										"fl_status" => 0
									 );				
			}
			$nr_prazo_atual--;			
			$nr_conta++;
		}
		
		echo json_encode($ar_retorno);
	}		
	
	function calculaPrazo($vl_cip)
	{
		$nr_prazo = (int) (($vl_cip  / getURINPEL()) / 12); 
		if($nr_prazo < 5)
		{
			$nr_prazo = 0;
		}

		return $nr_prazo;
	}	
	
	function getURINPEL()
	{
		global $db;
		$qr_sql = "
					SELECT vlr_taxa
					  FROM public.taxas
					 WHERE cd_indexador = 89
					 ORDER BY dt_taxa DESC
					 LIMIT 1						
	              ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['vlr_taxa'];		
	}

	function ajustaData($str) 
	{
		if (substr_count($str, '/') == 2) 
		{
			list($d, $m, $y) = explode('/', $str);
			if(checkdate($m, $d, sprintf('%04u', $y)))
			{
				return "$d/$m/$y";
			}
			else
			{
				return date("d/m/Y");
			}	
		}
		elseif (substr_count($str, '-') == 2) 
		{
			list($y, $m, $d) = explode('-', $str);
			if(checkdate($m, $d, sprintf('%04u', $y)))
			{
				return "$d/$m/$y";
			}
			else
			{
				return date("d/m/Y");
			}			
		}	
		else
		{
			return "";
		}
	}

	function checkAutoPatrocinioComPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		
		$qr_sql = "
					SELECT auto_patrocinio_com_patroc AS fl_auto_com_patroc
					  FROM simulador.auto_patrocinio_com_patroc(".$cd_empresa.",".$cd_registro_empregado.",".$seq_dependencia.")
		          ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg["fl_auto_com_patroc"];
	}	
	
	
	function checkAutoPatrocinioSemPatrocinadora($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		
		$qr_sql = "
					SELECT auto_patrocinio_sem_patroc AS fl_auto_sem_patroc
					  FROM simulador.auto_patrocinio_sem_patroc(".$cd_empresa.",".$cd_registro_empregado.",".$seq_dependencia.")
		          ";
		
		/*
		$qr_sql = "
			        SELECT COUNT(*) AS fl_auto_sem_patroc
					  FROM public.afastados a
					 WHERE a.cd_empresa            = ".$cd_empresa."
					   AND a.cd_registro_empregado = ".$cd_registro_empregado."
					   AND a.seq_dependencia       = ".$seq_dependencia."
					   AND a.tipo_afastamento      = 67 -- AUTOPATROCINIO
					   AND a.id_tipo_contribuicao  = 5  -- CONTRIB NORMAL SEM PATROCINADORA
					   AND ((a.dt_final_afastamento IS NULL) OR (DATE_TRUNC('month', a.dt_final_afastamento) >= DATE_TRUNC('month',CURRENT_DATE)))
	              ";
		*/		  
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg["fl_auto_sem_patroc"];
	}

	function checkBPD($cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		global $db;
		
		/*
		$qr_sql = "
			        SELECT CASE WHEN COUNT(*) > 0 
					            THEN 'S'
								ELSE 'N'
					       END AS fl_bpd
					  FROM public.afastados a
					 WHERE a.cd_empresa            = ".$cd_empresa."
					   AND a.cd_registro_empregado = ".$cd_registro_empregado."
					   AND a.seq_dependencia       = ".$seq_dependencia."
					   AND a.tipo_afastamento      = 72 -- BPD
					   AND ((a.dt_final_afastamento IS NULL) OR (DATE_TRUNC('month', a.dt_final_afastamento) >= DATE_TRUNC('month',CURRENT_DATE)))				 
	              ";
		*/	
		$qr_sql = "
					SELECT inpelprev_bpd AS fl_bpd
					  FROM simulador.inpelprev_bpd(".$cd_empresa.",".$cd_registro_empregado.",".$seq_dependencia.", CURRENT_DATE)		 
	              ";		
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return $ar_reg['fl_bpd'];		
	}	

	function getPercSalarioPatroc($cd_empresa, $vl_salario_perc_partic)
	{
		global $db;
		
		$qr_sql = "
				SELECT fcp.num_faixa,
					   (fcp.taxa_basica  * 100) AS vl_perc_partic,
					   ((CASE WHEN COALESCE(fcp.perc_contrib_patroc, 0) = 0 THEN fcp.taxa_basica ELSE fcp.perc_contrib_patroc END)* 100) AS vl_perc_patroc
				  FROM faixas_contrib_planos fcp
				 WHERE (fcp.taxa_basica  * 100) = ".floatval($vl_salario_perc_partic)."
				   AND fcp.cd_empresa = ".$cd_empresa."
				   AND fcp.data_referencia =(SELECT MAX(fcp2.data_referencia)
						                       FROM public.faixas_contrib_planos fcp2
						                      WHERE fcp2.cd_empresa = fcp.cd_empresa
						                        AND fcp2.cd_plano = fcp.cd_plano
						                        AND fcp2.num_faixa = fcp.num_faixa)
				ORDER BY fcp.num_faixa	 
	              ";		
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
		return floatval($ar_reg['vl_perc_patroc']);		
	}

?>