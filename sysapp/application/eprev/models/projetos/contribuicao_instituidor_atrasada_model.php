<?php
class Contribuicao_instituidor_atrasada_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function atrasada(&$result, $args=array())
    {
        $qr_sql = "
            SELECT CASE WHEN b.cd_empresa = 7
                        THEN CASE WHEN b.codigo_lancamento = 2400 THEN 'COBDL'
                        WHEN b.codigo_lancamento = 2410 THEN 'CODCC'
                        ELSE 'ERRO'
                     END
                        WHEN b.cd_empresa IN (8,10,12,19,20,24,25,26,27,28,29,30,31) 
                        THEN CASE WHEN b.codigo_lancamento = 2502 THEN 'COBDL'
                        WHEN b.codigo_lancamento = 2501 THEN 'CODCC'
                        WHEN b.codigo_lancamento = 2500 THEN 'COFOL'
                        WHEN b.codigo_lancamento IN (2503,2509) THEN 'COFLT'
                        ELSE 'ERRO'
                     END
                        ELSE 'ERRO'
                     END AS tp_pagamento,
                        COUNT(DISTINCT funcoes.cripto_re(b.cd_empresa, b.cd_registro_empregado, b.seq_dependencia)) AS qt_total
                   FROM public.bloqueto b
                   JOIN public.participantes p
                     ON p.cd_empresa            = b.cd_empresa 
                    AND p.cd_registro_empregado = b.cd_registro_empregado 
                    AND p.seq_dependencia       = b.seq_dependencia
                   JOIN public.titulares_planos tp
                     ON tp.cd_empresa            = p.cd_empresa
                    AND tp.cd_registro_empregado = p.cd_registro_empregado
                    AND tp.seq_dependencia       = p.seq_dependencia
                    AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
                   FROM titulares_planos tp1 
                  WHERE tp1.cd_empresa            = p.cd_empresa 
                    AND tp1.cd_registro_empregado = p.cd_registro_empregado 
                    AND tp1.seq_dependencia       = p.seq_dependencia)					   
                  WHERE b.status       IS NULL 
                    AND b.data_retorno IS NULL
                    AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NOT NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END)						   
                    AND b.cd_plano   = " . intval($args['cd_plano']) . "
                    AND b.cd_empresa = " . intval($args['cd_empresa']) . "
                    AND b.codigo_lancamento IN (" . implode(",", $args['codigo_lancamento'][intval($args['cd_empresa'])]) . ")
                    AND TO_DATE(TO_CHAR(b.ano_competencia,'FM9999') || '/' || TO_CHAR(b.mes_competencia,'FM09') || '/01' , 'YYYY/MM/DD' ) < TO_DATE('01/" . intval($args['nr_mes']) . "/" . intval($args['nr_ano']) . "' , 'DD/MM/YYYY')
                    AND DATE_TRUNC('month', b.dt_lancamento) = TO_DATE('01/" . intval($args['nr_mes']) . "/" . intval($args['nr_ano']) . "' , 'DD/MM/YYYY')
                    " . (trim($args['fl_email']) == "S" ? "AND (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%')" : "") . "
                    AND 0 = (SELECT COUNT(*)
                   FROM public.cobrancas c
                  WHERE c.cd_plano              = b.cd_plano
                    AND c.cd_empresa            = b.cd_empresa
                    AND c.cd_registro_empregado = b.cd_registro_empregado
                    AND c.seq_dependencia       = b.seq_dependencia
                    AND c.mes_competencia       = b.mes_competencia
                    AND c.ano_competencia       = b.ano_competencia
                    AND c.codigo_lancamento     = b.codigo_lancamento
                    AND c.sit_lancamento        = 'P'
                    AND DATE_TRUNC('month', c.dt_lancamento) = DATE_TRUNC('month', b.dt_lancamento))
                  GROUP BY tp_pagamento
                  UNION
             -- PRIMEIRO PAGAMENTO
                 SELECT CASE WHEN p.forma_pagamento IN ('BDL','BCO','FOL','FLT') THEN 'COB1P'
                   ELSE 'ERRO'
                    END AS tp_pagamento,
                        COUNT(DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia)) AS qt_total
                   FROM public.protocolos_participantes p
                   JOIN public.calendarios_planos cp
                     ON cp.cd_empresa = p.cd_empresa
                   JOIN public.participantes pr
                     ON pr.cd_empresa            = p.cd_empresa
                    AND pr.cd_registro_empregado = p.cd_registro_empregado
                    AND pr.seq_dependencia       = p.seq_dependencia	
                   JOIN public.titulares t
                     ON t.cd_empresa            = p.cd_empresa
                    AND t.cd_registro_empregado = p.cd_registro_empregado
                    AND t.seq_dependencia       = p.seq_dependencia				   
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
                  WHERE cp.cd_plano   = " . intval($args['cd_plano']) . "
                    AND cp.cd_empresa = " . intval($args['cd_empresa']) . "
                    " . (trim($args['fl_email']) == "S" ? "AND (COALESCE(pr.email,'') LIKE '%@%' OR COALESCE(pr.email_profissional,'') LIKE '%@%')" : "") . "
                    AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim
                    AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL 
                                  THEN 0 
                                  ELSE 1 
                              END)	

					-- MES ANO DE COMPETENCIA DEVE SER INFERIOR AO MES/ANO INFORMADO (4 a 1 meses de atraso)
                    AND DATE_TRUNC('day', cp.dt_competencia) BETWEEN (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '4 months'::interval) 
                    AND (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '1 month'::interval)
                    AND (p.forma_pagamento = 'BDL' 
                     OR (p.forma_pagamento = 'BCO')  
                     OR (p.forma_pagamento = 'FOL')
                     OR (p.forma_pagamento = 'FLT'))
                    AND t.dt_cancela_inscricao IS NULL
                  GROUP BY tp_pagamento		
                  UNION 
           ---#### SENGE - PRIMEIRO PAGAMENTO - BUSCA NA TABELA INSCRITOS INTERNET ####
            SELECT 'COB1P' AS tp_pagamento,
                   COUNT(DISTINCT funcoes.cripto_re(ii.cd_empresa, ii.cd_registro_empregado, ii.seq_dependencia)) AS qt_total
              FROM public.inscritos_internet ii,
                   public.taxas t,
                   public.pacotes p
             WHERE ii.dt_primeiro_pgto         IS NULL	 
               AND DATE_TRUNC('day', ii.dt_envio_primeira_cobr ) BETWEEN (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '4 months'::interval) 
               AND (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '1 month'::interval)
               AND ii.dt_geracao_primeira_cobr IS NOT NULL
               AND ii.cd_pacote                = 1
               AND ii.cd_plano                 = " . intval($args['cd_plano']) . "
               AND ii.cd_empresa               = " . intval($args['cd_empresa']) . "
               AND t.cd_indexador              = 42 
               AND t.dt_taxa                   = DATE_TRUNC('month',CURRENT_DATE)
               AND p.cd_pacote                 = ii.cd_pacote
               AND p.cd_plano                  = ii.cd_plano
               AND p.cd_empresa                = ii.cd_empresa
               AND p.tipo_cobranca             = 'I'
               AND p.dt_inicio                 = DATE_TRUNC('month',CURRENT_DATE)	
               " . (trim($args['fl_email']) == "S" ? "AND COALESCE(ii.email,'') LIKE '%@%'" : "") . "
             GROUP BY tp_pagamento					   
             ORDER BY tp_pagamento
            ";
        #echo "<pre style='text-align:left;'>atrasada<BR>$qr_sql</pre>";	#exit;
        $result = $this->db->query($qr_sql);
    }

    function sem_email(&$result, $args=array())
    {
        $qr_sql = "
                SELECT p.cd_empresa,
                       p.cd_registro_empregado,
                       p.seq_dependencia,
                       p.nome,
                       CASE WHEN b.cd_empresa = 7
                            THEN CASE WHEN b.codigo_lancamento = 2400 THEN 'COBDL'
                            WHEN b.codigo_lancamento = 2410 THEN 'CODCC'
                            ELSE 'ERRO'
                        END
                            WHEN b.cd_empresa IN (8,10,12,19,20,24,25,26,27,28,29,30,31) 
                            THEN CASE WHEN b.codigo_lancamento = 2502 THEN 'COBDL'
                            WHEN b.codigo_lancamento = 2501 THEN 'CODCC'
                            WHEN b.codigo_lancamento = 2500 THEN 'COFOL'
                            WHEN b.codigo_lancamento IN (2503,2509) THEN 'COFLT'
                            ELSE 'ERRO'
                        END
                            ELSE 'ERRO'
                        END AS forma_pagamento
                  FROM public.bloqueto b
                  JOIN public.participantes p
                    ON p.cd_empresa            = b.cd_empresa 
                   AND p.cd_registro_empregado = b.cd_registro_empregado 
                   AND p.seq_dependencia       = b.seq_dependencia
                  JOIN public.titulares_planos tp
                    ON tp.cd_empresa            = p.cd_empresa
                   AND tp.cd_registro_empregado = p.cd_registro_empregado
                   AND tp.seq_dependencia       = p.seq_dependencia
                   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
                                                     FROM titulares_planos tp1 
                                                    WHERE tp1.cd_empresa            = p.cd_empresa 
                                                      AND tp1.cd_registro_empregado = p.cd_registro_empregado 
                                                      AND tp1.seq_dependencia       = p.seq_dependencia)					   
                 WHERE b.status       IS NULL 
                   AND b.data_retorno IS NULL
                   AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NOT NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END)
                   AND b.cd_plano   = " . intval($args['cd_plano']) . "
                   AND b.cd_empresa = " . intval($args['cd_empresa']) . "
                   AND b.codigo_lancamento IN (" . implode(",", $args['codigo_lancamento'][intval($args['cd_empresa'])]) . ")
                   AND TO_DATE(TO_CHAR(b.ano_competencia,'FM9999') || '/' || TO_CHAR(b.mes_competencia,'FM09') || '/01' , 'YYYY/MM/DD' ) < TO_DATE('01/" . intval($args['nr_mes']) . "/" . intval($args['nr_ano']) . "' , 'DD/MM/YYYY')
                   AND DATE_TRUNC('month', b.dt_lancamento) = TO_DATE('01/" . intval($args['nr_mes']) . "/" . intval($args['nr_ano']) . "' , 'DD/MM/YYYY')
                   AND COALESCE(p.email,'') NOT LIKE '%@%' 
                   AND COALESCE(p.email_profissional,'') NOT LIKE '%@%'
                   AND 0 = (SELECT COUNT(*)
                              FROM public.cobrancas c
                             WHERE c.cd_plano              = b.cd_plano
                               AND c.cd_empresa            = b.cd_empresa
                               AND c.cd_registro_empregado = b.cd_registro_empregado
                               AND c.seq_dependencia       = b.seq_dependencia
                               AND c.mes_competencia       = b.mes_competencia
                               AND c.ano_competencia       = b.ano_competencia
                               AND c.codigo_lancamento     = b.codigo_lancamento
                               AND c.sit_lancamento        = 'P'
                               AND DATE_TRUNC('month', c.dt_lancamento) = DATE_TRUNC('month', b.dt_lancamento))
                 UNION
                SELECT pr.cd_empresa,
                       pr.cd_registro_empregado,
                       pr.seq_dependencia,
                       pr.nome,
                       p.forma_pagamento
                  FROM public.protocolos_participantes p
                  JOIN public.calendarios_planos cp
                    ON cp.cd_empresa = p.cd_empresa
                  JOIN public.participantes pr
                    ON pr.cd_empresa            = p.cd_empresa
                   AND pr.cd_registro_empregado = p.cd_registro_empregado
                   AND pr.seq_dependencia       = p.seq_dependencia	

                  JOIN public.titulares t
                    ON t.cd_empresa            = p.cd_empresa
                   AND t.cd_registro_empregado = p.cd_registro_empregado
                   AND t.seq_dependencia       = p.seq_dependencia				   

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
                 WHERE cp.cd_plano   = " . intval($args['cd_plano']) . "
                   AND cp.cd_empresa = " . intval($args['cd_empresa']) . "
                   AND COALESCE(pr.email,'') NOT LIKE '%@%' 
                   AND COALESCE(pr.email_profissional,'') NOT LIKE '%@%'
                   AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim
                   AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL 
                                 THEN 0 
                                 ELSE 1 
                             END)	

					-- MES ANO DE COMPETENCIA DEVE SER INFERIOR AO MES/ANO INFORMADO (4 a 1 meses de atraso)
                   AND DATE_TRUNC('day', cp.dt_competencia) BETWEEN (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '4 months'::interval) 
                   AND (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '1 month'::interval)
                   AND (p.forma_pagamento = 'BDL' 
                    OR (p.forma_pagamento = 'BCO')  
                    OR (p.forma_pagamento = 'FOL')
                    OR (p.forma_pagamento = 'FLT'))
                   AND t.dt_cancela_inscricao IS NULL
                 UNION 
              ---#### SENGE - BUSCA NA TABELA INSCRITOS INTERNET ####
                SELECT ii.cd_empresa,
                       ii.cd_registro_empregado,
                       ii.seq_dependencia,
                       ii.nome,
              'Primeiro Pagamento' AS forma_pagamento
                  FROM public.inscritos_internet ii,
                       public.taxas t,
                       public.pacotes p
                 WHERE ii.dt_primeiro_pgto         IS NULL	 
                   AND DATE_TRUNC('day', ii.dt_envio_primeira_cobr ) BETWEEN (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '4 months'::interval) 
                   AND (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '1 month'::interval)
                   AND ii.dt_geracao_primeira_cobr IS NOT NULL
                   AND ii.cd_pacote                = 1
                   AND ii.cd_plano                 = " . intval($args['cd_plano']) . "
                   AND ii.cd_empresa               = " . intval($args['cd_empresa']) . "
                   AND t.cd_indexador              = 42 
                   AND t.dt_taxa                   = DATE_TRUNC('month',CURRENT_DATE)
                   AND p.cd_pacote                 = ii.cd_pacote
                   AND p.cd_plano                  = ii.cd_plano
                   AND p.cd_empresa                = ii.cd_empresa
                   AND p.tipo_cobranca             = 'I'
                   AND p.dt_inicio                 = DATE_TRUNC('month',CURRENT_DATE)
                   AND COALESCE(ii.email,'') NOT LIKE '%@%'
                 ORDER BY forma_pagamento
					";
        #echo "<pre style='text-align:left;'>sem_email<BR>$qr_sql</pre>";	exit;
        $result = $this->db->query($qr_sql);
    }

    function atrasada_anterior(&$result, $args=array())
    {
        $qr_sql = "
                SELECT cc.cd_contribuicao_controle_tipo AS tp_pagamento,
                       COUNT(*) AS qt_total
                  FROM projetos.contribuicao_controle cc
                 WHERE cc.cd_empresa         = " . intval($args['cd_empresa']) . "
                   AND TO_DATE('01/' || cc.nr_mes_competencia::TEXT || '/' || cc.nr_ano_competencia::TEXT,'DD/MM/YYYY') = (TO_DATE('01/" . intval($args['nr_mes']) . "/" . intval($args['nr_ano']) . "','DD/MM/YYYY') - '1 month'::INTERVAL)
                   AND cc.fl_email_enviado       = 'S'
                   AND cc.cd_contribuicao_controle_tipo IN (" . implode(",", $args['cd_contribuicao_controle_tipo']) . ")
                 GROUP BY tp_pagamento
                 ORDER BY tp_pagamento
		          ";

        #echo "<pre style='text-align:left;'>$qr_sql</pre>";
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
						   CASE WHEN cc.cd_contribuicao_controle_tipo = 'COB1P' AND cc.cd_empresa = 7 AND cc.dt_controle < TO_DATE('01/08/2013','DD/MM/YYYY') 
								THEN (SELECT UPPER(funcoes.remove_acento(p.nome)) AS nome
										FROM public.inscritos_internet p
									   WHERE p.cd_empresa            = cc.cd_empresa
										 AND p.cd_registro_empregado = cc.cd_registro_empregado
										 AND p.seq_dependencia       = cc.seq_dependencia)
								ELSE (SELECT p.nome                 
										FROM public.participantes p
									   WHERE p.cd_empresa            = cc.cd_empresa
										 AND p.cd_registro_empregado = cc.cd_registro_empregado
										 AND p.seq_dependencia       = cc.seq_dependencia)
						   END AS nome
					  FROM projetos.contribuicao_controle cc
					  JOIN projetos.contribuicao_controle_tipo cct
						ON cct.cd_contribuicao_controle_tipo = cc.cd_contribuicao_controle_tipo
					 WHERE cc.cd_empresa                    = " . intval($args['cd_empresa']) . "
					   AND cc.nr_mes_competencia            = " . intval($args['nr_mes']) . "
					   AND cc.nr_ano_competencia            = " . intval($args['nr_ano']) . "
					   AND cc.cd_contribuicao_controle_tipo IN (" . implode(",", $args['cd_contribuicao_controle_tipo']) . ")
					   " . (trim($args['fl_email_enviado']) == "S" ? " AND cc.fl_email_enviado = 'S' " : "") . "
                   ";
        #echo "<pre style='text-align:left;'>contribuicao_controle<BR>$qr_sql</pre>"; exit;
        $result = $this->db->query($qr_sql);
    }

    function gerar(&$result, $args=array())
    {
        if ((trim($args['cd_empresa']) != "") and (intval($args['cd_plano']) > 0) and (intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0))
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
                             " . intval($args['nr_mes']) . ",
                             " . intval($args['nr_ano']) . ",
                             t.tp_pagamento,
                             " . intval($args['cd_usuario']) . "
                        FROM public.participantes p
                        JOIN (
                                SELECT DISTINCT funcoes.cripto_re(b.cd_empresa, b.cd_registro_empregado, b.seq_dependencia) AS re,
                                  CASE WHEN b.cd_empresa = 7
                                       THEN CASE WHEN b.codigo_lancamento = 2400 THEN 'COBDL'
                                       WHEN b.codigo_lancamento = 2410 THEN 'CODCC'
                                       ELSE 'ERRO'
                                   END
                                       WHEN b.cd_empresa IN (8,10,12,19,20,24,25,26,27,28,29,30,31)
                                       THEN CASE WHEN b.codigo_lancamento = 2502 THEN 'COBDL'
                                       WHEN b.codigo_lancamento = 2501 THEN 'CODCC'
                                       WHEN b.codigo_lancamento = 2500 THEN 'COFOL'
                                       WHEN b.codigo_lancamento IN (2503,2509) THEN 'COFLT'
                                       ELSE 'ERRO'
                                   END
                                       ELSE 'ERRO'
                                   END AS tp_pagamento
                                  FROM public.bloqueto b
                                  JOIN public.participantes p
                                    ON p.cd_empresa            = b.cd_empresa 
                                   AND p.cd_registro_empregado = b.cd_registro_empregado 
                                   AND p.seq_dependencia       = b.seq_dependencia
                                  JOIN public.titulares_planos tp
                                    ON tp.cd_empresa            = p.cd_empresa
                                   AND tp.cd_registro_empregado = p.cd_registro_empregado
                                   AND tp.seq_dependencia       = p.seq_dependencia
                                   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
                                                                     FROM titulares_planos tp1 
                                                                    WHERE tp1.cd_empresa            = p.cd_empresa 
                                                                      AND tp1.cd_registro_empregado = p.cd_registro_empregado 
                                                                      AND tp1.seq_dependencia       = p.seq_dependencia)					   
                                 WHERE b.status       IS NULL 
                                   AND b.data_retorno IS NULL
                                   AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NOT NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END)
                                   AND b.cd_plano   = " . intval($args['cd_plano']) . "
                                   AND b.cd_empresa = " . intval($args['cd_empresa']) . "
                                   AND b.codigo_lancamento IN (" . implode(",", $args['codigo_lancamento'][intval($args['cd_empresa'])]) . ")
                                   AND TO_DATE(TO_CHAR(b.ano_competencia,'FM9999') || '/' || TO_CHAR(b.mes_competencia,'FM09') || '/01' , 'YYYY/MM/DD' ) < TO_DATE('01/" . intval($args['nr_mes']) . "/" . intval($args['nr_ano']) . "' , 'DD/MM/YYYY')
                                   AND DATE_TRUNC('month', b.dt_lancamento) = TO_DATE('01/" . intval($args['nr_mes']) . "/" . intval($args['nr_ano']) . "' , 'DD/MM/YYYY')
                                   --AND (COALESCE(p.email,'') LIKE '%@%' OR COALESCE(p.email_profissional,'') LIKE '%@%')
                                   AND 0 = (SELECT COUNT(*)
                                              FROM public.cobrancas c
                                             WHERE c.cd_plano              = b.cd_plano
                                               AND c.cd_empresa            = b.cd_empresa
                                               AND c.cd_registro_empregado = b.cd_registro_empregado
                                               AND c.seq_dependencia       = b.seq_dependencia
                                               AND c.mes_competencia       = b.mes_competencia
                                               AND c.ano_competencia       = b.ano_competencia
                                               AND c.codigo_lancamento     = b.codigo_lancamento
                                               AND c.sit_lancamento        = 'P'
                                               AND DATE_TRUNC('month', c.dt_lancamento) = DATE_TRUNC('month', b.dt_lancamento))		
                                 UNION

                                SELECT DISTINCT funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re,
                                           CASE WHEN p.forma_pagamento IN ('BDL','BCO','FOL','FLT') THEN 'COB1P'
                                                ELSE 'ERRO'
                                            END AS tp_pagamento
                                  FROM public.protocolos_participantes p
                                  JOIN public.calendarios_planos cp
                                    ON cp.cd_empresa = p.cd_empresa
                                    
                                  JOIN public.participantes pr
                                    ON pr.cd_empresa            = p.cd_empresa
                                   AND pr.cd_registro_empregado = p.cd_registro_empregado
                                   AND pr.seq_dependencia       = p.seq_dependencia	

                                  JOIN public.titulares t
                                    ON t.cd_empresa            = p.cd_empresa
                                   AND t.cd_registro_empregado = p.cd_registro_empregado
                                   AND t.seq_dependencia       = p.seq_dependencia				   

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
                                 WHERE cp.cd_plano   = " . intval($args['cd_plano']) . "
                                   AND cp.cd_empresa = " . intval($args['cd_empresa']) . "
                                   --AND (COALESCE(pr.email,'') LIKE '%@%' OR COALESCE(pr.email_profissional,'') LIKE '%@%')
                                   AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim
                                   AND 0 = (CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL 
                                                 THEN 0 
                                                 ELSE 1 
                                             END)	

                                   -- MES ANO DE COMPETENCIA DEVE SER INFERIOR AO MES/ANO INFORMADO (4 a 1 meses de atraso)
                                   AND DATE_TRUNC('DAY', cp.dt_competencia) BETWEEN (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '4 months'::interval) 
                                   AND (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '1 month'::interval)
                                   AND (p.forma_pagamento = 'BDL' 
                                    OR (p.forma_pagamento = 'BCO')  
                                    OR (p.forma_pagamento = 'FOL')
                                    OR (p.forma_pagamento = 'FLT'))
                                   AND t.dt_cancela_inscricao IS NULL 
                                   ) t
                            ON t.re = funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia)
                           AND tp_pagamento <> 'ERRO'
                         UNION																   
                ---#### SENGE - BUSCA NA TABELA INSCRITOS INTERNET ####
                        SELECT ii.cd_empresa,
                               ii.cd_registro_empregado,
                               ii.seq_dependencia,
                               " . intval($args['nr_mes']) . ",
                               " . intval($args['nr_ano']) . ",
                               'COB1P' AS tp_pagamento,
                               " . intval($args['cd_usuario']) . "
                          FROM public.inscritos_internet ii,
                               public.taxas t,
                               public.pacotes p
                         WHERE ii.dt_primeiro_pgto         IS NULL	 
                           AND DATE_TRUNC('day', ii.dt_envio_primeira_cobr ) BETWEEN (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '4 months'::interval) 
                           AND (TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD') - '1 month'::interval)
                           AND ii.dt_geracao_primeira_cobr IS NOT NULL
                           AND ii.cd_pacote                = 1
                           AND ii.cd_plano                 = " . intval($args['cd_plano']) . "
                           AND ii.cd_empresa               = " . intval($args['cd_empresa']) . "
                           AND t.cd_indexador              = 42 
                           AND t.dt_taxa                   = DATE_TRUNC('month',CURRENT_DATE)
                           AND p.cd_pacote                 = ii.cd_pacote
                           AND p.cd_plano                  = ii.cd_plano
                           AND p.cd_empresa                = ii.cd_empresa
                           AND p.tipo_cobranca             = 'I'
                           AND p.dt_inicio                 = DATE_TRUNC('month',CURRENT_DATE))															   
          ";

            #echo "<PRE>gerar<BR>$qr_sql</PRE>"; //exit;

            $this->db->query($qr_sql);
        }
    }

    function enviarEmail(&$result, $args=array())
    {
        if ((trim($args['cd_empresa']) != "") and (intval($args['cd_plano']) > 0) and (intval($args['nr_mes']) > 0) and (intval($args['nr_ano']) > 0))
        {
            $qr_sql = " 
                SELECT email_contribuicao_atrasada 
                  FROM rotinas.email_contribuicao_atrasada(" . intval($args['cd_plano']) . ", " . intval($args['cd_empresa']) . ", " . intval($args['nr_mes']) . ", " . intval($args['nr_ano']) . ", ".$args['cd_usuario'].");
					  ";
            #echo "<PRE>$qr_sql</PRE>"; exit;

            $this->db->query($qr_sql);
        }
    }

    function enviarEmailCadastro(&$result, $args=array())
    {
        if (trim($args['lista']) != "")
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
                        'lrodriguez@eletroceee.com.br',                      
                        'Contribuição Atrasada - Lista de Participante SEM EMAIL',                             
                        'Contribuição Atrasada - Lista de Participante SEM EMAIL:\n\n" . trim($args['lista']) . "'
                     );";
            #echo "<PRE>$qr_sql</PRE>"; exit;

            $this->db->query($qr_sql);
        }
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
                       e.de,
                       e.cc,
                       e.cd_empresa,
                       e.cd_registro_empregado,
                       e.seq_dependencia,
                       p.nome,
                       e.assunto,
                       e.fl_retornou,
                       e.texto
                  FROM projetos.envia_emails e
                  JOIN public.inscritos_internet p
                    ON p.cd_empresa            = e.cd_empresa
                   AND p.cd_registro_empregado = e.cd_registro_empregado
                   AND p.seq_dependencia       = e.seq_dependencia
                 WHERE e.cd_evento             = (CASE WHEN e.cd_empresa = 7  THEN 92
                                                       WHEN e.cd_empresa = 8  THEN 86
                                                       WHEN e.cd_empresa = 10 THEN 89
                                                       WHEN e.cd_empresa = 12 THEN 245
                                                       WHEN e.cd_empresa = 19 THEN 82
                                                       WHEN e.cd_empresa = 20 THEN 185
                                                       WHEN e.cd_empresa = 24 THEN 253
                                                       WHEN e.cd_empresa = 25 THEN 261
                                                       WHEN e.cd_empresa = 26 THEN 395
                                                       WHEN e.cd_empresa = 27 THEN 396
                                                       WHEN e.cd_empresa = 28 THEN 398
                                                       WHEN e.cd_empresa = 29 THEN 401
                                                       WHEN e.cd_empresa = 30 THEN 414
                                                       WHEN e.cd_empresa = 31 THEN 425
                                                       ELSE NULL END)
                   AND e.cd_empresa            = " . intval($args['cd_empresa']) . "
                   AND DATE_TRUNC('month', e.dt_envio) = TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD')
                   " . (intval($args['cd_registro_empregado']) > 0 ? "AND e.cd_registro_empregado = " . intval($args['cd_registro_empregado']) : "") . "
                   " . (trim($args['seq_dependencia']) != "" ? "AND e.seq_dependencia = " . intval($args['seq_dependencia']) : "") . "
                   " . (trim($args["fl_retornou"]) != "" ? "AND e.fl_retornou = '" . $args["fl_retornou"] . "'" : "") . "
					  ";
        }
        else
        {
            $qr_sql = "
                SELECT e.cd_email,
                       TO_CHAR(e.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                       TO_CHAR(e.dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado,
                       e.para,
                       e.de,
                       e.cc,
                       e.cd_empresa,
                       e.cd_registro_empregado,
                       e.seq_dependencia,
                       p.nome,
                       e.assunto,
                       e.fl_retornou,
                       e.texto
                  FROM projetos.envia_emails e
                  JOIN public.participantes p
                    ON p.cd_empresa            = e.cd_empresa
                   AND p.cd_registro_empregado = e.cd_registro_empregado
                   AND p.seq_dependencia       = e.seq_dependencia
                 WHERE e.cd_evento             = (CASE WHEN e.cd_empresa = 7  THEN 92
                                                       WHEN e.cd_empresa = 8  THEN 86
                                                       WHEN e.cd_empresa = 10 THEN 89
                                                       WHEN e.cd_empresa = 12 THEN 245
                                                       WHEN e.cd_empresa = 19 THEN 82
                                                       WHEN e.cd_empresa = 20 THEN 185
                                                       WHEN e.cd_empresa = 24 THEN 253
                                                       WHEN e.cd_empresa = 25 THEN 261
                                                       WHEN e.cd_empresa = 26 THEN 395
                                                       WHEN e.cd_empresa = 27 THEN 396
                                                       WHEN e.cd_empresa = 28 THEN 398
                                                       WHEN e.cd_empresa = 29 THEN 401
                                                       WHEN e.cd_empresa = 30 THEN 414
                                                       WHEN e.cd_empresa = 31 THEN 425
                                                       ELSE NULL END)
                   AND e.cd_empresa            = " . intval($args['cd_empresa']) . "
                   AND DATE_TRUNC('month', e.dt_envio) = TO_DATE('" . intval($args['nr_ano']) . "-" . intval($args['nr_mes']) . "-01','YYYY-MM-DD')
                   " . (intval($args['cd_registro_empregado']) > 0 ? "AND e.cd_registro_empregado = " . intval($args['cd_registro_empregado']) : "") . "
                   " . (trim($args['seq_dependencia']) != "" ? "AND e.seq_dependencia = " . intval($args['seq_dependencia']) : "") . "
                   " . (trim($args["fl_retornou"]) != "" ? "AND e.fl_retornou = '" . $args["fl_retornou"] . "'" : "") . "
					  ";
        }
        #echo "<pre style='text-align:left;'>$qr_sql</pre>";exit;	
        $result = $this->db->query($qr_sql);
    }

    function checkPeriodo(&$result, $args=array())
    {
		$qr_sql = " 
					SELECT CASE WHEN TO_DATE('13/".str_pad(intval($args['nr_mes']),2, "0", STR_PAD_LEFT)."/".intval($args['nr_ano'])."','DD/MM/YYYY') <= CURRENT_DATE 
					            THEN 'S' 
								ELSE 'N' 
						   END AS fl_periodo
				  ";
		#echo "<PRE>$qr_sql</PRE>"; exit;

		$result = $this->db->query($qr_sql);
    }	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
					DELETE
					  FROM projetos.contribuicao_controle
					 WHERE cd_empresa                    = ".intval($args['cd_empresa'])."
					   AND cd_registro_empregado         = ".intval($args['cd_registro_empregado'])."
					   AND seq_dependencia               = ".intval($args['seq_dependencia'])."
					   AND nr_ano_competencia            = ".intval($args['nr_ano_competencia'])."
					   AND nr_mes_competencia            = ".intval($args['nr_mes_competencia'])."
					   AND cd_contribuicao_controle_tipo = '".trim($args['cd_contribuicao_controle_tipo'])."';
			      ";
		$result = $this->db->query($qr_sql);
	}

    public function envia_email_retorno($cd_plano, $cd_empresa, $nr_ano, $nr_mes, $cd_usuario_envio)
    {
        $qr_sql = "
            INSERT INTO projetos.envia_emails 
             (
                dt_envio,
                cd_usuario,
                dt_schedule_email, 
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
                ".intval($cd_usuario_envio).", 
                CURRENT_TIMESTAMP, 
                'Emails retornados - Contribuição (atrasada) do plano ".$cd_plano." empresa ".$cd_empresa."',
                funcoes.get_usuario(".intval($cd_usuario_envio).") || '@eletroceee.com.br',                   
                'gcmatendimento@eletroceee.com.br;cobranca@eletroceee.com.br',
                'lrodriguez@eletroceee.com.br',
                'Emails retornados - Contribuição (atrasada) do plano ".$cd_plano." empresa ".$cd_empresa." referente a ".$nr_mes."/".$nr_ano."', 
                'Confira o relatório de emails retornados:

http://www.e-prev.com.br/cieprev/index.php/planos/contribuicao_instituidor_atrasada/relatorio/".intval($cd_plano)."/".intval($cd_empresa)."/".$nr_mes."/".$nr_ano."/S

'
              );";

        $this->db->query($qr_sql);
    }
}

?>