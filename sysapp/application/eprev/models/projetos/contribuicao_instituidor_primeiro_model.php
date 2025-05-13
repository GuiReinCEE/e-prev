<?php
class Contribuicao_instituidor_primeiro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function forma_pagamento(&$result, $args=array())
	{
		### GP - CADASTRO ###
		$qr_sql = "
					SELECT id_tipo_liquidacao AS forma_pagamento, 
					       descricao
                      FROM vw_liquidacoes_inscricao
		          ";

		#echo "<pre>$qr_sql</pre>";	
		$result = $this->db->query($qr_sql);
	}	

	function cadastro(&$result, $args=array())
	{
		### GP - CADASTRO ###
		$qr_sql = "
					SELECT TO_CHAR(c.dt_confirmacao, 'DD/MM/YYYY') AS dt_confirmacao,
						   c.usuario_confirmacao AS usuario_cadastro,
						   COALESCE(c.tot_internet_confirm,0) AS tot_internet_cadastro,
						   --COALESCE(c.tot_bdl_confirm,0) AS tot_bdl_cadastro,
						   COALESCE(c.tot_cheque_confirm,0) AS tot_cheque_cadastro,
						   COALESCE(c.tot_deposito_confirm,0) AS tot_deposito_cadastro,
						   COALESCE(c.tot_debito_cc_confirm,0) AS tot_debito_cc_cadastro,
						   COALESCE(c.vlr_cheque_confirm,0) AS vlr_cheque_cadastro,
						   COALESCE(c.vlr_deposito_confirm,0) AS vlr_deposito_cadastro,
						   COALESCE(c.vlr_debito_cc_confirm,0) AS vlr_debito_cc_cadastro,
						   COALESCE(c.tot_folha_confirm,0) AS tot_folha_cadastro,
						   COALESCE(c.vlr_folha_confirm,0) AS vlr_folha_cadastro,
						   COALESCE(c.tot_fol_ter_confirm,0) AS tot_fol_ter_cadastro,     
						   COALESCE(c.vlr_fol_ter_confirm,0) AS vlr_fol_ter_cadastro,
						   --ENVIO ANTECIPADO DO EMAIL PARA PAGAMENTO
						   CASE WHEN (SELECT COUNT(*) 
										FROM public.protocolos_participantes pp
										JOIN public.titulares t
										  ON t.cd_empresa            = pp.cd_empresa
										 AND t.cd_registro_empregado = pp.cd_registro_empregado
										 AND t.seq_dependencia       = pp.seq_dependencia
										JOIN public.controle_geracao_cobranca cge
										  ON cge.cd_empresa = pp.cd_empresa
										 AND cge.dt_confirmacao = pp.dt_confirma 
									   WHERE pp.forma_pagamento = 'BDL'
										 AND pp.cd_empresa       = c.cd_empresa
										 AND cge.cd_plano        = c.cd_plano 
										 AND cge.ano_competencia = c.ano_competencia
										 AND cge.mes_competencia = c.mes_competencia) = COALESCE(c.tot_bdl_confirm,0) 
								THEN
									COALESCE(c.tot_bdl_confirm,0) - (SELECT COUNT(*) 
																	   FROM public.protocolos_participantes pp
																	   JOIN public.titulares t
																		 ON t.cd_empresa            = pp.cd_empresa
																		AND t.cd_registro_empregado = pp.cd_registro_empregado
																		AND t.seq_dependencia       = pp.seq_dependencia
																	   JOIN public.controle_geracao_cobranca cge
																		 ON cge.cd_empresa = pp.cd_empresa
																		AND cge.dt_confirmacao = pp.dt_confirma 
																	  WHERE pp.forma_pagamento = 'BDL'
																		AND pp.cd_empresa       = c.cd_empresa
																		AND cge.cd_plano        = c.cd_plano 
																		AND cge.ano_competencia = c.ano_competencia
																		AND cge.mes_competencia = c.mes_competencia   
																		AND t.dt_digita_ingresso < COALESCE(cge.dt_envio_internet, CURRENT_DATE))
								ELSE
									COALESCE(c.tot_bdl_confirm,0)
						   END AS tot_bdl_cadastro,
                           (SELECT COUNT(*) 
							 FROM public.protocolos_participantes pp
							 JOIN public.titulares t
							   ON t.cd_empresa            = pp.cd_empresa
							  AND t.cd_registro_empregado = pp.cd_registro_empregado
							  AND t.seq_dependencia       = pp.seq_dependencia
							 JOIN public.controle_geracao_cobranca cge
							   ON cge.cd_empresa = pp.cd_empresa
							  AND cge.dt_confirmacao = pp.dt_confirma 
							WHERE pp.forma_pagamento = 'BDL'
							  AND pp.cd_empresa       = c.cd_empresa
							  AND cge.cd_plano        = c.cd_plano 
							  AND cge.ano_competencia = c.ano_competencia
							  AND cge.mes_competencia = c.mes_competencia   
							  AND t.dt_digita_ingresso < COALESCE(cge.dt_envio_internet, CURRENT_DATE)) AS tot_bdl_pg_antecipado_cadastro
					  FROM public.controle_geracao_cobranca c
					 WHERE cd_plano        = ".intval($args['cd_plano'])."
					   AND cd_empresa      = ".intval($args['cd_empresa'])."
					   AND mes_competencia = ".intval($args['nr_mes'])."
					   AND ano_competencia = ".intval($args['nr_ano'])."
					   AND dt_confirmacao  IS NOT NULL
		          ";

		#echo "<pre>cadastro<br>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}
	
	function geracao(&$result, $args=array())
	{
		### GP - RECEITA ###
		$qr_sql = "
					SELECT TO_CHAR(c.dt_geracao,'DD/MM/YYYY') AS dt_geracao,
						   c.usuario_geracao,
						   COALESCE(c.tot_internet_gerado,0) AS tot_internet_gerado,
						   --COALESCE(c.tot_bdl_gerado,0) AS tot_bdl_gerado,						   
						   COALESCE(c.tot_cheque_gerado,0) AS tot_cheque_gerado,
						   COALESCE(c.tot_deposito_gerado,0) AS tot_deposito_gerado,
						   COALESCE(c.tot_debito_cc_gerado,0) AS tot_debito_cc_gerado,
						   COALESCE(c.vlr_internet_gerado,0) AS vlr_internet_gerado,
						   COALESCE(c.vlr_bdl_gerado,0) AS vlr_bdl_gerado,
						   COALESCE(c.vlr_cheque_gerado,0) AS vlr_cheque_gerado,
						   COALESCE(c.vlr_deposito_gerado,0) AS vlr_deposito_gerado,
						   COALESCE(c.vlr_debito_cc_gerado,0) AS vlr_debito_cc_gerado,
						   COALESCE(c.tot_folha_gerado,0) AS tot_folha_gerado,
						   COALESCE(c.vlr_folha_gerado,0) AS vlr_folha_gerado,
					       COALESCE(c.tot_fol_ter_gerado,0) AS tot_fol_ter_gerado,      
					       COALESCE(c.vlr_fol_ter_gerado,0) AS vlr_fol_ter_gerado,
						   --ENVIO ANTECIPADO DO EMAIL PARA PAGAMENTO
						   CASE WHEN (SELECT COUNT(*) 
										FROM public.protocolos_participantes pp
										JOIN public.titulares t
										  ON t.cd_empresa            = pp.cd_empresa
										 AND t.cd_registro_empregado = pp.cd_registro_empregado
										 AND t.seq_dependencia       = pp.seq_dependencia
										JOIN public.controle_geracao_cobranca cge
										  ON cge.cd_empresa = pp.cd_empresa
										 AND cge.dt_confirmacao = pp.dt_confirma 
									   WHERE pp.forma_pagamento = 'BDL'
										 AND pp.cd_empresa       = c.cd_empresa
										 AND cge.cd_plano        = c.cd_plano 
										 AND cge.ano_competencia = c.ano_competencia
										 AND cge.mes_competencia = c.mes_competencia) = COALESCE(c.tot_bdl_gerado,0) 
								THEN
									COALESCE(c.tot_bdl_gerado,0) - (SELECT COUNT(*) 
																	   FROM public.protocolos_participantes pp
																	   JOIN public.titulares t
																		 ON t.cd_empresa            = pp.cd_empresa
																		AND t.cd_registro_empregado = pp.cd_registro_empregado
																		AND t.seq_dependencia       = pp.seq_dependencia
																	   JOIN public.controle_geracao_cobranca cge
																		 ON cge.cd_empresa = pp.cd_empresa
																		AND cge.dt_confirmacao = pp.dt_confirma 
																	  WHERE pp.forma_pagamento = 'BDL'
																		AND pp.cd_empresa       = c.cd_empresa
																		AND cge.cd_plano        = c.cd_plano 
																		AND cge.ano_competencia = c.ano_competencia
																		AND cge.mes_competencia = c.mes_competencia   
																		AND t.dt_digita_ingresso < COALESCE(cge.dt_envio_internet, CURRENT_DATE))
								ELSE
									COALESCE(c.tot_bdl_gerado,0)
						   END AS tot_bdl_gerado,
                           (SELECT COUNT(*) 
							 FROM public.protocolos_participantes pp
							 JOIN public.titulares t
							   ON t.cd_empresa            = pp.cd_empresa
							  AND t.cd_registro_empregado = pp.cd_registro_empregado
							  AND t.seq_dependencia       = pp.seq_dependencia
							 JOIN public.controle_geracao_cobranca cge
							   ON cge.cd_empresa = pp.cd_empresa
							  AND cge.dt_confirmacao = pp.dt_confirma 
							WHERE pp.forma_pagamento = 'BDL'
							  AND pp.cd_empresa       = c.cd_empresa
							  AND cge.cd_plano        = c.cd_plano 
							  AND cge.ano_competencia = c.ano_competencia
							  AND cge.mes_competencia = c.mes_competencia   
							  AND t.dt_digita_ingresso < COALESCE(cge.dt_envio_internet, CURRENT_DATE)) AS tot_bdl_pg_antecipado_gerado						   
					  FROM public.controle_geracao_cobranca c
					 WHERE c.cd_plano        = ".intval($args['cd_plano'])."
					   AND c.cd_empresa      = ".intval($args['cd_empresa'])."
					   AND c.mes_competencia = ".intval($args['nr_mes'])."
					   AND c.ano_competencia = ".intval($args['nr_ano'])."
					   AND c.dt_geracao      IS NOT NULL
		          ";

		#echo "<pre>geracao<br>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}	
	
	function financeiro(&$result, $args=array())
	{
		#### GFC ####

		#### SENGE - BUSCA NA TABELA INSCRITOS INTERNET ####
		#01/08/2013 - NOVA ESTRUTURA DE INSCRICAO SENGE
		if((intval($args['cd_empresa']) == 7) and (strtotime(intval($args['nr_ano'])."-".str_pad(trim($args['nr_mes']), 2, "0", STR_PAD_LEFT)."-01") <= strtotime('2013-07-31')))
		{
			$qr_sql = "
						SELECT COUNT(*) AS qt_total, 
							   COALESCE(SUM(COALESCE(t.vlr_taxa,0)),0) AS vl_total
						  FROM public.inscritos_internet ii,
							   public.controle_geracao_cobranca cgc,
							   public.taxas t,
							   public.pacotes p
						 WHERE ii.dt_geracao_primeira_cobr IS NOT NULL
						   AND ii.dt_envio_primeira_cobr   IS NULL
		                   AND ii.dt_primeiro_pgto         IS NULL	
						   AND ii.cd_pacote                = 1
						   AND ii.cd_plano                 = ".intval($args['cd_plano'])."
						   AND ii.cd_empresa               = ".intval($args['cd_empresa'])."
						   AND cgc.cd_plano                = ii.cd_plano
						   AND cgc.cd_empresa              = ii.cd_empresa
						   AND cgc.mes_competencia         = ".intval($args['nr_mes'])."
						   AND cgc.ano_competencia         = ".intval($args['nr_ano'])."
						   AND cgc.dt_geracao              IS NOT NULL
						   AND t.cd_indexador              = 42 
						   AND t.dt_taxa                   = DATE_TRUNC('month',CURRENT_DATE)
						   AND p.cd_pacote                 = ii.cd_pacote
						   AND p.cd_plano                  = ii.cd_plano
						   AND p.cd_empresa                = ii.cd_empresa
						   AND p.tipo_cobranca             = 'I'
						   AND p.dt_inicio                 = DATE_TRUNC('month',CURRENT_DATE)	
						   AND 'BDL'                       = '".$args['forma_pagamento']."'
						   ".(trim($args['fl_email']) == "S" ? "AND COALESCE(ii.email,'') LIKE '%@%'" : "")."
					  ";
		}
		else
		{
			$qr_sql = "
						SELECT COUNT(*) AS qt_total, 
							   COALESCE(SUM(COALESCE(cpr.valor,0) + COALESCE(riscos.valor_risco,0) + COALESCE(dcc.vlr_debito,0)),0) AS vl_total
						  FROM public.protocolos_participantes p
						  LEFT JOIN (SELECT ap.cd_empresa, 
											ap.cd_registro_empregado, 
											ap.seq_dependencia, 
											SUM(ap.premio) AS valor_risco
									   FROM public.apolices_participantes ap
									   JOIN public.apolices a
										 ON a.cd_apolice   = ap.cd_apolice
									  WHERE ap.dt_exclusao IS NULL
									  GROUP BY ap.cd_empresa, 
											   ap.cd_registro_empregado, 
											   ap.seq_dependencia) AS riscos 
							ON riscos.cd_empresa            = p.cd_empresa
						   AND riscos.cd_registro_empregado = p.cd_registro_empregado
						   AND riscos.seq_dependencia       = p.seq_dependencia
						 
						  JOIN public.calendarios_planos cp
							ON cp.cd_empresa = p.cd_empresa
						 
						  JOIN public.controle_geracao_cobranca cgc
							ON cgc.cd_empresa = cp.cd_empresa
						   AND cgc.cd_plano   = cp.cd_plano
						   
						  JOIN public.participantes pr
							ON pr.cd_empresa            = p.cd_empresa
						   AND pr.cd_registro_empregado = p.cd_registro_empregado
						   AND pr.seq_dependencia       = p.seq_dependencia					   
						 
						  LEFT JOIN public.contribuicoes_programadas cpr
							ON cpr.cd_empresa            = p.cd_empresa
						   AND cpr.cd_registro_empregado = p.cd_registro_empregado
						   AND cpr.seq_dependencia       = p.seq_dependencia
						   AND cpr.dt_confirma_opcao     IS NOT NULL
						   AND cpr.dt_confirma_canc      IS NULL
						 
						  LEFT JOIN public.debito_conta_contribuicao dcc
							ON dcc.cd_empresa            = p.cd_empresa
						   AND dcc.cd_registro_empregado = p.cd_registro_empregado
						   AND dcc.seq_dependencia       = p.seq_dependencia
						   AND dcc.dt_confirma_opcao     IS NOT NULL
						   AND dcc.dt_confirma_canc      IS NULL
						   
						  LEFT JOIN public.titulares_planos tp
							ON tp.cd_empresa            = p.cd_empresa
						   AND tp.cd_registro_empregado = p.cd_registro_empregado
						   AND tp.seq_dependencia       = p.seq_dependencia
						   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
															 FROM titulares_planos tp1 
															WHERE tp1.cd_empresa            = p.cd_empresa 
															  AND tp1.cd_registro_empregado = p.cd_registro_empregado 
															  AND tp1.seq_dependencia       = p.seq_dependencia)
														  
						 WHERE cp.cd_empresa       = ".intval($args['cd_empresa'])."
						   AND cp.cd_plano         = ".intval($args['cd_plano'])."
						   AND cp.dt_competencia   = TO_DATE('".intval($args['nr_ano'])."-".intval($args['nr_mes'])."-01','YYYY-MM-DD')
						   AND cgc.mes_competencia = ".intval($args['nr_mes'])."
						   AND cgc.ano_competencia = ".intval($args['nr_ano'])."
						   AND p.forma_pagamento   = '".$args['forma_pagamento']."'
						   ".(trim($args['fl_email']) == "S" ? "AND (COALESCE(pr.email,'') LIKE '%@%' OR COALESCE(pr.email_profissional,'') LIKE '%@%')" : "")."
						   AND cgc.dt_geracao      IS NOT NULL
						   AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
						   AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL 
										THEN 0 
										ELSE 1 
								   END)
						   
						   -- OPCAO CORREIO	   
					       AND CASE WHEN p.forma_pagamento = 'BDL' 
						            THEN oracle.fnc_retorna_op_cust_correio((CASE WHEN p.cd_empresa = 7  THEN 7
					       											              WHEN p.cd_empresa = 8  THEN 8
					       											              WHEN p.cd_empresa = 10 THEN 8
					       											              WHEN p.cd_empresa = 19 THEN 9
					       											              WHEN p.cd_empresa = 20 THEN 9
					       											              ELSE NULL END), 
																            p.cd_empresa::INTEGER, 
																            p.cd_registro_empregado::INTEGER, 
																            p.seq_dependencia::INTEGER) = 'I' 
									ELSE 1 = 1
                               END									
					  ";
		}
		
		#echo "<BR>##############<BR><pre style='text-align:left;'>financeiro<BR>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}

	function sem_email(&$result, $args=array())
	{
		#### SENGE - BUSCA NA TABELA INSCRITOS INTERNET ####
		#01/08/2013 - NOVA ESTRUTURA DE INSCRICAO SENGE
		if((intval($args['cd_empresa']) == 7) and (strtotime(intval($args['nr_ano'])."-".str_pad(trim($args['nr_mes']), 2, "0", STR_PAD_LEFT)."-01") <= strtotime('2013-07-31')))
		{
			$qr_sql = "
						SELECT ii.cd_empresa,
						       ii.cd_registro_empregado,
						       ii.seq_dependencia,
						       ii.nome,
                               'BDL' AS forma_pagamento
						  FROM public.inscritos_internet ii,
							   public.controle_geracao_cobranca cgc,
							   public.taxas t,
							   public.pacotes p
						 WHERE ii.dt_geracao_primeira_cobr IS NOT NULL
						   AND ii.dt_envio_primeira_cobr   IS NULL
		                   AND ii.dt_primeiro_pgto         IS NULL	
						   AND ii.cd_pacote                = 1
						   AND ii.cd_plano                 = ".intval($args['cd_plano'])."
						   AND ii.cd_empresa               = ".intval($args['cd_empresa'])."
						   AND cgc.cd_plano                = ii.cd_plano
						   AND cgc.cd_empresa              = ii.cd_empresa
						   AND cgc.mes_competencia         = ".intval($args['nr_mes'])."
						   AND cgc.ano_competencia         = ".intval($args['nr_ano'])."
						   AND cgc.dt_geracao              IS NOT NULL
						   AND t.cd_indexador              = 42 
						   AND t.dt_taxa                   = DATE_TRUNC('month',CURRENT_DATE)
						   AND p.cd_pacote                 = ii.cd_pacote
						   AND p.cd_plano                  = ii.cd_plano
						   AND p.cd_empresa                = ii.cd_empresa
						   AND p.tipo_cobranca             = 'I'
						   AND p.dt_inicio                 = DATE_TRUNC('month',CURRENT_DATE)	
						   ".($args['forma_pagamento'] != "" ? "AND 'BDL' = '".$args['forma_pagamento']."'" : "")."
						   AND COALESCE(ii.email,'') NOT LIKE '%@%'
					  ";
		}
		else
		{
			$qr_sql = "
						SELECT pr.cd_empresa,
						       pr.cd_registro_empregado,
						       pr.seq_dependencia,
						       pr.nome,
                               p.forma_pagamento

						  FROM public.protocolos_participantes p
						  LEFT JOIN (SELECT ap.cd_empresa, 
											ap.cd_registro_empregado, 
											ap.seq_dependencia, 
											SUM(ap.premio) AS valor_risco
									   FROM public.apolices_participantes ap
									   JOIN public.apolices a
										 ON a.cd_apolice   = ap.cd_apolice
									  WHERE ap.dt_exclusao IS NULL
									  GROUP BY ap.cd_empresa, 
											   ap.cd_registro_empregado, 
											   ap.seq_dependencia) AS riscos 
							ON riscos.cd_empresa            = p.cd_empresa
						   AND riscos.cd_registro_empregado = p.cd_registro_empregado
						   AND riscos.seq_dependencia       = p.seq_dependencia
						 
						  JOIN public.calendarios_planos cp
							ON cp.cd_empresa = p.cd_empresa
						 
						  JOIN public.controle_geracao_cobranca cgc
							ON cgc.cd_empresa = cp.cd_empresa
						   AND cgc.cd_plano   = cp.cd_plano
						   
						  JOIN public.participantes pr
							ON pr.cd_empresa            = p.cd_empresa
						   AND pr.cd_registro_empregado = p.cd_registro_empregado
						   AND pr.seq_dependencia       = p.seq_dependencia					   
						 
						  LEFT JOIN public.contribuicoes_programadas cpr
							ON cpr.cd_empresa            = p.cd_empresa
						   AND cpr.cd_registro_empregado = p.cd_registro_empregado
						   AND cpr.seq_dependencia       = p.seq_dependencia
						   AND cpr.dt_confirma_opcao     IS NOT NULL
						   AND cpr.dt_confirma_canc      IS NULL
						 
						  LEFT JOIN public.debito_conta_contribuicao dcc
							ON dcc.cd_empresa            = p.cd_empresa
						   AND dcc.cd_registro_empregado = p.cd_registro_empregado
						   AND dcc.seq_dependencia       = p.seq_dependencia
						   AND dcc.dt_confirma_opcao     IS NOT NULL
						   AND dcc.dt_confirma_canc      IS NULL
						   
						  LEFT JOIN public.titulares_planos tp
							ON tp.cd_empresa            = p.cd_empresa
						   AND tp.cd_registro_empregado = p.cd_registro_empregado
						   AND tp.seq_dependencia       = p.seq_dependencia
						   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
															 FROM titulares_planos tp1 
															WHERE tp1.cd_empresa            = p.cd_empresa 
															  AND tp1.cd_registro_empregado = p.cd_registro_empregado 
															  AND tp1.seq_dependencia       = p.seq_dependencia)
														  
						 WHERE cp.cd_empresa       = ".intval($args['cd_empresa'])."
						   AND cp.cd_plano         = ".intval($args['cd_plano'])."
						   AND cp.dt_competencia   = TO_DATE('".intval($args['nr_ano'])."-".intval($args['nr_mes'])."-01','YYYY-MM-DD')
						   AND cgc.mes_competencia = ".intval($args['nr_mes'])."
						   AND cgc.ano_competencia = ".intval($args['nr_ano'])."
						   ".($args['forma_pagamento'] != "" ? "AND p.forma_pagamento   = '".$args['forma_pagamento']."'" : "")."
						   AND NOT (COALESCE(pr.email,'') LIKE '%@%' OR COALESCE(pr.email_profissional,'') LIKE '%@%')
						   AND cgc.dt_geracao      IS NOT NULL
						   AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
						   AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL 
										THEN 0 
										ELSE 1 
								   END)
						   
						   -- OPCAO CORREIO	   
					       AND CASE WHEN p.forma_pagamento = 'BDL' 
						            THEN oracle.fnc_retorna_op_cust_correio((CASE WHEN p.cd_empresa = 7  THEN 7
					       											              WHEN p.cd_empresa = 8  THEN 8
					       											              WHEN p.cd_empresa = 10 THEN 8
					       											              WHEN p.cd_empresa = 19 THEN 9
					       											              WHEN p.cd_empresa = 20 THEN 9
					       											              ELSE NULL END), 
																             p.cd_empresa::INTEGER, 
																             p.cd_registro_empregado::INTEGER, 
																             p.seq_dependencia::INTEGER) = 'I' 
									ELSE 1 = 1
                               END	
	
					  ";
		}
		
		#echo "<BR>##############<BR><pre style='text-align:left;'>sem_email<BR>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}

	function gerar(&$result, $args=array())
	{
		if( (trim($args['cd_empresa']) != "") and (intval($args['cd_plano']) > 0) and (intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0))
		{
			#### SENGE - BUSCA NA TABELA INSCRITOS INTERNET ####
			#01/08/2013 - NOVA ESTRUTURA DE INSCRICAO SENGE
			if((intval($args['cd_empresa']) == 7) and (strtotime(intval($args['nr_ano'])."-".str_pad(trim($args['nr_mes']), 2, "0", STR_PAD_LEFT)."-01") <= strtotime('2013-07-31')))
			{		
				$qr_sql = " 
							INSERT INTO projetos.contribuicao_controle
								 (
								   cd_empresa, 
								   cd_registro_empregado, 
								   seq_dependencia, 
								   nr_mes_competencia, 
								   nr_ano_competencia, 
								   cd_contribuicao_controle_tipo, 
								   cd_usuario
								 )
								(SELECT ii.cd_empresa,
									   ii.cd_registro_empregado,
									   ii.seq_dependencia,
									   cgc.mes_competencia,
									   cgc.ano_competencia,
									   '1PBDL',
									   ".intval($args['cd_usuario'])."
								  FROM public.inscritos_internet ii,
									   public.controle_geracao_cobranca cgc,
									   public.taxas t,
									   public.pacotes p
								 WHERE ii.dt_geracao_primeira_cobr IS NOT NULL
								   AND ii.dt_envio_primeira_cobr   IS NULL
								   AND ii.dt_primeiro_pgto         IS NULL	 
								   AND ii.cd_pacote                = 1
								   AND ii.cd_plano                 = ".intval($args['cd_plano'])."
								   AND ii.cd_empresa               = ".intval($args['cd_empresa'])."
								   AND cgc.cd_plano                = ii.cd_plano
								   AND cgc.cd_empresa              = ii.cd_empresa
								   AND cgc.mes_competencia         = ".intval($args['nr_mes'])."
								   AND cgc.ano_competencia         = ".intval($args['nr_ano'])."
								   AND cgc.dt_geracao              IS NOT NULL
								   AND t.cd_indexador              = 42 
								   AND t.dt_taxa                   = DATE_TRUNC('month',CURRENT_DATE)
								   AND p.cd_pacote                 = ii.cd_pacote
								   AND p.cd_plano                  = ii.cd_plano
								   AND p.cd_empresa                = ii.cd_empresa
								   AND p.tipo_cobranca             = 'I'
								   AND p.dt_inicio                 = DATE_TRUNC('month',CURRENT_DATE)	
                                   AND COALESCE(ii.email,'') LIKE '%@%')								   
						  ";							 
			}
			else
			{
			
				$qr_sql = " 
							INSERT INTO projetos.contribuicao_controle
								 (
								   cd_empresa, 
								   cd_registro_empregado, 
								   seq_dependencia, 
								   nr_mes_competencia, 
								   nr_ano_competencia, 
								   cd_contribuicao_controle_tipo, 
								   cd_usuario
								 )
								 (SELECT p.cd_empresa,
										 p.cd_registro_empregado,
										 p.seq_dependencia,
										 cgc.mes_competencia,
										 cgc.ano_competencia,
										 CASE WHEN p.forma_pagamento = 'BDL' THEN '1PBDL'
											WHEN p.forma_pagamento = 'BCO' THEN '1PDCC'
											ELSE NULL
										 END,
										 ".intval($args['cd_usuario'])."
									FROM public.protocolos_participantes p
								  
									JOIN public.calendarios_planos cp
									  ON cp.cd_empresa = p.cd_empresa
								  
									JOIN public.controle_geracao_cobranca cgc
									  ON cgc.cd_empresa = cp.cd_empresa
									 AND cgc.cd_plano   = cp.cd_plano
									
									JOIN public.participantes pr
									  ON pr.cd_empresa            = p.cd_empresa
									 AND pr.cd_registro_empregado = p.cd_registro_empregado
									 AND pr.seq_dependencia       = p.seq_dependencia					   
								  
									LEFT JOIN public.contribuicoes_programadas cpr
									  ON cpr.cd_empresa            = p.cd_empresa
									 AND cpr.cd_registro_empregado = p.cd_registro_empregado
									 AND cpr.seq_dependencia       = p.seq_dependencia
									 AND cpr.dt_confirma_opcao     IS NOT NULL
									 AND cpr.dt_confirma_canc      IS NULL
								  
									LEFT JOIN public.debito_conta_contribuicao dcc
									  ON dcc.cd_empresa            = p.cd_empresa
									 AND dcc.cd_registro_empregado = p.cd_registro_empregado
									 AND dcc.seq_dependencia       = p.seq_dependencia
									 AND dcc.dt_confirma_opcao     IS NOT NULL
									 AND dcc.dt_confirma_canc      IS NULL
									
									LEFT JOIN public.titulares_planos tp
									  ON tp.cd_empresa            = p.cd_empresa
									 AND tp.cd_registro_empregado = p.cd_registro_empregado
									 AND tp.seq_dependencia       = p.seq_dependencia
									 AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
																	   FROM titulares_planos tp1 
																	  WHERE tp1.cd_empresa            = p.cd_empresa 
																		AND tp1.cd_registro_empregado = p.cd_registro_empregado 
																		AND tp1.seq_dependencia       = p.seq_dependencia)
																  
								   WHERE cp.cd_empresa       = ".intval($args['cd_empresa'])."
									 AND cp.cd_plano         = ".intval($args['cd_plano'])."
									 AND cp.dt_competencia   = TO_DATE('".intval($args['nr_ano'])."-".intval($args['nr_mes'])."-01','YYYY-MM-DD')
									 AND cgc.mes_competencia = ".intval($args['nr_mes'])."
									 AND cgc.ano_competencia = ".intval($args['nr_ano'])."
									 AND p.forma_pagamento   IN ('BDL','BCO')
									 AND (COALESCE(pr.email,'') LIKE '%@%' OR COALESCE(pr.email_profissional,'') LIKE '%@%')
									 AND cgc.dt_geracao      IS NOT NULL
									 AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
									 AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL 
												   THEN 0 
												   ELSE 1 
											  END)

							         -- OPCAO CORREIO	   
									   AND CASE WHEN p.forma_pagamento = 'BDL' 
												THEN oracle.fnc_retorna_op_cust_correio((CASE WHEN p.cd_empresa = 7  THEN 7
																					          WHEN p.cd_empresa = 8  THEN 8
																					          WHEN p.cd_empresa = 10 THEN 8
																					          WHEN p.cd_empresa = 19 THEN 9
																					          WHEN p.cd_empresa = 20 THEN 9
																					          ELSE NULL END), 
																			            p.cd_empresa::INTEGER, 
																			            p.cd_registro_empregado::INTEGER, 
																			            p.seq_dependencia::INTEGER) = 'I' 
												ELSE 1 = 1
										   END)							 
						  ";
			}
			
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$this->db->query($qr_sql);
		}
	}	
	
	function enviarEmail(&$result, $args=array())
	{
		if((trim($args['cd_empresa']) != "") and (intval($args['cd_plano']) > 0) and (intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0))
		{
			$qr_sql = " 
						SELECT email_contribuicao_primeiro_pagamento 
						  FROM rotinas.email_contribuicao_primeiro_pagamento(".intval($args['cd_plano']).", ".intval($args['cd_empresa']).", ".intval($args['nr_mes']).", ".intval($args['nr_ano']).", ".$args['cd_usuario'].");
					  ";	
					  
			#echo "<PRE>enviarEmail<BR>$qr_sql</PRE>"; exit;
			$this->db->query($qr_sql);
		}
	}	
	
	function enviarEmailCadastro(&$result, $args=array())
	{
		if(trim($args['lista']) != "")
		{
			$qr_sql = " 
					INSERT INTO projetos.envia_emails 
						 (
							dt_envio, 
							de, 
							para, 
							cc, 
							cco, 
							assunto, 
							texto
					     ) 
					VALUES 
						 (
							CURRENT_TIMESTAMP,
							'Contribuição Controle',
							'gapcadastro@eletroceee.com.br',     
							funcoes.get_usuario(".intval($args['cd_usuario']).") || '@eletroceee.com.br',     
							'coliveira@eletroceee.com.br',                      
							'Contribuição Primeiro Pagamento - Lista de Participante SEM EMAIL',                             
							'Contribuição Primeiro Pagamento - Lista de Participante SEM EMAIL:\n\n".trim($args['lista'])."'
					     );
					  ";	
					  
			#echo "<PRE>$qr_sql</PRE>"; exit;
			$this->db->query($qr_sql);
		}
	}		
	
	function financeiro_envio(&$result, $args=array())
	{
		$qr_sql = "
					SELECT usuario_envio_bdl AS usuario_envio,
						   TO_CHAR(dt_envio_bdl, 'DD/MM/YYYY') AS dt_envio_bdl,
						   COALESCE(tot_bdl_enviado,0) AS tot_bdl_enviado,
						   COALESCE(vlr_bdl_enviado,0) AS vlr_bdl_enviado,
						   usuario_envio_debito_cc,
						   TO_CHAR(dt_envio_debito_cc, 'DD/MM/YYYY') AS dt_envio_debito_cc,
						   COALESCE(tot_debito_cc_enviado,0) AS tot_debito_cc_enviado,
						   COALESCE(vlr_debito_cc_enviado,0) AS vlr_debito_cc_enviado,
						   
						   COALESCE(tot_cheque_gerado,0) AS tot_cheque_gerado,
						   COALESCE(tot_deposito_gerado,0) AS tot_deposito_gerado,
						   COALESCE(vlr_cheque_gerado,0) AS vlr_cheque_gerado,
						   COALESCE(vlr_deposito_gerado,0) AS vlr_deposito_gerado,
						   COALESCE(tot_folha_gerado,0) AS tot_folha_gerado,
						   COALESCE(vlr_folha_gerado,0) AS vlr_folha_gerado,	
					       COALESCE(tot_fol_ter_gerado,0) AS tot_fol_ter_gerado,      
					       COALESCE(vlr_fol_ter_gerado,0) AS vlr_fol_ter_gerado
 				      FROM public.controle_geracao_cobranca
					 WHERE cd_plano        = ".intval($args['cd_plano'])."
					   AND cd_empresa      = ".intval($args['cd_empresa'])."
					   AND mes_competencia = ".intval($args['nr_mes'])."
					   AND ano_competencia = ".intval($args['nr_ano'])."
					   AND (dt_envio_bdl IS NOT NULL OR dt_envio_debito_cc IS NOT NULL)
		          ";
				  
		#echo "<pre>financeiro_envio<BR>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}	
	
	function contribuicao_controle(&$result, $args=array())
	{
		#### SENGE - BUSCA NA TABELA INSCRITOS INTERNET ####
		#01/08/2013 - NOVA ESTRUTURA DE INSCRICAO SENGE
		if((intval($args['cd_empresa']) == 7) and (strtotime(intval($args['nr_ano'])."-".str_pad(trim($args['nr_mes']), 2, "0", STR_PAD_LEFT)."-01") <= strtotime('2013-07-31')))
		{		
			$qr_sql = "
						SELECT cc.cd_empresa, 
							   cc.cd_registro_empregado, 
							   cc.seq_dependencia, 
							   cc.nr_ano_competencia, 
							   cc.nr_mes_competencia, 
							   cc.cd_contribuicao_controle_tipo, 
							   TO_CHAR(cc.dt_controle,'DD/MM/YYYY HH24:MI:SS') AS dt_geracao,
							   cc.cd_usuario, 
							   cc.fl_email_enviado,
							   cct.cd_contribuicao_controle_tipo, 
							   cct.ds_contribuicao_controle_tipo,
							   p.nome
						  FROM projetos.contribuicao_controle cc
						  JOIN projetos.contribuicao_controle_tipo cct
							ON cct.cd_contribuicao_controle_tipo = cc.cd_contribuicao_controle_tipo
						  JOIN public.inscritos_internet p
							ON p.cd_empresa            = cc.cd_empresa
						   AND p.cd_registro_empregado = cc.cd_registro_empregado
						   AND p.seq_dependencia       = cc.seq_dependencia
						 WHERE cc.cd_empresa                    = ".intval($args['cd_empresa'])."
						   AND cc.nr_mes_competencia            = ".intval($args['nr_mes'])."
						   AND cc.nr_ano_competencia            = ".intval($args['nr_ano'])."
						   AND cc.cd_contribuicao_controle_tipo IN (".trim($args['cd_contribuicao_controle_tipo']).")
						   ".(trim($args['fl_email_enviado']) == "S" ? " AND cc.fl_email_enviado = 'S' " : "")."
					  ";		
		}
		else
		{
		
			$qr_sql = "
						SELECT cc.cd_empresa, 
							   cc.cd_registro_empregado, 
							   cc.seq_dependencia, 
							   cc.nr_ano_competencia, 
							   cc.nr_mes_competencia, 
							   cc.cd_contribuicao_controle_tipo, 
							   TO_CHAR(cc.dt_controle,'DD/MM/YYYY HH24:MI:SS') AS dt_geracao,
							   cc.cd_usuario, 
							   cc.fl_email_enviado,
							   cct.cd_contribuicao_controle_tipo, 
							   cct.ds_contribuicao_controle_tipo,
							   p.nome
						  FROM projetos.contribuicao_controle cc
						  JOIN projetos.contribuicao_controle_tipo cct
							ON cct.cd_contribuicao_controle_tipo = cc.cd_contribuicao_controle_tipo
						  JOIN public.participantes p
							ON p.cd_empresa            = cc.cd_empresa
						   AND p.cd_registro_empregado = cc.cd_registro_empregado
						   AND p.seq_dependencia       = cc.seq_dependencia
						 WHERE cc.cd_empresa                    = ".intval($args['cd_empresa'])."
						   AND cc.nr_mes_competencia            = ".intval($args['nr_mes'])."
						   AND cc.nr_ano_competencia            = ".intval($args['nr_ano'])."
						   AND cc.cd_contribuicao_controle_tipo IN (".trim($args['cd_contribuicao_controle_tipo']).")
						   ".(trim($args['fl_email_enviado']) == "S" ? " AND cc.fl_email_enviado = 'S' " : "")."
					  ";
		}

		#echo "<pre>$qr_sql</pre>";	
		$result = $this->db->query($qr_sql);
	}

	function relatorioListar(&$result, $args=array())
	{
		#### SENGE - BUSCA NA TABELA INSCRITOS INTERNET ####
		#01/08/2013 - NOVA ESTRUTURA DE INSCRICAO SENGE
		if((intval($args['cd_empresa']) == 7) and (strtotime(intval($args['nr_ano'])."-".str_pad(trim($args['nr_mes']), 2, "0", STR_PAD_LEFT)."-01") <= strtotime('2013-07-31')))
		{		
			$qr_sql = "
						SELECT e.cd_email,
							   TO_CHAR(e.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
							   TO_CHAR(e.dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado,
							   e.para,
							   e.cc,
							   e.cd_empresa,
							   e.cd_registro_empregado,
							   e.seq_dependencia,
							   p.nome,
							   e.assunto,
							   e.fl_retornou
						  FROM projetos.envia_emails e
						  JOIN public.inscritos_internet p
							ON p.cd_empresa            = e.cd_empresa
						   AND p.cd_registro_empregado = e.cd_registro_empregado
						   AND p.seq_dependencia       = e.seq_dependencia
						 WHERE e.cd_evento             = (CASE WHEN e.cd_empresa = 7  THEN 40
															   WHEN e.cd_empresa = 8  THEN 39
															   WHEN e.cd_empresa = 10 THEN 54
															   WHEN e.cd_empresa = 19 THEN 80
															   WHEN e.cd_empresa = 20 THEN 183
															   ELSE NULL END)
						   AND e.cd_empresa            = ".intval($args['cd_empresa'])."
						   AND DATE_TRUNC('month', e.dt_envio) = TO_DATE('".intval($args['nr_ano'])."-".intval($args['nr_mes'])."-01','YYYY-MM-DD')
						   ".(intval($args['cd_registro_empregado']) > 0 ? "AND e.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
						   ".(trim($args['seq_dependencia']) != "" ? "AND e.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
						   ".(trim($args["fl_retornou"]) != "" ? "AND e.fl_retornou = '".$args["fl_retornou"]."'" : "")."
					  ";
		
		}
		else
		{
		
			$qr_sql = "
						SELECT e.cd_email,
							   TO_CHAR(e.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
							   TO_CHAR(e.dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado,
							   e.para,
							   e.cc,
							   e.cd_empresa,
							   e.cd_registro_empregado,
							   e.seq_dependencia,
							   p.nome,
							   e.assunto,
							   e.fl_retornou
						  FROM projetos.envia_emails e
						  JOIN public.participantes p
							ON p.cd_empresa            = e.cd_empresa
						   AND p.cd_registro_empregado = e.cd_registro_empregado
						   AND p.seq_dependencia       = e.seq_dependencia
						 WHERE e.cd_evento             = (CASE WHEN e.cd_empresa = 7  THEN 40
															   WHEN e.cd_empresa = 8  THEN 39
															   WHEN e.cd_empresa = 10 THEN 54
															   WHEN e.cd_empresa = 19 THEN 80
															   WHEN e.cd_empresa = 20 THEN 183
															   ELSE NULL END)
						   AND e.cd_empresa            = ".intval($args['cd_empresa'])."
						   AND DATE_TRUNC('month', e.dt_envio) = TO_DATE('".intval($args['nr_ano'])."-".intval($args['nr_mes'])."-01','YYYY-MM-DD')
						   ".(intval($args['cd_registro_empregado']) > 0 ? "AND e.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
						   ".(trim($args['seq_dependencia']) != "" ? "AND e.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
						   ".(trim($args["fl_retornou"]) != "" ? "AND e.fl_retornou = '".$args["fl_retornou"]."'" : "")."
					  ";
		}
		
		#echo "<pre>$qr_sql</pre>";	
		$result = $this->db->query($qr_sql);
	}	
	
}
?>