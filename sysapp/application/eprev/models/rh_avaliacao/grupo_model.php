<?php
class Grupo_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT cd_grupo,
                   ds_grupo || ' - ' || ds_grupo_sigla AS ds_grupo
              FROM rh_avaliacao.grupo
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_grupo)
    {
        $qr_sql = "
            SELECT cd_grupo,
                   ds_grupo,
                   ds_grupo_sigla
              FROM rh_avaliacao.grupo
             WHERE dt_exclusao IS NULL
               AND cd_grupo = ".intval($cd_grupo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.grupo
                (
                    ds_grupo,
                    ds_grupo_sigla,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".(trim($args['ds_grupo']) != '' ? str_escape($args['ds_grupo']) : "DEFAULT").",
                    ".(trim($args['ds_grupo_sigla']) != '' ? str_escape($args['ds_grupo_sigla']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_grupo, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.grupo
               SET ds_grupo             = ".(trim($args['ds_grupo']) != '' ? str_escape($args['ds_grupo']) : "DEFAULT").",
                   ds_grupo_sigla       = ".(trim($args['ds_grupo_sigla']) != '' ? str_escape($args['ds_grupo_sigla']) : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_grupo = ".intval($cd_grupo).";";

        $this->db->query($qr_sql);
    }
}