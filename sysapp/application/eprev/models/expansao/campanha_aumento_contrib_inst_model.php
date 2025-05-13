<?php
class Campanha_aumento_contrib_inst_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT TO_CHAR(caci.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   p.sigla AS ds_instituidor,
				   caci.cd_edicao,
                   TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_base_extrato,
                   caci.cd_campanha_aumento_contrib_inst,
                   (SELECT COUNT(*) 
                      FROM expansao.campanha_aumento_contrib_inst_participante cacip 
                     WHERE cacip.dt_exclusao IS NULL
                       AND cacip.cd_campanha_aumento_contrib_inst = caci.cd_campanha_aumento_contrib_inst) AS qt_participante,
                   COALESCE((SELECT COUNT(*)
                               FROM projetos.envia_emails ea
                              WHERE ea.cd_divulgacao = caci.cd_divulgacao
                                AND ea.fl_retornou = 'N'
                                AND ea.dt_email_enviado IS NULL
                              GROUP BY ea.cd_divulgacao),0) AS qt_email_aguarda_env,
                   COALESCE((SELECT COUNT(*)
                               FROM projetos.envia_emails er
                              WHERE er.cd_divulgacao = caci.cd_divulgacao
                                AND er.fl_retornou = 'S'
                                AND er.dt_email_enviado IS NOT NULL
                              GROUP BY er.cd_divulgacao),0) AS qt_email_nao_env,
                   COALESCE((SELECT COUNT(*)
                               FROM projetos.envia_emails ee
                              WHERE ee.cd_divulgacao = caci.cd_divulgacao
                                AND ee.fl_retornou = 'N'
                                AND ee.dt_email_enviado IS NOT NULL
                              GROUP BY ee.cd_divulgacao),0) AS qt_email_env,
                   COALESCE((SELECT COUNT(*)
                               FROM projetos.envia_emails ee
                              WHERE ee.cd_divulgacao = caci.cd_divulgacao
                              GROUP BY ee.cd_divulgacao),0) AS qt_email,
                   TO_CHAR(caci.dt_envio,'DD/MM/YYYY HH24:MI') AS dt_envio,
                   funcoes.get_usuario_nome(caci.cd_usuario_envio) AS usuario_envio
			  FROM expansao.campanha_aumento_contrib_inst caci
              JOIN patrocinadoras p
                ON p.cd_empresa = caci.cd_empresa
              JOIN meu_retrato.edicao e
                ON e.cd_edicao = caci.cd_edicao
			 WHERE caci.dt_exclusao IS NULL
			   ".(trim($args['cd_empresa']) != "" ? "AND caci.cd_empresa = ".intval($args['cd_empresa']) : "" )."
               ".(trim($args['dt_envio']) == 'S' ? "AND caci.dt_envio IS NOT NULL" : "")."
               ".(trim($args['dt_envio']) == 'N' ? "AND caci.dt_envio IS NULL": "")."
               ".(((trim($args['dt_envio_ini']) != '') AND (trim($args['dt_envio_fim']) != '')) ? " AND DATE_TRUNC('day', caci.dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', caci.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_instituidor()
	{
		$qr_sql = "
			SELECT sigla AS text, 
			       cd_empresa AS value
			  FROM patrocinadoras
			 WHERE tipo_cliente = 'I';";

		 return $this->db->query($qr_sql)->result_array();	
	}

    public function carrega($cd_campanha_aumento_contrib_inst)
    {
        $qr_sql = "
            SELECT caci.cd_campanha_aumento_contrib_inst,
                   caci.ds_assunto,
                   caci.ds_tpl,
                   caci.cd_edicao,
                   caci.cd_usuario_agenda_envio,
                   TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_base_extrato,
                   TO_CHAR(caci.dt_envio,'DD/MM/YYYY HH24:MI') AS dt_envio,
                   TO_CHAR(caci.dt_agenda_envio,'DD/MM/YYYY HH24:MI') AS dt_agenda_envio,
                   p.sigla AS ds_instituidor,
                   (SELECT COUNT(*)
                      FROM expansao.campanha_aumento_contrib_inst_participante cacip
                     WHERE cacip.cd_campanha_aumento_contrib_inst = caci.cd_campanha_aumento_contrib_inst
                       AND cacip.fl_email = 'S') AS qt_email,
                   (SELECT COUNT(*)
                      FROM expansao.campanha_aumento_contrib_inst_participante cacip
                     WHERE cacip.cd_campanha_aumento_contrib_inst = caci.cd_campanha_aumento_contrib_inst
                       AND cacip.fl_app = 'S') AS qt_app
              FROM expansao.campanha_aumento_contrib_inst caci
              JOIN patrocinadoras p
                ON p.cd_empresa = caci.cd_empresa
              JOIN meu_retrato.edicao e
                ON e.cd_edicao = caci.cd_edicao
             WHERE cd_campanha_aumento_contrib_inst = ".intval($cd_campanha_aumento_contrib_inst)."
             ORDER BY caci.dt_inclusao DESC 
             LIMIT 1;";
   
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_campanha_aumento_contrib_inst = intval($this->db->get_new_id('expansao.campanha_aumento_contrib_inst', 'cd_campanha_aumento_contrib_inst'));

        $qr_sql = "
            INSERT INTO expansao.campanha_aumento_contrib_inst
                 (
                    cd_campanha_aumento_contrib_inst, 
                    cd_empresa,
                    cd_edicao,
                    ds_assunto, 
                    ds_tpl,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_campanha_aumento_contrib_inst).",
                    ".intval($args['cd_empresa']).",
                    (SELECT e.cd_edicao 
          		       FROM meu_retrato.edicao e
          		      WHERE e.cd_empresa = ".intval($args['cd_empresa'])."
          		        AND e.dt_exclusao IS NULL
          		        AND e.dt_liberacao_atuarial IS NOT NULL
                        AND e.tp_participante = 'ATIV'
          		      ORDER BY cd_edicao DESC
          		      LIMIT 1),
                    ".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").",
                    ".(trim($args['ds_tpl']) != '' ? str_escape($args['ds_tpl']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_campanha_aumento_contrib_inst;
    }

    public function atualizar($cd_campanha_aumento_contrib_inst, $args = array())
    {
        $qr_sql = "
            UPDATE expansao.campanha_aumento_contrib_inst
               SET ds_assunto           = ".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").",
                   ds_tpl               = ".(trim($args['ds_tpl']) != '' ? str_escape($args['ds_tpl']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_campanha_aumento_contrib_inst  = ".intval($cd_campanha_aumento_contrib_inst).";";

        $this->db->query($qr_sql);
    }

    public function participante_listar($cd_campanha_aumento_contrib_inst, $args = array())
    {
        $qr_sql = "
            SELECT cacip.cd_empresa,
                   cacip.cd_registro_empregado,
                   cacip.seq_dependencia,
                   p.nome,
                   TO_CHAR(cacip.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
                   cacip.cd_campanha_aumento_contrib_inst,
                   cacip.cd_campanha_aumento_contrib_inst_participante,
                   p.email,
                   p.email_profissional,
                   (CASE WHEN cacip.fl_email = 'S' THEN 'Sim'
                         WHEN cacip.fl_email = 'N' THEN 'Não'
                         ELSE ''
                   END) AS ds_email,
                   (CASE WHEN cacip.fl_email = 'S' THEN 'label label-success'
                         WHEN cacip.fl_email = 'N' THEN 'label label-important'
                         ELSE ''
                   END) AS ds_class_email,
                   (CASE WHEN cacip.fl_app = 'S' THEN 'Sim'
                         WHEN cacip.fl_app = 'N' THEN 'Não'
                         ELSE ''
                   END) AS ds_app,
                   (CASE WHEN cacip.fl_app = 'S' THEN 'label label-success'
                         WHEN cacip.fl_app = 'N' THEN 'label label-important'
                         ELSE ''
                   END) AS ds_class_app
              FROM expansao.campanha_aumento_contrib_inst_participante cacip
              JOIN participantes p
                ON p.cd_empresa            = cacip.cd_empresa
               AND p.cd_registro_empregado = cacip.cd_registro_empregado
               AND p.seq_dependencia       = cacip.seq_dependencia
             WHERE cacip.cd_campanha_aumento_contrib_inst = ".intval($cd_campanha_aumento_contrib_inst)."
               ".(trim($args['fl_exclusao']) == 'S' ? "AND cacip.dt_exclusao IS NOT NULL" : "")."
               ".(trim($args['fl_exclusao']) == 'N' ? "AND cacip.dt_exclusao IS NULL": "")."


               ".(trim($args['fl_email']) == 'S' ? "AND cacip.fl_email = 'S'" : "")."
               ".(trim($args['fl_email']) == 'N' ? "AND cacip.fl_email = 'N'": "")."


               ".(trim($args['fl_app']) == 'S' ? "AND cacip.fl_app = 'S'" : "")."
               ".(trim($args['fl_app']) == 'N' ? "AND cacip.fl_app = 'N'": "")."

               ;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function remover($cd_campanha_aumento_contrib_inst_participante, $cd_usuario)
    {
        $qr_sql = "
            UPDATE expansao.campanha_aumento_contrib_inst_participante
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_campanha_aumento_contrib_inst_participante = ".intval($cd_campanha_aumento_contrib_inst_participante).";";

        $this->db->query($qr_sql);
    }

    public function adicionar($cd_campanha_aumento_contrib_inst_participante, $cd_usuario)
    {
        $qr_sql = "
            UPDATE expansao.campanha_aumento_contrib_inst_participante
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao  = NULL,
                   dt_exclusao          = NULL
             WHERE cd_campanha_aumento_contrib_inst_participante = ".intval($cd_campanha_aumento_contrib_inst_participante).";";

        $this->db->query($qr_sql);
    }

    public function get_campanha_anterior($cd_empresa)
    {
        $qr_sql = "
            SELECT caci.cd_campanha_aumento_contrib_inst,
                   caci.ds_assunto,
                   caci.ds_tpl
              FROM expansao.campanha_aumento_contrib_inst caci
             WHERE caci.cd_empresa = ".intval($cd_empresa)."
             ORDER BY caci.dt_inclusao DESC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_dados_meu_retrato($cd_edicao, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $qr_sql = "
            SELECT epd.cd_linha, 
                   epd.ds_linha, 
                   epd.vl_valor
              FROM meu_retrato.edicao_participante ep
              JOIN meu_retrato.edicao_participante_dado epd
                ON epd.cd_edicao_participante = ep.cd_edicao_participante
             WHERE ep.cd_edicao             = ".$cd_edicao."
               AND ep.cd_empresa            = ".$cd_empresa."
               AND ep.cd_registro_empregado = ".$cd_registro_empregado."
               AND ep.seq_dependencia       = ".$seq_dependencia."
               AND epd.cd_linha             IN (
                  'PARTICIPANTE_NOME',
                  'PARTICIPANTE_DT_INGRESSO',
                  'BEN_DATA_SIMULACAO',
                  'SIMULA_CONTRIB_ATUAL_C3',
                  'SIMULA_CONTRIB_NOVO_C3',
                  'SIMULA_SALDO_ACUMULADO_NOVO_C3',
                  'SIMULA_RENTABILIDADE_C3',
                  'SIMULA_TEMPO_C3'
              );";

        return $this->db->query($qr_sql)->result_array();
    }

    public function cripto_re($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $qr_sql = "SELECT funcoes.cripto_re(".$cd_empresa.", ".$cd_registro_empregado.", ".$seq_dependencia.") AS cripto_re;"; 

        return $this->db->query($qr_sql)->row_array();
    }

    public function cadastra_email_mkt($args)
    {
        $row = $this->db->query("SELECT nextval('projetos.divulgacao_cd_divulgacao_seq') AS cd_divulgacao")->row_array();
        $cd_divulgacao = intval($row["cd_divulgacao"]);

        $qr_sql = "
            INSERT INTO projetos.divulgacao
                 (
                    cd_divulgacao, 
                    assunto,
                    conteudo, 
                    cd_usuario,
                    cd_usuario_alteracao,
                    divisao,
                    tipo_divulgacao
                  )
             VALUES 
                  (
                    ".intval($cd_divulgacao).",
                    '".trim($args['ds_assunto'])."',
                    'N&atilde;o enviar e-mails, divulga&ccedil;&atilde;o criada apenas para monitorar a leitura dos e-mails.',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario']).",
                    'GCM',
                    'E'
                  );";

        $this->db->query($qr_sql);

        return $cd_divulgacao;
    }

    public function enviar($cd_campanha_aumento_contrib_inst, $cd_divulgacao, $cd_usuario)
    {
        $qr_sql = "
          UPDATE expansao.campanha_aumento_contrib_inst
             SET cd_divulgacao    = ".intval($cd_divulgacao).",
                 cd_usuario_envio = ".intval($cd_usuario).",
                 dt_envio         = CURRENT_TIMESTAMP
           WHERE cd_campanha_aumento_contrib_inst = ".intval($cd_campanha_aumento_contrib_inst).";";

        $this->db->query($qr_sql);
    }

    public function agendar_envio($cd_campanha_aumento_contrib_inst, $cd_usuario)
    {
        $qr_sql = "
          UPDATE expansao.campanha_aumento_contrib_inst
             SET fl_email                = 'S',
                 fl_app                  = 'S',
                 cd_usuario_agenda_envio = ".intval($cd_usuario).",
                 dt_agenda_envio         = CURRENT_TIMESTAMP
           WHERE cd_campanha_aumento_contrib_inst = ".intval($cd_campanha_aumento_contrib_inst).";";

        $this->db->query($qr_sql);
    }

    public function enviarPush($cd_campanha_aumento_contrib_inst, $cd_usuario)
    {
        $qr_sql = "SELECT autoatendimento.app_push_aumento_contribuicao(".intval($cd_campanha_aumento_contrib_inst).", ".intval($cd_usuario).");";

        $this->db->query($qr_sql);
    }

    public function get_envio_email_agendado()
    {
        $qr_sql = "
            SELECT i.cd_campanha_aumento_contrib_inst,
                   (SELECT COUNT(*)
                      FROM expansao.campanha_aumento_contrib_inst_participante p
                     WHERE p.cd_campanha_aumento_contrib_inst = i.cd_campanha_aumento_contrib_inst
                       AND p.dt_exclusao IS NULL
                       AND p.fl_email = 'S') AS qt_envio
              FROM expansao.campanha_aumento_contrib_inst i
             WHERE i.dt_envio IS NULL 
               AND i.dt_agenda_envio IS NOT NULL
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }
}