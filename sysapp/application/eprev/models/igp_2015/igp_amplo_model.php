<?php
class igp_amplo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT i.ds_indicador,
                           (SELECT lit.cd_indicador_tabela 
							  FROM indicador.listar_indicador_tabela_aberta_de_indicador lit 
							 WHERE lit.cd_indicador = i.cd_indicador 
							 ORDER BY nr_ano_referencia ASC 
							 LIMIT 1) AS cd_indicador_tabela,
					       ic.tp_analise,
						   ic.pr_peso,
					       it.ds_igp_tipo,
					       y.*
                      FROM (SELECT x.cd_igp_conf,
                                   MAX(CASE WHEN x.mes = '01' THEN x.vl_mes_resultado ELSE NULL END) AS \"01_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '01' THEN x.vl_mes_igp ELSE NULL END)       AS \"01_vl_mes_igp\",
                                   
                                   MAX(CASE WHEN x.mes = '02' THEN x.vl_mes_resultado ELSE NULL END) AS \"02_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '02' THEN x.vl_mes_igp ELSE NULL END)       AS \"02_vl_mes_igp\",     
                                   
                                   MAX(CASE WHEN x.mes = '03' THEN x.vl_mes_resultado ELSE NULL END) AS \"03_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '03' THEN x.vl_mes_igp ELSE NULL END)       AS \"03_vl_mes_igp\",  

                                   MAX(CASE WHEN x.mes = '04' THEN x.vl_mes_resultado ELSE NULL END) AS \"04_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '04' THEN x.vl_mes_igp ELSE NULL END)       AS \"04_vl_mes_igp\",     
                                   
                                   MAX(CASE WHEN x.mes = '05' THEN x.vl_mes_resultado ELSE NULL END) AS \"05_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '05' THEN x.vl_mes_igp ELSE NULL END)       AS \"05_vl_mes_igp\",         
                                   
                                   MAX(CASE WHEN x.mes = '06' THEN x.vl_mes_resultado ELSE NULL END) AS \"06_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '06' THEN x.vl_mes_igp ELSE NULL END)       AS \"06_vl_mes_igp\",     
                                   
                                   MAX(CASE WHEN x.mes = '07' THEN x.vl_mes_resultado ELSE NULL END) AS \"07_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '07' THEN x.vl_mes_igp ELSE NULL END)       AS \"07_vl_mes_igp\",  

                                   MAX(CASE WHEN x.mes = '08' THEN x.vl_mes_resultado ELSE NULL END) AS \"08_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '08' THEN x.vl_mes_igp ELSE NULL END)       AS \"08_vl_mes_igp\",  

                                   MAX(CASE WHEN x.mes = '09' THEN x.vl_mes_resultado ELSE NULL END) AS \"09_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '09' THEN x.vl_mes_igp ELSE NULL END)       AS \"09_vl_mes_igp\",  

                                   MAX(CASE WHEN x.mes = '10' THEN x.vl_mes_resultado ELSE NULL END) AS \"10_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '10' THEN x.vl_mes_igp ELSE NULL END)       AS \"10_vl_mes_igp\",  

                                   MAX(CASE WHEN x.mes = '11' THEN x.vl_mes_resultado ELSE NULL END) AS \"11_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '11' THEN x.vl_mes_igp ELSE NULL END)       AS \"11_vl_mes_igp\",  

                                   MAX(CASE WHEN x.mes = '12' THEN x.vl_mes_resultado ELSE NULL END) AS \"12_vl_mes_resultado\",
                                   MAX(CASE WHEN x.mes = '12' THEN x.vl_mes_igp ELSE NULL END)       AS \"12_vl_mes_igp\"      
                               
                              FROM (SELECT c.cd_igp_conf,
                                           TO_CHAR(v.dt_referencia,'MM') AS mes,
                                           v.vl_mes,
                                           v.vl_mes_meta,
                                           v.vl_mes_resultado,
                                           v.vl_mes_igp
                                      FROM igp_2015.igp_conf c
                                      JOIN igp_2015.igp_valor v
                                        ON v.cd_igp_conf = c.cd_igp_conf
                                     WHERE c.nr_ano = ".intval($args["nr_ano"])."
                                       AND c.dt_exclusao IS NULL
                                       AND v.dt_exclusao IS NULL) x
                             GROUP BY x.cd_igp_conf) y
                      JOIN igp_2015.igp_conf ic
                        ON ic.cd_igp_conf = y.cd_igp_conf
                      JOIN igp_2015.igp_tipo it
                        ON it.cd_igp_tipo = ic.cd_igp_tipo
                      JOIN indicador.indicador i
                        ON i.cd_indicador = ic.cd_indicador 
                     ORDER BY it.nr_ordem, 
                              i.ds_indicador
                  ";
        $result = $this->db->query($qr_sql);
    }

}
?>