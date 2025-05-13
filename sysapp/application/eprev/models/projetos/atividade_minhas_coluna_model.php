<?php
class Atividade_minhas_coluna_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar() 
	{
		$qr_sql = "
			SELECT amc.ds_atividade_minhas_coluna,
			       amc.fl_info,
			       amc.nr_ordem
			  FROM projetos.atividade_minhas_coluna amc
			 WHERE amc.dt_exclusao IS NULL
			 ORDER BY amc.nr_ordem;";

		return $this->db->query($qr_sql)->result_array();
	}


	public function listar_ocultar($cd_usuario) 
	{
		$qr_sql = "
			SELECT amc.nr_ordem
			  FROM projetos.atividade_minhas_coluna amc
			  JOIN projetos.atividade_minhas_coluna_usuario amcu
			    ON amcu.cd_atividade_minhas_coluna = amc.cd_atividade_minhas_coluna
			 WHERE amc.dt_exclusao IS NULL
			   AND amcu.fl_oculta = 'S'
			   AND amcu.cd_usuario = ".intval($cd_usuario)."
			 ORDER BY amc.nr_ordem;";

		return $this->db->query($qr_sql)->result_array();
	}
}