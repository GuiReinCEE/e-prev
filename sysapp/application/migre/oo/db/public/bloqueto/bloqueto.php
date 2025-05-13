<?php
class bloqueto
{
	/**
	 * bloqueto::total_email_enviar()
	 *
	 * Retorna a quantidade de bloquetos atrasados com participantes que tem email 
	 * com base na data de lançamento informada (mes/ano)
	 * 
	 * Critérios da seleção:
	 * 
	 * - Competências sem pagamento
	 * status IS NULL AND dt_retorno IS NULL
	 * 
	 * - Competências anteriores ao mes e ano de lançamento informados
	 * AND to_date( ano_competencia::varchar || '/' || mes_competencia::varchar || '/01' , 'YYYY/MM/DD' ) < to_date( '{ano_lancamento}/{mes_lancamento}/01' , 'YYYY/MM/DD' )
	 * 
	 * - Para empresa informada
	 * AND cd_empresa = {cd_empresa}
	 * 
	 * - No código de lançamento informado
	 * AND codigo_lancamento IN ( {codigo_lancamento} )
	 * 
	 * - No Mês e ano de lançamento informados
	 * AND date_trunc('month', dt_lancamento) = '{ano_lancamento}-{mes_lancamento}-01'
	 * 
	 * - Participantes com email preenchido
	 * AND COALESCE(part.email, part.email_profissional) IS NOT NULL
	 * 
	 * -------------------------------
	 * 
	 * @param array() @codigo_lacamento Array com Códigos de lançamento (public.codigos_cobranca)
	 * @param int @cd_empresa Código da empresa
	 * @param int @mes Mês de lançamento Mês da dt_lancamento
	 * @param int @ano Ano de lançamento Ano da dt_lancamento
	 * 
	 * @return int Quantidade
	 */
	public static function total_email_enviar( $codigo_lancamento, $cd_empresa, $mes_lancamento, $ano_lancamento )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT COUNT(*) AS nr_quantidade FROM
				(
				SELECT 
					DISTINCT bloq.cd_empresa, bloq.cd_registro_empregado, bloq.seq_dependencia
				FROM 
					
					public.bloqueto as bloq
					
					JOIN public.participantes AS part 
					ON bloq.cd_empresa=part.cd_empresa 
					AND bloq.cd_registro_empregado=part.cd_registro_empregado 
					AND bloq.seq_dependencia=part.seq_dependencia
					 
				WHERE
					
					bloq.status IS NULL 
					AND bloq.data_retorno IS NULL
					AND to_date( bloq.ano_competencia::varchar || '/' || bloq.mes_competencia::varchar || '/01' , 'YYYY/MM/DD' ) < to_date( '{ano_lancamento}/{mes_lancamento}/01' , 'YYYY/MM/DD' )
					AND bloq.cd_empresa = {cd_empresa}
					AND bloq.codigo_lancamento IN ( {codigo_lancamento} )
					AND date_trunc('month', bloq.dt_lancamento) = '{ano_lancamento}-{mes_lancamento}-01'
					AND COALESCE(part.email, part.email_profissional) IS NOT NULL
				) 
			AS atrasos

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{mes_lancamento}", $mes_lancamento );
		$db->setParameter( "{ano_lancamento}", $ano_lancamento );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get();

