<?php
class Documento_recebido_model extends Model
{
	function __construct()
	{
		parent::Model();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT dr.cd_registro_empregado,
                   dr.cd_empresa,
                   dr.seq_dependencia,
                   TO_CHAR(dr.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
                   dr.documento,
                   drt.ds_documento_recebido_tipo AS ds_origem,
                   td.nome_documento AS ds_tipo,
                   p.nome,
                   dr.ds_observacao
              FROM autoatendimento.documento_recebido dr
              JOIN autoatendimento.documento_recebido_tipo_doc drtd
                ON drtd.cd_tipo_doc = dr.cd_tipo_doc
              JOIN autoatendimento.documento_recebido_tipo drt
                ON drt.cd_documento_recebido_tipo = dr.cd_documento_recebido_tipo
              JOIN tipo_documentos td
                ON td.cd_tipo_doc = dr.cd_tipo_doc
              JOIN public.participantes p
                ON p.cd_registro_empregado = dr.cd_registro_empregado
               AND p.cd_empresa            = dr.cd_empresa
               AND p.seq_dependencia       = dr.seq_dependencia
             WHERE dr.dt_exclusao       IS NULL
               AND dr.dt_encaminhamento IS NOT NULL
               ".(trim($args['cd_empresa']) > 0 ? 'AND dr.cd_empresa = '.intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_registro_empregado']) > 0 ? 'AND dr.cd_registro_empregado = '.intval($args['cd_registro_empregado']) : "")."
               ".(trim($args['seq_dependencia']) > 0 ? 'AND dr.seq_dependencia = '.intval($args['seq_dependencia']) : "")."
               ".(trim($args['nome_participante']) != '' ? "AND UPPER(funcoes.remove_acento(p.nome)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['nome_participante'])."%')))" : "" )."
               ".(((trim($args['dt_encaminhamento_ini']) != '') AND trim($args['dt_encaminhamento_fim']) != '') ? "AND DATE_TRUNC('day', dt_encaminhamento) BETWEEN TO_DATE('".$args['dt_encaminhamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : '')."
               ".(trim($args['cd_documento_recebido_tipo']) > 0 ? 'AND drt.cd_documento_recebido_tipo = '.intval($args['cd_documento_recebido_tipo']) : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_origem()
    {
        $qr_sql = "
            SELECT cd_documento_recebido_tipo AS value,
                   ds_documento_recebido_tipo AS text
              FROM autoatendimento.documento_recebido_tipo
             WHERE dt_exclusao IS NULL
             ORDER BY ds_documento_recebido_tipo;";

        return $this->db->query($qr_sql)->result_array();
    }
}

