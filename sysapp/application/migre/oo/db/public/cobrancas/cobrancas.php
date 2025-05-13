<?php
class cobrancas
{
	/**
	 * titulares_planos::totais_por_lancamento_na_competencia()
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
	public static function totais_por_lancamento_na_competencia( $codigo_lancamento, $cd_empresa, $mes_competencia, $ano_competencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT

				COUNT(*) AS nr_quantidade

			FROM

				public.cobrancas c

				JOIN public.participantes p ON p.cd_empresa = c.cd_empresa
				AND p.cd_registro_empregado = c.cd_registro_empregado
				AND p.seq_dependencia = c.seq_dependencia
				AND p.cd_plano = c.cd_plano

			WHERE

				c.mes_competencia = {mes_competencia}
      			AND c.ano_competencia = {ano_competencia}
				AND c.cd_empresa={cd_empresa}
				AND c.codigo_lancamento IN ( {codigo_lancamento} )
				AND date_trunc('month', c.dt_lancamento) = (ano_competencia || '-' || mes_competencia || '-01')::date;

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{mes_competencia}", $mes_competencia );
		$db->setParameter( "{ano_competencia}", $ano_competencia );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get(true);

		return $r[0]['nr_quantidade'];
	}
	
	/**
	 * cobrancas::listar_sem_email()
	 * 
	 * Gera uma lista de participantes dentro das condições parametrizadas
	 * que não possuem email nem email profissional cadastrados
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
	public static function listar_sem_email( $codigo_lancamento, $cd_empresa, $mes_competencia, $ano_competencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT 

				par.cd_empresa, par.cd_registro_empregado, par.seq_dependencia, par.nome

			FROM 

				public.cobrancas cob
				JOIN public.participantes par 
				ON cob.cd_empresa=par.cd_empresa and cob.cd_registro_empregado=par.cd_registro_empregado and cob.seq_dependencia=par.seq_dependencia and cob.cd_plano=par.cd_plano

			WHERE

				mes_competencia = {mes_competencia}
      			AND ano_competencia = {ano_competencia}
				AND cob.cd_empresa={cd_empresa}
				AND cob.codigo_lancamento IN ( {codigo_lancamento} )
				AND date_trunc('month', cob.dt_lancamento) = (cob.ano_competencia || '-' || cob.mes_competencia || '-01')::date
				
				AND COALESCE(par.email, par.email_profissional) IS NULL;

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{mes_competencia}", $mes_competencia );
		$db->setParameter( "{ano_competencia}", $ano_competencia );
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
	 * cobrancas::totais_com_email()
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
	public static function totais_com_email( $codigo_lancamento, $cd_empresa, $mes_competencia, $ano_competencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT 

				COUNT(*) AS nr_quantidade

			FROM 

				public.cobrancas cob
				JOIN public.participantes par 
				ON cob.cd_empresa=par.cd_empresa and cob.cd_registro_empregado=par.cd_registro_empregado and cob.seq_dependencia=par.seq_dependencia and cob.cd_plano=par.cd_plano

			WHERE

				mes_competencia = {mes_competencia}
      			AND ano_competencia = {ano_competencia}
				AND cob.cd_empresa={cd_empresa}
				AND cob.codigo_lancamento IN ( {codigo_lancamento} )
				AND date_trunc('month', cob.dt_lancamento) = (cob.ano_competencia || '-' || cob.mes_competencia || '-01')::date
				
				AND COALESCE(par.email, par.email_profissional) IS NOT NULL;

		" );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{mes_competencia}", $mes_competencia );
		$db->setParameter( "{ano_competencia}", $ano_competencia );
		$db->setParameter( "{codigo_lancamento}", implode(",", $codigo_lancamento) );
		$r = $db->get();

		return $r[0]['nr_quantidade'];
	}
	
	/**
	 * titulares_planos::totais_por_lancamento_de_competencia_anterior()
	 *
	 * Retorna a quantidade de cobranças seguindo os seguintes critérios
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
	public static function totais_por_lancamento_de_competencia_anterior( $codigo_lancamento, $cd_empresa, $mes_competencia, $ano_competencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		/*
		 * Retorna a quantidade de cobranças seguindo os seguintes critérios
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
		 */
		$db->setSQL( "

			SELECT 

				COUNT(*) AS nr_quantidade

			FROM 

				public.cobrancas

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
}