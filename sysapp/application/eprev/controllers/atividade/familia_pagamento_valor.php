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
	$_REQUEST['cd_artigo'] = 136;
	include_once('monta_menu.php');
	$tpl->newBlock('conteudo');
/*
PRIMEIRO PAGAMENTO
#CIP http://10.63.255.150/eletroceee/familia_pagamento.php?re=d3a52b69778ca9f17a2e4e11d327dede&comp=670b14728ad9902aecba32e22fa4f6bd 
#BDL http://10.63.255.150/eletroceee/familia_pagamento.php?re=d6a87b9b1c51ea26311395b9f5efdb28&comp=670b14728ad9902aecba32e22fa4f6bd


PAGAMENTO MENSAL
#CIP http://10.63.255.150/eletroceee/familia_pagamento.php?re=d3a52b69778ca9f17a2e4e11d327dede&comp=6064e6d3493f8079cfc90a3ba5536fdc 
#BDL http://10.63.255.150/eletroceee/familia_pagamento.php?re=d6a87b9b1c51ea26311395b9f5efdb28&comp=6064e6d3493f8079cfc90a3ba5536fdc

*/	
	
	
/*###############################################
	CODIGOS_COBRANCAS
	
	CONTRIBUICAO
	2500;"CONTRIBUIÇÃO PLANO FAMILIA FOL"
	2501;"CONTRIBUIÇÃO PLANO FAMILIA C/C"
	2502;"CONTRIBUIÇÃO PLANO FAMILIA BDL"
	2503;"CONTRIB PLANO FAMILIA FOLHA 3º"
	2509;"CONTRIB P FAMILIA FOL PAT PROP"
	
	ADMINISTRATIVA
	2504;"CONTR CUSTO ADM PLANO FAMILIA"
	2505;"CONTR CUSTO ADM PL FAMILIA C/C"
	2506;"CONTRIB ADM PLANO FAMILIA BDL"
	2507;"CONTR ADM P FAMILIA F PATR 3º"
	2508;"CONTR ADM P FAMILIA F PAT PROP"
	
	CORREIO
	2511;"CONTR ADM  FAMILIA  CORREIO"
	2512;"CONTRI ADM FAMILIA CORREIO C/C"

	
################################################*/
	
	#### CODIGO LANCAMENTOS ####
	$ar_cd_lancamento['PREVI'][0] = "2500";
	$ar_cd_lancamento['PREVI'][1] = "2501";
	$ar_cd_lancamento['PREVI'][2] = "2502";
	$ar_cd_lancamento['PREVI'][3] = "2503";
	$ar_cd_lancamento['PREVI'][4] = "2509";

	#### ADMINISTRATIVA ####
	$ar_cd_lancamento['ADM'][0] = "2504";
	$ar_cd_lancamento['ADM'][1] = "2505";
	$ar_cd_lancamento['ADM'][2] = "2506";
	$ar_cd_lancamento['ADM'][3] = "2507";	
	$ar_cd_lancamento['ADM'][4] = "2508";	
	
	#### CORREIO ####
	$ar_cd_lancamento['CORREIO'][0] = "2511";
	$ar_cd_lancamento['CORREIO'][1] = "2512";

	
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
	if(intval($ar_risco['fl_risco']) > 0)
	{
		echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=familia_pagamento_risco.php?re='.$_REQUEST['re'].'">';
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
				 WHERE projetos.participante_tipo(t.cd_empresa, t.cd_registro_empregado, t.seq_dependencia) NOT IN ('ATIV', 'APOS')
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

	#echo $_REQUEST['comp'];

	#### BLOQUEAR ARRECAÇÃO DE 20/12/2018 ATÉ 31/12/2018 OS:55929 ####
	$qr_sql_arrecadacao = "SELECT CASE WHEN CURRENT_DATE BETWEEN '2018-12-20'::date AND '2018-12-31'::date THEN 'S' ELSE 'N' END AS fl_bloqueio_arrecadacao;";
	$ob_arrecadacao = pg_query($db,$qr_sql_arrecadacao);
	$ar_arrecadacao = pg_fetch_array($ob_arrecadacao);

	$fl_bloqueio_arrecadacao = $ar_arrecadacao['fl_bloqueio_arrecadacao'];
	
	#### VERIFICA TIPO DE PAGAMENTO ####
	if($_REQUEST['comp'] == md5('000000')) #### PRIMEIRO PAGAMENTO ####
	{
		$ds_arq   = "tpl/tpl_familia_pagamento_primeiro.html";
		$ob_arq   = fopen($ds_arq, 'r');
		$conteudo = fread($ob_arq, filesize($ds_arq));
		fclose($ob_arq);
		
		#### VERIFICA SE JÁ PAGOU ####
		$qr_sql = "
				    SELECT COUNT(*) AS fl_pago
					  FROM public.titulares_planos tp 
					  JOIN public.participantes p
					    ON p.cd_empresa            = tp.cd_empresa
					   AND p.cd_registro_empregado = tp.cd_registro_empregado
					   AND p.seq_dependencia       = tp.seq_dependencia
					 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
					   AND tp.dt_ingresso_plano = (SELECT max(tp1.dt_ingresso_plano)
								                     FROM public.titulares_planos tp1 
								                    WHERE tp1.cd_empresa            = tp.cd_empresa 
								                      AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
								                      AND tp1.seq_dependencia       = tp.seq_dependencia)
					   AND tp.dt_ingresso_plano IS NOT NULL		
		          ";
		$ob_resul = pg_query($db,$qr_sql);
		$ar_1pg = pg_fetch_array($ob_resul);
		if(intval($ar_1pg['fl_pago']) > 0)
		{
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
													FROM public.bloqueto b2 
												   WHERE b2.seq_dependencia       = 0 
													 AND b2.cd_registro_empregado = b.cd_registro_empregado 
													 AND b2.cd_empresa            = b.cd_empresa 
													 AND b2.seq_dependencia       = b.seq_dependencia
													 AND CAST(b2.dt_emissao AS DATE) <> CURRENT_DATE)													 
					  ";
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$ob_resul = pg_query($db,$qr_sql);
			$ar_bloqueto_aberto = pg_fetch_array($ob_resul);
			if(intval($ar_bloqueto_aberto['fl_aberto']) == 0)
			{
				#### ENCAMINHA PARA PAGAMENTO ADICIONAL ####
				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?re='.$_REQUEST['re'].'&comp='.md5('999999').'">';
				exit;
			}
			else
			{
				#### BUSCA A ULTIMA COMPETENCIA DO BLOQUETO ABERTO (SEM PAGAMENTO) ####
				$qr_sql = "
							SELECT TO_CHAR(MAX(TO_DATE(TO_CHAR(b.ano_competencia,'FM0000')||TO_CHAR(b.mes_competencia,'FM00'),'YYYYMM')),'MMYYYY') AS comp
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
														FROM public.bloqueto b2 
													   WHERE b2.seq_dependencia       = 0 
														 AND b2.cd_registro_empregado = b.cd_registro_empregado 
														 AND b2.cd_empresa            = b.cd_empresa 
														 AND b2.seq_dependencia       = b.seq_dependencia
														 AND CAST(b2.dt_emissao AS DATE) <> CURRENT_DATE)													 
						  ";
				#echo "<PRE>$qr_sql</PRE>"; exit;
				$ob_resul = pg_query($db,$qr_sql);
				$ar_bloqueto_aberto = pg_fetch_array($ob_resul);			
				
				#### ENCAMINHA PARA PAGAMENTO MENSAL ####
				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?re='.$_REQUEST['re'].'&comp='.md5($ar_bloqueto_aberto['comp']).'">';
				exit;			
			}
		}
		else
		{
			#### OS: 46852 - ALTERAÇÃO REGULAMENTAR 11/08/2016 ####
			#### OS: 48554 - ALTERAÇÃO REGULAMENTAR 11/03/2017 ####
			$qr_sql = "SELECT CASE WHEN CURRENT_DATE >= TO_DATE('11/03/2017', 'DD/MM/YYYY') THEN 'S' ELSE 'N' END AS fl_bloqueio;";
			$ob_resul = pg_query($db,$qr_sql);
			$ar_blq = pg_fetch_array($ob_resul);

			if(trim($ar_blq['fl_bloqueio']) == 'S')
			{
				$conteudo = '
					<body style="margin: 0px; text-align:center; padding: 0px;">
						<table width="695" border="0" align="center">
							<tr>
								<td valign="top" style="margin:0px; width: 695px; height: 490px;padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
									<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
										Entrar em contato pelo 0800-512596 (fixo) ou 51-30271221(celular), ou ainda, através do e-mail: atendimento@eletroceee.com.br
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
			
			#echo 'P';
			$conteudo = str_replace("{re_md5}",$_REQUEST['re'],$conteudo);
			$conteudo = str_replace("{comp_md5}",$_REQUEST['comp'],$conteudo);		
			$conteudo = str_replace("{vl_resumo_boleto}",number_format($ar_vl_bdl['vl_bdl'],2,",","."),$conteudo);			
			$conteudo = str_replace("{cd_empresa}",$ar_participante['cd_empresa'],$conteudo);
			$conteudo = str_replace("{cd_registro_empregado}",$ar_participante['cd_registro_empregado'],$conteudo);
			$conteudo = str_replace("{seq_dependencia}",$ar_participante['seq_dependencia'],$conteudo);
			$conteudo = str_replace("{nome}",$ar_participante['nome'],$conteudo);				
			$conteudo = str_replace("{cd_tipo_pagamento}","P",$conteudo);
			$conteudo = str_replace("{ds_tipo_pagamento}","PRIMEIRO PAGAMENTO",$conteudo);
		
			#### VENCIMENTO ####
			$qr_sql = "	
						SELECT TO_CHAR((CASE WHEN CURRENT_DATE > funcoes.dia_util('ANTES', CAST((DATE_TRUNC('month', CURRENT_DATE) + '9 days'::interval) AS DATE), 0)
                                             THEN funcoes.dia_util('ANTES', CAST((DATE_TRUNC('month', CURRENT_DATE + '1 months'::interval) + '9 days'::interval) AS DATE), 0)
                                             ELSE funcoes.dia_util('ANTES', CAST((DATE_TRUNC('month', CURRENT_DATE) + '9 days'::interval) AS DATE), 0)
                                        END),'DD/MM/YYYY') AS dt_vencimento,
                               TO_CHAR((CASE WHEN CURRENT_DATE > funcoes.dia_util('ANTES', CAST((DATE_TRUNC('month', CURRENT_DATE) + '9 days'::interval) AS DATE), 0)
                                             THEN funcoes.dia_util('ANTES', CAST((DATE_TRUNC('month', CURRENT_DATE + '1 months'::interval) + '9 days'::interval) AS DATE), 0)
                                             ELSE funcoes.dia_util('ANTES', CAST((DATE_TRUNC('month', CURRENT_DATE) + '9 days'::interval) AS DATE), 0)
                                        END),'YYYYMMDD') AS dt_vencimento_barra								   
					  ";		   
			$ob_resul = pg_query($db,$qr_sql);
			$ar_vencimento = pg_fetch_array($ob_resul);			
			$conteudo = str_replace("{dt_vencimento}",$ar_vencimento['dt_vencimento'],$conteudo);
			$conteudo = str_replace("{dt_vencimento_barra}",$ar_vencimento['dt_vencimento_barra'],$conteudo);
			$conteudo = str_replace("{nr_competencia}","Primeiro Pagamento",$conteudo);		
			$conteudo = str_replace("{nr_mes}","00",$conteudo);
			$conteudo = str_replace("{nr_ano}","0000",$conteudo);			
			
			#### PREVIDENCIARIA ####
			$qr_sql = "
						SELECT (projetos.participante_valor_contrib_previdenciaria(p.cd_empresa::integer, p.cd_registro_empregado::integer, p.seq_dependencia::integer, (oracle.fnc_busca_forma_pag_inst(p.cd_empresa::INTEGER,p.cd_registro_empregado::INTEGER,p.seq_dependencia::INTEGER)))) AS vl_contribuicao
						  FROM public.participantes p
						 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
					  ";
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$ob_resul = pg_query($db,$qr_sql);
			$ar_contribuicao = pg_fetch_array($ob_resul);		

			if(($ar_contribuicao['vl_contribuicao'] == "") or ($ar_contribuicao['vl_contribuicao'] == 0))
			{
				$conteudo = '
						<body style="margin: 0px; text-align:center; padding: 0px;">
							<table width="695" border="0" align="center">
								<tr>
									<td valign="top" style="margin:0px; width: 695px; height: 490px; padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
										<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
											ERRO (SP5) - Não há valor para contribuição definida.
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
			
			#### ADMINISTRATIVA ####
			$qr_sql = "
						SELECT oca.cd_tipo_opcao
                          FROM public.opcoes_custo_adm oca
                         WHERE funcoes.cripto_re(oca.cd_empresa, oca.cd_registro_empregado, oca.seq_dependencia) = '".$_REQUEST['re']."'
						   AND oca.dt_cancela_sistema IS NULL
						   AND oca.dt_inclui_sistema = (SELECT MAX(oca1.dt_inclui_sistema)
							                              FROM public.opcoes_custo_adm oca1
							                             WHERE oca1.dt_cancela_sistema    IS NULL
								                           AND oca1.cd_empresa            = oca.cd_empresa
								                           AND oca1.cd_registro_empregado = oca.cd_registro_empregado
								                           AND oca1.seq_dependencia       = oca.seq_dependencia);						 
					  ";
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$ob_resul = pg_query($db,$qr_sql);
			$ar_op_adm = pg_fetch_array($ob_resul);
			
			if(intval($ar_op_adm['cd_tipo_opcao']) == 1)
			{
				$qr_sql = "
							SELECT oracle.fnc_retorna_custo_adm_instit(p.cd_empresa::INTEGER, TO_CHAR(CURRENT_DATE,'DD/MM/YYYY'), ".$ar_contribuicao['vl_contribuicao'].") AS vl_adm,
								   CASE WHEN oracle.fnc_retorna_op_cust_correio(9, p.cd_empresa::INTEGER, p.cd_registro_empregado::INTEGER, p.seq_dependencia::INTEGER) = 'C' --CORREIOS
										THEN oracle.fnc_retorna_custo_adm_instit_correio(p.cd_empresa::INTEGER, TO_CHAR(CURRENT_DATE,'DD/MM/YYYY'), ".$ar_contribuicao['vl_contribuicao'].")
										ELSE 0
								   END AS vl_correio
							  FROM public.participantes p
							 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'							
						  ";
				#echo "<PRE>$qr_sql</PRE>"; exit;
				$ob_resul = pg_query($db,$qr_sql);
				$ar_adm = pg_fetch_array($ob_resul);
				
				if(($ar_adm['vl_adm'] == "") or ($ar_adm['vl_adm'] == 0))
				{
					$conteudo = '
							<body style="margin: 0px; text-align:center; padding: 0px;">
								<table width="695" border="0" align="center">
									<tr>
										<td valign="top" style="margin:0px; width: 695px; height: 490px; padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
											<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
												Não há valor para contribuição administrativa definida.
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
			}
			else
			{
				$ar_adm['vl_adm'] = 0;
				$ar_adm['vl_correio'] = 0;
			}
			
			$conteudo = str_replace("{cd_tipo_pagamento_adm}", intval($ar_op_adm['cd_tipo_opcao']),$conteudo);				
			$conteudo = str_replace("{vl_contribuicao}",number_format($ar_contribuicao['vl_contribuicao'],2,",","."),$conteudo);				
			$conteudo = str_replace("{vl_prev_total}",number_format($ar_contribuicao['vl_contribuicao'],2,",","."),$conteudo);	
			$conteudo = str_replace("{vl_resumo_prev}",number_format($ar_contribuicao['vl_contribuicao'],2,",","."),$conteudo);	
			$conteudo = str_replace("{vl_resumo_adm}",number_format($ar_adm['vl_adm'],2,",","."),$conteudo);	
			$conteudo = str_replace("{vl_resumo_correio}",number_format($ar_adm['vl_correio'],2,",","."),$conteudo);	
			$conteudo = str_replace("{vl_total_pagar}",number_format($ar_contribuicao['vl_contribuicao'] + $ar_adm['vl_adm'] + $ar_adm['vl_correio'],2,",","."),$conteudo);	
			
			#CIP http://10.63.255.150/eletroceee/familia_pagamento.php?re=d3a52b69778ca9f17a2e4e11d327dede&comp=670b14728ad9902aecba32e22fa4f6bd 
			#BDL http://10.63.255.150/eletroceee/familia_pagamento.php?re=d6a87b9b1c51ea26311395b9f5efdb28&comp=670b14728ad9902aecba32e22fa4f6bd 
		}
	}
	else if($_REQUEST['comp'] == md5('999999')) #### PAGAMENTO ADICIONAL ####
	{
		$ds_arq   = "tpl/tpl_familia_pagamento_adicional.html";
		$ob_arq   = fopen($ds_arq, 'r');
		$conteudo = fread($ob_arq, filesize($ds_arq));
		fclose($ob_arq);
		

		#### VERIFICA SE HÁ BLOQUETO ABERTO (SEM PAGAMENTO) ####
		/* 19/05/2020
		$qr_sql = "
					SELECT COUNT(*) AS fl_aberto 
					 FROM boleto.boleto_instituidor('".$_REQUEST['re']."')
					WHERE mes_competencia <> 99
					  AND ano_competencia <> 9999		
		          ";
		*/		  
		$qr_sql = "
					SELECT 0 AS fl_aberto 
		          ";		
		$ob_resul = pg_query($db,$qr_sql);
		$ar_bloqueto_aberto = pg_fetch_array($ob_resul);
		if(intval($ar_bloqueto_aberto['fl_aberto']) > 0)
		{
			$conteudo = '
					<body style="margin: 0px; text-align:center; padding: 0px;">
						<table width="695" border="0" align="center">
							<tr>
								<td valign="top" style="margin:0px; width: 695px; height: 490px; padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
									<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:blue">
										Não é possível fazer pagamento de contribuição voluntária.
										<BR>
										Você possui contribuições em aberto.
										<BR><BR>
										Para mais informações acesse o autoatendimento ou entre em contato através do 0800512596.
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
		else
		{
			#### VERIFICA SE JÁ É PARTICIPANTE ####
			$qr_sql = "
				    SELECT COUNT(*) AS fl_participante
					  FROM public.titulares_planos tp 
					  JOIN public.participantes p
					    ON p.cd_empresa            = tp.cd_empresa
					   AND p.cd_registro_empregado = tp.cd_registro_empregado
					   AND p.seq_dependencia       = tp.seq_dependencia
					 WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
					   AND tp.dt_ingresso_plano = (SELECT max(tp1.dt_ingresso_plano)
								                     FROM public.titulares_planos tp1 
								                    WHERE tp1.cd_empresa            = tp.cd_empresa 
								                      AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
								                      AND tp1.seq_dependencia       = tp.seq_dependencia)
					   AND tp.dt_ingresso_plano IS NOT NULL	
					  ";
			$ob_resul = pg_query($db,$qr_sql);
			$ar_part_plano = pg_fetch_array($ob_resul);			
			
			if(intval($ar_part_plano['fl_participante']) == 0)
			{
				#echo "RED PRIMEIRO";EXIT;
				#### ENCAMINHA PARA PRIMEIRO PAGAMENTO ####
				echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?re='.$_REQUEST['re'].'&comp='.md5('000000').'">';
				exit;			
			}
			else
			{
				#echo 'A';
				$conteudo = str_replace("{re_md5}",$_REQUEST['re'],$conteudo);
				$conteudo = str_replace("{comp_md5}",$_REQUEST['comp'],$conteudo);		
				$conteudo = str_replace("{vl_resumo_boleto}",number_format($ar_vl_bdl['vl_bdl'],2,",","."),$conteudo);			
				$conteudo = str_replace("{cd_empresa}",$ar_participante['cd_empresa'],$conteudo);
				$conteudo = str_replace("{cd_registro_empregado}",$ar_participante['cd_registro_empregado'],$conteudo);
				$conteudo = str_replace("{seq_dependencia}",$ar_participante['seq_dependencia'],$conteudo);
				$conteudo = str_replace("{nome}",$ar_participante['nome'],$conteudo);				
				$conteudo = str_replace("{cd_tipo_pagamento}","A",$conteudo);
				$conteudo = str_replace("{ds_tipo_pagamento}","PAGAMENTO DE CONTRIBUIÇÃO VOLUNTÁRIA",$conteudo);
				$conteudo = str_replace("{fl_bloqueio_arrecadacao}",$fl_bloqueio_arrecadacao,$conteudo);
				
				#### VENCIMENTO ####
				/*
				$qr_sql = "
							SELECT TO_CHAR(date_trunc('day', dd)::DATE,'DD/MM/YYYY') AS dt_vencimento,
							       TO_CHAR(date_trunc('day', dd)::DATE,'YYYYMMDD') AS dt_vencimento_barra
							  FROM generate_series ((CURRENT_DATE + '3 day'::interval), CURRENT_DATE + '30 day'::INTERVAL, '1 day'::interval) dd	
						  ";
				*/		 
				$qr_sql = "
							SELECT dt_vencimento, 
							       dt_vencimento_barra 
							  FROM boleto.boleto_vencimento_esporadica()	
						  ";				
				$ob_resul = pg_query($db,$qr_sql);	
				
				$dt_vencimento_adicional = '';
				while ($ar_vencimento = pg_fetch_array($ob_resul)) 
				{
					$dt_vencimento_adicional.= '<option value="'.$ar_vencimento['dt_vencimento_barra'].'">'.$ar_vencimento['dt_vencimento'].'</option>';
				}
				$conteudo = str_replace("{dt_vencimento_adicional}",$dt_vencimento_adicional,$conteudo);
				
				$ob_resul = pg_query($db,$qr_sql);
				$ar_vencimento = pg_fetch_array($ob_resul);			
				$conteudo = str_replace("{dt_vencimento}",$ar_vencimento['dt_vencimento'],$conteudo);
				$conteudo = str_replace("{dt_vencimento_barra}",$ar_vencimento['dt_vencimento_barra'],$conteudo);
				$conteudo = str_replace("{nr_competencia}","Pagamento de Contribuição Vonluntária",$conteudo);		
				$conteudo = str_replace("{nr_mes}","99",$conteudo);
				$conteudo = str_replace("{nr_ano}","9999",$conteudo);			
				
				#### PREVIDENCIARIA ####
				$ar_contribuicao['vl_contribuicao_minima'] = 0;
				/*
				# RETIRADO O VALOR MÍNIMO PARA PAGAMENTO DE ADICIONAL - OS 31213 #
				$qr_sql = "
							SELECT t.vlr_taxa AS vl_contribuicao_minima
							  FROM public.taxas t
							  JOIN public.planos_patrocinadoras pp
								ON pp.id_unidade = t.cd_indexador 
							 WHERE pp.cd_empresa = 19
							   AND pp.cd_plano   = 9
							   AND t.dt_taxa     = DATE_TRUNC('month', CURRENT_DATE)						   
						  ";
				#echo "<PRE>$qr_sql</PRE>"; exit;
				$ob_resul = pg_query($db,$qr_sql);
				$ar_contribuicao = pg_fetch_array($ob_resul);		

				if($ar_contribuicao['vl_contribuicao_minima'] == 0)
				{
					$conteudo = '
							<body style="margin: 0px; text-align:center; padding: 0px;">
								<table width="695" border="0" align="center">
									<tr>
										<td valign="top" style="margin:0px; width: 695px; height: 490px; padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
											<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
												ERRO (SP6) - Não há valor para contribuição mínima definida.
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
				*/
				$conteudo = str_replace("{vl_contribuicao_minima}",number_format($ar_contribuicao['vl_contribuicao_minima'],2,",","."),$conteudo);				
				$conteudo = str_replace("{vl_total_pagar}",number_format($ar_contribuicao['vl_contribuicao_minima'],2,",","."),$conteudo);
			}
		}

		#http://10.63.255.150/eletroceee/familia_pagamento.php?re=d3a52b69778ca9f17a2e4e11d327dede&comp=60fc815f26823dcae01685f47e4c01b9 
	}
	else #### PAGAMENTO MENSAL ####
	{
		$ds_arq   = "tpl/tpl_familia_pagamento_mensal.html";
		$ob_arq   = fopen($ds_arq, 'r');
		$conteudo = fread($ob_arq, filesize($ds_arq));
		fclose($ob_arq);	

		$conteudo = str_replace("{REMOTE_IP}",$_SERVER["REMOTE_ADDR"],$conteudo);		
		
		#### VERIFICA SE HÁ BLOQUETO ABERTO (SEM PAGAMENTO) ####
		$qr_sql = "
					SELECT COUNT(*) AS fl_aberto 
					 FROM boleto.boleto_instituidor('".$_REQUEST['re']."')
					WHERE mes_competencia <> 99
					  AND ano_competencia <> 9999											 
		          ";
		#echo "<PRE>$qr_sql</PRE>"; exit;
		$ob_resul = pg_query($db,$qr_sql);
		$ar_bloqueto_aberto = pg_fetch_array($ob_resul);
		if(intval($ar_bloqueto_aberto['fl_aberto']) == 0)
		{
			#echo "RED ADIC";EXIT;
			#### ENCAMINHA PARA PAGAMENTO ADICIONAL ####
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?re='.$_REQUEST['re'].'&comp='.md5('999999').'">';
			exit;
		}
		else
		{
			#CIP http://10.63.255.150/eletroceee/familia_pagamento.php?re=d3a52b69778ca9f17a2e4e11d327dede&comp=6064e6d3493f8079cfc90a3ba5536fdc
			#BDL http://10.63.255.150/eletroceee/familia_pagamento.php?re=d6a87b9b1c51ea26311395b9f5efdb28&comp=6064e6d3493f8079cfc90a3ba5536fdc 
			
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
			$conteudo = str_replace("{fl_bloqueio_arrecadacao}",$fl_bloqueio_arrecadacao,$conteudo);
			
			#### BUSCA VENCIMENTO ####
			$qr_sql = "
						SELECT TO_CHAR((CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
							                    THEN b.dt_limite_sem_encargos
							                    ELSE b.dt_vencimento 
						                   END),'DD/MM/YYYY') AS dt_vencimento,
							   TO_CHAR((CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
							                    THEN b.dt_limite_sem_encargos
							                    ELSE b.dt_vencimento 
						                    END),'YYYYMMDD') AS dt_vencimento_barra,							   
							   (TO_CHAR(b.mes_competencia,'FM00') || TO_CHAR(b.ano_competencia,'FM/0000')) AS nr_competencia,
							   
							   --(COALESCE(COALESCE(b.num_bloqueto_novo,b.num_bloqueto),0)) AS num_bloqueto,
							   
                               CASE WHEN b.num_bloqueto_novo IS NULL 
                               	    THEN b.num_bloqueto
                               	    ELSE CASE WHEN (SELECT MAX(CAST(b1.dt_emissao AS DATE))
                               				          FROM bloqueto b1
                               				         WHERE b1.num_bloqueto = b.num_bloqueto_novo) = CURRENT_DATE 
                               		          THEN b.num_bloqueto
                               			      ELSE b.num_bloqueto_novo
                               		     END 
                               END AS num_bloqueto,						   
							   
                               (CASE WHEN CURRENT_DATE <= b.dt_limite_sem_encargos
	                                    THEN 'N' 
	                                    ELSE 'S'
                                   END) AS fl_vencimento,
							   b.id_suspensao_presumida,
							   (CASE WHEN b.id_suspensao_presumida = 'A' THEN 'DO MÊS'
									 WHEN b.id_suspensao_presumida = 'S' THEN 'CONTRIBUIÇÃO SUSPENSA'
									 WHEN b.id_suspensao_presumida = 'N' THEN 'ATRASADA'
									 ELSE 'CONTRIBUIÇÃO'
							   END) AS ds_boleto
			              FROM public.bloqueto b
					      JOIN public.participantes p
					        ON p.cd_empresa            = b.cd_empresa
					       AND p.cd_registro_empregado = b.cd_registro_empregado
					       AND p.seq_dependencia       = b.seq_dependencia
			             WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
						   AND funcoes.cripto_mes_ano(b.mes_competencia,b.ano_competencia) = '".$_REQUEST['comp']."'
				           AND b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI'])." ) -- BDL E ADM
				           AND b.status       IS NULL
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
			          ";
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$ob_resul = pg_query($db,$qr_sql);	
			if(pg_num_rows($ob_resul) == 0)
			{
				$conteudo = '
						<body style="margin: 0px; text-align:center; padding: 0px;">
							<table width="695" border="0" align="center">
								<tr>
									<td valign="top" style="margin:0px; width: 695px; height: 490px; padding-top: 0px; padding-left: 50px; padding-right: 50px; padding-bottom: 0px;">
										<h1 style="margin-top: 100px; font-family: Calibri, Arial; font-size: 22pt; width:100%; text-align:center; color:red">
											Não há valor para esta competência.
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
			
			$ar_vencimento = pg_fetch_array($ob_resul);
			$conteudo = str_replace("{ds_tipo_pagamento}",($ar_vencimento['id_suspensao_presumida'] == "S" ? "PAGAMENTO - ".$ar_vencimento['ds_boleto'] : "PAGAMENTO NORMAL - ".$ar_vencimento['ds_boleto']),$conteudo);
			$conteudo = str_replace("{FL_CONTRIBUICAO_SUSPESA}",($ar_vencimento['id_suspensao_presumida'] == "S" ? "" : "display:none;"),$conteudo);
			$conteudo = str_replace("{BT_APORTE}",'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?re='.$_REQUEST['re'].'&comp='.md5('999999'),$conteudo);
			$conteudo = str_replace("{dt_vencimento}",$ar_vencimento['dt_vencimento'],$conteudo);
			$conteudo = str_replace("{dt_vencimento_barra}",$ar_vencimento['dt_vencimento_barra'],$conteudo);
			$conteudo = str_replace("{nr_competencia}",$ar_vencimento['nr_competencia'],$conteudo);
			$ar_competencia = explode("/",$ar_vencimento['nr_competencia']);
			$conteudo = str_replace("{nr_mes}",$ar_competencia[0],$conteudo);
			$conteudo = str_replace("{nr_ano}",$ar_competencia[1],$conteudo);
			$conteudo = str_replace("{num_bloqueto}",$ar_vencimento['num_bloqueto'],$conteudo);			
			
			#### BUSCA VALORES DO MES ####
			$qr_sql = "
						-- EM DIA
						SELECT b.codigo_lancamento,
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
	                           END) AS vl_minima,
							   CASE WHEN COALESCE((SELECT ba.valor_lancamento 
					                                 FROM public.bloqueto ba
					                                WHERE ba.dt_lancamento         = b.dt_lancamento
													  AND ba.dt_emissao            = b.dt_emissao
                                                      AND ba.cd_empresa            = b.cd_empresa
													  AND ba.cd_registro_empregado = b.cd_registro_empregado
													  AND ba.seq_dependencia       = b.seq_dependencia
													  AND ba.ano_competencia       = b.ano_competencia
													  AND ba.mes_competencia       = b.mes_competencia
													  AND ba.codigo_lancamento     IN (".implode(",",$ar_cd_lancamento['ADM']).")),0) = 0 
									THEN 'N' 
									ELSE 'S' 
							   END AS fl_adm,
							   COALESCE((SELECT SUM(ba.valor_lancamento)
										   FROM public.bloqueto ba
										  WHERE ba.dt_lancamento         = b.dt_lancamento
										    AND ba.dt_emissao            = b.dt_emissao
										    AND ba.cd_empresa            = b.cd_empresa
										    AND ba.cd_registro_empregado = b.cd_registro_empregado
										    AND ba.seq_dependencia       = b.seq_dependencia
										    AND ba.ano_competencia       = b.ano_competencia
										    AND ba.mes_competencia       = b.mes_competencia
										    AND ba.codigo_lancamento     IN (".implode(",",$ar_cd_lancamento['CORREIO']).")),0) AS vl_correio,
                               '01/' || TO_CHAR(b.mes_competencia,'FM00') || '/' || TO_CHAR(b.ano_competencia,'FM0000') AS dt_compentencia
			              FROM public.bloqueto b
					      JOIN public.participantes p
					        ON p.cd_empresa            = b.cd_empresa
					       AND p.cd_registro_empregado = b.cd_registro_empregado
					       AND p.seq_dependencia       = b.seq_dependencia
			             WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) = '".$_REQUEST['re']."'
						   AND funcoes.cripto_mes_ano(b.mes_competencia,b.ano_competencia) = '".$_REQUEST['comp']."'
				           AND b.codigo_lancamento IN (".implode(",",$ar_cd_lancamento['PREVI'])." ) -- BDL
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
						 ORDER BY b.codigo_lancamento ASC
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
											Não há valor para esta competência.
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
			
			$ar_reg = pg_fetch_array($ob_resul);
			#echo "<!-- <PRE>$qr_sql</PRE> -->"; #exit;
			$vl_prev_total  = 0;
			$fl_adm     = $ar_reg['fl_adm'];
			$vl_resumo_correio = $ar_reg['vl_correio'];
			$dt_compentencia = $ar_reg['dt_compentencia'];
			

			$conteudo = str_replace("{prev_mes_competencia}", $ar_reg['competencia'],$conteudo);
			$conteudo = str_replace("{msg_suspensao_temporaria}", ($FL_SUSPENSAO_TMP > 0 ? " + encargos" : ""),$conteudo);

			#### ENCARGOS ####
			$conteudo = str_replace("{ds_encargo}",(floatval($ar_reg['vlr_encargo']) > 0 ? " + Encargos " : ""),$conteudo);
			$conteudo = str_replace("{fl_encargo}",(floatval($ar_reg['vlr_encargo']) > 0 ? "" : "display:none;"),$conteudo);
			
			$vl_minima = ($ar_reg['vl_minima'] < $ar_reg['vl_contribuicao'] ? $ar_reg['vl_contribuicao'] : $ar_reg['vl_minima']);
			$conteudo = str_replace("{vl_prev_mes_minima}",number_format($vl_minima,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_prev_mes_encargo}",number_format($ar_reg['vlr_encargo'],2,",","."),$conteudo);
			
			$vl_dif_contratada = (($ar_reg['vl_contribuicao'] >= $ar_reg['vl_contratada']) ? 0 : ($ar_reg['vl_contratada'] - $ar_reg['vl_minima']));
			$vl_dif_contratada = ($vl_dif_contratada < 0 ? 0 : $vl_dif_contratada);
			$conteudo = str_replace("{vl_prev_mes_contrada}",number_format($vl_dif_contratada,2,",","."),$conteudo);
								
			$vl_prev_mes_total = ($vl_minima + $ar_reg['vlr_encargo'] + $vl_dif_contratada);
								
			$conteudo = str_replace("{vl_prev_mes_total}",number_format($vl_prev_mes_total,2,",","."),$conteudo);
			
			$vl_prev_total = $vl_prev_mes_total;
			
			
			$vl_resumo_adm = 0;
			$vl_adm_minima = 0;
			if($fl_adm == "S")
			{
				#### BUSCA VALOR ADMINISTRATIVA DO MES ####
				
				#### 04/04/2019 TAXA DE CARREGAMENTO ZERADA ####
				$qr_sql = "
							SELECT oracle.fnc_retorna_custo_adm_instit((SELECT pa.cd_empresa
																		  FROM public.participantes pa
																		 WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')::INTEGER, 
										                                ".(trim($dt_compentencia) != "" ? "'".$dt_compentencia."'" : "TO_CHAR(CURRENT_DATE,'DD/MM/YYYY')").", 
																		".($vl_prev_mes_total - floatval($ar_reg['vlr_encargo'])).") AS vl_resumo_adm,
							       oracle.fnc_retorna_custo_adm_instit((SELECT pa.cd_empresa
																		  FROM public.participantes pa
																		 WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')::INTEGER, 
																	   ".(trim($dt_compentencia) != "" ? "'".$dt_compentencia."'" : "TO_CHAR(CURRENT_DATE,'DD/MM/YYYY')").", 
																	   ".$vl_minima.") AS vl_adm_minima
						  ";				
				$ob_resul = pg_query($db,$qr_sql);
				#echo "<PRE>$qr_sql</PRE>"; exit;
				$ar_reg_adm = pg_fetch_array($ob_resul);
				$vl_resumo_adm     = $ar_reg_adm['vl_resumo_adm'];
				$vl_adm_minima     = $ar_reg_adm['vl_adm_minima'];
				
				#### MONTA TABELA ADMINISTRATIVA ####
				$qr_sql = "
							SELECT faixa, 
								   faixa_limite_inferior, 
								   faixa_limite_superior, 
								   usar_limite_inferior, 
								   percentual
							  FROM custo_adm_instituidor_percent
							 WHERE cd_empresa = (SELECT pa.cd_empresa
												   FROM public.participantes pa
												  WHERE funcoes.cripto_re(pa.cd_empresa, pa.cd_registro_empregado, pa.seq_dependencia) = '".$_REQUEST['re']."')
							   AND dt_faixa   = DATE_TRUNC('month', ".(trim($dt_compentencia) != "" ? "TO_DATE('".$dt_compentencia."','DD/MM/YYYY')" : "CURRENT_DATE").")
							 ORDER BY faixa DESC
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
					

					if($nr_conta == 1)
					{
						$if_adm.= "
									if (vl_contribuicao >= ".intval($ar_reg_adm['faixa_limite_inferior']).")
									{
										vl_adm = (".intval($ar_reg_adm['faixa_limite_inferior'])." * (".floatval($ar_reg_adm['percentual'])." / 100));
										vl_contrib_prev = vl_contribuicao - vl_adm;
									}";						
					}		
					else
					{
					$if_adm.= "
								else if (
											(vl_contribuicao >= (".floatval($ar_reg_adm['faixa_limite_inferior'])." + ((".floatval($ar_reg_adm['faixa_limite_inferior'])." * ".floatval($ar_reg_adm['percentual']).") / 100)))
											&&
											(vl_contribuicao <= (".floatval($ar_reg_adm['faixa_limite_superior'])." + ((".floatval($ar_reg_adm['faixa_limite_superior'])." * ".floatval($ar_reg_adm['percentual']).") / 100)))
									   )					
								{
									
									vl_contrib_prev = ((vl_contribuicao / (".floatval($ar_reg_adm['percentual'])." + 100)) * 100);
									vl_adm = (vl_contrib_prev * (".floatval($ar_reg_adm['percentual'])." / 100));
								}
							  ";
					}					

					$nr_conta++;
				}

			}
			
			
			#### ADMINISTRATIVA ####
			$conteudo = str_replace("{cd_tipo_pagamento_adm}",($fl_adm == "S" ? 1 : 2),$conteudo);
			$conteudo = str_replace("{ds_adm}",($fl_adm == "S" ? " + Contribuição Administrativa " : ""),$conteudo);
			$conteudo = str_replace("{fl_msg_adm}",($fl_adm == "S" ? "display:none;" : ""),$conteudo);
			$conteudo = str_replace("{fl_valor_adm}",($fl_adm == "S" ? "" : "display:none;"),$conteudo);
			$conteudo = str_replace("{tab_adm}",$tab_adm,$conteudo);
			$conteudo = str_replace("{if_adm}",$if_adm,$conteudo);
			
			### PREVIDENCIARIA ###
			$conteudo = str_replace("{vl_prev_total}",number_format($vl_prev_total,2,",","."),$conteudo);

			#### RESUMO ###
			$conteudo = str_replace("{vl_resumo_prev}",number_format($vl_prev_total,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_resumo_adm}",number_format($vl_resumo_adm,2,",","."),$conteudo);
			$conteudo = str_replace("{vl_resumo_correio}",number_format($vl_resumo_correio,2,",","."),$conteudo);
			
			
			$conteudo = str_replace("{vl_adm_minima}",$vl_adm_minima,$conteudo);
			$conteudo = str_replace("{vl_total_geral}",number_format(($vl_prev_total + $vl_resumo_adm + $vl_resumo_correio),2,",","."),$conteudo);
			$conteudo = str_replace("{vl_total_pagar}",number_format(($vl_prev_total + $vl_resumo_adm + $vl_resumo_correio),2,",","."),$conteudo);
								
			#### GARANTE ZERAR CAMPOS ####
			$conteudo = str_replace("{vl_prev_mes_total}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_mes_minima}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_mes_contrada}","0,00",$conteudo);
			$conteudo = str_replace("{vl_prev_mes_encargo}","0,00",$conteudo);	
				
		}		
	}	

	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
?>