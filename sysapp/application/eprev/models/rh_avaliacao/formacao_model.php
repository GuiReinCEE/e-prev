<?php
class Formacao_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_formacao,
				   ds_formacao,
				   (CASE WHEN tp_nivel = 'M' THEN 'Médio'
				         WHEN tp_nivel = 'S' THEN 'Superior'
				    END) AS ds_nivel
			  FROM rh_avaliacao.formacao
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_formacao)
	{
		$qr_sql = "
			SELECT cd_formacao,
				   ds_formacao,
				   tp_nivel
			  FROM rh_avaliacao.formacao
			 WHERE dt_exclusao IS NULL
			   AND cd_formacao = ".intval($cd_formacao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO rh_avaliacao.formacao
				(
					ds_formacao,
					tp_nivel,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['ds_formacao']) != '' ? str_escape($args['ds_formacao']) : "DEFAULT").",
					".(trim($args['tp_nivel']) != '' ? str_escape($args['tp_nivel']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_formacao, $args = array())
	{
		$qr_sql = "
			UPDATE rh_avaliacao.formacao
			   SET ds_formacao 			= ".(trim($args['ds_formacao']) != '' ? str_escape($args['ds_formacao']) : "DEFAULT").",
			   	   tp_nivel 			= ".(trim($args['tp_nivel']) != '' ? str_escape($args['tp_nivel']) : "DEFAULT").",
				   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				   dt_alteracao 		= CURRENT_TIMESTAMP
			 WHERE cd_formacao = ".intval($cd_formacao).";";

		$this->db->query($qr_sql);
	}
}