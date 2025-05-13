<?php
class bloco_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT b.cd_bloco,
                   b.cd_grupo,
                   b.ds_bloco,
                   b.ds_bloco_descricao,
                   g.ds_grupo ||' - '|| g.ds_grupo_sigla AS ds_grupo,
                   (CASE WHEN b.fl_conhecimento = 'S' 
                         THEN 'Sim'
                         ELSE 'Não'
                   END) AS ds_conhecimento
              FROM rh_avaliacao.bloco b
              JOIN rh_avaliacao.grupo g
                ON g.cd_grupo = b.cd_grupo
               AND g.dt_exclusao IS NULL
             WHERE b.dt_exclusao IS NULL
             ".(intval($args['cd_grupo']) > 0 ? "AND b.cd_grupo = ".intval($args['cd_grupo']) : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_grupo()
    {
        $qr_sql = "
            SELECT cd_grupo AS value,
                   ds_grupo ||' - '|| ds_grupo_sigla AS text
              FROM rh_avaliacao.grupo
             WHERE dt_exclusao IS NULL
             ORDER BY ds_grupo ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_bloco)
    {
        $qr_sql = "
            SELECT cd_bloco,
                   cd_grupo,
                   ds_bloco,
                   ds_bloco_descricao,
                   fl_conhecimento
              FROM rh_avaliacao.bloco
             WHERE dt_exclusao IS NULL
               AND cd_bloco = ".intval($cd_bloco).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.bloco
                (
                    cd_grupo,
                    ds_bloco,
                    ds_bloco_descricao,
                    fl_conhecimento,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".(intval($args['cd_grupo']) != '' ? intval($args['cd_grupo']) : "DEFAULT").",
                    ".(trim($args['ds_bloco']) != '' ? str_escape($args['ds_bloco']) : "DEFAULT").",
                    ".(trim($args['ds_bloco_descricao']) != '' ? str_escape($args['ds_bloco_descricao']) : "DEFAULT").",
                    ".(trim($args['fl_conhecimento']) != '' ? "'".trim($args['fl_conhecimento'])."'" : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_bloco, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.bloco
               SET cd_grupo             = ".(intval($args['cd_grupo']) != '' ? intval($args['cd_grupo']) : "DEFAULT").",
                   ds_bloco             = ".(trim($args['ds_bloco']) != '' ? str_escape($args['ds_bloco']) : "DEFAULT").",
                   ds_bloco_descricao   = ".(trim($args['ds_bloco_descricao']) != '' ? str_escape($args['ds_bloco_descricao']) : "DEFAULT").",
                   fl_conhecimento      = ".(trim($args['fl_conhecimento']) != '' ? "'".trim($args['fl_conhecimento'])."'" : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_bloco = ".intval($cd_bloco).";";

        $this->db->query($qr_sql);
    }
}