		return $r[0]['nr_quantidade'];
	}

	/**
	 * bloqueto::listar_sem_email()
	 *
	 * Retorna a lista de participantes sem email para bloquetos atrasados
	 * com base na data de lançamento informada (mes/ano)
	 * 
	 * Critérios da seleção:
	 * 
	 * - Competências sem pagamento
	 * status IS NULL AND dt_retorno IS NULL
	 * 
	 * - Competências anteriores ao mes e ano de lançamento informados
	 * AND to_date( ano_competencia::varchar || '/' || mes_competencia::varchar || '/01' , 'YYYY/MM/DD' ) < to_date( '{ano_lancamento}/{mes_lancamento}/01' , 'YYYY/MM/DD' )
	 * 
	 * - Para empresa informada
	 * AND cd_empresa = {cd_empresa}
	 * 
	 * - No código de lançamento informado
	 * AND codigo_lancamento IN ( {codigo_lancamento} )
	 * 
	 * - No Mês e ano de lançamento informados
	 * AND date_trunc('month', dt_lancamento) = '{ano_lancamento}-{mes_lancamento}-01'
	 * 
	 * - Apenas Participantes sem email
	 * AND COALESCE(part.email, part.email_profissional) IS NULL
	 * 
	 * -------------------------------
	 * 
	 * @param array() @codigo_lacamento Array com Códigos de lançamento (public.codigos_cobranca)
	 * @param int @cd_empresa Código da empresa
	 * @param int @mes Mês de lançamento Mês da dt_lancamento
	 * @param int @ano Ano de lançamento Ano da dt_lancamento
	 * 
	 * @return int Quantidade
	 */
	public static function listar_sem_email( $codigo_lancamento, $cd_empresa, $mes_lancamento, $ano_lancamento )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT * FROM
				(
				SELECT 
					DISTINCT bloq.cd_empresa, bloq.cd_registro_empregado, bloq.seq_dependencia, part.nome
				FROM 
					
					public.bloqueto as bloq
					
					JOIN public.participantes AS part 
					ON bloq.cd_empresa=part.cd_empresa 
					AND bloq.cd_registro_empregado=part.cd_registro_empregado 
					AND bloq.seq_dependencia=part.seq_dependencia
					 
				WHERE
					
					bloq.status IS NULL 
					AND bloq.data_retorno IS NULL
					AND to_date( bloq.ano_competencia::varchar || '/' || bloq.mes_competencia::varchar || '/01' , 'YYYY/MM/DD' ) < to_date( '{ano_lancamento}/{mes_lancamento}/01' , 'YYYY/MM/DD' )
					AND bloq.cd_empresa = {cd_empresa}
					AND bloq.codigo_lancamento IN ( {codigo_lancamento} )
					AND date_trunc('month', bloq.dt_lancamento) = '{ano_lancamento}-{mes_lancamento}-01'
					AND COALESCE(part.email, part.email_profissional) IS NULL
				) 
			AS atrasos

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{mes_lancamento}", $mes_lancamento );
		$db->setParameter( "{ano_lancamento}", $ano_lancamento );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}

	public static function totais_atraso_anterior( $codigo_lancamento, $cd_empresa, $mes_lancamento, $ano_lancamento )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL("

			SELECT extract( 'month' from '{ano_lancamento}-{mes_lancamento}-01'::date - '1 month'::interval ) as mes
				 , extract( 'year' from '{ano_lancamento}-{mes_lancamento}-01'::date - '1 month'::interval ) as ano

		");
		$db->setParameter("{ano_lancamento}", $ano_lancamento);
		$db->setParameter("{mes_lancamento}", $mes_lancamento);
		$foo = $db->get();

		$mes_lancamento_anterior = $foo[0]['mes'];
		$ano_lancamento_anterior = $foo[0]['ano'];
		return bloqueto::totais_em_atraso( $codigo_lancamento, $cd_empresa, $mes_lancamento_anterior, $ano_lancamento_anterior );
	}

	/**
	 * bloqueto::totais_em_atraso()
	 *
	 * Retorna a quantidade de bloquetos atrasados com base na data de lançamento informada (mes/ano)
	 * 
	 * Critérios da seleção:
	 * 
	 * - Competências sem pagamento
	 * status IS NULL AND dt_retorno IS NULL
	 * 
	 * - Competências anteriores ao mes e ano de lançamento informados
	 * AND to_date( ano_competencia::varchar || '/' || mes_competencia::varchar || '/01' , 'YYYY/MM/DD' ) < to_date( '{ano_lancamento}/{mes_lancamento}/01' , 'YYYY/MM/DD' )
	 * 
	 * - Para empresa informada
	 * AND cd_empresa = {cd_empresa}
	 * 
	 * - No código de lançamento informado
	 * AND codigo_lancamento IN ( {codigo_lancamento} )
	 * 
	 * - No Mês e ano de lançamento informados
	 * AND date_trunc('month', dt_lancamento) = '{ano_lancamento}-{mes_lancamento}-01'
	 * 
	 * -------------------------------
	 * 
	 * @param array() @codigo_lacamento Array com Códigos de lançamento (public.codigos_cobranca)
	 * @param int @cd_empresa Código da empresa
	 * @param int @mes Mês de lançamento Mês da dt_lancamento
	 * @param int @ano Ano de lançamento Ano da dt_lancamento
	 * 
	 * @return int Quantidade
	 */
	public static function totais_em_atraso( $codigo_lancamento, $cd_empresa, $mes_lancamento, $ano_lancamento )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

		SELECT COUNT(*) AS nr_quantidade FROM
			(
			SELECT 
				DISTINCT cd_empresa, cd_registro_empregado, seq_dependencia
			FROM 
				public.bloqueto
			WHERE
				status IS NULL 
				AND data_retorno IS NULL
				AND to_date( ano_competencia::varchar || '/' || mes_competencia::varchar || '/01' , 'YYYY/MM/DD' ) < to_date( '{ano_lancamento}/{mes_lancamento}/01' , 'YYYY/MM/DD' )
				AND cd_empresa = {cd_empresa}
				AND codigo_lancamento IN ( {codigo_lancamento} )
				AND date_trunc('month', dt_lancamento) = '{ano_lancamento}-{mes_lancamento}-01'
			) 
		AS atrasos

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{mes_lancamento}", $mes_lancamento );
		$db->setParameter( "{ano_lancamento}", $ano_lancamento );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get();

		return $r[0]['nr_quantidade'];
	}

	/**
	 * bloqueto::totais_na_competencia_anterior()
	 *
	 * Retorna a quantidade de bloquetos seguindo os seguintes critérios
	 * 
	 * Todos registros do Mês de competência anterior 
	 * mes_competencia = extract( 'months'  from to_date( '{ano_competencia}/{mes_competencia}/01' , 'YYYY/MM/DD' ) - '1 month'::interval )
	 * 
	 * Todos registros do Ano de competência anterior
	 * ano_competencia = extract( 'years'  from to_date( '{ano_competencia}/{mes_competencia}/01' , 'YYYY/MM/DD' ) - '1 month'::interval )
	 * 
	 * Todos registros da empresa do parametro
	 * cd_empresa={cd_empresa}
	 * 
	 * Todos os registros do código de lançamento do parametro
	 * codigo_lancamento IN ( {codigo_lancamento} )
	 * 
	 * Todos os registros onde a data de lançamento está dentro do Mes/Ano de competência
	 * date_trunc('month', dt_lancamento) = (ano_competencia || '-' || mes_competencia || '-01')::date
	 * 
	 * -------------------------------
	 * 
	 * @param array() @codigo_lacamento Array com Códigos de lançamento (public.codigos_cobranca)
	 * @param int @cd_empresa Código da empresa
	 * @param int @mes Mês de competência atual para que método busque a anterior
	 * @param int @ano Ano de competência atual para que método busque a anterior
	 * 
	 * @return int Quantidade
	 */
	public static function totais_na_competencia_anterior( $codigo_lancamento, $cd_empresa, $mes_competencia, $ano_competencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		/* Retorna a quantidade de bloquetos seguindo os seguintes critérios
		 * 
		 * Todos registros do Mês de competência anterior 
		 * mes_competencia = extract( 'months'  from to_date( '{ano_competencia}/{mes_competencia}/01' , 'YYYY/MM/DD' ) - '1 month'::interval )
		 * 
		 * Todos registros do Ano de competência anterior
		 * ano_competencia = extract( 'years'  from to_date( '{ano_competencia}/{mes_competencia}/01' , 'YYYY/MM/DD' ) - '1 month'::interval )
		 * 
		 * Todos registros da empresa do parametro
		 * cd_empresa={cd_empresa}
		 * 
		 * Todos os registros do código de lançamento do parametro
		 * codigo_lancamento IN ( {codigo_lancamento} )
		 * 
		 * Todos os registros onde a data de lançamento está dentro do Mes/Ano de competência
		 * date_trunc('month', dt_lancamento) = (ano_competencia || '-' || mes_competencia || '-01')::date
		 */
		$db->setSQL( "

			SELECT 
				COUNT(*) AS nr_quantidade
			FROM 
				public.bloqueto
			WHERE
				mes_competencia = extract( 'months'  from to_date( '{ano_competencia}/{mes_competencia}/01' , 'YYYY/MM/DD' ) - '1 month'::interval )
      			AND ano_competencia = extract( 'years'  from to_date( '{ano_competencia}/{mes_competencia}/01' , 'YYYY/MM/DD' ) - '1 month'::interval )
				AND cd_empresa={cd_empresa}
				AND codigo_lancamento IN ( {codigo_lancamento} )
				AND date_trunc('month', dt_lancamento) = (ano_competencia || '-' || mes_competencia || '-01')::date
				;

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{mes_competencia}", $mes_competencia );
		$db->setParameter( "{ano_competencia}", $ano_competencia );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get();

		return $r[0]['nr_quantidade'];
	}

	/**
	 * Lista de dados para pagamento em atraso do BDL
	 */
	public static function pagamento_bdl_atraso($codigo_lancamento, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			-- COMPETÊNCIAS EM ATRASO PARA PAGAMENTO
			SELECT
				SUM( valor_lancamento ) as valor_lancamento,
				SUM( CASE WHEN CURRENT_DATE <= dt_limite_sem_encargos THEN 0 ELSE vlr_encargo END ) AS vlr_encargo
			FROM 
				public.bloqueto bloq
			WHERE
				-- BDL E RISCOS
				codigo_lancamento IN ( {codigo_lancamento} )

				AND cd_registro_empregado = {cd_registro_empregado}
				AND cd_empresa = {cd_empresa}
				AND seq_dependencia = {seq_dependencia}

				-- ANO/MES DE COMPETENCIA NÃO PAGA ANTERIOR AO ÚLTIMO
				AND to_date(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD') < (
					SELECT MAX( TO_DATE(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD') )
					FROM public.bloqueto
					WHERE public.bloqueto.cd_empresa=bloq.cd_empresa
					AND public.bloqueto.cd_registro_empregado=bloq.cd_registro_empregado
					AND public.bloqueto.seq_dependencia=bloq.seq_dependencia
					AND public.bloqueto.status IS NULL 
					AND public.bloqueto.data_retorno IS NULL
					AND public.bloqueto.codigo_lancamento IN ( {codigo_lancamento} )
				)

				AND status IS NULL
				AND data_retorno IS NULL

				-- ULTIMO LANÇAMENTO (ULTIMA GERAÇÃO)
				AND dt_lancamento = (
					SELECT MAX(dt_lancamento) 
					FROM public.bloqueto 
					WHERE cd_empresa=bloq.cd_empresa 
					AND cd_registro_empregado=bloq.cd_registro_empregado 
					AND seq_dependencia=bloq.seq_dependencia
				)

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{cd_registro_empregado}", $cd_registro_empregado );
		$db->setParameter( "{seq_dependencia}", $seq_dependencia );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}

	/**
	 * Lista de dados para pagamento em atraso do BDL
	 * 
	 * @param string $codigo_lancamento Códigos separados por vírgula. Devem ser os códigos de lançamento para BDL e os RISCOS
	 * @param int $cd_empresa
	 * @param int $cd_registro_empregado
	 * @param int $seq_dependencia
	 * 
	 * @return array(array()) Coleção de resultados
	 */
	public static function pagamento_bdl_em_dia($codigo_lancamento, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			-- COMPETÊNCIAS EM DIA
			SELECT
				SUM( valor_lancamento ) as valor_lancamento, 
				SUM( CASE WHEN CURRENT_DATE <= dt_limite_sem_encargos THEN 0 ELSE vlr_encargo END ) AS vlr_encargo
			FROM 
				public.bloqueto bloq
			WHERE
				-- BDL E RISCOS
				codigo_lancamento IN ( {codigo_lancamento} )

				AND cd_registro_empregado = {cd_registro_empregado}
				AND cd_empresa = {cd_empresa}
				AND seq_dependencia = {seq_dependencia}

				-- MAIOR ANO/MES DE COMPETENCIA NÃO PAGA
				AND to_date(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD') = (
					SELECT MAX( TO_DATE(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD') )
					FROM public.bloqueto
					WHERE public.bloqueto.cd_empresa=bloq.cd_empresa
					AND public.bloqueto.cd_registro_empregado=bloq.cd_registro_empregado
					AND public.bloqueto.seq_dependencia=bloq.seq_dependencia
					AND public.bloqueto.status IS NULL 
					AND public.bloqueto.data_retorno IS NULL
					AND public.bloqueto.codigo_lancamento IN ( {codigo_lancamento} )
				)

				AND status IS NULL
				AND data_retorno IS NULL

				-- ULTIMO LANÇAMENTO (ULTIMA GERAÇÃO)
				AND dt_lancamento = (
					SELECT MAX(dt_lancamento) 
					FROM public.bloqueto 
					WHERE cd_empresa=bloq.cd_empresa 
					AND cd_registro_empregado=bloq.cd_registro_empregado 
					AND seq_dependencia=bloq.seq_dependencia
				)

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{cd_registro_empregado}", $cd_registro_empregado );
		$db->setParameter( "{seq_dependencia}", $seq_dependencia );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}
	
	public static function existe_em_aberto( $cd_empresa, $cd_registro_empregado, $seq_dependencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT
				COUNT(*) AS quantos
			FROM 
				public.bloqueto bloq
			WHERE
				    cd_registro_empregado = {cd_registro_empregado}
				AND cd_empresa = {cd_empresa}
				AND seq_dependencia = {seq_dependencia}
				
				AND status IS NULL
				AND data_retorno IS NULL
				
				AND dt_lancamento = ( 		SELECT MAX(b2.dt_lancamento) 
											  FROM bloqueto b2 
											 WHERE b2.seq_dependencia       = 0 
											   AND b2.cd_registro_empregado = bloq.cd_registro_empregado 
											   AND b2.cd_empresa            = bloq.cd_empresa 
											   AND b2.seq_dependencia       = bloq.seq_dependencia );
				;
				
		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{cd_registro_empregado}", $cd_registro_empregado );
		$db->setParameter( "{seq_dependencia}", $seq_dependencia );
		$r = $db->get();

		return ( $r[0]['quantos']>0 );
	}
}