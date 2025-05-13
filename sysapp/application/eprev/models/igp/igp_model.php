<?php
class Igp_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_igp,
					       TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
						   acu_total,
				           mes_total,
				           mm_total,
					       acu_rpp,
					       acu_recl,
					       acu_liq_erro,
					       acu_calc_ini,
					       acu_custo,
					       acu_equilibrio,
					       acu_participante,
					       acu_var_orc,
					       acu_treinamento,
					       acu_informatica,
					       acu_sat_colab,
					       acu_aval,
					       acu_sat_part,
					       acu_rentabilidade_ci,
					       mes_rpp,
					       mes_recl,
					       mes_liq_erro,
					       mes_calc_ini,
					       mes_custo,
					       mes_equilibrio,
					       mes_participante,
					       mes_var_orc,
					       mes_treinamento,
					       mes_informatica,
					       mes_sat_colab,
					       mes_aval,
					       mes_sat_part,
					       mes_rentabilidade_ci,
					       mm_rpp,
					       mm_recl,
					       mm_liq_erro,
					       mm_calc_ini,
					       mm_custo,
					       mm_equilibrio,
					       mm_participante,
					       mm_var_orc,
					       mm_treinamento,
					       mm_informatica,
					       mm_sat_colab,
					       mm_aval,
					       mm_sat_part,
					       mm_rentabilidade_ci,
					       nr_meta,
					       cd_indicador_tabela
			          FROM igp.igp 
			         ORDER BY dt_referencia ".(strtoupper(trim($args['ordem'])) == "ASC" ? "ASC" : "DESC")."
			         LIMIT ".(intval($args['qt_limit']) > 0 ? intval($args['qt_limit']) : "12")."
		          ";
		$result = $this->db->query($qr_sql);
	}
	
	function anual(&$result, $args=array())
	{	
		$qr_sql = "
					SELECT cd_igp_anual, 
					       nr_ano, 
						   resultado, 
						   dt_inclusao, 
						   cd_usuario_inclusao, 
						   dt_alteracao, 
						   cd_usuario_alteracao, 
						   dt_exclusao, 
						   cd_usuario_exclusao
					  FROM igp.igp_anual
					 WHERE dt_exclusao IS NULL
					 ORDER BY nr_ano DESC
		          ";
		$result = $this->db->query($qr_sql);
	}	
}
?>