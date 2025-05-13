<?php
class Rentabilidade_model extends Model
{
	function __construct()
  	{
    	parent::Model();
  	}

	public function get_indice_ano($cd_empresa, $cd_plano, $nr_ano)
    {
        $qr_sql = "
            SELECT i.vlr_indice AS vl_cota, 
                   i.dt_indice AS dt_cota,  
                   TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
                   TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
                   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
              FROM public.indices i
              JOIN public.planos_patrocinadoras pp
                ON pp.cd_indexador = i.cd_indexador
             WHERE pp.cd_empresa = ".intval($cd_empresa)."
               AND pp.cd_plano   = ".intval($cd_plano)."
               AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('01/12/".($nr_ano-1)."','DD/MM/YYYY') AND TO_DATE('01/01/".($nr_ano+1)."','DD/MM/YYYY')
               AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
                                         FROM public.indices i1
                                        WHERE i1.cd_indexador = i.cd_indexador 
                                        GROUP BY DATE_TRUNC('month', i1.dt_indice))
               AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
             ORDER BY i.dt_indice;";

        return $this->db->query($qr_sql)->result_array();
    }
	
	public function get_indice_ano_inpel($cd_empresa, $cd_plano, $nr_ano)
    {
        $qr_sql = "
            SELECT i.vlr_indice AS vl_cota, 
                   (i.dt_indice  - '1 month'::interval) AS dt_cota,  
                   TO_CHAR(i.dt_indice - '1 month'::interval, 'DD/MM') AS dt_dia, 
                   TO_CHAR(i.dt_indice - '1 month'::interval, 'MM') AS dt_mes,
                   TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
              FROM public.indices i
              JOIN public.planos_patrocinadoras pp
                ON pp.cd_indexador = i.cd_indexador
             WHERE pp.cd_empresa = ".intval($cd_empresa)."
               AND pp.cd_plano   = ".intval($cd_plano)."
               AND (DATE_TRUNC('day',dt_indice)  - '1 month'::interval)  BETWEEN TO_DATE('01/12/".($nr_ano-1)."','DD/MM/YYYY')  AND TO_DATE('01/01/".($nr_ano+1)."','DD/MM/YYYY')
               AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
                                     FROM public.indices i1
                                    WHERE i1.cd_indexador = i.cd_indexador 
                                    GROUP BY DATE_TRUNC('month', i1.dt_indice))
             ORDER BY i.dt_indice;";

        return $this->db->query($qr_sql)->result_array();
    }

	public function get_indice_ano_outras($cd_empresa, $cd_plano, $nr_ano)
    {
        $qr_sql = "
            SELECT i.vlr_indice AS vl_cota, 
                   (i.dt_indice  - '1 month'::interval) AS dt_cota,  
                   TO_CHAR(i.dt_indice - '1 month'::interval, 'DD/MM') AS dt_dia, 
                   TO_CHAR(i.dt_indice - '1 month'::interval, 'MM') AS dt_mes
              FROM public.indices i
              JOIN public.planos_patrocinadoras pp
                ON pp.cd_indexador = i.cd_indexador
             WHERE pp.cd_empresa = ".intval($cd_empresa)."
               AND pp.cd_plano   = ".intval($cd_plano)."
               AND (DATE_TRUNC('day',dt_indice)  - '1 month'::interval) BETWEEN TO_DATE('01/12/".($nr_ano-1)."','DD/MM/YYYY')  AND TO_DATE('31/12/".($nr_ano)."','DD/MM/YYYY')
               AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
                                     FROM public.indices i1
                                    WHERE i1.cd_indexador = i.cd_indexador
                                    GROUP BY DATE_TRUNC('month', i1.dt_indice))
               AND (i.dt_inclusao + '3 day'::INTERVAL)::DATE < CURRENT_DATE -- 3 DIAS APOS INCLUSAO LIBERA
               AND (DATE_TRUNC('day',dt_indice) - '1 month'::interval) 
                        >= 
                   (CASE WHEN pp.cd_empresa IN (8,10) THEN TO_DATE('01/07/2008','DD/MM/YYYY')
                         WHEN pp.cd_empresa IN (19,20) THEN TO_DATE('01/11/2010','DD/MM/YYYY')
                         ELSE TO_DATE('01/12/2005','DD/MM/YYYY') 
                   END)
             ORDER BY i.dt_indice;";

        return $this->db->query($qr_sql)->result_array();
    }
	
	public function get_qt_razao_cota_indice_ano($cd_empresa, $cod_plano, $nr_ano)
    {
        $qr_sql = "
            SELECT q.vl_cota, 
                   q.dt_ref_sld_cotas AS dt_cota,  
                   TO_CHAR(q.dt_ref_sld_cotas, 'DD/MM') AS dt_dia,  
                   TO_CHAR(q.dt_ref_sld_cotas, 'MM') AS dt_mes 
              FROM public.qt_razao_cota q
             WHERE DATE_TRUNC('day', q.dt_ref_sld_cotas) BETWEEN TO_DATE('01/12/".($nr_ano-1)."','DD/MM/YYYY')  AND TO_DATE('31/12/".$nr_ano."','DD/MM/YYYY')                    
               AND q.dt_ref_sld_cotas = (SELECT MAX(q1.dt_ref_sld_cotas) 
                                           FROM public.qt_razao_cota  q1
                                          WHERE q1.cod_tp_aplic = '00000' 
                                            AND q1.cod_plano    = q.cod_plano 
                                            AND q1.cod_empresa  = q.cod_empresa
                                            AND TO_CHAR(q1.dt_ref_sld_cotas,'MM-YYYY') = TO_CHAR(q.dt_ref_sld_cotas,'MM-YYYY'))
               AND q.cd_atividade = (SELECT MAX(q2.cd_atividade) 
                                       FROM qt_razao_cota q2
                                      WHERE q2.cod_tp_aplic = '00000' 
                                        AND q2.cod_plano    = q.cod_plano 
                                        AND q2.cod_empresa  = q.cod_empresa
                                        AND q2.dt_ref_sld_cotas = q.dt_ref_sld_cotas)                                                  
               AND q.dt_ref_sld_cotas <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
               AND q.cod_tp_aplic = '00000' 
               AND q.cod_plano    = ".intval($cod_plano)." 
               AND q.cod_empresa  = ".intval($cd_empresa)."
             ORDER BY dt_ref_sld_cotas
             LIMIT 13;";

        return $this->db->query($qr_sql)->result_array();
    }

	public function get_sc_calculo_cotas_indice_ano($cd_empresa, $cod_plano, $nr_ano)
    {
        $qr_sql = "
            SELECT scc.vl_cota,
                   scc.dt_calculo AS dt_cota,
                   TO_CHAR(scc.dt_calculo, 'DD/MM') AS dt_dia,  
                   TO_CHAR(scc.dt_calculo, 'MM') AS dt_mes 
              FROM public.sc_calculo_cotas scc
             WHERE scc.cd_tp_aplicacao = 8 --oracle.pck_sc_consultas_gerais_fnc_ret_tp_aplic_consolidador()
               AND scc.cd_empresa      = ".intval($cd_empresa)."
               AND scc.cd_plano        = ".intval($cod_plano)." 
               AND DATE_TRUNC('day', scc.dt_calculo) BETWEEN TO_DATE('01/12/".($nr_ano-1)."','DD/MM/YYYY')  AND TO_DATE('31/12/".$nr_ano."','DD/MM/YYYY')
               AND scc.dt_calculo < (DATE_TRUNC('month',CURRENT_DATE) - '1 month'::interval) -- MES ANTERIOR
               AND scc.dt_calculo = (SELECT MAX(scc1.dt_calculo) 
                                       FROM public.sc_calculo_cotas scc1
                                      WHERE scc1.cd_tp_aplicacao = scc.cd_tp_aplicacao
                                        AND scc1.cd_empresa      = scc.cd_empresa
                                        AND scc1.cd_plano        = scc.cd_plano
                                        AND TO_CHAR(scc1.dt_calculo,'MM-YYYY') = TO_CHAR(scc.dt_calculo,'MM-YYYY'))

                 ORDER BY scc.dt_calculo
                 LIMIT 13;";

        return $this->db->query($qr_sql)->result_array();
    }

	public function get_planos_patrocinadoras($cd_empresa, $cd_plano)
    {
        $qr_sql = "
            SELECT p.cd_plano,
                   pp.cd_plano_financ, 
                   pp.cd_empresa_financ,
                   (CASE WHEN pp.tipo_plano = 4 
                        THEN 'AAEX' -- INSTITUIDOR
                        ELSE 'AAPR' 
                   END) AS tipo_plano
              FROM participantes p
              JOIN planos_patrocinadoras pp
                ON p.cd_empresa            = pp.cd_empresa
               AND p.cd_plano              = pp.cd_plano
             WHERE p.cd_empresa            = ".intval($cd_empresa)."
               AND p.cd_plano 			   = ".intval($cd_plano).";";

        return $this->db->query($qr_sql)->row_array();
    }
}