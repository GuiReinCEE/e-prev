<?php
class Pergunta_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT p.cd_pergunta,
                   p.cd_bloco,
                   p.ds_pergunta,
                   b.ds_bloco,
                   b.ds_bloco_descricao
              FROM rh_avaliacao.pergunta p
              JOIN rh_avaliacao.bloco b
                ON b.cd_bloco = p.cd_bloco
               AND b.dt_exclusao IS NULL
             WHERE p.dt_exclusao IS NULL
             ".(intval($args['cd_bloco']) > 0 ? "AND p.cd_bloco = ".intval($args['cd_bloco']) : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_bloco()
    {
        $qr_sql = "
            SELECT cd_bloco AS value,
                   ds_bloco AS text
              FROM rh_avaliacao.bloco
             WHERE dt_exclusao IS NULL
             ORDER BY ds_bloco ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_classe()
    {
        $qr_sql = "
            SELECT cl.cd_classe AS value,
                   TRIM(cg.ds_cargo || CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END) AS text
              FROM rh_avaliacao.classe cl
              JOIN rh_avaliacao.cargo cg
                ON cg.cd_cargo = cl.cd_cargo
             WHERE cl.dt_exclusao IS NULL
               AND cg.dt_exclusao IS NULL
             ORDER BY text ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_classes($cd_pergunta)
    {
        $qr_sql = "
            SELECT pc.cd_classe,
                   TRIM(cg.ds_cargo || CASE WHEN cl.ds_classe IS NOT NULL THEN ' ' || cl.ds_classe ELSE '' END) AS ds_classe
              FROM rh_avaliacao.pergunta_classe pc
              JOIN rh_avaliacao.classe cl
                ON cl.cd_classe = pc.cd_classe
              JOIN rh_avaliacao.cargo cg
                ON cg.cd_cargo = cl.cd_cargo
             WHERE cl.dt_exclusao IS NULL
               AND cg.dt_exclusao IS NULL
               AND pc.dt_exclusao IS NULL
               AND pc.cd_pergunta = ".intval($cd_pergunta)."
             ORDER BY ds_classe ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_pergunta)
    {
        $qr_sql = "
            SELECT cd_pergunta,
                   cd_bloco,
                   ds_pergunta
              FROM rh_avaliacao.pergunta
             WHERE dt_exclusao IS NULL
               AND cd_pergunta = ".intval($cd_pergunta).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_pergunta = intval($this->db->get_new_id('rh_avaliacao.pergunta', 'cd_pergunta'));

        $qr_sql = "
            INSERT INTO rh_avaliacao.pergunta
                (
                    cd_pergunta,
                    cd_bloco,
                    ds_pergunta,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".intval($cd_pergunta).",
                    ".(intval($args['cd_bloco']) > 0 ? intval($args['cd_bloco']) : "DEFAULT").",
                    ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                );";

        if(count($args['classe']) > 0)
        {
            $qr_sql .= "
                INSERT INTO rh_avaliacao.pergunta_classe(cd_pergunta, cd_classe, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_pergunta).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['classe']).")) x;";
        }

        $this->db->query($qr_sql);

        return $cd_pergunta;
    }

    public function atualizar($cd_pergunta, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.pergunta
               SET cd_bloco              = ".(intval($args['cd_bloco']) > 0 ? intval($args['cd_bloco']) : "DEFAULT").",
                   ds_pergunta           = ".(trim($args['ds_pergunta']) != '' ? str_escape($args['ds_pergunta']) : "DEFAULT").",
                   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
                   dt_alteracao          = CURRENT_TIMESTAMP
             WHERE cd_pergunta = ".intval($cd_pergunta).";";


        if(count($args['classe']) > 0)
        {
            $qr_sql = "
                UPDATE rh_avaliacao.pergunta_classe
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_pergunta        = ".intval($cd_pergunta)."
                   AND dt_exclusao        IS NULL
                   AND cd_pergunta_classe NOT IN (".implode(",", $args['classe']).");

                INSERT INTO rh_avaliacao.pergunta_classe
                    (
                       cd_pergunta, 
                       cd_classe, 
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                    )
                SELECT ".intval($cd_pergunta).", 
                       x.column1, 
                       ".intval($args['cd_usuario']).", 
                       ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['classe']).")) x
                 WHERE x.column1 NOT IN (SELECT pc.cd_classe
                                           FROM rh_avaliacao.pergunta_classe pc
                                          WHERE pc.cd_pergunta = ".intval($cd_pergunta)."
                                            AND pc.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE rh_avaliacao.pergunta_classe
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_pergunta = ".intval($cd_pergunta)."
                   AND dt_exclusao IS NULL;";
        }

        $this->db->query($qr_sql);
    }

}