<?php
class Solic_fiscalizacao_audit_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($fl_permissao, $args = array())
    {
        $qr_sql = "
            SELECT projetos.nr_solic_fiscalizacao_audit(sfa.nr_ano, sfa.nr_numero) AS ds_ano_numero,
                   sfa.cd_solic_fiscalizacao_audit,
                   sfa.cd_solic_fiscalizacao_audit_origem, 
                   o.ds_solic_fiscalizacao_audit_origem, 
                   t.ds_solic_fiscalizacao_audit_tipo,
                   sfa.ds_origem,
                   sfa.cd_solic_fiscalizacao_audit_tipo,
                   sfa.ds_tipo, 
                   sfa.cd_gerencia,
                   sfa.ds_documento,
                   sfa.ds_teor,
                   (CASE WHEN sfa.fl_prazo = 'N' THEN 'Não'
                         ELSE 'Sim'  
                   END) AS ds_prazo,
                   sfa.nr_dias_prazo,
                   TO_CHAR(COALESCE(sfa.dt_prazo_porrogado, sfa.dt_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   TO_CHAR(sfa.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio,
                   TO_CHAR(sfa.dt_recebimento, 'DD/MM/YYYY') AS dt_recebimento,
                   TO_CHAR(sfa.dt_envio_atendimento, 'DD/MM/YYYY') AS dt_envio_atendimento,
                   sfa.arquivo, 
                   sfa.arquivo_nome,
                   funcoes.get_usuario_nome(sfa.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(sfa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   (SELECT COUNT(*)
                      FROM projetos.solic_fiscalizacao_audit_documentacao sfad
                     WHERE sfad.dt_exclusao                 IS NULL
                       AND sfad.cd_solic_fiscalizacao_audit = sfa.cd_solic_fiscalizacao_audit) qt_solicitacoes,
                   sfa.cd_liquid,
                   (CASE WHEN sfa.dt_envio_atendimento IS NOT NULL THEN 'info'
                         WHEN COALESCE(sfa.dt_prazo_porrogado, sfa.dt_prazo) < CURRENT_DATE THEN 'important'
                         ELSE 'warning'
                   END) AS ds_class_prazo
              FROM projetos.solic_fiscalizacao_audit sfa
              JOIN projetos.solic_fiscalizacao_audit_origem o
                ON o.cd_solic_fiscalizacao_audit_origem = sfa.cd_solic_fiscalizacao_audit_origem
              JOIN projetos.solic_fiscalizacao_audit_tipo t
                ON t.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo
             WHERE sfa.dt_exclusao IS NULL
               ".(!$fl_permissao ? "
                    AND sfa.dt_envio IS NOT NULL 
                    AND (
                            sfa.cd_gerencia = '".trim($args['cd_gerencia_usuario'])."'
                            OR 
                            (SELECT COUNT(*)
                               FROM projetos.solic_fiscalizacao_audit_tipo_gerencia tg
                              WHERE tg.dt_exclusao IS NULL
                                AND tg.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo
                                AND tg.cd_gerencia                      = '".trim($args['cd_gerencia_usuario'])."') > 0
                            OR
                            (SELECT COUNT(*)
                               FROM projetos.solic_fiscalizacao_audit_gerencia ag
                              WHERE ag.dt_exclusao                 IS NULL
                                AND ag.cd_solic_fiscalizacao_audit = sfa.cd_solic_fiscalizacao_audit
                                AND ag.cd_gerencia                 = '".trim($args['cd_gerencia_usuario'])."') > 0

                    )" : "")."
               ".(trim($args['cd_gestao']) != '' ? "AND (SELECT COUNT(*)
                                                           FROM projetos.solic_fiscalizacao_audit_tipo_gerencia tg
                                                          WHERE tg.dt_exclusao IS NULL
                                                            AND tg.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo
                                                            AND tg.cd_gerencia                      = '".trim($args['cd_gestao'])."') > 0" : "")."
               ".(trim($args['fl_enviado']) == "S" ? "AND sfa.dt_envio IS NOT NULL" : "")."
               ".(trim($args['fl_enviado']) == "N" ? "AND sfa.dt_envio IS NULL" : "")."               
               ".(trim($args['cd_gerencia']) != '' ? "AND sfa.cd_gerencia = '".trim($args['cd_gerencia'])."'" : "")."
               ".(((trim($args['dt_recebimento_ini']) != '') AND trim($args['dt_recebimento_fim']) != '') ? "AND DATE_TRUNC('day', dt_recebimento) BETWEEN TO_DATE('".$args['dt_recebimento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_recebimento_fim']."', 'DD/MM/YYYY')" : '')."
               ".(((trim($args['dt_prazo_ini']) != '') AND trim($args['dt_prazo_fim']) != '') ? "AND DATE_TRUNC('day', COALESCE(sfa.dt_prazo_porrogado, sfa.dt_prazo)) BETWEEN TO_DATE('".$args['dt_prazo_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_fim']."', 'DD/MM/YYYY')" : '')."
               ".(((trim($args['dt_envio_ini']) != '') AND trim($args['dt_envio_fim']) != '') ? "AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY HH24:MI') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY HH24:MI')" : '')."

               ".(((trim($args['dt_atendimento_ini']) != '') AND trim($args['dt_atendimento_fim']) != '') ? "AND DATE_TRUNC('day', dt_envio_atendimento) BETWEEN TO_DATE('".$args['dt_atendimento_ini']."', 'DD/MM/YYYY HH24:MI') AND TO_DATE('".$args['dt_atendimento_fim']."', 'DD/MM/YYYY HH24:MI')" : '')."
               ".(trim($args['cd_solic_fiscalizacao_audit_origem']) != '' ? "AND sfa.cd_solic_fiscalizacao_audit_origem = '".trim($args['cd_solic_fiscalizacao_audit_origem'])."'" : "")."
               ".(trim($args['cd_solic_fiscalizacao_audit_tipo']) != '' ? "AND sfa.cd_solic_fiscalizacao_audit_tipo = '".trim($args['cd_solic_fiscalizacao_audit_tipo'])."'" : "")."
               ".(trim($args['ds_documento']) != '' ? "AND UPPER(funcoes.remove_acento(sfa.ds_documento)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['ds_documento'])."%')))" : "" )."
               ".(trim($args['ds_teor']) != '' ? "AND UPPER(funcoes.remove_acento(sfa.ds_teor)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['ds_teor'])."%')))" : "" ).";";

        return $this->db->query($qr_sql)->result_array();   
    }

    public function get_grupos()
    {
    	$qr_sql = "
    		SELECT cd_solic_fiscalizacao_audit_grupo AS value,
    		       ds_grupo AS text
    		  FROM projetos.solic_fiscalizacao_audit_grupo
    		 WHERE dt_exclusao IS NULL;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_origem($cd_solic_fiscalizacao_audit_origem = 0)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_origem AS value,
                   ds_solic_fiscalizacao_audit_origem AS text,
                   fl_especificar
              FROM projetos.solic_fiscalizacao_audit_origem
             WHERE dt_exclusao IS NULL
               ".(intval($cd_solic_fiscalizacao_audit_origem) > 0 ? "AND cd_solic_fiscalizacao_audit_origem = ".intval($cd_solic_fiscalizacao_audit_origem) : "").";";

        if(intval($cd_solic_fiscalizacao_audit_origem) > 0)
        {
            return $this->db->query($qr_sql)->row_array();
        }
        else
        {
            return $this->db->query($qr_sql)->result_array();
        } 
    }

    public function get_agrupamento()
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_tipo_agrupamento,
                   ds_solic_fiscalizacao_audit_tipo_agrupamento AS value
              FROM projetos.solic_fiscalizacao_audit_tipo_agrupamento
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_tipo($cd_solic_fiscalizacao_audit_tipo = 0, $cd_solic_fiscalizacao_audit_tipo_agrupamento = 0)
    {
        $qr_sql = "
            SELECT t.cd_solic_fiscalizacao_audit_tipo AS value,
                   t.ds_solic_fiscalizacao_audit_tipo AS text,
                   t.cd_gerencia,
                   t.fl_especificar,
                   (SELECT COUNT(*)
                      FROM projetos.solic_fiscalizacao_audit_tipo_gerencia tg
                     WHERE tg.dt_exclusao                      IS NULL
                       AND tg.cd_solic_fiscalizacao_audit_tipo = t.cd_solic_fiscalizacao_audit_tipo) AS tl_gestao
             FROM projetos.solic_fiscalizacao_audit_tipo t
            WHERE t.dt_exclusao IS NULL
              ".(intval($cd_solic_fiscalizacao_audit_tipo) > 0 ? "AND t.cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo) : "")."
              ".(intval($cd_solic_fiscalizacao_audit_tipo_agrupamento) > 0 ? "AND t.cd_solic_fiscalizacao_audit_tipo_agrupamento = ".intval($cd_solic_fiscalizacao_audit_tipo_agrupamento) : "").";";

        if(intval($cd_solic_fiscalizacao_audit_tipo) > 0)
        {
            return $this->db->query($qr_sql)->row_array();
        }
        else
        {
            return $this->db->query($qr_sql)->result_array();
        }
    }

    public function get_tipo_gerencia($cd_solic_fiscalizacao_audit_tipo)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_tipo_gerencia,
                   cd_gerencia
              FROM projetos.solic_fiscalizacao_audit_tipo_gerencia
             WHERE dt_exclusao                      IS NULL
               AND cd_solic_fiscalizacao_audit_tipo = ".intval($cd_solic_fiscalizacao_audit_tipo)."
             ORDER BY cd_gerencia;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_gerencia($tipo = array())
    {
        $qr_sql = " 
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_gerencias_vigente(".(count($tipo) > 0 ? "'".implode(', ', $tipo)."'" : "").");";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_usuario($cd_divisao, $fl_remove_gerente = 'N', $fl_remover_sub = 'N')
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_divisao)."')
             WHERE 1 = 1
               ".(trim($fl_remove_gerente) == 'S' ? "AND codigo != 351" : "")."
               ".(trim($fl_remover_sub) == 'S' ? "AND codigo NOT IN (SELECT codigo FROM projetos.usuarios_controledi WHERE divisao = '".trim($cd_divisao)."' AND indic_01 = 'S')" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_documentacao_anexo($cd_solic_fiscalizacao_audit_documentacao_anexo)
    {
        $qr_sql = "
            SELECT arquivo,
                   arquivo_nome
              FROM projetos.solic_fiscalizacao_audit_documentacao_anexo
             WHERE cd_solic_fiscalizacao_audit_documentacao_anexo = ".intval($cd_solic_fiscalizacao_audit_documentacao_anexo)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_solic_fiscalizacao_audit)
    {
        $qr_sql = "
            SELECT projetos.nr_solic_fiscalizacao_audit(sfa.nr_ano, sfa.nr_numero) AS ds_ano_numero,
                   sfa.nr_ano,
                   sfa.nr_numero,
                   sfa.cd_solic_fiscalizacao_audit,
                   o.fl_especificar AS fl_especificar_origem,
                   t.fl_especificar AS fl_especificar_tipo,
                   sfa.cd_solic_fiscalizacao_audit_origem, 
                   o.ds_solic_fiscalizacao_audit_origem, 
                   sfa.ds_origem,
                   sfa.cd_solic_fiscalizacao_audit_tipo,
                   sfa.ds_tipo, 
                   sfa.cd_gerencia,
                   t.ds_solic_fiscalizacao_audit_tipo,
                   sfa.ds_documento,
                   sfa.ds_teor,
                   sfa.fl_prazo,
                   sfa.nr_dias_prazo,
                   sfa.arquivo_minuta,
                   sfa.arquivo_minuta_nome,
                   sfa.arquivo_pedido,
                   sfa.arquivo_pedido_nome,
                   TO_CHAR(COALESCE(sfa.dt_prazo_porrogado, sfa.dt_prazo), 'DD/MM/YYYY') AS dt_prazo,
                   TO_CHAR(sfa.dt_prazo, 'DD/MM/YYYY') AS dt_prazo_antes,
                   TO_CHAR(sfa.dt_prazo_porrogado, 'DD/MM/YYYY') AS dt_prazo_depois,
                   TO_CHAR(sfa.dt_prazo_porrogado, 'DD/MM/YYYY') AS dt_prazo_porrogado,
                   TO_CHAR(sfa.dt_recebimento, 'DD/MM/YYYY') AS dt_recebimento,
                   sfa.arquivo, 
                   sfa.arquivo_nome, 
                   funcoes.get_usuario_nome(sfa.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(sfa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   TO_CHAR(sfa.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio,
                   funcoes.get_usuario_nome(sfa.cd_usuario_envio) AS ds_usuario_envio,
                   (SELECT COUNT(*)
                      FROM projetos.solic_fiscalizacao_audit_tipo_gerencia tg
                     WHERE tg.dt_exclusao                      IS NULL
                       AND tg.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo) AS tl_gestao,
                   TO_CHAR(sfa.dt_solicitacao_prorrogacao, 'DD/MM/YYYY') AS dt_solicitacao_prorrogacao,
                   TO_CHAR(sfa.dt_solic_prorrogacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_solic_prorrogacao,
                   funcoes.get_usuario_nome(sfa.cd_usuario_solic_prorrogacao) AS ds_usuario_solic_prorrogacao,
                   sfa.ds_solicitacao_prorrogacao,
                   TO_CHAR(sfa.dt_envio_solicitacao_documento, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_solicitacao_documento,
                   funcoes.get_usuario_nome(sfa.cd_usuario_envio_solicitacao_documento) AS ds_usuario_envio_solicitacao_documento,
                   sfa.cd_liquid,
                   sfa.cd_liquid_minuta,
                   sfa.arquivo_minuta,
                   sfa.arquivo_pedido,
                   sfa.cd_correspondencia_recebida_item,
                   TO_CHAR(sfa.dt_envio_atendimento, 'DD/MM/YYYY') AS dt_envio_atendimento,
                   TO_CHAR(sfa.dt_envio_atendimento, 'YYYY') AS ds_ano_edicao,
                   TO_CHAR(sfa.dt_envio_atendimento, 'MM') AS ds_mes_edicao,
                   sfa.nr_correspondencia_ano,
                   sfa.nr_correspondencia_numero,
                   (SELECT COUNT(*) 
                      FROM projetos.solic_fiscalizacao_audit_documentacao sfad
                     WHERE sfad.cd_solic_fiscalizacao_audit = sfa.cd_solic_fiscalizacao_audit
                       AND sfad.dt_exclusao IS NULL
                       AND (fl_atendeu = 'N' OR dt_encerramento IS NULL)) AS tl_documento_encerramento,

                    (SELECT COUNT(*) 
                      FROM projetos.solic_fiscalizacao_audit_documentacao sfad
                     WHERE sfad.cd_solic_fiscalizacao_audit = sfa.cd_solic_fiscalizacao_audit
                       AND sfad.dt_exclusao     IS NULL
                       AND sfad.dt_atendimento  IS NOT NULL
                       AND sfad.dt_encerramento IS NULL) AS tl_documento_atendido,

                    (SELECT COUNT(*) 
                       FROM projetos.solic_fiscalizacao_audit_documentacao sfad
                      WHERE sfad.cd_solic_fiscalizacao_audit = sfa.cd_solic_fiscalizacao_audit
                        AND sfad.dt_exclusao IS NULL) AS tl_documento,
                   sfa.ds_link_acesso,
                   MD5(sfa.cd_solic_fiscalizacao_audit::TEXT) AS ds_chave_acesso,
                   sfa.ds_justificativa_atendimento,
                   sfa.arquivo_atendimento,
                   sfa.arquivo_atendimento_nome
              FROM projetos.solic_fiscalizacao_audit sfa
              JOIN projetos.solic_fiscalizacao_audit_origem o
                ON o.cd_solic_fiscalizacao_audit_origem = sfa.cd_solic_fiscalizacao_audit_origem
              JOIN projetos.solic_fiscalizacao_audit_tipo t
                ON t.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo
             WHERE sfa.cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_correspondencia_recebida($cd_correspondencia_recebida_item)
    {
        $qr_sql = "
            SELECT cr.cd_correspondencia_recebida,
                   cri.cd_correspondencia_recebida_item,
                   TO_CHAR(cri.dt_correspondencia, 'DD/MM/YYYY HH24:MI') AS dt_correspondencia,
                   cri.origem,
                   funcoes.nr_correspondencia_recebida(cr.nr_ano, cr.nr_numero) AS nr_ano_numero,
                   crt.ds_correspondencia_recebida_tipo
              FROM projetos.correspondencia_recebida_item cri
              JOIN projetos.correspondencia_recebida cr
                ON cr.cd_correspondencia_recebida = cri.cd_correspondencia_recebida
               AND cr.dt_exclusao IS NULL
              JOIN projetos.correspondencia_recebida_tipo crt
                ON crt.cd_correspondencia_recebida_tipo = cri.cd_correspondencia_recebida_tipo
               AND crt.dt_exclusao IS NULL
             WHERE cri.cd_correspondencia_recebida_item = ".intval($cd_correspondencia_recebida_item)."
               AND cri.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }
    
    public function salvar($args = array())
    {
        $cd_solic_fiscalizacao_audit = intval($this->db->get_new_id('projetos.solic_fiscalizacao_audit', 'cd_solic_fiscalizacao_audit'));

        $qr_sql = "
            INSERT INTO projetos.solic_fiscalizacao_audit
                 (
                    cd_solic_fiscalizacao_audit, 
                    cd_solic_fiscalizacao_audit_origem, 
                    ds_origem, 
                    dt_recebimento, 
                    cd_solic_fiscalizacao_audit_tipo, 
                    ds_tipo, 
                    ds_documento, 
                    ds_teor, 
                    fl_prazo,
                    dt_prazo, 
                    nr_dias_prazo, 
                    cd_gerencia, 
                    arquivo, 
                    arquivo_nome,
                    cd_correspondencia_recebida_item,
                    ds_link_acesso,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_solic_fiscalizacao_audit).",
                    ".(intval($args['cd_solic_fiscalizacao_audit_origem']) > 0 ? intval($args['cd_solic_fiscalizacao_audit_origem']) : "DEFAULT").",
                    ".(trim($args['ds_origem']) != '' ? str_escape($args['ds_origem']) : "DEFAULT").",
                    ".(trim($args['dt_recebimento']) != '' ? "TO_DATE('".trim($args['dt_recebimento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(intval($args['cd_solic_fiscalizacao_audit_tipo']) > 0 ? intval($args['cd_solic_fiscalizacao_audit_tipo']) : "DEFAULT").",
                    ".(trim($args['ds_tipo']) != '' ? str_escape($args['ds_tipo']) : "DEFAULT").",
                    ".(trim($args['ds_documento']) != '' ? str_escape($args['ds_documento']) : "DEFAULT").",
                    ".(trim($args['ds_teor']) != '' ? str_escape($args['ds_teor']) : "DEFAULT").",
                    ".(trim($args['fl_prazo']) != '' ? "'".trim($args['fl_prazo'])."'" : "DEFAULT").",
                    ".(((trim($args['fl_prazo']) == 'D') AND (trim($args['dt_prazo']) != '')) 
                            ? "TO_DATE('".trim($args['dt_prazo'])."', 'DD/MM/YYYY')" 
                            : (((trim($args['fl_prazo']) == 'C') AND (intval($args['nr_dias_prazo']) > 0)) 
                                ? "(TO_DATE('".trim($args['dt_recebimento'])."', 'DD/MM/YYYY') + '".intval($args['nr_dias_prazo'])." days'::interval)::date"
                                : (((trim($args['fl_prazo']) == 'N') AND (intval($args['nr_dias_prazo']) > 0)) 
                                    ? "funcoes.dia_util('DEPOIS', TO_DATE('".trim($args['dt_recebimento'])."', 'DD/MM/YYYY'), ".intval($args['nr_dias_prazo'])." )::date"
                                    : "DEFAULT"
                                )
                            )
                    ).",
                    ".(intval($args['nr_dias_prazo']) > 0 ? intval($args['nr_dias_prazo']) : "DEFAULT").",
                    ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",
                    ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
                    ".(intval($args['cd_correspondencia_recebida_item']) > 0 ? intval($args['cd_correspondencia_recebida_item']) : "DEFAULT").",
                    funcoes.gera_link('https://www.fcprev.com.br/link_solicitacao/index.php/sign_in', ''),
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
                 );";

        if(count($args['gerencia_opcional']) > 0)
        {
            $qr_sql .= "
                INSERT INTO projetos.solic_fiscalizacao_audit_gerencia(cd_solic_fiscalizacao_audit, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['gerencia_opcional'])."')) x;";
        }

        if(count($args['gestao']) > 0)
        {
            $qr_sql .= "
                INSERT INTO projetos.solic_fiscalizacao_audit_gestao(cd_solic_fiscalizacao_audit, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['gestao'])."')) x;";
        }

        if(count($args['grupo_opcional']) > 0)
        {
            $qr_sql .= "
                INSERT INTO projetos.solic_fiscalizacao_audit_grupo_opcional(cd_solic_fiscalizacao_audit, cd_solic_fiscalizacao_audit_grupo, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['grupo_opcional']).")) x;";
        }
 
        $this->db->query($qr_sql); 

        return $cd_solic_fiscalizacao_audit;
    }

    public function atualizar($cd_solic_fiscalizacao_audit, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET cd_solic_fiscalizacao_audit_origem = ".(intval($args['cd_solic_fiscalizacao_audit_origem']) > 0 ? intval($args['cd_solic_fiscalizacao_audit_origem']) : "DEFAULT").",
                   ds_origem                          = ".(trim($args['ds_origem']) != '' ? str_escape($args['ds_origem']) : "DEFAULT").",
                   dt_recebimento                     = ".(trim($args['dt_recebimento']) != '' ? "TO_DATE('".trim($args['dt_recebimento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   cd_solic_fiscalizacao_audit_tipo   = ".(intval($args['cd_solic_fiscalizacao_audit_tipo']) > 0 ? intval($args['cd_solic_fiscalizacao_audit_tipo']) : "DEFAULT").",
                   ds_tipo                            = ".(trim($args['ds_tipo']) != '' ? str_escape($args['ds_tipo']) : "DEFAULT").",
                   ds_documento                       = ".(trim($args['ds_documento']) != '' ? str_escape($args['ds_documento']) : "DEFAULT").",
                   ds_teor                            = ".(trim($args['ds_teor']) != '' ? str_escape($args['ds_teor']) : "DEFAULT").",
                   fl_prazo                           = ".(trim($args['fl_prazo']) != '' ? "'".trim($args['fl_prazo'])."'" : "DEFAULT").",
                   dt_prazo                           = ".(((trim($args['fl_prazo']) == 'D') AND (trim($args['dt_prazo']) != '')) 
                                                                ? "TO_DATE('".trim($args['dt_prazo'])."', 'DD/MM/YYYY')" 
                                                                : (((trim($args['fl_prazo']) == 'C') AND (intval($args['nr_dias_prazo']) > 0)) 
                                                                    ? "(TO_DATE('".trim($args['dt_recebimento'])."', 'DD/MM/YYYY') + '".intval($args['nr_dias_prazo'])." days'::interval)::date"
                                                                    : (((trim($args['fl_prazo']) == 'N') AND (intval($args['nr_dias_prazo']) > 0)) 
                                                                        ? "funcoes.dia_util('DEPOIS', TO_DATE('".trim($args['dt_recebimento'])."', 'DD/MM/YYYY'), ".intval($args['nr_dias_prazo'])." )::date"
                                                                        : "DEFAULT"
                                                                    )
                                                                )
                                                        ).",
                   nr_dias_prazo                      = ".(intval($args['nr_dias_prazo']) > 0 ? intval($args['nr_dias_prazo']) : "DEFAULT").",
                   cd_gerencia                        = ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",
                   arquivo                            = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
                   arquivo_nome                       = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
                   cd_usuario_alteracao               = ".intval($args['cd_usuario']).",
                   dt_alteracao                       = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        if(count($args['gerencia_opcional']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_gerencia
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                   AND dt_exclusao IS NULL
                   AND cd_gerencia NOT IN ('".implode("','", $args['gerencia_opcional'])."');
       
                INSERT INTO projetos.solic_fiscalizacao_audit_gerencia(cd_solic_fiscalizacao_audit, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['gerencia_opcional'])."')) x
                 WHERE x.column1 NOT IN (SELECT a.cd_gerencia
                                           FROM projetos.solic_fiscalizacao_audit_gerencia a
                                          WHERE a.cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_gerencia
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['gestao']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_gestao
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                   AND dt_exclusao IS NULL
                   AND cd_gerencia NOT IN ('".implode("','", $args['gestao'])."');
       
                INSERT INTO projetos.solic_fiscalizacao_audit_gestao(cd_solic_fiscalizacao_audit, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['gestao'])."')) x
                 WHERE x.column1 NOT IN (SELECT a.cd_gerencia
                                           FROM projetos.solic_fiscalizacao_audit_gestao a
                                          WHERE a.cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_gestao
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['grupo_opcional']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_grupo_opcional
                   SET cd_usuario_exclusao         = ".intval($args['cd_usuario']).",
                       dt_exclusao                 = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                   AND dt_exclusao IS NULL
                   AND cd_solic_fiscalizacao_audit_grupo NOT IN (".implode(",", $args['grupo_opcional']).");
       
                INSERT INTO projetos.solic_fiscalizacao_audit_grupo_opcional(cd_solic_fiscalizacao_audit, cd_solic_fiscalizacao_audit_grupo, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['grupo_opcional']).")) x
                 WHERE x.column1 NOT IN (SELECT a.cd_solic_fiscalizacao_audit_grupo
                                           FROM projetos.solic_fiscalizacao_audit_grupo_opcional a
                                          WHERE a.cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_grupo_opcional
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
                   AND dt_exclusao IS NULL;";
        }

        $this->db->query($qr_sql);
    }

    public function salvar_atendimento_correspondencia($cd_solic_fiscalizacao_audit, $args)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET dt_envio_atendimento         = ".(trim($args['dt_envio_atendimento']) != '' ? "TO_DATE('".trim($args['dt_envio_atendimento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   nr_correspondencia_ano       = ".(trim($args['nr_correspondencia_ano']) != '' ? intval($args['nr_correspondencia_ano']) : "DEFAULT").",
                   nr_correspondencia_numero    = ".(trim($args['nr_correspondencia_numero']) != '' ? intval($args['nr_correspondencia_numero']) : "DEFAULT").",
                   ds_link_acesso               = funcoes.gera_link('https://www.fcprev.com.br/link_solicitacao/index.php/sign_in', ''),
                   ds_justificativa_atendimento = ".(trim($args['ds_justificativa_atendimento']) != '' ? str_escape($args['ds_justificativa_atendimento']) : "DEFAULT").",
                   arquivo_atendimento          = ".(trim($args['arquivo_atendimento']) != '' ? "'".trim($args['arquivo_atendimento'])."'" : "DEFAULT").",
                   arquivo_atendimento_nome     = ".(trim($args['arquivo_atendimento_nome']) != '' ? "'".trim($args['arquivo_atendimento_nome'])."'" : "DEFAULT").",
                   cd_usuario_alteracao         = ".intval($args['cd_usuario']).",
                   dt_alteracao                 = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        $this->db->query($qr_sql); 
    }

    public function atualizar_atendimento_correspondencia($cd_solic_fiscalizacao_audit, $args)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET dt_envio_atendimento         = ".(trim($args['dt_envio_atendimento']) != '' ? "TO_DATE('".trim($args['dt_envio_atendimento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   nr_correspondencia_ano       = ".(trim($args['nr_correspondencia_ano']) != '' ? intval($args['nr_correspondencia_ano']) : "DEFAULT").",
                   nr_correspondencia_numero    = ".(trim($args['nr_correspondencia_numero']) != '' ? intval($args['nr_correspondencia_numero']) : "DEFAULT").",
                   arquivo_atendimento          = ".(trim($args['arquivo_atendimento']) != '' ? "'".trim($args['arquivo_atendimento'])."'" : "DEFAULT").",
                   arquivo_atendimento_nome     = ".(trim($args['arquivo_atendimento_nome']) != '' ? "'".trim($args['arquivo_atendimento_nome'])."'" : "DEFAULT").",
                   cd_usuario_alteracao         = ".intval($args['cd_usuario']).",
                   dt_alteracao                 = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        $this->db->query($qr_sql); 
    }

    public function get_gerencia_opcional($cd_solic_fiscalizacao_audit)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_gerencia,
                   cd_gerencia
              FROM projetos.solic_fiscalizacao_audit_gerencia
             WHERE dt_exclusao                 IS NULL
               AND cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
             ORDER BY cd_gerencia;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_gestao($cd_solic_fiscalizacao_audit)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_gestao,
                   cd_gerencia
              FROM projetos.solic_fiscalizacao_audit_gestao
             WHERE dt_exclusao                      IS NULL
               AND cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
             ORDER BY cd_gerencia;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_gerencia_opcional_grupo($cd_solic_fiscalizacao_audit)
    {
    	$qr_sql = "
    		SELECT sfag.ds_email_grupo,
    		       sfag.cd_solic_fiscalizacao_audit_grupo,
    		       sfag.ds_grupo
    		  FROM projetos.solic_fiscalizacao_audit_grupo_opcional sfago
    		  JOIN projetos.solic_fiscalizacao_audit_grupo sfag
    		    ON sfag.cd_solic_fiscalizacao_audit_grupo = sfago.cd_solic_fiscalizacao_audit_grupo
    		 WHERE sfago.dt_exclusao                 IS NULL
    		   AND sfag.dt_exclusao                  IS NULL
    		   AND sfago.cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_gerencia_opcional_grupo_integrante($cd_solic_fiscalizacao_audit_grupo)
    {
    	$qr_sql = "
    		SELECT funcoes.get_usuario(cd_usuario) AS ds_usuario
    		  FROM projetos.solic_fiscalizacao_audit_grupo_integrante
    		 WHERE dt_exclusao IS NULL
    		   AND cd_solic_fiscalizacao_audit_grupo = ".intval($cd_solic_fiscalizacao_audit_grupo).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_grupo($cd_solic_fiscalizacao_audit)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_grupo
              FROM projetos.solic_fiscalizacao_audit_grupo_opcional
             WHERE dt_exclusao                      IS NULL
               AND cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function enviar($cd_solic_fiscalizacao_audit, $cd_liquid, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET cd_liquid        = ".intval($cd_liquid).",
                   cd_usuario_envio = ".intval($cd_usuario).",
                   dt_envio         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        $this->db->query($qr_sql);
    }

    public function solicitar_prorrogacao($cd_solic_fiscalizacao_audit, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET dt_solicitacao_prorrogacao   = ".(trim($args['dt_solicitacao_prorrogacao']) != '' ? "TO_DATE('".trim($args['dt_solicitacao_prorrogacao'])."', 'DD/MM/YYYY')"  : "DEFAULT").",
                   ds_solicitacao_prorrogacao   = ".(trim($args['ds_solicitacao_prorrogacao']) != '' ? str_escape($args['ds_solicitacao_prorrogacao']) : "DEFAULT").",
                   arquivo_minuta               = ".(trim($args['arquivo_minuta']) != '' ? str_escape($args['arquivo_minuta']) : "DEFAULT").",
                   arquivo_minuta_nome          = ".(trim($args['arquivo_minuta_nome']) != '' ? str_escape($args['arquivo_minuta_nome']) : "DEFAULT").",
                   cd_usuario_alteracao         = ".intval($args['cd_usuario']).",
                   cd_usuario_solic_prorrogacao = ".intval($args['cd_usuario']).",
                   dt_solic_prorrogacao         = CURRENT_TIMESTAMP, 
                   dt_alteracao                 = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        $this->db->query($qr_sql);
    }

    public function confirma_prorrogacao($cd_solic_fiscalizacao_audit, $args)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET dt_prazo_porrogado   = ".(trim($args['dt_prazo_porrogado']) != '' ? "TO_DATE('".trim($args['dt_prazo_porrogado'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   arquivo_pedido       = ".(trim($args['arquivo_pedido']) != '' ? str_escape($args['arquivo_pedido']) : "DEFAULT").",
                   arquivo_pedido_nome  = ".(trim($args['arquivo_pedido_nome']) != '' ? str_escape($args['arquivo_pedido_nome']) : "DEFAULT").",
                   cd_liquid_minuta     = ".intval($args['cd_liquid']).",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        $this->db->query($qr_sql);
    }

    public function nao_confirma_prorrogacao($cd_solic_fiscalizacao_audit, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET dt_solicitacao_prorrogacao   = NULL,
                   ds_solicitacao_prorrogacao   = NULL,
                   cd_usuario_solic_prorrogacao = NULL,
                   dt_solic_prorrogacao         = NULL,
                   cd_usuario_alteracao         = ".intval($cd_usuario).",
                   dt_alteracao                 = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        $this->db->query($qr_sql);
    }

    public function salvar_acompanhamento($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.solic_fiscalizacao_audit_acompanhamento
                 (
                    cd_solic_fiscalizacao_audit, 
                    ds_solic_fiscalizacao_audit_acompanhamento, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($args['cd_solic_fiscalizacao_audit']).",
                    ".(trim($args['ds_solic_fiscalizacao_audit_acompanhamento']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_acompanhamento']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."   
                 );";

        $this->db->query($qr_sql);
    }

    public function listar_acompanhamento($cd_solic_fiscalizacao_audit)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_acompanhamento,
                   ds_solic_fiscalizacao_audit_acompanhamento,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
              FROM projetos.solic_fiscalizacao_audit_acompanhamento
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
               AND dt_exclusao                 IS NULL";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_next_documentacao($cd_solic_fiscalizacao_audit)
    {
        $qr_sql = "
            SELECT COALESCE(MAX(nr_item), '1') AS nr_item,
                   TO_CHAR(COALESCE(MAX(dt_prorrogacao_prazo_retorno), MAX(dt_prazo_retorno)), 'DD/MM/YYYY') AS dt_prazo_retorno
              FROM projetos.solic_fiscalizacao_audit_documentacao
             WHERE dt_exclusao                 IS NULL
               AND cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_documentacao($cd_solic_fiscalizacao_audit_documentacao)
    {
        $qr_sql = "
            SELECT sfad.cd_solic_fiscalizacao_audit_documentacao,
                   sfad.cd_solic_fiscalizacao_audit,
                   sfad.ds_solic_fiscalizacao_audit_documentacao,
                   sfad.nr_item,
                   TO_CHAR(COALESCE(sfad.dt_prorrogacao_prazo_retorno, sfad.dt_prazo_retorno), 'DD/MM/YYYY') AS dt_prazo_retorno,
                   TO_CHAR(sfad.dt_atendimento_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento_responsavel,
                   TO_CHAR(sfad.dt_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento,
                   TO_CHAR(sfad.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   TO_CHAR(sfad.dt_envio_conferencia, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_conferencia,
                   sfad.fl_atendeu,
                   sfad.ds_motivo_atendeu,
                   sfad.cd_gerencia,
                   sfad.ds_atendimento,
                   sfa.ds_documento,
                   sfa.ds_tipo,
                   COALESCE(sfa.ds_origem, o.ds_solic_fiscalizacao_audit_origem) AS ds_origem,
                   COALESCE(sfa.ds_tipo, t.ds_solic_fiscalizacao_audit_tipo) AS ds_tipo,
                   sfa.cd_gerencia AS cd_area_consolidadora,
                   funcoes.get_usuario_gerente(sfad.cd_gerencia) AS cd_usuario_gerente,
                   funcoes.get_usuario_nome(funcoes.get_usuario_gerente(sfad.cd_gerencia)) AS ds_usuario_gerente,
                   funcoes.get_usuario_gerente_substituto(sfad.cd_gerencia) AS cd_usuario_substituto,
                   funcoes.get_usuario_nome(funcoes.get_usuario_gerente_substituto(sfad.cd_gerencia)) AS ds_usuario_substituto,
                   sfad.fl_atendeu_conferencia,
                   sfad.ds_motivo_atendeu_conferencia,
                   sfad.cd_usuario_conferente,
                   sfad.cd_usuario_sub_conferente,
                   funcoes.get_usuario(sfad.cd_usuario_conferente) AS ds_usuario_conferente,
                   funcoes.get_usuario(sfad.cd_usuario_sub_conferente) AS ds_usuario_sub_conferente,
                   sfad.fl_verificar_gerencia,
                   sfad.fl_confirma_gerencia
              FROM projetos.solic_fiscalizacao_audit_documentacao sfad
              JOIN projetos.solic_fiscalizacao_audit sfa
                ON sfa.cd_solic_fiscalizacao_audit = sfad.cd_solic_fiscalizacao_audit
              JOIN projetos.solic_fiscalizacao_audit_origem o
                ON o.cd_solic_fiscalizacao_audit_origem = sfa.cd_solic_fiscalizacao_audit_origem
              JOIN projetos.solic_fiscalizacao_audit_tipo t
                ON t.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo
             WHERE sfad.cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function resp_area_consolidadora($cd_area_consolidadora)
    {
        $qr_sql = "
            SELECT funcoes.get_usuario(cd_usuario) || '@eletroceee.com.br' AS ds_usuario_email
              FROM projetos.solic_entrega_documento_resp_area_consolidadora
             WHERE cd_gerencia = '".trim($cd_area_consolidadora)."'
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_documentacao($args = array())
    {
        $cd_solic_fiscalizacao_audit_documentacao = intval($this->db->get_new_id('projetos.solic_fiscalizacao_audit_documentacao', 'cd_solic_fiscalizacao_audit_documentacao'));

        $qr_sql = "
            INSERT INTO projetos.solic_fiscalizacao_audit_documentacao
                 (
                    cd_solic_fiscalizacao_audit_documentacao, 
                    cd_solic_fiscalizacao_audit, 
                    ds_solic_fiscalizacao_audit_documentacao, 
                    nr_item, 
                    cd_gerencia,
                    dt_prazo_retorno,
                    fl_verificar_gerencia,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                  )
             VALUES 
                  (
                    ".intval($cd_solic_fiscalizacao_audit_documentacao).",
                    ".(intval($args['cd_solic_fiscalizacao_audit']) > 0 ? intval($args['cd_solic_fiscalizacao_audit']) : "DEFAULT").",
                    ".(trim($args['ds_solic_fiscalizacao_audit_documentacao']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_documentacao']) : "DEFAULT").",
                    ".(trim($args['nr_item']) != '' ? "'".trim($args['nr_item'])."'" : "DEFAULT").",
                    ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",
                    ".(trim($args['dt_prazo_retorno']) != '' ? "TO_DATE('".trim($args['dt_prazo_retorno'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['fl_verificar_gerencia']) != '' ? "'".trim($args['fl_verificar_gerencia'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])." 
                  );";

        if(count($args['usuario']) > 0)
        {
            $qr_sql .= "
                INSERT INTO projetos.solic_fiscalizacao_audit_documentacao_responsavel(cd_solic_fiscalizacao_audit_documentacao, cd_usuario, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit_documentacao).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['usuario']).")) x;";
        }

        $this->db->query($qr_sql);

        return $cd_solic_fiscalizacao_audit_documentacao;
    }

    public function atualizar_documentacao($cd_solic_fiscalizacao_audit_documentacao, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET ds_solic_fiscalizacao_audit_documentacao = ".(trim($args['ds_solic_fiscalizacao_audit_documentacao']) != '' ? str_escape($args['ds_solic_fiscalizacao_audit_documentacao']) : "DEFAULT").",
                   nr_item                                  = ".(trim($args['nr_item']) != '' ? "'".trim($args['nr_item'])."'" : "DEFAULT").",
                   cd_gerencia                              = ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",
                   dt_prazo_retorno                         = ".(trim($args['dt_prazo_retorno']) != '' ? "TO_DATE('".trim($args['dt_prazo_retorno'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   fl_confirma_gerencia                     = ".(trim($args['fl_confirma_gerencia']) != '' ? "'".trim($args['fl_confirma_gerencia'])."'" : "DEFAULT").",
                   cd_usuario_alteracao                     = ".intval($args['cd_usuario']).", 
                   dt_alteracao                             = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        if(count($args['usuario']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_documentacao_responsavel
                   SET cd_usuario_exclusao                      = ".intval($args['cd_usuario']).",
                       dt_exclusao                              = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
                   AND dt_exclusao                              IS NULL
                   AND cd_usuario                               NOT IN (".implode(",", $args['usuario']).");
       
                INSERT INTO projetos.solic_fiscalizacao_audit_documentacao_responsavel(cd_solic_fiscalizacao_audit_documentacao, cd_usuario, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_solic_fiscalizacao_audit_documentacao).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['usuario']).")) x
                 WHERE x.column1 NOT IN (SELECT a.cd_usuario
                                           FROM projetos.solic_fiscalizacao_audit_documentacao_responsavel a
                                          WHERE a.cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_documentacao_responsavel
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
                   AND dt_exclusao                              IS NULL;";
        }

        $this->db->query($qr_sql);
    }

    public function validar_gerencia($cd_solic_fiscalizacao_audit_documentacao, $fl_confirma_gerencia,  $cd_gerencia, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET fl_confirma_gerencia          = ".(trim($fl_confirma_gerencia) != '' ?  "'".trim($fl_confirma_gerencia)."'" : "DEFAULT").",
                   cd_gerencia_valida           = ".(trim($cd_gerencia) != '' ? "'".trim($cd_gerencia)."'" : "DEFAULT").",
                   cd_usuario_confirma_gerencia = ".intval($cd_usuario).", 
                   dt_confirma_gerencia         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }

    public function set_gerencia_apoio($cd_solic_fiscalizacao_audit_documentacao, $gerencia_apoio, $cd_usuario)
    {
        $qr_sql = "
            INSERT INTO projetos.solic_fiscalizacao_audit_gerencia_apoio(cd_solic_fiscalizacao_audit_documentacao, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
            SELECT ".intval($cd_solic_fiscalizacao_audit_documentacao).", x.column1, ".intval($cd_usuario).", ".intval($cd_usuario)."
              FROM (VALUES ('".implode("'),('", $gerencia_apoio)."')) x;";

        $this->db->query($qr_sql);
    }

    public function get_gerencia_apoio($cd_solic_fiscalizacao_audit_documentacao)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_gerencia_apoio,
                   cd_gerencia
              FROM projetos.solic_fiscalizacao_audit_gerencia_apoio
             WHERE dt_exclusao                      IS NULL
               AND cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
             ORDER BY 1;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function excluir_documentacao($cd_solic_fiscalizacao_audit_documentacao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }

    public function listar_documentacao($cd_solic_fiscalizacao_audit)
    {
        $qr_sql = "
            SELECT sfad.cd_solic_fiscalizacao_audit_documentacao,
                   sfad.ds_solic_fiscalizacao_audit_documentacao,
                   sfad.nr_item,
                   sfad.cd_gerencia,
                   TO_CHAR(COALESCE(sfad.dt_prorrogacao_prazo_retorno, sfad.dt_prazo_retorno), 'DD/MM/YYYY') AS dt_prazo_retorno,
                   TO_CHAR(sfad.dt_atendimento_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento_responsavel,
                   TO_CHAR(sfad.dt_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento,
                   sfad.fl_atendeu,
                   (CASE WHEN sfad.fl_atendeu = 'S' 
                         THEN 'Sim'
                         WHEN sfad.fl_atendeu = 'N'
                         THEN 'Não'
                         ELSE ''
                   END) AS ds_atendeu,
                   sfad.ds_motivo_atendeu,
                   sfad.dt_envio_solicitacao,
                   sfad.cd_gerencia_valida,
                   sfad.fl_verificar_gerencia,
                   sfad.fl_confirma_gerencia,
                   CASE WHEN sfad.fl_verificar_gerencia = 'N' AND sfad.fl_confirma_gerencia IS NULL THEN 'Sem Verificação'
                        WHEN sfad.fl_verificar_gerencia = 'S' AND sfad.fl_confirma_gerencia IS NULL THEN 'Aguardando Verificação'
                        WHEN sfad.fl_verificar_gerencia = 'S' AND sfad.fl_confirma_gerencia = 'N' THEN 'Não Confirmado, Competência : ' || sfad.cd_gerencia_valida
                        WHEN sfad.fl_verificar_gerencia = 'S' AND sfad.fl_confirma_gerencia = 'S' THEN 'Confirmado a Competência'
                        ELSE 'Sem Verificação'
                   END AS ds_verificação_gerencia,
                   (SELECT COUNT(*)
                      FROM projetos.solic_fiscalizacao_audit_documentacao_anexo sfada
                     WHERE sfada.dt_exclusao IS NULL 
                       AND sfada.dt_removido IS NULL
                       AND sfada.cd_solic_fiscalizacao_audit_documentacao = sfad.cd_solic_fiscalizacao_audit_documentacao) AS qt_doc
              FROM projetos.solic_fiscalizacao_audit_documentacao sfad
             WHERE sfad.dt_exclusao                 IS NULL
               AND sfad.cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
             ORDER BY sfad.nr_item ASC;";

        return $this->db->query($qr_sql)->result_array();  
    }

    public function get_usuario_responsavel($cd_solic_fiscalizacao_audit_documentacao)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_documentacao_responsavel,
                   funcoes.get_usuario_nome(cd_usuario) AS ds_usuario,
                   funcoes.get_usuario(cd_usuario) || '@eletroceee.com.br' AS ds_usuario_email,
                   cd_usuario
              FROM projetos.solic_fiscalizacao_audit_documentacao_responsavel
             WHERE dt_exclusao                      IS NULL
               AND cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
             ORDER BY 2;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function enviar_solicitacao($cd_solic_fiscalizacao_audit, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit
               SET cd_usuario_envio_solicitacao_documento = ".intval($cd_usuario).",
                   dt_envio_solicitacao_documento         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit).";";

        $this->db->query($qr_sql);
    }

    public function salvar_envio_solicitacao($cd_solic_fiscalizacao_audit_documentacao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET cd_usuario_envio_solicitacao           = ".intval($cd_usuario).",
                   dt_envio_solicitacao                   = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }

    public function get_caminho_liquid($cd_liquid)
    {
        $qr_sql = "
            SELECT replace(ds_caminho, 'C:', '\\\\srvged') AS ds_caminho
              FROM oracle.liquid_caminho(".intval($cd_liquid).");";

        return $this->db->query($qr_sql)->row_array();
    }

    public function anexar_documento($cd_solic_fiscalizacao_audit_documentacao, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.solic_fiscalizacao_audit_documentacao_anexo
                 (
                    cd_solic_fiscalizacao_audit_documentacao,
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_solic_fiscalizacao_audit_documentacao).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);
    }

    public function excluir_documento($cd_solic_fiscalizacao_audit_documentacao_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao_anexo = ".intval($cd_solic_fiscalizacao_audit_documentacao_anexo).";";

        $this->db->query($qr_sql);
    }

    public function listar_documento($cd_solic_fiscalizacao_audit_documentacao)
    {
        $qr_sql = "
            SELECT cd_solic_fiscalizacao_audit_documentacao_anexo,
                   arquivo,
                   arquivo_nome,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario,
                   fl_envio_conferencia,
                   cd_liquid,
                   (CASE WHEN fl_envio_conferencia = 'S' THEN 'Sim'
                        ELSE 'Não'
                   END) AS ds_envio_conferencia
              FROM projetos.solic_fiscalizacao_audit_documentacao_anexo
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function encerrar_solicitacao($cd_solic_fiscalizacao_audit_documentacao, $cd_usuario, $fl_atendeu = '')
    {
         $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET cd_usuario_atendimento_responsavel = ".intval($cd_usuario).",
                   dt_atendimento_responsavel         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        if(trim($fl_atendeu) == 'N')
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_documentacao
                   SET fl_atendeu        = NULL,
                       ds_motivo_atendeu = NULL
                 WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";
        }

        $this->db->query($qr_sql);
    }

    public function encaminhar_conferencia($cd_solic_fiscalizacao_audit_documentacao, $solic_fiscalizacao_audit_documentacao_anexo, $args = array(), $fl_atendeu = '')
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET cd_usuario_conferente        = ".intval($args['cd_usuario_conferente']).",
                   cd_usuario_sub_conferente    = ".intval($args['cd_usuario_sub_conferente']).",
                   cd_usuario_envio_conferencia = ".intval($args['cd_usuario']).",
                   dt_envio_conferencia         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";

            UPDATE projetos.solic_fiscalizacao_audit_documentacao_anexo
               SET fl_envio_conferencia = 'S'
             WHERE cd_solic_fiscalizacao_audit_documentacao_anexo IN (".implode(",", $solic_fiscalizacao_audit_documentacao_anexo).");";


        if(trim($fl_atendeu) == 'N')
        {
            $qr_sql .= "
                UPDATE projetos.solic_fiscalizacao_audit_documentacao
                   SET fl_atendeu_conferencia        = NULL,
                       ds_motivo_atendeu_conferencia = NULL
                 WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";
        }

        $this->db->query($qr_sql);
    }

    public function atendeu($cd_solic_fiscalizacao_audit_documentacao, $cd_usuario)
    {
         $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET fl_atendeu             = 'S',
                   cd_usuario_atendimento = ".intval($cd_usuario).",
                   dt_atendimento         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }

    public function reabrir_atendimento($cd_solic_fiscalizacao_audit_documentacao)
    {
         $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET fl_atendeu             = NULL,
                   cd_usuario_atendimento = NULL,
                   dt_atendimento         = NULL
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }

    public function minhas_listar($cd_usuario, $cd_gerencia, $args = array())
    {
        $qr_sql = "
            SELECT projetos.nr_solic_fiscalizacao_audit(sfa.nr_ano, sfa.nr_numero) AS ds_ano_numero,
                   o.ds_solic_fiscalizacao_audit_origem, 
                   t.ds_solic_fiscalizacao_audit_tipo,
                   sfa.ds_documento,
                   sfad.ds_solic_fiscalizacao_audit_documentacao,
                   sfad.cd_solic_fiscalizacao_audit_documentacao,
                   sfad.nr_item,
                   TO_CHAR(COALESCE(sfad.dt_prorrogacao_prazo_retorno, sfad.dt_prazo_retorno), 'DD/MM/YYYY') AS dt_prazo_retorno,
                   TO_CHAR(sfad.dt_atendimento_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento_responsavel,
                   funcoes.get_usuario_nome(sfad.cd_usuario_atendimento_responsavel) AS ds_usuario_encerramento,
                   TO_CHAR(sfad.dt_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento,
                   (CASE WHEN sfad.dt_atendimento_responsavel IS NOT NULL AND sfad.fl_atendeu = 'S' 
                         THEN 'Atendeu'
                         WHEN sfad.dt_atendimento_responsavel IS NULL AND sfad.fl_atendeu = 'N'
                         THEN 'Não Atendeu'
                         WHEN sfa.dt_envio_solicitacao_documento IS NOT NULL AND sfad.dt_atendimento_responsavel IS NULL
                         THEN 'Aguardando Retorno'
                         WHEN sfad.dt_atendimento_responsavel IS NOT NULL AND sfad.fl_atendeu IS NULL
                         THEN 'Encaminhado'
                         ELSE ''
                     END) AS ds_status,
                   (CASE WHEN sfad.dt_atendimento_responsavel IS NOT NULL AND sfad.fl_atendeu = 'S' 
                         THEN 'label label-success'
                         WHEN sfad.dt_atendimento_responsavel IS NULL AND sfad.fl_atendeu = 'N'
                         THEN 'label label-important'
                         WHEN sfa.dt_envio_solicitacao_documento IS NOT NULL AND sfad.dt_atendimento_responsavel IS NULL
                         THEN 'label label-info'
                         WHEN sfad.dt_atendimento_responsavel IS NOT NULL AND sfad.fl_atendeu IS NULL
                         THEN 'label label-warning'
                         ELSE ''
                     END) AS ds_class_label
              FROM projetos.solic_fiscalizacao_audit sfa
              JOIN projetos.solic_fiscalizacao_audit_documentacao sfad
                ON sfad.cd_solic_fiscalizacao_audit = sfa.cd_solic_fiscalizacao_audit
              JOIN projetos.solic_fiscalizacao_audit_origem o
                ON o.cd_solic_fiscalizacao_audit_origem = sfa.cd_solic_fiscalizacao_audit_origem
              JOIN projetos.solic_fiscalizacao_audit_tipo t
                ON t.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo
             WHERE sfa.dt_exclusao IS NULL
               AND sfad.dt_exclusao IS NULL
               AND sfa.dt_envio_solicitacao_documento IS NOT NULL
               AND sfad.dt_envio_solicitacao IS NOT NULL
               AND (
                    ((SELECT COUNT(*) 
                        FROM projetos.solic_fiscalizacao_audit_documentacao_responsavel sfadr
                       WHERE sfadr.cd_solic_fiscalizacao_audit_documentacao = sfad.cd_solic_fiscalizacao_audit_documentacao) = 0
                         AND sfad.cd_gerencia = '".trim($cd_gerencia)."'
                         AND sfad.dt_exclusao IS NULL) 
                        OR
                        (
                         (SELECT COUNT(*)
                            FROM projetos.solic_fiscalizacao_audit_documentacao_responsavel sfadr2
                           WHERE sfadr2.cd_solic_fiscalizacao_audit_documentacao = sfad.cd_solic_fiscalizacao_audit_documentacao
                             AND sfadr2.cd_usuario = ".$cd_usuario." 
                             AND sfad.dt_exclusao IS NULL) > 0
                        ) 
                    )
               ".(intval($args['nr_numero']) > 0 ? "AND sfa.nr_numero = ".intval($args['nr_numero']) : "")."
               ".(intval($args['nr_ano']) > 0 ? "AND sfa.nr_ano = ".intval($args['nr_ano']) : "")."
               ".(((trim($args['dt_prazo_ini']) != '') AND trim($args['dt_prazo_fim']) != '') ? "AND DATE_TRUNC('day', COALESCE(sfad.dt_prorrogacao_prazo_retorno, sfad.dt_prazo_retorno)) BETWEEN TO_DATE('".$args['dt_prazo_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_fim']."', 'DD/MM/YYYY')" : '')."
               ".(trim($args['cd_solic_fiscalizacao_audit_origem']) != '' ? "AND sfa.cd_solic_fiscalizacao_audit_origem = '".trim($args['cd_solic_fiscalizacao_audit_origem'])."'" : "")."
               ".(trim($args['cd_solic_fiscalizacao_audit_tipo']) != '' ? "AND sfa.cd_solic_fiscalizacao_audit_tipo = '".trim($args['cd_solic_fiscalizacao_audit_tipo'])."'" : "")."
               ".trim($args['status'] == "AR" ? "AND sfa.dt_envio_solicitacao_documento IS NOT NULL AND sfad.dt_atendimento_responsavel IS NULL AND sfad.fl_atendeu IS NULL" : "")."
               ".trim($args['status'] == "E" ? "AND sfad.dt_atendimento_responsavel IS NOT NULL AND sfad.fl_atendeu IS NULL" : "")."
               ".trim($args['status'] == "A" ? "AND sfad.dt_atendimento_responsavel IS NOT NULL AND sfad.fl_atendeu = 'S'" : "")."
               ".trim($args['status'] == "NA" ? "AND sfad.dt_atendimento_responsavel IS NULL AND sfad.fl_atendeu = 'N'" : "")."
               ;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_resp_area_consolidadora($cd_gerencia, $cd_usuario)
    {
        $qr_sql = "
            SELECT COUNT(*) AS tl_permissao
              FROM projetos.solic_entrega_documento_resp_area_consolidadora
             WHERE dt_exclusao IS NULL
               AND cd_gerencia = '".trim($cd_gerencia)."'
               AND cd_usuario  = ".intval($cd_usuario).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_documentos($cd_solic_fiscalizacao_audit_documentacao)
    {
        $qr_sql = "
            SELECT sfada.cd_solic_fiscalizacao_audit_documentacao_anexo,
                   TO_CHAR(sfada.dt_removido, 'DD/MM/YYYY HH24:MI:SS') AS dt_removido,
                   funcoes.get_usuario_nome(sfada.cd_usuario_removido) AS ds_usuario,
                   sfad.fl_atendeu,
                   sfada.arquivo,
                   sfada.arquivo_nome,
                   sfada.cd_liquid,
                   sfada.fl_encaminhar_documento,
                   (CASE WHEN sfada.fl_encaminhar_documento = 'S' THEN 'Sim'
                         ELSE 'Não'
                    END) AS ds_encaminhar_documento
              FROM projetos.solic_fiscalizacao_audit_documentacao_anexo sfada 
              JOIN projetos.solic_fiscalizacao_audit_documentacao sfad 
                ON sfad.cd_solic_fiscalizacao_audit_documentacao = sfada.cd_solic_fiscalizacao_audit_documentacao
             WHERE sfada.dt_exclusao IS NULL
               AND sfada.cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
             ORDER BY sfada.dt_inclusao DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_encaminhar_documento($cd_solic_fiscalizacao_audit_documentacao_anexo, $fl_salvar, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao_anexo
               SET fl_encaminhar_documento = ".(trim($fl_salvar) != '' ? str_escape($fl_salvar) : "DEFAULT").",
                   cd_usuario_alteracao    = ".intval($cd_usuario).",
                   dt_alteracao            = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao_anexo = ".intval($cd_solic_fiscalizacao_audit_documentacao_anexo).";";

        $this->db->query($qr_sql);
    }

    public function remover_documento($cd_usuario, $cd_solic_fiscalizacao_audit_documentacao_anexo)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao_anexo
               SET cd_usuario_removido = ".intval($cd_usuario).",
                   dt_removido         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao_anexo = ".intval($cd_solic_fiscalizacao_audit_documentacao_anexo).";";

        $this->db->query($qr_sql);
    }

    public function encerra_documentacao($cd_solic_fiscalizacao_audit, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   dt_encerramento         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
               AND dt_exclusao                 IS NULL;";

        $this->db->query($qr_sql);
    }

    public function reabrir_documentacao($cd_solic_fiscalizacao_audit, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET cd_usuario_encerramento = NULL,
                   dt_encerramento         = NULL
             WHERE cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
               AND dt_exclusao                 IS NULL;";

        $this->db->query($qr_sql);
    }

    public function salvar_atendimento($cd_solic_fiscalizacao_audit_documentacao, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET fl_atendeu                         = ".(trim($args['fl_atendeu']) != '' ? str_escape($args['fl_atendeu']) : "DEFAULT").",
                   ds_motivo_atendeu                  = ".(trim($args['ds_motivo_atendeu']) != '' ? str_escape($args['ds_motivo_atendeu']) : "DEFAULT").", 
                   dt_prorrogacao_prazo_retorno       = ".(trim($args['dt_prorrogacao_prazo_retorno']) != '' ? "TO_DATE('".trim($args['dt_prorrogacao_prazo_retorno'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   cd_usuario_alteracao               = ".intval($args['cd_usuario']).",
                   dt_alteracao                       = CURRENT_TIMESTAMP,
                   cd_usuario_atendimento_responsavel = NULL,
                   dt_atendimento_responsavel         = NULL,
                   cd_usuario_atendimento_conferencia = NULL,
                   dt_atendimento_conferencia         = NULL,
                   fl_atendeu_conferencia             = NULL,
                   cd_usuario_envio_conferencia       = NULL,
                   dt_envio_conferencia               = NULL
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }

    public function get_documentos_encerrados($cd_solic_fiscalizacao_audit)
    {
         $qr_sql = "
            SELECT sfada.cd_solic_fiscalizacao_audit_documentacao_anexo,
                   sfada.arquivo, 
                   sfada.arquivo_nome,
                   sfad.nr_item
              FROM projetos.solic_fiscalizacao_audit_documentacao_anexo sfada
              JOIN projetos.solic_fiscalizacao_audit_documentacao sfad
                ON sfad.cd_solic_fiscalizacao_audit_documentacao = sfada.cd_solic_fiscalizacao_audit_documentacao
             WHERE sfada.dt_removido                IS NULL
               AND sfada.dt_exclusao                IS NULL
               AND sfad.dt_exclusao                 IS NULL
               AND sfada.cd_liquid                  IS NULL
               AND sfad.cd_solic_fiscalizacao_audit = ".intval($cd_solic_fiscalizacao_audit)."
             ORDER BY nr_item, 
                      arquivo_nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_documentos($cd_solic_fiscalizacao_audit, $cd_solic_fiscalizacao_audit_documentacao)
    {
         $qr_sql = "
            SELECT sfada.cd_solic_fiscalizacao_audit_documentacao_anexo,
                   sfada.arquivo, 
                   sfada.arquivo_nome,
                   sfad.nr_item,
                   funcoes.remove_acento(sfada.arquivo_nome) AS arquivo_nome_zip
              FROM projetos.solic_fiscalizacao_audit_documentacao_anexo sfada
              JOIN projetos.solic_fiscalizacao_audit_documentacao sfad
                ON sfad.cd_solic_fiscalizacao_audit_documentacao = sfada.cd_solic_fiscalizacao_audit_documentacao
             WHERE sfada.dt_removido                             IS NULL
               AND sfada.dt_exclusao                             IS NULL
               AND sfad.dt_exclusao                              IS NULL
               AND sfad.cd_solic_fiscalizacao_audit              = ".intval($cd_solic_fiscalizacao_audit)."
               AND sfad.cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao)."
             ORDER BY nr_item, 
                      arquivo_nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function atualiza_documentacao_anexo_liquid($cd_solic_fiscalizacao_audit_documentacao_anexo, $cd_liquid)
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao_anexo
               SET cd_liquid = ".intval($cd_liquid)."
             WHERE cd_solic_fiscalizacao_audit_documentacao_anexo = ".intval($cd_solic_fiscalizacao_audit_documentacao_anexo).";";

        $this->db->query($qr_sql);
    }

    public function minhas_conferencia_listar($cd_usuario, $args = array())
    {
        $qr_sql = "
            SELECT projetos.nr_solic_fiscalizacao_audit(sfa.nr_ano, sfa.nr_numero) AS ds_ano_numero,
                   o.ds_solic_fiscalizacao_audit_origem, 
                   t.ds_solic_fiscalizacao_audit_tipo,
                   sfa.ds_documento,
                   sfad.ds_solic_fiscalizacao_audit_documentacao,
                   sfad.cd_solic_fiscalizacao_audit_documentacao,
                   sfad.nr_item,
                   TO_CHAR(COALESCE(sfad.dt_prorrogacao_prazo_retorno, sfad.dt_prazo_retorno), 'DD/MM/YYYY') AS dt_prazo_retorno,
                   TO_CHAR(sfad.dt_envio_conferencia, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_conferencia,
                   funcoes.get_usuario_nome(sfad.cd_usuario_envio_conferencia) AS ds_usuario_envio_conferencia,
                   TO_CHAR(sfad.dt_atendimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_atendimento,
                   (CASE WHEN sfad.dt_atendimento_conferencia IS NOT NULL AND sfad.fl_atendeu_conferencia = 'S' 
                         THEN 'Atendeu'
                         WHEN sfad.dt_atendimento_conferencia IS NULL AND sfad.fl_atendeu_conferencia = 'N'
                         THEN 'Não Atendeu'
                         WHEN sfad.dt_envio_conferencia IS NOT NULL AND sfad.fl_atendeu_conferencia IS NULL
                         THEN 'Encaminhado'
                         ELSE ''
                     END) AS ds_status,
                   (CASE WHEN sfad.dt_atendimento_conferencia IS NOT NULL AND sfad.fl_atendeu_conferencia = 'S' 
                         THEN 'label label-success'
                         WHEN sfad.dt_atendimento_conferencia IS NULL AND sfad.fl_atendeu_conferencia = 'N'
                         THEN 'label label-important'
                         WHEN sfad.dt_envio_conferencia IS NOT NULL AND sfad.fl_atendeu_conferencia IS NULL
                         THEN 'label label-warning'
                         ELSE ''
                     END) AS ds_class_label
              FROM projetos.solic_fiscalizacao_audit_documentacao sfad 
              JOIN projetos.solic_fiscalizacao_audit sfa
                ON sfa.cd_solic_fiscalizacao_audit = sfad.cd_solic_fiscalizacao_audit
              JOIN projetos.solic_fiscalizacao_audit_origem o
                ON o.cd_solic_fiscalizacao_audit_origem = sfa.cd_solic_fiscalizacao_audit_origem
              JOIN projetos.solic_fiscalizacao_audit_tipo t
                ON t.cd_solic_fiscalizacao_audit_tipo = sfa.cd_solic_fiscalizacao_audit_tipo
             WHERE sfa.dt_exclusao IS NULL
               AND sfad.dt_exclusao IS NULL
               AND sfad.dt_envio_conferencia IS NOT NULL
               AND (cd_usuario_conferente = ".intval($cd_usuario)." OR cd_usuario_sub_conferente = ".intval($cd_usuario).")
               ".(intval($args['nr_numero']) > 0 ? "AND sfa.nr_numero = ".intval($args['nr_numero']) : "")."
               ".(intval($args['nr_ano']) > 0 ? "AND sfa.nr_ano = ".intval($args['nr_ano']) : "")."
               ".(((trim($args['dt_prazo_ini']) != '') AND trim($args['dt_prazo_fim']) != '') ? "AND DATE_TRUNC('day', COALESCE(sfad.dt_prorrogacao_prazo_retorno, sfad.dt_prazo_retorno)) BETWEEN TO_DATE('".$args['dt_prazo_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_fim']."', 'DD/MM/YYYY')" : '')."
               ".(trim($args['cd_solic_fiscalizacao_audit_origem']) != '' ? "AND sfa.cd_solic_fiscalizacao_audit_origem = '".trim($args['cd_solic_fiscalizacao_audit_origem'])."'" : "")."
               ".(trim($args['cd_solic_fiscalizacao_audit_tipo']) != '' ? "AND sfa.cd_solic_fiscalizacao_audit_tipo = '".trim($args['cd_solic_fiscalizacao_audit_tipo'])."'" : "")."
               ".trim($args['status'] == "E" ? "AND sfad.dt_envio_conferencia IS NOT NULL AND sfad.fl_atendeu_conferencia IS NULL" : "")."
               ".trim($args['status'] == "A" ? "AND sfad.dt_atendimento_conferencia IS NOT NULL AND sfad.fl_atendeu_conferencia = 'S'" : "")."
               ".trim($args['status'] == "NA" ? "AND sfad.dt_atendimento_conferencia IS NULL AND sfad.fl_atendeu_conferencia = 'N'" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_atendimento_conferencia($cd_solic_fiscalizacao_audit_documentacao, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET fl_atendeu_conferencia        = ".(trim($args['fl_atendeu_conferencia']) != '' ? str_escape($args['fl_atendeu_conferencia']) : "DEFAULT").",
                   ds_motivo_atendeu_conferencia = ".(trim($args['ds_motivo_atendeu_conferencia']) != '' ? str_escape($args['ds_motivo_atendeu_conferencia']) : "DEFAULT").", 
                   cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
                   dt_alteracao                  = CURRENT_TIMESTAMP,
                   cd_usuario_envio_conferencia  = NULL,
                   dt_envio_conferencia          = NULL
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }

    public function encerrar_solicitacao_conferencia($cd_solic_fiscalizacao_audit_documentacao, $cd_usuario)
    {
         $qr_sql = "
            UPDATE projetos.solic_fiscalizacao_audit_documentacao
               SET fl_atendeu_conferencia             = 'S',
                   cd_usuario_atendimento_conferencia = ".intval($cd_usuario).",
                   dt_atendimento_conferencia         = CURRENT_TIMESTAMP
             WHERE cd_solic_fiscalizacao_audit_documentacao = ".intval($cd_solic_fiscalizacao_audit_documentacao).";";

        $this->db->query($qr_sql);
    }
}