<?php
class Cargo_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT c.cd_cargo,
			       c.ds_cargo,
				   go.ds_grupo_ocupacional,
				   f.ds_formacao,
				   c.ds_conhecimento_generico
			  FROM rh_avaliacao.cargo c
			  JOIN rh_avaliacao.grupo_ocupacional go
			    ON go.cd_grupo_ocupacional = c.cd_grupo_ocupacional
			  JOIN rh_avaliacao.formacao f
			    ON f.cd_formacao = c.cd_formacao
			 WHERE c.dt_exclusao IS NULL
			 ".(intval($args['cd_grupo_ocupacional']) > 0 ? "AND c.cd_grupo_ocupacional = ".intval($args['cd_grupo_ocupacional']) : "")."
			 ".(intval($args['cd_formacao']) > 0 ? "AND c.cd_formacao = ".intval($args['cd_formacao']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_grupo_ocupacional()
	{
		$qr_sql = "
			SELECT cd_grupo_ocupacional AS value,
				   ds_grupo_ocupacional AS text 
			  FROM rh_avaliacao.grupo_ocupacional
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_grupo_ocupacional ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_formacao()
	{
		$qr_sql = "
			SELECT cd_formacao AS value,
				   ds_formacao AS text 
			  FROM rh_avaliacao.formacao
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_formacao ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_cargo)
	{
		$qr_sql = "
			SELECT cd_cargo,
				   ds_cargo,
				   cd_grupo_ocupacional,
				   cd_formacao,
				   ds_conhecimento_generico
			  FROM rh_avaliacao.cargo
			 WHERE dt_exclusao IS NULL
			   AND cd_cargo = ".intval($cd_cargo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO rh_avaliacao.cargo
				(
					ds_cargo,
					cd_grupo_ocupacional,
					cd_formacao,
					ds_conhecimento_generico,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['ds_cargo']) != '' ? str_escape($args['ds_cargo']) : "DEFAULT").",
					".(intval($args['cd_grupo_ocupacional']) > 0 ? intval($args['cd_grupo_ocupacional']) : "DEFAULT").",
					".(intval($args['cd_formacao']) > 0 ? intval($args['cd_formacao']) : "DEFAULT").",
					".(trim($args['ds_conhecimento_generico']) != '' ? str_escape($args['ds_conhecimento_generico']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_cargo, $args = array())
	{
		$qr_sql = "
			UPDATE rh_avaliacao.cargo
			   SET ds_cargo 			= ".(trim($args['ds_cargo']) != '' ? str_escape($args['ds_cargo']) : "DEFAULT").",
				   cd_grupo_ocupacional = ".(intval($args['cd_grupo_ocupacional']) > 0 ? intval($args['cd_grupo_ocupacional']) : "DEFAULT").",
				   cd_formacao          = ".(intval($args['cd_formacao']) > 0 ? intval($args['cd_formacao']) : "DEFAULT").",
				   ds_conhecimento_generico        = ".(trim($args['ds_conhecimento_generico']) != '' ? str_escape($args['ds_conhecimento_generico']) : "DEFAULT").",
			   	   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			   	   dt_alteracao			= CURRENT_TIMESTAMP
			 WHERE cd_cargo = ".intval($cd_cargo).";";

		$this->db->query($qr_sql);
	}
}