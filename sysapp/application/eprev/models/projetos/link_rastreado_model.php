<?php
class Link_rastreado_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function cadastro(&$result, $args=array())
	{
		$qr_sql = "
			SELECT l.cd_link, 
				   l.ds_link, 
				   TO_CHAR(l.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(l.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
				   l.cd_empresa, 
				   l.cd_registro_empregado, 
				   l.seq_dependencia
			  FROM projetos.link l					
			 WHERE l.cd_link = '".$args['cd_link']."'
		  ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}
	
	function linkLog(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(t.data, 'DD/MM/YYYY') AS data,
				   SUM(COALESCE(t.qt_acesso_interno)) AS qt_acesso_interno,
				   SUM(COALESCE(t.qt_acesso_externo)) AS qt_acesso_externo
			  FROM (SELECT CAST(lg.dt_inclusao AS DATE) AS data, 
						   0 AS qt_acesso_interno,
						   COUNT(*) AS qt_acesso_externo
					  FROM projetos.link_log lg
					 WHERE lg.cd_link = '".$args['cd_link']."'
					   AND lg.ip NOT LIKE '10.63.%'
					   ".(((trim($args['dt_acesso_ini']) != "") and  (trim($args['dt_acesso_fim']) != "")) ? "AND CAST(lg.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_acesso_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_acesso_fim'])."','DD/MM/YYYY')" : "")."
					 GROUP BY data
					 UNION
					SELECT CAST(lg.dt_inclusao AS DATE) AS data, 
						   COUNT(*) AS qt_acesso_interno,
						   0 AS qt_acesso_externo
					  FROM projetos.link_log lg
					 WHERE lg.cd_link = '".$args['cd_link']."'
					   AND lg.ip LIKE '10.63.%'
					   ".(((trim($args['dt_acesso_ini']) != "") and  (trim($args['dt_acesso_fim']) != "")) ? "AND CAST(lg.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_acesso_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_acesso_fim'])."','DD/MM/YYYY')" : "")."
					 GROUP BY data) t
			 GROUP BY t.data
			 ORDER BY t.data 
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}
	
	function linkLogDia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(t.data, 'DD/MM/YYYY HH24:MI') AS data_ini,
				   TO_CHAR((t.data + '1 hour'::interval), 'DD/MM/YYYY HH24:MI') AS data_fim,
				   SUM(COALESCE(t.qt_acesso_interno)) AS qt_acesso_interno,
				   SUM(COALESCE(t.qt_acesso_externo)) AS qt_acesso_externo
			  FROM (SELECT DATE_TRUNC('hour', lg.dt_inclusao) AS data, 
						   0 AS qt_acesso_interno,
						   COUNT(*) AS qt_acesso_externo
					  FROM projetos.link_log lg
					 WHERE lg.cd_link = '".$args['cd_link']."'
					   AND lg.ip NOT LIKE '10.63.%'
					   AND CAST(lg.dt_inclusao AS DATE) = TO_DATE('".trim($args['dt_acesso'])."','DD/MM/YYYY')
					 GROUP BY data
					 UNION
					SELECT DATE_TRUNC('hour', lg.dt_inclusao) AS data, 
						   COUNT(*) AS qt_acesso_interno,
						   0 AS qt_acesso_externo
					  FROM projetos.link_log lg
					 WHERE lg.cd_link = '".$args['cd_link']."'
					   AND lg.ip LIKE '10.63.%'
					   AND CAST(lg.dt_inclusao AS DATE) = TO_DATE('".trim($args['dt_acesso'])."','DD/MM/YYYY')
					 GROUP BY data) t
			 GROUP BY data_ini, data_fim
			 ORDER BY data_ini 
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}	
	
	function linkLogHora(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(t.data, 'HH24:MI') AS hora_ini,
				   TO_CHAR((t.data + '1 hour'::interval), 'HH24:MI') AS hora_fim,
				   SUM(COALESCE(t.qt_acesso_interno)) AS qt_acesso_interno,
				   SUM(COALESCE(t.qt_acesso_externo)) AS qt_acesso_externo
			  FROM (SELECT DATE_TRUNC('hour', lg.dt_inclusao) AS data, 
						   0 AS qt_acesso_interno,
						   COUNT(*) AS qt_acesso_externo
					  FROM projetos.link_log lg
					 WHERE lg.cd_link = '".$args['cd_link']."'
					   AND lg.ip NOT LIKE '10.63.%'
					   ".(((trim($args['dt_acesso_ini']) != "") and  (trim($args['dt_acesso_fim']) != "")) ? "AND CAST(lg.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_acesso_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_acesso_fim'])."','DD/MM/YYYY')" : "")."
					 GROUP BY data
					 UNION
					SELECT DATE_TRUNC('hour', lg.dt_inclusao) AS data, 
						   COUNT(*) AS qt_acesso_interno,
						   0 AS qt_acesso_externo
					  FROM projetos.link_log lg
					 WHERE lg.cd_link = '".$args['cd_link']."'
					   AND lg.ip LIKE '10.63.%'
					   ".(((trim($args['dt_acesso_ini']) != "") and  (trim($args['dt_acesso_fim']) != "")) ? "AND CAST(lg.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_acesso_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_acesso_fim'])."','DD/MM/YYYY')" : "")."
					 GROUP BY data) t
			 GROUP BY hora_ini, hora_fim
			 ORDER BY hora_ini 
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}	
	
	function gerar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT funcoes.gera_link(
				'".trim($args['ds_url'])."', 
				".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : 'NULL')."::INTEGER, 
				".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : 'NULL')."::INTEGER, 
				".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : 'NULL')."::INTEGER) AS ds_link";

		$result = $this->db->query($qr_sql);
	}
	
	function salva_link(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.divulgacao_link
			     (
					ds_divulgacao_link, 
					cd_link, 
					cd_usuario_inclusao,
					ds_link
				 )
		    VALUES
			     (
				   '".trim($args['ds_divulgacao_link'])."',
				   '".trim($args['cd_link'])."',
				   ".intval($args['cd_usuario']).",
				   '".trim($args['ds_link'])."'
				 );";
				 
		$this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT dl.cd_link,
			       dl.ds_divulgacao_link,
				   dl.ds_link AS ds_url,
				   l.ds_link AS link,
				   l.cd_empresa, 
				   l.cd_registro_empregado,
				   l.seq_dependencia,
				   p.nome
			  FROM projetos.divulgacao_link dl
			  JOIN projetos.link l
				ON dl.cd_link = l.cd_link
			  LEFT JOIN participantes p
			    ON p.cd_registro_empregado = l.cd_registro_empregado
               AND p.cd_empresa = l.cd_empresa
			   AND p.seq_dependencia = l.seq_dependencia
			 WHERE dl.cd_link = '".trim($args['cd_link'])."'
			   AND dl.dt_exclusao IS NULL";

		$result = $this->db->query($qr_sql);
	}
	
	function lista(&$result, $args=array())
	{
		$qr_sql = "
			SELECT dl.cd_link,
			       dl.ds_divulgacao_link,
				   dl.ds_link AS ds_url,
				   l.ds_link AS link,
				   l.cd_empresa, 
				   l.cd_registro_empregado,
				   l.seq_dependencia,
				   TO_CHAR(dl.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   p.nome
			  FROM projetos.divulgacao_link dl
			  JOIN projetos.link l
				ON dl.cd_link = l.cd_link
			  LEFT JOIN participantes p
			    ON p.cd_registro_empregado = l.cd_registro_empregado
               AND p.cd_empresa = l.cd_empresa
			   AND p.seq_dependencia = l.seq_dependencia
			 WHERE dl.dt_exclusao IS NULL
			 ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND CAST(dl.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY')" : "")."
			 ORDER BY dl.cd_link DESC";
			
		$result = $this->db->query($qr_sql);
	}

	
	#### DADOS GRAFICOS ####
	function tecnologiaDeviceType(&$result, $args=array())
	{
		$qr_sql = "
				SELECT COALESCE(INITCAP(ee.device_type),'Não identificado') AS ds_item, 
				       COUNT(*) AS qt_item
				  FROM projetos.link_log ee
				 WHERE ee.cd_link = '".trim($args['cd_link'])."'
				     ".(((trim($args['dt_acesso_ini']) != "") AND (trim($args['dt_acesso_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_inclusao) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "")."
				 GROUP BY ds_item
				 ORDER BY qt_item DESC
				";
		#echo "<pre style='text-align:center;'>$qr_sql</pre>";

		$result = $this->db->query($qr_sql);
	}

	function tecnologiaDeviceName(&$result, $args=array())
	{
		$qr_sql = "
				SELECT COALESCE(ee.device_mobile,'Não identificado') AS ds_item, 
				       COUNT(*) AS qt_item
				  FROM projetos.link_log ee
				 WHERE ee.cd_link = '".trim($args['cd_link'])."'
				   AND COALESCE(ee.device_type,'') IN ('phone','tablet')
				     ".(((trim($args['dt_acesso_ini']) != "") AND (trim($args['dt_acesso_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_inclusao) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "")."
				 GROUP BY ds_item
				 ORDER BY qt_item DESC
				";
		#echo "<pre style='text-align:center;'>$qr_sql</pre>";

		$result = $this->db->query($qr_sql);
	}	
	
	function tecnologiaOSFamily(&$result, $args=array())
	{
		$qr_sql = "
				SELECT COALESCE(ee.ug_os_family,'Não identificado') AS ds_item, 
				       COUNT(*) AS qt_item
				  FROM projetos.link_log ee
				 WHERE ee.cd_link = '".trim($args['cd_link'])."'
				     ".(((trim($args['dt_acesso_ini']) != "") AND (trim($args['dt_acesso_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_inclusao) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "")."
				 GROUP BY ds_item
				 ORDER BY qt_item DESC
				";
		#echo "<pre style='text-align:center;'>$qr_sql</pre>";

		$result = $this->db->query($qr_sql);
	}

	function tecnologiaOSName(&$result, $args=array())
	{
		$qr_sql = "
				SELECT COALESCE(ee.ug_os_name,'Não identificado') AS ds_item, 
				       COUNT(*) AS qt_item
				  FROM projetos.link_log ee
				 WHERE ee.cd_link = '".trim($args['cd_link'])."'
				     ".(((trim($args['dt_acesso_ini']) != "") AND (trim($args['dt_acesso_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_inclusao) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "")."
				 GROUP BY ds_item
				 ORDER BY qt_item DESC
				";
		#echo "<pre style='text-align:center;'>$qr_sql</pre>";

		$result = $this->db->query($qr_sql);
	}

	function tecnologiaUATipo(&$result, $args=array())
	{
		$qr_sql = "
				SELECT COALESCE(ee.ug_typ,'Não identificado') AS ds_item, 
				       COUNT(*) AS qt_item
				  FROM projetos.link_log ee
				 WHERE ee.cd_link = '".trim($args['cd_link'])."'
				     ".(((trim($args['dt_acesso_ini']) != "") AND (trim($args['dt_acesso_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_inclusao) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "")."
				 GROUP BY ds_item
				 ORDER BY qt_item DESC
				";
		#echo "<pre style='text-align:center;'>$qr_sql</pre>";

		$result = $this->db->query($qr_sql);
	}	
	
	function tecnologiaUAFamily(&$result, $args=array())
	{
		$qr_sql = "
				SELECT COALESCE(ee.ug_ua_family,'Não identificado') AS ds_item, 
				       COUNT(*) AS qt_item
				  FROM projetos.link_log ee
				 WHERE ee.cd_link = '".trim($args['cd_link'])."'
				     ".(((trim($args['dt_acesso_ini']) != "") AND (trim($args['dt_acesso_fim']) != "")) ? " AND DATE_TRUNC('day', ee.dt_inclusao) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "")."
				 GROUP BY ds_item
				 ORDER BY qt_item DESC
				";
		#echo "<pre style='text-align:center;'>$qr_sql</pre>";

		$result = $this->db->query($qr_sql);
	}	

}
?>