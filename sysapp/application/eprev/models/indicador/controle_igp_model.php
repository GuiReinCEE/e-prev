<?php
class Controle_igp_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT TO_CHAR(i.dt_referencia, 'YYYY/MM') AS dt_referencia,
				   TO_CHAR(i.dt_referencia, 'YYYY') AS ano,
				   TO_CHAR(i.dt_referencia, 'MM') AS mes,
			       TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   i.cd_igp,
				   c.dt_referencia AS dt_encerrar,
				   uc.nome
			  FROM igp.igp i
			  LEFT JOIN igp.controle c
				ON c.dt_referencia = i.dt_referencia
			   AND c.dt_exclusao IS NULL
			  LEFT JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = c.cd_usuario_inclusao
			 WHERE i.dt_exclusao IS NULL
			 ".(trim($args['ano']) != '' ? "AND TO_CHAR(i.dt_referencia, 'YYYY') = '".intval($args['ano'])."'" : '')."
			 ".(trim($args['fl_encerrado']) == 'S' ? "AND c.dt_inclusao IS NOT NULL" : '')."
			 ".(trim($args['fl_encerrado']) == 'N' ? "AND c.dt_inclusao IS NULL" : '')."
			 ORDER BY i.dt_referencia DESC";

		$result = $this->db->query($qr_sql);
	}
	
	function encerrar( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO igp.controle
			     (
				   cd_usuario_inclusao,
				   dt_referencia
				 )
		    VALUES
			     (
				  ".intval($args['cd_usuario']).",
				  (SELECT CAST(dt_referencia AS DATE) 
				     FROM igp.igp
					WHERE cd_igp = ".intval($args['cd_igp']).")
				 )";
				 
		$this->db->query($qr_sql);
	}
}

?>