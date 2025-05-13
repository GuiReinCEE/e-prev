<?php
class Contribuicao_instituidor_mensal_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function mensal(&$result, $args=array())
	{
		$qr_sql = "
					SELECT CASE WHEN b.cd_empresa IN (8,10) AND b.codigo_lancamento = 2450 THEN 'PMBDL'
								WHEN b.cd_empresa IN (19,20) AND b.codigo_lancamento = 2502 THEN 'PMBDL'
								WHEN b.cd_empresa = 7 AND b.codigo_lancamento = 2400 THEN 'PMBDL'
								ELSE 'ERRO'
					       END AS tp_pagamento,
						   COUNT(DISTINCT funcoes.cripto_re(b.cd_empresa, b.cd_registro_empregado, b.seq_dependencia)) AS qt_total
					  FROM public.bloqueto b
					  JOIN public.participantes p
					    ON p.cd_empresa            = b.cd_empresa 
					   AND p.cd_registro_empregado = b.cd_registro_empregado 
					   AND p.seq_dependencia       = b.seq_dependencia
					 WHERE b.status       IS NULL 
					   AND b.data_retorno IS NULL
					   AND b.cd_plano   = ".intval($args['cd_plano'])."
					   AND b.cd_empresa = ".intval($args['cd_empresa'])."
					   AND b.codigo_lancamento = ".$args['codigo_lancamento'][intval($args['cd_empresa'])]['PMBDL']."
					   AND b.ano_competencia = ".intval($args['nr_ano'])."
					   AND b.mes_competencia = ".intval($args['nr_mes'])."
					   --AND b.cd_registro_empregado != 10308
					   AND DATE_TRUNC('month', b.dt_lancamento) = TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY')
					   ".(trim($args['fl_email']) == "S" ? "AND (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%')" : "")."
				     GROUP BY tp_pagamento
					 
					UNION 
					 
					SELECT CASE WHEN c.cd_empresa IN (8,10) AND c.codigo_lancamento = 2460 THEN 'PMDCC'
					            WHEN c.cd_empresa IN (19,20) AND c.codigo_lancamento = 2501 THEN 'PMDCC'
					            WHEN c.cd_empresa = 7 AND c.codigo_lancamento = 2410 THEN 'PMDCC'
					       ELSE 'ERRO'
					       END AS tp_pagamento,
					       COUNT(DISTINCT funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia)) AS qt_total
					  FROM public.cobrancas c
					  JOIN public.participantes p 
					    ON p.cd_empresa            = c.cd_empresa
					   AND p.cd_registro_empregado = c.cd_registro_empregado
					   AND p.seq_dependencia       = c.seq_dependencia
					   AND p.cd_plano              = c.cd_plano
					 WHERE c.cd_plano   = ".intval($args['cd_plano'])."
					   AND c.cd_empresa = ".intval($args['cd_empresa'])."
					   AND c.codigo_lancamento = ".$args['codigo_lancamento'][intval($args['cd_empresa'])]['PMDCC']."
					   AND c.ano_competencia = ".intval($args['nr_ano'])."
					   AND c.mes_competencia = ".intval($args['nr_mes'])."
					   AND c.sit_registro    = 'I'
					   AND DATE_TRUNC('month', c.dt_lancamento) = TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY')
					   ".(trim($args['fl_email']) == "S" ? "AND (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%')" : "")."
					 GROUP BY tp_pagamento					 
					 
					";
		#echo "<pre style='text-align:left;'>mensal<BR>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}
	
	function sem_email(&$result, $args=array())
	{
		$qr_sql = "
					SELECT p.cd_empresa, 
					       p.cd_registro_empregado, 
					       p.seq_dependencia,
						   p.nome,
                           CASE WHEN b.cd_empresa IN (8,10) AND b.codigo_lancamento = 2450 THEN 'BDL'
								WHEN b.cd_empresa IN (19,20) AND b.codigo_lancamento = 2502 THEN 'BDL'
								WHEN b.cd_empresa = 7 AND b.codigo_lancamento = 2400 THEN 'BDL'
								ELSE 'ERRO'
					       END AS forma_pagamento,
						   oracle.fnc_retorna_opcao(p.cd_empresa::INTEGER, p.cd_registro_empregado::INTEGER, p.seq_dependencia::INTEGER,'WEB0001',CURRENT_DATE)::TEXT AS tp_opcao
					  FROM public.bloqueto b
					  JOIN public.participantes p
					    ON p.cd_empresa            = b.cd_empresa 
					   AND p.cd_registro_empregado = b.cd_registro_empregado 
					   AND p.seq_dependencia       = b.seq_dependencia
					 WHERE b.status       IS NULL 
					   AND b.data_retorno IS NULL
					   AND b.cd_plano   = ".intval($args['cd_plano'])."
					   AND b.cd_empresa = ".intval($args['cd_empresa'])."
					   AND b.codigo_lancamento = ".$args['codigo_lancamento'][intval($args['cd_empresa'])]['PMBDL']."
					   AND b.ano_competencia = ".intval($args['nr_ano'])."
					   AND b.mes_competencia = ".intval($args['nr_mes'])."
					   AND DATE_TRUNC('month', b.dt_lancamento) = TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY')
					   AND COALESCE(p.email,'') NOT LIKE '%@%' 
					   AND COALESCE(p.email_profissional,'') NOT LIKE '%@%'
					 
					UNION 
					 
					SELECT p.cd_empresa, 
					       p.cd_registro_empregado, 
					       p.seq_dependencia,
						   p.nome,
						   CASE WHEN c.cd_empresa IN (8,10) AND c.codigo_lancamento = 2460 THEN 'BCO'
					            WHEN c.cd_empresa IN (19,20) AND c.codigo_lancamento = 2501 THEN 'BCO'
					            WHEN c.cd_empresa = 7 AND c.codigo_lancamento = 2410 THEN 'BCO'
					            ELSE 'ERRO'
					       END AS forma_pagamento,
						   oracle.fnc_retorna_opcao(p.cd_empresa::INTEGER, p.cd_registro_empregado::INTEGER, p.seq_dependencia::INTEGER,'WEB0001',CURRENT_DATE)::TEXT AS tp_opcao
					  FROM public.cobrancas c
					  JOIN public.participantes p 
					    ON p.cd_empresa            = c.cd_empresa
					   AND p.cd_registro_empregado = c.cd_registro_empregado
					   AND p.seq_dependencia       = c.seq_dependencia
					   AND p.cd_plano              = c.cd_plano
					 WHERE c.cd_plano   = ".intval($args['cd_plano'])."
					   AND c.cd_empresa = ".intval($args['cd_empresa'])."
					   AND c.codigo_lancamento = ".$args['codigo_lancamento'][intval($args['cd_empresa'])]['PMDCC']."
					   AND c.ano_competencia = ".intval($args['nr_ano'])."
					   AND c.mes_competencia = ".intval($args['nr_mes'])."
					   AND DATE_TRUNC('month', c.dt_lancamento) = TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY')
					   AND COALESCE(p.email,'') NOT LIKE '%@%' 
					   AND COALESCE(p.email_profissional,'') NOT LIKE '%@%'
					";
		#echo "<pre style='text-align:left;'>sem_email<BR>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}	
	
	function mensal_anterior(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cc.cd_contribuicao_controle_tipo AS tp_pagamento,
                           COUNT(*) AS qt_total
					  FROM projetos.contribuicao_controle cc
					 WHERE cc.cd_empresa         = ".intval($args['cd_empresa'])."
					   AND TO_DATE('01/' || cc.nr_mes_competencia::TEXT || '/' || cc.nr_ano_competencia::TEXT,'DD/MM/YYYY') = (TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY') - '1 month'::INTERVAL)
					   AND cc.fl_email_enviado       = 'S'
					   AND cc.cd_contribuicao_controle_tipo IN (".implode(",",$args['cd_contribuicao_controle_tipo']).")
					 GROUP BY tp_pagamento
					 ORDER BY tp_pagamento
		          ";

		#echo "<pre style='text-align:left;'>mensal_anterior<BR>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}
	
	function contribuicao_controle(&$result, $args=array())
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
					   AND cc.cd_contribuicao_controle_tipo IN (".implode(",",$args['cd_contribuicao_controle_tipo']).")
					   ".(trim($args['fl_email_enviado']) == "S" ? " AND cc.fl_email_enviado = 'S' " : "")."
		          ";
			   

		#echo "<pre style='text-align:left;'>contribuicao_controle<BR>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}

	function mensal_cadastro(&$result, $args=array())
	{
		$qr_sql = "
					-- PARTICIPANTES - CADASTRO POR FORMA DE PAGAMENTO
					SELECT 'BDL' AS tp_pagamento, 
						   COUNT(*) AS qt_total,
                           SUM((SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END -- INSTITUTO - AGUARDANDO OPCAO
                                  FROM public.institutos_desligamentos i
						         WHERE i.cd_empresa            = p.cd_empresa
						           AND i.cd_registro_empregado = p.cd_registro_empregado
						           AND i.seq_dependencia       = p.seq_dependencia
						           AND i.tipo_calculo          = 1 
						           AND i.dt_encerramento       IS NULL)) AS qt_instituto						   
					  FROM public.participantes p
					  JOIN public.titulares_planos tp
						ON tp.cd_empresa            = p.cd_empresa
					   AND tp.cd_registro_empregado = p.cd_registro_empregado
					   AND tp.seq_dependencia       = p.seq_dependencia
					   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
							                             FROM public.titulares_planos tp1 
							                            WHERE tp1.cd_empresa            = p.cd_empresa 
							                              AND tp1.cd_registro_empregado = p.cd_registro_empregado 
							                              AND tp1.seq_dependencia       = p.seq_dependencia)
                      JOIN public.tipo_folhas tf
                        ON tf.tipo_folha = p.tipo_folha
                       AND tf.tipo_pagamento <> 'A'														  
					 WHERE p.cd_empresa = ".intval($args['cd_empresa'])."
					   AND p.cd_plano   = ".intval($args['cd_plano'])."
					   AND p.dt_obito   IS NULL /* obitos ocorridos depois da geração devem gerar inconsistência */
					   AND p.cd_registro_empregado != 10308
					   AND NOT EXISTS (SELECT 1 
										 FROM public.debito_conta_contribuicao dcc 
										WHERE dcc.cd_empresa            = p.cd_empresa
										  AND dcc.cd_registro_empregado = p.cd_registro_empregado
										  AND dcc.seq_dependencia       = p.seq_dependencia
										  ---AND dcc.dt_confirma_opcao     IS NOT NULL
										  AND (dcc.dt_confirma_opcao     IS NULL
											   OR
											   dcc.dt_confirma_opcao < (SELECT cgc.dt_geracao 
																          FROM controle_geracao_cobranca cgc
																         WHERE cgc.cd_empresa      = p.cd_empresa 
																	       AND cgc.cd_plano        = p.cd_plano
																	       AND cgc.mes_competencia = ".intval($args['nr_mes'])." 
																	       AND cgc.ano_competencia = ".intval($args['nr_ano'])."))										  
										  AND dcc.dt_entrega_formulario IS NOT NULL
										  AND (dcc.dt_confirma_canc     IS NULL
											  OR
											   dcc.dt_confirma_canc > (SELECT cgc.dt_geracao 
																         FROM controle_geracao_cobranca cgc
																        WHERE cgc.cd_empresa      = p.cd_empresa 
																	      AND cgc.cd_plano        = p.cd_plano
																	      AND cgc.mes_competencia = ".intval($args['nr_mes'])." 
																	      AND cgc.ano_competencia = ".intval($args['nr_ano'])."))
										  AND dcc.forma_pagamento       <> 'BDL')
					   AND NOT EXISTS (SELECT 1 
										 FROM public.cobrancas c
										WHERE c.cd_empresa            = p.cd_empresa
										  AND c.cd_registro_empregado = p.cd_registro_empregado
										  AND c.seq_dependencia       = p.seq_dependencia
										  AND c.sit_lancamento	  = 'P'
										  AND c.mes_competencia	  = ".intval($args['nr_mes'])."
										  AND c.ano_competencia	  = ".intval($args['nr_ano'])."
										  AND c.codigo_lancamento = ".$args['codigo_lancamento'][intval($args['cd_empresa'])]['PMBDL'].")
					   
					   -- INSTITUTO - AGUARDANDO OPCAO
					   AND 0 = (SELECT COUNT(*)
                                  FROM public.institutos_desligamentos i
								 WHERE i.cd_empresa            = p.cd_empresa
								   AND i.cd_registro_empregado = p.cd_registro_empregado
								   AND i.seq_dependencia       = p.seq_dependencia
								   AND i.tipo_calculo          = 1 
								   AND i.dt_encerramento       IS NULL)

					UNION 

					SELECT dcc.forma_pagamento AS tp_pagamento, 
						   COUNT(*) AS qt_total,
                           SUM((SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END -- INSTITUTO - AGUARDANDO OPCAO
                                  FROM public.institutos_desligamentos i
						         WHERE i.cd_empresa            = p.cd_empresa
						           AND i.cd_registro_empregado = p.cd_registro_empregado
						           AND i.seq_dependencia       = p.seq_dependencia
						           AND i.tipo_calculo          = 1 
						           AND i.dt_encerramento       IS NULL)) AS qt_instituto
					  FROM public.participantes p
					  JOIN public.titulares_planos tp
						ON tp.cd_empresa            = p.cd_empresa
					   AND tp.cd_registro_empregado = p.cd_registro_empregado
					   AND tp.seq_dependencia       = p.seq_dependencia
					   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
							                             FROM public.titulares_planos tp1 
							                            WHERE tp1.cd_empresa            = p.cd_empresa 
							                              AND tp1.cd_registro_empregado = p.cd_registro_empregado 
							                              AND tp1.seq_dependencia       = p.seq_dependencia)
                      JOIN public.tipo_folhas tf
                        ON tf.tipo_folha = p.tipo_folha
                       AND tf.tipo_pagamento <> 'A'															  
					  JOIN public.debito_conta_contribuicao dcc 
						ON dcc.cd_empresa            = p.cd_empresa
					   AND dcc.cd_registro_empregado = p.cd_registro_empregado
					   AND dcc.seq_dependencia       = p.seq_dependencia
					 WHERE p.cd_empresa          = ".intval($args['cd_empresa'])."
					   AND p.cd_plano            = ".intval($args['cd_plano'])."
					   AND p.dt_obito            IS NULL /* obitos ocorridos depois da geração devem gerar inconsistência */
					   AND dcc.dt_confirma_opcao IS NOT NULL
					   AND dcc.dt_confirma_canc  IS NULL
					   AND dcc.forma_pagamento   <> 'BDL'
					   AND tp.dt_ingresso_plano  <> TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY')
					   
					   
					   
					   
					 GROUP BY dcc.forma_pagamento

					 ORDER BY tp_pagamento	
			";
		#echo "<pre style='text-align:left;'>mensal_cadastro<BR>$qr_sql</pre>";exit;
		$result = $this->db->query($qr_sql);			
	}
	
	function gerar(&$result, $args=array())
	{
		if( (trim($args['cd_empresa']) != "") and (intval($args['cd_plano']) > 0) and (intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0))
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
						     	     ".intval($args['nr_mes']).",
						     	     ".intval($args['nr_ano']).",
						     	     t.tp_pagamento,
						     	     ".intval($args['cd_usuario'])."
                                FROM public.participantes p
					            JOIN (
								
										SELECT DISTINCT (funcoes.cripto_re(b.cd_empresa, b.cd_registro_empregado, b.seq_dependencia)) AS re,
											   CASE WHEN b.cd_empresa IN (8,10) AND b.codigo_lancamento = 2450 THEN 'PMBDL'
													WHEN b.cd_empresa IN (19,20) AND b.codigo_lancamento = 2502 THEN 'PMBDL'
													WHEN b.cd_empresa = 7 AND b.codigo_lancamento = 2400 THEN 'PMBDL'
													ELSE 'ERRO'
											   END AS tp_pagamento
										  FROM public.bloqueto b
										  JOIN public.participantes p
											ON p.cd_empresa            = b.cd_empresa 
										   AND p.cd_registro_empregado = b.cd_registro_empregado 
										   AND p.seq_dependencia       = b.seq_dependencia
										 WHERE b.status       IS NULL 
										   AND b.data_retorno IS NULL
										   AND b.cd_plano   = ".intval($args['cd_plano'])."
										   AND b.cd_empresa = ".intval($args['cd_empresa'])."
										   AND b.codigo_lancamento = ".$args['codigo_lancamento'][intval($args['cd_empresa'])]['PMBDL']."
										   AND b.ano_competencia = ".intval($args['nr_ano'])."
										   AND b.mes_competencia = ".intval($args['nr_mes'])."
										   AND DATE_TRUNC('month', b.dt_lancamento) = TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY')
										 
										UNION 
										 
										SELECT DISTINCT (funcoes.cripto_re(c.cd_empresa, c.cd_registro_empregado, c.seq_dependencia)) AS re,
											   CASE WHEN c.cd_empresa IN (8,10) AND c.codigo_lancamento = 2460 THEN 'PMDCC'
													WHEN c.cd_empresa IN (19,20) AND c.codigo_lancamento = 2501 THEN 'PMDCC'
													WHEN c.cd_empresa = 7 AND c.codigo_lancamento = 2410 THEN 'PMDCC'
											   ELSE 'ERRO'
											   END AS tp_pagamento
										  FROM public.cobrancas c
										  JOIN public.participantes p 
											ON p.cd_empresa            = c.cd_empresa
										   AND p.cd_registro_empregado = c.cd_registro_empregado
										   AND p.seq_dependencia       = c.seq_dependencia
										   AND p.cd_plano              = c.cd_plano
										 WHERE c.cd_plano   = ".intval($args['cd_plano'])."
										   AND c.cd_empresa = ".intval($args['cd_empresa'])."
										   AND c.codigo_lancamento = ".$args['codigo_lancamento'][intval($args['cd_empresa'])]['PMDCC']."
										   AND c.ano_competencia = ".intval($args['nr_ano'])."
										   AND c.mes_competencia = ".intval($args['nr_mes'])."
										   AND DATE_TRUNC('month', c.dt_lancamento) = TO_DATE('01/".intval($args['nr_mes'])."/".intval($args['nr_ano'])."','DD/MM/YYYY')
								
								     )t
   								  ON t.re = funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia)
								 AND t.tp_pagamento <> 'ERRO')									 
							 
						        					 
					  ";			
			#echo "<pre style='text-align:left;'>gerar<br>$qr_sql</pre>";	exit;
			$this->db->query($qr_sql);
		}
	}	
	
	function enviarEmail(&$result, $args=array())
	{
		if((trim($args['cd_empresa']) != "") and (intval($args['cd_plano']) > 0) and (intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0))
		{
			$qr_sql = " 
						SELECT email_contribuicao_mensal 
						  FROM rotinas.email_contribuicao_mensal(".intval($args['cd_plano']).", ".intval($args['cd_empresa']).", ".intval($args['nr_mes']).", ".intval($args['nr_ano']).", ".$args['cd_usuario'].");
					  ";			
			#echo "<PRE>enviarEmail<br>$qr_sql</PRE>"; exit;
			
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
							'Contribuição Mensal - Lista de Participante SEM EMAIL',                             
							'Contribuição Mensal - Lista de Participante SEM EMAIL:\n\n".trim($args['lista'])."'
					     );
					  ";			
			#echo "<PRE>$qr_sql</PRE>"; exit;
			
			$this->db->query($qr_sql);
		}
	}	
	
	function relatorioListar(&$result, $args=array())
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
			         WHERE e.cd_evento             = (CASE WHEN e.cd_empresa = 7  THEN 91
														   WHEN e.cd_empresa = 8  THEN 85
														   WHEN e.cd_empresa = 10 THEN 88
														   WHEN e.cd_empresa = 19 THEN 81
														   WHEN e.cd_empresa = 20 THEN 184
														   ELSE NULL END)
                       AND e.cd_empresa            = ".intval($args['cd_empresa'])."
			           AND DATE_TRUNC('month', e.dt_envio) = TO_DATE('".intval($args['nr_ano'])."-".intval($args['nr_mes'])."-01','YYYY-MM-DD')
					   ".(intval($args['cd_registro_empregado']) > 0 ? "AND e.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
					   ".(trim($args['seq_dependencia']) != "" ? "AND e.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
					   ".(trim($args["fl_retornou"]) != "" ? "AND e.fl_retornou = '".$args["fl_retornou"]."'" : "")."
		          ";
		#echo "<pre style='text-align:left;'>relatorioListar<br>$qr_sql</pre>";	
		$result = $this->db->query($qr_sql);
	}	
}
?>