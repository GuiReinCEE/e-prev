<?php

class atividade_cronograma_grupo_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT a.cd_atividade_cronograma_grupo,
				   a.ds_atividade_cronograma_grupo,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.atividade_cronograma_grupo a
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.dt_exclusao IS NULL
			 ".(trim($args['cd_usuario']) != '' ? "AND a.cd_usuario_inclusao = ". intval($args['cd_usuario']) : "")."
			 ORDER BY a.ds_atividade_cronograma_grupo ASC";

		$result = $this->db->query($qr_sql);
	}
	
	function usuarios(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT a.cd_usuario_inclusao AS value, 
			       uc.nome AS text
			  FROM projetos.atividade_cronograma_grupo a
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.dt_exclusao IS NULL
			 ORDER BY uc.nome ASC";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_atividade_cronograma_grupo,
				   ds_atividade_cronograma_grupo
			  FROM projetos.atividade_cronograma_grupo
			 WHERE cd_atividade_cronograma_grupo = ".intval($args['cd_atividade_cronograma_grupo']);
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_atividade_cronograma_grupo']) > 0)
		{
			$qr_sql = "
				UPDATE projetos.atividade_cronograma_grupo
				   SET ds_atividade_cronograma_grupo = '".trim($args['ds_atividade_cronograma_grupo'])."',
				       cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
					   dt_alteracao                  = CURRENT_TIMESTAMP
				 WHERE cd_atividade_cronograma_grupo = ".intval($args['cd_atividade_cronograma_grupo']);
				 
		}
		else
		{
			$qr_sql = "
				INSERT INTO projetos.atividade_cronograma_grupo
				     (
						ds_atividade_cronograma_grupo,
						cd_usuario_inclusao,
						cd_usuario_alteracao,
						dt_alteracao
					 )
			    VALUES
				     (
						'".trim($args['ds_atividade_cronograma_grupo'])."',
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario']).",
						CURRENT_TIMESTAMP
					 )";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE projetos.atividade_cronograma_grupo
			   SET dt_exclusao = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_atividade_cronograma_grupo = ".intval($args['cd_atividade_cronograma_grupo']);
			 
		$this->db->query($qr_sql);	
	}
}
?>