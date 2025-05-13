<?php
class area_atuacao_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_area_atuacao,
				   ds_area_atuacao
			  FROM rh_avaliacao.area_atuacao
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_area_atuacao)
	{
		$qr_sql = "
			SELECT cd_area_atuacao,
				   ds_area_atuacao
			  FROM rh_avaliacao.area_atuacao
			 WHERE dt_exclusao IS NULL
			   AND cd_area_atuacao = ".intval($cd_area_atuacao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO rh_avaliacao.area_atuacao
				(
					ds_area_atuacao,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['ds_area_atuacao']) != '' ? str_escape($args['ds_area_atuacao']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_area_atuacao, $args = array())
	{
		$qr_sql = "
			UPDATE rh_avaliacao.area_atuacao
			   SET ds_area_atuacao 			= ".(trim($args['ds_area_atuacao']) != '' ? str_escape($args['ds_area_atuacao']) : "DEFAULT").",
				   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				   dt_alteracao 		= CURRENT_TIMESTAMP
			 WHERE cd_area_atuacao = ".intval($cd_area_atuacao).";";

		$this->db->query($qr_sql);
	}
}