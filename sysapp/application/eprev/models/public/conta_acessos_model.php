<?php
class Conta_acessos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT DATE_TRUNC('year', data_hora), 
			      TRIM(TO_CHAR(DATE_TRUNC('year', data_hora),'YYYY')) AS ano, 
			      COUNT(date_trunc('year', data_hora)) AS nr_acessos
		     FROM public.conta_acessos
		    WHERE pagina = 'DEFAULT.HTM'
		      ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
		    GROUP BY DATE_TRUNC('year', data_hora);";

		$result = $this->db->query($qr_sql);
	}

	function listar_acesso(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pagina,
			       COUNT(pagina) AS nr_acessos
			  FROM conta_acessos
			 WHERE data_hora > '2004-07-04'
			   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 GROUP BY pagina
			 ORDER BY nr_acessos desc, pagina";

		$result = $this->db->query($qr_sql);
	}

	function listar_acesso_auto_atendimento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pagina,
			       COUNT(pagina) AS nr_acessos
			  FROM log_acessos_usuario
			 WHERE hora > '2002-02-25'
			   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			 GROUP BY pagina
			 ORDER BY nr_acessos desc, pagina";

		$result = $this->db->query($qr_sql);
	}

	function listar_analitico_so(&$result, $args=array())
	{
		$qr_sql = "
			SELECT x.so, x.nr_acessos
			  FROM (
					SELECT 'Windows XP' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%NT 5.1%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Windows 98' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Windows 98%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					  UNION

					SELECT 'Windows 2000' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%NT 5.0%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					  UNION

					SELECT 'Windows NT 4' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Windows NT 4%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Windows 95' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Windows 95%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Linux' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Linux%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Mac' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE (so LIKE '%Mac%'
					    OR so LIKE '%Mac_%')
 					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'WAP (celular)' AS so,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%WAP%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

				   ) x
			 ORDER BY nr_acessos DESC, so;";

		$result = $this->db->query($qr_sql);
	}

	function listar_analitico_navegador(&$result, $args=array())
	{
		$qr_sql = "
			SELECT x.navegador, x.nr_acessos
			  FROM (
					SELECT 'Internet Explorer 6.XX' AS navegador,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%MSIE 6%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Firefox' AS navegador,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Firefox%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Internet Explorer 5.XX' AS navegador,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%MSIE 5%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Internet Explorer 4.XX' AS navegador,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%MSIE 4%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					 UNION

					SELECT 'Opera' AS navegador,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Opera%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					  UNION

					SELECT 'Netscape' AS navegador,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Netscape%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

					  UNION

					SELECT 'Safari' AS navegador,
					       COUNT(*) AS nr_acessos
					  FROM conta_acessos
					 WHERE so LIKE '%Safari%'
					   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND data_hora BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
				   ) x
			 ORDER BY nr_acessos DESC, navegador;";

		$result = $this->db->query($qr_sql);
	}
}
?>