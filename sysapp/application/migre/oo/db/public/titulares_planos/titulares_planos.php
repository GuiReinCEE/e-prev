<?php
class titulares_planos
{
	/**
	 * titulares_planos::cadastro_por_forma_pagamento()
	 * 
	 * Criar uma lista da quantidade de participantes em cada forma de pagamento.
	 * A lista é gerada com base no cadastro (participantes ativos)
	 * Para colher a informação são envolvidos os cadastros de:
	 * 
	 * - Participantes
	 *  
	 * (public.participantes e public.titulares_planos)
	 * 
	 * - Forma de pagamento utilizada pelo participante
	 * 
	 * (public.debito_conta_contribuicao)
	 * Nesse caso a ausencia de registro nessa tabela caracteriza BDL,
	 * todas as outras formas de pagamento devem estar inseridas nessa tabela.
	 * 
	 * - Cobranças
	 * (public.cobrancas)
	 * Identifica se o primeiro pagamento ocorreu dentro da competência atual
	 * 
	 * @param int @cd_empresa Código da empresa
	 * @param int @cd_plano Código do plano
	 * @param int @mes_competencia Código do plano
	 * @param int @ano_competencia Código do plano
	 * 
	 * @return array($row) 	onde row é um array() com as colunas da seleção
	 * 						Array devolvido no seguinte formato:
	 * 
	 * 						$collection[0]['forma_pagamento']
	 * 						$collection[0]['qt_participante']
	 */
	public static function cadastro_por_forma_pagamento($cd_empresa, $cd_plano, $mes_competencia, $ano_competencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			-- PARTICIPANTES - CADASTRO POR FORMA DE PAGAMENTO
			SELECT 'BDL' AS forma_pagamento, 
			       COUNT(*) AS qt_participante
			  FROM public.participantes p
			  JOIN public.titulares_planos tp
			    ON tp.cd_empresa            = p.cd_empresa
			   AND tp.cd_registro_empregado = p.cd_registro_empregado
			   AND tp.seq_dependencia       = p.seq_dependencia
			   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
			         FROM public.titulares_planos tp1 
			        WHERE tp1.cd_empresa=p.cd_empresa 
			          AND tp1.cd_registro_empregado=p.cd_registro_empregado 
			          AND tp1.seq_dependencia=p.seq_dependencia)
			 WHERE p.cd_empresa = {cd_empresa}
			   AND p.cd_plano   = {cd_plano}
			   AND p.dt_obito IS NULL /* obitos ocorridos depois da geração devem gerar inconsistência */
			   AND NOT EXISTS (SELECT 1 
			                     FROM public.debito_conta_contribuicao dcc 
			                    WHERE dcc.cd_empresa            = p.cd_empresa
			                      AND dcc.cd_registro_empregado = p.cd_registro_empregado
			                      AND dcc.seq_dependencia       = p.seq_dependencia
			                      AND dcc.dt_confirma_opcao     IS NOT NULL

								  AND (dcc.dt_confirma_canc     IS NULL
					                  OR
					                   dcc.dt_confirma_canc > (SELECT cgc.dt_geracao 
					                                       FROM controle_geracao_cobranca cgc
					                                      WHERE cgc.cd_empresa      = p.cd_empresa 
					                                        AND cgc.cd_plano        = p.cd_plano
					                                        AND cgc.mes_competencia = {mes_competencia} 
					                                        AND cgc.ano_competencia = {ano_competencia}))

								  
								  AND dcc.forma_pagamento       <> 'BDL')
			   AND NOT EXISTS (SELECT 1 
			                     FROM public.cobrancas c
			                    WHERE c.cd_empresa            = p.cd_empresa
			                      AND c.cd_registro_empregado = p.cd_registro_empregado
			                      AND c.seq_dependencia       = p.seq_dependencia
			                      AND c.sit_lancamento	  = 'P'
			                      AND c.mes_competencia	  = {mes_competencia}
			                      AND c.ano_competencia	  = {ano_competencia}
			                      AND codigo_lancamento   = 2450
					)

			UNION 

			SELECT dcc.forma_pagamento, 
			       COUNT(*) AS qt_participante
			  FROM public.participantes p
			  JOIN public.titulares_planos tp
			    ON tp.cd_empresa            = p.cd_empresa
			   AND tp.cd_registro_empregado = p.cd_registro_empregado
			   AND tp.seq_dependencia       = p.seq_dependencia
			   AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
			         FROM public.titulares_planos tp1 
			        WHERE tp1.cd_empresa=p.cd_empresa 
			          AND tp1.cd_registro_empregado=p.cd_registro_empregado 
			          AND tp1.seq_dependencia=p.seq_dependencia)
			  JOIN public.debito_conta_contribuicao dcc 
			    ON dcc.cd_empresa = p.cd_empresa
			   AND dcc.cd_registro_empregado = p.cd_registro_empregado
			   AND dcc.seq_dependencia = p.seq_dependencia
			 WHERE p.cd_empresa = {cd_empresa}
			   AND p.cd_plano = {cd_plano}
			   AND p.dt_obito IS NULL /* obitos ocorridos depois da geração devem gerar inconsistência */
			   AND dcc.dt_confirma_opcao IS NOT NULL
			   AND dcc.dt_confirma_canc IS NULL
			   AND dcc.forma_pagamento <> 'BDL'
			   AND tp.dt_ingresso_plano <> TO_DATE('01/{mes_competencia}/{ano_competencia}','DD/MM/YYYY')
			 GROUP BY dcc.forma_pagamento

			 ORDER BY forma_pagamento

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{cd_plano}", $cd_plano );
		$db->setParameter( "{mes_competencia}", $mes_competencia );
		$db->setParameter( "{ano_competencia}", $ano_competencia );
		$r = $db->get(true);
	
		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}
}