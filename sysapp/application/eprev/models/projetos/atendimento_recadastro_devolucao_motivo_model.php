<?php
class Atendimento_Recadastro_Devolucao_Motivo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT *
					  FROM projetos.atendimento_recadastro_devolucao_motivo ardm
					 WHERE ardm.dt_exclusao IS NULL
		          ";
		$result = $this->db->query($qr_sql);
	}
	
	function combo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT ardm.cd_atendimento_recadastro_devolucao_motivo AS value, 
						   ardm.motivo AS text
					  FROM projetos.atendimento_recadastro_devolucao_motivo ardm
					 WHERE ardm.dt_exclusao IS NULL
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	
}
?>