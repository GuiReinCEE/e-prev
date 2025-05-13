<?php
class Matriz_conceito_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT mc.cd_matriz_conceito,
                   g.ds_grupo_sigla ||  mc.nr_matriz_conceito AS ds_conceito,
                   mc.nr_nota_min,
                   mc.nr_nota_max
              FROM rh_avaliacao.matriz_conceito mc
              JOIN rh_avaliacao.grupo g
                ON g.cd_grupo = mc.cd_grupo
               AND g.dt_exclusao IS NULL
             WHERE mc.dt_exclusao IS NULL
             ".(intval($args['cd_grupo']) > 0 ? "AND mc.cd_grupo = ".intval($args['cd_grupo']) : "").";";

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

    public function carrega($cd_matriz_conceito)
    {
        $qr_sql = "
            SELECT mc.cd_matriz_conceito,
                   mc.cd_grupo,
                   mc.nr_matriz_conceito,
                   mc.nr_nota_min,
                   mc.nr_nota_max
              FROM rh_avaliacao.matriz_conceito mc
             WHERE mc.dt_exclusao IS NULL
               AND mc.cd_matriz_conceito = ".intval($cd_matriz_conceito).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.matriz_conceito
                (
                    cd_grupo,
                    nr_matriz_conceito,
                    nr_nota_min,
                    nr_nota_max,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (

                    ".(intval($args['cd_grupo']) > 0 ? intval($args['cd_grupo']) : "DEFAULT").",
                    ".(intval($args['nr_matriz_conceito']) > 0 ? intval($args['nr_matriz_conceito']) : "DEFAULT").",
                    ".(trim($args['nr_nota_min']) != '' ? floatval($args['nr_nota_min']) : "DEFAULT").",
                    ".(trim($args['nr_nota_max']) != '' ? floatval($args['nr_nota_max']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_matriz_conceito, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.matriz_conceito
               SET cd_grupo             = ".(intval($args['cd_grupo']) > 0 ? intval($args['cd_grupo']) : "DEFAULT").",
                   nr_matriz_conceito   = ".(intval($args['nr_matriz_conceito']) > 0 ? intval($args['nr_matriz_conceito']) : "DEFAULT").",
                   nr_nota_min          = ".(trim($args['nr_nota_min']) != '' ? floatval($args['nr_nota_min']) : "DEFAULT").",
                   nr_nota_max          = ".(trim($args['nr_nota_max']) != '' ? floatval($args['nr_nota_max']) : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_matriz_conceito = ".intval($cd_matriz_conceito).";";

        $this->db->query($qr_sql);
    }
}