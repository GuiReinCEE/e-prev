<?php
class Pos_venda_participante_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_usuario_cadastro()
	{
		$qr_sql = "
            SELECT DISTINCT codigo AS value, 
                   nome AS text 
              FROM projetos.usuarios_controledi u 
              JOIN projetos.pos_venda_participante pvp 
                ON u.codigo = pvp.cd_usuario_inicio 
             ORDER BY nome;";
        
        return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuario_encerramento()
	{		
        $qr_sql = "
            SELECT DISTINCT codigo AS value, 
                   nome AS text 
              FROM projetos.usuarios_controledi u 
              JOIN projetos.pos_venda_participante pvp 
                ON u.codigo = pvp.cd_usuario_final
             ORDER BY nome;";
        
        return $this->db->query($qr_sql)->result_array();
	}

    public function get_vendedor()
    {
        $qr_sql = "
            SELECT DISTINCT uc.codigo AS value,
                   uc.nome AS text
              FROM titulares t
              JOIN projetos.usuarios_controledi uc
                ON UPPER(uc.usuario) = t.user_vendedor_plano
             ORDER BY uc.nome ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function lista($args = array())
    {
        $qr_sql = "
            SELECT p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
                   p.nome,
                   TO_CHAR(t.dt_ingresso_eletro, 'DD/MM/YYYY') AS dt_ingresso,
                   TO_CHAR(t.dt_digita_ingresso, 'DD/MM/YYYY') AS dt_digita_ingresso,
                   TO_CHAR(COALESCE(bvc.dt_envio, bvcp.dt_envio_email), 'DD/MM/YYYY') AS dt_boas_vindas,
                   TO_CHAR(pvp.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
                   funcoes.get_usuario_nome(pvp.cd_usuario_inicio) AS ds_usuario_inicio,
                   TO_CHAR(pvp.dt_final, 'DD/MM/YYYY') AS dt_final,
                   funcoes.get_usuario_nome(pvp.cd_usuario_final) AS ds_usuario_final,
                   (SELECT TO_CHAR(MAX(pvpa.dt_inclusao),'DD/MM/YYYY HH24:MI') 
                      FROM projetos.pos_venda_participante_acompanhamento pvpa
                     WHERE pvpa.dt_exclusao IS NULL
                       AND pvpa.cd_pos_venda_participante = pvp.cd_pos_venda_participante) AS dt_acompanhamento,
                   pvp.cd_atendimento,
                   pvp.cd_pos_venda_participante,
                   uc.nome AS ds_usuario_vendedor
              FROM public.participantes p
              JOIN public.titulares t
                ON t.cd_empresa            = p.cd_empresa 
               AND t.cd_registro_empregado = p.cd_registro_empregado 
               AND t.seq_dependencia       = p.seq_dependencia
              LEFT JOIN projetos.boas_vindas_controle bvc
                ON bvc.cd_empresa            = p.cd_empresa 
               AND bvc.cd_registro_empregado = p.cd_registro_empregado 
               AND bvc.seq_dependencia       = p.seq_dependencia
              LEFT JOIN projetos.boas_vindas_controle_patrocinadora bvcp
                ON bvcp.cd_empresa            = p.cd_empresa 
               AND bvcp.cd_registro_empregado = p.cd_registro_empregado 
               AND bvcp.seq_dependencia       = p.seq_dependencia
              LEFT JOIN projetos.pos_venda_participante pvp
                ON pvp.cd_empresa            = p.cd_empresa 
               AND pvp.cd_registro_empregado = p.cd_registro_empregado 
               AND pvp.seq_dependencia       = p.seq_dependencia
              LEFT JOIN projetos.usuarios_controledi uc
                ON UPPER(uc.usuario) = t.user_vendedor_plano
             WHERE pvp.dt_exclusao IS NULL
               ".(trim($args['cd_empresa']) != '' ? "AND p.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_registro_empregado']) != '' ? "AND p.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
               ".(trim($args['seq_dependencia']) != '' ? "AND p.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
               ".(((trim($args['dt_ingresso_ini']) != '') AND (trim($args['dt_ingresso_fim']) != ''))? "AND DATE_TRUNC('day', t.dt_ingresso_eletro) BETWEEN TO_DATE('".$args['dt_ingresso_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_ingresso_fim']."','DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_digita_ingresso_ini']) != '') AND (trim($args['dt_digita_ingresso_fim']) != ''))? "AND DATE_TRUNC('day', t.dt_digita_ingresso) BETWEEN TO_DATE('".$args['dt_digita_ingresso_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_digita_ingresso_fim']."','DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_boas_vindas_ini']) != '') AND (trim($args['dt_boas_vindas_fim']) != ''))? "AND DATE_TRUNC('day', COALESCE(bvc.dt_envio, bvcp.dt_envio_email)) BETWEEN TO_DATE('".$args['dt_boas_vindas_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_boas_vindas_fim']."','DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != ''))? "AND DATE_TRUNC('day', pvp.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."','DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_final_ini']) != '') AND (trim($args['dt_final_fim']) != ''))? "AND DATE_TRUNC('day', pvp.dt_final) BETWEEN TO_DATE('".$args['dt_final_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_final_fim']."','DD/MM/YYYY')" : "")."
               ".(trim($args['cd_atendimento']) != '' ? "AND pvp.cd_atendimento = ".intval($args['cd_atendimento']) : "")."
               ".(trim($args['cd_usuario_inicio']) != '' ? "AND pvp.cd_usuario_inicio = ".intval($args['cd_usuario_inicio']) : "")."
               ".(trim($args['cd_usuario_final']) != '' ? "AND pvp.cd_usuario_final = ".intval($args['cd_usuario_final']) : "")."
               ".(trim($args['cd_usuario_vendedor']) != '' ? "AND uc.codigo = ".intval($args['cd_usuario_vendedor']) : "").";";
 
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function excluir($cd_pos_venda_participante, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.pos_venda_participante 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
                   cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_pos_venda_participante = ".intval($cd_pos_venda_participante).";";
        
        $this->db->query($qr_sql);
    }

	public function listar_email($args = array())
	{
		$qr_sql = "
            SELECT ee.cd_email,
                   TO_CHAR(ee.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio,
                   TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY HH24:MI') AS dt_email_enviado,
                   ee.cd_empresa,
                   ee.cd_registro_empregado,
                   ee.seq_dependencia,
                   p.nome,
                   ee.fl_retornou,
                   funcoes.get_usuario_nome(ee.cd_usuario) AS ds_usuario,
                   (CASE WHEN ee.fl_retornou = 'S' 
                        THEN 'Retornou'
                        ELSE 'Normal'
                   END) AS retornou,
                   (CASE WHEN ee.fl_retornou = 'S' 
                        THEN 'important'
                        ELSE 'success'
                   END) AS class_retornou
              FROM projetos.envia_emails ee
              LEFT JOIN public.participantes p
                ON ee.cd_empresa            = p.cd_empresa
               AND ee.cd_registro_empregado = p.cd_registro_empregado
               AND ee.seq_dependencia       = p.seq_dependencia
             WHERE ee.cd_evento = 55
               ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? "AND CAST(ee.dt_envio AS DATE) BETWEEN TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY')" : "")."
             ORDER BY ee.cd_email DESC;";
        
		return $this->db->query($qr_sql)->result_array();
	}

    public function posvenda_participante($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $qr_sql = "
            SELECT p.nome,
                   p.cd_empresa,
                   p.cd_registro_empregado,
                   p.seq_dependencia,
                   p.cd_empresa || '/' || p.cd_registro_empregado || '/' || p.seq_dependencia  AS re,
                   (SELECT TO_CHAR(MAX(pvp.dt_final),'DD/MM/YYYY HH24:MI')
                      FROM projetos.pos_venda_participante pvp
                     WHERE pvp.cd_empresa            = p.cd_empresa 
                       AND pvp.cd_registro_empregado = p.cd_registro_empregado 
                       AND pvp.seq_dependencia       = p.seq_dependencia
                       AND pvp.dt_exclusao           IS NULL
                       AND pvp.dt_final              IS NOT NULL) AS dt_ultimo
              FROM public.participantes p
             WHERE p.cd_empresa            = ".intval($cd_empresa)."
               AND p.cd_registro_empregado = ".intval($cd_registro_empregado)."
               AND p.seq_dependencia       = ".intval($seq_dependencia).";";
        
        return $this->db->query($qr_sql)->row_array();
    }

    public function posvenda_aberto($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $qr_sql = "
            SELECT cd_pos_venda,
                   cd_pos_venda_participante
              FROM projetos.pos_venda_participante
             WHERE cd_empresa            = ".intval($cd_empresa)."
               AND cd_registro_empregado = ".intval($cd_registro_empregado)."
               AND seq_dependencia       = ".intval($seq_dependencia)."
               AND dt_final              IS NULL
               AND dt_exclusao           IS NULL;";
    
        return $this->db->query($qr_sql)->result_array();
    }

    public function iniciar_posvenda($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_usuario, $cd_atendimento = 0)
    {
        $qr_sql = "SELECT projetos.gera_formulario_posvenda(".intval($cd_empresa).", ".intval($cd_registro_empregado).", ".intval($seq_dependencia).", ".intval($cd_usuario).", ".intval($cd_atendimento).");";
        
        $this->db->query($qr_sql);
    }

    public function posvenda_aberto_perguntas($cd_pos_venda)
    {
        $qr_sql = "
            SELECT cd_pos_venda_pergunta, 
                   ds_pergunta, 
                   CASE WHEN fl_multipla_resposta = 'S'
                        THEN 'checkbox'
                        ELSE 'radio'
                   END AS tp_resposta
              FROM projetos.pos_venda_pergunta
             WHERE cd_pos_venda = ".intval($cd_pos_venda)."
               AND dt_exclusao  IS NULL
             ORDER BY nr_ordem ASC;";
    
        return $this->db->query($qr_sql)->result_array();
    }

    public function posvenda_aberto_respostas($cd_pos_venda_participante, $cd_pos_venda_pergunta)
    {
        $qr_sql = "
            SELECT pvr.cd_pos_venda_resposta,
                   pvr.cd_resposta, 
                   COALESCE(pvr.ds_resposta,cd_resposta) AS ds_resposta,
                   pvr.fl_complemento, 
                   pvr.fl_complemento_obrigatorio,
                   CASE WHEN pvpr.cd_pos_venda_resposta IS NOT NULL
                        THEN 'S'
                        ELSE 'N'
                   END AS fl_respondido,
                   pvpr.complemento
              FROM projetos.pos_venda_resposta pvr
              LEFT JOIN projetos.pos_venda_participante_resposta pvpr
                ON pvpr.cd_pos_venda_resposta = pvr.cd_pos_venda_resposta
               AND pvpr.cd_pos_venda_participante = ".intval($cd_pos_venda_participante)."
             WHERE pvr.cd_pos_venda_pergunta = ".intval($cd_pos_venda_pergunta)."
               AND pvr.dt_exclusao           IS NULL
             ORDER BY pvr.nr_ordem ASC;";
    
        return $this->db->query($qr_sql)->result_array();
    }

    public function enviar_email($cd_empresa, $cd_registro_empregado, $seq_dependencia, $cd_usuario)
    {
        $qr_sql = "
            SELECT projetos.email_pos_vendas(".intval($cd_empresa).", ".intval($cd_registro_empregado).", ".intval($seq_dependencia).", ".intval($cd_usuario).");";
        
        $this->db->query($qr_sql);
    }

    public function salvar_respostas($args = array())
    {
        $qr_sql = "
            DELETE 
              FROM projetos.pos_venda_participante_resposta
             WHERE cd_pos_venda_participante = ".intval($args['cd_pos_venda_participante']).";";
        
        foreach($args['pos_venda_participante_resposta'] as $item)
        {
            $qr_sql.= "
                INSERT INTO projetos.pos_venda_participante_resposta
                     (
                        cd_pos_venda_participante, 
                        cd_pos_venda_resposta, 
                        cd_usuario_inclusao, 
                        complemento
                     )
                VALUES 
                     (
                        ".intval($args['cd_pos_venda_participante']).",
                        ".intval($item['cd_resposta']).",
                        ".intval($args['cd_usuario']).",
                        ".(trim($item['complemento']) != "" ? "'".trim($item['complemento'])."'" : "NULL")."
                     );";
        }
        
        if(trim($args['fl_encerra']) == 'S')
        {
            $qr_sql.= "
                UPDATE projetos.pos_venda_participante
                   SET dt_final         = CURRENT_TIMESTAMP,
                       cd_usuario_final =  ".intval($args['cd_usuario'])."
                 WHERE cd_pos_venda_participante = ".intval($args['cd_pos_venda_participante']).";";
        }
        
        $this->db->query($qr_sql);
    }
    
    public function patrocinadora()
    {
        $qr_sql = "
            SELECT p.cd_empresa AS value, 
                   p.sigla AS text 
              FROM public.patrocinadoras p
             WHERE 0 < (SELECT COUNT(*)
                          FROM projetos.pos_venda_participante pvp
                         WHERE pvp.cd_empresa  = p.cd_empresa
                           AND pvp.dt_exclusao IS NULL
                         LIMIT 1)
             ORDER BY p.nome_empresa ASC;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function plano()
    {
        $qr_sql = "
            SELECT cd_plano AS value, 
                   descricao AS text 
              FROM planos 
             WHERE cd_plano > 0;";

        return $this->db->query($qr_sql)->result_array();
    }
    
    public function nome_empresa($cd_empresa)
    {
        $qr_sql = "
            SELECT nome_empresa
              FROM patrocinadoras
             WHERE cd_empresa = ".intval($cd_empresa).";";
        
        return $this->db->query($qr_sql)->row_array();
    }

    public function nome_plano($cd_plano)
    {
        $qr_sql = "
            SELECT descricao AS nome_plano
              FROM planos
             WHERE cd_plano = ".intval($cd_plano).";";
        
        return $this->db->query($qr_sql)->row_array();
    }
    
    public function total_ingressos($args = array())
    {
        $qr_sql = "
            SELECT COUNT(*) AS qt_total,
                   '".$args['dt_ini']."' AS dt_ingresso_ini,
				   '".$args['dt_fim']."' AS dt_ingresso_fim
			  FROM public.titulares t
              JOIN planos_patrocinadoras pp
                ON pp.cd_empresa = t.cd_empresa
			 WHERE DATE_TRUNC('day',t.dt_ingresso_eletro) BETWEEN TO_DATE('".$args['dt_ini']."' ,'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')
               ".(trim($args['cd_empresa']) != '' ? "AND t.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_plano']) != '' ? "AND pp.cd_plano = ".intval($args['cd_plano']) : "").";";
        
        return $this->db->query($qr_sql)->row_array();
    }
    
    public function total_posvenda($args = array())
    {
        $qr_sql = "
            SELECT COUNT(*) AS qt_total
              FROM projetos.pos_venda_participante pvp
              JOIN planos_patrocinadoras pp
                ON pp.cd_empresa = pvp.cd_empresa
             WHERE pvp.dt_exclusao IS NULL
               AND DATE_TRUNC('day',pvp.dt_inicio) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
               ".(trim($args['cd_empresa']) != '' ? "AND pvp.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_plano']) != '' ? "AND pp.cd_plano = ".intval($args['cd_plano']) : "").";";
        
        return $this->db->query($qr_sql)->row_array();
    }
    
    public function total_posvenda_realizado($args = array())
    {
        $qr_sql = "
            SELECT COUNT(*) AS qt_total
              FROM projetos.pos_venda_participante pvp
              JOIN planos_patrocinadoras pp
                ON pp.cd_empresa = pvp.cd_empresa
             WHERE pvp.dt_final    IS NOT NULL
               AND pvp.dt_exclusao IS NULL
               AND DATE_TRUNC('day',pvp.dt_inicio) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
               ".(trim($args['cd_empresa']) != '' ? "AND pvp.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_plano']) != '' ? "AND pp.cd_plano = ".intval($args['cd_plano']) : "").";";
        
        return $this->db->query($qr_sql)->row_array();
    }
    
    public function relatorio_pergunta($args = array())
    {
        $qr_sql = "
            SELECT pvq.nr_ordem,
				   pvq.cd_pos_venda_pergunta,
				   pvq.ds_pergunta
			  FROM projetos.pos_venda_participante pvp
			  JOIN projetos.pos_venda_pergunta pvq
				ON pvq.cd_pos_venda = 27
			   AND pvp.dt_final   IS NOT NULL
			   AND DATE_TRUNC('day',pvp.dt_inicio) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
			 GROUP BY pvq.nr_ordem,
					  pvq.cd_pos_venda_pergunta,
					  pvq.ds_pergunta
			 ORDER BY pvq.nr_ordem,
					  pvq.cd_pos_venda_pergunta,
					  pvq.ds_pergunta;";

        return $this->db->query($qr_sql)->result_array();
    }
    
    public function relatorio_respostas($args = array())
    {
        $qr_sql = "
            SELECT pvq.nr_ordem,
				   pvq.cd_pos_venda_pergunta,
				   pvq.ds_pergunta,
				   pvr.nr_ordem,
				   pvpr.cd_pos_venda_resposta,
				   pvr.ds_resposta,
				   COUNT(*) AS  qt_total	   
			  FROM projetos.pos_venda_participante pvp
			  JOIN projetos.pos_venda_pergunta pvq
				ON pvq.cd_pos_venda = pvp.cd_pos_venda
			  JOIN projetos.pos_venda_resposta pvr
    			ON pvr.cd_pos_venda_pergunta = pvq.cd_pos_venda_pergunta
			  JOIN projetos.pos_venda_participante_resposta pvpr
				ON pvpr.cd_pos_venda_participante = pvp.cd_pos_venda_participante
			   AND pvpr.cd_pos_venda_resposta     = pvr.cd_pos_venda_resposta 
              JOIN planos_patrocinadoras pp
                ON pp.cd_empresa = pvp.cd_empresa
			 WHERE pvq.cd_pos_venda_pergunta_pai = ".intval($args['cd_pos_venda_pergunta'])."
			   AND pvp.dt_final   IS NOT NULL
			   AND DATE_TRUNC('day',pvp.dt_inicio) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
               ".(trim($args['cd_empresa']) != '' ? "AND pvp.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_plano']) != '' ? "AND pp.cd_plano = ".intval($args['cd_plano']) : "")."
			 GROUP BY pvq.nr_ordem,
				      pvq.cd_pos_venda_pergunta,
					  pvq.ds_pergunta,
					  pvpr.cd_pos_venda_resposta,          
					  pvr.nr_ordem,
					  pvr.ds_resposta
			 ORDER BY pvq.nr_ordem,
					  pvq.cd_pos_venda_pergunta,
					  pvq.ds_pergunta,
					  pvr.nr_ordem,
					  pvpr.cd_pos_venda_resposta,         
					  pvr.ds_resposta;";
        
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function relatorio_respostas_complemento($args=array())
    {
        $qr_sql = "
            SELECT pvq.cd_pos_venda_pergunta,
				   pvpr.cd_pos_venda_resposta,
				   pvr.ds_resposta,
				   pvpr.complemento
			  FROM projetos.pos_venda_participante pvp
			  JOIN projetos.pos_venda_pergunta pvq
				ON pvq.cd_pos_venda = pvp.cd_pos_venda
			  JOIN projetos.pos_venda_resposta pvr
				ON pvr.cd_pos_venda_pergunta = pvq.cd_pos_venda_pergunta
			  JOIN projetos.pos_venda_participante_resposta pvpr
				ON pvpr.cd_pos_venda_participante = pvp.cd_pos_venda_participante
			   AND pvpr.cd_pos_venda_resposta     = pvr.cd_pos_venda_resposta 
              JOIN planos_patrocinadoras pp
                ON pp.cd_empresa = pvp.cd_empresa
			 WHERE pvp.dt_final   IS NOT NULL
			   AND DATE_TRUNC('day',pvp.dt_inicio) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
			   AND pvpr.complemento IS NOT NULL				
			   AND pvq.cd_pos_venda_pergunta_pai = ".intval($args['cd_pos_venda_pergunta'])."
               ".(trim($args['cd_empresa']) != '' ? "AND pvp.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_plano']) != '' ? "AND pp.cd_plano = ".intval($args['cd_plano']) : "").";";
        
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function resposta_participante($cd_pos_venda_participante)
    {
        $qr_sql = "
            SELECT p.cd_pos_venda_participante,
                   c.cd_empresa,
                   c.cd_registro_empregado,
                   c.seq_dependencia,
                   c.nome,
                   TO_CHAR(p.dt_inicio, 'DD/MM/YYYY') AS dt_inicio, 
                   TO_CHAR(p.dt_final, 'DD/MM/YYYY') AS dt_final
              FROM projetos.pos_venda_participante p
              JOIN public.participantes c 
                ON p.cd_empresa            = c.cd_empresa 
               AND p.cd_registro_empregado = c.cd_registro_empregado 
               AND p.seq_dependencia       = c.seq_dependencia 
             WHERE p.cd_pos_venda_participante = ".intval($cd_pos_venda_participante)."
               AND p.dt_exclusao IS NULL;";
        
        return $this->db->query($qr_sql)->row_array();
    }

    public function perguntas($cd_pos_venda_participante)
    {
        $qr_sql = "
            SELECT b.cd_pos_venda_pergunta,
                   b.ds_pergunta
              FROM projetos.pos_venda_participante a
              JOIN projetos.pos_venda_pergunta b 
                ON a.cd_pos_venda = b.cd_pos_venda
             WHERE a.cd_pos_venda_participante = ".intval($cd_pos_venda_participante)."
               AND a.dt_exclusao IS NULL 
               AND b.dt_exclusao IS NULL
             ORDER BY nr_ordem ASC;";
        
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function respostas($cd_pos_venda_participante, $cd_pos_venda_pergunta)
    {
        $qr_sql = "
            SELECT a.ds_resposta,
                   b.complemento
              FROM projetos.pos_venda_resposta a
              JOIN projetos.pos_venda_participante_resposta b
                ON a.cd_pos_venda_resposta = b.cd_pos_venda_resposta
             WHERE a.cd_pos_venda_pergunta     = ".intval($cd_pos_venda_pergunta)."
               AND b.cd_pos_venda_participante = ".intval($cd_pos_venda_participante)."
             ORDER BY a.nr_ordem ASC;";
        
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function listar_acompanhamento($cd_pos_venda_participante)
    {
        $qr_sql = "
            SELECT pvpa.cd_pos_venda_participante_acompanhamento, 
                   pvpa.cd_pos_venda_participante, 
                   pvpa.acompanhamento, 
                   TO_CHAR(pvpa.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   pvpa.cd_usuario_inclusao,
                   uc.nome
              FROM projetos.pos_venda_participante_acompanhamento pvpa
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pvpa.cd_usuario_inclusao
             WHERE pvpa.dt_exclusao IS NULL
               AND pvpa.cd_pos_venda_participante = ".intval($cd_pos_venda_participante)."
             ORDER BY pvpa.dt_inclusao DESC;";
        
		return $this->db->query($qr_sql)->result_array();
    }
    
    public function salvar_acompanhamento($args = array())
    {
        $qr_sql = " 
            INSERT INTO projetos.pos_venda_participante_acompanhamento
                 (
                   cd_pos_venda_participante, 
                   acompanhamento, 
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                   ".intval($args['cd_pos_venda_participante']).",
                   ".str_escape($args['acompanhamento']).",
                   ".intval($args['cd_usuario'])."
                 );";
        
        $this->db->query($qr_sql);
    }    
}
?>