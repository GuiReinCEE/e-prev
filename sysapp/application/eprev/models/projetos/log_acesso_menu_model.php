<?php
class Log_acesso_menu_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT ds_menu,
			       ds_uri,
			       TO_CHAR(dt_log_acesso_menu, 'DD/MM/YYYY HH24:MI:SS') AS dt_log_acesso_menu,
			       funcoes.get_usuario_nome(cd_usuario) AS nome_usuario
			  FROM projetos.log_acesso_menu 
			 WHERE 1 = 1
			   ".(((trim($args['dt_acesso_ini']) != '') AND (trim($args['dt_acesso_fim']) != '')) ? " AND DATE_TRUNC('day', dt_log_acesso_menu) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_menu($args = array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS nr_acesso,
			       ds_menu
			  FROM projetos.log_acesso_menu 
			 WHERE TO_CHAR(dt_log_acesso_menu, 'YYYY') = '".trim($args['nr_ano'])."'
			   ".(trim($args['nr_mes']) ? "AND TO_CHAR(dt_log_acesso_menu, 'MM') = '".trim($args['nr_mes'])."'" : "")."
			 GROUP BY ds_menu;";
			   
		return $this->db->query($qr_sql)->result_array();
	}
}