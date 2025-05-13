<?php
class Atividade_solicitacao_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
	function gerencia_destino(&$result, $args=array())
    {
    	if(trim($args["cd_gerencia"]) == 'GF')
    	{
    		$args["cd_gerencia"] = 'GFC';
    	}

    	if(trim($args["cd_gerencia"]) == 'GA')
    	{
    		$args["cd_gerencia"] = 'GP';
    	}

    	if(trim($args["cd_gerencia"]) == 'GRI')
    	{
    		$args["cd_gerencia"] = 'GNR-COM';
    	}

        $qr_sql = "
			SELECT nome
			  FROM projetos.divisoes 
			 WHERE codigo       = '".trim($args['cd_gerencia'])."';";

        $row = $this->db->query($qr_sql)->row_array();

        if(count($row) == 0)
        {
        	$qr_sql = "
				SELECT ds_descricao AS nome
				  FROM projetos.gerencia_unidade
				 WHERE cd_gerencia_unidade = '".trim($args['cd_gerencia'])."';";

	        $row = $this->db->query($qr_sql)->row_array();
        }

        return $row;
    }
    
    function solicitante(&$result, $args=array())
    {
        $qr_sql = "
					SELECT uc.codigo AS value,
						   uc.nome AS text
					  FROM projetos.usuarios_controledi uc
					 WHERE uc.divisao <> 'SNG'
					   AND (uc.tipo    <> 'X' OR uc.codigo = (SELECT a.cod_solicitante FROM projetos.atividades a WHERE a.numero = ".intval($args['numero'])."))
					 ORDER BY uc.nome;
			      ";

        $result = $this->db->query($qr_sql);
    }
    
    function tipo_manutencao(&$result, $args=array())
    {
        $qr_sql = "
			SELECT codigo AS value,
                   descricao AS text
              FROM listas 
             WHERE categoria='TPMN' 
               AND divisao = '".trim($args["cd_gerencia"])."' 
               AND dt_exclusao IS NULL 
             ORDER BY descricao;";
			 
		
        $result = $this->db->query($qr_sql);
    }
    
    function tipo_atividade(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   descricao AS text 
              FROM listas 
             WHERE categoria = 'TPAT' 
               AND divisao   = '".trim($args["cd_gerencia"])."' 
             ORDER BY descricao;";

        $result = $this->db->query($qr_sql);
    }

    function cb_sistema(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo AS value, 
                   nome AS text
              FROM projetos.projetos 
             WHERE (area         = '".$args['cd_gerencia_destino']."' ".(trim($args['cd_gerencia_destino']) == 'GI' ? "OR area = 'GGS' OR area = 'GTI'" : "")." )
               AND dt_exclusao  IS NULL 
               AND fl_atividade = 'S' 
             ORDER BY text;";

        $result = $this->db->query($qr_sql);
    }

    public function cb_sistema_descricao($codigo)
    {
        $qr_sql = "
            SELECT descricao
              FROM projetos.projetos 
             WHERE codigo = ".intval($codigo)."
               AND dt_exclusao  IS NULL;";
        return $this->db->query($qr_sql)->row_array();	
    }
    
    function atendente(&$result, $args=array())
    {
    	if(trim($args["cd_gerencia"]) == 'GF')
    	{
    		$args["cd_gerencia"] = 'GFC';
    	}

    	if(trim($args["cd_gerencia"]) == 'GA')
    	{
    		$args["cd_gerencia"] = 'GAP-ATU';
    	}

    	if(trim($args["cd_gerencia"]) == 'GRI')
    	{
    		$args["cd_gerencia"] = 'GNR-COM';
    	}

    	if(trim($args["cd_gerencia"]) == 'GAD')
    	{
    		$args["cd_gerencia"] = 'GS-ADM';
    	}

    	if(trim($args["cd_gerencia"]) == 'GCM-CAD')
    	{
    		$args["cd_gerencia"] = 'GNR-CAD';
    	}
		
    	if(trim($args["cd_gerencia"]) == 'GC-RH')
    	{
    		$args["cd_gerencia"] = 'GS-RH';
    	}		

        $qr_sql = "
					SELECT uc.codigo AS value,
						   uc.nome AS text
					  FROM projetos.usuarios_controledi uc
					 WHERE (uc.tipo IN ('N', 'G', 'U', 'E', 'P') OR uc.codigo = (SELECT a.cod_atendente FROM projetos.atividades a WHERE a.numero = ".intval($args['numero'])."))
					   AND (
					   		uc.divisao = '".trim($args["cd_gerencia"])."' 
							".(trim($args["cd_gerencia"]) == 'GNR-CAD' ? "OR uc.codigo IN (146)" : "")."
					   		OR 
					   		uc.divisao_ant = '".trim($args["cd_gerencia"])."'
					   		".(trim($args["cd_gerencia"]) == 'GFC-DIG' ? "OR uc.codigo IN (457)" : "")."
					   		OR 
					   		cd_gerencia_unidade = '".trim($args["cd_gerencia"])."'  
					   		OR '' = '".trim($args["cd_gerencia"])."'
					   	)
					   
					   AND (COALESCE(uc.indic_06,'N') = 'S' OR uc.codigo = (SELECT a.cod_atendente FROM projetos.atividades a WHERE a.numero = ".intval($args['numero']).")) 
					 ORDER BY uc.nome;
			      ";
				  
		#echo $qr_sql; exit;
        $result = $this->db->query($qr_sql);

    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT a.numero,
			       a.cd_atividade_origem,
                   a.nr_prioridade,
				   a.area AS cd_gerencia_destino,                                          
				   a.divisao,                                                                          
				   a.cod_solicitante,                                                  
				   a.cd_recorrente,                                 
				   a.tipo_solicitacao,                              
				   TO_CHAR(a.dt_cad, 'DD/MM/YYYY HH24:MI:SS') AS dt_cad,  
				   a.tipo,                              
				   a.status_atual,                                   
				   a.cod_atendente,
				   a.cd_substituto,
				   funcoes.get_usuario_nome(a.cod_atendente) AS ds_atendente,
				   funcoes.get_usuario_nome(a.cd_substituto) AS ds_substituto, 
				   funcoes.get_usuario_nome(a.cod_solicitante) AS ds_solicitante,                                                               
				   a.descricao,                                      
				   a.problema,                                            
				   a.titulo,		 																
				   a.cd_atendimento,		 							
				   TO_CHAR(dt_limite, 'DD/MM/YYYY') AS dt_limite,
				   TO_CHAR(a.dt_aguardando_usuario, 'DD/MM/YYYY HH24:MI:SS') AS dt_aguardando_usuario,
				   TO_CHAR(a.dt_aguardando_usuario_limite, 'DD/MM/YYYY') AS dt_aguardando_usuario_limite,	
                   TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY') AS dt_limite_teste,	
                   TO_CHAR(a.dt_fim_real, 'DD/MM/YYYY HH24:MI:SS') AS data_conclusao,	
                   a.sistema,	   
                   d.codigo || ' - ' || d.nome AS gerencia_destino,
                   l.descricao AS status_atividade,
                   CASE WHEN l.valor = 1 THEN 'label label-info'
                        WHEN l.valor = 2 THEN 'label'
                        WHEN l.valor = 3 THEN 'label label-important'
                        WHEN l.valor = 4 THEN 'label label-warning'
                        WHEN l.valor = 5 THEN 'label label-info'
                        ELSE 'label label-success'
                   END AS class_status,
				   a.cd_empresa,
				   a.cd_plano,
				   a.cd_registro_empregado,
				   a.cd_sequencia AS seq_dependencia,
				   a.solicitante,
				   a.forma,
				   a.tp_envio,
				   p.nome AS nome_participante,
				   p.endereco,
				   p.nr_endereco,
				   p.complemento_endereco,
				   p.bairro,
				   TO_CHAR(p.cep, 'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep,
				   p.cidade,
				   p.unidade_federativa AS uf,
				   p.ddd,
				   p.telefone,
				   p.ddd_celular,
				   p.celular,							   
				   p.email,
				   p.email_profissional,
				   (SELECT COUNT(*)
				      FROM projetos.atividade_anexo aa
					 WHERE aa.dt_exclusao IS NULL
					   AND aa.cd_atividade = a.numero) AS qt_anexo,
				   a.cod_testador,
				   a.dt_fim_real,
				   a.cd_cenario,
				   c.cd_edicao,
				   CASE WHEN (a.status_atual = 'CAGC') THEN (SELECT ah.observacoes
															   FROM projetos.atividade_historico ah
															  WHERE ah.cd_atividade = a.numero
															    AND ah.status_atual = 'CAGC'
															  ORDER BY ah.codigo DESC 
															  LIMIT 1)
						WHEN (a.status_atual = 'RAGC') THEN (SELECT ah.observacoes
														       FROM projetos.atividade_historico ah
														      WHERE ah.cd_atividade = a.numero
														        AND ah.status_atual = 'RAGC'
														      ORDER BY ah.codigo DESC 
														      LIMIT 1)
						WHEN (a.pertinencia = '0') THEN 'Não pertinente'
						WHEN (a.pertinencia = '1') THEN 'Pertinente, mas não altera processo'
						WHEN (a.pertinencia = '2') THEN 'Pertinente e altera processo'
						ELSE 'Não verificado'
				   END AS pertinencia_status,
				   CASE WHEN (a.pertinencia = '0') THEN 'Não pertinente'
						WHEN (a.pertinencia = '1') THEN 'Pertinente, mas não altera processo'
						WHEN (a.pertinencia = '2') THEN 'Pertinente e altera processo'
						ELSE 'Não verificado'
				   END AS ds_pertinencia,
				   CASE WHEN (a.status_atual = 'CAGC') THEN 'gray'
						WHEN (a.status_atual = 'RAGC') THEN 'gray'
						WHEN (a.pertinencia = '0')     THEN 'black'
						WHEN (a.pertinencia = '1')     THEN 'green'
						WHEN (a.pertinencia = '2')     THEN 'blue'
						ELSE 'orange'
				   END AS cor,
				   CASE WHEN (a.status_atual = 'CAGC') THEN ''
						WHEN (a.status_atual = 'RAGC') THEN ''
						WHEN (a.pertinencia = '0')     THEN 'label-inverse'
						WHEN (a.pertinencia = '1')     THEN 'label-success'
						WHEN (a.pertinencia = '2')     THEN 'label-info'
						ELSE 'label-important'
				   END AS cor_status,
				   CASE WHEN (a.pertinencia != '2')                       THEN 'N'
				        WHEN (a.dt_implementacao_norma_legal IS NOT NULL) THEN 'N'
						ELSE 'S'
				   END AS fl_salvar_legal,
				   a.pertinencia,
				   TO_CHAR(a.dt_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_implementacao_norma_legal,
			       TO_CHAR(a.dt_prevista_implementacao_norma_legal, 'DD/MM/YYYY') AS dt_prevista_implementacao_norma_legal,
			       TO_CHAR(c.dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao,
			       TO_CHAR(c.dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
			       uc.usuario AS usuario_solicitante,
			       c.titulo AS titulo_cenario,
				   a.fl_abrir_encerrar,
				   a.cd_gerencia_abrir_ao_encerrar,				   
				   a.cd_tipo_solicitacao_abrir_ao_encerrar,				   
				   a.cd_tipo_abrir_ao_encerrar,				   
				   a.cd_usuario_abrir_ao_encerrar,
				   a.descricao_abrir_ao_encerrar,
				   a.solucao,
				   a.ds_justificativa_cenario
		      FROM projetos.atividades a		
		      JOIN projetos.usuarios_controledi uc 
		        ON uc.codigo = a.cod_solicitante
			  LEFT JOIN public.participantes p
			    ON p.cd_empresa            = a.cd_empresa
               AND p.cd_registro_empregado = a.cd_registro_empregado
               AND p.seq_dependencia       = a.cd_sequencia			   
              LEFT JOIN projetos.divisoes d
                ON d.codigo = a.area
              LEFT JOIN listas l
                ON l.categoria = 'STAT' 
               AND l.codigo    = a.status_atual
               AND l.divisao   = a.area
              LEFT JOIN projetos.cenario c
				ON c.cd_cenario = a.cd_cenario
		     WHERE a.numero = ".intval($args['numero']).";";

        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['numero']) == 0)
        {
            $numero = intval($this->db->get_new_id("projetos.atividades", "numero"));
            
            $qr_sql = "
                INSERT INTO projetos.atividades 
                     (
                        numero,
                        dt_cad,
                        tipo,
                        descricao,
                        cd_recorrente,
                        area,
                        problema,
                        cod_solicitante,         
						cod_atendente, 
						cd_substituto,   
                        status_atual,
                        tipo_solicitacao,
                        divisao,
                        dt_limite,
                        titulo,
						cd_empresa,
						cd_registro_empregado,
						cd_sequencia,
						cd_plano,
						solicitante,
						forma,
						tp_envio,
						cd_atendimento,
				        fl_abrir_encerrar,
						cd_gerencia_abrir_ao_encerrar,
						cd_tipo_solicitacao_abrir_ao_encerrar,
						cd_tipo_abrir_ao_encerrar,
				        cd_usuario_abrir_ao_encerrar,
				        descricao_abrir_ao_encerrar,
				        sistema					
                     )
                VALUES
                     (
                        ".intval($numero).",
                        CURRENT_TIMESTAMP,
                        CASE WHEN (SELECT CASE WHEN TRIM(indic_02) = 'A' THEN 'S'
											   WHEN TRIM(indic_02) = 'S' THEN 'S'
											   ELSE NULL
										  END
									 FROM projetos.usuarios_controledi 
									WHERE codigo = ".intval($args['cod_atendente']).") = 'S' 
							  THEN 'S'
							  ELSE '".(trim($args['tipo']) != ''? trim($args['tipo']) : 'I')."'
						END,
                        ".(trim($args['descricao']) != ''? str_escape($args['descricao']) : "DEFAULT").",
                        ".(trim($args['cd_recorrente']) != ''? str_escape($args['cd_recorrente']) : "DEFAULT").",
                        ".(trim($args['area']) != ''? str_escape($args['area']) : "DEFAULT").",
                        ".(trim($args['problema']) != ''? str_escape($args['problema']) : "DEFAULT").",
                        ".(trim($args['cod_solicitante']) != ''? intval($args['cod_solicitante']) : "DEFAULT").",
                        ".(trim($args['cod_atendente']) != ''? intval($args['cod_atendente']) : "DEFAULT").",
                        ".(trim($args['cd_substituto']) != ''? intval($args['cd_substituto']) : "DEFAULT").",
                        ".(trim($args['status_atual']) != ''? str_escape($args['status_atual']) : "'AINI'").",
                        ".(trim($args['tipo_solicitacao']) != ''? str_escape($args['tipo_solicitacao']) : "DEFAULT").",
                        (SELECT uc.divisao FROM projetos.usuarios_controledi uc WHERE uc.codigo = ".intval($args['cod_solicitante'])."),
                        ".(trim($args['dt_limite']) != '' ? "TO_DATE('".trim($args['dt_limite'])."','DD/MM/YYYY')" : "DEFAULT").",
                        ".(trim($args['titulo']) != ''? str_escape($args['titulo']) : "DEFAULT").",
						".(trim($args['cd_empresa']) != ''? trim($args['cd_empresa']) : "DEFAULT").",
						".(trim($args['cd_registro_empregado']) != ''? trim($args['cd_registro_empregado']) : "DEFAULT").",
						".(trim($args['seq_dependencia']) != ''? trim($args['seq_dependencia']) : "DEFAULT").",
						".(trim($args['cd_plano']) != ''? intval($args['cd_plano']) : "DEFAULT").",
						".(trim($args['solicitante']) != ''? str_escape($args['solicitante']) : 'DEFAULT').",
						".(trim($args['forma']) != ''? str_escape($args['forma']) : 'DEFAULT').",
						".(trim($args['tp_envio']) != ''? str_escape($args['tp_envio']) : 'DEFAULT').",
						".(trim($args['cd_atendimento']) != ''? str_escape($args['cd_atendimento']) : 'DEFAULT').",
						".(trim($args['fl_abrir_encerrar']) != ''? str_escape($args['fl_abrir_encerrar']) : 'DEFAULT').",
						".(trim($args['cd_gerencia_abrir_ao_encerrar']) != ''? str_escape($args['cd_gerencia_abrir_ao_encerrar']) : 'DEFAULT').",
						".(trim($args['cd_tipo_solicitacao_abrir_ao_encerrar']) != ''? str_escape($args['cd_tipo_solicitacao_abrir_ao_encerrar']) : 'DEFAULT').",
						".(trim($args['cd_tipo_abrir_ao_encerrar']) != ''? str_escape($args['cd_tipo_abrir_ao_encerrar']) : 'DEFAULT').",
						".(trim($args['cd_usuario_abrir_ao_encerrar']) != ''? intval($args['cd_usuario_abrir_ao_encerrar']) : "DEFAULT").",
						".(trim($args['descricao_abrir_ao_encerrar']) != ''? str_escape($args['descricao_abrir_ao_encerrar']) : 'DEFAULT').",
						".(trim($args['sistema']) != ''? intval($args['sistema']) : 'DEFAULT')."
                     );";
        }
        else
        {
            $numero = $args['numero'];
            
            $qr_sql = "
                UPDATE projetos.atividades 
                   SET tipo = CASE WHEN (SELECT CASE WHEN TRIM(indic_02) = 'A' THEN 'S'
											         WHEN TRIM(indic_02) = 'S' THEN 'S'
											         ELSE NULL
										        END
									       FROM projetos.usuarios_controledi 
									      WHERE codigo = ".intval($args['cod_atendente']).") = 'S' 
							       THEN 'S'
							       ELSE ".(trim($args['tipo']) != ''? "'".trim($args['tipo'])."'" : "'I'")."
						      END,
                       descricao             = ".(trim($args['descricao']) != ''? str_escape($args['descricao']) : "DEFAULT").",
                       cd_recorrente         = ".(trim($args['cd_recorrente']) != ''? str_escape($args['cd_recorrente']) : "DEFAULT").",
                       area                  = ".(trim($args['area']) != ''? str_escape($args['area']) : "DEFAULT").",
                       problema              = ".(trim($args['problema']) != ''? str_escape($args['problema']) : "DEFAULT").",
                       cod_solicitante       = ".(trim($args['cod_solicitante']) != ''? intval($args['cod_solicitante']) : "DEFAULT").",
                       cod_atendente         = ".(trim($args['cod_atendente']) != ''? intval($args['cod_atendente']) : "DEFAULT").",
                       cd_substituto         = ".(trim($args['cd_substituto']) != ''? intval($args['cd_substituto']) : "DEFAULT").",
                       tipo_solicitacao      = ".(trim($args['tipo_solicitacao']) != ''? str_escape($args['tipo_solicitacao']) : "DEFAULT").",
                       dt_limite             = ".(trim($args['dt_limite']) != '' ? "TO_DATE('".trim($args['dt_limite'])."','DD/MM/YYYY')" : "DEFAULT").",
                       titulo                = ".(trim($args['titulo']) != ''? str_escape($args['titulo']) : "DEFAULT").",
					   cd_empresa            = ".(trim($args['cd_empresa']) != ''? trim($args['cd_empresa']) : "DEFAULT").",
					   cd_registro_empregado = ".(trim($args['cd_registro_empregado']) != ''? trim($args['cd_registro_empregado']) : "DEFAULT").",
					   cd_sequencia          = ".(trim($args['seq_dependencia']) != ''? trim($args['seq_dependencia']) : "DEFAULT").",
					   cd_plano              = ".(trim($args['cd_plano']) != ''? intval($args['cd_plano']) : "DEFAULT").",
					   fl_abrir_encerrar     = ".(trim($args['fl_abrir_encerrar']) != ''? str_escape($args['fl_abrir_encerrar']) : 'DEFAULT').",
					   cd_gerencia_abrir_ao_encerrar         = ".(trim($args['cd_gerencia_abrir_ao_encerrar']) != ''? str_escape($args['cd_gerencia_abrir_ao_encerrar']) : 'DEFAULT').",
					   cd_tipo_solicitacao_abrir_ao_encerrar = ".(trim($args['cd_tipo_solicitacao_abrir_ao_encerrar']) != ''? str_escape($args['cd_tipo_solicitacao_abrir_ao_encerrar']) : 'DEFAULT').",
					   cd_tipo_abrir_ao_encerrar             = ".(trim($args['cd_tipo_abrir_ao_encerrar']) != ''? str_escape($args['cd_tipo_abrir_ao_encerrar']) : 'DEFAULT').",
					   cd_usuario_abrir_ao_encerrar          = ".(trim($args['cd_usuario_abrir_ao_encerrar']) != ''? intval($args['cd_usuario_abrir_ao_encerrar']) : "DEFAULT").",
					   descricao_abrir_ao_encerrar           = ".(trim($args['descricao_abrir_ao_encerrar']) != ''? str_escape($args['descricao_abrir_ao_encerrar']) : 'DEFAULT').",
					   sistema 								 = ".(trim($args['sistema']) != ''? intval($args['sistema']) : 'DEFAULT')."
                 WHERE numero = ".intval($numero).";";
        }
    
        $result = $this->db->query($qr_sql);
        
        return $numero;
    }
	
	function data_limite(&$result, $args=array())
    {
		$qr_sql = "
			SELECT CASE WHEN divisao = 'GB' AND EXTRACT(DAY FROM CURRENT_DATE) BETWEEN 14 AND 21 --PERÍODO DA FOLHA
						THEN '24/'||TO_CHAR(CURRENT_DATE,'MM/YYYY')
						ELSE NULL
				   END AS dt_data_limite,
				   valor AS qt_dias,
				   CASE WHEN COALESCE(valor1,0) = 0
						THEN 'S'
						ELSE 'N'
				   END AS fl_dia_util
			  FROM public.listas 
			 WHERE codigo      = ".str_escape($args['tipo_solicitacao'])."
			   AND categoria   IN ('TPMN','TPAT')
			   AND dt_exclusao IS NULL;";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function proximo_dia_util(&$result, $args=array())
	{
		if($args['fl_dia_util'])
		{
			$qr_sql = "
				SELECT TO_CHAR(dia_util,'DD/MM/YYYY') AS dt_util 
				  FROM funcoes.dia_util('DEPOIS', TO_DATE('".$args['dt_data']."','DD/MM/YYYY'), ".$args['qt_dias'].");";
		}
		else
		{
			$qr_sql = "
				SELECT TO_CHAR(funcoes.dia_util('DEPOIS',(TO_DATE('".$args['dt_data']."','DD/MM/YYYY') + ".$args['qt_dias']."), 1),'DD/MM/YYYY') AS dt_util;";	
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function descricao_atividade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT obs 
			  FROM public.listas 
			 WHERE categoria IN ('TPMN','TPAT')
			   AND divisao   = '".$args['cd_gerencia']."' 
			   AND codigo    = ".str_escape($args['tipo_solicitacao']).";";
	
		$result = $this->db->query($qr_sql);
	}
	
	function plano(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_plano AS value,
			       descricao AS text
		      FROM public.planos;";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function solicitante_participante(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   descricao AS text
			  FROM listas 
			 WHERE categoria = 'SDAP' 
			 ORDER BY descricao;";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function forma_solicitacao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   descricao AS text
			  FROM listas 
			 WHERE categoria = 'FDAP' 
			 ORDER BY descricao;";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.atividade_anexo
			     (
					cd_atividade,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_atividade']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function reabrir_atividade(&$result, $args=array())
	{
		 $numero = intval($this->db->get_new_id("projetos.atividades", "numero"));
		 
		 $qr_sql = "
			-- DUPLICA OS
	         INSERT INTO projetos.atividades 
   	              (
				    numero, tipo, dt_cad, descricao, area, dt_inicio_prev, sistema, problema, solucao, dt_inicio_real, status_atual,
	                complexidade, prioridade, negocio_fim, prejuizo, legislacao, situacao, dependencia, dias_realizados, cliente_externo, concorrencia, tarefa,
	                tipo_solicitacao, numero_dias, dt_fim_prev, periodicidade, dt_deacordo, observacoes, divisao, origem, recurso, cod_atendente,
	                cod_solicitante, dt_limite, dt_limite_testes, ok, complemento, num_dias_adicionados, titulo, cd_empresa, cd_registro_empregado, cd_sequencia,
	                dt_retorno, pertinencia, cd_cenario, opt_grafica, opt_eletronica, opt_evento, opt_anuncio, opt_folder, opt_mala, opt_cartaz, opt_cartilha, 
	                opt_site, opt_outro, cores, formato, gramatura, quantia, custo, cc, pacs, patracs, nacs, cacs, lacs, dacs, forma, solicitante, cd_plano, numero_at_origem, cd_atividade_origem
				  ) 
		          (
				    SELECT ".intval($numero).", tipo, current_timestamp, descricao, area, dt_inicio_prev, sistema, problema, solucao, dt_inicio_real, 'AINI', 
	                       complexidade, prioridade, negocio_fim, prejuizo, legislacao, situacao, dependencia, dias_realizados, cliente_externo, concorrencia, tarefa,
	                       tipo_solicitacao, numero_dias, dt_fim_prev, periodicidade, dt_deacordo, observacoes, divisao, origem, recurso, cod_atendente, 
	                       cod_solicitante, dt_limite, dt_limite_testes, ok, complemento, num_dias_adicionados, titulo, cd_empresa, cd_registro_empregado, cd_sequencia, 
	                       dt_retorno, pertinencia, cd_cenario, opt_grafica, opt_eletronica, opt_evento, opt_anuncio, opt_folder, opt_mala, opt_cartaz, opt_cartilha, 
	                       opt_site, opt_outro, cores, formato, gramatura, quantia, custo, cc, pacs, patracs, nacs, cacs, lacs, dacs, forma, solicitante, cd_plano, numero, numero
					  FROM projetos.atividades 
					 WHERE numero = ".intval($args['numero'])."
				  );

            -- INSERE NO HISTORICO DA NOVA ATIVIDADE
            INSERT INTO projetos.atividade_historico 
			     ( 
				   cd_atividade, 
				   cd_recurso,	
				   dt_inicio_prev,
				   status_atual,
				   observacoes 
				 )
			VALUES 
			     ( 
				   ".intval($numero).", 
				   ".intval($args['cd_usuario']).",
				   CURRENT_TIMESTAMP,
				   'AINI',
				   'Atividade duplicada, atividade anterior número ".intval($args['numero'])."' 
				 );
				  			
            -- INSERE NO HISTORICO DA ATIVIDADE DUPLICADA
            INSERT INTO projetos.atividade_historico 
			     ( 
				   cd_atividade, 
				   cd_recurso,	
				   dt_inicio_prev,
				   status_atual,
				   observacoes 
				 )
			VALUES 
			     ( 
				   ".intval($args['numero']).", 
				   ".intval($args['cd_usuario']).",
				   CURRENT_TIMESTAMP,
				   'CANC',
				   'Atividade duplicada, nova atividade número ".intval($numero)."'
				 );";
		 
		$result = $this->db->query($qr_sql);
		
		return $numero;
	}
	
    function concluirAtividade(&$result, $args=array())
    {
		#### STATUS DE AGUARDANDO INICIO ####
		$ar_status_inicio["GB"]  = "AISB";
		$ar_status_inicio["GF"]  = "AINF";
		$ar_status_inicio["GRI"] = "AICS";
		$ar_status_inicio["GRC-RH"] = "AIRH";
		$ar_status_inicio["GFC-DIG"] = "AIDI";
		$ar_status_inicio["GA"]  = "AIGA";
		$ar_status_inicio["GI"]  = "AINI";
		$ar_status_inicio["GAP"] = "AIST";
		$ar_status_inicio["GC"]  = "GCAI";
		$ar_status_inicio["GJ"]  = "AIGJ";
		$ar_status_inicio["SG"]  = "SGAI";	

		#### STATUS DE CONCLUIDO ####
		$ar_gerencia_concluido["GB"]  = "COSB";
		$ar_gerencia_concluido["GAP"] = "COST";
		$ar_gerencia_concluido["GI"]  = "CONC";
		$ar_gerencia_concluido["GF"]  = "CONF";
		$ar_gerencia_concluido["GA"]  = "COGA";
		$ar_gerencia_concluido["GRI"] = "COCS";
		$ar_gerencia_concluido["GRC-RH"] = "ACRH";
		$ar_gerencia_concluido["GFC-DIG"] = "ACDI";
		$ar_gerencia_concluido["GC"]  = "GCCO";
		$ar_gerencia_concluido["GJ"]  = "COGJ";
		$ar_gerencia_concluido["SG"]  = "SGCO";        
		
		$qr_sql = "";
		if(trim($args['fl_concluir']) == "AP") 
		{
			#### AP = Atendeu Plenamente | Conclui a atividade ####
			$args['complemento_conclusao'] = (trim($args['complemento_conclusao']) != "" ? "Atividade ATENDEU à necessidade do usuário.".chr(10).chr(10).str_replace("'","´",trim($args['complemento_conclusao'])) : "Atividade ATENDEU à necessidade do usuário.");
			
			$qr_sql = "
						UPDATE projetos.atividades SET
							   complemento  = ".(trim($args['complemento_conclusao']) != '' ? "'".trim($args['complemento_conclusao'])."'" : "DEFAULT").",
							   status_atual = '".$ar_gerencia_concluido[strtoupper(trim($args['cd_gerencia_destino']))]."',
							   dt_fim_real  = CURRENT_TIMESTAMP
						 WHERE numero = ".intval($args['cd_atividade']).";
						 
						INSERT INTO projetos.atividade_historico 
							 (
							   cd_atividade, 
							   cd_recurso,	
							   status_atual, 
							   observacoes 
							 ) 
						VALUES 
							 ( 
							   ".intval($args['cd_atividade']).", 
							   ".intval($args['cd_usuario']).", 
							   '".$ar_gerencia_concluido[strtoupper(trim($args['cd_gerencia_destino']))]."',
							   ".(trim($args['complemento_conclusao']) != '' ? "'".trim($args['complemento_conclusao'])."'" : "DEFAULT")."
							 );	
					  ";
		}
		else if(trim($args['fl_concluir']) == "NA")
		{
			#### NA = Não Atendeu | Devolve a atividade para atendente e define o status de aguardando inicio ####
			$qr_sql = "
						INSERT INTO projetos.atividade_historico 
							 (
							   cd_atividade, 
							   cd_recurso,	
							   status_atual, 
							   observacoes 
							 ) 
						VALUES 
							 ( 
							   ".intval($args['cd_atividade']).", 
							   ".intval($args['cd_usuario']).", 
							   '".$ar_status_inicio[strtoupper(trim($args['cd_gerencia_destino']))]."',
							   'Atividade NÃO ATENDEU à necessidade do usuário: ".chr(10).chr(10)."Complemento:".chr(10).str_replace("'","´",trim($args['complemento_conclusao'])).chr(10).chr(10).
							   "Data envio para teste: ' || (SELECT TO_CHAR(dt_env_teste,'DD/MM/YYYY HH24:MI:SS') FROM projetos.atividades WHERE numero = ".intval($args['cd_atividade']).") || '
							   ".chr(10)."Data limite para teste: ' || (SELECT TO_CHAR(dt_limite_testes,'DD/MM/YYYY') FROM projetos.atividades WHERE numero = ".intval($args['cd_atividade']).")
							 );			   
				
						UPDATE projetos.atividades 
						   SET complemento      = '".str_replace("'","´",trim($args['complemento_conclusao']))."',
							   status_atual     = '".$ar_status_inicio[strtoupper(trim($args['cd_gerencia_destino']))]."',
							   dt_env_teste     = NULL,
							   dt_limite_testes = NULL
						 WHERE numero = ".intval($args['cd_atividade']).";		
					  ";			
		}
		
		if($qr_sql != "")
		{
			#### ENVIAR EMAIL ####
			$quebra = chr(10);

			$para    = "COALESCE(funcoes.get_usuario(COALESCE(a.cod_testador, a.cod_solicitante)) || '@eletroceee.com.br;','') || COALESCE((funcoes.get_usuario(a.cod_solicitante)) || '@eletroceee.com.br','') ||
			           (CASE WHEN cod_solicitante = 287 THEN ';' || funcoes.get_usuario(40) || '@eletroceee.com.br;' || funcoes.get_usuario(75) || '@eletroceee.com.br'  
				         ELSE ''
				   END)";
			$cc      = "COALESCE((funcoes.get_usuario(a.cod_atendente)) || '@eletroceee.com.br','')";
			
			if(trim($args['fl_concluir']) == "AP") 
			{
				$assunto = "'(' || UPPER(COALESCE(l.descricao, '')) || ') Atividade nº ".intval($args['cd_atividade'])."'";
			}
			else if(trim($args['fl_concluir']) == "NA")
			{
				$assunto = "'(NÃO ATENDEU) Atividade nº ".intval($args['cd_atividade'])."'";
			}

			$mensagem = "Prezado(a): ' || COALESCE(funcoes.get_usuario_nome(COALESCE(a.cod_testador, a.cod_solicitante)),'') || '".$quebra.$quebra;

			$mensagem.= "-------------------------------------------------------------".$quebra.$quebra;
			
			if(trim($args['fl_concluir']) == "AP") 
			{
				$mensagem.= "A atividade abaixo foi ' || UPPER(COALESCE(l.descricao, '')) || '.".$quebra.$quebra;
			}
			else if(trim($args['fl_concluir']) == "NA")
			{
				$mensagem.= "A atividade abaixo NÃO ATENDEU.".$quebra.$quebra;
				$mensagem.= "Complemento:".$quebra.$quebra.trim($args['complemento_conclusao']).$quebra.$quebra;
			}			

			$mensagem.= "-------------------------------------------------------------".$quebra;
			
			$mensagem.= "Atividade: ".intval($args['cd_atividade']).$quebra;
			$mensagem.= "Solicitante: ' || COALESCE(funcoes.get_usuario_nome(a.cod_solicitante),'') || '".$quebra;
			$mensagem.= "Atendente: ' || COALESCE(funcoes.get_usuario_nome(a.cod_atendente),'') || '".$quebra;
			$mensagem.= "Situação: ' || COALESCE(l.descricao, '') || '".$quebra;
			$mensagem.= "-------------------------------------------------------------".$quebra;
			$mensagem.= "DATA LIMITE PARA TESTES: ' || COALESCE(TO_CHAR(a.dt_limite_testes, 'DD/MM/YYYY'),'') || '".$quebra;
			$mensagem.= "Testador: ' || COALESCE(funcoes.get_usuario_nome(COALESCE(a.cod_testador, a.cod_solicitante)),'') || '".$quebra;
			$mensagem.= "-------------------------------------------------------------".$quebra;
			$mensagem.= "Descrição:".$quebra."' || COALESCE(a.descricao,'') || '".$quebra;
			$mensagem.= "-------------------------------------------------------------".$quebra;
			$mensagem.= "Link para Atividade: ".$quebra;
			$mensagem.= site_url('atividade/atividade_solicitacao/index/'.trim($args['cd_gerencia_destino']).'/'.intval($args['cd_atividade'])).$quebra;
			$mensagem.= "-------------------------------------------------------------".$quebra;		
			$mensagem.= "Justificativa da Manutenção: ".$quebra."' || COALESCE(a.problema,'') || '".$quebra;
			$mensagem.= "-------------------------------------------------------------".$quebra;
			$mensagem.= "Descrição da Manutenção: ".$quebra."' || COALESCE(a.solucao,'') || '".$quebra;
			$mensagem.= "-------------------------------------------------------------".$quebra;
			$mensagem.= "Observações: ".$quebra."' || COALESCE(a.observacoes,'') || '".$quebra;
			$mensagem.= "-------------------------------------------------------------".$quebra;			
			
			$qr_sql.= "
						INSERT INTO projetos.envia_emails 
							 ( 
							   dt_envio, 
							   de,
							   para, 
							   cc,	
							   cco, 
							   assunto,
							   texto,
							   cd_evento	 
							 ) 
						SELECT CURRENT_TIMESTAMP AS dt_envio, 
							   'Controle de Atividades (Solicitado pela ' || a.divisao || ')' AS de,
							   ".$para." AS para, 
							   ".$cc." AS cc, 
							   '' AS cco,
							   ".$assunto." AS assunto, 
							   '".$mensagem."' AS texto,
							   131 AS cd_evento
						  FROM projetos.atividades a
						  LEFT JOIN public.listas l
						    ON l.codigo    = a.status_atual 
						   AND l.categoria = 'STAT'
						 WHERE a.numero = ".intval($args['cd_atividade']).";
			          ";			
		}
		
		#echo "<PRE>$qr_sql</PRE>"; exit;
        $result = $this->db->query($qr_sql);
    }

    public function valida_participante($cd_empresa, $cd_registro_empregado, $cd_sequencia)
    {
    	$qr_sql = "
    	   SELECT COUNT(*)
			 FROM public.participantes
			WHERE cd_empresa = ".intval($cd_empresa)."
			  AND cd_registro_empregado = ".intval($cd_registro_empregado)."
			  AND seq_dependencia = ".intval($cd_sequencia).";";

		return $this->db->query($qr_sql)->row_array();
    }

    public function get_gerente_supervisor($cd_gerencia)
    {
    	$qr_sql = "
    		SELECT usuario || '@eletroceee.com.br' AS ds_email 
			  FROM projetos.usuarios_controledi
			 WHERE divisao = '".trim($cd_gerencia)."'
			   AND (tipo = 'G' OR (indic_13 = 'S' AND tipo != 'X'));";

		return $this->db->query($qr_sql)->result_array();
    }
	
    function atividade_filha(&$result, $args=array())
    {
        $qr_sql = "
					SELECT numero
					  FROM projetos.atividades 
					 WHERE cd_atividade_origem = ".intval($args["numero"])." 
					 ORDER BY numero
			      ";
        $result = $this->db->query($qr_sql);
    }	
	
    function getArea(&$result, $args=array())
    {
        $qr_sql = "
					SELECT area
					  FROM projetos.atividades 
					 WHERE numero = ".intval($args["numero"])." 
			      ";
        $result = $this->db->query($qr_sql);
    }	
}
?>