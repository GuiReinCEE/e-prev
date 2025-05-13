<?php
class resumo_atividades_gri_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

    function listar( &$result, $args=array() )
	{
        $sql = "
                SELECT mesano.ano,
                       mesano.mes,
                       COALESCE(abertas.qt_abertas, 0) as abertas,
                       COALESCE(solicitadas.qt_solicitadas, 0) as solicitadas,
                       COALESCE(atendidas_no_prazo.qt_atendidas_no_prazo, 0) AS atendidas_no_prazo,
                       COALESCE(solicitadas.qt_solicitadas, 0) - COALESCE(atendidas_no_prazo.qt_atendidas_no_prazo, 0) AS atendidas_fora_prazo
				  FROM
					 (
                     SELECT ano,
                            mes
					   FROM generate_series(".intval($args['ano']).", ".intval($args['ano']).") AS ano,
                            generate_series(1,12) AS mes
					 )
                    AS mesano
             LEFT JOIN
			-- ABERTAS
                     (
					 SELECT extract(year from dt_cad) AS dt_ano,
                            extract(month from dt_cad) as dt_mes,
                            count(*) AS qt_abertas
                       FROM projetos.atividades
                      WHERE extract(year from dt_cad) = ".intval($args['ano'])."
                        AND area = 'GRI'
                        AND tipo <> 'L'
                      GROUP BY dt_ano, dt_mes
					 )
                    AS abertas
				    ON abertas.dt_ano = mesano.ano AND abertas.dt_mes = mesano.mes
             LEFT JOIN
	    -- SOLICITADAS
					 (
					 SELECT extract(year from dt_limite) AS dt_ano,
                            EXTRACT(MONTH from dt_limite) as dt_mes,
                            COUNT(*) AS qt_solicitadas
                       FROM projetos.atividades
                      WHERE extract(year from dt_limite) = ".intval($args['ano'])."
                        AND area = 'GRI'
                        AND tipo <> 'L'
                      GROUP BY dt_ano, dt_mes
					 )
                    AS solicitadas
                    ON solicitadas.dt_ano = mesano.ano AND solicitadas.dt_mes = mesano.mes
             LEFT JOIN
 -- ATENDIDAS NO PRAZO
					 (
					 SELECT EXTRACT(YEAR FROM dt_limite) AS dt_ano,
                            EXTRACT(month FROM dt_limite) AS dt_mes,
                            COUNT(*) AS qt_atendidas_no_prazo
					   FROM projetos.atividades
					  WHERE dt_limite >= DATE_TRUNC( 'day', COALESCE(dt_fim_real, CURRENT_TIMESTAMP) )
					    AND area = 'GRI'
					    AND tipo <> 'L'
					  GROUP BY dt_ano, dt_mes
					 )
                    AS atendidas_no_prazo
				    ON atendidas_no_prazo.dt_ano = mesano.ano
                   AND atendidas_no_prazo.dt_mes = mesano.mes
            ";

       # echo "<pre>".$sql."</pre><br/>";

      //  exit;
        $result = $this->db->query($sql);
    }

    function listaAnoAnterior(&$result, $args=array())
    {
        $sql = "
            SELECT
                 (
                 SELECT count(*) as qt_abertas
		           FROM projetos.atividades
		          WHERE extract(year from dt_cad) < ".intval($args['ano'])."
		            AND area = 'GRI'
		            AND tipo <> 'L'
                 )
                as abertas_anterior,
                 (
                 SELECT count(*) as qt_solicitadas
	               FROM projetos.atividades
	              WHERE extract(year from dt_limite) < ".intval($args['ano'])."
	                AND area = 'GRI'
	                AND tipo <> 'L'
                 )
                as solicitadas_anterior,
                 (
                 SELECT count(*) as qt_atendidas
			       FROM projetos.atividades
			      WHERE extract(year from dt_limite) < ".intval($args['ano'])."
			        AND dt_limite >= DATE_TRUNC('day', COALESCE(dt_fim_real, CURRENT_TIMESTAMP))
					AND area = 'GRI'
					AND tipo <> 'L'
                 )
                as atendidas_no_prazo_anterior
        ";

        #echo "<pre>".$sql."</pre><br/>";
        $result = $this->db->query($sql);
    }

    function carregaAtividades(&$result, $args=array())
    {
        $sql = "
            SELECT a.numero,
                   u.guerra,
                   l.descricao as status_descricao,
                   a.titulo as atividade_titulo,
                   TO_CHAR( a.dt_limite , 'DD/MM/YYYY' ) as dt_limite,
                   TO_CHAR( a.dt_limite_testes , 'DD/MM/YYYY' ) as dt_limite_testes,
                   TO_CHAR( a.dt_fim_real , 'DD/MM/YYYY' ) as dt_fim_real
		      FROM projetos.atividades a
			  JOIN projetos.usuarios_controledi u ON u.codigo=a.cod_atendente
			  JOIN public.listas l ON a.status_atual=l.codigo
		     WHERE extract( 'month' from A.dt_limite )=".intval($args['mes'])."
			   AND extract('year' from A.dt_limite)=".intval($args['ano'])."
			   AND area='GRI'
		     ORDER BY numero ASC;
          ";

        $result = $this->db->query($sql);

    }

    function listaAtendimentos(&$result, $args=array())
    {
        $sql = "
            SELECT mesano.mes,
                   t1.divisao,
                   coalesce( t1.total_mes_divisao, 0 ) as total_mes_divisao,
                   coalesce( t2.total_mes , 0 ) as total_mes,
                   ((t1.total_mes_divisao*100)/t2.total_mes) AS percentual
			  FROM
			     (
				 SELECT trim(to_char(mes, '00')) || '/' || ano as mes,
                        ano || trim(to_char(mes, '00')) as mes_invert
				   FROM generate_series(".intval($args['ano']).", ".intval($args['ano']).") AS ano,
                        generate_series(1,12) AS mes
			     )
                AS mesano
		 LEFT JOIN
			     (
				 SELECT to_char( dt_limite, 'MM/YYYY' ) AS mes,
				        to_char( dt_limite, 'YYYY-MM' ) AS mes_invert,
				        divisao, count(*) as total_mes_divisao
				   FROM projetos.atividades AS pa
				  WHERE EXTRACT('year' FROM pa.dt_limite) = ".intval($args['ano'])."
				    AND area='GRI'
				    AND
				      (
					  dt_fim_real IS NOT NULL
					  OR ( dt_fim_real IS NULL AND dt_limite<CURRENT_DATE )
				      )
			      GROUP BY to_char( dt_limite, 'MM/YYYY' ), to_char( dt_limite, 'YYYY-MM' ), divisao
			      ORDER BY to_char( dt_limite, 'YYYY-MM' ), divisao
			      )
                 AS t1
			     ON mesano.mes = t1.mes
		  LEFT JOIN
			      (
				  SELECT to_char( dt_limite, 'MM/YYYY' ) AS mes,
				         count(*) as total_mes
				    FROM projetos.atividades AS pa
				   WHERE EXTRACT('year' FROM pa.dt_limite) = ".intval($args['ano'])."
				     AND area='GRI'
				     AND
				       (
					   dt_fim_real IS NOT NULL
					   OR ( dt_fim_real IS NULL AND dt_limite<CURRENT_DATE )
				       )
			       GROUP BY to_char( dt_limite, 'MM/YYYY' )
			       ORDER BY to_char( dt_limite, 'MM/YYYY' )
			       )
                  AS t2
			    ON t1.mes = t2.mes
			 ORDER BY mesano.mes_invert, t1.divisao
            ";
            $result = $this->db->query($sql);
    }

    function listaProgramas(&$result, $args=array())
    {
        $sql = "
            SELECT pl.descricao AS programa,
				   SUM( par.nr_percentual/100 ) AS quantidade,
				   SUM( (EXTRACT(DAYS FROM ( pa.dt_fim_real-pa.dt_cad ) ) + EXTRACT(HOURS FROM ( pa.dt_fim_real-pa.dt_cad ) ) / 24 ) * (par.nr_percentual / 100 )  ) AS dias
			  FROM projetos.atividades pa
			  JOIN projetos.atividade_rateio par
			    ON pa.numero=par.cd_atividade
			  JOIN public.listas pl
			    ON par.cd_listas_programa=pl.codigo AND categoria='PRFC'
			 WHERE EXTRACT('year' FROM pa.dt_cad) = ".intval($args['ano'])."
			   AND area='GRI'
			   AND dt_fim_real IS NOT NULL
		     GROUP BY pl.descricao
		     ORDER BY pl.descricao
            ";
        $result = $this->db->query($sql);
    }


}
