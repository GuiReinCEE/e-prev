<?php
class atividades
{
	/**
	 * Retorna uma coleção
	 * 
	 * @return e_atividades_collection
	 */
	static function select($where=array())
	{
		return t_atividades::select($where);
	}
	
	/**
	 * Retorna uma coleção
	 * 
	 * @return e_atividades_collection
	 */
	static function select_by_limite($where)
	{
		return t_atividades::atividades_by_limite($where);
	}
	
	/**
	 * Consulta de atividades por mes x gerencia
	 * 
	 * @param $ano
	 * @param $gerencia Sigla da gerencia, criado originalmente para uso da GRI
	 * 
	 * @return array(array()) Coleção com resultado
	 */
	public static function select_01($ano, $gerencia="GRI")
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT 
			mesano.mes
			, t1.divisao
			, coalesce( t1.total_mes_divisao, 0 ) as total_mes_divisao
			, coalesce( t2.total_mes , 0 ) as total_mes
			, ((t1.total_mes_divisao*100)/t2.total_mes) AS percentual
			FROM
			(
				SELECT trim(to_char(mes, '00')) || '/' || ano as mes, ano || trim(to_char(mes, '00')) as mes_invert
				FROM generate_series({ano}, {ano}) AS ano, generate_series(1,12) AS mes
			) AS mesano
			LEFT JOIN
			(
				SELECT to_char( dt_limite, 'MM/YYYY' ) AS mes, 
				       to_char( dt_limite, 'YYYY-MM' ) AS mes_invert, 
				       divisao, count(*) as total_mes_divisao
				  FROM projetos.atividades AS pa
				 WHERE EXTRACT('year' FROM pa.dt_limite) = {ano}
				   AND area='GRI'
				   AND 
				   (
					dt_fim_real IS NOT NULL
					OR ( dt_fim_real IS NULL AND dt_limite<CURRENT_DATE )
				   )
			      GROUP BY to_char( dt_limite, 'MM/YYYY' ), to_char( dt_limite, 'YYYY-MM' ), divisao
			      ORDER BY to_char( dt_limite, 'YYYY-MM' ), divisao
			) AS t1
			ON mesano.mes = t1.mes
			LEFT JOIN
			(
				SELECT to_char( dt_limite, 'MM/YYYY' ) AS mes, 
				       count(*) as total_mes
				  FROM projetos.atividades AS pa
				 WHERE EXTRACT('year' FROM pa.dt_limite) = {ano}
				   AND area='{area}'
				   AND 
				   (
					dt_fim_real IS NOT NULL
					OR ( dt_fim_real IS NULL AND dt_limite<CURRENT_DATE )
				   )
			      GROUP BY to_char( dt_limite, 'MM/YYYY' )
			      ORDER BY to_char( dt_limite, 'MM/YYYY' )
			) AS t2
			ON t1.mes = t2.mes
			ORDER BY mesano.mes_invert, t1.divisao
			;

		" );
		
		$db->setParameter("{ano}", $ano);
		$db->setParameter("{area}", $gerencia);

		$r = $db->get();
		$collection = array();

		foreach( $r as $item )
		{
			$collection[sizeof($collection)] = $item;
		}

		return $collection;
	}
	
	/**
	 * Consulta de atividades relacionadas a rateios por programa listando:
	 * - quantidade de atividades por programa
	 * - quantidade de dias para atendimento por programa
	 * uso exclusivo da GRI (rateios por programa até hoje 10/12/2008 só para gri)
	 * 
	 * @param $ano
	 * 
	 * @return array(array()) Coleção com resultado
	 */
	public static function select_02($ano)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT pl.descricao AS programa, 
				   SUM( par.nr_percentual/100 ) AS quantidade, 
				   SUM(  (EXTRACT(DAYS FROM ( pa.dt_fim_real-pa.dt_cad ) ) + EXTRACT(HOURS FROM ( pa.dt_fim_real-pa.dt_cad ) ) / 24 ) * (par.nr_percentual / 100 )  ) AS dias 
			  FROM projetos.atividades pa
			  JOIN projetos.atividade_rateio par 
			    ON pa.numero=par.cd_atividade
			  JOIN public.listas pl
			    ON par.cd_listas_programa=pl.codigo AND categoria='PRFC'
			 WHERE EXTRACT('year' FROM pa.dt_cad) = {ano}
			   AND area='GRI'
			   AND dt_fim_real IS NOT NULL
		  GROUP BY pl.descricao
		  ORDER BY pl.descricao
		  ;

		" );
		
		$db->setParameter("{ano}", $ano);

		$r = $db->get();
		$collection = array();

		foreach( $r as $item )
		{
			$collection[sizeof($collection)] = $item;
		}

		return $collection;
	}
}
?>