<?php
class Job_log_model extends Model 
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args=array())
	{
		$qr_sql = "
			SELECT cd_job_log,
			       ds_funcao,
			       ds_job,
			       ds_comando,
			       TO_CHAR(dt_erro, 'DD/MM/YYYY HH24:MI:SS') AS dt_erro,
			       ds_erro
			  FROM projetos.job_log
			 WHERE 1 = 1
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? "AND CAST(dt_erro AS DATE) BETWEEN TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY')" : '')."
			   ".(trim($args['fl_status']) != '' ? "AND fl_status = '".trim($args['fl_status'])."'" : '').";";
				
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_job_log)
	{
		$qr_sql = "
			SELECT cd_job_log,
			       ds_funcao,
			       ds_job,
			       ds_comando,
			       TO_CHAR(dt_erro, 'DD/MM/YYYY HH24:MI:SS') AS dt_erro,
			       ds_erro
			  FROM projetos.job_log
			 WHERE cd_job_log = ".intval($cd_job_log).";";
				
		return $this->db->query($qr_sql)->row_array();
	}
}

?>
