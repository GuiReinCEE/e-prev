<?php
class Performance_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT p.cd_performance,
                   p.cd_grupo,
                   p.ds_performance_sigla ||' - '|| p.ds_performance AS ds_performance,
                   p.ds_performance_descricao,
                   p.nr_ponto,
                   g.ds_grupo ||' - '|| g.ds_grupo_sigla AS ds_grupo
              FROM rh_avaliacao.performance p
              JOIN rh_avaliacao.grupo g
                ON g.cd_grupo = p.cd_grupo
               AND g.dt_exclusao IS NULL
             WHERE p.dt_exclusao IS NULL
             ".(intval($args['cd_grupo']) > 0 ? "AND p.cd_grupo = ".intval($args['cd_grupo']) : "").";";

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

    public function carrega($cd_performance)
    {
        $qr_sql = "
            SELECT p.cd_performance,
                   p.cd_grupo,
                   p.ds_performance,
                   p.ds_performance_sigla,
                   p.ds_performance_descricao,
                   p.nr_ponto
              FROM rh_avaliacao.performance p
             WHERE p.dt_exclusao IS NULL
               AND p.cd_performance = ".intval($cd_performance).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.performance
                (
                    cd_grupo,
                    ds_performance,
                    ds_performance_sigla,
                    ds_performance_descricao,
                    nr_ponto,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".(intval($args['cd_grupo']) > 0 ? intval($args['cd_grupo']) : "DEFAULT").",
                    ".(trim($args['ds_performance']) != '' ? str_escape($args['ds_performance']) : "DEFAULT").",
                    ".(trim($args['ds_performance_sigla']) != '' ? str_escape($args['ds_performance_sigla']) : "DEFAULT").",
                    ".(trim($args['ds_performance_descricao']) != '' ? str_escape($args['ds_performance_descricao']) : "DEFAULT").",
                    ".(intval($args['nr_ponto']) > 0 ? intval($args['nr_ponto']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_performance, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.performance
               SET cd_grupo                 = ".(intval($args['cd_grupo']) > 0 ? intval($args['cd_grupo']) : "DEFAULT").",
                   ds_performance           = ".(trim($args['ds_performance']) != '' ? str_escape($args['ds_performance']) : "DEFAULT").",
                   ds_performance_sigla     = ".(trim($args['ds_performance_sigla']) != '' ? str_escape($args['ds_performance_sigla']) : "DEFAULT").",
                   ds_performance_descricao = ".(trim($args['ds_performance_descricao']) != '' ? str_escape($args['ds_performance_descricao']) : "DEFAULT").",
                   nr_ponto                 = ".(intval($args['nr_ponto']) > 0 ? intval($args['nr_ponto']) : "DEFAULT").",
                   cd_usuario_alteracao     = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
             WHERE cd_performance = ".intval($cd_performance).";";

        $this->db->query($qr_sql);
    }
}