<?php
class Pergunta_resposta_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($cd_usuario, $args = array())
    {
        $qr_sql = "  
            SELECT cd_pergunta_resposta,
                   projetos.pergunta_resposta_ano_numero(nr_ano, nr_pergunta_resposta) AS nr_ano_pergunta,
                   ds_pergunta,
                   ds_resposta,
                   cd_gerencia_responsavel,
                   funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_usuario_responsavel,
                   TO_CHAR(dt_encaminha_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminha_responsavel,
                   funcoes.get_usuario_nome(cd_usuario_encaminha_reponsavel) AS ds_usuario_encaminha_reponsavel,
                   TO_CHAR(dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
                   cd_usuario_resposta,
                   cd_usuario_responsavel,
                   cd_usuario_inclusao
              FROM projetos.pergunta_resposta
             WHERE dt_exclusao IS NULL
               AND (cd_usuario_inclusao = ".intval($cd_usuario)." 
                OR dt_resposta IS NOT NULL 
                OR cd_usuario_responsavel = ".intval($cd_usuario)."
                OR (SELECT COUNT(*)
                      FROM projetos.usuarios_controledi
                     WHERE codigo   = ".intval($cd_usuario)."
                       AND indic_09 = '*') > 0);";

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

	public function get_usuario_responsavel($cd_gerencia_responsavel)
    {
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE divisao = '".trim($cd_gerencia_responsavel)."'
			   AND tipo    NOT IN ('X')
			 ORDER BY nome;";
				  
		return $this->db->query($qr_sql)->result_array();
    }  

    public function carrega($cd_pergunta_resposta, $cd_usuario)
    {
        $qr_sql = "
            SELECT cd_pergunta_resposta,
                   cd_gerencia_responsavel,
                   cd_usuario_responsavel,
                   cd_usuario_inclusao,
                   nr_pergunta_resposta,
                   nr_ano,
                   ds_pergunta,
                   ds_resposta,
                   funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
                   funcoes.get_usuario(cd_usuario_responsavel) AS ds_usuario_responsavel_email,
                   funcoes.get_usuario(cd_usuario_inclusao) AS ds_usuario_inclusao_email,
                   TO_CHAR(dt_encaminha_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminha_responsavel,
                   TO_CHAR(dt_resposta, 'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
                   (SELECT CASE WHEN COUNT(*) > 0 THEN 'S' 
                                ELSE 'N' 
                           END
                      FROM projetos.usuarios_controledi
                     WHERE codigo   = ".intval($cd_usuario)."
                       AND indic_09 = '*') AS fl_usuario_rh
              FROM projetos.pergunta_resposta
             WHERE cd_pergunta_resposta = ".intval($cd_pergunta_resposta)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_pergunta_resposta = intval($this->db->get_new_id('projetos.pergunta_resposta', 'cd_pergunta_resposta'));

        $qr_sql = "
            INSERT INTO projetos.pergunta_resposta
                (
                    cd_pergunta_resposta,
                    nr_pergunta_resposta,
                    nr_ano,
                    ds_pergunta,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".(intval($cd_pergunta_resposta) > 0 ? intval($cd_pergunta_resposta) : "DEFAULT").",
                    ".(intval($args['nr_pergunta_resposta']) > 0 ? intval($args['nr_pergunta_resposta']) : "DEFAULT").",
                    ".(intval($args['nr_ano']) > 0 ? intval($args['nr_ano']) : "DEFAULT").",
                    ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."

                );";

        $this->db->query($qr_sql);

        return $cd_pergunta_resposta;
    }

    public function atualizar($cd_pergunta_resposta, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.pergunta_resposta
               SET ds_pergunta          = ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").",
                   nr_pergunta_resposta = ".(intval($args['nr_pergunta_resposta']) > 0 ? intval($args['nr_pergunta_resposta']) : "DEFAULT").",
                   nr_ano               = ".(intval($args['nr_ano']) > 0 ? intval($args['nr_ano']) : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_pergunta_resposta = ".intval($cd_pergunta_resposta).";";

        $this->db->query($qr_sql);
    }

    public function salvar_responsavel($args = array())
    {
        $qr_sql = "
            UPDATE projetos.pergunta_resposta
               SET cd_gerencia_responsavel           = ".(trim($args['cd_gerencia_responsavel']) != '' ? str_escape($args['cd_gerencia_responsavel']) : "DEFAULT").", 
                   cd_usuario_responsavel            = ".(intval($args['cd_usuario_responsavel']) > 0 ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                   cd_usuario_alteracao              = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao                      = CURRENT_TIMESTAMP,
                   cd_usuario_encaminha_reponsavel   = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_encaminha_responsavel          = CURRENT_TIMESTAMP
             WHERE cd_pergunta_resposta = ".intval($args['cd_pergunta_resposta']).";";

        $this->db->query($qr_sql);
    }

    public function salvar_resposta($args = array())
    {
        $qr_sql = "
            UPDATE projetos.pergunta_resposta
               SET ds_resposta          = ".(trim($args['ds_resposta']) != '' ? str_escape($args['ds_resposta']) : "DEFAULT").",
                   cd_usuario_resposta  = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_resposta          = CURRENT_TIMESTAMP,
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_pergunta_resposta = ".intval($args['cd_pergunta_resposta']).";";

        $this->db->query($qr_sql);
    }
}