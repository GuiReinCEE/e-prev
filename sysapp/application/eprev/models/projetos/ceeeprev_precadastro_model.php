<?php
class ceeeprev_precadastro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cp.cd_ceeeprev_precadastro,
			       cp.nome,
				   cp.email,
				   cp.telefone_1,
				   cp.telefone_2,
				   cp.descricao,
				   TO_CHAR(cp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   (SELECT COUNT(*)
				      FROM projetos.ceeeprev_precadastro_acompanhamento cpa
				     WHERE cpa.dt_exclusao IS NULL
					   AND cpa.cd_ceeeprev_precadastro = cp.cd_ceeeprev_precadastro) AS tl_acompanhamento
			  FROM projetos.ceeeprev_precadastro cp
			 WHERE 1 = 1
			   ".(trim($args['fl_status']) == 'C' ? " AND (SELECT COUNT(*)
														     FROM projetos.ceeeprev_precadastro_acompanhamento cpa
														    WHERE cpa.dt_exclusao IS NULL
															  AND cpa.cd_ceeeprev_precadastro = cp.cd_ceeeprev_precadastro) > 0" : "")."
			   ".(trim($args['fl_status']) == 'A' ? " AND (SELECT COUNT(*)
														     FROM projetos.ceeeprev_precadastro_acompanhamento cpa
														    WHERE cpa.dt_exclusao IS NULL
															  AND cpa.cd_ceeeprev_precadastro = cp.cd_ceeeprev_precadastro) = 0" : "")."				
			   ".(((trim($args['dt_inclusao_ini']) != "") AND (trim($args['dt_inclusao_fim']) != "")) ? " AND DATE_TRUNC('day', cp.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "").";";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_ceeeprev_precadastro,
			       nome,
				   email,
				   telefone_1,
				   telefone_2,
				   descricao,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM projetos.ceeeprev_precadastro
			 WHERE cd_ceeeprev_precadastro = ".intval($args['cd_ceeeprev_precadastro']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cpa.cd_ceeeprev_precadastro_acompanhamento,
				   TO_CHAR(cpa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   cpa.descricao,
				   uc.nome
			  FROM projetos.ceeeprev_precadastro_acompanhamento cpa
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = cpa.cd_usuario_inclusao
			 WHERE cpa.dt_exclusao IS NULL
			   AND cpa.cd_ceeeprev_precadastro = ".intval($args['cd_ceeeprev_precadastro']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.ceeeprev_precadastro_acompanhamento
			     (
                   cd_ceeeprev_precadastro, 
                   descricao, 
				   cd_usuario_inclusao
				 )
            VALUES 
			     (
				  ".intval($args['cd_ceeeprev_precadastro']).",
				  ".str_escape($args['descricao']).",
				  ".intval($args['cd_usuario'])."
				 );";
				 
		$result = $this->db->query($qr_sql);
	} 
	
	function excluir_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.ceeeprev_precadastro_acompanhamento
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).", 
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_ceeeprev_precadastro_acompanhamento = ".intval($args['cd_ceeeprev_precadastro_acompanhamento']).";";
				 
		$result = $this->db->query($qr_sql);
	}
	
}
?>