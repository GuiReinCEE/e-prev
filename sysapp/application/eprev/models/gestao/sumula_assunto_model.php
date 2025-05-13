<?php
class Sumula_assunto_model extends Model
{
	function __construct()
	{
		parent::Model();
	}


	public function listar($args = array())
	{
		$qr_sql = "
			SELECT x.nr_sumula,
			       x.ds_sumula,
			       x.fl_colegiado,
			       x.ds_class
			  FROM
				 (
					SELECT 'DE' AS fl_colegiado,
					       s.nr_sumula AS nr_sumula,  
					       si.nr_sumula_item || ' - ' || si.descricao AS ds_sumula,
					       s.dt_sumula,
					       'label label-success' AS ds_class
					  FROM gestao.sumula_item si
					  JOIN gestao.sumula s
					    ON s.cd_sumula = si.cd_sumula
					 WHERE si.dt_exclusao IS NULL
					   AND s.dt_exclusao IS NULL
					 
					UNION

					SELECT 'CD' AS fl_colegiado,
					       s.nr_sumula_conselho AS nr_sumula, 
					       si.nr_sumula_conselho_item || ' - ' || si.descricao AS ds_sumula,
					       s.dt_sumula_conselho AS dt_sumula,
					       'label label-info' AS ds_class
					  FROM gestao.sumula_conselho_item si
					  JOIN gestao.sumula_conselho s
					    ON s.cd_sumula_conselho = si.cd_sumula_conselho
					 WHERE si.dt_exclusao IS NULL
					   AND s.dt_exclusao IS NULL
					 
					 UNION

					SELECT 'CF' AS fl_colegiado,
					       s.nr_sumula_conselho_fiscal AS nr_sumula,  
					       si.nr_sumula_conselho_fiscal_item || ' - ' || si.descricao AS ds_sumula,
					       s.dt_sumula_conselho_fiscal AS dt_sumula,
					       'label label-warning' AS ds_class
					  FROM gestao.sumula_conselho_fiscal_item si
					  JOIN gestao.sumula_conselho_fiscal s
					    ON s.cd_sumula_conselho_fiscal = si.cd_sumula_conselho_fiscal
					 WHERE si.dt_exclusao IS NULL
					   AND s.dt_exclusao IS NULL

					 UNION

					SELECT 'IN' AS fl_colegiado,
					       s.nr_sumula_interventor AS nr_sumula,  
					       si.nr_sumula_interventor_item || ' - ' || si.descricao AS ds_sumula,
					       s.dt_sumula_interventor AS dt_sumula,
					       'label label-inverse' AS ds_class
					  FROM gestao.sumula_interventor_item si
					  JOIN gestao.sumula_interventor s
					    ON s.cd_sumula_interventor = si.cd_sumula_interventor
					 WHERE si.dt_exclusao IS NULL
					   AND s.dt_exclusao IS NULL

				 ) AS x
			 WHERE 1 = 1
			  ".(intval($args['nr_sumula']) > 0 ? "AND x.nr_sumula = ".intval($args['nr_sumula']) : "")."
			  ".(trim($args['fl_colegiado']) != '' ? "AND x.fl_colegiado = '".trim($args['fl_colegiado'])."'" : "")."

			  ".(((trim($args['dt_sumula_ini']) != '') AND (trim($args['dt_sumula_fim']) != '')) ? "AND DATE_TRUNC('day', x.dt_sumula) BETWEEN TO_DATE('".$args['dt_sumula_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_sumula_fim']."', 'DD/MM/YYYY')" : "")."
		    ORDER BY x.nr_sumula;";


		return $this->db->query($qr_sql)->result_array();
	}
}