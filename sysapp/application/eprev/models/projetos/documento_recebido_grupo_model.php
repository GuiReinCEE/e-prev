<?php

class Documento_recebido_grupo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function grupo(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_documento_recebido_grupo AS value, 
				   ds_nome AS text 
			  FROM projetos.documento_recebido_grupo 
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_nome;";
			
		$result = $this->db->query($qr_sql);
	}

	function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_documento_recebido_grupo, 
				   ds_nome,
				   email_grupo
			  FROM projetos.documento_recebido_grupo 
			 WHERE dt_exclusao IS NULL
			 ".(trim($args["cd_documento_recebido_grupo"]) != "" ? "AND cd_documento_recebido_grupo = ".intval($args["cd_documento_recebido_grupo"]) : "")."
			 ORDER BY ds_nome;";
			
		$result = $this->db->query($qr_sql);
	}

	function usuario_grupo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_documento_recebido_grupo_usuario,
			       funcoes.get_usuario_nome(cd_usuario) AS usuario
			  FROM projetos.documento_recebido_grupo_usuario
			 WHERE dt_exclusao IS NULL
			   AND cd_documento_recebido_grupo = ".intval($args["cd_documento_recebido_grupo"]).";";
		 
		$result = $this->db->query($qr_sql);
	}

	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_documento_recebido_grupo,
				   email_grupo,
				   ds_nome
			  FROM projetos.documento_recebido_grupo
			 WHERE cd_documento_recebido_grupo = ".intval($args["cd_documento_recebido_grupo"]).";";
			   
		$result = $this->db->query($qr_sql);
	}

	function salvar(&$result, $args=array())
	{
		if(intval($args["cd_documento_recebido_grupo"]) == 0)
		{
			$cd_documento_recebido_grupo = $this->db->get_new_id("projetos.documento_recebido_grupo", "cd_documento_recebido_grupo");
		
			$qr_sql = "
				INSERT INTO projetos.documento_recebido_grupo
				     (
					   cd_documento_recebido_grupo,
					   ds_nome,
					   email_grupo,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
				VALUES 
					 (
					   ".intval($cd_documento_recebido_grupo).",
					   ".(trim($args["ds_nome"]) != '' ? str_escape($args["ds_nome"]) : "DEFAULT").",
					   ".(trim($args["email_grupo"]) != '' ? str_escape($args["email_grupo"]) : "DEFAULT")." ,
					   ".intval($args["cd_usuario"]).",
					   ".intval($args["cd_usuario"])."
					 )";
		}
		else
		{
			$cd_documento_recebido_grupo = intval($args["cd_documento_recebido_grupo"]);
			
			$qr_sql = "
				UPDATE projetos.documento_recebido_grupo
				   SET ds_nome              = ".(trim($args["ds_nome"]) != '' ? str_escape($args["ds_nome"]) : "DEFAULT").",
				       email_grupo          = ".(trim($args["email_grupo"]) != '' ? str_escape($args["email_grupo"]) : "DEFAULT")." ,
				       cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_documento_recebido_grupo = ".intval($args['cd_documento_recebido_grupo']).";";
		}

		$result = $this->db->query($qr_sql);
		
		return $cd_documento_recebido_grupo;
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.documento_recebido_grupo
			   SET cd_usuario_exclusao = ".intval($args["cd_usuario"]).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_documento_recebido_grupo = ".intval($args["cd_documento_recebido_grupo"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function usuario_not_grupo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE codigo NOT IN (SELECT cd_usuario
									FROM projetos.documento_recebido_grupo_usuario
								   WHERE dt_exclusao IS NULL
								     AND cd_documento_recebido_grupo = ".intval($args["cd_documento_recebido_grupo"]).")
			   AND tipo NOT IN('X', 'T')
			 ORDER BY nome;";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar_usuario(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.documento_recebido_grupo_usuario
			     (
                   cd_documento_recebido_grupo, 
                   cd_usuario,
				   cd_usuario_inclusao
				 )
            VALUES 
			     (
				   ".intval($args["cd_documento_recebido_grupo"]).",
				   ".intval($args["cd_usuario"]).",
				   ".intval($args["cd_usuario_inclusao"])."
			     );";
			
		$result = $this->db->query($qr_sql);
	}

	function excluir_usuario(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.documento_recebido_grupo_usuario
			   SET cd_usuario_exclusao = ".intval($args["cd_usuario"]).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_documento_recebido_grupo_usuario = ".intval($args["cd_documento_recebido_grupo_usuario"]).";";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>