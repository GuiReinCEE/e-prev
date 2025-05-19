<?php

class Edicao_model extends Model
{
	function __construct()
  {
      parent::Model();
  }

    public function get_data_base()
    {
    	$qr_sql = "
    		SELECT DISTINCT dt_base_extrato AS value,
			       TO_CHAR(dt_base_extrato, 'DD/MM/YYYY') AS text
			  FROM meu_retrato.edicao 
			 WHERE dt_exclusao IS NULL
			 ORDER BY value DESC";

    	return $this->db->query($qr_sql)->result_array();
    }
   
    public function listar($args)
    {
    	$qr_sql = "
    		SELECT e.cd_edicao,
    		       p.sigla,
    		       pl.descricao AS plano,
    		       e.nr_extrato,
                   TO_CHAR(cee.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_geracao_extrato,
    		       TO_CHAR(e.dt_base_extrato, 'DD/MM/YYYY') AS dt_base_extrato,
    		       TO_CHAR(e.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(e.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   TO_CHAR(e.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
                   funcoes.get_usuario_nome(e.cd_usuario_alteracao) AS ds_usuario_alteracao,
    		       funcoes.get_usuario_nome(e.cd_usuario_liberacao_informatica) AS ds_usuario_informatica,
                   TO_CHAR(e.dt_liberacao_informatica, 'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao_informatica,
                   funcoes.get_usuario_nome(e.cd_usuario_liberacao_atuarial) AS ds_usuario_atuarial,
                   TO_CHAR(e.dt_liberacao_atuarial, 'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao_atuarial,
                   TO_CHAR(e.dt_liberacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao_comunicacao,
                   funcoes.get_usuario_nome(e.cd_usuario_liberacao) AS ds_usuario_comunicacao,
                   TO_CHAR(e.dt_cancelamento_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento_envio,
                   funcoes.get_usuario_nome(e.cd_usuario_cancelamento_envio) AS ds_usuario_cancelamento_envio,
                   (SELECT COUNT(*)
                      FROM meu_retrato.edicao_participante ep 
                     WHERE ep.cd_edicao = e.cd_edicao
                       AND dt_exclusao IS NULL) AS tl_participante,
                   e.qt_participante,
                   (CASE WHEN tp_participante = 'ATIV'  THEN 'Ativo'
                         WHEN tp_participante = 'APOS'  THEN 'Aposentado'
                         WHEN tp_participante = 'APOSM' THEN 'Aposentado Migrado'
                         WHEN tp_participante = 'EXAU'  THEN 'Ex-Autárquico'
                         ELSE ''
                   END) AS tipo_participante,
                   (CASE WHEN tp_participante = 'ATIV'  THEN 'label-success'
                         WHEN tp_participante = 'APOS'  THEN 'label-info'
                         WHEN tp_participante = 'APOSM' THEN 'label-info'
                         WHEN tp_participante = 'EXAU'  THEN 'label-warning'
                         ELSE ''
                   END) AS class_tipo_participante,
				   e.fl_gerar
    		  FROM meu_retrato.edicao e 
    		  JOIN public.patrocinadoras p
    		    ON p.cd_empresa = e.cd_empresa
    		  JOIN public.planos pl
    		    ON pl.cd_plano = e.cd_plano
              LEFT JOIN projetos.controles_extrato_envio cee
                ON cee.cd_empresa = e.cd_empresa
               AND cee.cd_plano   = e.cd_plano
               AND cee.nr_extrato = e.nr_extrato
    		 WHERE e.dt_exclusao IS NULL
    		   ".(trim($args['cd_empresa']) != '' ? "AND e.cd_empresa = ".intval($args['cd_empresa']) : "")."
    		   ".(trim($args['cd_plano']) != '' ? "AND e.cd_plano = ".intval($args['cd_plano']) : "")."
    		   ".(trim($args['nr_extrato']) != '' ? "AND e.nr_extrato = ".intval($args['nr_extrato']) : "")."
    		   ".(trim($args['dt_base_extrato']) != '' ? "AND e.dt_base_extrato = '".trim($args['dt_base_extrato'])."'" : "")."
			   ".(((trim($args['dt_ini']) != '') and (trim($args['dt_fim']) != ''))? "AND e.dt_base_extrato BETWEEN TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY')" : "")."
			   ".(trim($args['cd_gerencia']) != 'GS' ? "AND e.dt_liberacao_informatica IS NOT NULL" : "")."
			   ".(trim($args['fl_liberado_ti']) == 'S' ? "AND e.dt_liberacao_informatica IS NOT NULL" : "")."
			   ".(trim($args['fl_liberado_ti']) == 'N' ? "AND e.dt_liberacao_informatica IS NULL" : "")."
			   ".(trim($args['fl_liberado_prev']) == 'S' ? "AND e.dt_liberacao_atuarial IS NOT NULL" : "")."
			   ".(trim($args['fl_liberado_prev']) == 'N' ? "AND e.dt_liberacao_atuarial IS NULL" : "")."
			   ".(trim($args['fl_liberado_com']) == 'S' ? "AND e.dt_liberacao IS NOT NULL" : "")."
			   ".(trim($args['fl_liberado_com']) == 'N' ? "AND e.dt_liberacao IS NULL" : "")."			   
               ".(trim($args['tp_participante']) != '' ? "AND e.tp_participante = '".trim($args['tp_participante'])."'" : "")."
    		 ORDER BY e.dt_base_extrato DESC, plano DESC";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_edicao)
    {
        $qr_sql = "
            SELECT e.cd_edicao,
                   e.cd_plano,
                   e.cd_empresa,
                   p.sigla,
                   pl.descricao AS plano,
                   e.nr_extrato,
                   TO_CHAR(e.dt_base_extrato, 'YYYY-MM-DD') AS dt_base_comparativo,
                   TO_CHAR(e.dt_base_extrato, 'DD/MM/YYYY') AS dt_base_extrato,
                   TO_CHAR(e.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(e.cd_usuario_inclusao) AS usuario_inclusao,
                   TO_CHAR(e.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
                   funcoes.get_usuario_nome(e.cd_usuario_alteracao) AS usuario_alteracao,
                   e.ficaadica,
                   e.comentario_rentabilidade,
                   e.arquivo_comparativo,
                   e.arquivo_comparativo_nome,
                   e.arquivo_premissas_atuariais,
                   e.arquivo_premissas_atuariais_nome,
                   funcoes.get_usuario_nome(e.cd_usuario_liberacao_informatica) AS usuario_informatica,
                   TO_CHAR(e.dt_liberacao_informatica, 'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao_informatica,
                   funcoes.get_usuario_nome(e.cd_usuario_liberacao_atuarial) AS usuario_atuarial,
                   TO_CHAR(e.dt_liberacao_atuarial, 'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao_atuarial,
                   TO_CHAR(e.dt_liberacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao_comunicacao,
                   funcoes.get_usuario_nome(e.cd_usuario_liberacao) AS usuario_comunicacao,
                   e.cd_divulgacao,
                   (SELECT COUNT(*)
                      FROM meu_retrato.edicao_participante ep 
                     WHERE ep.cd_edicao = e.cd_edicao) AS tl_participante,
                   e.fl_gerar,
                   e.tp_participante,
				   TO_CHAR(e.dt_equilibrio, 'DD/MM/YYYY') AS dt_equilibrio,
				   e.ds_equilibrio,
				   ec.cd_edicao_comparativo,
				   TO_CHAR(ec.dt_inicial, 'DD/MM/YYYY') AS dt_inicial_comparativo,
				   TO_CHAR(ec.dt_final, 'DD/MM/YYYY') AS dt_final_comparativo,
				   ec.vl_plano, 
				   ec.vl_cdi, 
				   ec.vl_poupanca, 
				   ec.vl_inpc, 
				   ec.vl_igpm, 
				   ec.vl_ipca_ibge				   
              FROM meu_retrato.edicao e 
              JOIN public.patrocinadoras p
                ON p.cd_empresa = e.cd_empresa
              JOIN public.planos pl
                ON pl.cd_plano = e.cd_plano
			  LEFT JOIN meu_retrato.edicao_comparativo ec
			    ON ec.dt_exclusao IS NULL
			   AND ec.cd_edicao = e.cd_edicao
             WHERE e.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args)
    {
        $qr_sql = "
            SELECT edicao_incluir AS cd_edicao FROM meu_retrato.edicao_incluir(
                ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                ".(trim($args['cd_plano']) != '' ? intval($args['cd_plano']) : "DEFAULT").",
                ".(trim($args['dt_base_extrato']) != '' ? "TO_DATE('".$args['dt_base_extrato']."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".(trim($args['tp_participante']) != '' ? "'".trim($args['tp_participante'])."'" : "DEFAULT" ).",
                ".intval($args['cd_usuario'])."
            );";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_plano_unico($args)
    {
        $qr_sql = "
            SELECT edicao_incluir_plano_unico AS cd_edicao FROM meu_retrato.edicao_incluir_plano_unico(
                ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                ".(trim($args['cd_plano']) != '' ? intval($args['cd_plano']) : "DEFAULT").",
                ".(trim($args['dt_base_extrato']) != '' ? "TO_DATE('".$args['dt_base_extrato']."', 'DD/MM/YYYY')" : "DEFAULT").",
				".(trim($args['tp_participante']) != '' ? "'".trim($args['tp_participante'])."'" : "DEFAULT" ).",
                ".intval($args['cd_usuario'])."
            );";

        return $this->db->query($qr_sql)->row_array();
    }

    public function gerar_instituidor_aposentado($cd_usuario)
    {
        $qr_sql = "
            SELECT meu_retrato.edicao_incluir(e.cd_empresa::integer, v.cd_plano::integer, ce.data_base::date, 'APOS', ".intval($cd_usuario).") AS cd_edicao
              FROM public.extr_controle_extrato c
              JOIN public.extr_meses_extrato m
                ON m.seq_contr_extr = c.seq_contr_extr
              JOIN public.extr_parametrizacao p
                ON p.seq_param_extr    = c.seq_param_extr
              JOIN public.extr_extratos_participantes e
                ON e.seq_mes_extr = m.seq_mes_extr 
              JOIN public.extr_param_mov v
                ON v.seq_param_mov = e.seq_param_mov
              JOIN public.extr_tipo_mov t
                ON t.seq_tp_mov = v.seq_tp_mov
              JOIN controles_extratos ce
                ON ce.dt_liberacao IS NOT NULL
               AND ce.data_base = c.dt_fim
               AND ce.cd_empresa = e.cd_empresa
               AND ce.cd_plano   = v.cd_plano 
              JOIN gestao.pendencia_minha pm 
                ON pm.cd_pendencia_minha = 'MEURET'::text
             WHERE c.dt_fim >= '2020-08-31'::date
               AND (SELECT COUNT(*) FROM meu_retrato.edicao ed WHERE ed.cd_empresa = e.cd_empresa AND ed.cd_plano = v.cd_plano AND ed.dt_base_extrato = ce.data_base AND ed.tp_participante = 'APOS') = 0
             GROUP BY e.cd_empresa::integer,  v.cd_plano::integer, ce.data_base::date;";
        
        return $this->db->query($qr_sql)->row_array();
    }

    public function gerar_plano_unico($cd_edicao, $cd_empresa, $cd_usuario)
    {
        $qr_sql = "
            SELECT meu_retrato.gerar_participante(".intval($cd_edicao).", m.cd_empresa, m.cd_registro_empregado, m.seq_dependencia, ".intval($cd_usuario).")
              FROM meu_retrato.participantes_ativo_plano_unico(
																".intval($cd_empresa).",
																(SELECT e.dt_base_extrato::DATE FROM meu_retrato.edicao e WHERE e.cd_edicao = ".intval($cd_edicao)."),
																(SELECT e.tp_participante FROM meu_retrato.edicao e WHERE e.cd_edicao = ".intval($cd_edicao).")
															   ) m
				  ";
		
		/*
		$qr_sql = "
            SELECT meu_retrato.gerar_participante(".intval($cd_edicao).", m.cd_empresa, m.cd_registro_empregado, m.seq_dependencia, ".intval($cd_usuario).")
              FROM meu_retrato_controle_etapas r, 
                   meu_retrato_plano_unico m
             WHERE m.dt_referencia       = (SELECT e.dt_base_extrato::DATE FROM meu_retrato.edicao e WHERE e.cd_edicao = ".intval($cd_edicao).")
			   AND m.dt_confirma_calculo IS NOT NULL -- cálculo efetuado
               AND m.cd_empresa          = ".intval($cd_empresa)." -- empresa
               AND m.status              = 2 -- Somente os selecionados/confirmados para efetuar o cálculo
               AND m.cd_motivo           IN (1, 2) -- Somente os re's com 36 meses e os Re's novos (sem 36 meses)
               AND r.dt_referencia       = m.dt_referencia
               AND r.cd_empresa          = m.cd_empresa
               AND r.cd_etapa            = 'CNFRT' -- etapa de confirmação da geração do meu retrato
               AND r.dt_etapa            IS NOT NULL; -- indica que a etapa foi finalizada";
		*/
		$this->db->query($qr_sql);
    }

    public function atualizar($cd_edicao, $args)
    {
        $qr_sql = "
            UPDATE meu_retrato.edicao
               SET cd_empresa                       = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                   cd_plano                         = ".(trim($args['cd_plano']) != '' ? intval($args['cd_plano']) : "DEFAULT").",
                   nr_extrato                       = ".(trim($args['nr_extrato']) != '' ? intval($args['nr_extrato']) : "DEFAULT").",
                   ficaadica                        = ".(trim($args['ficaadica']) != '' ? str_escape($args['ficaadica']) : "DEFAULT").",
                   dt_equilibrio                    = ".(trim($args['dt_equilibrio']) != '' ? "TO_DATE('".$args['dt_equilibrio']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   ds_equilibrio                    = ".(trim($args['ds_equilibrio']) != '' ? str_escape($args['ds_equilibrio']) : "DEFAULT").",
                   comentario_rentabilidade         = ".(trim($args['comentario_rentabilidade']) != '' ? str_escape($args['comentario_rentabilidade']) : "DEFAULT").",
                   arquivo_comparativo_nome         = ".(trim($args['arquivo_comparativo_nome']) != '' ? str_escape($args['arquivo_comparativo_nome']) : "DEFAULT").",
                   arquivo_comparativo              = ".(trim($args['arquivo_comparativo']) != '' ? str_escape($args['arquivo_comparativo']) : "DEFAULT").",
                   arquivo_premissas_atuariais_nome = ".(trim($args['arquivo_premissas_atuariais_nome']) != '' ? str_escape($args['arquivo_premissas_atuariais_nome']) : "DEFAULT").",
                   arquivo_premissas_atuariais      = ".(trim($args['arquivo_premissas_atuariais']) != '' ? str_escape($args['arquivo_premissas_atuariais']) : "DEFAULT").",
                   cd_usuario_alteracao             = ".intval($args['cd_usuario']).",
                   dt_alteracao                     = CURRENT_TIMESTAMP
             WHERE cd_edicao = ".intval($cd_edicao).";";
			 
		if(intval($args['cd_edicao_comparativo']) == 0)
		{
			$qr_sql.= "
						INSERT INTO meu_retrato.edicao_comparativo
							 (
								cd_usuario_inclusao, 
								cd_usuario_alteracao, 
								cd_edicao, 
								dt_inicial, 
								dt_final, 
								vl_plano, 
								vl_cdi, 
								vl_poupanca, 
								vl_inpc, 
								vl_igpm, 
								vl_ipca_ibge
							  )
						 VALUES 
							  (
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario']).",
								".intval($cd_edicao).",
								".(trim($args['dt_inicial_comparativo']) != '' ? "TO_DATE('".$args['dt_inicial_comparativo']."','DD/MM/YYYY')" : "DATE_TRUNC('month', (((DATE_TRUNC('month', TO_DATE('".$args['dt_base_extrato']."','DD/MM/YYYY'))) - '10 years'::INTERVAL) + '1 month'::INTERVAL)::DATE)").",
								".(trim($args['dt_final_comparativo']) != '' ? "TO_DATE('".$args['dt_final_comparativo']."','DD/MM/YYYY')" : "DATE_TRUNC('month', TO_DATE('".$args['dt_base_extrato']."','DD/MM/YYYY'))").",
								".(trim($args['comparativo_vl_plano']) != '' ? floatval($args['comparativo_vl_plano']) : "DEFAULT").",
								".(trim($args['comparativo_vl_cdi']) != '' ? floatval($args['comparativo_vl_cdi']) : "DEFAULT").",
								".(trim($args['comparativo_vl_poupanca']) != '' ? floatval($args['comparativo_vl_poupanca']) : "DEFAULT").",
								".(trim($args['comparativo_vl_inpc']) != '' ? floatval($args['comparativo_vl_inpc']) : "DEFAULT").",
								".(trim($args['comparativo_vl_igpm']) != '' ? floatval($args['comparativo_vl_igpm']) : "DEFAULT").",
								".(trim($args['comparativo_vl_ipca_ibge']) != '' ? floatval($args['comparativo_vl_ipca_ibge']) : "DEFAULT")."
							  );
			          ";
		}
		else
		{
			$qr_sql.= "
						UPDATE meu_retrato.edicao_comparativo
						   SET dt_inicial           = ".(trim($args['dt_inicial_comparativo']) != '' ? "TO_DATE('".$args['dt_inicial_comparativo']."','DD/MM/YYYY')" : "DEFAULT").", 
						       dt_final             = ".(trim($args['dt_final_comparativo']) != '' ? "TO_DATE('".$args['dt_final_comparativo']."','DD/MM/YYYY')" : "DEFAULT").", 
							   vl_plano             = ".(trim($args['comparativo_vl_plano']) != '' ? floatval($args['comparativo_vl_plano']) : "DEFAULT").",
							   vl_cdi               = ".(trim($args['comparativo_vl_cdi']) != '' ? floatval($args['comparativo_vl_cdi']) : "DEFAULT").",
							   vl_poupanca          = ".(trim($args['comparativo_vl_poupanca']) != '' ? floatval($args['comparativo_vl_poupanca']) : "DEFAULT").",
							   vl_inpc              = ".(trim($args['comparativo_vl_inpc']) != '' ? floatval($args['comparativo_vl_inpc']) : "DEFAULT").",
							   vl_igpm              = ".(trim($args['comparativo_vl_igpm']) != '' ? floatval($args['comparativo_vl_igpm']) : "DEFAULT").",
							   vl_ipca_ibge         = ".(trim($args['comparativo_vl_ipca_ibge']) != '' ? floatval($args['comparativo_vl_ipca_ibge']) : "DEFAULT").",
                               cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                               dt_alteracao         = CURRENT_TIMESTAMP
                         WHERE cd_edicao_comparativo = ".intval($args['cd_edicao_comparativo']).";		   
			          ";			
		}

		#echo "<PRE>".$qr_sql; print_r($args); exit;

        $this->db->query($qr_sql);
    }

    public function gerar($cd_edicao, $cd_usuario)
    {
        $qr_sql = "
					UPDATE meu_retrato.edicao
					   SET fl_gerar             = 'S',
						   cd_usuario_alteracao = ".intval($cd_usuario).",
						   dt_alteracao         = CURRENT_TIMESTAMP
					 WHERE cd_edicao = ".intval($cd_edicao).";
			      ";

        $this->db->query($qr_sql);
    }

    public function libera_informatica($cd_edicao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE meu_retrato.edicao
               SET cd_usuario_liberacao_informatica = ".intval($cd_usuario).",
                   dt_liberacao_informatica         = CURRENT_TIMESTAMP
             WHERE cd_edicao = ".intval($cd_edicao).";";

        $this->db->query($qr_sql);
    }

    public function libera_atuarial($cd_edicao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE meu_retrato.edicao
               SET cd_usuario_liberacao_atuarial = ".intval($cd_usuario).",
                   dt_liberacao_atuarial         = CURRENT_TIMESTAMP
             WHERE cd_edicao = ".intval($cd_edicao).";";

        $this->db->query($qr_sql);
    }

    public function atualizar_comunicacao($cd_edicao, $args)
    {
        $qr_sql = "
            UPDATE meu_retrato.edicao
               SET ficaadica                        = ".(trim($args['ficaadica']) != '' ? str_escape($args['ficaadica']) : "DEFAULT").",
                   dt_equilibrio                    = ".(trim($args['dt_equilibrio']) != '' ? "TO_DATE('".$args['dt_equilibrio']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   ds_equilibrio                    = ".(trim($args['ds_equilibrio']) != '' ? str_escape($args['ds_equilibrio']) : "DEFAULT").",			   
                   comentario_rentabilidade         = ".(trim($args['comentario_rentabilidade']) != '' ? str_escape($args['comentario_rentabilidade']) : "DEFAULT").",
                   arquivo_comparativo_nome         = ".(trim($args['arquivo_comparativo_nome']) != '' ? str_escape($args['arquivo_comparativo_nome']) : "DEFAULT").",
                   arquivo_comparativo              = ".(trim($args['arquivo_comparativo']) != '' ? str_escape($args['arquivo_comparativo']) : "DEFAULT").",
                   arquivo_premissas_atuariais_nome = ".(trim($args['arquivo_premissas_atuariais_nome']) != '' ? str_escape($args['arquivo_premissas_atuariais_nome']) : "DEFAULT").",
                   arquivo_premissas_atuariais      = ".(trim($args['arquivo_premissas_atuariais']) != '' ? str_escape($args['arquivo_premissas_atuariais']) : "DEFAULT").",
                   cd_usuario_alteracao             = ".intval($args['cd_usuario']).",
                   dt_alteracao                     = CURRENT_TIMESTAMP
             WHERE cd_edicao = ".intval($cd_edicao).";";
			 
			 
		if(intval($args['cd_edicao_comparativo']) == 0)
		{
			$qr_sql.= "
						INSERT INTO meu_retrato.edicao_comparativo
							 (
								cd_usuario_inclusao, 
								cd_usuario_alteracao, 
								cd_edicao, 
								dt_inicial, 
								dt_final, 
								vl_plano, 
								vl_cdi, 
								vl_poupanca, 
								vl_inpc, 
								vl_igpm, 
								vl_ipca_ibge
							  )
						 VALUES 
							  (
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario']).",
								".intval($cd_edicao).",
								".(trim($args['dt_inicial_comparativo']) != '' ? "TO_DATE('".$args['dt_inicial_comparativo']."','DD/MM/YYYY')" : "DATE_TRUNC('month', (((DATE_TRUNC('month', TO_DATE('".$args['dt_base_extrato']."','DD/MM/YYYY'))) - '10 years'::INTERVAL) + '1 month'::INTERVAL)::DATE)").",
								".(trim($args['dt_final_comparativo']) != '' ? "TO_DATE('".$args['dt_final_comparativo']."','DD/MM/YYYY')" : "DATE_TRUNC('month', TO_DATE('".$args['dt_base_extrato']."','DD/MM/YYYY'))").",
								".(trim($args['comparativo_vl_plano']) != '' ? floatval($args['comparativo_vl_plano']) : "DEFAULT").",
								".(trim($args['comparativo_vl_cdi']) != '' ? floatval($args['comparativo_vl_cdi']) : "DEFAULT").",
								".(trim($args['comparativo_vl_poupanca']) != '' ? floatval($args['comparativo_vl_poupanca']) : "DEFAULT").",
								".(trim($args['comparativo_vl_inpc']) != '' ? floatval($args['comparativo_vl_inpc']) : "DEFAULT").",
								".(trim($args['comparativo_vl_igpm']) != '' ? floatval($args['comparativo_vl_igpm']) : "DEFAULT").",
								".(trim($args['comparativo_vl_ipca_ibge']) != '' ? floatval($args['comparativo_vl_ipca_ibge']) : "DEFAULT")."
							  );
			          ";
		}
		else
		{
			$qr_sql.= "
						UPDATE meu_retrato.edicao_comparativo
						   SET dt_inicial           = ".(trim($args['dt_inicial_comparativo']) != '' ? "TO_DATE('".$args['dt_inicial_comparativo']."','DD/MM/YYYY')" : "DEFAULT").", 
						       dt_final             = ".(trim($args['dt_final_comparativo']) != '' ? "TO_DATE('".$args['dt_final_comparativo']."','DD/MM/YYYY')" : "DEFAULT").", 
							   vl_plano             = ".(trim($args['comparativo_vl_plano']) != '' ? floatval($args['comparativo_vl_plano']) : "DEFAULT").",
							   vl_cdi               = ".(trim($args['comparativo_vl_cdi']) != '' ? floatval($args['comparativo_vl_cdi']) : "DEFAULT").",
							   vl_poupanca          = ".(trim($args['comparativo_vl_poupanca']) != '' ? floatval($args['comparativo_vl_poupanca']) : "DEFAULT").",
							   vl_inpc              = ".(trim($args['comparativo_vl_inpc']) != '' ? floatval($args['comparativo_vl_inpc']) : "DEFAULT").",
							   vl_igpm              = ".(trim($args['comparativo_vl_igpm']) != '' ? floatval($args['comparativo_vl_igpm']) : "DEFAULT").",
							   vl_ipca_ibge         = ".(trim($args['comparativo_vl_ipca_ibge']) != '' ? floatval($args['comparativo_vl_ipca_ibge']) : "DEFAULT").",
                               cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                               dt_alteracao         = CURRENT_TIMESTAMP
                         WHERE cd_edicao_comparativo = ".intval($args['cd_edicao_comparativo']).";		   
			          ";				
		}			 

        $this->db->query($qr_sql);
    }

    public function libera($cd_edicao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE meu_retrato.edicao
               SET cd_usuario_liberacao = ".intval($cd_usuario).",
                   dt_liberacao         = CURRENT_TIMESTAMP
             WHERE cd_edicao = ".intval($cd_edicao).";";

        $this->db->query($qr_sql);
    }

    public function edicao_listar($cd_edicao, $nr_pagina, $qt_pagina, $args = array())
    {
      $qr_sql = "
        SELECT p.nome,
		       ep.cd_edicao_participante,
               e.cd_edicao,
               e.nr_extrato,
               TO_CHAR(e.dt_base_extrato,'DD/MM/YYYY') AS dt_base_extrato,
               TO_CHAR(e.dt_liberacao,'DD/MM/YYYY HH24:MI:SS') AS dt_liberacao,
               ep.cd_empresa,
               ep.cd_registro_empregado,
               ep.seq_dependencia,
               (CASE WHEN 
                  (
                    e.dt_inclusao 
                    > 
                    (
                      SELECT tp.dt_deslig_plano
                        FROM public.titulares_planos tp
                        JOIN public.titulares t
                          ON t.cd_empresa            = tp.cd_empresa            
                         AND t.cd_registro_empregado = tp.cd_registro_empregado 
                         AND t.seq_dependencia       = tp.seq_dependencia                  
                       WHERE tp.cd_empresa            = p.cd_empresa 
                         AND tp.cd_registro_empregado = p.cd_registro_empregado 
                         AND tp.seq_dependencia       = p.seq_dependencia  
                         AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
                                                           FROM public.titulares_planos tp1 
                                                          WHERE tp1.cd_empresa            = tp.cd_empresa 
                                                            AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
                                                            AND tp1.seq_dependencia       = tp.seq_dependencia)
                    )
                  ) THEN 'Sim'
                  ELSE 'Não'
               END) desligado,
                (CASE WHEN 
                  (
                    e.dt_inclusao 
                    > 
                    (
                      SELECT tp.dt_deslig_plano
                        FROM public.titulares_planos tp
                        JOIN public.titulares t
                          ON t.cd_empresa            = tp.cd_empresa            
                         AND t.cd_registro_empregado = tp.cd_registro_empregado 
                         AND t.seq_dependencia       = tp.seq_dependencia                  
                       WHERE tp.cd_empresa            = p.cd_empresa 
                         AND tp.cd_registro_empregado = p.cd_registro_empregado 
                         AND tp.seq_dependencia       = p.seq_dependencia  
                         AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
                                                           FROM public.titulares_planos tp1 
                                                          WHERE tp1.cd_empresa            = tp.cd_empresa 
                                                            AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
                                                            AND tp1.seq_dependencia       = tp.seq_dependencia)
                    )
                  ) THEN 'label-important'
                  ELSE 'label-success'
               END) class_desligado,

               'https://www.fundacaoceee.com.br/auto_atendimento_meu_retrato.php' AS url
          FROM meu_retrato.edicao e
          JOIN meu_retrato.edicao_participante ep
            ON ep.cd_edicao = e.cd_edicao
          JOIN participantes p
            ON p.cd_empresa            = ep.cd_empresa
           AND p.cd_registro_empregado = ep.cd_registro_empregado
           AND p.seq_dependencia       = ep.seq_dependencia
         WHERE e.dt_exclusao IS NULL
           AND ep.cd_edicao = ".intval($cd_edicao)."
           ".(intval($args['cd_registro_empregado']) > 0 ? "AND ep.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
           ".(trim($args['desligado']) != '' ? "AND '".trim($args['desligado'])."' =  
            (CASE WHEN 
                  (
                    e.dt_inclusao 
                    > 
                    (
                      SELECT tp.dt_deslig_plano
                        FROM public.titulares_planos tp
                        JOIN public.titulares t
                          ON t.cd_empresa            = tp.cd_empresa            
                         AND t.cd_registro_empregado = tp.cd_registro_empregado 
                         AND t.seq_dependencia       = tp.seq_dependencia                  
                       WHERE tp.cd_empresa            = p.cd_empresa 
                         AND tp.cd_registro_empregado = p.cd_registro_empregado 
                         AND tp.seq_dependencia       = p.seq_dependencia  
                         AND tp.dt_ingresso_plano     = (SELECT MAX(tp1.dt_ingresso_plano)
                                                           FROM public.titulares_planos tp1 
                                                          WHERE tp1.cd_empresa            = tp.cd_empresa 
                                                            AND tp1.cd_registro_empregado = tp.cd_registro_empregado 
                                                            AND tp1.seq_dependencia       = tp.seq_dependencia)
                    )
                  ) THEN 'S'
                  ELSE 'N'
               END) " : "")."
         ORDER BY p.nome ASC
         LIMIT ".intval($qt_pagina)."
         OFFSET ".$nr_pagina.";";

      return $this->db->query($qr_sql)->result_array();
    }

    public function get_edicao_replica($cd_edicao, $cd_plano, $tp_participante = '')
    {
        $qr_sql = "
            SELECT cd_edicao
              FROM meu_retrato.edicao 
             WHERE cd_plano  = ".intval($cd_plano)."
               AND cd_edicao != ".intval($cd_edicao)."
               AND dt_base_extrato = (SELECT dt_base_extrato FROM meu_retrato.edicao WHERE cd_edicao = ".intval($cd_edicao).") 
               ".(trim($tp_participante) != '' ? "AND tp_participante = '".trim($tp_participante)."'" : "")."
               AND dt_liberacao    IS NULL
               AND cd_edicao       > 1313;";

        return $this->db->query($qr_sql)->result_array();
    }
	
    function verificar_listar_maior(&$result, $args=array())
    {
        $qr_sql = "
					SELECT projetos.participante_nome(ep.cd_empresa, ep.cd_registro_empregado, ep.seq_dependencia) AS nome, 
					       'https://www.fundacaoceee.com.br/auto_atendimento_meu_retrato.php' AS url,
						   *
					  FROM meu_retrato.edicao_participante ep
					  JOIN meu_retrato.edicao_participante_dado epd
						ON epd.cd_edicao_participante = ep.cd_edicao_participante
					 WHERE ep.cd_edicao = ".intval($args['cd_edicao'])."
					   AND epd.cd_linha IN ('".implode("','",explode(",",trim($args['cd_item'])))."')
					 ORDER BY epd.vl_valor DESC
					 LIMIT ".intval($args['qt_amostra'])."	
                  ";
        #echo "<PRE>$qr_sql</PRE>"; exit;
		$result = $this->db->query($qr_sql);
    }	
	
    function verificar_listar_menor(&$result, $args=array())
    {
        $qr_sql = "
					SELECT projetos.participante_nome(ep.cd_empresa, ep.cd_registro_empregado, ep.seq_dependencia) AS nome, 
					       'https://www.fundacaoceee.com.br/auto_atendimento_meu_retrato.php' AS url,
						   *
					  FROM meu_retrato.edicao_participante ep
					  JOIN meu_retrato.edicao_participante_dado epd
						ON epd.cd_edicao_participante = ep.cd_edicao_participante
					 WHERE ep.cd_edicao = ".intval($args['cd_edicao'])."
					   AND epd.cd_linha IN ('".implode("','",explode(",",trim($args['cd_item'])))."')
					 ORDER BY epd.vl_valor ASC
					 LIMIT ".intval($args['qt_amostra'])."	
                  ";
        #echo "<PRE>$qr_sql</PRE>"; exit;
		$result = $this->db->query($qr_sql);
    }	
	
    function equilibrio_listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT ee.cd_edicao_equilibrio,
					       ee.nr_ano, 
						   ee.vl_provisao, 
						   ee.vl_cobertura
					  FROM meu_retrato.edicao_equilibrio ee
					 WHERE ee.cd_edicao = ".intval($args['cd_edicao'])."
					   AND ee.dt_exclusao IS NULL
					 ORDER BY nr_ano 
                  ";
        #echo "<PRE>$qr_sql</PRE>"; exit;
		$result = $this->db->query($qr_sql);
    }		
	
    function equilibrio_add($args)
    {
        $qr_sql = "
					INSERT INTO meu_retrato.edicao_equilibrio
					     (
							cd_edicao, 
							nr_ano, 
							vl_provisao, 
							vl_cobertura,
							cd_usuario_inclusao
						 )
					VALUES 
					     (
							".intval($args['cd_edicao']).",
							".intval($args['nr_ano']).",
							".floatval($args['vl_provisao']).",
							".floatval($args['vl_cobertura']).",
							".intval($args['cd_usuario'])."
						 );
                  ";

        $this->db->query($qr_sql);
    }

    function equilibrio_del($args)
    {
        $qr_sql = "
					UPDATE meu_retrato.edicao_equilibrio
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
					 WHERE cd_edicao_equilibrio = ".intval($args['cd_edicao_equilibrio']).";
                  ";

        $this->db->query($qr_sql);
    }	

    public function getIndice($args)
    {
      $qr_sql = "
                  SELECT (i.vl_indice/100) + 1 AS vl_cota, 
                         i.dt_indice AS dt_cota,  
                         TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
                         TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
                         TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
                    FROM autoatendimento.indice_mercado_valor i 
                   WHERE i.cd_indice_mercado = ".$args['cd_indice']."
                     AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
                     AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
                                           FROM autoatendimento.indice_mercado_valor i1
                                          WHERE i1.cd_indice_mercado = i.cd_indice_mercado 
                                          GROUP BY DATE_TRUNC('month', i1.dt_indice))
                     AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
                     AND i.dt_exclusao IS NULL
                   ORDER BY i.dt_indice      
    		        ";

    	return $this->db->query($qr_sql)->result_array();
    }    

    public function getRentabilidade($args)
    {
      $qr_sql = "
                  SELECT * 
                    FROM meu_retrato.rentabilidade_periodo
                         (
                          ".intval($args['cd_empresa']).",
                          ".intval($args['cd_plano']).",
                          TO_DATE('".trim($args['dt_ini'])."','DD/MM/YYYY'),
                          TO_DATE('".trim($args['dt_fim'])."','DD/MM/YYYY')
                         )
                   WHERE nr_cota_mes IS NOT NULL
                     AND nr_cota_acumulada IS NOT NULL
                   ORDER BY dt_indice DESC
                   LIMIT 1     
    		        ";
      #echo $qr_sql;
    	return $this->db->query($qr_sql)->row_array();
    }    
    
    public function getPoupanca($args)
    {
      $qr_sql = "
                  SELECT (i.vl_indice/100) + 1 AS vl_cota, 
                         i.dt_indice AS dt_cota,  
                         TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
                         TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
                         TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
                    FROM autoatendimento.indice_mercado_valor i 
                   WHERE i.cd_indice_mercado = 2
                     AND i.dt_indice < TO_DATE('01/09/2013','DD/MM/YYYY')
                     AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
                     AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
                                           FROM autoatendimento.indice_mercado_valor i1
                                          WHERE i1.cd_indice_mercado = i.cd_indice_mercado 
                                          GROUP BY DATE_TRUNC('month', i1.dt_indice))
                     AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
                     AND i.dt_exclusao IS NULL

                   UNION 
                     
                  SELECT (i.vl_indice/100) + 1 AS vl_cota, 
                         i.dt_indice AS dt_cota,  
                         TO_CHAR(i.dt_indice, 'DD/MM') AS dt_dia, 
                         TO_CHAR(i.dt_indice, 'MM') AS dt_mes,
                         TO_CHAR(i.dt_indice, 'DD/MM/YYYY') AS dt_referencia
                    FROM autoatendimento.indice_mercado_valor i 
                   WHERE i.cd_indice_mercado = 8
                     AND i.dt_indice >= TO_DATE('01/09/2013','DD/MM/YYYY')
                     AND DATE_TRUNC('day',i.dt_indice) BETWEEN TO_DATE('".$args['dt_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."','DD/MM/YYYY')
                     AND i.dt_indice IN (SELECT MAX(i1.dt_indice) 
                                          FROM autoatendimento.indice_mercado_valor i1
                                          WHERE i1.cd_indice_mercado = i.cd_indice_mercado 
                                          GROUP BY DATE_TRUNC('month', i1.dt_indice))
                     AND i.dt_indice <= DATE_TRUNC('month',CURRENT_DATE) - '1 days'::interval -- MES ANTERIOR
                     AND i.dt_exclusao IS NULL       
                   
                   ORDER BY dt_cota      
    		        ";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados($args)
    {
		$qr_sql = "
					SELECT ea.cd_edicao, 
						   ea.cd_empresa, 
						   ea.cd_registro_empregado, 
						   ea.seq_dependencia,				   
						   ead.cd_linha, 
						   ead.ds_linha, 
						   ead.vl_valor
					  FROM meu_retrato.edicao_participante ea 
					  JOIN meu_retrato.edicao_participante_dado ead
						ON ead.cd_edicao_participante = ea.cd_edicao_participante 
					 WHERE ea.cd_edicao_participante = ".intval($args['cd_edicao_participante'])."
					 ORDER BY ead.cd_linha
    		      ";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_dados_instituidor($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.ds_linha
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'PARTICIPANTE_DT_NASCIMENTO') AS dt_nascimento,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SIMULA_CONTRIB_ATUAL_C1') AS sim_contrib_atual_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SIMULA_SALDO_ACUMULADO_ATUAL_C1') AS vl_sim_saldo_acum_atual_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SIMULA_SALDO_ACUMULADO_ATUAL_C2') AS vl_sim_saldo_acum_atual_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SIMULA_SALDO_ACUMULADO_ATUAL_C3') AS vl_sim_saldo_acum_atual_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SIMULA_BENEFICIO_INICIAL_ATUAL_C1') AS vl_sim_benef_ini_atual_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SIMULA_BENEFICIO_INICIAL_ATUAL_C2') AS vl_sim_benef_ini_atual_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SIMULA_BENEFICIO_INICIAL_ATUAL_C3') AS vl_sim_benef_ini_atual_3
              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_dados_aposentado($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'APOSENTADO_VL_BENEFICIO') AS vl_beneficio,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'APOSENTADO_VL_SALDO_ANTERIOR') AS vl_saldo_anterior,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'APOSENTADO_VL_BENEFICIO_PAGO') AS vl_beneficio_pago,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'APOSENTADO_VL_RENTABILIDADE') AS vl_rentabilidade,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'APOSENTADO_VL_SALDO_ATUAL') AS saldo_atual
              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados_ieabprev($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.vl_valor
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_MESES_FALTAM') AS ben_meses_faltam,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_PARAM_SALARIO') AS ben_param_salario,   
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,
				   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'CONTRIB_MES_TOTAL') AS contrib_mes_total,   
					  

                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_1') AS ben_saldo_acumulado_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_1') AS ben_inicial_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_FA_1') AS ben_fa_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_2') AS ben_saldo_acumulado_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_2') AS ben_inicial_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_3') AS ben_saldo_acumulado_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_3') AS ben_inicial_3

              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados_municipios($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.ds_linha
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'PARTICIPANTE_DT_NASCIMENTO') AS dt_nascimento,
                   (SELECT epd.ds_linha
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'PARTICIPANTE_DT_INGRESSO') AS dt_ingresso,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_VALOR_CONTRIB_PARTIC_1') AS ben_valor_contrib_partic_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_VALOR_CONTRIB_PATROC_1') AS ben_valor_contrib_patroc_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_1') AS ben_saldo_acumulado_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_1') AS ben_inicial_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_2') AS ben_saldo_acumulado_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_2') AS ben_inicial_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_3') AS ben_saldo_acumulado_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_3') AS ben_inicial_3
              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados_familia_corporativo($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.ds_linha
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'PARTICIPANTE_DT_NASCIMENTO') AS dt_nascimento,
                   (SELECT epd.ds_linha
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'PARTICIPANTE_DT_INGRESSO') AS dt_ingresso,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'CONTRIB_MES_TOTAL') AS contrib_mes_total,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,

                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_1') AS ben_saldo_acumulado_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_1') AS ben_inicial_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_2') AS ben_saldo_acumulado_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_2') AS ben_inicial_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_3') AS ben_saldo_acumulado_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_3') AS ben_inicial_3

              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados_ceeeprev($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.vl_valor
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_MESES_FALTAM') AS ben_meses_faltam,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_PARAM_SALARIO') AS ben_param_salario,   
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,

                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_1') AS ben_saldo_acumulado_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_1') AS ben_inicial_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_2') AS ben_saldo_acumulado_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_2') AS ben_inicial_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_3') AS ben_saldo_acumulado_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_3') AS ben_inicial_3

              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados_ceranprev($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.vl_valor
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_MESES_FALTAM') AS ben_meses_faltam,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_PARAM_SALARIO') AS ben_param_salario,   
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,

                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_1') AS ben_saldo_acumulado_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_1') AS ben_inicial_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_2') AS ben_saldo_acumulado_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_2') AS ben_inicial_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_3') AS ben_saldo_acumulado_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_3') AS ben_inicial_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'PERCENTUAL_CONTRIB_SALARIO') AS percentual_contrib_salario

              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados_crmprev($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.vl_valor
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_MESES_FALTAM') AS ben_meses_faltam,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_PARAM_SALARIO') AS ben_param_salario,   
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,

                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_1') AS ben_saldo_acumulado_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_1') AS ben_inicial_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_2') AS ben_saldo_acumulado_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_2') AS ben_inicial_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_3') AS ben_saldo_acumulado_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_3') AS ben_inicial_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'PERCENTUAL_CONTRIB_SALARIO') AS percentual_contrib_salario

              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function participante_dados_foz($cd_edicao)
    {
        $qr_sql = "
            SELECT ep.cd_empresa || '/' || ep.cd_registro_empregado || '/' || ep.seq_dependencia AS re,
                   (SELECT epd.vl_valor
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_MESES_FALTAM') AS ben_meses_faltam,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_PARAM_SALARIO') AS ben_param_salario,   
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'SALDO_ACUMULADO') AS saldo_acumulado,

                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_1') AS ben_saldo_acumulado_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_1') AS ben_inicial_1,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_2') AS ben_saldo_acumulado_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_2') AS ben_inicial_2,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_SALDO_ACUMULADO_3') AS ben_saldo_acumulado_3,
                   (SELECT REPLACE (epd.vl_valor::text, '.', ',') 
                      FROM meu_retrato.edicao_participante_dado epd
                     WHERE epd.cd_edicao_participante = ep.cd_edicao_participante
                       AND epd.cd_linha = 'BEN_INICIAL_3') AS ben_inicial_3

              FROM meu_retrato.edicao_participante ep
             WHERE ep.cd_edicao = ".intval($cd_edicao).";";

        return $this->db->query($qr_sql)->result_array();
    }
}