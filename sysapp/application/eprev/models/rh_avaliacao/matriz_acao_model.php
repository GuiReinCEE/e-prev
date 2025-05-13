<?php
class Matriz_acao_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT cd_matriz_acao,
                   ds_matriz_acao,
                   (CASE WHEN fl_progressao = 'S'
                         THEN 'Sim'
                         ELSE 'Não'
                   END) AS ds_progressao,
                   (CASE WHEN fl_promocao = 'S'
                         THEN 'Sim'
                         ELSE 'Não'
                   END) AS ds_promocao,
                   cor_fundo,
                   cor_texto
              FROM rh_avaliacao.matriz_acao
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_matriz_acao)
    {
        $qr_sql = "
            SELECT cd_matriz_acao,
                   ds_matriz_acao,
                   fl_progressao,
                   fl_promocao,
                   cor_fundo,
                   cor_texto
              FROM rh_avaliacao.matriz_acao
             WHERE dt_exclusao IS NULL
               AND cd_matriz_acao = ".intval($cd_matriz_acao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO rh_avaliacao.matriz_acao
                (
                    ds_matriz_acao,
                    fl_progressao,
                    fl_promocao,
                    cor_fundo,
                    cor_texto,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (

                    ".(trim($args['ds_matriz_acao']) != '' ? str_escape($args['ds_matriz_acao']) : "DEFAULT").",
                    ".(trim($args['fl_progressao']) != '' ? str_escape($args['fl_progressao']) : "DEFAULT").",
                    ".(trim($args['fl_promocao']) != '' ? str_escape($args['fl_promocao']) : "DEFAULT").",
                    ".(trim($args['cor_fundo']) != '' ? str_escape($args['cor_fundo']) : "DEFAULT").",
                    ".(trim($args['cor_texto']) != '' ? str_escape($args['cor_texto']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_matriz_acao, $args = array())
    {
        $qr_sql = "
            UPDATE rh_avaliacao.matriz_acao
               SET ds_matriz_acao       = ".(trim($args['ds_matriz_acao']) != '' ? str_escape($args['ds_matriz_acao']) : "DEFAULT").",
                   fl_progressao        = ".(trim($args['fl_progressao']) != '' ? str_escape($args['fl_progressao']) : "DEFAULT").",
                   fl_promocao          = ".(trim($args['fl_promocao']) != '' ? str_escape($args['fl_promocao']) : "DEFAULT").",
                   cor_fundo            = ".(trim($args['cor_fundo']) != '' ? str_escape($args['cor_fundo']) : "DEFAULT").",
                   cor_texto            = ".(trim($args['cor_texto']) != '' ? str_escape($args['cor_texto']) : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_matriz_acao = ".intval($cd_matriz_acao).";";

        $this->db->query($qr_sql);
    }
}