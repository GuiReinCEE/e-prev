<?php
class Contrato_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_contrato,
			       a.seq_contrato AS cd_contrato_eletro,
				   a.ds_empresa,
				   a.ds_servico,
				   a.ds_valor,
				   b.ds_contrato_pagamento,
				   TO_CHAR(a.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
				   TO_CHAR(a.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento,
				   TO_CHAR(a.dt_reajuste, 'DD/MM/YYYY') AS dt_reajuste,
				   a.cd_divisao,
				   a.fl_avaliar,
				   a.status_contrato,
				   a.id_renovacao_automatica,
				   (SELECT COUNT(*) 
				      FROM projetos.contrato_avaliador ca
					 WHERE ca.cd_contrato = a.cd_contrato
					   AND ca.dt_exclusao IS NULL) AS qt_avaliador
			  FROM projetos.contrato a
			  JOIN projetos.contrato_pagamento b 
			    ON b.cd_contrato_pagamento = a.cd_contrato_pagamento
			 WHERE a.dt_exclusao IS NULL
			   ".(trim($args['status_contrato']) != '' ? "AND a.status_contrato IN ('".implode("','",explode(",",$args['status_contrato']))."')" : '')."
			   ".(trim($args['fl_avaliar']) != '' ? "AND a.fl_avaliar = '".trim($args['fl_avaliar'])."'" : '')."
			   ".(trim($args['cd_gerencia']) != '' ? "AND a.cd_divisao = '".trim($args['cd_gerencia'])."'" : '')."
			   ".(trim($args['ds_empresa']) ? "AND funcoes.remove_acento(UPPER(a.ds_empresa)) LIKE funcoes.remove_acento(UPPER(('%".str_replace(' ','%', trim($args['ds_empresa']))."%')))" : "")."
			   ".(trim($args['ds_servico']) ? "AND funcoes.remove_acento(UPPER(a.ds_servico)) LIKE funcoes.remove_acento(UPPER(('%".str_replace(' ','%', trim($args['ds_servico']))."%')))" : "")."
			   ".(((trim($args['dt_inicio_ini']) != "") AND (trim($args['dt_inicio_fim']) != "")) ? " AND CAST(a.dt_inicio AS DATE) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_encerramento_ini']) != "") AND (trim($args['dt_encerramento_fim']) != "")) ? " AND CAST(a.dt_encerramento AS DATE) BETWEEN TO_DATE('".$args['dt_encerramento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encerramento_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_reajuste_ini']) != "") AND (trim($args['dt_reajuste_fim']) != "")) ? " AND CAST(a.dt_reajuste AS DATE) BETWEEN TO_DATE('".$args['dt_reajuste_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_reajuste_fim']."', 'DD/MM/YYYY')" : "").";";
			
		$result = $this->db->query($qr_sql);
	}
	
	function gerencias(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
				   codigo || ' - ' || nome AS text
			  FROM projetos.divisoes
			 WHERE tipo IN ('DIV', 'ASS')
			 ORDER BY nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function contrato_pagamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_contrato_pagamento AS value,
				   ds_contrato_pagamento AS text
			  FROM projetos.contrato_pagamento
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_contrato_pagamento;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	public function carrega($cd_contrato)
	{
		$qr_sql = "
			SELECT cd_contrato, 
			       ds_empresa, 
				   ds_servico, 
				   ds_valor, 
				   cd_contrato_pagamento, 
		           TO_CHAR(dt_inicio, 'DD/MM/YYYY') AS dt_inicio, 
				   TO_CHAR(dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento, 
				   TO_CHAR(dt_reajuste, 'DD/MM/YYYY') AS dt_reajuste, 
				   cd_divisao, 
				   dt_exclusao
			  FROM projetos.contrato 
			 WHERE cd_contrato = ".intval($cd_contrato)." 
			   AND dt_exclusao IS NULL;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_contrato']) == 0)
		{		
			$cd_contrato = $this->db->get_new_id("projetos.contrato", "cd_contrato");
		
			$qr_sql = "
				INSERT INTO projetos.contrato
				     (
					    cd_contrato,
						ds_empresa,
						ds_servico,
						ds_valor,
						cd_contrato_pagamento,
						dt_inicio,
						dt_encerramento,
						dt_reajuste,
						cd_divisao
					 )
				VALUES 
				     (
					    ".intval($cd_contrato).",
						".str_escape($args['ds_empresa']).",
						".str_escape($args['ds_servico']).",
						".(trim($args['ds_valor']) != '' ? str_escape($args['ds_valor']) : "DEFAULT").",
						".intval($args['cd_contrato_pagamento']).",
						TO_DATE('".trim($args['dt_inicio'])."', 'DD/MM/YYYY'),
						".(trim($args['dt_encerramento']) != '' ? "TO_DATE('".trim($args['dt_encerramento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['dt_reajuste']) != '' ? "TO_DATE('".trim($args['dt_reajuste'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						'".trim($args['cd_divisao'])."'
					 );";
		}
		else
		{		
			$cd_contrato = intval($args['cd_contrato']);
		
			$qr_sql = "
				UPDATE projetos.contrato
				   SET ds_empresa            = ".str_escape($args['ds_empresa']).",
				   	   ds_servico            = ".str_escape($args['ds_servico']).",
				   	   ds_valor              = ".(trim($args['ds_valor']) != '' ? str_escape($args['ds_valor']) : "DEFAULT").",
				   	   cd_contrato_pagamento = ".intval($args['cd_contrato_pagamento']).",
				   	   dt_inicio             = TO_DATE('".trim($args['dt_inicio'])."', 'DD/MM/YYYY'),
				   	   dt_encerramento       = ".(trim($args['dt_encerramento']) != '' ? "TO_DATE('".trim($args['dt_encerramento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   	   dt_reajuste           = ".(trim($args['dt_reajuste']) != '' ? "TO_DATE('".trim($args['dt_reajuste'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   	   cd_divisao            = '".trim($args['cd_divisao'])."'
				 WHERE cd_contrato = ".intval($args['cd_contrato']).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_contrato;
	}
	
	function adicionar_responsavel(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.contrato_responsavel
			     (
					cd_contrato,
					cd_usuario
				 )
			VALUES
			     (
					".intval($args['cd_contrato']).",
					".intval($args['cd_usuario'])."
				 )";
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_responsaveis(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_contrato_responsavel, 
			       uc.nome,
				   uc.divisao
			  FROM projetos.contrato_responsavel r
			  JOIN projetos.usuarios_controledi uc 
			    ON r.cd_usuario = uc.codigo
		 	 WHERE r.dt_exclusao IS NULL
			   AND r.cd_contrato = ".intval($args['cd_contrato']).";";
		
		$result = $this->db->query($qr_sql);
	}

	function excluir_responsavel(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.contrato_responsavel 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])." 
			 WHERE cd_contrato_responsavel = ".intval($args['cd_contrato_responsavel']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function excluir( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.contrato
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."  
			 WHERE cd_contrato = ".intval($args['cd_contrato']).";";
	
		$result = $this->db->query($qr_sql);
	}

	public function get_usuarios($divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

	public function salvar_avaliador($args=array())
	{
		$cd_contrato_avaliador = $this->db->get_new_id("projetos.contrato_avaliador", "cd_contrato_avaliador");
		
		$qr_sql = "
			INSERT INTO projetos.contrato_avaliador
			     (
				    cd_contrato,
				    dt_inclusao,
					cd_usuario,
					cd_usuario_inclusao
				 )
			VALUES 
			     (
				   ".intval($args['cd_contrato']).",
				   CURRENT_TIMESTAMP,
				   ".intval($args['cd_usuario']).", 
				   ".intval($args['cd_usuario_inclusao'])." 
				 );";

		return $this->db->query($qr_sql); 
	}
		
	public function listar_avaliadores($cd_contrato)
	{
		$qr_sql = "
			SELECT cd_contrato,
				   cd_contrato_avaliador,
				   cd_usuario,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
	               funcoes.get_usuario_nome (cd_usuario) as usuario,
	               funcoes.get_usuario_nome (cd_usuario_inclusao) as usuario_inclusao
			  FROM projetos.contrato_avaliador 
			 WHERE dt_exclusao IS NULL
			   AND cd_contrato =".intval($cd_contrato).";";

	  return $this->db->query($qr_sql)->result_array();

	}

	public function excluir_avaliador($cd_contrato, $cd_contrato_avaliador, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.contrato_avaliador
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."  
			 WHERE cd_contrato = ".intval($cd_contrato)."
			   AND cd_contrato_avaliador = ".intval($cd_contrato_avaliador).";";
	
		$this->db->query($qr_sql);
	}


	function listarContratoNoticacaoLGPD(&$result, $args=array())
	{
		$qr_sql = "
					SELECT (CASE WHEN COALESCE(c.id_fornec_estrangeiro,'N') = 'S' 
									 THEN c.nm_fornec_estrangeiro
									 ELSE COALESCE(e.nm_fantasia,e.nm_entidade)
							   END) AS ds_empresa,
                               e.nm_fantasia,
							   e.nm_entidade,
							   c.ds_objeto AS ds_servico, 
							   e.cd_tipo_pessoa, 
							   e.cnpf_cnpj,
							   (CASE WHEN e.cd_tipo_pessoa = 'PJ'
							        THEN funcoes.format_cnpj(e.cnpf_cnpj)
									ELSE funcoes.format_cpf(e.cnpf_cnpj)
							   END)::TEXT AS nr_registro,
							   funcoes.number_format(c.vl_a_pagar,2) AS ds_valor,
							   0 AS cd_contrato_pagamento, 
							   c.dt_inicio, 
							   NULL AS dt_encerramento, 
							   NULL AS dt_reajuste,  
							   (SELECT (CASE WHEN d.sigla_divisao = 'GGP' THEN 'GGPA' ELSE d.sigla_divisao END) AS sigla_divisao
								  FROM public.ss_areas_gestoras_ct a
								  JOIN public.sf_divisoes d
									ON d.cd_divisao = a.cd_divisao  
								 WHERE a.seq_contrato = c.seq_contrato
								 ORDER BY a.seq_area_ct ASC 
								 LIMIT 1) AS cd_divisao,
							   (SELECT array_to_string(array_agg(distinct d.sigla_divisao),',')
								  FROM public.ss_areas_gestoras_ct a
								  JOIN public.sf_divisoes d
									ON d.cd_divisao = a.cd_divisao  
								 WHERE a.seq_contrato = c.seq_contrato) AS gestor_contrato,
							   c.id_avaliacao AS fl_avaliar,
							   c.seq_contrato,
							   c.status_contrato,
							   c.id_renovacao_automatica,
							   c.cd_formulario_aval_eprev
						  FROM public.ss_contratos_servicos c
						  LEFT JOIN public.sf_entidades e
							ON e.cd_entidade = c.cd_entidade
						 WHERE c.seq_contrato IN (".implode(",",$args['ar_seq_contrato']).")
						 ORDER BY c.seq_contrato;
			       ";
		$result = $this->db->query($qr_sql);
	}
	
	function getContratoNoticacaoLGPD(&$result, $args=array())
	{
		$qr_sql = "
					SELECT (CASE WHEN COALESCE(c.id_fornec_estrangeiro,'N') = 'S' 
									 THEN c.nm_fornec_estrangeiro
									 ELSE COALESCE(e.nm_fantasia,e.nm_entidade)
							   END) AS ds_empresa,
                               e.nm_fantasia,
							   e.nm_entidade,
							   c.ds_objeto AS ds_servico, 
							   funcoes.number_format(c.vl_a_pagar,2) AS ds_valor,
							   0 AS cd_contrato_pagamento, 
							   c.dt_inicio, 
							   NULL AS dt_encerramento, 
							   NULL AS dt_reajuste,  
							   (SELECT (CASE WHEN d.sigla_divisao = 'GGP' THEN 'GGPA' ELSE d.sigla_divisao END) AS sigla_divisao
								  FROM public.ss_areas_gestoras_ct a
								  JOIN public.sf_divisoes d
									ON d.cd_divisao = a.cd_divisao  
								 WHERE a.seq_contrato = c.seq_contrato
								 ORDER BY a.seq_area_ct ASC 
								 LIMIT 1) AS cd_divisao,
							   (SELECT array_to_string(array_agg(distinct d.sigla_divisao),',')
								  FROM public.ss_areas_gestoras_ct a
								  JOIN public.sf_divisoes d
									ON d.cd_divisao = a.cd_divisao  
								 WHERE a.seq_contrato = c.seq_contrato) AS gestor_contrato,
							   c.id_avaliacao AS fl_avaliar,
							   c.seq_contrato,
							   c.status_contrato,
							   c.id_renovacao_automatica,
							   c.cd_formulario_aval_eprev
						  FROM public.ss_contratos_servicos c
						  LEFT JOIN public.sf_entidades e
							ON e.cd_entidade = c.cd_entidade
						 WHERE c.seq_contrato = ".intval($args['ar_seq_contrato'])."
						 ORDER BY c.seq_contrato;
			       ";
		$result = $this->db->query($qr_sql);
	}	
	
	function getContatoNoticacaoLGPD(&$result, $args=array())
	{
		$qr_sql = "
					    SELECT c.*
						  FROM ss_contatos_ct c
						 WHERE c.seq_contrato IN (".implode(",",$args['ar_seq_contrato']).")
			       ";
		$result = $this->db->query($qr_sql);
	}


	function listarContratosPPE(&$result, $args=array())
	{
		$qr_sql = "
                    SELECT (CASE WHEN COALESCE(c.id_fornec_estrangeiro,'N') = 'S' 
							     THEN c.nm_fornec_estrangeiro
								 ELSE COALESCE(e.nm_fantasia,e.nm_entidade)
						   END) AS ds_empresa,
						   e.nm_entidade,
						   
						   (CASE WHEN e.cd_tipo_pessoa = 'PJ'
							 	 THEN funcoes.format_cnpj(e.cnpf_cnpj)
								 ELSE funcoes.format_cpf(e.cnpf_cnpj)
						   END)::TEXT AS nr_registro,
						   
                           (SELECT array_to_string(array_agg(distinct c1.seq_contrato::TEXT),',')
							  FROM public.ss_contratos_servicos c1
							 WHERE c1.cd_entidade = c.cd_entidade
						   	   AND c1.status_contrato in ('A','S')
                               AND c1.tp_classificacao ='N'
						       AND c1.id_in34_prev_lav = 'S') AS ar_seq_contrato,				   
							 
						   (SELECT array_to_string(array_agg(distinct d.sigla_divisao),',')
							  FROM public.ss_areas_gestoras_ct a
							  JOIN public.ss_contratos_servicos c3
							    ON c3.seq_contrato = a.seq_contrato
							   AND c3.status_contrato in ('A','S')
                               AND c3.tp_classificacao ='N'
							   AND c3.id_in34_prev_lav = 'S'
                              JOIN public.sf_entidades e2
						        ON e2.cd_entidade = c3.cd_entidade							
							  JOIN public.sf_divisoes d
								ON d.cd_divisao = a.cd_divisao  
							 WHERE c3.cd_entidade = c.cd_entidade) AS ar_gestor_contrato,
                          
						  (SELECT array_to_string(array_agg(distinct TRIM(COALESCE(c2.email,''))::TEXT),';')
							  FROM public.ss_contatos_ct c2
							  JOIN public.ss_contratos_servicos c3
							    ON c3.seq_contrato = c2.seq_contrato
							   AND c3.status_contrato in ('A','S')
                               AND c3.tp_classificacao ='N'
						       AND c3.id_in34_prev_lav = 'S'
                              JOIN public.sf_entidades e2
						        ON e2.cd_entidade = c3.cd_entidade							
							 WHERE c3.cd_entidade = c.cd_entidade
						    AND TRIM(COALESCE(c2.email,'')) LIKE '%'   
							AND TRIM(COALESCE(c2.email,'')) <> '')  AS ar_contato,
						   (SELECT array_to_string(array_agg(distinct (SELECT funcoes.get_usuario(funcoes.get_usuario_gerente(d.sigla_divisao)) ||'@familiaprevidencia.com.br;'|| (SELECT funcoes.get_usuario(funcoes.get_usuario_gerente_substituto(d.sigla_divisao))) ||'@familiaprevidencia.com.br')),',')
							  FROM public.ss_areas_gestoras_ct a
							  JOIN public.ss_contratos_servicos c3
							    ON c3.seq_contrato = a.seq_contrato
							   AND c3.status_contrato in ('A','S')
                               AND c3.tp_classificacao ='N'
							   AND c3.id_in34_prev_lav = 'S'
                              JOIN public.sf_entidades e2
						        ON e2.cd_entidade = c3.cd_entidade							
							  JOIN public.sf_divisoes d
								ON d.cd_divisao = a.cd_divisao  
							 WHERE c3.cd_entidade = c.cd_entidade) AS ar_email_gestor_contrato							
					  FROM public.ss_contratos_servicos c
					  JOIN public.sf_entidades e
						ON e.cd_entidade = c.cd_entidade
                     WHERE 1 = 1
					   --AND c.tp_contrato ='C' -- TIPO PONTUAL CONTINUADO
                       AND c.status_contrato in ('A','S')
                       AND c.tp_classificacao ='N' 
	                   AND c.id_in34_prev_lav = 'S' -- IN34
                     GROUP BY ds_empresa, e.nm_entidade, nr_registro, ar_seq_contrato, ar_gestor_contrato, ar_contato, ar_email_gestor_contrato
                     ORDER BY ds_empresa
			       ";
		$result = $this->db->query($qr_sql);
	}	
}
?>