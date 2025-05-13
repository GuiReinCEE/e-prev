<?php
class Classe_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cl.cd_classe,
			       cl.ds_classe,
			       cr.ds_cargo
			  FROM rh_avaliacao.classe cl
			  JOIN rh_avaliacao.cargo cr
			    ON cr.cd_cargo = cl.cd_cargo
			 WHERE cl.dt_exclusao IS NULL
			 ".(intval($args['cd_cargo']) > 0 ? "AND cl.cd_cargo = ".intval($args['cd_cargo']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_cargo()
	{
		$qr_sql = "
			SELECT cd_cargo AS value,
				   ds_cargo AS text
			  FROM rh_avaliacao.cargo
	         WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_classe)
	{
		$qr_sql = "
			SELECT cd_classe,
				   ds_classe,
			       cd_cargo
			  FROM rh_avaliacao.classe
			 WHERE dt_exclusao IS NULL
			   AND cd_classe = ".intval($cd_classe).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_classe = intval($this->db->get_new_id('rh_avaliacao.classe', 'cd_classe'));

		$qr_sql = "
			INSERT INTO rh_avaliacao.classe
				(
					cd_classe,
					ds_classe,
					cd_cargo,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".intval($cd_classe).",
					".(trim($args['ds_classe']) != '' ? str_escape($args['ds_classe']) : "DEFAULT").",
					".(intval($args['cd_cargo']) > 0 ? intval($args['cd_cargo']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				);";

		if(count($args['padrao']) > 0)
        {
            $qr_sql .= "
                INSERT INTO rh_avaliacao.classe_padrao(cd_classe, ds_padrao, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_classe).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['padrao'])."')) x;";
        }

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_classe, $args = array())
	{
		$qr_sql = "
			UPDATE rh_avaliacao.classe
			   SET ds_classe 			= ".(trim($args['ds_classe']) != '' ? str_escape($args['ds_classe']) : "DEFAULT").",
				   cd_cargo 			= ".(intval($args['cd_cargo']) > 0 ? intval($args['cd_cargo']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao 		= CURRENT_TIMESTAMP
			 WHERE cd_classe = ".intval($cd_classe).";";

		if(count($args['padrao']) > 0)
        {
            $qr_sql = "
                UPDATE rh_avaliacao.classe_padrao
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_classe           = ".intval($cd_classe)."
                   AND dt_exclusao         IS NULL
                   AND ds_padrao           NOT IN ('".implode(",", $args['padrao'])."');

                INSERT INTO rh_avaliacao.classe_padrao
                    (
                       cd_classe, 
                       ds_padrao, 
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                    )
                SELECT ".intval($cd_classe).", 
                       x.column1, 
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['padrao'])."')) x
                 WHERE x.column1 NOT IN (SELECT pc.ds_padrao
                                           FROM rh_avaliacao.classe_padrao pc
                                          WHERE pc.cd_classe = ".intval($cd_classe)."
                                            AND pc.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql = "
                UPDATE rh_avaliacao.classe_padrao
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_classe = ".intval($cd_classe)."
                   AND dt_exclusao IS NULL;";
        }

		$this->db->query($qr_sql);
	}

	public function listar_padrao($cd_classe)
	{
		$qr_sql = "
			SELECT ds_padrao
			  FROM rh_avaliacao.classe_padrao
			 WHERE dt_exclusao IS NULL
			   AND cd_classe = ".intval($cd_classe)."
			 ORDER BY ds_padrao;";

		return $this->db->query($qr_sql)->result_array();
	}
}