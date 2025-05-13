<?php
class Cargo_area_atuacao_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT caa.cd_cargo_area_atuacao,
			       caa.cd_gerencia,
			       cr.ds_cargo,
			       aa.ds_area_atuacao,
			       go.ds_grupo_ocupacional,
			       caa.ds_conhecimento_especifico
			  FROM rh_avaliacao.cargo_area_atuacao caa
			  JOIN rh_avaliacao.cargo cr
			    ON cr.cd_cargo = caa.cd_cargo
			  JOIN rh_avaliacao.grupo_ocupacional go
			    ON go.cd_grupo_ocupacional = cr.cd_grupo_ocupacional
	          LEFT JOIN rh_avaliacao.area_atuacao aa
			    ON aa.cd_area_atuacao = caa.cd_area_atuacao
			 WHERE caa.dt_exclusao IS NULL
			   AND cr.dt_exclusao  IS NULL
			   AND go.dt_exclusao  IS NULL
			   AND aa.dt_exclusao  IS NULL
			 ".(trim($args['cd_gerencia']) != '' ? "AND caa.cd_gerencia = ".str_escape($args['cd_gerencia']) : "")."
			 ".(intval($args['cd_cargo']) > 0 ? "AND caa.cd_cargo = ".intval($args['cd_cargo']) : "")."
			 ".(intval($args['cd_area_atuacao']) > 0 ? "AND caa.cd_area_atuacao = ".intval($args['cd_area_atuacao']) : "")."
			 ".(intval($args['cd_grupo_ocupacional']) > 0 ? "AND cr.cd_grupo_ocupacional = ".intval($args['cd_grupo_ocupacional']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM funcoes.get_gerencias_vigente();";

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

	public function get_cargo()
	{
		$qr_sql = "
			SELECT cd_cargo AS value,
			       ds_cargo AS text
			  FROM rh_avaliacao.cargo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_cargo ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_area_atuacao()
	{
		$qr_sql = "
			SELECT cd_area_atuacao AS value,
			       ds_area_atuacao AS text
			  FROM rh_avaliacao.area_atuacao
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_area_atuacao ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_cargo_area_atuacao)
	{
		$qr_sql = "
			SELECT cd_cargo_area_atuacao,
			       cd_gerencia,
			       cd_cargo,
			       cd_area_atuacao,
			       ds_conhecimento_especifico
			  FROM rh_avaliacao.cargo_area_atuacao
			 WHERE dt_exclusao IS NULL
			   AND cd_cargo_area_atuacao = ".intval($cd_cargo_area_atuacao).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO rh_avaliacao.cargo_area_atuacao
				(
			       cd_gerencia,
			       cd_cargo,
			       cd_area_atuacao,
			       ds_conhecimento_especifico,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
					".(intval($args['cd_cargo']) > 0 ? intval($args['cd_cargo']) : "DEFAULT").",
					".(intval($args['cd_area_atuacao']) > 0 ? intval($args['cd_area_atuacao']) : "DEFAULT").",
					".(trim($args['ds_conhecimento_especifico']) != '' ? str_escape($args['ds_conhecimento_especifico']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_cargo_area_atuacao, $args = array())
	{
		$qr_sql = "
			UPDATE rh_avaliacao.cargo_area_atuacao
			   SET cd_gerencia 		     	  = ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
			       cd_cargo 		          = ".(intval($args['cd_cargo']) > 0 ? intval($args['cd_cargo']) : "DEFAULT").",
			       cd_area_atuacao 		      = ".(intval($args['cd_area_atuacao']) > 0 ? intval($args['cd_area_atuacao']) : "DEFAULT").",
			       ds_conhecimento_especifico = ".(trim($args['ds_conhecimento_especifico']) != '' ? str_escape($args['ds_conhecimento_especifico']) : "DEFAULT").",
			       cd_usuario_alteracao       = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
			       dt_alteracao 		      = CURRENT_TIMESTAMP
			 WHERE cd_cargo_area_atuacao = ".intval($cd_cargo_area_atuacao).";";

		$this->db->query($qr_sql);
	}
}