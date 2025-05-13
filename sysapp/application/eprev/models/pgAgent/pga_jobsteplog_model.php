<?php
class Pga_jobsteplog_model extends Model 
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT jstname, 
				   TO_CHAR(jslstart,'DD/MM/YYYY HH24:MI:SS') AS jslstart, 
				   jsloutput, 
				   jslstatus 
			  FROM pgAgent.pga_jobsteplog pga_log
			  JOIN pgAgent.pga_jobstep pga
				ON pga.jstid = pga_log.jsljstid
			 WHERE 1 = 1
			    ".(trim($args["keyword"]) != "" ? "AND (UPPER(funcoes.remove_acento(jstname)) LIKE UPPER(funcoes.remove_acento('%".trim($args["keyword"])."%')) OR
				UPPER(funcoes.remove_acento(jsloutput)) LIKE UPPER(funcoes.remove_acento('%".trim($args["keyword"])."%')))" : "")."
				".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND CAST(pga_log.jslstart AS DATE) BETWEEN TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY') " : "")."
				".(trim($args['status']) != "" ? "AND pga_log.jslstatus = '".trim($args['status'])."'" : "")."
				;";
				
		$result = $this->db->query($qr_sql);
	}

	function listar_status( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT DISTINCT pgagent.pga_jobsteplog.jslstatus AS value, 
			       pgagent.pga_jobsteplog.jslstatus AS text 
		      FROM pgagent.pga_jobsteplog;";
			  
		$result = $this->db->query($qr_sql);
	}
}
?>
