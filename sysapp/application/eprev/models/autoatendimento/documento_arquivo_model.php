<?php
class Documento_arquivo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT cd_documento_arquivo,
                   ds_documento_arquivo,
                   arquivo,
                   arquivo_nome,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_alteracao) AS ds_usuario_alteracao,
                   TO_CHAR(dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao
              FROM autoatendimento.documento_arquivo
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_documento_arquivo)
    {
        $qr_sql = "
            SELECT cd_documento_arquivo,
                   ds_documento_arquivo,
                   arquivo,
                   arquivo_nome,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_alteracao) AS ds_usuario_alteracao,
                   TO_CHAR(dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao
              FROM autoatendimento.documento_arquivo
             WHERE cd_documento_arquivo = ".intval($cd_documento_arquivo)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }
    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO autoatendimento.documento_arquivo
                (
                    ds_documento_arquivo,
                    arquivo,
                    arquivo_nome,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".(trim($args['ds_documento_arquivo']) != '' ? str_escape($args['ds_documento_arquivo']) : "DEFAULT").",
                    ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_documento_arquivo, $args = array())
    {
        $qr_sql = "
            UPDATE autoatendimento.documento_arquivo
               SET ds_documento_arquivo = ".(trim($args['ds_documento_arquivo']) != '' ? str_escape($args['ds_documento_arquivo']) : "DEFAULT").",
                   arquivo = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                   arquivo_nome = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao = CURRENT_TIMESTAMP
             WHERE cd_documento_arquivo = ".intval($cd_documento_arquivo).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_documento_arquivo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE autoatendimento.documento_arquivo
               SET cd_usuario_exclusao = ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT").",
                   dt_exclusao = CURRENT_TIMESTAMP
             WHERE cd_documento_arquivo = ".intval($cd_documento_arquivo).";";

        $this->db->query($qr_sql);
    }
}