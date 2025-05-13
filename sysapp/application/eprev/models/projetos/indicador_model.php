<?php
class Indicador_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function tipos( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT DISTINCT i.cd_tipo AS value, 
						   CASE WHEN i.cd_tipo = 'A' THEN 'Gerência'
						        WHEN i.cd_tipo = 'G' THEN 'Gestão'
								ELSE i.cd_tipo
						   END AS text 
					  FROM indicador.indicador i 
					 WHERE i.dt_exclusao IS NULL
					 ORDER BY text
				   ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function controles( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT DISTINCT ic.cd_indicador_controle AS value, 
						   ic.ds_indicador_controle AS text 
					  FROM indicador.indicador_controle ic 
					  JOIN indicador.indicador i
					    ON i.cd_indicador_controle = ic.cd_indicador_controle
					   AND i.dt_exclusao IS NULL
					 WHERE ic.dt_exclusao IS NULL
					 ORDER BY text
				   ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function grupos( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT g.cd_indicador_grupo AS value, 
						   g.ds_indicador_grupo AS text 
					  FROM indicador.indicador_grupo g 
					  JOIN indicador.indicador i 
						ON i.cd_indicador_grupo = g.cd_indicador_grupo 
					 WHERE g.dt_exclusao IS NULL
					 ORDER BY g.ds_indicador_grupo;";
			 
		$result = $this->db->query($qr_sql);
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT ind.cd_indicador,
						   ind.ds_indicador,
						   gru.ds_indicador_grupo,
						   ure.nome AS nome_usuario_responsavel,
						   ind.nr_ordem,
						   ind.fl_igp,
						   ind.cd_tipo,
						   (CASE WHEN ind.fl_igp = 'S' THEN 'Sim'
								 WHEN ind.fl_igp = 'N' THEN 'Não'
						   END) AS igp, 
						   ind.fl_poder,
						   (CASE WHEN ind.fl_poder = 'S' THEN 'Sim'
								 WHEN ind.fl_poder = 'N' THEN 'Não'
						   END) AS poder, 
						   p.procedimento AS ds_processo,
						   TO_CHAR(indicador.get_dt_atualizacao_indicador(ind.plugin_tabela),'DD/MM/YYYY HH24:MI:SS') AS dt_atualizacao,
						   TO_CHAR(indicador.data_limite_atualizar(ind.cd_indicador),'DD/MM/YYYY') AS dt_limite_atualizar,
						   ic.ds_indicador_controle,
						   funcoes.get_usuario_nome(ind.cd_responsavel) AS responsavel,
						   funcoes.get_usuario_nome(ind.cd_substituto) AS substituto,
						   CASE WHEN indicador.data_limite_atualizar(ind.cd_indicador) <= CURRENT_DATE 
								THEN 'label label-important'
								ELSE 'label label-success'
						   END AS status_atualizar,
						   (CASE WHEN (SELECT COUNT(*) 
							             FROM indicador.indicador_label il
							            WHERE il.cd_indicador = ind.cd_indicador
						                  AND TRIM(COALESCE(il.ds_coluna_tabela,'')) <> ''
						                  AND TRIM(COALESCE(il.ds_integracao_sa,'')) <> '') > 0 
							     THEN 'S'
								 ELSE 'N'
						   END) AS fl_sa
					  FROM indicador.indicador ind
					  JOIN indicador.indicador_grupo gru 
						ON gru.cd_indicador_grupo = ind.cd_indicador_grupo
					  JOIN projetos.usuarios_controledi ure 
						ON ure.codigo=ind.cd_usuario_responsavel
					  JOIN indicador.indicador_controle ic				
						ON ic.cd_indicador_controle = ind.cd_indicador_controle						
					  LEFT JOIN projetos.processos p
						ON p.cd_processo = ind.cd_processo				
					 WHERE ind.dt_exclusao IS NULL
					   ".(trim($args['cd_indicador_grupo']) != '' ? "AND ind.cd_indicador_grupo = ".intval($args['cd_indicador_grupo']) : '')."
					   ".(trim($args['fl_igp']) != '' ? "AND ind.fl_igp = '".trim($args['fl_igp'])."'" : '')."
					   ".(trim($args['fl_poder']) != '' ? "AND ind.fl_poder = '".trim($args['fl_poder'])."'" : '')."
					   ".(trim($args['cd_processo']) != '' ? "AND ind.cd_processo = ".intval($args['cd_processo']) : '')."
					   ".(trim($args['cd_indicador_controle']) != '' ? "AND ind.cd_indicador_controle = ".intval($args['cd_indicador_controle']) : '')."
					   ".(trim($args['cd_tipo']) != '' ? "AND ind.cd_tipo = '".trim($args['cd_tipo'])."'" : '')."
					 ORDER BY ind.ds_indicador
			      ";

		$result = $this->db->query($qr_sql);
	}

	function carregar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_indicador,
						   i.cd_indicador_grupo,
						   i.cd_processo,
						   i.cd_usuario_responsavel,
						   i.ds_indicador,
						   i.cd_responsavel,
						   i.cd_substituto,
						   i.ds_dimensao_qualidade,
						   i.nr_ordem,
						   i.ds_formula,
						   TO_CHAR(i.dt_pronto,'DD/MM/YYYY') AS dt_pronto,
						   TO_CHAR(i.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
						   TO_CHAR(i.dt_limite_atualizar,'DD/MM/YYYY') AS dt_limite_atualizar,
						   i.cd_usuario_exclusao,
						   i.cd_indicador_controle,
						   i.cd_indicador_unidade_medida,
						   i.ds_meta,
						   i.cd_tipo,
						   i.ds_missao,
						   i.plugin_nome,
						   i.plugin_tabela,
						   i.tp_analise,
						   i.cd_gerencia,
						   i.fl_periodo,
						   i.fl_igp,
						   i.fl_poder,
						   i.fl_config_sa,
						   i.qt_periodo_anterior,
						   (SELECT COUNT(*) 
							  FROM indicador.indicador_tabela tab 
							 WHERE tab.cd_indicador = i.cd_indicador
							   AND tab.dt_exclusao IS NULL) AS tl_indicador_tabela
					  FROM indicador.indicador i
					 WHERE i.cd_indicador = ".intval($args['cd_indicador']).";
			      ";
	
		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_indicador'])== 0)
		{
			$cd_indicador = $this->db->get_new_id("indicador.indicador", "cd_indicador");
		
			$qr_sql = "
				INSERT INTO indicador.indicador 
				     ( 
					    cd_indicador,
						cd_indicador_grupo,
						cd_processo,
						cd_usuario_responsavel,
						ds_indicador,
						cd_responsavel,
						cd_substituto,
						ds_dimensao_qualidade,
						nr_ordem,
						cd_indicador_controle,
						dt_limite_atualizar,
						ds_formula,
						cd_indicador_unidade_medida,
						ds_meta,
						ds_missao,
						cd_tipo,
						plugin_nome,
						plugin_tabela,
						tp_analise,
						cd_gerencia,
						fl_periodo,
						fl_igp,
						fl_poder,
						qt_periodo_anterior
			         ) 
			    VALUES 
				     ( 
					    ".intval($cd_indicador).",
						".intval($args["cd_indicador_grupo"]).",
						".intval($args["cd_processo"]).",
						".intval($args["cd_usuario_responsavel"]).",
						'".trim($args["ds_indicador"])."',
						".intval($args["cd_responsavel"]).",
						".intval($args["cd_substituto"]).",
						".(trim($args['ds_dimensao_qualidade']) != '' ? "'".trim($args['ds_dimensao_qualidade'])."'" : "DEFAULT").",
						".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
						".(trim($args['cd_indicador_controle']) != '' ? intval($args['cd_indicador_controle']) : "DEFAULT").",
						".(trim($args['dt_limite_atualizar']) != '' ? "TO_DATE('".trim($args['dt_limite_atualizar'])."','DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['ds_formula']) != '' ? "'".trim($args['ds_formula'])."'" : "DEFAULT").",
						".(trim($args['cd_indicador_unidade_medida']) != '' ? intval($args['cd_indicador_unidade_medida']) : "DEFAULT").",
						".(trim($args['ds_meta']) != '' ? "'".trim($args['ds_meta'])."'" : "DEFAULT").",
						".(trim($args['ds_missao']) != '' ? "'".trim($args['ds_missao'])."'" : "DEFAULT").",
						'".trim($args["cd_tipo"])."',
						".(trim($args['plugin_nome']) != '' ? "'".trim($args['plugin_nome'])."'" : "DEFAULT").",
						".(trim($args['plugin_tabela']) != '' ? "'".trim($args['plugin_tabela'])."'" : "DEFAULT").",
						".(trim($args['tp_analise']) != '' ? "'".trim($args['tp_analise'])."'" : "DEFAULT").",
						".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",
						".(trim($args['fl_periodo']) != '' ? "'".trim($args['fl_periodo'])."'" : "DEFAULT").",
						".(trim($args['fl_igp']) != '' ? "'".trim($args['fl_igp'])."'" : "DEFAULT").",
						".(trim($args['fl_poder']) != '' ? "'".trim($args['fl_poder'])."'" : "DEFAULT").",
						".(trim($args['qt_periodo_anterior']) != '' ? intval($args['qt_periodo_anterior']) : "DEFAULT")."
			         );";
		}
		else
		{
			$cd_indicador = intval($args['cd_indicador']);
		
			$qr_sql = "
						UPDATE indicador.indicador 
						   SET cd_indicador_grupo          = ".intval($args["cd_indicador_grupo"]).",
							   cd_processo                 = ".intval($args["cd_processo"]).",
							   ds_indicador                = '".trim($args["ds_indicador"])."',
							   cd_responsavel              = ".intval($args["cd_responsavel"]).",
							   cd_substituto               = ".intval($args["cd_substituto"]).",
							   ds_dimensao_qualidade       = ".(trim($args['ds_dimensao_qualidade']) != '' ? "'".trim($args['ds_dimensao_qualidade'])."'" : "DEFAULT").",
							   nr_ordem                    = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
							   cd_indicador_controle       = ".(trim($args['cd_indicador_controle']) != '' ? intval($args['cd_indicador_controle']) : "DEFAULT").",
							   dt_limite_atualizar         = ".(trim($args['dt_limite_atualizar']) != '' ? "TO_DATE('".trim($args['dt_limite_atualizar'])."','DD/MM/YYYY')" : "DEFAULT").",
							   ds_formula                  = ".(trim($args['ds_formula']) != '' ? "'".trim($args['ds_formula'])."'" : "DEFAULT").",
							   cd_indicador_unidade_medida = ".(trim($args['cd_indicador_unidade_medida']) != '' ? intval($args['cd_indicador_unidade_medida']) : "DEFAULT").",
							   ds_meta                     = ".(trim($args['ds_meta']) != '' ? "'".trim($args['ds_meta'])."'" : "DEFAULT").",
							   ds_missao                   = ".(trim($args['ds_missao']) != '' ? "'".trim($args['ds_missao'])."'" : "DEFAULT").",
							   cd_tipo                     = '".trim($args["cd_tipo"])."',
							   plugin_nome                 = ".(trim($args['plugin_nome']) != '' ? "'".trim($args['plugin_nome'])."'" : "DEFAULT").",
							   plugin_tabela               = ".(trim($args['plugin_tabela']) != '' ? "'".trim($args['plugin_tabela'])."'" : "DEFAULT").",
							   tp_analise                  = ".(trim($args['tp_analise']) != '' ? "'".trim($args['tp_analise'])."'" : "DEFAULT").",
							   cd_gerencia                 = ".(trim($args['cd_gerencia']) != '' ? "'".trim($args['cd_gerencia'])."'" : "DEFAULT").",
							   fl_periodo                  = ".(trim($args['fl_periodo']) != '' ? "'".trim($args['fl_periodo'])."'" : "DEFAULT").",
							   fl_igp                      = ".(trim($args['fl_igp']) != '' ? "'".trim($args['fl_igp'])."'" : "DEFAULT").",
							   fl_poder                    = ".(trim($args['fl_poder']) != '' ? "'".trim($args['fl_poder'])."'" : "DEFAULT").",
							   qt_periodo_anterior         = ".(trim($args['qt_periodo_anterior']) != '' ? intval($args['qt_periodo_anterior']) : "DEFAULT")."
						 WHERE cd_indicador = ".intval($args['cd_indicador']).";
						 
						UPDATE indicador.indicador_tabela
						   SET ds_indicador_tabela = '".trim($args["ds_indicador"])."',
						       cd_indicador_grupo  = ".intval($args["cd_indicador_grupo"]).",
							   cd_processo         = ".intval($args["cd_processo"]).",
							   cd_tipo             = '".trim($args["cd_tipo"])."'
						 WHERE dt_exclusao           IS NULL
						   AND dt_fechamento_periodo IS NULL
						   AND cd_indicador          = ".intval($args['cd_indicador']).";
				      ";
		}

		#echo "<PRE>$qr_sql</PRE>"; exit;
		
		$result = $this->db->query($qr_sql);
		
		return $cd_indicador;
	}

	function excluir( &$result, $args=array() )
	{
		$qr_sql = " 
					UPDATE indicador.indicador 
					   SET dt_exclusao         = CURRENT_TIMESTAMP, 
						   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
					 WHERE cd_indicador = ".intval($args['cd_indicador']).";
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function gerencia( &$result, $args=array() )
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM projetos.divisoes
             ORDER BY nome;";

		$result = $this->db->query($qr_sql);
    }
	
	function periodos_abertos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT it.cd_indicador_tabela, 
			       ip.ds_periodo
			  FROM indicador.indicador_tabela it
			  JOIN indicador.indicador_periodo ip 
			    ON it.cd_indicador_periodo = ip.cd_indicador_periodo
			 WHERE it.cd_indicador = ".intval($args['cd_indicador'])." 
			   AND current_timestamp BETWEEN ip.dt_inicio AND ip.dt_fim 
			   AND ip.dt_exclusao IS NULL
			   AND it.dt_fechamento_periodo IS NULL
			   AND it.dt_exclusao IS NULL;";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function novo_periodo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ip.ds_periodo,
				   ip.cd_indicador_periodo
			  FROM indicador.indicador_periodo ip
			 WHERE CURRENT_TIMESTAMP BETWEEN ip.dt_inicio AND ip.dt_fim 
			   AND ip.dt_exclusao IS NULL
			   AND NOT EXISTS ( SELECT per.ds_periodo
								  FROM indicador.indicador_tabela tab
								  JOIN indicador.indicador_periodo per 
									ON tab.cd_indicador_periodo = per.cd_indicador_periodo
								 WHERE tab.cd_indicador = ".intval($args['cd_indicador'])." 
								   AND ( NOT CURRENT_TIMESTAMP BETWEEN per.dt_inicio AND per.dt_fim 
										 AND per.dt_exclusao IS NULL 
										  OR tab.dt_fechamento_periodo IS NOT NULL )
								   AND tab.dt_exclusao IS NULL
								   AND per.cd_indicador_periodo = ip.cd_indicador_periodo)
			 ORDER BY ip.ds_periodo DESC;";
		
		$result = $this->db->query($qr_sql);
	}
	
	function periodos_fechados( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT it.cd_indicador_tabela, 
				   ip.ds_periodo
			  FROM indicador.indicador_tabela it
			  JOIN indicador.indicador_periodo ip
				ON it.cd_indicador_periodo=ip.cd_indicador_periodo
			 WHERE it.cd_indicador = ".intval($args['cd_indicador'])."  
			   AND ( NOT CURRENT_TIMESTAMP BETWEEN ip.dt_inicio AND ip.dt_fim 
					 AND ip.dt_exclusao IS NULL
					  OR it.dt_fechamento_periodo IS NOT NULL)
			   AND it.dt_exclusao IS NULL;";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function fechar_periodo_tabela( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP,  
			       cd_usuario_fechamento_periodo = ".intval($args['cd_usuario'])."   
		     WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function criar_tabela( &$result, $args=array() )
	{
		$cd_indicador_tabela = $this->db->get_new_id("indicador.indicador_tabela", "cd_indicador_tabela");

		$qr_sql=" 
			INSERT INTO indicador.indicador_tabela
				 ( 
					cd_indicador_tabela, 
					cd_indicador, 
					ds_indicador_tabela,
					cd_processo,
					cd_indicador_grupo,
					cd_indicador_periodo, 
					dt_inclusao, 
					cd_usuario_inclusao 
				 )
			VALUES
				 ( 
					".intval($cd_indicador_tabela).", 
					".intval($args['cd_indicador']).", 
					(SELECT i.ds_indicador 
					   FROM indicador.indicador i 
					  WHERE i.cd_indicador = ".intval($args['cd_indicador'])."
						AND i.dt_exclusao IS NULL) || ' - ' || (SELECT ip.ds_periodo 
																  FROM indicador.indicador_periodo ip 
																 WHERE ip.cd_indicador_periodo = ".intval($args['cd_indicador_periodo'])." 
																   AND ip.dt_exclusao IS NULL 
																   AND CURRENT_TIMESTAMP BETWEEN ip.dt_inicio AND ip.dt_fim), 
					(SELECT i.cd_processo 
					   FROM indicador.indicador i 
					  WHERE i.cd_indicador = ".intval($args['cd_indicador'])."
						AND i.dt_exclusao IS NULL), 
					(SELECT i.cd_indicador_grupo 
					   FROM indicador.indicador i 
					  WHERE i.cd_indicador = ".intval($args['cd_indicador'])."
						AND i.dt_exclusao IS NULL), 						
					".intval($args['cd_indicador_periodo']).", 
					CURRENT_TIMESTAMP, 
					".intval($args['cd_usuario'])." 
				 );";

		$result = $this->db->query($qr_sql);
		
		return $cd_indicador_tabela;
	}
	
	function listar_rotulos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT il.cd_indicador_label,
			       il.id_label,
				   il.ds_label,
				   il.ds_coluna_tabela,
				   il.ds_integracao_sa,				   
				   il.ds_tipo_sa,				   
				   il.ds_modelo_sa
			  FROM indicador.indicador_label il
			 WHERE il.dt_exclusao IS NULL
			   AND il.cd_indicador = ".intval($args['cd_indicador']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_rotulo( &$result, $args=array() ) 
	{
		if(intval($args['cd_indicador_label']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador.indicador_label
				     (
					   id_label,
					   ds_label,
					   ds_coluna_tabela,
					   ds_integracao_sa,
					   ds_modelo_sa,
					   ds_tipo_sa,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao,
					   cd_indicador
					 )
				VALUES
					 (
						".intval($args['id_label']).",
						'".trim($args['ds_label'])."',
						'".trim($args['ds_coluna_tabela'])."',
						'".trim($args['ds_integracao_sa'])."',
						'".trim($args['ds_modelo_sa'])."',
						'".trim($args['ds_tipo_sa'])."',
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario']).",
						".intval($args['cd_indicador'])."
					 )";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador.indicador_label
				   SET id_label             = ".intval($args['id_label']).",
				       ds_label             = '".trim($args['ds_label'])."',
				       ds_coluna_tabela     = '".trim($args['ds_coluna_tabela'])."',
				       ds_integracao_sa     = '".trim($args['ds_integracao_sa'])."',
				       ds_modelo_sa         = '".trim($args['ds_modelo_sa'])."',
				       ds_tipo_sa           = '".trim($args['ds_tipo_sa'])."',
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_indicador_label = ".intval($args['cd_indicador_label']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_rotulo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_indicador_label,
			       id_label,
				   ds_label,
				   ds_coluna_tabela,
				   ds_integracao_sa,				   
				   ds_modelo_sa,			   
				   ds_tipo_sa			   
			  FROM indicador.indicador_label
			 WHERE dt_exclusao IS NULL
			   AND cd_indicador_label = ".intval($args['cd_indicador_label']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function verifica_nr_label( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
			  FROM indicador.indicador_label
			 WHERE dt_exclusao IS NULL
			   AND cd_indicador       = ".intval($args['cd_indicador'])."
			   AND cd_indicador_label != ".intval($args['cd_indicador_label'])."
			   AND id_label           IN (".intval($args['id_label']).");";

		$result = $this->db->query($qr_sql);
	}
	
	function excluir_rotulo( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE indicador.indicador_label
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_indicador_label = ".intval($args['cd_indicador_label']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function saveConfigSA(&$result, $args=array())
	{
		$qr_sql = "
					UPDATE indicador.indicador
					   SET fl_config_sa = '".trim($args['fl_config_sa'])."'
					 WHERE cd_indicador = ".intval($args['cd_indicador']).";
			      ";
		#echo $qr_sql; exit;	 
		$result = $this->db->query($qr_sql);
	}

	function carregar_grafico($cd_indicador_tabela)
	{
		$sql = " 
		SELECT grafico.* 
		FROM indicador.indicador_tabela it 
		JOIN indicador.indicador_tabela_grafico grafico ON grafico.cd_indicador_tabela=it.cd_indicador_tabela 
		WHERE grafico.dt_exclusao IS NULL 
		AND it.cd_indicador_tabela={cd_indicador_tabela}
		";
		esc('{cd_indicador_tabela}', $cd_indicador_tabela, $sql, 'int');
		$query = $this->db->query( $sql . ' LIMIT 1 ' );

		return $query->row_array();
	}

	/**
	 *  Retorna um array com informações das tabela 'indicador', 'indicador_tabela', 'indicador_periodo'
	 */
	function listar_indicador_tabela_aberta_de_indicador($cd_indicador)
	{
		$return = array();
		$query = $this->db->query( "
									SELECT *
		                              FROM indicador.listar_indicador_tabela_aberta_de_indicador 
		                             WHERE cd_indicador = ".intval($cd_indicador)."
		                             ORDER BY nr_ano_referencia ASC
								");
		/*
		$query = $this->db->query( "
									SELECT i.ds_indicador AS ds_indicador, 
									       i.fl_periodo,
										   i.qt_periodo_anterior,
										   CASE WHEN i.fl_periodo = 'N' 
										        THEN ''
 										        ELSE ip.ds_periodo
										   END AS ds_periodo, 
										   ip.nr_ano_referencia, 
										   it.*
		                              FROM indicador.indicador AS i 
		                              JOIN indicador.indicador_tabela AS it 
									    ON i.cd_indicador=it.cd_indicador
		                              JOIN indicador.indicador_periodo AS ip 
									    ON ip.cd_indicador_periodo=it.cd_indicador_periodo
		                             WHERE it.dt_exclusao          IS NULL
		                               AND it.cd_indicador          = ".intval($cd_indicador)."
		                               AND it.dt_fechamento_periodo IS NULL
		                             ORDER BY ip.nr_ano_referencia ASC
		                           ");
		*/
		$return = $query->result_array();
		return $return;
	}
	
	function usuarioCombo( &$result, $args=array() )
	{
        $qr_sql = "
                SELECT uc.codigo AS value,
                       uc.nome AS text
                  FROM projetos.usuarios_controledi uc
                 WHERE uc.divisao NOT IN ('FC','SNG','CF','CEE')
                   AND (uc.tipo NOT IN ('X') OR uc.codigo = " . intval($args['cd_usuario']).")
                 ORDER BY text";

        $result = $this->db->query($qr_sql);
	}	

	function getColunasTabela( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT column_name AS value,
					       column_name AS text
					  FROM information_schema.columns 
					 WHERE (table_schema ||'.'|| table_name) = (SELECT plugin_tabela FROM indicador.indicador i WHERE i.cd_indicador = ".intval($args['cd_indicador']).")
					   AND column_name NOT IN (
									'cd_' || table_name,
									'cd_indicador_tabela',
									'fl_media',
									'dt_inclusao',
									'cd_usuario_inclusao',
									'dt_alteracao',
									'cd_usuario_alteracao',
									'dt_exclusao',
									'cd_usuario_exclusao'
								   )
					 ORDER BY value
				  ";

        $result = $this->db->query($qr_sql);
	}

	function rotuloValor( &$result, $args=array() )
	{
        $qr_sql = "
					SELECT ".$args['ds_coluna_tabela']." AS valor
					  FROM ".$args['indicador_plugin_tabela']."
					 WHERE dt_exclusao IS NULL
					   AND TRIM(COALESCE(fl_media,'N')) IN ('','N')
					 ORDER BY dt_referencia DESC
					 LIMIT 1
				  ";

        $result = $this->db->query($qr_sql);
	}
}
?>