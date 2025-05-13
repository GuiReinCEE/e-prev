<?php
class Pauta_sg_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
            SELECT p.cd_pauta_sg,
                   p.nr_ata,
                   gestao.nr_pauta_sg(p.nr_ano, p.nr_pauta_sg) AS ano_numero,
                   p.local,
                   p.fl_sumula,
                   funcoes.get_usuario_nome(p.cd_usuario_aprovacao) AS ds_usuario_aprovacao,
                   TO_CHAR(p.dt_pauta_sg, 'DD/MM/YYYY HH24:MI') AS dt_pauta,
                   TO_CHAR(p.dt_pauta_sg_fim, 'DD/MM/YYYY HH24:MI') AS dt_pauta_sg_fim,
                   TO_CHAR(p.dt_aprovacao, 'DD/MM/YYYY HH24:MI') AS dt_aprovacao,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_sg_assunto a
                     WHERE a.dt_exclusao IS NULL
                       AND a.cd_pauta_sg = p.cd_pauta_sg) AS tl_itens,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_sg_assunto a
                     WHERE a.dt_exclusao IS NULL
                       AND a.cd_pauta_sg       = p.cd_pauta_sg
                       AND a.dt_retirada_pauta IS NULL
                       AND a.ds_decisao        IS NOT NULL) AS tl_itens_decisao,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_sg_assunto a
                     WHERE a.dt_exclusao       IS NULL
                       AND a.cd_pauta_sg       = p.cd_pauta_sg
                       AND a.dt_retirada_pauta IS NOT NULL) AS tl_itens_retirada,
                   (CASE WHEN p.fl_sumula = 'DE' 
                         THEN 'label label-success'
                         WHEN p.fl_sumula = 'CF' 
                         THEN 'label label-warning'
                         WHEN p.fl_sumula = 'IN' 
                         THEN 'label label-inverse'
                         ELSE 'label label-info'
                   END) AS class_sumula,
                   (CASE WHEN p.fl_tipo_reuniao = 'E' 
                         THEN 'Extraordinária'
                         WHEN p.fl_tipo_reuniao = 'O' 
                         THEN 'Ordinária'
                         ELSE ''
                   END) AS ds_tipo_reuniao
              FROM gestao.pauta_sg p
              WHERE p.dt_exclusao IS NULL
			    ".(trim($args['nr_ata']) != '' ? "AND p.nr_ata = ".intval($args['nr_ata']) : "")."
                ".(((trim($args['dt_pauta_sg_ini']) != '') AND (trim($args['dt_pauta_sg_fim']) != '')) ? "AND DATE_TRUNC('day', p.dt_pauta_sg) BETWEEN TO_DATE('".$args['dt_pauta_sg_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_sg_fim']."', 'DD/MM/YYYY')" : "")."
                ".(((trim($args['dt_pauta_sg_fim_ini']) != '') AND (trim($args['dt_pauta_sg_fim_fim']) != '')) ? "AND DATE_TRUNC('day', p.dt_pauta_sg_fim) BETWEEN TO_DATE('".$args['dt_pauta_sg_fim_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_sg_fim_fim']."', 'DD/MM/YYYY')" : "")."
                ".(trim($args['fl_sumula']) != '' ? "AND p.fl_sumula = '".trim($args['fl_sumula'])."'" : "")."
                ".(trim($args['fl_tipo_reuniao']) != '' ? "AND p.fl_tipo_reuniao = '".trim($args['fl_tipo_reuniao'])."'" : "")."
                ".(trim($args['fl_aprovado']) == 'N' ? "AND p.dt_aprovacao IS NULL" : "")."
                ".(trim($args['fl_aprovado']) == 'S' ? "AND p.dt_aprovacao IS NOT NULL" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_pauta_sg)
    {
        $qr_sql = "
            SELECT p.cd_pauta_sg,
			       MD5(p.cd_pauta_sg::TEXT) AS cd_pauta_sg_md5,
                   p.nr_ata,
				   p.nr_ano,
                   p.integracao_arq,
                   gestao.nr_pauta_sg(p.nr_ano, p.nr_pauta_sg) AS ano_numero,
                   p.fl_sumula,
                   p.local,
                   p.fl_tipo_reuniao,
                   TO_CHAR(p.dt_pauta_sg, 'DD/MM/YYYY') AS dt_pauta_sg,
                   TO_CHAR(p.dt_pauta_sg, 'DD/MM/YYYY') AS dt_pauta,
                   TO_CHAR(p.dt_pauta_sg, 'HH24:MI') AS hr_pauta,
                   TO_CHAR(p.dt_pauta_sg_fim, 'DD/MM/YYYY') AS dt_pauta_sg_fim,
                   TO_CHAR(p.dt_pauta_sg_fim, 'HH24:MI') AS hr_pauta_sg_fim,
                   TO_CHAR(p.dt_aprovacao, 'DD/MM/YYYY HH24:MI') AS dt_aprovacao,
                   TO_CHAR(p.dt_publicacao_libera,'DD/MM/YYYY') AS dt_publicacao_libera,
                   TO_CHAR(p.dt_publicacao,'DD/MM/YYYY HH24:MI:SS') AS dt_publicacao,
                   TO_CHAR(p.dt_envio_responsavel, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_responsavel,
                   funcoes.get_usuario_nome(p.cd_usuario_publicacao) AS ds_usuario_publicacao,
                   funcoes.get_usuario_nome(p.cd_usuario_aprovacao) AS ds_usuario_aprovacao,
                   funcoes.get_usuario_nome(p.cd_usuario_envio_responsavel) AS ds_usuario_envio_responsavel,
                   TO_CHAR(p.dt_envio_colegiado, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_colegiado,
                   TO_CHAR(p.dt_limite, 'DD/MM/YYYY') AS dt_limite,
                   (CASE WHEN p.fl_sumula = 'DE' 
                         THEN 'label label-success'
                         WHEN p.fl_sumula = 'CF' 
                         THEN 'label label-warning'
                         WHEN p.fl_sumula = 'IN' 
                         THEN 'label label-inverse'
                         ELSE 'label label-info'
                   END) AS class_sumula,
                   (
                    SELECT COUNT(*)
                      FROM gestao.pauta_sg_assunto ass1
                     WHERE ass1.dt_exclusao       IS NULL
                       AND ass1.dt_retirada_pauta IS NULL
                       AND ass1.cd_pauta_sg       = p.cd_pauta_sg
                   ) 
                   - 
                   (
                    SELECT COUNT(*)
                      FROM gestao.pauta_sg_assunto ass2
                     WHERE ass2.dt_exclusao       IS NULL
                       AND ass2.dt_retirada_pauta IS NULL
                       AND ass2.cd_pauta_sg       = p.cd_pauta_sg
                       AND ass2.ds_decisao        IS NOT NULL
                   ) AS tl_sem_decisao,
                   (CASE WHEN p.fl_tipo_reuniao = 'E' 
                         THEN 'Extraordinária'
                         WHEN p.fl_tipo_reuniao = 'O' 
                         THEN 'Ordinária'
                         ELSE ''
                   END) AS ds_tipo_reuniao
              FROM gestao.pauta_sg p
             WHERE p.cd_pauta_sg = ".intval($cd_pauta_sg).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function valida_numero_ata($cd_pauta_sg, $nr_ata, $fl_sumula)
    {
        $qr_sql = "
            SELECT COUNT(*) AS valida
              FROM gestao.pauta_sg
             WHERE dt_exclusao IS NULL
               AND nr_ata      = ".intval($nr_ata)." 
               AND fl_sumula   = ".str_escape($fl_sumula)."
               AND cd_pauta_sg != ".intval($cd_pauta_sg).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_pauta_sg = intval($this->db->get_new_id('gestao.pauta_sg', 'cd_pauta_sg'));

        $qr_sql = "
            INSERT INTO gestao.pauta_sg
                 (
                    cd_pauta_sg,
                    nr_ata,
                    dt_pauta_sg, 
                    dt_pauta_sg_fim,
                    local, 
                    fl_sumula,
                    fl_tipo_reuniao,
                    integracao_arq,
                    dt_limite,
                    dt_limite_anexo,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_pauta_sg).",
                    ".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
                    ".(trim($args['dt_pauta_sg']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_sg'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(trim($args['dt_pauta_sg_fim']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_sg_fim'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(trim($args['local']) != '' ? str_escape($args['local']) : "DEFAULT").",
                    ".(trim($args['fl_sumula']) != '' ? "'".trim($args['fl_sumula'])."'" : "DEFAULT").",
                    ".(trim($args['fl_tipo_reuniao']) != '' ? "'".trim($args['fl_tipo_reuniao'])."'" : "DEFAULT").",
                    ".(trim($args['integracao_arq']) != '' ? str_escape($args['integracao_arq']) : "DEFAULT").",
                    ".(trim($args['dt_pauta_sg']) != '' ? "funcoes.dia_util('ANTES', TO_DATE('".trim($args['dt_pauta_sg'])."', 'DD/MM/YYYY'), 3)" : "DEFAULT").",
                    ".(trim($args['dt_pauta_sg']) != '' ? "funcoes.dia_util('ANTES', TO_DATE('".trim($args['dt_pauta_sg'])."', 'DD/MM/YYYY'), 2)" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_pauta_sg;
    }

    public function salvar_integrante_presente($cd_pauta_sg, $fl_colegiado, $cd_usuario)
    {
        $qr_sql = "
            INSERT INTO gestao.pauta_sg_integrante_presente
                (
                    cd_pauta_sg_integrante,
                    cd_pauta_sg,
                    ds_pauta_sg_integrante_presente,
                    fl_presidente,
                    fl_secretaria,
                    fl_tipo,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
             SELECT cd_pauta_sg_integrante,
                    ".intval($cd_pauta_sg).",
                    ds_pauta_sg_integrante,
                    fl_presidente,
                    fl_secretaria,
                    fl_tipo,
                    ".intval($cd_usuario).",
                    ".intval($cd_usuario)."
               FROM gestao.pauta_sg_integrante
              WHERE dt_exclusao IS NULL
                AND dt_removido IS NULL
                AND fl_colegiado = ".str_escape($fl_colegiado).";";

        $this->db->query($qr_sql);
    }

    public function atualizar_titulares_presentes($cd_pauta_sg)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_integrante_presente AS t
               SET cd_pauta_sg_integrante_presente_titular = x.cd_pauta_sg_integrante_presente
              FROM (SELECT psip.cd_pauta_sg_integrante_presente,
                           psip.cd_pauta_sg,
                           psi.cd_pauta_sg_integrante
                      FROM gestao.pauta_sg_integrante_presente psip
                      JOIN gestao.pauta_sg_integrante psi
                        ON psi.cd_pauta_sg_integrante_titular = psip.cd_pauta_sg_integrante
                     WHERE psip.dt_exclusao IS NULL
                       AND psip.cd_pauta_sg = ".intval($cd_pauta_sg)."
                       AND psi.dt_exclusao IS NULL
                       AND psi.dt_removido IS NULL) x
             WHERE t.cd_pauta_sg_integrante = x.cd_pauta_sg_integrante
               AND t.cd_pauta_sg            = x.cd_pauta_sg;";

        $this->db->query($qr_sql); 
    }

    public function listar_presentes($cd_pauta_sg)
    {
        $qr_sql = "
            SELECT psip.cd_pauta_sg_integrante_presente,
                   psip.ds_pauta_sg_integrante_presente,
                   psip.fl_tipo,
                   (CASE WHEN psip.fl_tipo = 'S' THEN 'Suplente'
                         ELSE 'Titular'
                    END) AS ds_tipo,
                   psip.fl_presidente,
                   (CASE WHEN psip.fl_presidente = 'S' THEN 'Sim'
                         ELSE 'Não'
                    END) AS ds_presidente,
                   psip.fl_secretaria,
                   (CASE WHEN psip.fl_secretaria = 'S' THEN 'Sim'
                         ELSE 'Não'
                    END) AS ds_secretaria,
                   psip.fl_presente,
                   psip2.ds_pauta_sg_integrante_presente AS ds_pauta_sg_integrante_presente_titular
              FROM gestao.pauta_sg_integrante_presente psip
         LEFT JOIN gestao.pauta_sg_integrante_presente psip2
                ON psip2.cd_pauta_sg_integrante_presente = psip.cd_pauta_sg_integrante_presente_titular
             WHERE psip.dt_exclusao IS NULL
               AND psip.cd_pauta_sg = ".intval($cd_pauta_sg).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_presentes($cd_pauta_sg)
    {
        $qr_sql = "
            SELECT psip.cd_pauta_sg_integrante_presente AS cd_titular,
                   psip.ds_pauta_sg_integrante_presente AS ds_titular,
                   psip.fl_presente AS fl_titular_presente,
                   psip.fl_secretaria,
                   (CASE WHEN psip.fl_presidente = 'S'
                         THEN 1
                         ELSE 0
                    END) AS nr_presidente,
                   psip2.cd_pauta_sg_integrante_presente AS cd_suplente,
                   psip2.ds_pauta_sg_integrante_presente AS ds_suplente,
                   psip2.fl_presente AS fl_suplente_presente
              FROM gestao.pauta_sg_integrante_presente psip
              LEFT JOIN gestao.pauta_sg_integrante_presente psip2
                ON psip2.cd_pauta_sg_integrante_presente_titular = psip.cd_pauta_sg_integrante_presente
             WHERE psip.dt_exclusao IS NULL
               AND psip.cd_pauta_sg = ".intval($cd_pauta_sg)."
               AND psip.fl_tipo     != 'S'
             ORDER BY nr_presidente desc, psip.ds_pauta_sg_integrante_presente;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_presente($cd_pauta_sg_integrante_presente, $fl_presente, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_integrante_presente
               SET fl_presente = ".(trim($fl_presente) != '' ? str_escape($fl_presente) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_integrante_presente = ".intval($cd_pauta_sg_integrante_presente).";";

        $this->db->query($qr_sql);
    }

    public function salvar_presidente($cd_pauta_sg_integrante_presente, $fl_presidente, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_integrante_presente
               SET fl_presidente = ".(trim($fl_presidente) != '' ? str_escape($fl_presidente) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_integrante_presente = ".intval($cd_pauta_sg_integrante_presente).";";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_pauta_sg, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg
               SET nr_ata               = ".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
                   dt_pauta_sg          = ".(trim($args['dt_pauta_sg']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_sg'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   dt_pauta_sg_fim      = ".(trim($args['dt_pauta_sg_fim']) != '' ? "TO_TIMESTAMP('".trim($args['dt_pauta_sg_fim'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   local                = ".(trim($args['local']) != '' ? str_escape($args['local']) : "DEFAULT").",
                   fl_tipo_reuniao      = ".(trim($args['fl_tipo_reuniao']) != '' ? "'".trim($args['fl_tipo_reuniao'])."'" : "DEFAULT").",
                   integracao_arq       = ".(trim($args['integracao_arq']) != '' ? str_escape($args['integracao_arq']) : "DEFAULT").",
                   dt_limite            =  ".(trim($args['dt_pauta_sg']) != '' ? "funcoes.dia_util('ANTES', TO_DATE('".trim($args['dt_pauta_sg'])."', 'DD/MM/YYYY'), 3)" : "DEFAULT").",
                   dt_limite_anexo      =  ".(trim($args['dt_pauta_sg']) != '' ? "funcoes.dia_util('ANTES', TO_DATE('".trim($args['dt_pauta_sg'])."', 'DD/MM/YYYY'), 2)" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg).";";

        $this->db->query($qr_sql);
    }

    public function get_diretoria()
    {
        $qr_sql = "
            SELECT cd_diretoria AS value,
                   ds_diretoria AS text
              FROM projetos.diretoria
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function anexos_assuntos_removidos($cd_pauta_sg)
    {
    	$qr_sql = "
    		SELECT pa.nr_item_sumula,
			       paa.arquivo,
			       paa.arquivo_nome
			  FROM gestao.pauta_sg_assunto_anexo paa
			  JOIN gestao.pauta_sg_assunto pa
			    ON pa.cd_pauta_sg_assunto = paa.cd_pauta_sg_assunto
			 WHERE pa.cd_pauta_sg = ".intval($cd_pauta_sg).";";

		  return $this->db->query($qr_sql)->result_array();
    }

    public function publicar($cd_pauta_sg, $dt_publicacao_libera, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg
               SET dt_publicacao_libera  = ".(trim($dt_publicacao_libera) != '' ? "TO_DATE('".$dt_publicacao_libera."','DD/MM/YYYY')" : "NULL").",
                   dt_publicacao         = ".(trim($dt_publicacao_libera) != '' ? "CURRENT_TIMESTAMP" : "NULL").",
                   cd_usuario_publicacao = ".(trim($dt_publicacao_libera) != '' ? intval($cd_usuario) : "NULL").",
                   cd_usuario_alteracao  = ".intval($cd_usuario).",
                   dt_alteracao          = CURRENT_TIMESTAMP 
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg).";";
        
        $this->db->query($qr_sql);
    }

    public function get_gerente_secretaria()
    {
        $qr_sql = "
            SELECT codigo AS cd_usuario
              FROM projetos.usuarios_controledi 
             WHERE divisao IN ('SG', 'GRC')
               AND tipo    = 'G';";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_substituto_secretaria()
    {
        $qr_sql = "
            SELECT codigo AS cd_usuario
              FROM projetos.usuarios_controledi 
             WHERE divisao  IN ('SG', 'GRC')
               AND tipo     = 'U'
               AND indic_01 = 'S';";

        return $this->db->query($qr_sql)->row_array();
    }

    public function assunto_salvar($args = array())
    {
        $cd_pauta_sg_assunto = intval($this->db->get_new_id('gestao.pauta_sg_assunto', 'cd_pauta_sg_assunto'));

        $qr_sql = "
            INSERT INTO gestao.pauta_sg_assunto
                 (
                    cd_pauta_sg, 
                    cd_pauta_sg_assunto,
                    nr_item_sumula,
                    ds_pauta_sg_assunto, 
                    nr_tempo, 
                    nr_rds, 
                    nr_ano_rds, 
                    fl_aplica_rds,
                    fl_ordem_fornecimento,
                    instancia_aprovacao,
                    cd_gerencia_responsavel, 
                    cd_usuario_responsavel,
                    cd_gerencia_substituto,
                    cd_usuario_substituto,
                    cd_diretoria,
                    cd_pauta_sg_objetivo,
                    cd_pauta_sg_justificativa,
                    nr_ordem_fornecimento,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($args['cd_pauta_sg']).",
                    ".intval($cd_pauta_sg_assunto).",
                    ".(trim($args['nr_item_sumula']) != '' ? intval($args['nr_item_sumula']) : "DEFAULT").",
                    ".(trim($args['ds_pauta_sg_assunto']) != '' ? str_escape($args['ds_pauta_sg_assunto']) : "DEFAULT").",
                    ".(trim($args['nr_tempo']) != '' ? intval($args['nr_tempo']) : "DEFAULT").",
                    ".(trim($args['nr_rds']) != '' ? intval($args['nr_rds']) : "DEFAULT").",
                    ".(trim($args['nr_ano_rds']) != '' ? intval($args['nr_ano_rds']) : "DEFAULT").",
                    ".(trim($args['fl_aplica_rds']) != '' ? str_escape($args['fl_aplica_rds']) : "DEFAULT").",
                    ".(trim($args['fl_ordem_fornecimento']) != '' ? str_escape($args['fl_ordem_fornecimento']) : "DEFAULT").", 
                    ".(trim($args['instancia_aprovacao']) != '' ? str_escape($args['instancia_aprovacao']) : "DEFAULT").",
                    ".(trim($args['cd_gerencia_responsavel']) != '' ? "'".trim($args['cd_gerencia_responsavel'])."'" : "DEFAULT").",
                    ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                    ".(trim($args['cd_gerencia_substituto']) != '' ? "'".trim($args['cd_gerencia_substituto'])."'" : "DEFAULT").",
                    ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
                    ".(trim($args['cd_diretoria']) != '' ? "'".trim($args['cd_diretoria'])."'" : "DEFAULT").",
                    ".(trim($args['cd_pauta_sg_objetivo']) != '' ? intval($args['cd_pauta_sg_objetivo']) : "DEFAULT").",
                    ".(trim($args['cd_pauta_sg_justificativa']) != '' ? intval($args['cd_pauta_sg_justificativa']) : "DEFAULT").",
                    ".(trim($args['nr_ordem_fornecimento']) != '' ? intval($args['nr_ordem_fornecimento']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_pauta_sg_assunto;
    }

    public function assunto_atualizar($cd_pauta_sg_assunto, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET nr_item_sumula          = ".(trim($args['nr_item_sumula']) != '' ? intval($args['nr_item_sumula']) : "DEFAULT").",
                   nr_rds                  = ".(trim($args['nr_rds']) != '' ? intval($args['nr_rds']) : "DEFAULT").",
                   nr_ano_rds              = ".(trim($args['nr_ano_rds']) != '' ? intval($args['nr_ano_rds']) : "DEFAULT").",
                   fl_aplica_rds           = ".(trim($args['fl_aplica_rds']) != '' ? str_escape($args['fl_aplica_rds']) : "DEFAULT").",
                   fl_ordem_fornecimento   = ".(trim($args['fl_ordem_fornecimento']) != '' ? str_escape($args['fl_ordem_fornecimento']) : "DEFAULT").",
                   ds_pauta_sg_assunto     = ".(trim($args['ds_pauta_sg_assunto']) != '' ? str_escape($args['ds_pauta_sg_assunto']) : "DEFAULT").",
                   instancia_aprovacao     = ".(trim($args['instancia_aprovacao']) != '' ? str_escape($args['instancia_aprovacao']) : "DEFAULT").",
                   cd_gerencia_responsavel = ".(trim($args['cd_gerencia_responsavel']) != '' ? "'".trim($args['cd_gerencia_responsavel'])."'" : "DEFAULT").",
                   cd_usuario_responsavel  = ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                   cd_gerencia_substituto  = ".(trim($args['cd_gerencia_substituto']) != '' ? "'".trim($args['cd_gerencia_substituto'])."'" : "DEFAULT").",
                   cd_usuario_substituto   = ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
                   cd_diretoria            = ".(trim($args['cd_diretoria']) != '' ? "'".trim($args['cd_diretoria'])."'" : "DEFAULT").",
                   ds_decisao              = ".(trim($args['ds_decisao']) != '' ? str_escape($args['ds_decisao']) : "DEFAULT").",
                   fl_pendencia_gestao     = ".(trim($args['fl_pendencia_gestao']) != '' ? str_escape($args['fl_pendencia_gestao']) : "DEFAULT").",
                   fl_pautar_reuniao       = ".(trim($args['fl_pautar_reuniao']) != '' ? str_escape($args['fl_pautar_reuniao']) : "DEFAULT").",
                   fl_proxima_reuniao      = ".(trim($args['fl_proxima_reuniao']) != '' ? str_escape($args['fl_proxima_reuniao']) : "DEFAULT").",
                   nr_mes_pautar           = ".(intval($args['nr_mes_pautar']) > 0 ? intval($args['nr_mes_pautar']) : "DEFAULT").",
                   nr_ano_pautar           = ".(intval($args['nr_ano_pautar']) > 0 ? intval($args['nr_ano_pautar']) : "DEFAULT").",
                   tp_colegiado_pautar     = ".(trim($args['tp_colegiado_pautar']) != '' ? str_escape($args['tp_colegiado_pautar']) : "DEFAULT").",
                   cd_gerencia_pendencia   = ".(trim($args['cd_gerencia_pendencia']) != '' ? str_escape($args['cd_gerencia_pendencia']) : "DEFAULT").",
                   cd_usuario_pendencia    = ".(intval($args['cd_usuario_pendencia']) > 0 ? intval($args['cd_usuario_pendencia']) : "DEFAULT").",
                   cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
                   dt_alteracao            = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function get_pauta($cd_pauta_sg)
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_pauta_sg, 'MM/YYYY') AS ds_mes_ano,
                   fl_sumula
              FROM gestao.pauta_sg
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg).";";

        return $this->db->query($qr_sql)->row_array();
    }
	
    public function get_pauta_anterior($cd_pauta_sg, $fl_sumula, $ds_mes_ano)
    {
        $qr_sql = "
            SELECT COUNT(*) AS fl_pauta_anterior
              FROM gestao.pauta_sg
             WHERE dt_exclusao                     IS NULL
               AND TO_CHAR(dt_pauta_sg, 'MM/YYYY') = '".trim($ds_mes_ano)."'
               AND fl_sumula                       = '".trim($fl_sumula)."'
               AND cd_pauta_sg                     != ".intval($cd_pauta_sg)."
               AND fl_tipo_reuniao                 = 'O';";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_pauta_sg_anual($fl_sumula, $ds_mes_ano)
    {
        $qr_sql = "
            SELECT paa.cd_pauta_sg_anual_assunto,
                   paa.ds_assunto,
                   paa.nr_tempo,
                   paa.cd_gerencia_responsavel,
                   funcoes.get_gerente(paa.cd_gerencia_responsavel) AS cd_responsavel,
                   funcoes.get_substituto_gerencia(paa.cd_gerencia_responsavel) AS cd_substituto,
                   d.area AS cd_diretoria,
                   cd_pauta_sg_objetivo,
                   cd_pauta_sg_justificativa
              FROM gestao.pauta_sg_anual pa
              JOIN gestao.pauta_sg_anual_assunto paa
                ON paa.cd_pauta_sg_anual = pa.cd_pauta_sg_anual
              JOIN projetos.divisoes d
                ON d.codigo = paa.cd_gerencia_responsavel
             WHERE paa.cd_pauta_sg_assunto               IS NULL
               AND pa.dt_exclusao                        IS NULL
               AND pa.dt_confirmacao                     IS NOT NULL
               AND pa.dt_envio_responsavel               IS NOT NULL
               AND pa.fl_colegiado                       = '".trim($fl_sumula)."'
               AND TO_CHAR(paa.dt_referencia, 'MM/YYYY') = '".trim($ds_mes_ano)."';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function atualiza_pauta_anual($cd_pauta_sg_assunto, $cd_pauta_sg_anual_assunto, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_anual_assunto
               SET cd_pauta_sg_assunto       = ".intval($cd_pauta_sg_assunto).",
                   cd_usuario_alteracao      = ".intval($cd_usuario).",
                   dt_alteracao              = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_anual_assunto = ".intval($cd_pauta_sg_anual_assunto).";";

        $this->db->query($qr_sql);
    }

    public function get_numero_assunto_pauta($cd_pauta_sg)
    {
        $qr_sql = "
            SELECT nr_item_sumula
              FROM gestao.pauta_sg_assunto
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg)."
               AND dt_exclusao IS NULL
             ORDER BY nr_item_sumula DESC
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function assunto_listar($cd_pauta_sg, $fl_removido = 'N', $fl_enviado = '')
    {
      	$qr_sql = "
            SELECT a.cd_pauta_sg_assunto,
                   a.nr_item_sumula,
                   a.ds_pauta_sg_assunto,
                   a.nr_tempo,
                   a.cd_gerencia_responsavel,
                   a.cd_usuario_responsavel,
                   a.cd_gerencia_substituto,
                   a.cd_usuario_substituto,
                   a.cd_diretoria,
                   a.instancia_aprovacao,
                   a.ds_decisao,
                   a.fl_resolucao_diretoria,
                   gestao.nr_controle_rds(nr_ano_rds, nr_rds) AS nr_ano_numero_rds,
                   funcoes.get_usuario(a.cd_usuario_responsavel) || '@eletroceee.com.br' AS ds_email_responsavel,
                   funcoes.get_usuario(a.cd_usuario_substituto) || '@eletroceee.com.br' AS ds_email_substituto,
                   funcoes.get_usuario_nome(a.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(a.cd_usuario_substituto) AS ds_usuario_substituto,
                   COALESCE(a.cd_usuario_encerramento, a.cd_usuario_responsavel) AS cd_usuario_encerramento,
                   TO_CHAR(a.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   d.ds_diretoria,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_sg_assunto_anexo an
                     WHERE an.dt_exclusao IS NULL
                       AND an.cd_pauta_sg_assunto = a.cd_pauta_sg_assunto) AS tl_arquivo,
                   (CASE WHEN a.dt_retirada_pauta IS NOT NULL 
                         THEN 'R'
                         ELSE 'N'
                   END) AS fl_removido,
                   (CASE WHEN a.fl_resolucao_diretoria = 'S' 
                         THEN 'Sim'
                         WHEN a.fl_resolucao_diretoria = 'N' 
                         THEN 'Não'
                         ELSE 'Não'
                   END) AS ds_resolucao_diretoria,
                   COALESCE(a.fl_aplica_rds, 'N') AS fl_aplica_rds,
                   COALESCE(a.fl_rds_restrita, 'N') AS fl_rds_restrita,
                   (CASE WHEN a.fl_pendencia_gestao = 'S'
                         THEN 'Sim'
                         ELSE 'Não'
                   END) AS fl_pendencia_gestao,

                   (CASE WHEN a.fl_ordem_fornecimento = 'S' 
                         THEN 'Sim'
                         WHEN a.fl_ordem_fornecimento = 'N' 
                         THEN 'Não'
                         ELSE 'Não'
                   END) AS fl_ordem_fornecimento,
                   a.nr_mes_pautar,
                   a.nr_ano_pautar,
                   a.fl_proxima_reuniao,
                   a.tp_colegiado_pautar,
                   a.fl_aprovado,
                   (CASE WHEN a.fl_aprovado = 'S'
                        THEN 'Aprovado'
                        ELSE ''
                   END) AS ds_aprovado,
                   a.nr_ordem_fornecimento
              FROM gestao.pauta_sg_assunto a
              LEFT JOIN projetos.diretoria d
                ON d.cd_diretoria = a.cd_diretoria
             WHERE a.cd_pauta_sg = ".intval($cd_pauta_sg)."
               AND a.dt_exclusao IS NULL
               ".(trim($fl_enviado) == 'N' ? "AND dt_envio_responsavel IS NULL" : "")."
               ".(trim($fl_removido) == 'S' ? "AND dt_retirada_pauta IS NULL" : "")."
             ORDER BY a.nr_item_sumula, a.ds_pauta_sg_assunto;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function assunto_carrega($cd_pauta_sg_assunto)
    {
        $qr_sql = "
            SELECT a.cd_pauta_sg_assunto,
                   a.nr_item_sumula,
                   a.ds_pauta_sg_assunto,
                   a.nr_tempo,
                   a.cd_gerencia_responsavel,
                   a.cd_usuario_responsavel,
                   a.cd_gerencia_substituto,
                   a.cd_usuario_substituto,
                   a.cd_diretoria,
                   a.instancia_aprovacao,
                   a.ds_decisao,
                   funcoes.get_usuario_nome(a.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(a.cd_usuario_substituto) AS ds_usuario_substituto,
                   a.fl_resolucao_diretoria,
                   a.cd_pauta_sg_objetivo,
                   a.cd_pauta_sg_justificativa,
                   a.ds_detalhamento,
                   a.ds_historico,
                   a.ds_recomendacao,
                   a.ds_situacao,
                   a.nr_rds,
                   a.nr_ano_rds,
                   a.fl_ordem_fornecimento,
                   a.nr_ordem_fornecimento, 
                   po.ds_pauta_sg_objetivo,
                   pj.ds_pauta_sg_justificativa,
                   po.fl_anexo_obrigatorio,
                   gestao.nr_controle_rds(nr_ano_rds, nr_rds) AS nr_ano_numero_rds,
                   TO_CHAR(a.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
                   d.ds_diretoria,
                   (SELECT COUNT(*)
                      FROM gestao.pauta_sg_assunto_anexo an
                     WHERE an.dt_exclusao IS NULL
                       AND an.cd_pauta_sg_assunto = a.cd_pauta_sg_assunto) AS tl_arquivo,
                   (CASE WHEN a.nr_tempo IS NULL 
                         THEN 'N'
                         WHEN a.cd_pauta_sg_objetivo IS NULL 
                         THEN 'N'
                         WHEN a.cd_pauta_sg_justificativa IS NULL
                         THEN 'N'
                         ELSE 'S'
                   END) AS fl_encerrar,
                   COALESCE(a.fl_aplica_detalhamento, 'N') AS fl_aplica_detalhamento,
                   COALESCE(a.fl_aplica_historico, 'N') AS fl_aplica_historico,
                   COALESCE(a.fl_aplica_situacao, 'N') AS fl_aplica_situacao,
                   COALESCE(a.fl_aplica_recomendacao, 'N') AS fl_aplica_recomendacao,
                   COALESCE(a.fl_aplica_rds, 'N') AS fl_aplica_rds,
                   COALESCE(a.fl_rds_restrita, 'N') AS fl_rds_restrita,
                   a.fl_pendencia_gestao,
                   a.fl_pautar_reuniao,
                   a.fl_proxima_reuniao,
                   a.nr_mes_pautar,
                   a.nr_ano_pautar,
                   01||'/'||a.nr_mes_pautar||'/'||a.nr_ano_pautar AS dt_pautar,
                   a.tp_colegiado_pautar,
                   a.nr_ordem_fornecimento,
                   cd_gerencia_pendencia,
                   cd_usuario_pendencia
              FROM gestao.pauta_sg_assunto a
              LEFT JOIN gestao.pauta_sg_objetivo po
                ON po.cd_pauta_sg_objetivo = a.cd_pauta_sg_objetivo
              LEFT JOIN gestao.pauta_sg_justificativa pj
                ON pj.cd_pauta_sg_justificativa = a.cd_pauta_sg_justificativa
              LEFT JOIN projetos.diretoria d
                ON d.cd_diretoria = a.cd_diretoria 
             WHERE a.cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_usuarios($cd_divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

    public function set_ordem($cd_pauta_sg_assunto, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET nr_item_sumula       = ".(trim($args['nr_item_sumula']) != '' ? intval($args['nr_item_sumula']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP, 
                   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function set_resolucao_diretoria($cd_pauta_sg_assunto, $cd_usuario, $fl_resolucao_diretoria = '')
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET fl_resolucao_diretoria = ".(trim($fl_resolucao_diretoria) != '' ? "'".trim($fl_resolucao_diretoria)."'" : "DEFAULT").",
                   cd_usuario_alteracao   = ".intval($cd_usuario).",
                   dt_alteracao           = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function set_aprovar_assunto($cd_pauta_sg_assunto, $cd_usuario, $fl_aprovado = '')
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET fl_aprovado          = ".(trim($fl_aprovado) != '' ? "'".trim($fl_aprovado)."'" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }


    public function enviar($cd_pauta_sg, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg
               SET cd_usuario_envio_responsavel = ".intval($cd_usuario).",
                   cd_usuario_alteracao         = ".intval($cd_usuario).",
                   dt_alteracao                 = CURRENT_TIMESTAMP,
                   dt_envio_responsavel         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg).";

            UPDATE gestao.pauta_sg_assunto 
               SET cd_usuario_envio_responsavel = ".intval($cd_usuario).",
                   cd_usuario_alteracao         = ".intval($cd_usuario).",
                   dt_alteracao                 = CURRENT_TIMESTAMP,
                   dt_envio_responsavel         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg)."
               AND dt_envio_responsavel IS NULL;";

        $this->db->query($qr_sql);
    }

    public function listar_assuntos_pautar($cd_pauta_sg)
    {
        $qr_sql = "
            SELECT psa.cd_pauta_sg_assunto,
                   (SELECT psap.cd_pauta_sg_assunto_pautar
                      FROM gestao.pauta_sg_assunto_pautar psap
                     WHERE psap.cd_pauta_sg_assunto = psa.cd_pauta_sg_assunto)
              FROM gestao.pauta_sg_assunto psa
             WHERE psa.dt_exclusao IS NULL
               AND psa.cd_pauta_sg = ".intval($cd_pauta_sg)."
               AND psa.fl_pautar_reuniao = 'S';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_assuntos_pautar($args = array())
    {
        if(intval($args['cd_pauta_sg_assunto_pautar']) > 0)
        {
            $qr_sql = "
                UPDATE gestao.pauta_sg_assunto_pautar
                   SET cd_usuario_exclusao = NULL,
                       dt_exclusao         = NULL
                 WHERE cd_pauta_sg_assunto_pautar = ".intval($args['cd_pauta_sg_assunto_pautar']).";";
        }
        else
        {
            $qr_sql = "
                INSERT INTO gestao.pauta_sg_assunto_pautar
                    (
                       cd_pauta_sg_assunto,
                       cd_usuario_inclusao,
                       cd_usuario_alteracao
                    )
                VALUES
                    (
                       ".intval($args['cd_pauta_sg_assunto']).",
                       ".intval($args['cd_usuario']).",
                       ".intval($args['cd_usuario'])."
                    );";
        }

        $this->db->query($qr_sql);
    }

    public function atualizar_assuntos_pautar($args = array())
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto_pautar
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($args['cd_pauta_sg_assunto'])."
               AND dt_exclusao IS NULL;";

        $this->db->query($qr_sql);
    }

    public function get_assuntos_pautar_proxima_reuniao($tp_colegiado)
    {
        $qr_sql = "
            SELECT psap.cd_pauta_sg_assunto_pautar,
                   psap.cd_pauta_sg_assunto,
                   psa.cd_pauta_sg,
                   psa.nr_item_sumula,
                   psa.ds_pauta_sg_assunto,
                   psa.nr_tempo,
                   psa.nr_rds,
                   psa.nr_ano_rds,
                   psa.fl_aplica_rds,
                   psa.instancia_aprovacao,
                   psa.cd_gerencia_responsavel,
                   psa.cd_usuario_responsavel,
                   psa.cd_gerencia_substituto,
                   psa.cd_usuario_substituto,
                   psa.cd_diretoria,
                   psa.cd_pauta_sg_objetivo,
                   psa.cd_pauta_sg_justificativa,
                   psa.fl_ordem_fornecimento,
                   psa.nr_ordem_fornecimento
              FROM gestao.pauta_sg_assunto_pautar psap
              JOIN gestao.pauta_sg_assunto psa
                ON psa.cd_pauta_sg_assunto = psap.cd_pauta_sg_assunto
             WHERE psap.dt_pauta_incluido IS NULL
               AND psap.dt_exclusao       IS NULL
               AND psa.fl_pautar_reuniao      = 'S'
               AND psa.fl_proxima_reuniao     = 'S'
               AND psa.tp_colegiado_pautar    = '".trim($tp_colegiado)."';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_assuntos_pautar_proxima_reuniao_mes($nr_mes, $nr_ano, $tp_colegiado)
    {
        $qr_sql = "
            SELECT psap.cd_pauta_sg_assunto_pautar,
                   psap.cd_pauta_sg_assunto,
                   psa.cd_pauta_sg,
                   psa.nr_item_sumula,
                   psa.ds_pauta_sg_assunto,
                   psa.nr_tempo,
                   psa.nr_rds,
                   psa.nr_ano_rds,
                   psa.fl_aplica_rds,
                   psa.instancia_aprovacao,
                   psa.cd_gerencia_responsavel,
                   psa.cd_usuario_responsavel,
                   psa.cd_gerencia_substituto,
                   psa.cd_usuario_substituto,
                   psa.cd_diretoria,
                   psa.cd_pauta_sg_objetivo,
                   psa.cd_pauta_sg_justificativa,
                   psa.fl_ordem_fornecimento,
                   psa.nr_ordem_fornecimento
              FROM gestao.pauta_sg_assunto_pautar psap
              JOIN gestao.pauta_sg_assunto psa
                ON psa.cd_pauta_sg_assunto = psap.cd_pauta_sg_assunto
             WHERE psap.dt_pauta_incluido IS NULL
               AND psap.dt_exclusao       IS NULL
               AND psa.fl_pautar_reuniao      = 'S'
               AND psa.fl_proxima_reuniao     = 'N'
               AND psa.nr_mes_pautar          = ".intval($nr_mes)."
               AND psa.nr_ano_pautar          = ".intval($nr_ano)."
               AND psa.tp_colegiado_pautar    = '".trim($tp_colegiado)."';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_assunto_pautar_incluido($cd_pauta_sg_assunto, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto_pautar
               SET cd_usuario_pauta_incluido = ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT").",
                   dt_pauta_incluido         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function listar_assuntos_pendencia_gestao($cd_pauta_sg)
    {
        $qr_sql = "
            SELECT psa.cd_pauta_sg_assunto,
                   psa.cd_pauta_sg,
                   psa.ds_pauta_sg_assunto,
                   psa.cd_diretoria,
                   psa.cd_gerencia_pendencia,
                   psa.cd_usuario_pendencia,
                   psa.nr_item_sumula,
                   TO_CHAR(ps.dt_pauta_sg, 'DD/MM/YYYY') AS dt_pauta_sg,
                   ps.nr_pauta_sg,
                   ps.fl_sumula,
                   (CASE WHEN ps.fl_sumula = 'DE'
                         THEN 11
                         WHEN ps.fl_sumula = 'CD'
                         THEN 12
                         WHEN ps.fl_sumula = 'CF'
                         THEN 13
                   END) AS cd_reuniao_sistema_gestao_tipo,
                   (SELECT COUNT(*)
                      FROM gestao.pendencia_gestao pg
                     WHERE pg.cd_pauta_sg_assunto = psa.cd_pauta_sg_assunto
                       AND pg.dt_exclusao         IS NULL) AS cd_pauta_sg_assunto_pendencia
              FROM gestao.pauta_sg_assunto psa
              JOIN gestao.pauta_sg ps
                ON ps.cd_pauta_sg = psa.cd_pauta_sg
               AND ps.dt_exclusao IS NULL
             WHERE psa.dt_exclusao         IS NULL
               AND psa.cd_pauta_sg         = ".intval($cd_pauta_sg)."
               AND psa.fl_pendencia_gestao = 'S';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_assuntos_pendencia_gestao($args = array())
    {
        if(intval($args['cd_pauta_sg_assunto_pendencia']) == 0)
        {
            $cd_pendencia_gestao = $this->db->get_new_id('gestao.pendencia_gestao', 'cd_pendencia_gestao');

            $qr_sql = "
                INSERT INTO gestao.pendencia_gestao
                    (
                       cd_pendencia_gestao,
                       cd_pauta_sg_assunto,
                       cd_reuniao_sistema_gestao_tipo,
                       cd_superior,
                       dt_reuniao,
                       ds_item,
                       cd_usuario_responsavel,
                       cd_usuario_inclusao,
                       cd_usuario_alteracao
                    )
                VALUES
                    (
                       ".intval($cd_pendencia_gestao).",
                       ".(intval($args['cd_pauta_sg_assunto']) > 0 ? intval($args['cd_pauta_sg_assunto']) : "DEFAULT").",
                       ".(intval($args['cd_reuniao_sistema_gestao_tipo']) > 0 ? intval($args['cd_reuniao_sistema_gestao_tipo']) : "DEFAULT").",
                       ".(trim($args['cd_superior']) != '' ? str_escape($args['cd_superior']) : "DEFAULT").",
                       ".(trim($args['dt_reuniao']) != '' ? "TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY')" : "DEFAULT").",
                       ".(trim($args['ds_item']) != '' ? str_escape($args['ds_item']) : "DEFAULT").",
                       ".(intval($args['cd_usuario_pendencia']) > 0 ? intval($args['cd_usuario_pendencia']) : "DEFAULT").",
                       ".intval($args['cd_usuario']).",
                       ".intval($args['cd_usuario'])."
                    );";
    
            $qr_sql .= "
                INSERT INTO gestao.pendencia_gestao_gerencia
                    (
                        cd_pendencia_gestao, 
                        cd_gerencia, 
                        cd_usuario_inclusao
                    )
                VALUES
                    (
                        ".intval($cd_pendencia_gestao).",
                        '".(trim($args['cd_gerencia_pendencia']) != '' ? trim($args['cd_gerencia_pendencia']) : "DEFAULT")."',
                        ".intval($args['cd_usuario'])."
                    );";
    
            $this->db->query($qr_sql);
    
            return $cd_pendencia_gestao;
        }
    }
    
    public function salvar_assuntos_pendencia_gestao_acompanhamento($cd_pendencia_gestao, $args = array())
    {
        if(intval($args['cd_pauta_sg_assunto_pendencia']) == 0)
        {
            $qr_sql = "
                INSERT INTO gestao.pendencia_gestao_acompanhamento
                    (
                        cd_pendencia_gestao,
                        ds_pendencia_gestao_acompanhamento,
                        cd_usuario_inclusao,
                        cd_usuario_alteracao
                    )
                VALUES
                    (
                        ".intval($cd_pendencia_gestao).",
                        ".(trim($args['ds_pendencia_gestao_acompanhamento']) != '' ? str_escape($args['ds_pendencia_gestao_acompanhamento']) : "DEFAULT").",
                        ".intval($args['cd_usuario']).",
                        ".intval($args['cd_usuario'])."
                    );";

            $this->db->query($qr_sql);
        }
    }

    public function encerrar($cd_pauta_sg, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg
               SET cd_usuario_aprovacao = ".intval($cd_usuario).",
                   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP,
                   dt_aprovacao         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg).";";

        $this->db->query($qr_sql);
    }

    public function reabrir($cd_pauta_sg, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   cd_usuario_aprovacao = NULL,
                   dt_alteracao         = CURRENT_TIMESTAMP,
                   dt_aprovacao         = NULL
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg).";";

        $this->db->query($qr_sql);
    }

    public function enviar_colegiado($cd_pauta_sg, $cd_usuario)
    {
    	$qr_sql = "
            UPDATE gestao.pauta_sg
               SET cd_usuario_envio_colegiado = ".intval($cd_usuario).",
                   cd_usuario_alteracao       = ".intval($cd_usuario).",
                   dt_alteracao               = CURRENT_TIMESTAMP,
                   dt_envio_colegiado         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg = ".intval($cd_pauta_sg).";";

        $this->db->query($qr_sql);
    }

    public function anexo_listar($cd_pauta_sg_assunto, $fl_rds = '', $fl_ordem_fornecimento = '', $fl_quadro_comparativo = '')
    {
        $qr_sql = "
            SELECT a.cd_pauta_sg_assunto_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario,
                   a.nr_ano_rds,
                   a.nr_rds,
                   gestao.nr_controle_rds(a.nr_ano_rds, a.nr_rds) AS nr_ano_numero_rds,
                   a.fl_rds,
                   a.fl_quadro_comparativo,
                   a.fl_ordem_fornecimento
              FROM gestao.pauta_sg_assunto_anexo a
             WHERE a.cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto)."
               AND a.dt_exclusao IS NULL
               ".(trim($fl_rds) == 'N' ? "AND a.fl_rds = 'N'" : "")."
               ".(trim($fl_rds) == 'S' ? "AND a.fl_rds = 'S'" : "")."
               ".(trim($fl_ordem_fornecimento) == 'N' ? "AND a.fl_ordem_fornecimento = 'N'" : "")."
               ".(trim($fl_ordem_fornecimento) == 'S' ? "AND a.fl_ordem_fornecimento = 'S'" : "")."
               ".(trim($fl_quadro_comparativo) == 'N' ? "AND a.fl_quadro_comparativo = 'N'" : "")."
               ".(trim($fl_quadro_comparativo) == 'S' ? "AND a.fl_quadro_comparativo = 'S'" : "")."

             ORDER BY a.dt_inclusao DESC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function anexo_salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.pauta_sg_assunto_anexo
                 (
                    cd_pauta_sg_assunto,
                    arquivo,
                    arquivo_nome,
                    fl_rds,
                    nr_ano_rds,
                    nr_rds,
                    fl_quadro_comparativo,
                    fl_ordem_fornecimento,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($args['cd_pauta_sg_assunto']).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".(trim($args['fl_rds']) != '' ? "'".trim($args['fl_rds'])."'" : "DEFAULT").",
                    ".(trim($args['nr_ano_rds']) != '' ? intval($args['nr_ano_rds']) : "DEFAULT").",
                    ".(trim($args['nr_rds']) != '' ? intval($args['nr_rds']) : "DEFAULT").",
                    ".(trim($args['fl_quadro_comparativo']) != '' ? "'".trim($args['fl_quadro_comparativo'])."'" : "DEFAULT").",
                    ".(trim($args['fl_ordem_fornecimento']) != '' ? "'".trim($args['fl_ordem_fornecimento'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function anexo_atualizar($cd_pauta_sg_assunto_anexo, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto_anexo
               SET arquivo                = '".trim($args['arquivo'])."',
                   arquivo_nome           = '".trim($args['arquivo_nome'])."',
                   fl_rds                 = ".(trim($args['fl_rds']) != '' ? "'".trim($args['fl_rds'])."'" : "DEFAULT").",
                   fl_ordem_fornecimento  = ".(trim($args['fl_ordem_fornecimento']) != '' ? "'".trim($args['fl_ordem_fornecimento'])."'" : "DEFAULT").",
                   fl_quadro_comparativo  = ".(trim($args['fl_quadro_comparativo']) != '' ? "'".trim($args['fl_quadro_comparativo'])."'" : "DEFAULT").",
                   nr_ano_rds             = ".(trim($args['nr_ano_rds']) != '' ? intval($args['nr_ano_rds']) : "DEFAULT").",
                   nr_rds                 = ".(trim($args['nr_rds']) != '' ? intval($args['nr_rds']) : "DEFAULT").",
                   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
                   dt_alteracao           = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto_anexo = ".intval($cd_pauta_sg_assunto_anexo).";";

        $this->db->query($qr_sql);
    }  


    public function anexo_carrega($cd_pauta_sg_assunto_anexo)
    {
        $qr_sql = "
            SELECT a.arquivo,
                   a.arquivo_nome
              FROM gestao.pauta_sg_assunto_anexo a
             WHERE a.cd_pauta_sg_assunto_anexo = ".intval($cd_pauta_sg_assunto_anexo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function anexo_excluir($cd_pauta_sg_assunto_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto_anexo = ".intval($cd_pauta_sg_assunto_anexo).";";
        
        $this->db->query($qr_sql);
    }

    public function assunto_remover($cd_pauta_sg_assunto, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET cd_usuario_retirada_pauta = ".intval($cd_usuario).",
                   dt_retirada_pauta         = CURRENT_TIMESTAMP, 
                   ds_decisao                = NULL
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function reabrir_assunto($cd_pauta_sg_assunto, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET cd_usuario_alteracao    = ".intval($cd_usuario).",
                   cd_usuario_encerramento = NULL,
                   dt_alteracao            = CURRENT_TIMESTAMP,
                   dt_encerramento         = NULL
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function assunto_excluir($cd_pauta_sg_assunto, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function pesquisa_listar($args = array())
    {
        $qr_sql = "
            SELECT p.cd_pauta_sg,
                   p.nr_ata,
                   a.nr_item_sumula,
                   gestao.nr_pauta_sg(p.nr_ano, p.nr_pauta_sg) AS ano_numero,
                   p.local,
                   TO_CHAR(p.dt_pauta_sg, 'DD/MM/YYYY HH24:MI') AS dt_pauta,
                   TO_CHAR(p.dt_pauta_sg_fim, 'DD/MM/YYYY HH24:MI') AS dt_pauta_sg_fim,
                   TO_CHAR(p.dt_aprovacao, 'DD/MM/YYYY HH24:MI') AS dt_aprovacao,
                   p.fl_sumula,
                   a.cd_pauta_sg_assunto,
                   a.ds_pauta_sg_assunto,
                   a.nr_tempo,
                   a.cd_gerencia_responsavel,
                   a.cd_usuario_responsavel,
                   a.cd_gerencia_substituto,
                   a.cd_usuario_substituto,
                   a.cd_diretoria,
                   a.ds_decisao,
                   funcoes.get_usuario_nome(p.cd_usuario_aprovacao) AS ds_usuario_aprovacao,
                   funcoes.get_usuario_nome(a.cd_usuario_responsavel) AS ds_usuario_responsavel,
                   funcoes.get_usuario_nome(a.cd_usuario_substituto) AS ds_usuario_substituto,
                   d.ds_diretoria,
                   (CASE WHEN a.dt_retirada_pauta IS NOT NULL 
                        THEN 'R'
                        ELSE 'N'
                   END) AS fl_removido,
                   (CASE WHEN p.fl_sumula = 'DE' 
                         THEN 'label label-success'
                         WHEN p.fl_sumula = 'CF' 
                         THEN 'label label-warning'
                         WHEN p.fl_sumula = 'IN' 
                         THEN 'label label-inverse'
                         ELSE 'label label-info'
                   END) AS class_sumula,
                   (CASE WHEN p.fl_tipo_reuniao = 'E' 
                         THEN 'Extraordinária'
                         WHEN p.fl_tipo_reuniao = 'O' 
                         THEN 'Ordinária'
                         ELSE ''
                   END) AS ds_tipo_reuniao
              FROM gestao.pauta_sg_assunto a
              JOIN gestao.pauta_sg p
                ON p.cd_pauta_sg = a.cd_pauta_sg
              LEFT JOIN projetos.diretoria d
                ON d.cd_diretoria = a.cd_diretoria
             WHERE a.dt_exclusao IS NULL
               AND p.dt_exclusao IS NULL
               AND a.cd_pauta_sg_assunto NOT IN (SELECT a2.cd_pauta_sg_assunto_referencia
                                                   FROM gestao.pauta_sg_assunto a2
                                                  WHERE a2.dt_exclusao IS NULL
                                                    AND a2.cd_pauta_sg_assunto_referencia IS NOT NULL)
               ".(trim($args['nr_ata']) != '' ? "AND p.nr_ata = ".intval($args['nr_ata']) : "")."
               ".(trim($args['fl_sumula']) != '' ? "AND p.fl_sumula = '".trim($args['fl_sumula'])."'" : "")."
               ".(trim($args['fl_tipo_reuniao']) != '' ? "AND p.fl_tipo_reuniao = '".trim($args['fl_tipo_reuniao'])."'" : "")."
               ".(((trim($args['dt_pauta_sg_ini']) != '') AND (trim($args['dt_pauta_sg_fim']) != '')) ? " AND DATE_TRUNC('day', p.dt_pauta_sg) BETWEEN TO_DATE('".$args['dt_pauta_sg_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_sg_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_pauta_sg_fim_ini']) != '') AND (trim($args['dt_pauta_sg_fim_fim']) != '')) ? " AND DATE_TRUNC('day', p.dt_pauta_sg_fim) BETWEEN TO_DATE('".$args['dt_pauta_sg_fim_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_sg_fim_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['ds_pauta_sg_assunto']) != '' ? "AND UPPER(funcoes.remove_acento(a.ds_pauta_sg_assunto)) LIKE UPPER(funcoes.remove_acento('%".trim($args['ds_pauta_sg_assunto'])."%'))" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function minhas_listar($cd_usuario, $args = array())
    {
    	$qr_sql = "
    		SELECT p.cd_pauta_sg,
				   psa.cd_pauta_sg_assunto,
				   p.nr_ata,
				   p.fl_sumula,
                   p.local,
                   psa.ds_pauta_sg_assunto,
                   psa.nr_tempo,
                   TO_CHAR(p.dt_pauta_sg, 'DD/MM/YYYY HH24:MI') AS dt_pauta,
                   TO_CHAR(p.dt_pauta_sg_fim, 'DD/MM/YYYY HH24:MI') AS dt_pauta_sg_fim,
                   TO_CHAR(p.dt_limite, 'DD/MM/YYYY') AS dt_limite,
				   (CASE WHEN p.fl_sumula = 'DE' 
                         THEN 'label label-success'
						 WHEN p.fl_sumula = 'CF' 
                         THEN 'label label-warning'
						 WHEN p.fl_sumula = 'IN' 
                         THEN 'label label-inverse'
						 ELSE 'label label-info'
				   END) AS class_sumula,
                   (CASE WHEN p.fl_tipo_reuniao = 'E' 
                         THEN 'Extraordinária'
                         WHEN p.fl_tipo_reuniao = 'O' 
                         THEN 'Ordinária'
                         ELSE ''
                   END) AS ds_tipo_reuniao,
				   (SELECT COUNT(*)
					  FROM gestao.pauta_sg_assunto_anexo pssa
                     WHERE pssa.dt_exclusao IS NULL
                       AND pssa.cd_pauta_sg_assunto = psa.cd_pauta_sg_assunto) AS tl_arquivo,
                   (CASE WHEN p.dt_limite < CURRENT_DATE 
                         THEN 'label label-important'
                         ELSE 'label label-warning'
                   END) AS ds_class_limite
			  FROM gestao.pauta_sg p
			  JOIN gestao.pauta_sg_assunto psa
			    ON psa.cd_pauta_sg = p.cd_pauta_sg
		     WHERE p.dt_exclusao          IS NULL
               AND psa.dt_exclusao        IS NULL
               AND psa.dt_retirada_pauta  IS NULL
			   AND p.dt_aprovacao         IS NULL
			   AND p.dt_envio_responsavel IS NOT NULL
			   AND psa.dt_encerramento    IS NULL
			   AND (cd_usuario_responsavel = ".intval($cd_usuario)." OR cd_usuario_substituto = ".intval($cd_usuario).")
			   ".(trim($args['nr_ata']) != '' ? "AND p.nr_ata = ".intval($args['nr_ata']) : "")."   
			   ".(((trim($args['dt_pauta_sg_ini']) != '') AND (trim($args['dt_pauta_sg_fim']) != '')) ? " AND DATE_TRUNC('day', p.dt_pauta_sg) BETWEEN TO_DATE('".$args['dt_pauta_sg_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_sg_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_tipo_reuniao']) != '' ? "AND p.fl_tipo_reuniao = '".trim($args['fl_tipo_reuniao'])."'" : "")."
			   ".(trim($args['fl_sumula']) != '' ? "AND p.fl_sumula = '".trim($args['fl_sumula'])."'" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_objetivo()
	{
		$qr_sql = "
			SELECT cd_pauta_sg_objetivo AS value,
			       ds_pauta_sg_objetivo AS text
			  FROM gestao.pauta_sg_objetivo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_pauta_sg_objetivo";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_justificativa()
	{
		$qr_sql = "
			SELECT cd_pauta_sg_justificativa AS value,
			       ds_pauta_sg_justificativa AS text
			  FROM gestao.pauta_sg_justificativa
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_pauta_sg_justificativa";

		return $this->db->query($qr_sql)->result_array();
	}

	public function responder_salvar($cd_pauta_sg_assunto, $args = array())
	{
		 $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET nr_tempo                  = ".(trim($args['nr_tempo']) != '' ? intval($args['nr_tempo']) : "DEFAULT").",
                   cd_pauta_sg_objetivo      = ".(trim($args['cd_pauta_sg_objetivo']) != '' ? intval($args['cd_pauta_sg_objetivo']) : "DEFAULT").",
                   cd_pauta_sg_justificativa = ".(trim($args['cd_pauta_sg_justificativa']) != '' ? intval($args['cd_pauta_sg_justificativa']) : "DEFAULT").",
                   fl_aplica_detalhamento    = ".(trim($args['fl_aplica_detalhamento']) != '' ? "'".trim($args['fl_aplica_detalhamento'])."'" : "DEFAULT").",
                   ds_detalhamento           = ".(trim($args['ds_detalhamento']) != '' ? str_escape($args['ds_detalhamento']) : "DEFAULT").",
                   fl_aplica_historico       = ".(trim($args['fl_aplica_historico']) != '' ? "'".trim($args['fl_aplica_historico'])."'" : "DEFAULT").",
                   ds_historico              = ".(trim($args['ds_historico']) != '' ? str_escape($args['ds_historico']) : "DEFAULT").",
                   fl_aplica_recomendacao    = ".(trim($args['fl_aplica_recomendacao']) != '' ? "'".trim($args['fl_aplica_recomendacao'])."'" : "DEFAULT").",
                   ds_recomendacao           = ".(trim($args['ds_recomendacao']) != '' ? str_escape($args['ds_recomendacao']) : "DEFAULT").",
                   fl_aplica_situacao        = ".(trim($args['fl_aplica_situacao']) != '' ? "'".trim($args['fl_aplica_situacao'])."'" : "DEFAULT").",
                   ds_situacao               = ".(trim($args['ds_situacao']) != '' ? str_escape($args['ds_situacao']) : "DEFAULT").",
                   fl_aplica_rds             = ".(trim($args['fl_aplica_rds']) != '' ? "'".trim($args['fl_aplica_rds'])."'" : "DEFAULT").",
                   fl_rds_restrita           = ".(trim($args['fl_rds_restrita']) != '' ? "'".trim($args['fl_rds_restrita'])."'" : "DEFAULT").",
                   nr_ordem_fornecimento     = ".(trim($args['nr_ordem_fornecimento']) != '' ? intval($args['nr_ordem_fornecimento']) : "DEFAULT").",
                   cd_usuario_alteracao      = ".intval($args['cd_usuario']).",
                   dt_alteracao              = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
	}

	public function encerrar_assunto($cd_pauta_sg_assunto, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.pauta_sg_assunto
               SET cd_usuario_encerramento = ".intval($cd_usuario).",
                   cd_usuario_alteracao    = ".intval($cd_usuario).",
                   dt_alteracao            = CURRENT_TIMESTAMP,
                   dt_encerramento         = CURRENT_TIMESTAMP
             WHERE cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        $this->db->query($qr_sql);
    }

    public function gerencia_anexo_listar($args = array())
    {
    	$qr_sql = "
    		SELECT p.cd_pauta_sg,
				   psa.cd_pauta_sg_assunto,
				   p.nr_ata,
				   p.fl_sumula,
                   p.local,
                   psa.ds_pauta_sg_assunto,
                   psa.nr_tempo,
                   TO_CHAR(p.dt_pauta_sg, 'DD/MM/YYYY HH24:MI') AS dt_pauta,
                   TO_CHAR(p.dt_pauta_sg_fim, 'DD/MM/YYYY HH24:MI') AS dt_pauta_sg_fim,
				   (CASE WHEN p.fl_sumula = 'DE' 
                         THEN 'label label-success'
						 WHEN p.fl_sumula = 'CF' 
                         THEN 'label label-warning'
						 WHEN p.fl_sumula = 'IN' 
                         THEN 'label label-inverse'
						 ELSE 'label label-info'
				   END) AS class_sumula,
                   (CASE WHEN p.fl_tipo_reuniao = 'E' 
                         THEN 'Extraordinária'
                         WHEN p.fl_tipo_reuniao = 'O' 
                         THEN 'Ordinária'
                         ELSE ''
                   END) AS ds_tipo_reuniao,
				   (SELECT COUNT(*)
					  FROM gestao.pauta_sg_assunto_anexo pssa
                     WHERE pssa.dt_exclusao IS NULL
                       AND pssa.cd_pauta_sg_assunto = psa.cd_pauta_sg_assunto) AS tl_arquivo
			  FROM gestao.pauta_sg p
			  JOIN gestao.pauta_sg_assunto psa
			    ON psa.cd_pauta_sg = p.cd_pauta_sg
		     WHERE p.dt_exclusao IS NULL
               AND psa.dt_exclusao IS NULL
               AND psa.dt_retirada_pauta IS NULL
			   AND p.dt_aprovacao IS NULL
			   ".(trim($args['nr_ata']) != '' ? "AND p.nr_ata = ".intval($args['nr_ata']) : "")."   
			   ".(((trim($args['dt_pauta_sg_ini']) != '') AND (trim($args['dt_pauta_sg_fim']) != '')) ? " AND DATE_TRUNC('day', p.dt_pauta_sg) BETWEEN TO_DATE('".$args['dt_pauta_sg_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_pauta_sg_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_tipo_reuniao']) != '' ? "AND p.fl_tipo_reuniao = '".trim($args['fl_tipo_reuniao'])."'" : "")."
			   ".(trim($args['fl_sumula']) != '' ? "AND p.fl_sumula = '".trim($args['fl_sumula'])."'" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_controle_rds($cd_pauta_sg_assunto)
    {
        $qr_sql = "
            SELECT *
              FROM gestao.controle_rds
             WHERE dt_exclusao IS NULL
               AND cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function controle_rds_salvar($args = array())
    {
        $cd_controle_rds = intval($this->db->get_new_id('gestao.controle_rds', 'cd_controle_rds'));
        
        $qr_sql = "
            INSERT INTO gestao.controle_rds
                 (
                   cd_controle_rds,
                   cd_pauta_sg_assunto,
                   ds_controle_rds, 
                   nr_ata,
                   nr_ano,
                   nr_rds,
                   dt_reuniao,
                   arquivo, 
                   arquivo_nome,
                   fl_restrito,
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_controle_rds).",
                    ".(trim($args['cd_pauta_sg_assunto']) != '' ? intval($args['cd_pauta_sg_assunto']) : "DEFAULT").",
                    ".(trim($args['ds_controle_rds']) != '' ? str_escape($args['ds_controle_rds']) : "DEFAULT").",
                    ".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
                    ".(trim($args['nr_ano_rds']) != '' ? intval($args['nr_ano_rds']) : "DEFAULT").",
                    ".(trim($args['nr_rds']) != '' ? intval($args['nr_rds']) : "DEFAULT").",
                    ".(trim($args['dt_reuniao']) != '' ? "TO_DATE('".trim($args['dt_reuniao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                    ".(trim($args['fl_restrito']) != '' ? "'".trim($args['fl_restrito'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
        
        return $cd_controle_rds;
    }

    public function controle_rds_gerencia_salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.controle_rds_area
                 (
                   cd_controle_rds, 
                   cd_area, 
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                   ".intval($args['cd_controle_rds']).",
                   '".trim($args['cd_gerencia'])."',
                   ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    function assunto_sumula_de(&$result, $args=array())
    {
      $qr_sql = "
        INSERT INTO gestao.sumula_item
             (
               cd_sumula, 
               nr_sumula_item, 
               descricao, 
               cd_diretoria,
               cd_gerencia, 
               cd_responsavel, 
               cd_substituto, 
               cd_pauta_sg_assunto,
               cd_usuario_inclusao
             )

        SELECT ".intval($args['cd_sumula']).",
               a.nr_item_sumula,
               a.ds_pauta_sg_assunto || ': ' || a.ds_decisao,
               a.cd_diretoria,
               a.cd_gerencia_responsavel,
               a.cd_usuario_responsavel,
               a.cd_usuario_substituto,
               a.cd_pauta_sg_assunto,
               ".intval($args['cd_usuario'])."
          FROM gestao.pauta_sg_assunto a
          JOIN gestao.pauta_sg p
            ON p.cd_pauta_sg = a.cd_pauta_sg
         WHERE p.nr_ata      = ".intval($args["nr_sumula"])."
           AND p.fl_sumula   = 'DE'
           AND p.dt_exclusao IS NULL
           AND p.cd_usuario_aprovacao IS NOT NULL
           AND a.dt_exclusao IS NULL
           AND a.dt_retirada_pauta IS NULL
           AND a.ds_decisao IS NOT NULL;";

      $result = $this->db->query($qr_sql);
    }

    function assunto_sumula_cd(&$result, $args=array())
    {
      $qr_sql = "
        INSERT INTO gestao.sumula_conselho_item
             (
               cd_sumula_conselho, 
               nr_sumula_conselho_item, 
               descricao, 
               cd_diretoria,
               cd_gerencia, 
               cd_responsavel, 
               cd_substituto, 
               cd_pauta_sg_assunto,
               cd_usuario_inclusao
             )
        SELECT ".intval($args['cd_sumula_conselho']).",
               a.nr_item_sumula,
               a.ds_pauta_sg_assunto || ': ' || a.ds_decisao,
               a.cd_diretoria,
               a.cd_gerencia_responsavel,
               a.cd_usuario_responsavel,
               a.cd_usuario_substituto,
               a.cd_pauta_sg_assunto,
               ".intval($args['cd_usuario'])."
          FROM gestao.pauta_sg_assunto a
          JOIN gestao.pauta_sg p
            ON p.cd_pauta_sg = a.cd_pauta_sg
         WHERE p.nr_ata      = ".intval($args["nr_sumula_conselho"])."
           AND p.fl_sumula   = 'CD'
           AND p.dt_exclusao IS NULL
           AND p.cd_usuario_aprovacao IS NOT NULL
           AND a.dt_exclusao IS NULL
           AND a.dt_retirada_pauta IS NULL
           AND a.ds_decisao IS NOT NULL;";

      $result = $this->db->query($qr_sql);
    }

    function assunto_sumula_cf(&$result, $args=array())
    {
      $qr_sql = "
        INSERT INTO gestao.sumula_conselho_fiscal_item
             (
               cd_sumula_conselho_fiscal, 
               nr_sumula_conselho_fiscal_item, 
               descricao,
               cd_diretoria,
               cd_gerencia, 
               cd_responsavel, 
               cd_substituto, 
               cd_pauta_sg_assunto,
               cd_usuario_inclusao
             )
        SELECT ".intval($args['cd_sumula_conselho_fiscal']).",
               a.nr_item_sumula,
               a.ds_pauta_sg_assunto || ': ' || a.ds_decisao,
               a.cd_diretoria,
               a.cd_gerencia_responsavel,
               a.cd_usuario_responsavel,
               a.cd_usuario_substituto,
               a.cd_pauta_sg_assunto,
               ".intval($args['cd_usuario'])."
          FROM gestao.pauta_sg_assunto a
          JOIN gestao.pauta_sg p
            ON p.cd_pauta_sg = a.cd_pauta_sg
         WHERE p.nr_ata      = ".intval($args["nr_sumula_conselho_fiscal"])."
           AND p.fl_sumula   = 'CF'
           AND p.dt_exclusao IS NULL
           AND p.cd_usuario_aprovacao IS NOT NULL
           AND a.dt_exclusao IS NULL
           AND a.dt_retirada_pauta IS NULL
           AND a.ds_decisao IS NOT NULL;";

      $result = $this->db->query($qr_sql);
    }
	
    function assunto_sumula_in(&$result, $args=array())
    {
      $qr_sql = "
        INSERT INTO gestao.sumula_interventor_item
             (
               cd_sumula_interventor, 
               nr_sumula_interventor_item, 
               descricao, 
               cd_gerencia, 
               cd_responsavel, 
               cd_substituto, 
               cd_pauta_sg_assunto,
               cd_usuario_inclusao
             )

        SELECT ".intval($args['cd_sumula_interventor']).",
               a.nr_item_sumula,
               a.ds_pauta_sg_assunto || ': ' || a.ds_decisao,
               a.cd_gerencia_responsavel,
               a.cd_usuario_responsavel,
               a.cd_usuario_substituto,
               a.cd_pauta_sg_assunto,
               ".intval($args['cd_usuario'])."
          FROM gestao.pauta_sg_assunto a
          JOIN gestao.pauta_sg p
            ON p.cd_pauta_sg = a.cd_pauta_sg
         WHERE p.nr_ata      = ".intval($args["nr_sumula_interventor"])."
           AND p.fl_sumula   = 'IN'
           AND p.dt_exclusao IS NULL
           AND p.cd_usuario_aprovacao IS NOT NULL
           AND a.dt_exclusao IS NULL
           AND a.dt_retirada_pauta IS NULL
           AND a.ds_decisao IS NOT NULL;";

      $result = $this->db->query($qr_sql);
    }	
}