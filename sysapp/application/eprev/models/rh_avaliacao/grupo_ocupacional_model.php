<?php
class Grupo_ocupacional_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_grupo_ocupacional,
			       ds_grupo_ocupacional
			  FROM rh_avaliacao.grupo_ocupacional
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_grupo_ocupacional)
	{
		$qr_sql = "
			SELECT cd_grupo_ocupacional,
				   ds_grupo_ocupacional
			  FROM rh_avaliacao.grupo_ocupacional
			 WHERE dt_exclusao IS NULL
			   AND cd_grupo_ocupacional = ".intval($cd_grupo_ocupacional).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO rh_avaliacao.grupo_ocupacional
				(
					ds_grupo_ocupacional,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['ds_grupo_ocupacional']) ? str_escape($args['ds_grupo_ocupacional']) : "DEFAULT").",
					".(intval($args['cd_usuario']) ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_grupo_ocupacional, $args = array())
	{
		$qr_sql = "
			UPDATE rh_avaliacao.grupo_ocupacional
			   SET ds_grupo_ocupacional = ".(trim($args['ds_grupo_ocupacional']) ? str_escape($args['ds_grupo_ocupacional']) : "DEFAULT").",
			   	   cd_usuario_alteracao = ".(intval($args['cd_usuario']) ? intval($args['cd_usuario']) : "DEFAULT").",
			   	   dt_alteracao			= CURRENT_TIMESTAMP
			 WHERE cd_grupo_ocupacional = ".intval($cd_grupo_ocupacional).";";

		$this->db->query($qr_sql);
	}
}