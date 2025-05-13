<?php

	if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') and ($_SERVER['HTTPS'] != 'on'))
	{
		#### REDIRECIONA PARA HTTPS ####
		$ir_para_https = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$ir_para_https = str_replace('http://','',$ir_para_https);
		$ir_para_https = str_replace('https://','',$ir_para_https);
		$ir_para_https = 'https://'.$ir_para_https;
		header("location: ".$ir_para_https); 
		exit;
	}


	require_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_padrao.html');
	$tpl->prepare();	
	$tpl->newBlock('titulo');
	$tpl->assign('titulo',"Área do Participante");	

	$_REQUEST['cd_secao']  = 'SERV';
	$_REQUEST['cd_artigo'] = 135;
	include_once('monta_menu.php');
	$tpl->newBlock('conteudo');

/*
-- PRIMEIRO PAGAMENTO
http://10.63.255.150/eletroceee/sinprors_pagamento.php?re=d80277917a94f909349105e9bd532c25&comp=670b14728ad9902aecba32e22fa4f6bd 
-- PAGAMENTO ADICIONAL
http://10.63.255.150/eletroceee/sinprors_pagamento.php?re=e12788e09e6b9dabd62283ffde0b1f1f&comp=60fc815f26823dcae01685f47e4c01b9 
-- PAGAMENTO NORMAL COM ATRASADA (2 atrasada)
http://10.63.255.150/eletroceee/sinprors_pagamento.php?re=2ebf8858ac5d00c5c5640d643bdaa4ad&comp=60fc815f26823dcae01685f47e4c01b9
-- PAGAMENTO NORMAL COM ATRASADA (1 atrasada)
http://10.63.255.150/eletroceee/sinprors_pagamento.php?re=f152767dd87639ffbb326e486963168f&comp=60fc815f26823dcae01685f47e4c01b9
-- PAGAMENTO NORMAL (R$ 50,00)
http://10.63.255.150/eletroceee/sinprors_pagamento.php?re=e2a005419213e9f27f68bf4479a29c26&comp=60fc815f26823dcae01685f47e4c01b9
-- PAGAMENTO NORMAL (R$ 600,00)
http://10.63.255.150/eletroceee/sinprors_pagamento.php?re=f6719dd03cd068e1b3eaeef6958b6ddf&comp=60fc815f26823dcae01685f47e4c01b9
*/	
	
	
/*###############################################
	CODIGOS_COBRANCAS
	2450;"CONTRIBUIÇÃO SINPRO-RS PREV"
	2451;"RISCO DE MORTE SINPRO-RS"
	2452;"RISCO DE INVALIDEZ SINPRO-RS"
	
	2460;"CONTRIBUIÇÃO SINPRORS PREV C/C"
	2461;"RISCO DE MORTE SINPRO-RS C/C"
	2462;"RISCO DE INVALIDEZ SINPRORS CC"
	
	2470;"CONTR SINPRO-RS PREV ADICIONAL"
	
	2480;"CONTRIBUIÇÃO SINPRORS PREV FOL"
	2481;"RISCO DE MORTE SINPRORS FOLH"
	2482;"RISCO  INVALIDEZ SINPRORS FOLH"
	2483;"APORTE INSTITUIDOR SINPRORS"

	ADMINISTRATIVA
	2484;"CONTRIB ADM SINPRORS FOLHA"
	2485;"CONTRIB ADM SINPRORS C/C"
	2486;"CONTR ADM SINPRORS  BDL"
	
	CORREIO
	2487;"CONTR  ADM SINPRO CORREIO  BDL"
	2488;"CONTR ADM SINPRO CORREIO C/C"
	
	APOLICES
	77;"RISCO DE MORTE SINPRO-RS"
	78;"RISCO DE INVALIDEZ SINPRO-RS"
	
################################################*/
	
	#### CODIGO LANCAMENTOS ####
	$ar_cd_lancamento['PREVI'][0] = "2500";
	$ar_cd_lancamento['PREVI'][1] = "2501";
	$ar_cd_lancamento['PREVI'][2] = "2502";
	$ar_cd_lancamento['PREVI'][3] = "2503";
	$ar_cd_lancamento['PREVI'][4] = "2509";
	
	$ar_cd_lancamento['RISCO']['MORTE'][0] = "2520";
	$ar_cd_lancamento['RISCO']['MORTE'][1] = "2530";
	$ar_cd_lancamento['RISCO']['MORTE'][2] = "2540";
	
	$ar_cd_lancamento['RISCO']['INVAL'][0] = "2521";
	$ar_cd_lancamento['RISCO']['INVAL'][1] = "2531";
	$ar_cd_lancamento['RISCO']['INVAL'][2] = "2541";
	
	#### ADMINISTRATIVA ####
	$ar_cd_lancamento['ADM'][0] = "2504";
	$ar_cd_lancamento['ADM'][1] = "2505";
	$ar_cd_lancamento['ADM'][2] = "2506";
	$ar_cd_lancamento['ADM'][3] = "2507";	
	$ar_cd_lancamento['ADM'][4] = "2508";
	
	#### CORREIO ####
	$ar_cd_lancamento['CORREIO'][0] = "2511";
	$ar_cd_lancamento['CORREIO'][1] = "2512";
	
	#### CODIGO APOLICES ####
	$ar_cd_lancamento['APOL']['MORTE'] = "77"; ##VERIFICAR
	$ar_cd_lancamento['APOL']['INVAL'] = "78"; ##VERIFICAR	
   	
	
	#### VERIFICA SE RE VEIO NO LINK ####
	if(trim($_REQUEST['re']) == "")
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									ERRO (SP1) - Participante não encontrado.
									<BR><BR>
									Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	


	#### VERIFICA SE TEM RISCO ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_risco 
				  FROM boleto.boleto_instituidor('".$_REQUEST['re']."') x
				 WHERE COALESCE(x.fl_risco,'N') = 'S';
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_risco = pg_fetch_array($ob_resul);	
	if(intval($ar_risco['fl_risco']) == 0)
	{
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento.php?re='.$_REQUEST['re'].'">';
		exit;
	}

	#### BUSCA PARTICIPANTE ####
	$qr_sql = "
				SELECT p.*
 				  FROM public.participantes p
				 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
				   AND p.cd_plano = 9
              ";        
	$ob_resul = pg_query($db,$qr_sql);
	$ar_participante = pg_fetch_array($ob_resul);
	if(pg_num_rows($ob_resul) != 1)
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									ERRO (SP3) - Participante não encontrado.
									<BR><BR>
									Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	
	
	#### VERIFICA SE INSCRIÇÃO FOI CANCELADA ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_inscricao
 				  FROM public.titulares t
				 WHERE funcoes.cripto_re(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) = '".$_REQUEST['re']."'
				   AND t.dt_cancela_inscricao IS NULL
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_inscricao = pg_fetch_array($ob_resul);	
	if(intval($ar_inscricao['fl_inscricao']) == 0)
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									Inscrição cancelada.
									<BR><BR>
									Tente novamente clicando no link que você recebeu ou entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	

	#### VERIFICA SE FOI DESLIGADO ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_desligado
 				  FROM public.titulares t
				 WHERE funcoes.cripto_re(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) = '".$_REQUEST['re']."'
				   AND t.dt_desligamento_eletro IS NOT NULL
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_inscricao = pg_fetch_array($ob_resul);	
	if(intval($ar_inscricao['fl_desligado']) > 0)
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									Participante desligado.
									<BR><BR>
									Entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	

	#### VERIFICA SE É ASSISTIDO ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_ativo
 				  FROM public.titulares t
 				  JOIN public.participantes p
                    ON p.cd_empresa            = t.cd_empresa
                   AND p.cd_registro_empregado = t.cd_registro_empregado
                   AND p.seq_dependencia       = t.seq_dependencia
                   AND p.cd_plano              > 0
                   AND funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
				 WHERE projetos.participante_tipo(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) <> 'ATIV'
				   AND funcoes.cripto_re(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) = '".$_REQUEST['re']."'
              ";      

	$ob_resul = pg_query($db,$qr_sql);
	$ar_inscricao = pg_fetch_array($ob_resul);	
	if(intval($ar_inscricao['fl_ativo']) > 0)
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									Somente para Participantes ativos.
									<BR><BR>
									Entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}	
	
	#### VERIFICA SUSPENSAO TEMPORARIA DE CONTRIBUICAO ####
	$qr_sql = "
				SELECT COUNT(*) AS fl_suspensao_tmp
				  FROM public.afastados a
				 WHERE funcoes.cripto_re(a.cd_empresa, a.cd_registro_empregado, a.seq_dependencia) = '".$_REQUEST['re']."'
				   AND a.tipo_afastamento IN (94,95)
				   AND a.dt_inicio_afastamento                              <= CURRENT_DATE
				   AND COALESCE(a.dt_final_afastamento, (CURRENT_DATE + 1)) >= CURRENT_DATE
              ";         
	$ob_resul = pg_query($db,$qr_sql);
	$ar_suspensao_tmp = pg_fetch_array($ob_resul);	
	$FL_SUSPENSAO_TMP = intval($ar_suspensao_tmp["fl_suspensao_tmp"]);
	
	#echo "<!-- FL_SUSPENSAO_TMP => ".$FL_SUSPENSAO_TMP." -->";	
	
	#### BUSCA VALOR DO BDL ####
	$qr_sql = "
				 SELECT p.valor_bdl AS vl_bdl
				   FROM public.pacotes p 
				  WHERE p.cd_plano   = 9
					AND p.cd_empresa = (SELECT pa.cd_empresa
 				                          FROM public.participantes pa
				                         WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')
					AND p.cd_pacote  = 1
					AND p.dt_inicio  = DATE_TRUNC('month', CURRENT_DATE)
	          ";
	$ob_resul = pg_query($db,$qr_sql);	
	$ar_vl_bdl = pg_fetch_array($ob_resul);
	if((pg_num_rows($ob_resul) != 1))
	{
		$conteudo = '
				<body style="margin: 0px; text-align:center; padding: 0px;">
					<table width="695" border="0" align="center">
						<tr>
							<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
								<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
									ERRO (SP4) - Sem valor para BDL.
									<BR><BR>
									Entre em contato através do 0800512596.
								</h1>
							</td>
						</tr>
					</table>
				</body>		
				<BR>
				<BR>
			 ';
		$tpl->assign('conteudo',$conteudo);
		$tpl->printToScreen();			 
		exit;
	}

	#### BLOQUEAR ARRECAÇÃO DE 20/12/2018 ATÉ 31/12/2018 OS:55929 ####
	$qr_sql_arrecadacao = "SELECT CASE WHEN CURRENT_DATE BETWEEN '2018-12-20'::date AND '2018-12-31'::date THEN 'S' ELSE 'N' END AS fl_bloqueio_arrecadacao;";
	$ob_arrecadacao = pg_query($db,$qr_sql_arrecadacao);
	$ar_arrecadacao = pg_fetch_array($ob_arrecadacao);

	$fl_bloqueio_arrecadacao = $ar_arrecadacao['fl_bloqueio_arrecadacao'];
	
	#### VERIFICA TIPO DE PAGAMENTO ####
	if($_REQUEST['comp'] == md5('000000')) #### PRIMEIRO PAGAMENTO ####
	{
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento.php?re='.$_REQUEST['re'].'">';
		exit;
	}
	else if($_REQUEST['comp'] == md5('999999')) #### PAGAMENTO ADICIONAL ####
	{
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento.php?re='.$_REQUEST['re'].'">';
		exit;
	}
	else #### PAGAMENTO MENSAL ####
	{
		$ds_arq   = "tpl/tpl_familia_pagamento_risco_mensal.html";
		$ob_arq   = fopen($ds_arq, 'r');
		$conteudo = fread($ob_arq, filesize($ds_arq));
		fclose($ob_arq);		
		
		#### VERIFICA SE HÁ BLOQUETO ABERTO (SEM PAGAMENTO) ####
		$qr_sql = "
					SELECT COUNT(*) AS fl_aberto
			          FROM public.bloqueto b
					  JOIN public.participantes p
					    ON p.cd_empresa            = b.cd_empresa
					   AND p.cd_registro_empregado = b.cd_registro_empregado
					   AND p.seq_dependencia       = b.seq_dependencia					  
			         WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
					   AND b.status        IS NULL
				       AND b.data_retorno  IS NULL
					   AND b.dt_lancamento >= DATE_TRUNC('month', (CURRENT_DATE - '1 month'::interval))
					   AND CAST(b.dt_emissao AS DATE) <> CURRENT_DATE
				       AND b.dt_lancamento = (SELECT MAX(b2.dt_lancamento) 
											    FROM bloqueto b2 
											   WHERE b2.seq_dependencia       = 0 
											     AND CAST(b2.dt_emissao AS DATE) <> CURRENT_DATE
											     AND b2.cd_registro_empregado = b.cd_registro_empregado 
											     AND b2.cd_empresa            = b.cd_empresa 
											     AND b2.seq_dependencia       = b.seq_dependencia)													 
		          ";
		#echo "<PRE>$qr_sql</PRE>"; exit;
		$ob_resul = pg_query($db,$qr_sql);
		$ar_bloqueto_aberto = pg_fetch_array($ob_resul);
		if(intval($ar_bloqueto_aberto['fl_aberto']) == 0)
		{
			#echo "RED ADIC";EXIT;
			#### ENCAMINHA PARA PAGAMENTO ADICIONAL ####
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento.php?re='.$_REQUEST['re'].'">';
			exit;		
		}
		else
		{
			#### MENSAL ####
			#echo 'M';
			$conteudo = str_replace("{re_md5}",$_REQUEST['re'],$conteudo);
			$conteudo = str_replace("{comp_md5}",$_REQUEST['comp'],$conteudo);	
			$conteudo = str_replace("{vl_resumo_boleto}",number_format($ar_vl_bdl['vl_bdl'],2,",","."),$conteudo);			
			$conteudo = str_replace("{cd_empresa}",$ar_participante['cd_empresa'],$conteudo);
			$conteudo = str_replace("{cd_registro_empregado}",$ar_participante['cd_registro_empregado'],$conteudo);
			$conteudo = str_replace("{seq_dependencia}",$ar_participante['seq_dependencia'],$conteudo);
			$conteudo = str_replace("{nome}",$ar_participante['nome'],$conteudo);				
			$conteudo = str_replace("{cd_tipo_pagamento}","M",$conteudo);
			$conteudo = str_replace("{ds_tipo_pagamento}","PAGAMENTO NORMAL",$conteudo);
			$conteudo = str_replace("{fl_bloqueio_arrecadacao}",$fl_bloqueio_arrecadacao,$conteudo);
			
			#### BUSCA VENCIMENTO #### 
			$qr_sql = "
						SELECT TO_CHAR(MAX(CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
							                    THEN b.dt_limite_sem_encargos
							                    ELSE b.dt_vencimento 
						                   END),'DD/MM/YYYY') AS dt_vencimento,
							   TO_CHAR(MAX(CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
							                    THEN b.dt_limite_sem_encargos
							                    ELSE b.dt_vencimento 
						                    END),'YYYYMMDD') AS dt_vencimento_barra,							   
							   MAX(TO_CHAR(b.mes_competencia,'FM00') || TO_CHAR(b.ano_competencia,'FM/0000')) AS nr_competencia,
							   
							   --MAX(COALESCE(COALESCE(b.num_bloqueto_novo,b.num_bloqueto),0)) AS num_bloqueto,
							   
                               MAX(CASE WHEN b.num_bloqueto_novo IS NULL 
                               	        THEN b.num_bloqueto
                               	        ELSE CASE WHEN (SELECT MAX(CAST(b1.dt_emissao AS DATE))
                               				              FROM bloqueto b1
                               				             WHERE b1.num_bloqueto = b.num_bloqueto_novo) = CURRENT_DATE 
                               		              THEN b.num_bloqueto
                               			          ELSE b.num_bloqueto_novo
                               		         END 
                                   END) AS num_bloqueto,								   
							   
                               MAX(CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
	                                    THEN 'N' 
	                                    ELSE 'S'
                                   END) AS fl_vencimento,
							   CASE WHEN (MAX((SELECT COUNT(*)
								                 FROM public.bloqueto b2
								                WHERE b2.cd_empresa            = b.cd_empresa
								                  AND b2.cd_registro_empregado = b.cd_registro_empregado
								                  AND b2.seq_dependencia       = b.seq_dependencia
									              AND b2.dt_lancamento         = b.dt_lancamento
												  AND b2.dt_emissao            = b.dt_emissao
									              AND b2.status                IS NULL
								                  AND b2.data_retorno          IS NULL
									              AND b2.codigo_lancamento     IN(".implode(",",$ar_cd_lancamento['RISCO']['MORTE']).")))) < 4
							        THEN 'N' 
									ELSE 'S' 
							   END AS fl_morte,
							   CASE WHEN (MAX((SELECT COUNT(*)
								                 FROM public.bloqueto b2
								                WHERE b2.cd_empresa            = b.cd_empresa
								                  AND b2.cd_registro_empregado = b.cd_registro_empregado
								                  AND b2.seq_dependencia       = b.seq_dependencia
									              AND b2.dt_lancamento         = b.dt_lancamento
												  AND b2.dt_emissao            = b.dt_emissao
									              AND b2.status                IS NULL
								                  AND b2.data_retorno          IS NULL
									              AND b2.codigo_lancamento     IN(".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).")))) < 4
							        THEN 'N' 
									ELSE 'S' 
							   END AS fl_invalidez,
							   CASE WHEN (SUM(COALESCE((SELECT SUM(ba.valor_lancamento)
					                                      FROM public.bloqueto ba
					                                     WHERE ba.dt_lancamento         = b.dt_lancamento
												           AND ba.dt_emissao            = b.dt_emissao
                                                           AND ba.cd_empresa            = b.cd_empresa
												           AND ba.cd_registro_empregado = b.cd_registro_empregado
												           AND ba.seq_dependencia       = b.seq_dependencia
												           AND ba.ano_competencia       = b.ano_competencia
												           AND ba.mes_competencia       = b.mes_competencia
												           AND ba.codigo_lancamento     IN (".implode(",",$ar_cd_lancamento['ADM']).")),0))) = 0 
									THEN 'N' 
									ELSE 'S' 
							   END AS fl_adm,
							   COALESCE((SUM(COALESCE((SELECT SUM(ba.valor_lancamento)
					                                      FROM public.bloqueto ba
					                                     WHERE ba.dt_lancamento         = b.dt_lancamento
												           AND ba.dt_emissao            = b.dt_emissao
                                                           AND ba.cd_empresa            = b.cd_empresa
												           AND ba.cd_registro_empregado = b.cd_registro_empregado
												           AND ba.seq_dependencia       = b.seq_dependencia
												           AND ba.ano_competencia       = b.ano_competencia
												           AND ba.mes_competencia       = b.mes_competencia
												           AND ba.codigo_lancamento     IN (".implode(",",$ar_cd_lancamento['ADM']).")),0))),0) AS vl_adm,							   
							   (SUM(COALESCE((SELECT SUM(ba.valor_lancamento)
										        FROM public.bloqueto ba
										       WHERE ba.dt_lancamento         = b.dt_lancamento
										         AND ba.dt_emissao            = b.dt_emissao
										         AND ba.cd_empresa            = b.cd_empresa
										         AND ba.cd_registro_empregado = b.cd_registro_empregado
										         AND ba.seq_dependencia       = b.seq_dependencia
										         AND ba.ano_competencia       = b.ano_competencia
										         AND ba.mes_competencia       = b.mes_competencia
										         AND ba.codigo_lancamento     IN (".implode(",",$ar_cd_lancamento['CORREIO']).")),0))) AS vl_correio,
                               MAX('01/' || TO_CHAR(b.mes_competencia,'FM00') || '/' || TO_CHAR(b.ano_competencia,'FM0000')) AS dt_compentencia												 
			              FROM public.bloqueto b
					      JOIN public.participantes p
					        ON p.cd_empresa            = b.cd_empresa
					       AND p.cd_registro_empregado = b.cd_registro_empregado
					       AND p.seq_dependencia       = b.seq_dependencia
			             WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
				           AND b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).",".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE'])." ) -- BDL E RISCOS
				           AND b.status IS NULL
				           AND b.data_retorno IS NULL
				           -- ULTIMO LANÇAMENTO (ULTIMA GERAÇÃO)
						   AND b.dt_lancamento >= DATE_TRUNC('month', (CURRENT_DATE - '1 month'::interval))
						   AND CAST(b.dt_emissao AS DATE) <> CURRENT_DATE
				           AND b.dt_lancamento = (SELECT MAX(b1.dt_lancamento) 
					                                FROM public.bloqueto b1 
					                               WHERE b1.cd_empresa            = b.cd_empresa 
					                                 AND b1.cd_registro_empregado = b.cd_registro_empregado 
					                                 AND b1.seq_dependencia       = b.seq_dependencia
													 AND CAST(b1.dt_emissao AS DATE) <> CURRENT_DATE)						   
						   -- MAIOR ANO/MES DE COMPETENCIA NÃO PAGA
				           AND TO_DATE(b.ano_competencia::varchar || '-' || b.mes_competencia::varchar || '-01', 'YYYY-MM-DD') <= (SELECT MAX(TO_DATE(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD'))
					                                                                                                                 FROM public.bloqueto b2
					                                                                                                                WHERE b2.cd_empresa            = b.cd_empresa
					                                                                                                                  AND b2.cd_registro_empregado = b.cd_registro_empregado
					                                                                                                                  AND b2.seq_dependencia       = b.seq_dependencia
					                                                                                                                  AND b2.status                IS NULL 
					                                                                                                                  AND b2.data_retorno          IS NULL
																																	  AND CAST(b2.dt_emissao AS DATE) <> CURRENT_DATE
																																      -- BDL E RISCOS
					                                                                                                                  AND b2.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).",".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE'])." ))
						
			          ";
			#echo "<!-- SQL <PRE>".$qr_sql."</PRE> -->"; 
			#echo "<pre>$qr_sql</pre>"; 
			#exit;
			$ob_resul = pg_query($db,$qr_sql);			
			$ar_vencimento = pg_fetch_array($ob_resul);
			
			$fl_adm = $ar_vencimento['fl_adm'];
			$dt_compentencia = $ar_vencimento['dt_compentencia'];
		
			$conteudo = str_replace("{fl_adm}",($fl_adm == "S" ? "" : "display:none;"),$conteudo);
			
			
			$conteudo = str_replace("{dt_vencimento}",$ar_vencimento['dt_vencimento'],$conteudo);
			$conteudo = str_replace("{dt_vencimento_barra}",$ar_vencimento['dt_vencimento_barra'],$conteudo);
			$conteudo = str_replace("{nr_competencia}",$ar_vencimento['nr_competencia'],$conteudo);
			$ar_competencia = explode("/",$ar_vencimento['nr_competencia']);
			$conteudo = str_replace("{nr_mes}",$ar_competencia[0],$conteudo);
			$conteudo = str_replace("{nr_ano}",$ar_competencia[1],$conteudo);			
			$conteudo = str_replace("{num_bloqueto}",$ar_vencimento['num_bloqueto'],$conteudo);			
			
			
			
			$FL_CANCELA_RISCO = false;
			if($ar_vencimento['fl_vencimento'] == "S")
			{
				if($ar_vencimento['fl_morte'] == "S")
				{
					$FL_CANCELA_RISCO = true;
				}
				else if($ar_vencimento['fl_invalidez'] == "S")
				{
					$FL_CANCELA_RISCO = true;
				}
			}
			
			$vl_prev_total  = 0;
			$vl_risco_total = 0;
			
			#### BUSCA VALORES DO MES ####
			$qr_sql = "
						-- EM DIA
						SELECT b.codigo_lancamento,
						       b.valor_lancamento AS vl_contribuicao, 
							   TO_CHAR(b.ano_competencia,'FM0000') || '/' || TO_CHAR(b.mes_competencia,'FM00') AS competencia,
						       (CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
							         THEN 0 
							         ELSE vlr_encargo 
						       END) AS vlr_encargo,
                               COALESCE((CASE WHEN b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).") 
                                     THEN (SELECT cpr.valor
	                                         FROM public.contribuicoes_programadas cpr
	                                        WHERE cpr.cd_empresa            = b.cd_empresa
	                                          AND cpr.cd_registro_empregado = b.cd_registro_empregado
	                                          AND cpr.seq_dependencia       = b.seq_dependencia
	                                          AND cpr.dt_confirma_opcao     IS NOT NULL
	                                          AND cpr.dt_confirma_canc      IS NULL)
	                                 ELSE 0
	                           END),(SELECT t.vlr_taxa 
									  FROM public.taxas t
									  JOIN public.planos_patrocinadoras pp
										ON pp.id_unidade = t.cd_indexador 
									 WHERE pp.cd_empresa = (SELECT pa.cd_empresa
															  FROM public.participantes pa
															 WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')
									   AND pp.cd_plano   = 9
									   AND t.dt_taxa = DATE_TRUNC('month', CURRENT_DATE))) AS vl_contratada,
                               (CASE WHEN b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).") 
                                     THEN (SELECT t.vlr_taxa 
									  FROM public.taxas t
									  JOIN public.planos_patrocinadoras pp
										ON pp.id_unidade = t.cd_indexador 
									 WHERE pp.cd_empresa = (SELECT pa.cd_empresa
															  FROM public.participantes pa
															 WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')
									   AND pp.cd_plano   = 9
									   AND t.dt_taxa = DATE_TRUNC('month', CURRENT_DATE))
	                                 ELSE 0
	                           END) AS vl_minima
			              FROM public.bloqueto b
					      JOIN public.participantes p
					        ON p.cd_empresa            = b.cd_empresa
					       AND p.cd_registro_empregado = b.cd_registro_empregado
					       AND p.seq_dependencia       = b.seq_dependencia
			             WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
				           AND b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).",".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE']).",".implode(",",$ar_cd_lancamento['CORREIO'])." ) -- BDL, RISCOS E CORREIO
				           AND b.status IS NULL
				           AND b.data_retorno IS NULL
						   ".($FL_CANCELA_RISCO == true ? " AND b.codigo_lancamento NOT IN (".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE'])." )" : "")." -- CANCELA RISCOS
				           -- ULTIMO LANÇAMENTO (ULTIMA GERAÇÃO)
						   AND b.dt_lancamento >= DATE_TRUNC('month', (CURRENT_DATE - '1 month'::interval))
						   AND CAST(b.dt_emissao AS DATE) <> CURRENT_DATE
				           AND b.dt_lancamento = (SELECT MAX(b1.dt_lancamento) 
					                                FROM public.bloqueto b1 
					                               WHERE b1.cd_empresa            = b.cd_empresa 
					                                 AND b1.cd_registro_empregado = b.cd_registro_empregado 
					                                 AND b1.seq_dependencia       = b.seq_dependencia
													 AND CAST(b1.dt_emissao AS DATE) <> CURRENT_DATE)						   
						   -- MAIOR ANO/MES DE COMPETENCIA NÃO PAGA
				           AND TO_DATE(b.ano_competencia::varchar || '-' || b.mes_competencia::varchar || '-01', 'YYYY-MM-DD') = (SELECT MAX(TO_DATE(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD'))
					                                                                                                                FROM public.bloqueto b2
					                                                                                                               WHERE b2.cd_empresa            = b.cd_empresa
					                                                                                                                 AND b2.cd_registro_empregado = b.cd_registro_empregado
					                                                                                                                 AND b2.seq_dependencia       = b.seq_dependencia
					                                                                                                                 AND b2.status                IS NULL 
					                                                                                                                 AND b2.data_retorno          IS NULL
																																	 AND CAST(b2.dt_emissao AS DATE) <> CURRENT_DATE
																																     -- BDL E RISCOS
					                                                                                                                 AND b2.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).",".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE']).",".implode(",",$ar_cd_lancamento['CORREIO'])." ))
						
						 ORDER BY b.codigo_lancamento ASC
			          ";
			
			$ob_resul = pg_query($db,$qr_sql);
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$vl_risco_mes_total = 0;
			$fl_encargo = false;
			$vl_risco_mes_invalidez = 0;
			$vl_risco_mes_morte = 0;
			$vl_correio_mes = 0;
			$fl_comp_mes = FALSE;
			
			while($ar_reg = pg_fetch_array($ob_resul))
			{
				#### PREVIDENCIARIA EM DIA ####
				if (in_array($ar_reg['codigo_lancamento'], $ar_cd_lancamento['PREVI']))
				{
					$fl_comp_mes = TRUE;
					
					$conteudo = str_replace("{listaCompMes}", $ar_reg['competencia'],$conteudo);
					
					if($ar_reg['vlr_encargo'] > 0)
					{
						$fl_encargo = true;
					}

					$conteudo = str_replace("{prev_mes_competencia}", $ar_reg['competencia'],$conteudo);
					$conteudo = str_replace("{msg_suspensao_temporaria}", ($FL_SUSPENSAO_TMP > 0 ? " + encargos" : ""),$conteudo);
					
					$ar_reg['vl_minima'] = ($ar_reg['vl_minima'] < $ar_reg['vl_contribuicao'] ? $ar_reg['vl_contribuicao'] : $ar_reg['vl_minima']);
					$conteudo = str_replace("{vl_prev_mes_minima}",number_format($ar_reg['vl_minima'],2,",","."),$conteudo);
					$conteudo = str_replace("{vl_prev_mes_encargo}",number_format($ar_reg['vlr_encargo'],2,",","."),$conteudo);
					
					$vl_dif_contratada = $ar_reg['vl_contratada'] - $ar_reg['vl_minima'];
					$vl_dif_contratada = ($vl_dif_contratada < 0 ? 0 : $vl_dif_contratada);
					$conteudo = str_replace("{vl_prev_mes_contrada}",number_format($vl_dif_contratada,2,",","."),$conteudo);
										
					$vl_prev_mes_total = ($ar_reg['vl_minima'] + $ar_reg['vlr_encargo'] + $vl_dif_contratada);
					$vl_prev_mes_total_calc_adm = ($ar_reg['vl_minima'] + $vl_dif_contratada); ### CALCULA ADM SEM ENCARGO ###
					
					$conteudo = str_replace("{vl_prev_mes_total}",number_format($vl_prev_mes_total,2,",","."),$conteudo);
					
					$vl_prev_total = $vl_prev_mes_total;
				}
				
				#### RISCO EM DIA ####
				if ((in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['INVAL'])) or (in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['MORTE'])))
				{
					if (in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['INVAL'])) 
					{
						$vl_risco_mes_invalidez+= $ar_reg['vl_contribuicao'];
					}
					
					if (in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['MORTE'])) 
					{
						
						$vl_risco_mes_morte+= $ar_reg['vl_contribuicao'];
					}
				}	

				#### CORREIO ####
				if (in_array($ar_reg['codigo_lancamento'], $ar_cd_lancamento['CORREIO']))
				{
					$vl_correio_mes = $ar_reg['vl_contribuicao'];
				}					
			}
			
			if($fl_comp_mes == FALSE)
			{
				$conteudo = str_replace("{listaCompMes}", "",$conteudo);
				$conteudo = str_replace("{prev_mes_competencia}", "",$conteudo);
				$conteudo = str_replace("{msg_suspensao_temporaria}", "",$conteudo);
				$conteudo = str_replace("{vl_prev_mes_minima}", "0,00",$conteudo);
				$conteudo = str_replace("{vl_prev_mes_encargo}", "0,00",$conteudo);
				$conteudo = str_replace("{vl_prev_mes_contrada}", "0,00",$conteudo);
				$conteudo = str_replace("{vl_prev_mes_total}", "0,00",$conteudo);
			}
			
			$conteudo = str_replace("{vl_risco_mes_invalidez}",number_format($vl_risco_mes_invalidez,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_risco_mes_morte}",number_format($vl_risco_mes_morte,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_risco_mes_total}",number_format(($vl_risco_mes_invalidez + $vl_risco_mes_morte),2,",","."),$conteudo);
			$vl_risco_total = ($vl_risco_mes_invalidez + $vl_risco_mes_morte);
			

			#### BUSCA VALORES ATRASADOS ####
			$qr_sql = "
						-- ATRASADO
						SELECT b.codigo_lancamento,
						       cc.descricao,
						       TO_CHAR(b.ano_competencia,'FM0000') || '/' || TO_CHAR(b.mes_competencia,'FM00') AS competencia,						
						       b.valor_lancamento AS vl_contribuicao, 
						       (CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
							         THEN 0 
							         ELSE vlr_encargo 
						       END) AS vlr_encargo,
                               COALESCE((CASE WHEN b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).") 
                                     THEN (SELECT cpr.valor
	                                         FROM public.contribuicoes_programadas cpr
	                                        WHERE cpr.cd_empresa            = b.cd_empresa
	                                          AND cpr.cd_registro_empregado = b.cd_registro_empregado
	                                          AND cpr.seq_dependencia       = b.seq_dependencia
	                                          AND cpr.dt_confirma_opcao     IS NOT NULL
	                                          AND cpr.dt_confirma_canc      IS NULL)
	                                 ELSE 0
	                           END),(SELECT t.vlr_taxa 
									  FROM public.taxas t
									  JOIN public.planos_patrocinadoras pp
										ON pp.id_unidade = t.cd_indexador 
									 WHERE pp.cd_empresa = (SELECT pa.cd_empresa
															  FROM public.participantes pa
															 WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')
									   AND pp.cd_plano   = 9
									   AND t.dt_taxa = DATE_TRUNC('month', CURRENT_DATE))) AS vl_contratada,
                               (CASE WHEN b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).") 
                                     THEN (SELECT t.vlr_taxa 
									  FROM public.taxas t
									  JOIN public.planos_patrocinadoras pp
										ON pp.id_unidade = t.cd_indexador 
									 WHERE pp.cd_empresa = (SELECT pa.cd_empresa
															  FROM public.participantes pa
															 WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')
									   AND pp.cd_plano   = 9
									   AND t.dt_taxa = DATE_TRUNC('month', CURRENT_DATE))
	                                 ELSE 0
	                           END) AS vl_minima						   
			              FROM public.bloqueto b
					      JOIN public.participantes p
					        ON p.cd_empresa            = b.cd_empresa
					       AND p.cd_registro_empregado = b.cd_registro_empregado
					       AND p.seq_dependencia       = b.seq_dependencia
						  JOIN public.codigos_cobrancas cc
						    ON cc.codigo_lancamento = b.codigo_lancamento
			             WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
				           AND b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).",".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE']).",".implode(",",$ar_cd_lancamento['ADM']).",".implode(",",$ar_cd_lancamento['CORREIO'])." ) -- BDL + RISCOS + ADM + CORREIO
				           AND b.status IS NULL
				           AND b.data_retorno IS NULL
						   ".($FL_CANCELA_RISCO == true ? " AND b.codigo_lancamento NOT IN (".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE'])." )" : "")." -- CANCELA RISCOS
				           -- ULTIMO LANÇAMENTO (ULTIMA GERAÇÃO)
						   AND b.dt_lancamento >= DATE_TRUNC('month', (CURRENT_DATE - '1 month'::interval))
						   AND CAST(b.dt_emissao AS DATE) <> CURRENT_DATE
				           AND b.dt_lancamento = (SELECT MAX(b1.dt_lancamento) 
					                                FROM public.bloqueto b1 
					                               WHERE b1.cd_empresa            = b.cd_empresa 
					                                 AND b1.cd_registro_empregado = b.cd_registro_empregado 
					                                 AND b1.seq_dependencia       = b.seq_dependencia
													 AND CAST(b1.dt_emissao AS DATE) <> CURRENT_DATE)						   
						   -- MAIOR ANO/MES DE COMPETENCIA NÃO PAGA
				           AND TO_DATE(b.ano_competencia::varchar || '-' || b.mes_competencia::varchar || '-01', 'YYYY-MM-DD') < (SELECT MAX(TO_DATE(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD'))
					                                                                                                                FROM public.bloqueto b2
					                                                                                                               WHERE b2.cd_empresa            = b.cd_empresa
					                                                                                                                 AND b2.cd_registro_empregado = b.cd_registro_empregado
					                                                                                                                 AND b2.seq_dependencia       = b.seq_dependencia
					                                                                                                                 AND b2.status                IS NULL 
					                                                                                                                 AND b2.data_retorno          IS NULL
																																	 AND CAST(b2.dt_emissao AS DATE) <> CURRENT_DATE
																																     -- BDL E RISCOS
					                                                                                                                 AND b2.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI']).",".implode(",",$ar_cd_lancamento['RISCO']['INVAL']).",".implode(",",$ar_cd_lancamento['RISCO']['MORTE']).",".implode(",",$ar_cd_lancamento['ADM'])." ))
						
						 ORDER BY b.codigo_lancamento ASC, competencia
			          ";
			#echo "<PRE>$qr_sql</PRE>"; EXIT;
			$ob_resul = pg_query($db,$qr_sql);
			$vl_prev_atrasada_minima   = 0;
			$vl_prev_atrasada_contrada = 0;
			$vl_prev_atrasada_total    = 0;
			$vl_adm_atrasada_total     = 0;
			$vl_correio_atrasada       = 0;
			
			$vl_risco_atrasado_invalidez = 0;
			$vl_risco_atrasado_morte     = 0;
			$vl_risco_atrasado_total     = 0;
			$listaPrevAtrasada = Array();
			$listaRiscoAtrasado = Array();
			
			while($ar_reg = pg_fetch_array($ob_resul))
			{
				$fl_encargo = true;
				
				#### PREVIDENCIARIA ATRASADA ####
				if (in_array($ar_reg['codigo_lancamento'], $ar_cd_lancamento['PREVI']))
				{
					$listaPrevAtrasada[] = $ar_reg;
					$vl_prev_atrasada_minima += ($ar_reg['vl_contribuicao'] + $ar_reg['vlr_encargo']);
							
					$vl_dif_contratada = (($ar_reg['vl_contribuicao'] >= $ar_reg['vl_contratada']) ? 0 : ($ar_reg['vl_contratada'] - $ar_reg['vl_minima']));
					$vl_dif_contratada = ($vl_dif_contratada < 0 ? 0 : $vl_dif_contratada);					
					$vl_prev_atrasada_contrada += $vl_dif_contratada;
				}
				
				#### RISCO ATRASADO ####
				if ((in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['INVAL'])) or (in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['MORTE'])))
				{
					$listaRiscoAtrasado[] = $ar_reg;
					if (in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['INVAL'])) 
					{
						$vl_risco_atrasado_invalidez += $ar_reg['vl_contribuicao'];
					}
					
					if (in_array($ar_reg['codigo_lancamento'],$ar_cd_lancamento['RISCO']['MORTE'])) 
					{
						$vl_risco_atrasado_morte += $ar_reg['vl_contribuicao'];
					}
					
					$vl_risco_atrasado_total += $ar_reg['vl_contribuicao'];
					$vl_risco_total += $ar_reg['vl_contribuicao'];
				}

				#### ADMINISTRATIVA ATRASADA ####
				if (in_array($ar_reg['codigo_lancamento'], $ar_cd_lancamento['ADM']))
				{
					$vl_adm_atrasada_total += $ar_reg['vl_contribuicao'];
				}

				#### ADMINISTRATIVA ATRASADA ####
				if (in_array($ar_reg['codigo_lancamento'], $ar_cd_lancamento['CORREIO']))
				{
					$vl_correio_atrasada += $ar_reg['vl_contribuicao'];
				}				
			}
			$conteudo = str_replace("{fl_adm_atrasada}", (floatval($vl_adm_atrasada_total) > 0 ? "" : "display:none"),$conteudo);
			$conteudo = str_replace("{vl_adm_atrasada}", number_format(floatval($vl_adm_atrasada_total),2,",","."), $conteudo);
			
			$vl_prev_atrasada_total = $vl_prev_atrasada_minima + $vl_prev_atrasada_contrada;
			$vl_prev_total+= $vl_prev_atrasada_total;
			
			if($vl_prev_atrasada_total > 0)
			{
				$conteudo = str_replace("{fl_prev_atrasada}","",$conteudo);
				
				$nr_conta = 0;
				$nr_fim   = count($listaPrevAtrasada);
				$tb_prev_atrasada = "
										<table class='sort-table' id='table-1' align='center' width='90%' cellspacing='2' cellpadding='2'>
											<thead>
											<tr>
												<td>
													Competência
												</td>
												<td>
													Valor (R$)
												</td>												
											</tr>
											</thead>
											<tbody>	
									";
				$ar_comp_prev_atrasada = array();
				while ($nr_conta < $nr_fim)
				{
					$ar_comp_prev_atrasada[] = $listaPrevAtrasada[$nr_conta]['competencia'];
				
					$tb_prev_atrasada.= "
											<tr class='".($nr_conta % 2 ? 'sort-par' : 'sort-impar')."'>
												<td>
													".$listaPrevAtrasada[$nr_conta]['competencia']."
												</td>
												<td align='right'>
													".number_format(($listaPrevAtrasada[$nr_conta]['vl_contribuicao'] + $listaPrevAtrasada[$nr_conta]['vlr_encargo']),2,",",".")."
												</td>												
											</tr>
										";					
					$nr_conta++;
				}
				$tb_prev_atrasada.= "	
											<tbody>
										</table>
				                    ";				
				$conteudo = str_replace("{listaPrevAtrasada}",$tb_prev_atrasada,$conteudo);
				$conteudo = str_replace("{listaCompPrevAtrasada}",(implode(", ",$ar_comp_prev_atrasada)),$conteudo);
			}
			else
			{
				$conteudo = str_replace("{fl_prev_atrasada}","display:none;",$conteudo);
				$conteudo = str_replace("{listaPrevAtrasada}","",$conteudo);
				$conteudo = str_replace("{listaCompPrevAtrasada}","",$conteudo);
			}

			if($vl_risco_atrasado_total > 0)
			{
				$conteudo = str_replace("{fl_risco_atrasado}","",$conteudo);
				
				$nr_conta = 0;
				$nr_fim   = count($listaRiscoAtrasado);
				
				#echo "<PRE>";print_r($listaRiscoAtrasado);echo "</PRE>";
				
				$tb_risco_atrasada = "
										<table class='sort-table' id='table-2' align='center' width='90%' cellspacing='2' cellpadding='2'>
											<thead>
											<tr>
												<td>
													Competência
												</td>
												<td>
													Descrição
												</td>												
												<td>
													Valor (R$)
												</td>												
											</tr>
											</thead>
											<tbody>											
									";
				$ar_comp_risco_atrasada = array();
				while ($nr_conta < $nr_fim)
				{
					if (!in_array($listaRiscoAtrasado[$nr_conta]['competencia'], $ar_comp_risco_atrasada))
					{
						$ar_comp_risco_atrasada[] = $listaRiscoAtrasado[$nr_conta]['competencia'];
					}
					
					$tb_risco_atrasada.= "
											<tr class='".($nr_conta % 2 ? 'sort-par' : 'sort-impar')."'>
												<td>
													".$listaRiscoAtrasado[$nr_conta]['competencia']."
												</td>
												<td>
													".$listaRiscoAtrasado[$nr_conta]['descricao']."
												</td>												
												<td align='right'>
													".number_format(($listaRiscoAtrasado[$nr_conta]['vl_contribuicao'] + $listaRiscoAtrasado[$nr_conta]['vlr_encargo']),2,",",".")."
												</td>												
											</tr>
										";					
					$nr_conta++;
				}
				$tb_risco_atrasada.= "	
											<tbody>
										</table>
									 ";				
				$conteudo = str_replace("{listaRiscoAtrasado}",$tb_risco_atrasada,$conteudo);
				$conteudo = str_replace("{listaCompRiscoAtrasada}",(implode(", ",$ar_comp_risco_atrasada)),$conteudo);
			}
			else
			{
				$conteudo = str_replace("{fl_risco_atrasado}","display:none;",$conteudo);
				$conteudo = str_replace("{listaRiscoAtrasado}","",$conteudo);
				$conteudo = str_replace("{listaCompRiscoAtrasada}","",$conteudo);
			}
			
			if($fl_encargo)
			{
				$conteudo = str_replace("{ds_encargo}"," + Encargos ",$conteudo);
			}
			else
			{
				$conteudo = str_replace("{ds_encargo}","",$conteudo);
			}
			
			#echo $dt_compentencia." | ".$fl_adm;
			
			#### ADMINISTRATIVA ####
			if($fl_adm == "S")
			{
				#### BUSCA VALOR ADMINISTRATIVA ####
				$qr_sql = "
							SELECT oracle.fnc_retorna_custo_adm_instit((SELECT pa.cd_empresa::INTEGER
 				                                                          FROM public.participantes pa
				                                                         WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."'), 
																		".(trim($dt_compentencia) != "" ? "'".$dt_compentencia."'" : "TO_CHAR(CURRENT_DATE,'DD/MM/YYYY')").", 
																		".($vl_prev_mes_total_calc_adm + $vl_prev_atrasada_contrada).") AS vl_adm_mes
						  ";
		
				$ob_resul = pg_query($db,$qr_sql);
				#echo "<PRE>$qr_sql</PRE>"; #exit;
				$ar_reg_adm = pg_fetch_array($ob_resul);
				$vl_resumo_adm = floatval($ar_reg_adm['vl_adm_mes']) + floatval($vl_adm_atrasada_total);
				$conteudo = str_replace("{vl_adm_mes}", number_format(floatval($ar_reg_adm['vl_adm_mes']),2,",","."),$conteudo);
				
				#### MONTA TABELA ADMINISTRATIVA ####
				$qr_sql = "
							SELECT a.faixa, 
								   a.faixa_limite_inferior, 
								   a.faixa_limite_superior, 
								   a.usar_limite_inferior, 
								   a.percentual
							  FROM custo_adm_instituidor_percent a
							 WHERE a.cd_empresa = (SELECT pa.cd_empresa
 				                                   FROM public.participantes pa
				                                  WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')
							   AND a.dt_faixa   = DATE_TRUNC('month', ".(trim($dt_compentencia) != "" ? "TO_DATE('".$dt_compentencia."','DD/MM/YYYY')" : "CURRENT_DATE").")
							 ORDER BY a.faixa
						  ";
				
				$ob_resul = pg_query($db,$qr_sql);
				if(pg_num_rows($ob_resul) == 0)
				{
					$conteudo = '
							<body style="margin: 0px; text-align:center; padding: 0px;">
								<table width="695" border="0" align="center">
									<tr>
										<td valign="top" style="margin:0px; width: 695px; height: 490px; padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
											<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
												Não há tabela administrativa para esta competência.
												<BR><BR>
												Para mais informações entre em contato através do 0800512596.
											</h1>
										</td>
									</tr>
								</table>
							</body>		
							<BR>
							<BR>
						 ';
					$tpl->assign('conteudo',$conteudo);
					$tpl->printToScreen();			 
					exit;			
				}				
				#echo "<PRE>$qr_sql</PRE>"; #exit;				
				$tab_adm  = "";
				$if_adm   = "";
				$nr_conta = 1;
				$nr_maior = pg_num_rows($ob_resul);
				while($ar_reg_adm = pg_fetch_array($ob_resul))
				{
					$ar_adm[$nr_conta] = intval($ar_reg_adm['faixa_limite_inferior']);
					
					$tab_adm.= "ar_adm[".intval($ar_reg_adm['faixa_limite_inferior'])."] = ".$ar_reg_adm['percentual'].";\n\t\t\t";
					

					if(($nr_conta > 1) and ($nr_conta < $nr_maior))
					{
						#ROUND((CASE WHEN USAR_LIMITE_INFERIOR = 'S' THEN FAIXA_LIMITE_INFERIOR ELSE p_valor END) * PERCENTUAL /100,2)
						$if_adm.="
									".($nr_conta == 2 ? "" : "else")." if(vl_contribuicao < ".$ar_reg_adm['faixa_limite_inferior'].")
									{
										vl_contrib =  (vl_contribuicao * ar_adm[".$ar_adm[$nr_conta - 1]."]) /100;
									}						
								 ";
					}
					elseif ($nr_conta == $nr_maior) 
					{
						$if_adm.="  else
									{
										vl_contrib = (".$ar_reg_adm['faixa_limite_inferior']." * ar_adm[".intval($ar_reg_adm['faixa_limite_inferior'])."]) / 100;
									}						
								 ";					
					}					

					$nr_conta++;
				}
			}
			else
			{
				$conteudo = str_replace("{vl_adm_mes}", number_format(floatval($ar_reg_adm['vl_adm_mes']),2,",","."),$conteudo);
			}
			
			#### ADMINISTRATIVA ####
			$conteudo = str_replace("{cd_tipo_pagamento_adm}",($fl_adm == "S" ? 1 : 2),$conteudo);
			#$conteudo = str_replace("{ds_adm}",($fl_adm == "S" ? " + Contribuição Administrativa " : ""),$conteudo);
			#$conteudo = str_replace("{fl_msg_adm}",($fl_adm == "S" ? "display:none;" : ""),$conteudo);
			#$conteudo = str_replace("{fl_valor_adm}",($fl_adm == "S" ? "" : "display:none;"),$conteudo);
			$conteudo = str_replace("{tab_adm}",$tab_adm,$conteudo);
			$conteudo = str_replace("{if_adm}",$if_adm,$conteudo);
			$conteudo = str_replace("{vl_resumo_adm}",number_format($vl_resumo_adm,2,",","."),$conteudo);			
			
			#### CORRREIO ####
			$conteudo = str_replace("{vl_resumo_correio}",number_format(floatval($vl_correio_mes) + floatval($vl_correio_atrasada),2,",","."),$conteudo);
			
			### PREVIDENCIARIA ###
			$conteudo = str_replace("{vl_prev_atrasada_minima}",number_format($vl_prev_atrasada_minima,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_prev_atrasada_contrada}",number_format($vl_prev_atrasada_contrada,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_prev_atrasada_total}",number_format($vl_prev_atrasada_total,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_prev_total}",number_format($vl_prev_total,2,",","."),$conteudo);

			### RISCO ###
			$conteudo = str_replace("{vl_risco_atrasado_invalidez}",number_format($vl_risco_atrasado_invalidez,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_risco_atrasado_morte}",number_format($vl_risco_atrasado_morte,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_risco_atrasado_total}",number_format($vl_risco_atrasado_total,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_risco_total}",number_format($vl_risco_total,2,",","."),$conteudo);


			#### RESUMO ###
			$conteudo = str_replace("{vl_resumo_prev}",number_format($vl_prev_total,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_resumo_risco}",number_format($vl_risco_total,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_total_geral}",number_format(($vl_prev_total + $vl_risco_total + $vl_resumo_adm + (floatval($vl_correio_mes) + floatval($vl_correio_atrasada))),2,",","."),$conteudo);
			$conteudo = str_replace("{vl_total_pagar}",number_format(($vl_prev_total + $vl_risco_total + $vl_resumo_adm + (floatval($vl_correio_mes) + floatval($vl_correio_atrasada))),2,",","."),$conteudo);
			
								
			#### GARANTE ZERAR CAMPOS ####
			$conteudo = str_replace("{vl_prev_atrasada_total}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_atrasada_minima}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_atrasada_contrada}","0,00",$conteudo);

			$conteudo = str_replace("{vl_prev_mes_total}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_mes_minima}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_mes_contrada}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_mes_encargo}","0,00",$conteudo);	

			$conteudo = str_replace("{vl_risco_atrasado_total}","0,00",$conteudo);
			$conteudo = str_replace("{vl_risco_atrasado_invalidez}","0,00",$conteudo);
			$conteudo = str_replace("{vl_risco_atrasado_morte}","0,00",$conteudo);
			
			$conteudo = str_replace("{vl_risco_mes_total}","0,00",$conteudo);
			$conteudo = str_replace("{vl_risco_mes_invalidez}","0,00",$conteudo);
			$conteudo = str_replace("{vl_risco_mes_morte}","0,00",$conteudo);
				
			#echo "<PRE>$qr_sql</PRE>"; exit;
		}		
	}	

	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>