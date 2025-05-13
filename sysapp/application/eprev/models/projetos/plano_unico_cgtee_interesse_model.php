<?php
class plano_unico_cgtee_interesse_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cp.cd_plano_unico_cgtee_interesse,
			       cp.nome,
				   cp.email,
				   cp.telefone_1,
				   cp.telefone_2,
				   cp.descricao,
				   TO_CHAR(cp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   (SELECT COUNT(*)
				      FROM projetos.plano_unico_cgtee_interesse_acompanhamento cpa
				     WHERE cpa.dt_exclusao IS NULL
					   AND cpa.cd_plano_unico_cgtee_interesse = cp.cd_plano_unico_cgtee_interesse) AS tl_acompanhamento
			  FROM projetos.plano_unico_cgtee_interesse cp
			 WHERE 1 = 1
			   ".(trim($args['fl_status']) == 'C' ? " AND (SELECT COUNT(*)
														     FROM projetos.plano_unico_cgtee_interesse_acompanhamento cpa
														    WHERE cpa.dt_exclusao IS NULL
															  AND cpa.cd_plano_unico_cgtee_interesse = cp.cd_plano_unico_cgtee_interesse) > 0" : "")."
			   ".(trim($args['fl_status']) == 'A' ? " AND (SELECT COUNT(*)
														     FROM projetos.plano_unico_cgtee_interesse_acompanhamento cpa
														    WHERE cpa.dt_exclusao IS NULL
															  AND cpa.cd_plano_unico_cgtee_interesse = cp.cd_plano_unico_cgtee_interesse) = 0" : "")."				
			   ".(((trim($args['dt_inclusao_ini']) != "") AND (trim($args['dt_inclusao_fim']) != "")) ? " AND DATE_TRUNC('day', cp.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "").";";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_plano_unico_cgtee_interesse,
			       nome,
				   email,
				   telefone_1,
				   telefone_2,
				   descricao,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM projetos.plano_unico_cgtee_interesse
			 WHERE cd_plano_unico_cgtee_interesse = ".intval($args['cd_plano_unico_cgtee_interesse']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cpa.cd_plano_unico_cgtee_interesse_acompanhamento,
				   TO_CHAR(cpa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   cpa.descricao,
				   uc.nome
			  FROM projetos.plano_unico_cgtee_interesse_acompanhamento cpa
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = cpa.cd_usuario_inclusao
			 WHERE cpa.dt_exclusao IS NULL
			   AND cpa.cd_plano_unico_cgtee_interesse = ".intval($args['cd_plano_unico_cgtee_interesse']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.plano_unico_cgtee_interesse_acompanhamento
			     (
                   cd_plano_unico_cgtee_interesse, 
                   descricao, 
				   cd_usuario_inclusao
				 )
            VALUES 
			     (
				  ".intval($args['cd_plano_unico_cgtee_interesse']).",
				  ".str_escape($args['descricao']).",
				  ".intval($args['cd_usuario'])."
				 );";
				 
		$result = $this->db->query($qr_sql);
	} 
	
	function excluir_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.plano_unico_cgtee_interesse_acompanhamento
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).", 
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_plano_unico_cgtee_interesse_acompanhamento = ".intval($args['cd_plano_unico_cgtee_interesse_acompanhamento']).";";
				 
		$result = $this->db->query($qr_sql);
	}
	
}
?>