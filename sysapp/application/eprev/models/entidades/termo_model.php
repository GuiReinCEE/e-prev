<?php
class termo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_termo,
				   TO_CHAR(dt_inicial, 'DD/MM/YYYY') AS dt_inicial,
				   TO_CHAR(dt_final, 'DD/MM/YYYY') AS dt_final,
				   nr_dia_termo
			  FROM entidades.termo
			 WHERE dt_exclusao IS NULL
			   ".(((trim($args['dt_alteracao_ini']) != "") and  (trim($args['dt_alteracao_fim']) != "")) ? " AND DATE_TRUNC('day', dt_alteracao) BETWEEN TO_DATE('".$args['dt_alteracao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_alteracao_fim']."', 'DD/MM/YYYY')" : "").";";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_termo,
				   TO_CHAR(dt_inicial, 'DD/MM/YYYY') AS dt_inicial,
				   TO_CHAR(dt_final, 'DD/MM/YYYY') AS dt_final,
				   ds_termo,
				   nr_dia_termo
			  FROM entidades.termo
			 WHERE cd_termo = ".intval($args['cd_termo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_termo']) == 0)
		{
			$qr_sql = "
				INSERT INTO entidades.termo
				     (
						ds_termo,
						dt_inicial,
						dt_final,
						nr_dia_termo,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
						".(trim($args['ds_termo']) != '' ? str_escape($args['ds_termo']) : "DEFAULT").",
						".(trim($args['dt_inicial']) != '' ? "TO_DATE('".$args['dt_inicial']."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['dt_final']) != '' ? "TO_DATE('".$args['dt_final']."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['nr_dia_termo']) != '' ? intval($args['nr_dia_termo']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE entidades.termo
				   SET ds_termo             = ".(trim($args['ds_termo']) != '' ? "'".trim($args['ds_termo'])."'" : "DEFAULT").",
				       dt_inicial           = ".(trim($args['dt_inicial']) != '' ? "TO_DATE('".$args['dt_inicial']."', 'DD/MM/YYYY')" : "DEFAULT").",
				       dt_final             = ".(trim($args['dt_final']) != '' ? "TO_DATE('".$args['dt_final']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   nr_dia_termo         = ".(trim($args['nr_dia_termo']) != '' ? intval($args['nr_dia_termo']) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_termo = ".intval($args['cd_termo']).";";
		}
		
		$this->db->query($qr_sql);
	}
}
?>