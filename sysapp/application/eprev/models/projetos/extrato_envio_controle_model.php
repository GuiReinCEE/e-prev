<?php
class Extrato_envio_controle_model extends Model 
{
	function __construct()
    {
    	parent::Model();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT ce.cd_empresa, 
                   ce.cd_plano,
                   ce.nro_extrato,
                   TO_CHAR(ce.dt_liberacao, 'DD/MM/YYYY') AS dt_liberacao,
                   p.sigla AS ds_empresa,
                   pl.descricao AS ds_plano,
                   TO_CHAR(ce.data_base, 'DD/MM/YYYY') AS dt_base,
                   COALESCE((SELECT i.qtd_extratos
                               FROM inconsist_ctrl_extratos i
                              WHERE i.cd_empresa  = ce.cd_empresa
                                AND i.cd_plano    = ce.cd_plano
                                AND i.nro_extrato = ce.nro_extrato
                              ORDER BY i.dt_inconsistencia DESC
                              LIMIT 1), 0) AS qt_extrato,
                   (SELECT COUNT(*) 
                      FROM projetos.extrato_participantes ep
                     WHERE ep.nro_extrato = ce.nro_extrato
                       AND ep.cd_empresa  = ce.cd_empresa
                       AND ep.cd_plano    = ce.cd_plano) AS qt_extrato_participante,
                   (CASE WHEN 
                            COALESCE((SELECT i.qtd_extratos
                                        FROM inconsist_ctrl_extratos i
                                       WHERE i.cd_empresa  = ce.cd_empresa
                                         AND i.cd_plano    = ce.cd_plano
                                         AND i.nro_extrato = ce.nro_extrato
                                       ORDER BY i.dt_inconsistencia DESC
                                       LIMIT 1), 0)
                            =
                            (SELECT COUNT(*) 
                               FROM projetos.extrato_participantes ep
                              WHERE ep.nro_extrato = ce.nro_extrato
                                AND ep.cd_empresa  = ce.cd_empresa
                                AND ep.cd_plano    = ce.cd_plano)
                         THEN 'S'
                         ELSE 'N'

                   END) AS fl_libera_envio
              FROM public.controles_extratos ce
              JOIN public.patrocinadoras p
                ON p.cd_empresa = ce.cd_empresa
              JOIN public.planos pl
                ON pl.cd_plano = ce.cd_plano
             WHERE ce.dt_liberacao IS NOT NULL
               AND (SELECT COUNT(*)
                      FROM projetos.controles_extrato_envio cee
                     WHERE cee.cd_plano   = ce.cd_plano
                       AND cee.cd_empresa = ce.cd_empresa
                       AND cee.nr_extrato = ce.nro_extrato) = 0
               AND TO_CHAR(ce.data_base, 'YYYY')::integer >= 2017
               AND TO_CHAR(ce.data_base, 'MM-YYYY'::text) != '01-2023' 
               AND COALESCE((SELECT i.qtd_extratos
                               FROM inconsist_ctrl_extratos i
                              WHERE i.cd_empresa  = ce.cd_empresa
                                AND i.cd_plano    = ce.cd_plano
                                AND i.nro_extrato = ce.nro_extrato
                              ORDER BY i.dt_inconsistencia DESC
                              LIMIT 1), 0) > 0
               ".(trim($args['cd_plano_empresa']) != '' ? "AND ce.cd_empresa = ".intval($args['cd_plano_empresa']) : "")."
               ".(trim($args['cd_plano']) != '' ? "AND ce.cd_plano = ".intval($args['cd_plano']) : "")."
               ".(intval($args['nro_extrato']) > 0 ? "AND ce.nro_extrato = ".intval($args['nro_extrato']) : "")."
             ORDER BY ce.dt_liberacao DESC, 
                      ce.nro_extrato DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function enviado_listar($args)
    {
        $qr_sql = "
            SELECT cee.cd_plano, 
                   pl.descricao AS ds_plano,
                   cee.cd_empresa, 
                   p.sigla AS ds_empresa,
                   cee.nr_extrato,
                   TO_CHAR(cee.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cee.cd_usuario_inclusao) AS ds_usuario,
                   TO_CHAR(ce.data_base, 'DD/MM/YYYY') AS dt_base,
                   cee.qt_extrato_eletro
              FROM projetos.controles_extrato_envio cee
              JOIN public.controles_extratos ce
                ON ce.cd_plano    = cee.cd_plano
               AND ce.cd_empresa  = cee.cd_empresa
               AND ce.nro_extrato = cee.nr_extrato
              JOIN public.patrocinadoras p
                ON p.cd_empresa = cee.cd_empresa
              JOIN public.planos pl
                ON pl.cd_plano = cee.cd_plano
             WHERE 1 = 1
               ".(trim($args['cd_plano_empresa']) != '' ? "AND cee.cd_empresa = ".intval($args['cd_plano_empresa']) : "")."
               ".(trim($args['cd_plano']) != '' ? "AND cee.cd_plano = ".intval($args['cd_plano']) : "")."
               ".(intval($args['nro_extrato']) > 0 ? "AND cee.nr_extrato = ".intval($args['nro_extrato']) : "")."
               ".(((trim($args['dt_gerado_ini']) != '') AND (trim($args['dt_gerado_fim']) != '')) ? " AND DATE_TRUNC('day', cee.dt_inclusao) BETWEEN TO_DATE('".$args['dt_gerado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_gerado_fim']."', 'DD/MM/YYYY')" : "")."
                                          
             ORDER BY cee.cd_empresa ASC,
                      cee.nr_extrato DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function email_enviado($cd_plano, $cd_empresa, $nr_extrato, $args = array())
    {
        $qr_sql = "
            SELECT (SELECT TO_CHAR(MIN(ea.dt_schedule_email),'DD/MM/YYYY HH24:MI:SS')
                      FROM projetos.envia_emails ea
                     WHERE ea.cd_email IN (SELECT eec.cd_email
                                             FROM projetos.extrato_envio_controle eec
                                            WHERE eec.dt_exclusao    IS NULL
                                              AND eec.cd_plano   = ".intval($cd_plano)."
                                              AND eec.cd_empresa = ".intval($cd_empresa)." 
                                              AND eec.nr_extrato = ".intval($nr_extrato).")
                   ".(((trim($args['dt_envio_ini']) != '') AND (trim($args['dt_envio_fim']) != '')) ? " AND DATE_TRUNC('day', ea.dt_schedule_email) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                   ) dt_agendado,
                   COALESCE((SELECT COUNT(*)
                               FROM projetos.envia_emails ea
                              WHERE ea.fl_retornou = 'N'
                                AND ea.dt_email_enviado IS NULL
                                AND ea.cd_email IN (SELECT eec.cd_email
                                                      FROM projetos.extrato_envio_controle eec
                                                      JOIN participantes p
                                                        ON p.cd_empresa            = eec.cd_empresa
                                                       AND p.cd_registro_empregado = eec.cd_registro_empregado
                                                       AND p.seq_dependencia       = eec.seq_dependencia
                                                       AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
                                                     WHERE eec.dt_exclusao    IS NULL
                                                       AND eec.cd_plano   = ".intval($cd_plano)."
                                                       AND eec.cd_empresa = ".intval($cd_empresa)." 
                                                       AND eec.nr_extrato = ".intval($nr_extrato).")
                   ".(((trim($args['dt_envio_ini']) != '') AND (trim($args['dt_envio_fim']) != '')) ? " AND DATE_TRUNC('day', ea.dt_schedule_email) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                   ),0) AS qt_aguardando,
                   COALESCE((SELECT COUNT(*)
                               FROM projetos.envia_emails er
                              WHERE er.fl_retornou = 'S'
                                AND er.dt_email_enviado IS NOT NULL
                                AND er.cd_email IN (SELECT eec.cd_email
                                                      FROM projetos.extrato_envio_controle eec
                                                      JOIN participantes p
                                                        ON p.cd_empresa            = eec.cd_empresa
                                                       AND p.cd_registro_empregado = eec.cd_registro_empregado
                                                       AND p.seq_dependencia       = eec.seq_dependencia
                                                       AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
                                                     WHERE eec.dt_exclusao    IS NULL
                                                       AND eec.cd_plano   = ".intval($cd_plano)."
                                                       AND eec.cd_empresa = ".intval($cd_empresa)." 
                                                       AND eec.nr_extrato = ".intval($nr_extrato).")
                   ".(((trim($args['dt_envio_ini']) != '') AND (trim($args['dt_envio_fim']) != '')) ? " AND DATE_TRUNC('day', er.dt_schedule_email) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                   ),0) AS qt_enviado_nao,
                   COALESCE((SELECT COUNT(*)
                               FROM projetos.envia_emails ee
                              WHERE ee.fl_retornou = 'N'
                                AND ee.dt_email_enviado IS NOT NULL
                                AND ee.cd_email IN (SELECT eec.cd_email
                                                      FROM projetos.extrato_envio_controle eec
                                                      JOIN participantes p
                                                        ON p.cd_empresa            = eec.cd_empresa
                                                       AND p.cd_registro_empregado = eec.cd_registro_empregado
                                                       AND p.seq_dependencia       = eec.seq_dependencia
                                                       AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
                                                     WHERE eec.dt_exclusao    IS NULL
                                                       AND eec.cd_plano   = ".intval($cd_plano)."
                                                       AND eec.cd_empresa = ".intval($cd_empresa)." 
                                                       AND eec.nr_extrato = ".intval($nr_extrato).")
                   ".(((trim($args['dt_envio_ini']) != '') AND (trim($args['dt_envio_fim']) != '')) ? " AND DATE_TRUNC('day', ee.dt_schedule_email) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                   ),0) AS qt_enviado;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_extrato($cd_plano, $cd_empresa, $nro_extrato)
    {
        $qr_sql = "
            SELECT ce.cd_empresa, 
                   ce.cd_plano,
                   ce.nro_extrato,
                   TO_CHAR(ce.dt_liberacao, 'DD/MM/YYYY') AS dt_liberacao,
                   p.sigla AS ds_empresa,
                   pl.descricao AS ds_plano,
                   TO_CHAR(ce.data_base, 'DD/MM/YYYY') AS dt_base,
                   TO_CHAR(ce.data_base, 'YYYY') AS nr_ano,
                   TO_CHAR(ce.data_base, 'MM') AS nr_mes,
                   COALESCE((SELECT i.qtd_extratos
                               FROM inconsist_ctrl_extratos i
                              WHERE i.cd_empresa  = ce.cd_empresa
                                AND i.cd_plano    = ce.cd_plano
                                AND i.nro_extrato = ce.nro_extrato
                                AND i.seq_inconsistencia = (SELECT MAX(i2.seq_inconsistencia) 
                                                              FROM inconsist_ctrl_extratos i2
                                                             WHERE i2.cd_empresa  = i.cd_empresa
                                                               AND i2.cd_plano    = i.cd_plano
                                                               AND i2.nro_extrato = i.nro_extrato)), 0) AS qt_extrato,
                   (SELECT COUNT(*) 
                      FROM projetos.extrato_participantes ep
                     WHERE ep.nro_extrato = ce.nro_extrato
                       AND ep.cd_empresa  = ce.cd_empresa
                       AND ep.cd_plano    = ce.cd_plano) AS qt_extrato_participante,
                   (SELECT COUNT(*) 
                      FROM projetos.extrato_participantes ep
                      JOIN participantes p
                        ON p.cd_empresa            = ep.cd_empresa
                       AND p.cd_registro_empregado = ep.cd_registro_empregado
                       AND p.seq_dependencia       = ep.seq_dependencia
                       AND p.dt_obito     IS NOT NULL 
                     WHERE ep.nro_extrato = ce.nro_extrato
                       AND ep.cd_empresa  = ce.cd_empresa
                       AND ep.cd_plano    = ce.cd_plano) AS qt_obito,
                   (SELECT COUNT(*) 
                      FROM projetos.extrato_participantes ep
                      JOIN participantes p
                        ON p.cd_empresa            = ep.cd_empresa
                       AND p.cd_registro_empregado = ep.cd_registro_empregado
                       AND p.seq_dependencia       = ep.seq_dependencia
                       AND p.cd_plano              = 0
                       AND p.dt_obito              IS NULL 
                     WHERE ep.nro_extrato = ce.nro_extrato
                       AND ep.cd_empresa  = ce.cd_empresa
                       AND ep.cd_plano    = ce.cd_plano) AS qt_sem_plano,
                   (SELECT COUNT(*) 
                      FROM projetos.extrato_participantes ep
                      JOIN participantes p
                        ON p.cd_empresa            = ep.cd_empresa
                       AND p.cd_registro_empregado = ep.cd_registro_empregado
                       AND p.seq_dependencia       = ep.seq_dependencia
                       AND p.cd_plano              > 0
                       AND p.dt_obito              IS NULL 
                       AND p.email                 IS NULL 
                       AND p.email_profissional    IS NULL
                     WHERE ep.nro_extrato = ce.nro_extrato
                       AND ep.cd_empresa  = ce.cd_empresa
                       AND ep.cd_plano    = ce.cd_plano) AS qt_sem_email,
                   (CASE WHEN 
                            COALESCE((SELECT i.qtd_extratos
                                        FROM inconsist_ctrl_extratos i
                                       WHERE i.cd_empresa  = ce.cd_empresa
                                         AND i.cd_plano    = ce.cd_plano
                                         AND i.nro_extrato = ce.nro_extrato
                                       ORDER BY i.dt_inconsistencia DESC
                                       LIMIT 1), 0)
                            =
                            (SELECT COUNT(*) 
                               FROM projetos.extrato_participantes ep
                              WHERE ep.nro_extrato = ce.nro_extrato
                                AND ep.cd_empresa  = ce.cd_empresa
                                AND ep.cd_plano    = ce.cd_plano)
                         THEN 'S'
                         ELSE 'N'

                   END) AS fl_libera_envio,
                   (CASE WHEN TO_CHAR(ce.data_base, 'MM') IN ('03', '06', '09', '12') 
                         THEN 'S'
                         ELSE 'N'
                   END) AS fl_enviar_email_cadastro
              FROM public.controles_extratos ce
              JOIN public.patrocinadoras p
                ON p.cd_empresa = ce.cd_empresa
              JOIN public.planos pl
                ON pl.cd_plano = ce.cd_plano
             WHERE ce.dt_liberacao IS NOT NULL
               AND (SELECT COUNT(*)
                      FROM projetos.controles_extrato_envio cee
                     WHERE cee.cd_plano   = ce.cd_plano
                       AND cee.cd_empresa = ce.cd_empresa
                       AND cee.nr_extrato = ce.nro_extrato) = 0
               AND TO_CHAR(ce.data_base, 'YYYY')::integer >= 2017
               AND COALESCE((SELECT i.qtd_extratos
                               FROM inconsist_ctrl_extratos i
                              WHERE i.cd_empresa  = ce.cd_empresa
                                AND i.cd_plano    = ce.cd_plano
                                AND i.nro_extrato = ce.nro_extrato
                              ORDER BY i.dt_inconsistencia DESC
                              LIMIT 1), 0) > 0
               AND ce.cd_empresa  = ".intval($cd_empresa)."
               AND ce.cd_plano    = ".intval($cd_plano)."
               AND ce.nro_extrato = ".intval($nro_extrato).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_sem_email($cd_plano, $cd_empresa, $nro_extrato)
    {
        $qr_sql = "
            SELECT ep.cd_empresa,
                   ep.cd_registro_empregado,
                   ep.seq_dependencia,
                   p.nome
              FROM projetos.extrato_participantes ep
              JOIN participantes p
                ON p.cd_empresa            = ep.cd_empresa
               AND p.cd_registro_empregado = ep.cd_registro_empregado
               AND p.seq_dependencia       = ep.seq_dependencia
               AND p.cd_plano              > 0
               AND p.dt_obito              IS NULL 
               AND p.email                 IS NULL 
               AND p.email_profissional    IS NULL
             WHERE ep.cd_empresa  = ".intval($cd_empresa)."
               AND ep.cd_plano    = ".intval($cd_plano)."
               AND ep.nro_extrato = ".intval($nro_extrato).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_agendamento($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.extrato_envio_controle
                   (
                        cd_plano, 
                        cd_empresa, 
                        cd_registro_empregado,
                        seq_dependencia, 
                        nr_ano, 
                        nr_mes, 
                        nr_extrato, 
                        dt_envio, 
                        cd_usuario_inclusao
                   )
            SELECT ep.cd_plano,
                   ep.cd_empresa,
                   ep.cd_registro_empregado,
                   ep.seq_dependencia,
                   ".intval($args['nr_ano']).",
                   ".intval($args['nr_mes']).",
                   ep.nro_extrato,
                   TO_DATE('".$args['dt_envio']."', 'DD/MM/YYY'),
                   ".intval($args['cd_usuario'])."
              FROM projetos.extrato_participantes ep
              JOIN participantes p
                ON p.cd_empresa            = ep.cd_empresa
               AND p.cd_registro_empregado = ep.cd_registro_empregado
               AND p.seq_dependencia       = ep.seq_dependencia
               AND p.cd_plano              > 0
               AND p.dt_obito              IS NULL 
               AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
             WHERE ep.nro_extrato = ".intval($args['nro_extrato'])."
               AND ep.cd_empresa  = ".intval($args['cd_empresa'])."
               AND ep.cd_plano    = ".intval($args['cd_plano']).";

        INSERT INTO projetos.controles_extrato_envio
             (
                cd_plano, 
                cd_empresa, 
                nr_extrato, 
                qt_extrato_eletro,
                cd_usuario_inclusao
             )
        VALUES 
             (
                ".intval($args['cd_plano']).",
                ".intval($args['cd_empresa']).",
                ".intval($args['nro_extrato']).",
                ".intval($args['qt_extrato']).",
                ".intval($args['cd_usuario'])."
             );

        SELECT rotinas.email_extrato_participante(
                ".intval($args['cd_plano']).", 
                ".intval($args['cd_empresa']).", 
                ".intval($args['nr_mes']).", 
                ".intval($args['nr_ano']).", 
                TO_DATE('".trim($args['dt_envio'])."','DD/MM/YYYY'), 
                ".intval($args['cd_usuario'])."
        ); ";

        $this->db->query($qr_sql);
    }

    public function get_extrato_enviado($cd_plano, $cd_empresa, $nro_extrato)
    {
        $qr_sql = "
            SELECT cee.cd_plano, 
                   pl.descricao AS ds_plano,
                   cee.cd_empresa, 
                   p.sigla AS ds_empresa,
                   cee.nr_extrato,
                   TO_CHAR(cee.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cee.cd_usuario_inclusao) AS ds_usuario,
                   TO_CHAR(ce.data_base, 'DD/MM/YYYY') AS dt_base,
                   cee.qt_extrato_eletro
              FROM projetos.controles_extrato_envio cee
              JOIN public.controles_extratos ce
                ON ce.cd_plano    = cee.cd_plano
               AND ce.cd_empresa  = cee.cd_empresa
               AND ce.nro_extrato = cee.nr_extrato
              JOIN public.patrocinadoras p
                ON p.cd_empresa = cee.cd_empresa
              JOIN public.planos pl
                ON pl.cd_plano = cee.cd_plano
             WHERE cee.cd_empresa = ".intval($cd_empresa)."
               AND cee.cd_plano   = ".intval($cd_plano)."
               AND cee.nr_extrato = ".intval($nro_extrato).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_participantes_enviados($cd_plano, $cd_empresa, $nro_extrato)
    {
        $qr_sql = "
            SELECT p.cd_plano, 
                   p.cd_empresa, 
                   p.cd_registro_empregado, 
                   p.seq_dependencia, 
                   p.nome, 
                   eec.cd_email,
                   TO_CHAR(eec.dt_envio, 'DD/MM/YYYY') AS dt_envio
              FROM projetos.extrato_envio_controle eec
              JOIN public.participantes p
                ON p.cd_empresa            = eec.cd_empresa
               AND p.cd_registro_empregado = eec.cd_registro_empregado
               AND p.seq_dependencia       = eec.seq_dependencia
             WHERE eec.dt_exclusao    IS NULL
               AND eec.dt_envio_email IS NOT NULL
               AND eec.cd_plano   = ".intval($cd_plano)."
               AND eec.cd_empresa = ".intval($cd_empresa)." 
               AND eec.nr_extrato = ".intval($nro_extrato)." 
             ORDER BY eec.dt_envio, 
                      p.nome;";

        return $this->db->query($qr_sql)->result_array();
    }   

}