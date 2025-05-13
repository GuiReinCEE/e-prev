<?php
class Limite_voip_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT l.cd_limite, 
						   l.nr_ramal,
						   s.nome,
						   l.qt_chamada, 
						   l.vl_chamada, 
						   l.hr_chamada, 
						   TO_CHAR(COALESCE(l.dt_atualizacao,l.dt_inclusao),'DD/MM/YYYY HH24:MI:SS') AS dt_atualizacao
					  FROM asterisk.limite l
					  JOIN asterisk.sip s
						ON s.nr_ramal = l.nr_ramal
					 WHERE s.conta = '{cd_divisao}'
		          ";
		esc("{cd_divisao}", $args["cd_divisao"],$qr_sql);
		$result = $this->db->query($qr_sql);
	}
	
	function atualizar(&$result, $args=array())
	{
		if(intval($args['cd_limite']) > 0)
		{
			$qr_sql = " 
						UPDATE asterisk.limite 
						   SET qt_chamada             = ".(trim($args['qt_chamada']) == "" ? "DEFAULT" : $args['qt_chamada']).",
						       vl_chamada             = ".(trim($args['vl_chamada']) == "" ? "DEFAULT" : $args['vl_chamada']).",
						       hr_chamada             = ".(trim($args['hr_chamada']) == "" ? "DEFAULT" : "CAST('".$args['hr_chamada']."' AS INTERVAL)").",
							   dt_atualizacao         = CURRENT_TIMESTAMP,
							   cd_usuario_atualizacao = ".intval($args['cd_usuario'])."
						 WHERE cd_limite = ".intval($args['cd_limite'])."
					  ";		
			$this->db->query($qr_sql);
		}
		
		
			
		$qr_sql = "
					SELECT TO_CHAR(COALESCE(dt_atualizacao,dt_inclusao),'DD/MM/YYYY HH24:MI:SS') AS dt_atualizacao
					  FROM asterisk.limite
					 WHERE cd_limite = ".intval($args['cd_limite'])."
				  ";
		$result = $this->db->query($qr_sql);
		$ar_reg = $result->row_array();			
		$retorno = $ar_reg['dt_atualizacao'];		
		
		return $retorno;
	}	
}
?>