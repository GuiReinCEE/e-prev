<?php

class correspondencia_recebida_grupo_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function grupo(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_correspondencia_recebida_grupo AS value, 
				   ds_nome AS text 
			  FROM projetos.correspondencia_recebida_grupo 
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_nome;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_correspondencia_recebida_grupo, 
				   ds_nome 
			  FROM projetos.correspondencia_recebida_grupo 
			 WHERE dt_exclusao IS NULL
			 ".(trim($args['cd_correspondencia_recebida_grupo']) != '' ? "AND cd_correspondencia_recebida_grupo = ".intval($args['cd_correspondencia_recebida_grupo']) : "")."
			 ORDER BY ds_nome;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function usuario_grupo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_correspondencia_recebida_grupo_usuario,
			       funcoes.get_usuario_nome(cd_usuario) AS usuario
			  FROM projetos.correspondencia_recebida_grupo_usuario
			 WHERE dt_exclusao IS NULL
			   AND cd_correspondencia_recebida_grupo = ".intval($args['cd_correspondencia_recebida_grupo']).";";
		 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_correspondencia_recebida_grupo,
				   ds_nome
			  FROM projetos.correspondencia_recebida_grupo
			 WHERE cd_correspondencia_recebida_grupo = ".intval($args['cd_correspondencia_recebida_grupo']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_correspondencia_recebida_grupo']) == 0)
		{
			$cd_correspondencia_recebida_grupo = $this->db->get_new_id("projetos.correspondencia_recebida_grupo", "cd_correspondencia_recebida_grupo");
		
			$qr_sql = "
				INSERT INTO projetos.correspondencia_recebida_grupo
				     (
					   cd_correspondencia_recebida_grupo,
					   ds_nome,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
				VALUES 
					 (
					   ".intval($cd_correspondencia_recebida_grupo).",
					   ".(trim($args['ds_nome']) != '' ? "'".trim($args['ds_nome'])."'" : "DEFAULT")." ,
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 )";
		}
		else
		{
			$cd_correspondencia_recebida_grupo = intval($args['cd_correspondencia_recebida_grupo']);
			
			$qr_sql = "
				UPDATE projetos.correspondencia_recebida_grupo
				   SET ds_nome              = ".(trim($args['ds_nome']) != '' ? "'".trim($args['ds_nome'])."'" : "DEFAULT").",
				       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_correspondencia_recebida_grupo = ".intval($args['cd_correspondencia_recebida_grupo']).";";
		}

		$result = $this->db->query($qr_sql);
		
		return $cd_correspondencia_recebida_grupo;
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_grupo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida_grupo = ".intval($args['cd_correspondencia_recebida_grupo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function usuario_not_grupo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE codigo NOT IN (SELECT cd_usuario
									FROM projetos.correspondencia_recebida_grupo_usuario
								   WHERE dt_exclusao IS NULL
								     AND cd_correspondencia_recebida_grupo = ".intval($args['cd_correspondencia_recebida_grupo']).")
			   AND tipo NOT IN('X', 'T')
			 ORDER BY nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_usuario(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.correspondencia_recebida_grupo_usuario
			     (
                   cd_correspondencia_recebida_grupo, 
                   cd_usuario,
				   cd_usuario_inclusao
				 )
            VALUES 
			     (
				   ".intval($args['cd_correspondencia_recebida_grupo']).",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario_inclusao'])."
			     );";
			
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_usuario(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_grupo_usuario
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida_grupo_usuario = ".intval($args['cd_correspondencia_recebida_grupo_usuario']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>