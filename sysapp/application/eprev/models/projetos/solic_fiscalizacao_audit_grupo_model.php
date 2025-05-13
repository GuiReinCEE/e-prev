<?php
class Solic_fiscalizacao_audit_grupo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_grupos()
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_grupo,
			       ds_grupo,
			       ds_email_grupo
			  FROM projetos.solic_fiscalizacao_audit_grupo
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_integrantes_grupos($cd_solic_fiscalizacao_audit_grupo)
	{
		$qr_sql = "
			SELECT funcoes.get_usuario_nome(cd_usuario) AS ds_integrante,
				   cd_usuario
			  FROM projetos.solic_fiscalizacao_audit_grupo_integrante
			 WHERE dt_exclusao IS NULL
			   AND cd_solic_fiscalizacao_audit_grupo = ".intval($cd_solic_fiscalizacao_audit_grupo).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_solic_fiscalizacao_audit_grupo)
	{
		$qr_sql = "
			SELECT cd_solic_fiscalizacao_audit_grupo,
			       ds_grupo,
			       ds_email_grupo
			  FROM projetos.solic_fiscalizacao_audit_grupo
			 WHERE dt_exclusao IS NULL
			   AND cd_solic_fiscalizacao_audit_grupo = ".$cd_solic_fiscalizacao_audit_grupo.";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_usuarios()
	{
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE tipo NOT IN ('X', 'T')
			 ORDER BY text ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar($args = array())
	{
		$cd_solic_fiscalizacao_audit_grupo = $this->db->get_new_id('projetos.solic_fiscalizacao_audit_grupo', 'cd_solic_fiscalizacao_audit_grupo');

		$qr_sql = "
			INSERT INTO projetos.solic_fiscalizacao_audit_grupo
				(
					cd_solic_fiscalizacao_audit_grupo,
					ds_grupo,
					ds_email_grupo,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".intval($cd_solic_fiscalizacao_audit_grupo).",
					".(trim($args['ds_grupo']) != '' ? str_escape($args['ds_grupo']) : "DEFAULT").",
					".(trim($args['ds_email_grupo']) != '' ? str_escape($args['ds_email_grupo']) : "DEFAULT").",
					".(intval($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);

		return $cd_solic_fiscalizacao_audit_grupo;
	}

	public function atualizar($cd_solic_fiscalizacao_audit_grupo, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.solic_fiscalizacao_audit_grupo
			   SET ds_grupo             = ".(trim($args['ds_grupo']) != '' ? str_escape($args['ds_grupo']) : "DEFAULT").",
				   ds_email_grupo       = ".(trim($args['ds_email_grupo']) != '' ? str_escape($args['ds_email_grupo']) : "DEFAULT").",
				   cd_usuario_alteracao = ".(intval($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")."
			 WHERE cd_solic_fiscalizacao_audit_grupo = ".intval($cd_solic_fiscalizacao_audit_grupo).";";

		$this->db->query($qr_sql);
	}

    public function salvar_integrantes_grupo($cd_solic_fiscalizacao_audit_grupo, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_grupo_integrante
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE dt_exclusao 						 IS NULL
               AND cd_solic_fiscalizacao_audit_grupo = ".intval($cd_solic_fiscalizacao_audit_grupo)."
               AND cd_usuario 						 NOT IN (".trim($args['cd_usuario_grupo_u']).");
                
            INSERT INTO projetos.solic_fiscalizacao_audit_grupo_integrante
            	(
            		cd_solic_fiscalizacao_audit_grupo, 
            		cd_usuario, 
            		cd_usuario_inclusao,
            		cd_usuario_alteracao
            	)
             SELECT ".intval($cd_solic_fiscalizacao_audit_grupo).", 
            		x.column1, 
            		".intval($args['cd_usuario']).", 
            		".intval($args['cd_usuario'])."
               FROM (VALUES (".$args['cd_usuario_grupo_i'].")) x
              WHERE x.column1 NOT IN (SELECT a.cd_usuario
                                        FROM projetos.solic_fiscalizacao_audit_grupo_integrante a
                                       WHERE a.cd_solic_fiscalizacao_audit_grupo = ".intval($cd_solic_fiscalizacao_audit_grupo)."
                                         AND a.dt_exclusao IS NULL);";

        $this->db->query($qr_sql);
    }

    public function remover_integrantes_grupo($cd_solic_fiscalizacao_audit_grupo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_grupo_integrante
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE dt_exclusao   					 IS NULL
               AND cd_solic_fiscalizacao_audit_grupo = ".intval($cd_solic_fiscalizacao_audit_grupo).";";

        $this->db->query($qr_sql);
    }
}