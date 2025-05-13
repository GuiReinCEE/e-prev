<?php
class Abaixo_assinado_model extends Model
{
    public function listar($args = array())
    {
        $qr_sql = "
            SELECT cd_abaixo_assinado,
                   projetos.nr_abaixo_assinado(nr_ano, nr_numero) AS nr_numero_ano,
                   TO_CHAR(dt_protocolo, 'DD/MM/YYYY') AS dt_protocolo,
                   cd_empresa ||'/'|| cd_registro_empregado ||'/'|| seq_dependencia AS nr_re,
                   TO_CHAR((dt_protocolo + '30 days'::interval)::date, 'DD/MM/YYYY') AS dt_limite_retorno,
                   ds_nome,
                   ds_descricao,
                   ds_email,
                   ds_telefone_1,
                   ds_telefone_2,
                   ds_acao,
                   TO_CHAR(dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
                   ds_retorno
              FROM projetos.abaixo_assinado
             WHERE dt_exclusao IS NULL
             ".(intval($args['nr_ano']) > 0 ? "AND nr_ano = ".intval($args['nr_ano']) : "")."
             ".(intval($args['nr_numero']) > 0 ? "AND nr_numero = ".intval($args['nr_numero']) : "")."
             ".(intval($args['cd_empresa']) > 0 ? "AND cd_empresa = ".intval($args['cd_empresa']) : "")."
             ".(intval($args['cd_registro_empregado']) > 0 ? "AND cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
             ".(intval($args['seq_dependencia']) > 0 ? "AND seq_dependencia = ".intval($args['seq_dependencia']) : "")."
             ".(((trim($args['dt_protocolo_ini']) != '') AND (trim($args['dt_protocolo_fim']) != '')) ? "AND DATE_TRUNC('day', dt_protocolo) BETWEEN TO_DATE('".$args['dt_protocolo_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_protocolo_fim']."', 'DD/MM/YYYY')" : "")."
             ".(((trim($args['dt_retorno_ini']) != '') AND (trim($args['dt_retorno_fim']) != '')) ? "AND DATE_TRUNC('day', dt_retorno) BETWEEN TO_DATE('".$args['dt_retorno_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_retorno_fim']."', 'DD/MM/YYYY')" : "")."
             ".(((trim($args['dt_limite_retorno_ini']) != '') AND (trim($args['dt_limite_retorno_fim']) != '')) ? "AND DATE_TRUNC('day', (dt_protocolo + '30 days'::interval)::date) BETWEEN TO_DATE('".$args['dt_limite_retorno_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_retorno_fim']."', 'DD/MM/YYYY')" : "")."
             ".(trim($args['fl_retorno']) == 'S' ? "AND ds_retorno IS NOT NULL" : "")."
             ".(trim($args['fl_retorno']) == 'N' ? "AND ds_retorno IS NULL" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_numero()
    {
        $qr_sql = "
            SELECT (COALESCE(MAX(nr_numero), 0) + 1) AS nr_numero
              FROM projetos.abaixo_assinado
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_abaixo_assinado)
    {
        $qr_sql = "
            SELECT cd_abaixo_assinado,
                   nr_numero,
                   nr_ano,
                   projetos.nr_abaixo_assinado(nr_ano, nr_numero) AS nr_numero_ano,
                   TO_CHAR(dt_protocolo, 'DD/MM/YYYY') AS dt_protocolo,
                   TO_CHAR((dt_protocolo + '30 days'::interval)::date, 'DD/MM/YYYY') AS dt_limite_retorno,
                   cd_empresa ||'/'|| cd_registro_empregado ||'/'|| seq_dependencia AS nr_re,
                   cd_empresa,
                   cd_registro_empregado,
                   seq_dependencia,
                   ds_nome,
                   ds_descricao,
                   ds_email,
                   ds_telefone_1,
                   ds_telefone_2,
                   ds_acao,
                   ds_retorno,
                   TO_CHAR(dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
                   cd_abaixo_assinado_retorno_tipo
              FROM projetos.abaixo_assinado
             WHERE dt_exclusao IS NULL
               AND cd_abaixo_assinado = ".intval($cd_abaixo_assinado).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_abaixo_assinado = intval($this->db->get_new_id('projetos.abaixo_assinado', 'cd_abaixo_assinado'));

        $qr_sql = "
            INSERT INTO projetos.abaixo_assinado
                (
                    cd_abaixo_assinado,
                    nr_numero,
                    nr_ano,
                    dt_protocolo,
                    cd_empresa,
                    cd_registro_empregado,
                    seq_dependencia,
                    ds_nome,
                    ds_descricao,
                    ds_email,
                    ds_telefone_1,
                    ds_telefone_2,
                    ds_acao,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".(intval($cd_abaixo_assinado) > 0 ? intval($cd_abaixo_assinado) : "DEFAULT").",
                    ".(intval($args['nr_numero']) > 0 ? intval($args['nr_numero']) : "DEFAULT").",
                    ".(intval($args['nr_ano']) > 0 ? intval($args['nr_ano']) : "DEFAULT").",
                    ".(trim($args['dt_protocolo']) != '' ? "TO_TIMESTAMP('".trim($args['dt_protocolo'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
                    ".(intval($args['cd_registro_empregado']) > 0 ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                    ".(intval($args['seq_dependencia']) > 0 ? intval($args['seq_dependencia']) : "DEFAULT").",
                    ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
                    ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                    ".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
                    ".(trim($args['ds_telefone_1']) != '' ? str_escape($args['ds_telefone_1']) : "DEFAULT").",
                    ".(trim($args['ds_telefone_2']) != '' ? str_escape($args['ds_telefone_2']) : "DEFAULT").",
                    ".(trim($args['ds_acao']) != '' ? str_escape($args['ds_acao']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);

        return $cd_abaixo_assinado;
    }

    public function atualizar($cd_abaixo_assinado, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.abaixo_assinado
               SET dt_protocolo          = ".(trim($args['dt_protocolo']) != '' ? "TO_TIMESTAMP('".trim($args['dt_protocolo'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   cd_empresa            = ".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
                   cd_registro_empregado = ".(intval($args['cd_registro_empregado']) > 0 ? intval($args['cd_registro_empregado']) : "DEFAULT").",
                   seq_dependencia       = ".(intval($args['seq_dependencia']) > 0 ? intval($args['seq_dependencia']) : "DEFAULT").",
                   ds_nome               = ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
                   ds_descricao          = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
                   ds_email              = ".(trim($args['ds_email']) != '' ? str_escape($args['ds_email']) : "DEFAULT").",
                   ds_telefone_1         = ".(trim($args['ds_telefone_1']) != '' ? str_escape($args['ds_telefone_1']) : "DEFAULT").",
                   ds_telefone_2         = ".(trim($args['ds_telefone_2']) != '' ? str_escape($args['ds_telefone_2']) : "DEFAULT").",
                   ds_acao               = ".(trim($args['ds_acao']) != '' ? str_escape($args['ds_acao']) : "DEFAULT").",
                   cd_usuario_alteracao  = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao          = CURRENT_TIMESTAMP
             WHERE cd_abaixo_assinado = ".intval($cd_abaixo_assinado).";";

        $this->db->query($qr_sql);
    }

    public function listar_acompanhamento($cd_abaixo_assinado)
    {
        $qr_sql = "
            SELECT cd_abaixo_assinado_acompanhamento,
                   ds_acompanhamento,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.abaixo_assinado_acompanhamento
             WHERE dt_exclusao IS NULL
               AND cd_abaixo_assinado = ".intval($cd_abaixo_assinado).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_acompanhamento($cd_abaixo_assinado_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_abaixo_assinado_acompanhamento,
                   ds_acompanhamento
              FROM projetos.abaixo_assinado_acompanhamento
             WHERE dt_exclusao IS NULL
               AND cd_abaixo_assinado_acompanhamento = ".intval($cd_abaixo_assinado_acompanhamento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_acompanhamento($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.abaixo_assinado_acompanhamento
                (
                    cd_abaixo_assinado,
                    ds_acompanhamento,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
            VALUES
                (
                    ".(intval($args['cd_abaixo_assinado']) > 0 ? intval($args['cd_abaixo_assinado']) : "DEFAULT").",
                    ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar_acompanhamento($cd_abaixo_assinado_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.abaixo_assinado_acompanhamento
               SET cd_abaixo_assinado   = ".(intval($args['cd_abaixo_assinado']) > 0 ? intval($args['cd_abaixo_assinado']) : "DEFAULT").",
                   ds_acompanhamento    = ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_abaixo_assinado_acompanhamento = ".intval($cd_abaixo_assinado_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function excluir_acompanhamento($cd_abaixo_assinado_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.abaixo_assinado_acompanhamento
               SET cd_usuario_exclusao = ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT").",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_abaixo_assinado_acompanhamento = ".intval($cd_abaixo_assinado_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function get_tipo()
    {
      $qr_sql = "
        SELECT cd_abaixo_assinado_retorno_tipo AS value,
               ds_abaixo_assinado_retorno_tipo AS text
          FROM projetos.abaixo_assinado_retorno_tipo
         WHERE dt_exclusao IS NULL
         ORDER BY ds_abaixo_assinado_retorno_tipo ASC;";

      return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_retorno($cd_abaixo_assinado, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.abaixo_assinado
               SET ds_retorno                      = ".(trim($args['ds_retorno']) != '' ? str_escape($args['ds_retorno']) : "DEFAULT").",
                   dt_retorno                      = ".(trim($args['dt_retorno']) != '' ? "TO_DATE('".$args['dt_retorno']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   cd_abaixo_assinado_retorno_tipo = ".(intval($args['cd_abaixo_assinado_retorno_tipo']) > 0 ? intval($args['cd_abaixo_assinado_retorno_tipo']) : "DEFAULT").",
                   cd_usuario_alteracao            = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao                    = CURRENT_TIMESTAMP
             WHERE cd_abaixo_assinado = ".intval($cd_abaixo_assinado).";";

        $this->db->query($qr_sql);
    }

    public function listar_anexo($cd_abaixo_assinado)
    {
        $qr_sql = "
            SELECT cd_abaixo_assinado_anexo,
                   arquivo,
                   arquivo_nome,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.abaixo_assinado_anexo
             WHERE dt_exclusao IS NULL
               AND cd_abaixo_assinado = ".intval($cd_abaixo_assinado).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_anexo($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.abaixo_assinado_anexo
                (
                    cd_abaixo_assinado,
                    arquivo,
                    arquivo_nome,
                    cd_usuario_inclusao
                )
            VALUES
                (
                    ".(intval($args['cd_abaixo_assinado']) > 0 ? intval($args['cd_abaixo_assinado']) : "DEFAULT").",
                    ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function excluir_anexo($cd_abaixo_assinado_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.abaixo_assinado_anexo
               SET cd_usuario_exclusao = ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT").",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_abaixo_assinado_anexo = ".intval($cd_abaixo_assinado_anexo).";";

        $this->db->query($qr_sql);
    }
}