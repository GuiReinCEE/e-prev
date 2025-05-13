<?php
class Indicador_tabela_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT CASE WHEN ind.cd_indicador = 13 THEN (SELECT lit.cd_indicador_tabela FROM indicador.listar_indicador_tabela_aberta_de_indicador lit WHERE lit.cd_indicador = 40 ORDER BY nr_ano_referencia ASC LIMIT 1)
							    WHEN ind.cd_indicador = 14 THEN (SELECT lit.cd_indicador_tabela FROM indicador.listar_indicador_tabela_aberta_de_indicador lit WHERE lit.cd_indicador = 41 ORDER BY nr_ano_referencia ASC LIMIT 1)
							    WHEN ind.cd_indicador = 15 THEN (SELECT lit.cd_indicador_tabela FROM indicador.listar_indicador_tabela_aberta_de_indicador lit WHERE lit.cd_indicador = 128 ORDER BY nr_ano_referencia ASC LIMIT 1)
							    ELSE tab.cd_indicador_tabela
						   END AS cd_indicador_tabela,
						   gru.ds_indicador_grupo, 
						   ind.cd_indicador, 
						   ind.cd_tipo,
						   (CASE WHEN ind.cd_tipo = 'G' THEN 'Gestão'
						         ELSE 'Auxiliar'
						    END) AS ds_tipo,
						   tab.ds_indicador_tabela AS ds_indicador, 
						   CASE WHEN ind.fl_periodo = 'N' 
								THEN ''
								ELSE per.ds_periodo
						   END AS ds_periodo,						   
						   CASE WHEN ind.fl_periodo = 'N' 
								THEN NULL
								ELSE per.nr_ano_referencia
						   END AS nr_ano_referencia,						   
						   p.procedimento AS ds_processo,
                           ind.plugin_nome,
						   ind.fl_igp,
						   (CASE WHEN ind.fl_igp = 'S' THEN 'Sim'
								 WHEN ind.fl_igp = 'N' THEN 'Não'
						   END) AS igp, 
						   ind.fl_poder,
						   (CASE WHEN ind.fl_poder = 'S' THEN 'Sim'
								 WHEN ind.fl_poder = 'N' THEN 'Não'
						   END) AS poder, 
						   COALESCE(ind.nr_ordem,0) AS nr_ordem,
						   TO_CHAR(ind.dt_exclusao,'DD/MM/YYYY') AS dt_exclusao,
						   ic.ds_indicador_controle,
						   TO_CHAR(indicador.get_dt_atualizacao_indicador(ind.plugin_tabela),'DD/MM/YYYY HH24:MI:SS') AS dt_atualizacao,
						   TO_CHAR(indicador.data_limite_atualizar(ind.cd_indicador),'DD/MM/YYYY') AS dt_limite_atualizar,
						   funcoes.get_usuario_nome(ind.cd_responsavel) AS responsavel,
						   funcoes.get_usuario_nome(ind.cd_substituto) AS substituto,
						   CASE WHEN indicador.data_limite_atualizar(ind.cd_indicador) <= CURRENT_DATE 
						        THEN 'label label-important'
								ELSE 'label label-success'
						   END AS status_atualizar 
					  FROM indicador.indicador ind
					  JOIN indicador.indicador_tabela tab 
						ON tab.cd_indicador = ind.cd_indicador
					  JOIN indicador.indicador_controle ic				
						ON ic.cd_indicador_controle = ind.cd_indicador_controle							
					  JOIN indicador.indicador_grupo gru 
						ON gru.cd_indicador_grupo = tab.cd_indicador_grupo						
					  JOIN indicador.indicador_periodo per 
						ON per.cd_indicador_periodo = tab.cd_indicador_periodo
					  LEFT JOIN projetos.processos p
					    ON p.cd_processo = tab.cd_processo
					 WHERE gru.dt_exclusao IS NULL
					   AND tab.dt_exclusao IS NULL
					   AND per.dt_exclusao IS NULL
                       AND (CASE WHEN ind.fl_periodo = 'N' 
                                 THEN (SELECT MAX(tab1.cd_indicador_tabela) 
								         FROM indicador.indicador_tabela tab1 
										WHERE tab1.cd_indicador = ind.cd_indicador)
                                 ELSE tab.cd_indicador_tabela
                           END) = tab.cd_indicador_tabela						   
                        ".(intval($args['cd_processo']) > 0 ? "AND tab.cd_processo = ".$args["cd_processo"] : "")."
						".(intval($args['cd_indicador_grupo']) > 0 ? "AND tab.cd_indicador_grupo = ".$args["cd_indicador_grupo"] : "")."
						".(intval($args['cd_indicador_periodo']) > 0 ? " AND (per.cd_indicador_periodo = ".$args["cd_indicador_periodo"]." OR ind.fl_periodo = 'N')" : "")."					   
						".(trim($args['fl_igp']) != '' ? "AND ind.fl_igp = '".trim($args['fl_igp'])."'" : '')."
					    ".(trim($args['fl_poder']) != '' ? "AND ind.fl_poder = '".trim($args['fl_poder'])."'" : '')."
					    ".(trim($args['cd_tipo']) != '' ? "AND ind.cd_tipo = '".trim($args['cd_tipo'])."'" : '')."
						".(trim($args['fl_encerrado']) == "S" ? "AND ind.dt_exclusao IS NOT NULL" : '')."
						".(trim($args['fl_encerrado']) == "N" ? "AND ind.dt_exclusao IS NULL" : '')."
						".(trim($args['cd_indicador_controle'])  > 0 ? "AND ind.cd_indicador_controle = ".intval($args['cd_indicador_controle']) : '')."
					 ORDER BY gru.ds_indicador_grupo,
					          COALESCE(ind.nr_ordem,0),
					          ds_indicador,
							  per.ds_periodo,
							  p.procedimento
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}

    function manutencaoListar( &$result, $args=array() )
	{
        $qr_sql = "
			SELECT ind.plugin_nome,
				   gru.ds_indicador_grupo,
				   ind.ds_indicador,
				   ind.cd_processo,
				   ind.fl_igp,
				   ind.cd_tipo,
				   (CASE WHEN ind.cd_tipo = 'G' THEN 'Gestão'
				         ELSE 'Auxiliar'
				    END) AS ds_tipo,
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
				   END AS status_atualizar				   
			  FROM indicador.indicador ind
			  JOIN indicador.indicador_grupo gru
				ON gru.cd_indicador_grupo = ind.cd_indicador_grupo
			  JOIN indicador.indicador_controle ic				
			    ON ic.cd_indicador_controle = ind.cd_indicador_controle				
			  LEFT JOIN projetos.processos p
				ON p.cd_processo = ind.cd_processo
			 WHERE ind.dt_exclusao IS NULL
			   AND gru.dt_exclusao IS NULL
			   AND ind.plugin_nome IS NOT NULL
			   ".(($args['cd_tipo'] == "A" and (!gerencia_in(array("I"))))?  "AND ind.cd_gerencia = '".$this->session->userdata('divisao')."'": '')."
			   ".(intval($args['cd_processo']) > 0 ? "AND ind.cd_processo = ".$args["cd_processo"] : "")."
			   ".(intval($args['cd_indicador_grupo']) > 0 ? "AND ind.cd_indicador_grupo = ".$args["cd_indicador_grupo"] : "")."
			   ".(intval($args['cd_indicador_controle']) > 0 ? "AND ind.cd_indicador_controle = ".$args["cd_indicador_controle"] : "")."
			   ".(trim($args['fl_igp']) != '' ? "AND ind.fl_igp = '".trim($args['fl_igp'])."'" : '')."
			   ".(trim($args['fl_poder']) != '' ? "AND ind.fl_poder = '".trim($args['fl_poder'])."'" : '')."
			   ".(trim($args['cd_tipo']) != '' ? "AND ind.cd_tipo = '".trim($args['cd_tipo'])."'" : '').";";

		//echo "<pre>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}


	function carregar($cd)
	{
		$row = Array();
		$qr_sql = " 
					SELECT cd_indicador
						   cd_indicador_grupo
		                   cd_processo
		                   cd_usuario_responsavel
		                   ds_indicador
		                   ds_dimensao_qualidade
		                   nr_ordem
		                   ds_formula
		                   TO_CHAR(dt_pronto,'DD/MM/YYYY') as dt_pronto
		                   TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		                   cd_usuario_exclusao 
		                   cd_indicador_controle 
		                   cd_indicador_unidade_medida
		              FROM indicador.indicador 
				  ";
		$query = $this->db->query($qr_sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach($fields as $field)
		{
			$row[$field->name] = '';
		}
		$row['indicador_tabela'] = Array();

		if(intval($cd)>0)
		{
			$qr_sql.=" WHERE cd_indicador= ".intval($cd);
			$query=$this->db->query($qr_sql);

			if($query->row_array())
			{
				$row = $query->row_array();
				$row['indicador_tabela'] = array();

				$q=$this->db->query("
									SELECT * 
									  FROM indicador.indicador_tabela tab 
									 WHERE tab.cd_indicador = ".intval($cd)." 
									   AND tab.dt_exclusao IS NULL
									");
				$col=$q->result_array();
				foreach($col as $item)
				{
					$row['indicador_tabela'][]=$item;
				}
			}
		}

		return $row;
	}

	function salvar($args,&$msg=array())
	{
		/*if(intval($args['cd_indicador'])==0)
		{
			$sql="
			INSERT INTO indicador.indicador ( cd_indicador_grupo 
			, cd_processo 
			, cd_usuario_responsavel
			, ds_indicador 
			, ds_dimensao_qualidade
			, nr_ordem 
			, cd_indicador_controle 
			, ds_formula 
			, cd_indicador_unidade_medida 
			, ds_meta 
			) VALUES ( {cd_indicador_grupo} 
			, {cd_processo} 
			, {cd_usuario_responsavel}
			, '{ds_indicador}' 
			, '{ds_dimensao_qualidade}' 
			, {nr_ordem} 
			, {cd_indicador_controle} 
			, '{ds_formula}' 
			, {cd_indicador_unidade_medida} 
			, '{ds_meta}' 
			)
			";
		}
		else
		{
			$sql="
			UPDATE indicador.indicador SET 
			  cd_indicador_grupo = {cd_indicador_grupo} 
			, cd_processo = {cd_processo} 
			, ds_indicador = '{ds_indicador}' 
			, ds_dimensao_qualidade = '{ds_dimensao_qualidade}' 
			, nr_ordem = {nr_ordem} 
			, cd_indicador_controle = {cd_indicador_controle} 
			, ds_formula = '{ds_formula}' 
			, cd_indicador_unidade_medida = {cd_indicador_unidade_medida} 
			, ds_meta = '{ds_meta}' 
			 WHERE 
			cd_indicador = {cd_indicador} 
			";
		}

		esc("{cd_indicador_grupo}", $args["cd_indicador_grupo"], $sql, "int", FALSE);
		esc("{cd_processo}", $args["cd_processo"], $sql, "int", FALSE);
		esc("{ds_indicador}", $args["ds_indicador"], $sql, "str", FALSE);
		esc("{ds_dimensao_qualidade}", $args["ds_dimensao_qualidade"], $sql, "str", FALSE);
		esc("{nr_ordem}", $args["nr_ordem"], $sql, "int", FALSE);

		if( intval($args["cd_indicador_controle"])!=0 ){esc("{cd_indicador_controle}", $args["cd_indicador_controle"], $sql, "int", false);}else{esc("{cd_indicador_controle}", "NULL", $sql, "str", false);}
		if( intval($args["cd_indicador_unidade_medida"])!=0 ){esc("{cd_indicador_unidade_medida}", $args["cd_indicador_unidade_medida"], $sql, "int", false);}else{esc("{cd_indicador_unidade_medida}", 'NULL', $sql, "str", false);}

		esc("{ds_formula}", $args["ds_formula"], $sql, "str", FALSE);
		
		esc("{ds_meta}", $args["ds_meta"], $sql, "str", FALSE);
		esc("{cd_indicador}", $args["cd_indicador"], $sql, "int", FALSE);
		esc("{cd_usuario_responsavel}", $args["cd_usuario_responsavel"], $sql, "int", FALSE);

		try
		{
			$query = $this->db->query($sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}*/
	}

	function excluir($id)
	{
		$sql = " 
				UPDATE indicador.indicador 
				   SET dt_exclusao         = CURRENT_TIMESTAMP, 
				       cd_usuario_exclusao = {cd_usuario_exclusao} 
		         WHERE md5(cd_indicador::varchar)='{cd_indicador}' 
		       "; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_indicador}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 
	}

	function carregar_grafico($cd_indicador)
	{
		$qr_sql = " 
					SELECT grafico.* 
					  FROM indicador.indicador_tabela it 
					  JOIN indicador.indicador_tabela_grafico grafico 
					    ON grafico.cd_indicador_tabela = it.cd_indicador_tabela 
				     WHERE grafico.dt_exclusao IS NULL 
					   AND it.cd_indicador = ".intval($cd_indicador)."
					 LIMIT 1
				  ";
		$query = $this->db->query($qr_sql);
		return $query->row_array();
	}

	function info_indicador_tabela(&$result, $args=array())
	{
		$qr_sql = "	
					SELECT DISTINCT i.*,
                           it.ds_indicador_tabela,
						   c.ds_indicador_controle, 
						   u.ds_indicador_unidade_medida, 
						   CASE WHEN i.fl_periodo = 'N' 
								THEN ''
								ELSE ip.ds_periodo
						   END AS ds_periodo,
						   ig.ds_indicador_grupo,
						   p.procedimento AS ds_processo
					  FROM indicador.indicador i 
					  JOIN indicador.indicador_grupo ig 
					    ON ig.cd_indicador_grupo=i.cd_indicador_grupo
					  JOIN indicador.indicador_controle c 
					    ON c.cd_indicador_controle=i.cd_indicador_controle 
					  JOIN indicador.indicador_unidade_medida u 
					    ON u.cd_indicador_unidade_medida=i.cd_indicador_unidade_medida
					  JOIN indicador.indicador_tabela it 
					    ON it.cd_indicador=i.cd_indicador
					  JOIN indicador.indicador_periodo ip 
					    ON it.cd_indicador_periodo=ip.cd_indicador_periodo
					  LEFT JOIN projetos.processos p
						ON p.cd_processo = it.cd_processo						
					 WHERE it.cd_indicador_tabela = ".intval($args["cd_indicador_tabela"])."
		          ";
		$result = $this->db->query($qr_sql);		
	}
	
	/**
	 *  Retorna um array com informações das tabela 'indicador', 'indicador_tabela', 'indicador_periodo'
	 */
	function listar_indicador_tabela_aberta_de_indicador($cd_indicador)
	{
		$return = Array();

		$query = $this->db->query( "
									SELECT i.ds_indicador as ds_indicador, 
									       i.fl_periodo,
										   CASE WHEN i.fl_periodo = 'N' 
										        THEN ''
 										        ELSE ip.ds_periodo
										   END AS ds_periodo, 
										   ip.nr_ano_referencia, 
										   it.*
									  FROM indicador.indicador as i 
									  JOIN indicador.indicador_tabela as it 
									    ON i.cd_indicador=it.cd_indicador
									  JOIN indicador.indicador_periodo as ip 
									    ON ip.cd_indicador_periodo=it.cd_indicador_periodo
									 WHERE it.dt_exclusao           IS NULL
									   AND it.cd_indicador          = ".intval($cd_indicador)."
									   AND it.dt_fechamento_periodo IS NULL
									 ORDER BY ip.nr_ano_referencia ASC
		                           ");
		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	 exit;
		$return = $query->result_array();
		return $return;
	}
	
	function buscaPeriodo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT p.cd_indicador_periodo, 
						   p.ds_periodo,
						   p.nr_ano_referencia
					  FROM indicador.indicador_periodo p
					 WHERE p.dt_exclusao  IS NULL 
					   AND p.nr_ano_referencia = ".intval($args['nr_ano'])."
					 LIMIT 1
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}	
	
    function apresentacaoListarIndicador(&$result, $args=array())
	{
        $qr_sql = "
					SELECT tab.cd_indicador_tabela, 
						   gru.ds_indicador_grupo, 
						   ind.cd_indicador, 
						   ind.ds_indicador, 
						   CASE WHEN ind.fl_periodo = 'N' 
								THEN ''
								ELSE per.ds_periodo
						   END AS ds_periodo,						   
						   CASE WHEN ind.fl_periodo = 'N' 
								THEN NULL
								ELSE per.nr_ano_referencia
						   END AS nr_ano_referencia,						   
						   p.procedimento AS ds_processo,
                           ind.plugin_nome,
						   ic.ds_indicador_controle
					  FROM indicador.indicador ind
					  JOIN indicador.indicador_grupo gru 
						ON gru.cd_indicador_grupo = ind.cd_indicador_grupo
					  JOIN indicador.indicador_controle ic				
			            ON ic.cd_indicador_controle = ind.cd_indicador_controle	
					  JOIN indicador.indicador_tabela tab 
						ON tab.cd_indicador = ind.cd_indicador
					  JOIN indicador.indicador_periodo per 
						ON per.cd_indicador_periodo = tab.cd_indicador_periodo
					  LEFT JOIN projetos.processos p
					    ON p.cd_processo = tab.cd_processo
					 WHERE ind.dt_exclusao IS NULL
                       AND (CASE WHEN ind.fl_periodo = 'N' 
                                 THEN (SELECT MAX(tab1.cd_indicador_tabela) 
								         FROM indicador.indicador_tabela tab1 
										WHERE tab1.cd_indicador = ind.cd_indicador)
                                 ELSE tab.cd_indicador_tabela
                           END) = tab.cd_indicador_tabela					 
                       AND tab.cd_tipo     = (SELECT i1.cd_tipo
											    FROM indicador.indicador_tabela it1
											    JOIN indicador.indicador i1
												  ON i1.cd_indicador = it1.cd_indicador
											   WHERE it1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					   AND gru.dt_exclusao IS NULL
					   AND tab.dt_exclusao IS NULL
					   AND per.dt_exclusao IS NULL
					   AND ind.cd_indicador_grupo = (SELECT i1.cd_indicador_grupo
					                                   FROM indicador.indicador_tabela it1
													   JOIN indicador.indicador i1
													     ON i1.cd_indicador = it1.cd_indicador
												      WHERE it1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					   AND (per.cd_indicador_periodo = (SELECT ip2.cd_indicador_periodo
														  FROM indicador.indicador_periodo ip2
														  JOIN indicador.indicador_tabela it2
															ON it2.cd_indicador_periodo = ip2.cd_indicador_periodo
														  JOIN indicador.indicador i2
															ON i2.cd_indicador = it2.cd_indicador
														 WHERE i2.cd_indicador_grupo = (SELECT i1.cd_indicador_grupo
																						  FROM indicador.indicador_tabela it1
																						  JOIN indicador.indicador i1
																							ON i1.cd_indicador = it1.cd_indicador
																						 WHERE it1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
														 ORDER BY ip2.nr_ano_referencia DESC
														 LIMIT 1)
					        OR ind.fl_periodo = 'N')
					 ORDER BY ind.nr_ordem,
					          gru.ds_indicador_grupo,
					          ind.ds_indicador,
							  per.ds_periodo,
							  p.procedimento
                   ";

		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}	
	
	function grupoCombo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT g.cd_indicador_grupo AS value, 
						   g.ds_indicador_grupo AS text 
					  FROM indicador.indicador_grupo g 
					  JOIN indicador.indicador i 
						ON i.cd_indicador_grupo = g.cd_indicador_grupo 
					 WHERE g.dt_exclusao IS NULL 
					   AND i.dt_exclusao IS NULL
					 ORDER BY g.ds_indicador_grupo
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}

    function grupoComboTipo( &$result, $args=array() )
    {
		$qr_sql = "
					SELECT g.cd_indicador_grupo AS value,
						   g.ds_indicador_grupo AS text
					  FROM indicador.indicador_grupo g
					 WHERE g.dt_exclusao IS NULL
		
                     --  AND 0 < (SELECT COUNT(cd_indicador) FROM indicador.indicador WHERE cd_tipo = '".$args['cd_tipo']."' AND cd_indicador_grupo = g.cd_indicador_grupo)
					 ORDER BY g.ds_indicador_grupo
		          ";
  
		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}

	function periodoCombo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT p.cd_indicador_periodo AS value, 
						   p.ds_periodo AS text
					  FROM indicador.indicador_periodo p
					 WHERE p.dt_exclusao  IS NULL 
					 ORDER BY p.ds_periodo DESC
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}		
	
	function processoCombo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT p.cd_processo AS value, 
						   p.procedimento AS text
					  FROM projetos.processos p
					 ORDER BY p.procedimento ASC
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>";	exit;
		$result = $this->db->query($qr_sql);
	}	
	
	function controlesCombo( &$result, $args=array() )
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
}
?>