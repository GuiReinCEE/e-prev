<?php
class Matriz_quadro_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT mq.cd_matriz_quadro,
                   mq.cd_matriz_conceito_a,
                   mq.cd_matriz_conceito_b,
                   mq.cd_matriz_acao,
                   g1.ds_grupo_sigla || mc1.nr_matriz_conceito AS ds_matriz_conceito_a,
                   g2.ds_grupo_sigla || mc2.nr_matriz_conceito AS ds_matriz_conceito_b,
                   ma.ds_matriz_acao,
                   ma.cor_fundo,
                   ma.cor_texto
              FROM rh_avaliacao.matriz_quadro mq
              JOIN rh_avaliacao.matriz_conceito mc1
                ON mc1.cd_matriz_conceito = mq.cd_matriz_conceito_a
               AND mc1.dt_exclusao IS NULL
              JOIN rh_avaliacao.grupo g1
                ON g1.cd_grupo = mc1.cd_grupo
               AND g1.dt_exclusao IS NULL
              JOIN rh_avaliacao.matriz_conceito mc2
                ON mc2.cd_matriz_conceito = mq.cd_matriz_conceito_b
               AND mc2.dt_exclusao IS NULL
              JOIN rh_avaliacao.grupo g2
                ON g2.cd_grupo = mc2.cd_grupo
               AND g2.dt_exclusao IS NULL
              JOIN rh_avaliacao.matriz_acao ma
                ON ma.cd_matriz_acao = mq.cd_matriz_acao
               AND ma.dt_exclusao IS NULL
             WHERE mq.dt_exclusao IS NULL
             ".(intval($args['cd_matriz_conceito']) > 0 ? "AND (mq.cd_matriz_conceito_a = ".intval($args['cd_matriz_conceito'])." OR mq.cd_matriz_conceito_b = ".intval($args['cd_matriz_conceito']).")" : "")."
             ".(intval($args['cd_matriz_acao']) > 0 ? "AND mq.cd_matriz_acao = ".intval($args['cd_matriz_acao']) : "")."
             ORDER BY g1.ds_grupo_sigla, 
                      mc1.nr_matriz_conceito DESC, 
                      g2.ds_grupo_sigla, 
                      mc2.nr_matriz_conceito;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_conceito()
    {
        $qr_sql = "
            SELECT cd_matriz_conceito AS value,
                   g.ds_grupo_sigla || mc.nr_matriz_conceito AS text
              FROM rh_avaliacao.matriz_conceito mc
              JOIN rh_avaliacao.grupo g
                ON g.cd_grupo = mc.cd_grupo
               AND g.dt_exclusao IS NULL
             WHERE mc.dt_exclusao IS NULL
             ORDER BY g.ds_grupo_sigla ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_acao()
    {
        $qr_sql = "
            SELECT cd_matriz_acao AS value,
                   ds_matriz_acao AS text
              FROM rh_avaliacao.matriz_acao
             WHERE dt_exclusao IS NULL
             ORDER BY ds_matriz_acao ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_matriz_quadro)
    {
        $qr_sql = "
            SELECT cd_matriz_quadro,
                   cd_matriz_conceito_a,
                   cd_matriz_conceito_b,
                   cd_matriz_acao
              FROM rh_avaliacao.matriz_quadro
             WHERE dt_exclusao IS NULL
               AND cd_matriz_quadro = ".intval($cd_matriz_quadro).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.matriz_quadro
                (
                    cd_matriz_conceito_a,
                    cd_matriz_conceito_b,
                    cd_matriz_acao,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (

                    ".(intval($args['cd_matriz_conceito_a']) > 0 ? intval($args['cd_matriz_conceito_a']) : "DEFAULT").",
                    ".(intval($args['cd_matriz_conceito_b']) > 0 ? intval($args['cd_matriz_conceito_b']) : "DEFAULT").",
                    ".(intval($args['cd_matriz_acao']) > 0 ? intval($args['cd_matriz_acao']) : "DEFAULT").",                    
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_matriz_quadro, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.matriz_quadro
               SET cd_matriz_conceito_a = ".(intval($args['cd_matriz_conceito_a']) > 0 ? intval($args['cd_matriz_conceito_a']) : "DEFAULT").",
                   cd_matriz_conceito_b = ".(intval($args['cd_matriz_conceito_b']) > 0 ? intval($args['cd_matriz_conceito_b']) : "DEFAULT").",
                   cd_matriz_acao       = ".(intval($args['cd_matriz_acao']) > 0 ? intval($args['cd_matriz_acao']) : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_matriz_quadro = ".intval($cd_matriz_quadro).";";

        $this->db->query($qr_sql);
    }
}