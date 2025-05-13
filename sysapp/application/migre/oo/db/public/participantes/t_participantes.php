<?php
class t_participantes
{
	public static function select($where=null)
	{
		return array();
	}
	
	public static function select_custom($where)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT *
			  FROM public.participantes
			 WHERE cd_empresa = {cd_empresa}
			 AND cd_registro_empregado = {cd_registro_empregado}
			 AND seq_dependencia = {seq_dependencia}
		  ORDER BY nome

		" );

		$db->setParameter("{cd_empresa}", $where['cd_empresa']);
		$db->setParameter("{cd_registro_empregado}", $where['cd_registro_empregado']);
		$db->setParameter("{seq_dependencia}", $where['seq_dependencia']);

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			// echo $db->getMessage();
			exit;
		}

		$ret2 = new e_participantes_collection();
		$i=-1;
		foreach( $rows as $row )
		{	
			$i++;
			$item = new e_participantes_ext();
            foreach( $item as $key=>$value )
            {
            	eval( '$item->'.$key.' = $row[' . $key . '];' );
            }
			$ret2->add( $item );
		}
		return $ret2;
	}
	
	public static function insert($entidade)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("");
		
		$db->setParameter("{column}", $entidade->column);
		
		$db->execute();
		
		if( $db->haveError() )
		{
			throw new Exception( $db->getMessage() );
		}
	}

	/**
	 * Consulta avançada para verificar se o participante é migrado ou não
	 * 
	 * @param e_participantes_ext $e Objeto participantes populado com a PK
	 */
	public static function verifica_se_migrado( e_participantes_ext $e )
	{
		// $db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT p.cd_plano as cd_plano 
			FROM participantes p 
			WHERE p.cd_registro_empregado = {cd_registro_empregado} 
			AND p.cd_empresa = {cd_empresa}
			AND p.seq_dependencia = 0
			AND p.cd_plano <> 0 
			AND EXISTS 
			(
				SELECT tp.cd_plano from titulares_planos tp 
				WHERE tp.cd_empresa = p.cd_empresa 
				AND tp.cd_registro_empregado = p.cd_registro_empregado 
				AND tp.seq_dependencia = p.seq_dependencia 
				AND tp.cd_plano = p.cd_plano 
				AND tp.dt_migracao is not null 
				AND tp.dt_ingresso_plano = 
				(
					SELECT max(tp2.dt_ingresso_plano) 
					FROM titulares_planos tp2 
					WHERE tp2.cd_empresa = tp.cd_empresa 
					AND tp2.cd_registro_empregado = tp.cd_registro_empregado 
					AND tp2.seq_dependencia = tp.seq_dependencia 
					AND tp2.cd_plano = tp.cd_plano
				)
			);

		" );

		$db->setParameter("{cd_empresa}", $e->cd_empresa);
		$db->setParameter("{cd_registro_empregado}", $e->cd_registro_empregado);

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			echo $db->getMessage();
			exit;
		}

		// nenhum registro encontrado
		if(sizeof($rows)==0)
		{
			return false;
		}
		// encontrado plano 2 para query indica que é migrado
		elseif($rows[0]['cd_plano']==2)
		{
			return true;
		}
		// outro retorno qualquer para query indica que não é migrado
		else
		{
			return false;
		}
	}

	public static function verifica_se_ativo( e_participantes_ext $e )
	{
		// $db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

				 SELECT p.cd_registro_empregado
			       FROM PARTICIPANTES P,
			            TITULARES     T
			       WHERE P.CD_EMPRESA            = T.CD_EMPRESA
			         AND P.CD_REGISTRO_EMPREGADO = T.CD_REGISTRO_EMPREGADO
			         AND P.SEQ_DEPENDENCIA       = T.SEQ_DEPENDENCIA
			         and (
				             (
				                P.TIPO_FOLHA          IN (00,01,11,12,13)
				              AND
				                T.CATEGORIA_FUNCIONAL <> 'B'
				             )
			               OR
			               (
			                  P.TIPO_FOLHA          = 6
			                AND
			                  T.TIPO_APOSENTADO     = 13
			               )
			               OR
			               (  
			                  P.TIPO_FOLHA          = 0
			                AND
			                  P.CD_PLANO            > 0
			                )
			             )
			
			AND p.cd_empresa = {cd_empresa}
			AND p.cd_registro_empregado = {cd_registro_empregado}
			AND p.seq_dependencia = {seq_dependencia};

		" );

		$db->setParameter("{cd_empresa}", $e->cd_empresa);
		$db->setParameter("{cd_registro_empregado}", $e->cd_registro_empregado);
		$db->setParameter("{seq_dependencia}", $e->seq_dependencia);

		$rows = array();
		$rows = $db->get();

		if( $db->haveError() )
		{
			// echo $db->getMessage();
			exit;
		}

		// nenhum registro encontrado
		if(sizeof($rows)==0)
		{
			return false;
		}
		// encontrado plano 2 para query indica que é migrado
		else
		{
			return true;
		}
	}

	/**
	 * t_participantes::total_primeiro_pagamento_atrasado( $cd_empresa, $cd_plano, $mes, $ano )
	 * 
	 * Método criado para atender a um relatório gerado no 2º dia útil do mês
	 * quando os participantes com cobrança de primeiro pagamento do mes anterior
	 * ainda não são considerados atrasados, então o intervalo de datas da cobrança 
	 * considerados nessa consulta são de 2 a 5 meses atras
	 * 
	 * @param int $cd_empresa
	 * @param int $cd_plano
	 * @param int $mes
	 * @param int $ano
	 * 
	 * @return array( array('com_email'=>0), array('geral'=>0) )
	 * 
	 * O retorno deve ser usado da seguinte forma
	 * 
	 * $total['geral']; // total do resultado da query principal
	 * $total['com_email']; // do resultado, apenas o total de participante com email
	 * 
	 */
	public static function total_primeiro_pagamento_atrasado( $cd_empresa, $cd_plano, $mes, $ano )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL("

		-- total_primeiro_pagamento_atrasado
		SELECT

			COUNT(*) AS quantos

		FROM

			public.protocolos_participantes p

			LEFT JOIN
			(
				SELECT ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia, sum(ap.premio) as valor_risco
				FROM public.apolices_participantes ap
				JOIN public.apolices a
				ON a.cd_apolice = ap.cd_apolice
				WHERE ap.dt_exclusao IS NULL
				group by ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia
			) AS riscos
			ON riscos.cd_empresa = p.cd_empresa 
			AND riscos.cd_registro_empregado = p.cd_registro_empregado 
			AND riscos.seq_dependencia = p.seq_dependencia

			JOIN public.calendarios_planos cp
			ON cp.cd_empresa = p.cd_empresa

			LEFT JOIN public.contribuicoes_programadas cpr
			ON cpr.cd_empresa = p.cd_empresa
			AND cpr.cd_registro_empregado = p.cd_registro_empregado
			AND cpr.seq_dependencia = p.seq_dependencia
			AND cpr.dt_confirma_opcao IS NOT NULL
			AND cpr.dt_confirma_canc IS NULL

			LEFT JOIN titulares_planos tp
			ON tp.cd_empresa = p.cd_empresa
			AND tp.cd_registro_empregado = p.cd_registro_empregado
			AND tp.seq_dependencia = p.seq_dependencia
			AND tp.dt_ingresso_plano =
			( 
				SELECT max(tp1.dt_ingresso_plano) AS max 
				FROM titulares_planos tp1 
				WHERE tp1.cd_empresa=p.cd_empresa 
				AND tp1.cd_registro_empregado=p.cd_registro_empregado 
				AND tp1.seq_dependencia=p.seq_dependencia
			)
			
			JOIN public.titulares pt
			ON pt.cd_empresa=p.cd_empresa 
			AND pt.cd_registro_empregado=p.cd_registro_empregado 
			AND pt.seq_dependencia=p.seq_dependencia

		WHERE 

			cp.cd_empresa = {cd_empresa}
			AND cp.cd_plano = {cd_plano}

			-- MES ANO DE COMPETENCIA DEVE SER INFERIOR AO MES/ANO INFORMADO
			-- DEVE RETORNAR 5 E 2 MESES POIS QUANDO ESSA QUERY É USADA PARA GERAR COBRANÇA,
			-- OS REGISTROS DO MES ANTERIOR NÃO ESTÃO EM ATRASO
			AND DATE_TRUNC( 'DAY', cp.dt_competencia) BETWEEN ('{ano_competencia}-{mes_competencia}-01'::date - '5 months'::interval) AND ('{ano_competencia}-{mes_competencia}-01'::date - '2 month'::interval )

			AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim
			AND (  
					p.forma_pagamento = 'BDL' 
					OR (p.forma_pagamento = 'BCO' AND p.nao_desconto_primcontrib='S') 
					OR (p.forma_pagamento = 'FOL' AND p.nao_desconto_primcontrib='S')  
				)

			AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END
			
			AND pt.DT_CANCELA_INSCRICAO IS NULL
			;

		");

		$db->setParameter("{cd_empresa}", $cd_empresa);
		$db->setParameter("{cd_plano}", $cd_plano);
		$db->setParameter("{mes_competencia}", $mes);
		$db->setParameter("{ano_competencia}", $ano);

		$rows = array();
		$rows = $db->get();
		
		$db->setSQL("
		
		-- total_primeiro_pagamento_atrasado
		SELECT
		
			COUNT(*) AS quantos
			
		FROM 
		
			public.protocolos_participantes p
			JOIN public.participantes participante
			ON participante.cd_empresa=p.cd_empresa
			AND participante.cd_registro_empregado=p.cd_registro_empregado
			AND participante.seq_dependencia=p.seq_dependencia
		
			LEFT JOIN 
			(
				SELECT ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia, sum(ap.premio) as valor_risco
				FROM public.apolices_participantes ap
				JOIN public.apolices a
				ON a.cd_apolice = ap.cd_apolice
				WHERE ap.dt_exclusao IS NULL
				group by ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia
			) AS riscos
			ON riscos.cd_empresa = p.cd_empresa 
			AND riscos.cd_registro_empregado = p.cd_registro_empregado 
			AND riscos.seq_dependencia = p.seq_dependencia
		
			JOIN public.calendarios_planos cp
			ON cp.cd_empresa = p.cd_empresa
		
			LEFT JOIN public.contribuicoes_programadas cpr
			ON cpr.cd_empresa = p.cd_empresa
			AND cpr.cd_registro_empregado = p.cd_registro_empregado
			AND cpr.seq_dependencia = p.seq_dependencia
			AND cpr.dt_confirma_opcao IS NOT NULL
			AND cpr.dt_confirma_canc IS NULL
		
			LEFT JOIN titulares_planos tp
			ON tp.cd_empresa = p.cd_empresa
			AND tp.cd_registro_empregado = p.cd_registro_empregado
			AND tp.seq_dependencia = p.seq_dependencia
			AND tp.dt_ingresso_plano =
			( 
				SELECT max(tp1.dt_ingresso_plano) AS max 
				FROM titulares_planos tp1 
				WHERE tp1.cd_empresa=p.cd_empresa 
				AND tp1.cd_registro_empregado=p.cd_registro_empregado 
				AND tp1.seq_dependencia=p.seq_dependencia
			)
			
			JOIN public.titulares pt
			ON pt.cd_empresa=p.cd_empresa 
			AND pt.cd_registro_empregado=p.cd_registro_empregado 
			AND pt.seq_dependencia=p.seq_dependencia
		
		WHERE 
		
			cp.cd_empresa = {cd_empresa}
			AND cp.cd_plano = {cd_plano}
		
			-- MES ANO DE COMPETENCIA DEVE SER INFERIOR AO MES/ANO INFORMADO
			-- DEVE RETORNAR 5 E 2 MESES POIS QUANDO ESSA QUERY É USADA PARA GERAR COBRANÇA,
			-- OS REGISTROS DO MES ANTERIOR NÃO ESTÃO EM ATRASO
			AND DATE_TRUNC( 'DAY', cp.dt_competencia) BETWEEN ('{ano_competencia}-{mes_competencia}-01'::date - '5 months'::interval) AND ('{ano_competencia}-{mes_competencia}-01'::date - '2 month'::interval )
		
			AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim

			AND (  
					p.forma_pagamento = 'BDL' 
					OR (p.forma_pagamento = 'BCO' AND p.nao_desconto_primcontrib='S') 
					OR (p.forma_pagamento = 'FOL' AND p.nao_desconto_primcontrib='S')  
				)

			AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END
			
			AND COALESCE( participante.email, participante.email_profissional ) IS NOT NULL
			
			AND pt.DT_CANCELA_INSCRICAO IS NULL
			;
		
		");

		$db->setParameter("{cd_empresa}", $cd_empresa);
		$db->setParameter("{cd_plano}", $cd_plano);
		$db->setParameter("{mes_competencia}", $mes);
		$db->setParameter("{ano_competencia}", $ano);

		$rows2 = array();
		$rows2 = $db->get();

		$return['geral'] = $rows[0]['quantos'];
		$return['com_email'] = $rows2[0]['quantos'];

		return $return;
	}

	/**
	 * t_participantes::listar_sem_email_primeiro_pagamento_atrasado( $cd_empresa, $cd_plano, $mes, $ano )
	 * 
	 * Método criado para atender a uma cobrança gerada no 2º dia útil do mês
	 * quando os participantes com cobrança de primeiro pagamento do mes anterior
	 * ainda não são considerados atrasados, então o intervalo de datas da cobrança 
	 * considerados nessa consulta são de 2 a 5 meses atras
	 * 
	 * Esse método retorna a lista de participantes que não possuem email mas devem
	 * ser cobrados refentes Primeiro Pagamento
	 * 
	 * @param int $cd_empresa
	 * @param int $cd_plano
	 * @param int $mes
	 * @param int $ano
	 * 
	 * @return array( array('com_email'=>0), array('geral'=>0) )
	 * 
	 * O retorno deve ser usado da seguinte forma
	 * 
	 * $total['geral']; // total do resultado da query principal
	 * $total['com_email']; // do resultado, apenas o total de participante com email
	 * 
	 */
	public static function listar_sem_email_primeiro_pagamento( $cd_empresa, $cd_plano, $mes, $ano )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		
		$db->setSQL("

		SELECT

			participante.cd_empresa
			, participante.cd_registro_empregado
			, participante.seq_dependencia
			, participante.nome

		FROM 
		
			public.protocolos_participantes p
			JOIN public.participantes participante
			ON participante.cd_empresa=p.cd_empresa
			AND participante.cd_registro_empregado=p.cd_registro_empregado
			AND participante.seq_dependencia=p.seq_dependencia
		
			LEFT JOIN 
			(
				SELECT ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia, sum(ap.premio) as valor_risco
				FROM public.apolices_participantes ap
				JOIN public.apolices a
				ON a.cd_apolice = ap.cd_apolice
				WHERE ap.dt_exclusao IS NULL
				group by ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia
			) AS riscos
			ON riscos.cd_empresa = p.cd_empresa 
			AND riscos.cd_registro_empregado = p.cd_registro_empregado 
			AND riscos.seq_dependencia = p.seq_dependencia
		
			JOIN public.calendarios_planos cp
			ON cp.cd_empresa = p.cd_empresa
		
			LEFT JOIN public.contribuicoes_programadas cpr
			ON cpr.cd_empresa = p.cd_empresa
			AND cpr.cd_registro_empregado = p.cd_registro_empregado
			AND cpr.seq_dependencia = p.seq_dependencia
			AND cpr.dt_confirma_opcao IS NOT NULL
			AND cpr.dt_confirma_canc IS NULL
		
			LEFT JOIN titulares_planos tp
			ON tp.cd_empresa = p.cd_empresa
			AND tp.cd_registro_empregado = p.cd_registro_empregado
			AND tp.seq_dependencia = p.seq_dependencia
			AND tp.dt_ingresso_plano =
			( 
				SELECT max(tp1.dt_ingresso_plano) AS max 
				FROM titulares_planos tp1 
				WHERE tp1.cd_empresa=p.cd_empresa 
				AND tp1.cd_registro_empregado=p.cd_registro_empregado 
				AND tp1.seq_dependencia=p.seq_dependencia
			)

		WHERE 

			cp.cd_empresa = {cd_empresa}
			AND cp.cd_plano = {cd_plano}

			-- MES ANO DE COMPETENCIA DEVE SER INFERIOR AO MES/ANO INFORMADO
			-- DEVE RETORNAR 5 E 2 MESES POIS QUANDO ESSA QUERY É USADA PARA GERAR COBRANÇA,
			-- OS REGISTROS DO MES ANTERIOR NÃO ESTÃO EM ATRASO
			AND DATE_TRUNC( 'DAY', cp.dt_competencia) BETWEEN ('{ano_competencia}-{mes_competencia}-01'::date - '5 months'::interval) AND ('{ano_competencia}-{mes_competencia}-01'::date - '2 month'::interval )

			AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim
			
			AND p.forma_pagamento = 'BDL'

			AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

			AND COALESCE( participante.email, participante.email_profissional ) IS NULL
			;

		");

		$db->setParameter("{cd_empresa}", $cd_empresa);
		$db->setParameter("{cd_plano}", $cd_plano);
		$db->setParameter("{mes_competencia}", $mes);
		$db->setParameter("{ano_competencia}", $ano);

		$r = array();
		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}
}
?>