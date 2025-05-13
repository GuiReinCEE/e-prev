<?php
class Acompanha_inscricao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function forma_pagamento()
	{
		$qr_sql = "
			SELECT id_tipo_liquidacao AS value, 
			       descricao AS text
              FROM vw_liquidacoes_inscricao
             ORDER BY descricao;";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function empresa()
	{
		$qr_sql = "
			SELECT p.cd_empresa AS value, 
				   p.sigla AS text
			  FROM public.patrocinadoras p
			 ORDER BY text;";
			 
		return $this->db->query($qr_sql)->result_array();
	}	

	public function listar($args=array())
	{
		$qr_sql = "
			SELECT p.cd_empresa, 
				   p.cd_registro_empregado, 
				   p.seq_dependencia, 
				   p.nome,
				   t.forma_pagamento,
				   TO_CHAR(t.dt_solicitacao, 'DD/MM/YYYY') AS dt_solicitacao,  --dt_solicitacao
				   TO_CHAR(COALESCE(t.dt_recebimento,t.dt_recebimento_mongeral)::DATE, 'DD/MM/YYYY') AS dt_recebimento,  --dt_recebimento
				   
                   (CASE WHEN pa.tipo_cliente = 'P'
				         THEN TO_CHAR(t.dt_digita_ingresso, 'DD/MM/YYYY')
				         ELSE TO_CHAR(p.dt_inclusao, 'DD/MM/YYYY') 
			       END) AS dt_inclusao,  --dt_inclusao
				   
				   TO_CHAR(pp.dt_confirma::DATE, 'DD/MM/YYYY') AS dt_confirma,  --dt_confirma
					
				   (SELECT TO_CHAR(pvc.dt_envio_inscricao, 'DD/MM/YYYY')
					  FROM projetos.pre_venda pv 
					  JOIN projetos.pre_venda_contato pvc
					    ON pvc.cd_pre_venda = pv.cd_pre_venda
					 WHERE pv.dt_exclusao IS NULL 
					   AND pvc.dt_exclusao IS NULL
					   AND pv.cpf = funcoes.format_cpf(p.cpf_mf)
					   AND pvc.dt_envio_inscricao IS NOT NULL 
					 ORDER BY pvc.dt_envio_inscricao DESC 
					 LIMIT 1) AS dt_envio_cadastro, -- dt_envio_cadastro

				   TO_CHAR((CASE WHEN pa.tipo_cliente = 'P' 
				         THEN (SELECT MIN(c.dt_inclusao)::DATE
								FROM public.cobrancas c
							   WHERE c.cd_empresa            = p.cd_empresa
								 AND c.cd_registro_empregado = p.cd_registro_empregado
								 AND c.seq_dependencia       = p.seq_dependencia
								 AND c.dt_inclusao           >= t.dt_digita_ingresso) --DATA GERADO NO COBRANCA
						 ELSE (CASE WHEN t.forma_pagamento = 'BDL' 
						            THEN COALESCE((SELECT MIN(cc.dt_controle)::DATE
								             		 FROM projetos.contribuicao_controle cc
								   				    WHERE cc.cd_empresa            = p.cd_empresa
								   				      AND cc.cd_registro_empregado = p.cd_registro_empregado
								   				      AND cc.seq_dependencia       = p.seq_dependencia
								   				      AND cc.cd_contribuicao_controle_tipo IN('1PBDL','1PDCC','1PFLT','1PFOL','COB1P')),--ENVIO DO EMAIL
								   			  (SELECT MIN(c.dt_inclusao)::DATE
								   				 FROM public.cobrancas c
								   				WHERE c.cd_empresa            = p.cd_empresa
								   				  AND c.cd_registro_empregado = p.cd_registro_empregado
								   		          AND c.seq_dependencia       = p.seq_dependencia)) --DATA GERADO NO COBRANCA
								    ELSE (SELECT MIN(c.dt_inclusao)::DATE
								   		    FROM public.cobrancas c
								   	       WHERE c.cd_empresa            = p.cd_empresa
								   		     AND c.cd_registro_empregado = p.cd_registro_empregado
								   		     AND c.seq_dependencia       = p.seq_dependencia) --DATA GERADO NO COBRANCA
						       END) END), 'DD/MM/YYYY') AS dt_cobranca, -- dt_cobranca
						   
				    TO_CHAR(CASE WHEN t.forma_pagamento IN ('FOL','FLT')
								 THEN (SELECT MIN(c.dt_envio_patroc)::DATE
										 FROM public.cobrancas c
										WHERE c.cd_empresa            = p.cd_empresa
										  AND c.cd_registro_empregado = p.cd_registro_empregado
										  AND c.seq_dependencia       = p.seq_dependencia) --ENVIO DA FOLHA (PATROCINADORA/EMPREGADOR)
								 WHEN t.forma_pagamento IN ('DEP','CHQ')
								 THEN (SELECT MIN(c.dt_inclusao)::DATE
										 FROM public.cobrancas c
										WHERE c.cd_empresa            = p.cd_empresa
										  AND c.cd_registro_empregado = p.cd_registro_empregado
										  AND c.seq_dependencia       = p.seq_dependencia) --DATA GERADO NO COBRANCA    
								 WHEN t.forma_pagamento = 'BCO'
								 THEN (SELECT MIN(COALESCE(a.dt_inclusao::DATE,a.datdeb))::DATE
										 FROM public.arq_desc_banco a
										WHERE a.cd_empresa            = p.cd_empresa
										  AND a.cd_registro_empregado = p.cd_registro_empregado
										  AND a.seq_dependencia       = p.seq_dependencia) --ENVIO DO ARQUIVO PARA O BANCO
								 WHEN t.forma_pagamento = 'BDL'
								 THEN COALESCE((SELECT MIN(cc.dt_controle)::DATE
								 				  FROM projetos.contribuicao_controle cc
												 WHERE cc.cd_empresa            = p.cd_empresa
												   AND cc.cd_registro_empregado = p.cd_registro_empregado
												   AND cc.seq_dependencia       = p.seq_dependencia
												   AND cc.cd_contribuicao_controle_tipo IN('1PBDL','1PDCC','1PFLT','1PFOL','COB1P')),--ENVIO DO EMAIL
											   (SELECT MIN(c.dt_inclusao)::DATE
												  FROM public.cobrancas c
												 WHERE c.cd_empresa            = p.cd_empresa
												   AND c.cd_registro_empregado = p.cd_registro_empregado
												   AND c.seq_dependencia       = p.seq_dependencia)) --DATA GERADO NO COBRANCA
								 ELSE NULL::DATE
						   END, 'DD/MM/YYYY') AS dt_envio, -- dt_envio
						   
			       TO_CHAR(t.dt_digita_ingresso::DATE, 'DD/MM/YYYY') AS dt_dig_ingresso,  --dt_dig_ingresso
			       TO_CHAR(t.dt_ingresso_eletro::DATE, 'DD/MM/YYYY') AS dt_ingresso,  --dt_ingresso
			       TO_CHAR(t.dt_desligamento_eletro::DATE, 'DD/MM/YYYY') AS dt_desliga,  --dt_desliga
			       TO_CHAR(t.dt_cancela_inscricao::DATE, 'DD/MM/YYYY') AS dt_cancela,  --dt_cancela
				   
				   (CASE WHEN pa.tipo_cliente = 'P'
				         THEN (t.dt_digita_ingresso::DATE - t.dt_solicitacao::DATE)
				         ELSE (pp.dt_confirma::DATE - t.dt_solicitacao::DATE)
			       END) AS qt_dia_cadastro,
				   
				   (((CASE WHEN pa.tipo_cliente = 'P' 
				         THEN (SELECT MIN(c.dt_inclusao)::DATE
								FROM public.cobrancas c
							   WHERE c.cd_empresa            = p.cd_empresa
								 AND c.cd_registro_empregado = p.cd_registro_empregado
								 AND c.seq_dependencia       = p.seq_dependencia
								 AND c.dt_inclusao           >= t.dt_digita_ingresso) --DATA GERADO NO COBRANCA
						 ELSE (CASE WHEN t.forma_pagamento = 'BDL' 
						            THEN COALESCE((SELECT MIN(cc.dt_controle)::DATE
								             		 FROM projetos.contribuicao_controle cc
								   				    WHERE cc.cd_empresa            = p.cd_empresa
								   				      AND cc.cd_registro_empregado = p.cd_registro_empregado
								   				      AND cc.seq_dependencia       = p.seq_dependencia
								   				      AND cc.cd_contribuicao_controle_tipo IN('1PBDL','1PDCC','1PFLT','1PFOL','COB1P')),--ENVIO DO EMAIL
								   			  (SELECT MIN(c.dt_inclusao)::DATE
								   				 FROM public.cobrancas c
								   				WHERE c.cd_empresa            = p.cd_empresa
								   				  AND c.cd_registro_empregado = p.cd_registro_empregado
								   		          AND c.seq_dependencia       = p.seq_dependencia)) --DATA GERADO NO COBRANCA
								    ELSE (SELECT MIN(c.dt_inclusao)::DATE
								   		    FROM public.cobrancas c
								   	       WHERE c.cd_empresa            = p.cd_empresa
								   		     AND c.cd_registro_empregado = p.cd_registro_empregado
								   		     AND c.seq_dependencia       = p.seq_dependencia) --DATA GERADO NO COBRANCA
						       END) END)) - t.dt_solicitacao::DATE) AS qt_dia_geracao,
				   
				   (CASE WHEN t.forma_pagamento IN ('FOL','FLT')
						 THEN (SELECT MIN(c.dt_envio_patroc)::DATE
								 FROM public.cobrancas c
								WHERE c.cd_empresa            = p.cd_empresa
								  AND c.cd_registro_empregado = p.cd_registro_empregado
								  AND c.seq_dependencia       = p.seq_dependencia) --ENVIO DA FOLHA (PATROCINADORA/EMPREGADOR)
						 WHEN t.forma_pagamento IN ('DEP','CHQ')
						 THEN (SELECT MIN(c.dt_inclusao)::DATE
								 FROM public.cobrancas c
								WHERE c.cd_empresa            = p.cd_empresa
								  AND c.cd_registro_empregado = p.cd_registro_empregado
								  AND c.seq_dependencia       = p.seq_dependencia) --DATA GERADO NO COBRANCA    
						 WHEN t.forma_pagamento = 'BCO'
						 THEN (SELECT MIN(COALESCE(a.dt_inclusao::DATE,a.datdeb))::DATE
								 FROM public.arq_desc_banco a
								WHERE a.cd_empresa            = p.cd_empresa
								  AND a.cd_registro_empregado = p.cd_registro_empregado
								  AND a.seq_dependencia       = p.seq_dependencia) --ENVIO DO ARQUIVO PARA O BANCO
						 WHEN t.forma_pagamento = 'BDL'
						 THEN COALESCE((SELECT MIN(cc.dt_controle)::DATE
										  FROM projetos.contribuicao_controle cc
										 WHERE cc.cd_empresa            = p.cd_empresa
										   AND cc.cd_registro_empregado = p.cd_registro_empregado
										   AND cc.seq_dependencia       = p.seq_dependencia
										   AND cc.cd_contribuicao_controle_tipo IN('1PBDL','1PDCC','1PFLT','1PFOL','COB1P')),--ENVIO DO EMAIL
									   (SELECT MIN(c.dt_inclusao)::DATE
										  FROM public.cobrancas c
										 WHERE c.cd_empresa            = p.cd_empresa
										   AND c.cd_registro_empregado = p.cd_registro_empregado
										   AND c.seq_dependencia       = p.seq_dependencia)) --DATA GERADO NO COBRANCA
						 ELSE NULL::DATE
				   END)::DATE - t.dt_solicitacao::DATE AS qt_dia_envio,
				   (t.dt_digita_ingresso::DATE - t.dt_solicitacao::DATE) AS qt_dia_ingresso,

				   TO_CHAR(cp.dt_inicio::DATE, 'DD/MM/YYYY') AS dt_inicio_calendario,  --dt_inicio
			       TO_CHAR(cp.dt_fim::DATE, 'DD/MM/YYYY') AS dt_fim_calendario,  --dt_fim
			       (SELECT COALESCE(CASE WHEN sp1.sp_recomposto = 0
                                         THEN NULL
                                         ELSE sp1.sp_recomposto
                                   END, sp1.sp_competencia)
                      FROM public.salarios_participacoes sp1
                     WHERE sp1.cd_empresa            = COALESCE(p.cd_empresa,-1)
                       AND sp1.cd_registro_empregado = COALESCE(p.cd_registro_empregado,-1)
                       AND sp1.seq_dependencia       = COALESCE(p.seq_dependencia,-1)
                       AND sp1.mes                   <> 13
                     ORDER BY sp1.dt_lancamento ASC
                     LIMIT 1) AS vl_contrib
			  FROM public.participantes p
			  JOIN public.patrocinadoras pa
				ON pa.cd_empresa   = p.cd_empresa
			   --AND pa.tipo_cliente = 'I'
			  JOIN public.titulares t
				ON t.cd_empresa            = p.cd_empresa
			   AND t.cd_registro_empregado = p.cd_registro_empregado
			   AND t.seq_dependencia       = p.seq_dependencia
			  LEFT JOIN public.protocolos_participantes pp
				ON pp.cd_empresa            = p.cd_empresa
			   AND pp.cd_registro_empregado = p.cd_registro_empregado
			   AND pp.seq_dependencia       = p.seq_dependencia   
			  LEFT JOIN public.calendarios_planos cp
			    ON cp.cd_empresa       = p.cd_empresa
            -- AND cp.cd_plano         = 8
               AND cp.dt_competencia   = DATE_TRUNC('month', COALESCE(t.dt_recebimento,t.dt_recebimento_mongeral)) --TO_DATE('2016-07-01','YYYY-MM-DD')
			 WHERE p.seq_dependencia        = 0
			   ".(trim($args['cd_tipo_cliente']) != '' ? "AND pa.tipo_cliente = '".trim($args['cd_tipo_cliente'])."'" : '')."
			   ".(trim($args['cd_plano_empresa']) != '' ? "AND p.cd_empresa = ".intval($args['cd_plano_empresa']) : '')."
			   ".(trim($args['cd_plano']) != '' ? "AND p.cd_plano = ".intval($args['cd_plano']) : '')."
			   ".(((trim($args['dt_solicitacao_ini'])) AND (trim($args['dt_solicitacao_fim']))) ? "AND DATE_TRUNC('day', t.dt_solicitacao) BETWEEN TO_DATE('".$args['dt_solicitacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_solicitacao_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(((trim($args['dt_inclusao_ini'])) AND (trim($args['dt_inclusao_fim']))) ? "AND DATE_TRUNC('day', p.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(((trim($args['dt_confirma_ini'])) AND (trim($args['dt_confirma_fim']))) ? "AND DATE_TRUNC('day', pp.dt_confirma::DATE) BETWEEN TO_DATE('".$args['dt_confirma_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_confirma_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(((trim($args['dt_cobranca_ini'])) AND (trim($args['dt_cobranca_fim']))) ? "AND DATE_TRUNC('day', 
						   CASE WHEN t.forma_pagamento = 'BDL' 
						        THEN COALESCE((SELECT MIN(cc.dt_controle)::DATE
								          		 FROM projetos.contribuicao_controle cc
												WHERE cc.cd_empresa            = p.cd_empresa
												  AND cc.cd_registro_empregado = p.cd_registro_empregado
												  AND cc.seq_dependencia       = p.seq_dependencia
												  AND cc.cd_contribuicao_controle_tipo IN('1PBDL','1PDCC','1PFLT','1PFOL','COB1P')),--ENVIO DO EMAIL
											  (SELECT MIN(c.dt_inclusao)::DATE
												 FROM public.cobrancas c
												WHERE c.cd_empresa            = p.cd_empresa
												  AND c.cd_registro_empregado = p.cd_registro_empregado
										  AND c.seq_dependencia       = p.seq_dependencia)) --DATA GERADO NO COBRANCA
								ELSE (SELECT MIN(c.dt_inclusao)::DATE
										FROM public.cobrancas c
									   WHERE c.cd_empresa            = p.cd_empresa
										 AND c.cd_registro_empregado = p.cd_registro_empregado
										 AND c.seq_dependencia       = p.seq_dependencia) --DATA GERADO NO COBRANCA
						   END) BETWEEN TO_DATE('".$args['dt_cobranca_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_cobranca_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(((trim($args['dt_envio_ini'])) AND (trim($args['dt_envio_fim']))) ? "AND DATE_TRUNC('day', 
							CASE WHEN t.forma_pagamento IN ('FOL','FLT')
								 THEN (SELECT MIN(c.dt_envio_patroc)::DATE
										 FROM public.cobrancas c
										WHERE c.cd_empresa            = p.cd_empresa
										  AND c.cd_registro_empregado = p.cd_registro_empregado
										  AND c.seq_dependencia       = p.seq_dependencia) --ENVIO DA FOLHA (PATROCINADORA/EMPREGADOR)
								 WHEN t.forma_pagamento IN ('DEP','CHQ')
								 THEN (SELECT MIN(c.dt_inclusao)::DATE
										 FROM public.cobrancas c
										WHERE c.cd_empresa            = p.cd_empresa
										  AND c.cd_registro_empregado = p.cd_registro_empregado
										  AND c.seq_dependencia       = p.seq_dependencia) --DATA GERADO NO COBRANCA    
								 WHEN t.forma_pagamento = 'BCO'
								 THEN (SELECT MIN(COALESCE(a.dt_inclusao::DATE,a.datdeb))::DATE
										 FROM public.arq_desc_banco a
										WHERE a.cd_empresa            = p.cd_empresa
										  AND a.cd_registro_empregado = p.cd_registro_empregado
										  AND a.seq_dependencia       = p.seq_dependencia) --ENVIO DO ARQUIVO PARA O BANCO
								 WHEN t.forma_pagamento = 'BDL'
								 THEN COALESCE((SELECT MIN(cc.dt_controle)::DATE
								 				  FROM projetos.contribuicao_controle cc
												 WHERE cc.cd_empresa            = p.cd_empresa
												   AND cc.cd_registro_empregado = p.cd_registro_empregado
												   AND cc.seq_dependencia       = p.seq_dependencia
												   AND cc.cd_contribuicao_controle_tipo IN('1PBDL','1PDCC','1PFLT','1PFOL','COB1P')),--ENVIO DO EMAIL
											   (SELECT MIN(c.dt_inclusao)::DATE
												  FROM public.cobrancas c
												 WHERE c.cd_empresa            = p.cd_empresa
												   AND c.cd_registro_empregado = p.cd_registro_empregado
												   AND c.seq_dependencia       = p.seq_dependencia)) --DATA GERADO NO COBRANCA
								 ELSE NULL::DATE
						    END) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(((trim($args['dt_dig_ingresso_ini'])) AND (trim($args['dt_dig_ingresso_fim']))) ? "AND DATE_TRUNC('day', t.dt_digita_ingresso::DATE) BETWEEN TO_DATE('".$args['dt_dig_ingresso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_dig_ingresso_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(((trim($args['dt_ingresso_ini'])) AND (trim($args['dt_ingresso_fim']))) ? "AND DATE_TRUNC('day', t.dt_ingresso_eletro::DATE) BETWEEN TO_DATE('".$args['dt_ingresso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ingresso_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(trim($args['fl_participante']) == 'S' ? "AND t.dt_ingresso_eletro IS NOT NULL AND t.dt_desligamento_eletro IS NULL" : '')."
			   ".(trim($args['fl_participante']) == 'N' ? "AND (t.dt_ingresso_eletro IS NULL OR t.dt_desligamento_eletro IS NOT NULL)" : '')."
			   ".(trim($args['fl_ingresso']) == 'S' ? "AND t.dt_ingresso_eletro IS NOT NULL" : '')."
			   ".(trim($args['fl_ingresso']) == 'N' ? "AND t.dt_ingresso_eletro IS NULL" : '')."
			   ".(trim($args['fl_cancela_inscricao']) == 'S' ? "AND t.dt_cancela_inscricao IS NOT NULL" : '')."
			   ".(trim($args['fl_cancela_inscricao']) == 'N' ? "AND t.dt_cancela_inscricao IS NULL" : '')."
			   ".(trim($args['cd_empresa']) != '' ? "AND p.cd_empresa = ".intval($args['cd_empresa']) : '')."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND p.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : '')."
			   ".(trim($args['seq_dependencia']) != '' ? "AND p.seq_dependencia = ".intval($args['seq_dependencia']) : '')."
			   ".(trim($args['cpf_mf']) != '' ? "AND (funcoes.format_cpf(p.cpf_mf) = '".trim($args['cpf_mf'])."')" : '')."
			   ".(trim($args["nome"]) != "" ? "AND UPPER(funcoes.remove_acento(p.nome)) LIKE UPPER(funcoes.remove_acento('%".trim($args["nome"])."%'))" : "")."
			   ".(trim($args['id_tipo_liquidacao']) != '' ? "AND t.forma_pagamento = '".trim($args['id_tipo_liquidacao'])."'" : '')."
			 ORDER BY t.dt_solicitacao DESC;";
		
		return $this->db->query($qr_sql)->result_array();
	}
}
?>