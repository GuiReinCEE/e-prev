<?php
class ADO_projetos_atividades
{
    // DAL
    private $db;
    private $dal;

    function ADO_projetos_atividades( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct()
    {
        $this->dal = null;
    } 

    /**
     * Cria array de objetos da estrutura de atividade
     * 
     * @return Array[] Coleção de objetos de atividade
     */
    public function fetch_all()
    {
        return null;
    }

    /**
     * ADO_projetos_atividades.fetch_atividades_totais_mes_ano()
     * Consultar totalizadores de atividades por mes e ano de ano determinado por parametro
     * 
     * @param int $ano Ano base para totalizadores
     * @param string @divisao Divisão dos resultados
     * 
     * @return Array()helper_projetos_atividades_agrupador_mes_ano  
     */
    public function fetch_atividades_totais_mes_ano($ano, $divisao)
    {
        $this->dal->createQuery( "

                SELECT 

					mesano.ano
                    , mesano.mes
                    , COALESCE(abertas.qt_abertas, 0) as abertas
                    , COALESCE(solicitadas.qt_solicitadas, 0) as solicitadas
                    , COALESCE(atendidas_no_prazo.qt_atendidas_no_prazo, 0) AS atendidas_no_prazo
					, COALESCE(solicitadas.qt_solicitadas, 0) - COALESCE(atendidas_no_prazo.qt_atendidas_no_prazo, 0) AS atendidas_fora_prazo

				FROM

					(
					SELECT ano, mes
					FROM generate_series({ano}, {ano}) AS ano, generate_series(1,12) AS mes
					) AS mesano

                LEFT JOIN

					-- ABERTAS

                    (
					SELECT extract(year from dt_cad) AS dt_ano
                    , extract(month from dt_cad) as dt_mes 
                    , count(*) AS qt_abertas
                    FROM projetos.atividades
                    WHERE extract(year from dt_cad) = {ano}
                    AND area = '{divisao}'
                    AND tipo <> 'L'
                    GROUP BY dt_ano, dt_mes
					) AS abertas

				ON abertas.dt_ano = mesano.ano AND abertas.dt_mes = mesano.mes

                LEFT JOIN

					-- SOLICITADAS

					(
					SELECT extract(year from dt_limite) AS dt_ano
                    , EXTRACT(MONTH from dt_limite) as dt_mes 
                    , COUNT(*) AS qt_solicitadas
                    FROM projetos.atividades
                    WHERE extract(year from dt_limite) = {ano}
                    AND area = '{divisao}'
                    AND tipo <> 'L'
                    GROUP BY dt_ano, dt_mes
					) AS solicitadas

                ON solicitadas.dt_ano = mesano.ano AND solicitadas.dt_mes = mesano.mes

                LEFT JOIN

					-- ATENDIDAS NO PRAZO

					(
					SELECT EXTRACT(YEAR FROM dt_limite) AS dt_ano
					, EXTRACT(month FROM dt_limite) AS dt_mes
					, COUNT(*) AS qt_atendidas_no_prazo
					FROM projetos.atividades
					WHERE dt_limite >= DATE_TRUNC( 'day', COALESCE(dt_fim_real, CURRENT_TIMESTAMP) )
					AND area = '{divisao}'
					AND tipo <> 'L'
					GROUP BY dt_ano, dt_mes
					) AS atendidas_no_prazo

				ON atendidas_no_prazo.dt_ano = mesano.ano AND atendidas_no_prazo.dt_mes = mesano.mes

        
        " );
        $this->dal->setAttribute( "{ano}", (int)$ano );
        $this->dal->setAttribute( "{divisao}", $divisao );
        $rs = $this->dal->getResultset();
        
        $this->dal->createQuery("
                SELECT 
                      (SELECT count(*) as qt_abertas
		                    FROM projetos.atividades
		                    WHERE extract(year from dt_cad) < {ano}
		                    AND area = '{divisao}'
		                    AND tipo <> 'L'
                          ) as abertas
                ,
                    (SELECT count(*) as qt_solicitadas
	                    FROM projetos.atividades
	                    WHERE extract(year from dt_limite) < {ano}
	                    AND area = '{divisao}'
	                    AND tipo <> 'L'
                          ) as solicitadas
                ,
                    (SELECT count(*) as qt_atendidas
						FROM projetos.atividades
						WHERE extract(year from dt_limite) < {ano}
						AND dt_limite >= DATE_TRUNC('day', COALESCE(dt_fim_real, CURRENT_TIMESTAMP))
						AND area = '{divisao}'
						AND tipo <> 'L'
                          ) as atendidas_no_prazo
                
        ");
        $this->dal->setAttribute( "{ano}", (int)$ano );
        $this->dal->setAttribute( "{divisao}", $divisao );

        $rstotais = $this->dal->getResultset();

        if( $rwtotais=pg_fetch_array($rstotais) )
        {
            $helper = new helper_projetos_atividades_agrupador_mes_ano();
            $helper->ano = $ano-1;
            
            $helper->abertas = $rwtotais["abertas"];
            $helper->solicitadas = $rwtotais["solicitadas"];
            $helper->atendidas_no_prazo = $rwtotais["atendidas_no_prazo"];
            $helper->atendidas_fora_prazo = intval( $rwtotais["solicitadas"] ) - intval( $rwtotais["atendidas_no_prazo"] );
            
            $resultado[0] = $helper;
        }
        while($rw=pg_fetch_array($rs))
        {
            $helper = new helper_projetos_atividades_agrupador_mes_ano();
            $helper->mes = $rw["mes"];
            $helper->ano = $rw["ano"];
            
            $helper->abertas = $rw["abertas"];
            $helper->solicitadas = $rw["solicitadas"];
            $helper->atendidas_no_prazo = $rw["atendidas_no_prazo"];
            $helper->atendidas_fora_prazo = $rw["atendidas_fora_prazo"];
            
            $resultado[$rw["mes"]] = $helper;
        }

        return $resultado;
    }

    /**
     * ADO_projetos_atividades.fetch_menor_ano_atividade()
     * Retorna menor ano de cadastro entre os registros de atividade
     */
    public function fetch_menor_ano_atividade($divisao)
    {
        $array_return = null;
        $ret = "0";

        $this->dal->createQuery( "

                  SELECT MIN(EXTRACT(YEAR FROM dt_cad)) AS ano
                    FROM projetos.atividades
                   WHERE area='{divisao}'
                     AND tipo <> 'L'

        " );
        $this->dal->setAttribute( '{divisao}', $divisao );
        $rs = $this->dal->getResultset();

        if( $rw = pg_fetch_array($rs) )
        {
            $ret = $rw["ano"];
        }
        else
        {
            $ret = "2008";
        }

        return $ret;
    }
}
?>