<?php
class Cadastro_Origem_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT co.cd_cadastro_origem, 
						   co.origem, 
						   co.dt_inclusao, 
						   co.cd_usuario_inclusao, 
						   co.dt_exclusao, 
						   co.cd_usuario_exclusao
					  FROM acs.cadastro_origem co
					 WHERE co.dt_exclusao IS NULL
		          ";
		$result = $this->db->query($qr_sql);
	}
	
	function combo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT co.cd_cadastro_origem AS value, 
						   co.origem AS text
					  FROM acs.cadastro_origem co
					 WHERE co.dt_exclusao IS NULL
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	
}
?>