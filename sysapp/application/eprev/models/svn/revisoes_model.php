<?php
class revisoes_model extends Model
{
	function __construct()
	{
		parent::Model();
	}	
	function inicio(&$result, $args=array())
	{
		$qr_sql = "
			SELECT SUM(nr_tamanho) AS nr_tamanho
			  FROM svn.revisoes
			 WHERE dt_revisao = (SELECT MIN(dt_revisao)
								   FROM svn.revisoes
								  WHERE ds_repositorio = '".trim($args['ds_repositorio'])."')	
			   AND ds_repositorio = '".trim($args['ds_repositorio'])."';";

		$result = $this->db->query($qr_sql);
	}	
	
	function total(&$result, $args=array())
	{
		$qr_sql = "
			SELECT SUM(nr_tamanho) AS nr_tamanho
			  FROM svn.revisoes	
			 WHERE dt_revisao < DATE_TRUNC('month', CURRENT_DATE)
			   AND ds_repositorio = '".trim($args['ds_repositorio'])."';";

		$result = $this->db->query($qr_sql);
	}	
	
	function media(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ROUND(AVG(d.nr_tamanho),2) AS nr_tamanho,
				   ROUND(AVG(d.pr_crescimento),2) AS pr_crescimento
			  FROM (SELECT TO_CHAR(dt_revisao,'MM/YYYY') AS dt_mes,
					       SUM(nr_tamanho) AS nr_tamanho,
						   ROUND((SUM(nr_tamanho) / (SELECT SUM(nr_tamanho)
													   FROM svn.revisoes
													  WHERE dt_revisao = (SELECT MIN(dt_revisao)
																		    FROM svn.revisoes
																		   WHERE ds_repositorio = '".trim($args['ds_repositorio'])."')
													    AND ds_repositorio = '".trim($args['ds_repositorio'])."') * 100),2) AS pr_crescimento
					  FROM svn.revisoes
				     WHERE dt_revisao > (SELECT MIN(dt_revisao)
										   FROM svn.revisoes
										  WHERE ds_repositorio = '".trim($args['ds_repositorio'])."')
					   AND dt_revisao < DATE_TRUNC('month', CURRENT_DATE)
					   AND ds_repositorio = '".trim($args['ds_repositorio'])."'
				     GROUP BY TO_CHAR(dt_revisao,'MM/YYYY')) AS d		;";

		$result = $this->db->query($qr_sql);
	}	
	
	function mes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_revisao,'MM/YYYY') AS dt_mes,
				   TO_CHAR(dt_revisao,'YYYY-MM') AS dt_mes_ingles,
				   SUM(nr_tamanho) AS nr_tamanho,
				   ROUND((SUM(nr_tamanho) / (SELECT SUM(nr_tamanho)
											   FROM svn.revisoes
											  WHERE dt_revisao = (SELECT MIN(dt_revisao)
																	FROM svn.revisoes
																   WHERE ds_repositorio = '".trim($args['ds_repositorio'])."')
												AND ds_repositorio = '".trim($args['ds_repositorio'])."') * 100),2) AS pr_crescimento
			  FROM svn.revisoes
			 WHERE dt_revisao > (SELECT MIN(dt_revisao)
								   FROM svn.revisoes
								  WHERE ds_repositorio = '".trim($args['ds_repositorio'])."')
			   AND ds_repositorio = '".trim($args['ds_repositorio'])."'
			 GROUP BY dt_mes, 
					  dt_mes_ingles
			 ORDER BY dt_mes_ingles;";

		$result = $this->db->query($qr_sql);
	}	
	
}
?>