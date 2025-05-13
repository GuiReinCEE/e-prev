<?php
class Atividade_cronograma_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function solicitantes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT a.cod_solicitante AS value,
				   uc.nome AS text
			  FROM projetos.atividade_cronograma_item aci
			  JOIN projetos.atividades a
				ON a.numero = aci.cd_atividade
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = a.cod_solicitante
			 WHERE cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
			   AND aci.dt_exclusao IS NULL
			 ORDER BY uc.nome";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function cronogramaResponsavel(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ac.cd_responsavel
			  FROM projetos.atividade_cronograma ac
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = ac.cd_responsavel
			 WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function cronograma(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ac.cd_atividade_cronograma, 
					       ac.descricao, 
						   CASE WHEN TO_CHAR(ac.dt_inicio, 'DD/MM') = '01/04' AND TO_CHAR(ac.dt_final, 'DD/MM') = '31/07' THEN '1'
								WHEN TO_CHAR(ac.dt_inicio, 'DD/MM') = '01/08' AND TO_CHAR(ac.dt_final, 'DD/MM') = '30/11' THEN '2'
								WHEN TO_CHAR(ac.dt_inicio, 'DD/MM') = '01/12' AND TO_CHAR(ac.dt_final, 'DD/MM') = '31/03' THEN '3'
								ELSE null
							END AS periodo,
						   TO_CHAR(ac.dt_inicio,'DD/MM/YYYY') AS dt_inicio, 
						   TO_CHAR(ac.dt_final,'DD/MM/YYYY') AS dt_final, 
						   ac.cd_responsavel, 
						   uc.divisao AS cd_divisao,
						   uc.nome,
						   TO_CHAR(ac.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao, 
						   ac.cd_usuario_inclusao, 
						   TO_CHAR(ac.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao, 
						   ac.cd_usuario_exclusao,
						   TO_CHAR(ac.dt_encerra,'DD/MM/YYYY HH24:MI') AS dt_encerra
                      FROM projetos.atividade_cronograma ac
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ac.cd_responsavel
					 WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
		          ";

		$result = $this->db->query($qr_sql);
	}	

	function cronogramaListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ac.cd_atividade_cronograma, 
					       ac.descricao, 
						   TO_CHAR(ac.dt_inicio,'DD/MM/YYYY') AS dt_inicio, 
						   TO_CHAR(ac.dt_final,'DD/MM/YYYY') AS dt_final, 
						   TO_CHAR(ac.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao, 
						   TO_CHAR(ac.dt_encerra,'DD/MM/YYYY') AS dt_encerra, 
						   ac.cd_responsavel,
						   uc.nome AS ds_responsavel,
						   (SELECT COUNT(*) 
						      FROM projetos.atividade_cronograma_item aci
							 WHERE aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
							   AND aci.dt_exclusao IS NULL) AS qt_atividade,
						   (SELECT COUNT(*) 
						      FROM projetos.atividade_cronograma_item aci
							 WHERE aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
							   AND aci.dt_exclusao IS NULL
							   AND aci.nr_prioridade_gerente IS NOT NULL) AS qt_atividade_prio,
						   (SELECT COUNT(*) 
						      FROM projetos.atividade_cronograma_item aci2
							  JOIN projetos.atividades a
							    ON a.numero = aci2.cd_atividade
							 WHERE aci2.cd_atividade_cronograma = ac.cd_atividade_cronograma    
							   AND (CAST(a.dt_fim_real AS DATE) BETWEEN ac.dt_inicio AND ac.dt_final
							       OR
								   CAST(a.dt_env_teste AS DATE) BETWEEN ac.dt_inicio AND ac.dt_final)
							   AND aci2.dt_exclusao IS NULL
							   AND (a.dt_fim_real IS NOT NULL
							       OR 
								   a.dt_env_teste IS NOT NULL)
							   AND aci2.nr_prioridade_gerente IS NOT NULL) AS qt_atividade_conc,
						   (SELECT COUNT(*) 
						      FROM projetos.atividades a2
						     WHERE a2.cod_atendente = ac.cd_responsavel
						       AND CAST(a2.dt_fim_real AS DATE) BETWEEN ac.dt_inicio AND ac.dt_final
						       AND a2.numero NOT IN(SELECT aci2.cd_atividade 
							  	 				      FROM projetos.atividade_cronograma_item aci2
												     WHERE aci2.cd_atividade_cronograma = ac.cd_atividade_cronograma
												       AND aci2.dt_exclusao IS NULL
													   AND aci2.nr_prioridade_gerente IS NOT NULL)) AS qt_atividade_conc_fora
                      FROM projetos.atividade_cronograma ac
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = ac.cd_responsavel
					 WHERE ac.dt_exclusao IS NULL
					   AND (
							-- RESPONSAVEL
							ac.cd_responsavel = ".intval($args['cd_usuario'])." 
							
					        OR 
							-- POSSUI UMA ATIVIDADE DA GERENCIA
							0 < (SELECT COUNT(*)
								   FROM projetos.atividade_cronograma_item aci
								   JOIN projetos.atividades a
							    	 ON a.numero = aci.cd_atividade
								  WHERE aci.cd_atividade_cronograma = ac.cd_atividade_cronograma
								    AND aci.dt_exclusao IS NULL
								    AND a.divisao = (SELECT uc.divisao 
													   FROM projetos.usuarios_controledi uc 
													  WHERE uc.codigo = ".intval($args['cd_usuario'])."))
							OR 
							-- GERENTE DA GI
							0 < (SELECT COUNT(*)
								   FROM projetos.usuarios_controledi uc 
								  WHERE uc.codigo = ".intval($args['cd_usuario'])."
									AND uc.tipo = 'G'
									AND (uc.divisao = 'GI' OR uc.divisao_ant = 'GI'))
									
							OR 
							-- TOKEN PARA VER CRONOGRAMAS DA AREA
							0 < (SELECT COUNT(*)
								   FROM projetos.usuarios_controledi uc 
								  WHERE uc.codigo = ".intval($args['cd_usuario'])."
									AND (MD5(UPPER(uc.divisao)) = '".trim($args['token_gerencia'])."') OR MD5(UPPER(uc.divisao_ant)) = '".trim($args['token_gerencia'])."')					
							
							)
		
					   ".(intval($args["cd_analista"]) > 0 ? " AND ac.cd_responsavel = ".intval($args["cd_analista"]) : "")."
					   
					   ".(((trim($args["dt_inclusao_ini"]) != "") and (trim($args["dt_inclusao_fim"]) != "")) ? " AND CAST(ac.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args["dt_inclusao_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_inclusao_fim"]."','DD/MM/YYYY')" : "")."
		          ";	   				  

		#echo "<pre>".$qr_sql."</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function cronograma_salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_atividade_cronograma']) > 0)
		{
			#### UPDATE ####
			$qr_sql = " 
						UPDATE projetos.atividade_cronograma
						   SET dt_inicio      = ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')").", 
							   dt_final       = ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')").", 
							   cd_responsavel = ".(intval($args['cd_responsavel']) == 0 ? "DEFAULT" : $args['cd_responsavel'])."
						 WHERE cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."			
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_atividade_cronograma']);	
		}
		else
		{
			#### INSERT ####
			$new_id = intval($this->db->get_new_id("projetos.atividade_cronograma", "cd_atividade_cronograma"));
			$qr_sql = " 
						INSERT INTO projetos.atividade_cronograma
						     (
							   cd_atividade_cronograma, 
							   dt_inicio, 
							   dt_final, 
							   cd_responsavel,
							   cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')").",
							   ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')").",
							   ".(intval($args['cd_responsavel']) == 0 ? "DEFAULT" : $args['cd_responsavel']).",
							   ".(intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario'])."
							 );
					  ";
			$this->db->query($qr_sql);	
			$retorno = $new_id;			
		}
		
		#echo "<pre>$qr_sql</pre>";
		#exit;
		
		return $retorno;
	}

	function excluir_cronograma(&$result, $args=array())
	{
		if(intval($args['cd_atividade_cronograma']) > 0)
		{
			$qr_sql = " 
				UPDATE projetos.atividade_cronograma
				   SET dt_exclusao         = CURRENT_TIMESTAMP,
					   cd_usuario_exclusao = ".$args['cd_usuario']."
				 WHERE cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}
	
	function cronograma_item(&$result, $args=array())
	{
		$qr_sql = "
					SELECT aci.cd_atividade_cronograma_item, 
					       aci.cd_atividade_cronograma, 
						   aci.cd_atividade,
						   aci.nr_prioridade_operacional,
						   aci.nr_prioridade_gerente,
						   TO_CHAR(aci.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao, 
						   aci.cd_usuario_inclusao, 
						   TO_CHAR(aci.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao, 
						   aci.cd_usuario_exclusao,
						   aci.cd_atividade_cronograma_grupo
                      FROM projetos.atividade_cronograma_item aci
					 WHERE aci.cd_atividade_cronograma_item = ".intval($args['cd_atividade_cronograma_item'])."
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}		
	
	function listar_cronograma_item(&$result, $args=array())
	{
		$qr_sql = "
			SELECT aci.cd_atividade_cronograma_item,
				   aci.cd_atividade_cronograma,
				   aci.cd_atividade,
				   aci.nr_prioridade_operacional,
				   aci.nr_prioridade_gerente,
				   TO_CHAR(a.dt_cad,'DD/MM/YYYY HH24:MI') AS dt_atividade,
				   a.dt_fim_real,
				   a.dt_env_teste,
				   a.descricao,
				   a.divisao,
				   l.descricao AS status_atividade,
				   c.descricao AS ds_complexidade,
				   a.cod_atendente,
				   uca.guerra AS atendente,
				   a.cod_solicitante,
				   ucs.guerra AS solicitante,
				   ds_atividade_cronograma_grupo,
				   aci.cd_atividade_cronograma_grupo,
				   CASE WHEN l.valor = 1 THEN 'blue'
						WHEN l.valor = 2 THEN '#8B7D7B'
						WHEN l.valor = 3 THEN 'red'
						WHEN l.valor = 4 THEN '#FF6A00'
						WHEN l.valor = 5 THEN '#4169E1'
						ELSE 'green'
				   END AS status_cor,					
					CASE WHEN a.status_atual NOT IN ('AGDF','CANC','SUSP','ETES','CONC') THEN 'S'
						ELSE 'N'
					END AS fl_edit,
					pp.nome as projeto_nome,
					a.sistema AS cd_projeto,
					a.complexidade AS cd_complexidade
			  FROM projetos.atividade_cronograma_item aci
			  LEFT JOIN projetos.atividade_cronograma_grupo tcg
				ON tcg.cd_atividade_cronograma_grupo = aci.cd_atividade_cronograma_grupo 
			  JOIN projetos.atividades a
				ON a.numero = aci.cd_atividade
			  LEFT JOIN projetos.projetos pp 
				ON pp.codigo = a.sistema
			  JOIN projetos.usuarios_controledi uca
				ON uca.codigo = a.cod_atendente
			  JOIN projetos.usuarios_controledi ucs
				ON ucs.codigo = a.cod_solicitante						
			  LEFT JOIN listas l 
				ON l.codigo    = a.status_atual 
			   AND l.categoria = 'STAT'
			  LEFT JOIN listas c
				ON c.codigo    = a.complexidade
			   AND c.categoria = 'CPLX' 					   
			 WHERE aci.dt_exclusao             IS NULL
			   AND aci.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
			   ".(trim($args["cd_divisao"]) != "" ? " AND a.divisao = '".trim($args["cd_divisao"])."'" : "")."
			   ".(trim($args["status_atual"]) != "" ? " AND a.status_atual = '".trim($args["status_atual"])."'" : "")."
			   ".(trim($args["cd_atividade_cronograma_grupo"]) != "" ? " AND aci.cd_atividade_cronograma_grupo = ".intval($args["cd_atividade_cronograma_grupo"]) : "")."
			   ".(((trim($args["ini_operacional"]) != "") and (trim($args["fim_operacional"]) != "")) ? " AND aci.nr_prioridade_operacional  BETWEEN ".intval($args["ini_operacional"])." AND ".intval($args["fim_operacional"]) : "")."
			   ".(((trim($args["ini_gerente"]) != "") and (trim($args["fim_gerente"]) != "")) ? " AND aci.nr_prioridade_gerente  BETWEEN ".intval($args["ini_gerente"])." AND ".intval($args["fim_gerente"]) : "")."
			   ".(trim($args["sistema"]) != "" ? " AND a.sistema = ".intval($args["sistema"]) : "")."
			   ".(trim($args["complexidade"]) != "" ? " AND a.complexidade = '".trim($args["complexidade"])."'" : "")."
			   ".(trim($args["cd_solicitante"]) != "" ? " AND a.cod_solicitante = ".intval($args["cd_solicitante"]) : "")."
			   ".(trim($args["fl_prioridade_area"]) == "S" ? " AND aci.nr_prioridade_operacional IS NOT NULL" : "")."
			   ".(trim($args["fl_prioridade_area"]) == "N" ? " AND aci.nr_prioridade_operacional IS NULL" : "")."
			   ".(trim($args["fl_prioridade_consenso"]) == "S" ? " AND aci.nr_prioridade_gerente IS NOT NULL" : "")."
			   ".(trim($args["fl_prioridade_consenso"]) == "N" ? " AND aci.nr_prioridade_gerente IS NULL" : "")."
		  ";
				  		   				  
				  
		#echo "<pre>".$qr_sql."</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function atividade_nao_concluidas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT numero
			  FROM projetos.atividades a
			 WHERE a.cod_atendente = ".intval($args['cd_usuario'])."
			   AND dt_fim_real IS NULL
			   AND status_atual NOT IN ('AGDF','CANC','SUSP','ETES','CONC')
			   AND numero NOT IN (SELECT cd_atividade
			                        FROM projetos.atividade_cronograma_item
								   WHERE cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
								     AND dt_exclusao IS NULL)
			 ORDER BY numero ";

		$result = $this->db->query($qr_sql);
	}
	
	function salvar_item(&$result, $args=array())
	{		
		if((intval($args['cd_atividade_cronograma_item']) == 0) and (intval($args['cd_atividade_cronograma']) > 0) and (intval($args['cd_atividade']) > 0))
		{
			$qr_sql = " 
				INSERT INTO projetos.atividade_cronograma_item
					 (
					   cd_atividade_cronograma,
					   cd_atividade, 
					   cd_usuario_inclusao,
					   cd_atividade_cronograma_grupo
					 )
				VALUES 
					 (
					   ".(intval($args['cd_atividade_cronograma']) == 0 ? "DEFAULT" : $args['cd_atividade_cronograma']).",
					   ".(intval($args['cd_atividade']) == 0 ? "DEFAULT" : $args['cd_atividade']).",
					   ".(intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']).",
					   ".(intval($args['cd_atividade_cronograma_grupo']) == 0 ? "DEFAULT" : $args['cd_atividade_cronograma_grupo'])."
					 );
			  ";
			$this->db->query($qr_sql);			
		}
	}
	
	function excluir_item(&$result, $args=array())
	{
		if(intval($args['cd_atividade_cronograma_item']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.atividade_cronograma_item
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE cd_atividade_cronograma_item = ".intval($args['cd_atividade_cronograma_item'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
	
	function cronograma_grupo(&$result, $args=array())
	{
		$qr_sql = " 
			SELECT cd_atividade_cronograma_grupo AS value,
			       ds_atividade_cronograma_grupo AS text
		      FROM projetos.atividade_cronograma_grupo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_atividade_cronograma_grupo;";			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = " 
			INSERT INTO projetos.atividade_cronograma_acompanhamento
			     (
					descricao,
					cd_atividade_cronograma,
					cd_usuario_inclusao
				 )
		    VALUES
				 (
					'".trim($args['descricao'])."',
					".intval($args['cd_atividade_cronograma']).",
					".intval($args['cd_usuario'])."
				 )";			
		$result = $this->db->query($qr_sql);
	}
	
	function lista_acompanhamento(&$result, $args=array())
	{
		$qr_sql = " 
			SELECT a.descricao,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.atividade_cronograma_acompanhamento a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.dt_exclusao IS NULL
			   AND a.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
			 ORDER BY a.dt_inclusao DESC;";			
		$result = $this->db->query($qr_sql);
	}
	
	function encerrar_cronograma(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE projetos.atividade_cronograma
			   SET cd_usuario_encerra = ".intval($args["cd_usuario"]).",
			       dt_encerra = CURRENT_TIMESTAMP
			 WHERE cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma']);			
			 

		$result = $this->db->query($qr_sql);
	}
	
	function encerrado(&$result, $args=array())
	{
		$qr_sql = " 
			SELECT dt_encerra
			  FROM projetos.atividade_cronograma
			 WHERE	cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma']);		
			
		$result = $this->db->query($qr_sql);
	}
	
	function salva_operacional(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atividade_cronograma_item
			   SET nr_prioridade_operacional = ".(intval($args['nr_prioridade_operacional']) == 0 ? "NULL" : $args['nr_prioridade_operacional'])."
			 WHERE cd_atividade_cronograma_item = ".intval($args['cd_atividade_cronograma_item']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function salva_gerente(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atividade_cronograma_item
			   SET nr_prioridade_gerente = ".(intval($args['nr_prioridade_gerente']) == 0 ? "NULL" : $args['nr_prioridade_gerente'])."
			 WHERE cd_atividade_cronograma_item = ".intval($args['cd_atividade_cronograma_item']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function projetos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text 
			  FROM projetos.projetos 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY nome;";
		$result = $this->db->query($qr_sql);
	}
	
	function complexidade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
				   descricao AS text 
			  FROM listas 
			 WHERE categoria = 'CPLX'
			 ORDER BY codigo;";
		$result = $this->db->query($qr_sql);
	}
	
	function salva_projeto(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atividades
			   SET sistema = ".intval($args['sistema'])."
			 WHERE numero = ".intval($args['cd_atividade']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function salva_complexidade(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atividades
			   SET complexidade = '".$args['cd_complexidade']."'
			 WHERE numero = ".intval($args['cd_atividade']).";";

		$this->db->query($qr_sql);
	}
	
	function status(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
				   descricao AS text
			  FROM listas 
			 WHERE categoria = 'STAT'
			   AND divisao = 'GI'
			 ORDER BY descricao";
		$result = $this->db->query($qr_sql);
	}
	
	function salva_grupo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.atividade_cronograma_item
			   SET cd_atividade_cronograma_grupo = ".(intval($args['cd_grupo']) == 0 ? "NULL" : $args['cd_grupo'])."
			 WHERE cd_atividade_cronograma_item = ".intval($args['cd_atividade_cronograma_item']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function verifica_atividade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
			  FROM projetos.atividade_cronograma_item
			 WHERE dt_exclusao IS NULL
			   AND cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
			   AND cd_atividade            = ".intval($args['cd_atividade'])."";
			
		$result = $this->db->query($qr_sql);
	}
	
	function gerencias(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   nome AS text
			  FROM projetos.divisoes
			 WHERE tipo = 'DIV'
			 ORDER BY nome";
		
		$result = $this->db->query($qr_sql);
	}
	
	function analistas(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT ac.cd_responsavel AS value,
						   uc.nome AS text
					  FROM projetos.atividade_cronograma ac
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = ac.cd_responsavel
					 ORDER BY uc.nome		
				  ";
		
		$result = $this->db->query($qr_sql);
	}	
	
	function lista_concluidas_fora(&$result, $args=array())
	{
		$qr_sql = "
			 SELECT a.numero, 
					TO_CHAR(a.dt_cad,'DD/MM/YYYY HH24:MI') AS dt_atividade,
					TO_CHAR(a.dt_fim_real,'DD/MM/YYYY HH24:MI') AS dt_conclusao,
					uc1.guerra AS atendente,
					uc2.guerra AS solicitante,
					a.divisao,
					a.descricao,
					CASE WHEN l.valor = 1 THEN 'blue'
					     WHEN l.valor = 2 THEN '#8B7D7B'
					     WHEN l.valor = 3 THEN 'red'
					     ELSE 'green'
				    END AS status_cor,
					l.descricao AS status_atividade,
					c.descricao AS ds_complexidade,
					pp.nome as projeto_nome
			   FROM projetos.atividades a
			   JOIN projetos.usuarios_controledi uc1
				 ON uc1.codigo = a.cod_atendente
			   JOIN projetos.usuarios_controledi uc2
				 ON uc2.codigo = a.cod_solicitante	
			   LEFT JOIN listas l 
				 ON l.codigo    = a.status_atual 
				AND l.categoria = 'STAT'
			   LEFT JOIN listas c
				 ON c.codigo    = a.complexidade
				AND c.categoria = 'CPLX' 
			   LEFT JOIN projetos.projetos pp 
				 ON pp.codigo = a.sistema
			  WHERE a.cod_atendente = (SELECT ac.cd_usuario_inclusao
										 FROM projetos.atividade_cronograma ac
										WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma']).") 
				AND CAST(a.dt_fim_real AS DATE) BETWEEN (SELECT ac.dt_inicio
														   FROM projetos.atividade_cronograma ac
														  WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma']).") AND (SELECT ac.dt_final
																										                                          FROM projetos.atividade_cronograma ac
																										                                         WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma']).") 
				AND a.numero NOT IN(SELECT aci2.cd_atividade 
						              FROM projetos.atividade_cronograma_item aci2
									 WHERE aci2.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
								  	   AND aci2.dt_exclusao IS NULL
									   AND aci2.nr_prioridade_gerente IS NOT NULL)
				".(trim($args['cd_gerencia']) != '' ? "AND a.divisao = '".trim($args['cd_gerencia'])."'" : '').";";
	
		$result = $this->db->query($qr_sql);
	}
	
	function quadro_resumo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT r.divisao AS cd_gerencia,
				   SUM(r.qt_Atividade) AS qt_atividade,
				   SUM(r.qt_atividade_prio) AS qt_atividade_prio,
				   SUM(r.qt_atividade_conc) AS qt_atividade_conc,
				   SUM(r.qt_atividade_conc_fora) AS qt_atividade_conc_fora
			  FROM (SELECT a.divisao,
						   COUNT(*) AS qt_atividade,
						   0 AS qt_atividade_prio,
						   0 AS qt_atividade_conc,
						   0 AS qt_atividade_conc_fora
					  FROM projetos.atividade_cronograma_item qt_aci
				      JOIN projetos.atividades a
						ON a.numero = qt_aci.cd_atividade
				     WHERE qt_aci.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
				       AND qt_aci.dt_exclusao IS NULL
				     GROUP BY a.divisao

					 UNION 

					SELECT a.divisao AS cd_gerencia,
						   0 AS qt_atividade,
						   COUNT(*) AS qt_atividade_prio,
						   0 AS qt_atividade_conc,
						   0 AS qt_atividade_conc_fora
				      FROM projetos.atividade_cronograma_item aci
					  JOIN projetos.atividades a
						ON a.numero = aci.cd_atividade
				     WHERE aci.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
				       AND aci.dt_exclusao IS NULL
				       AND aci.nr_prioridade_gerente IS NOT NULL
					 GROUP BY a.divisao

					 UNION

					SELECT a.divisao AS cd_gerencia,
						   0 AS qt_atividade,
						   0 AS qt_atividade_prio,
						   COUNT(*) AS qt_atividade_conc,
						   0 AS qt_atividade_conc_fora
					  FROM projetos.atividade_cronograma_item aci2
					  JOIN projetos.atividades a
					    ON a.numero = aci2.cd_atividade
					  JOIN projetos.atividade_cronograma ac  
					    ON aci2.cd_atividade_cronograma = ac.cd_atividade_cronograma    
					 WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
				       AND (CAST(a.dt_fim_real AS DATE) BETWEEN ac.dt_inicio AND ac.dt_final
					       OR
					       CAST(a.dt_env_teste AS DATE) BETWEEN ac.dt_inicio AND ac.dt_final)
				       AND aci2.dt_exclusao IS NULL
				       AND (a.dt_fim_real IS NOT NULL
					       OR 
					       a.dt_env_teste IS NOT NULL)
				       AND aci2.nr_prioridade_gerente IS NOT NULL
					 GROUP BY a.divisao

					 UNION

				    SELECT a2.divisao AS cd_gerencia,
						   0 AS qt_atividade,
						   0 AS qt_atividade_prio,
						   0 AS qt_atividade_conc,
						   COUNT(*) AS  qt_atividade_conc_fora
				      FROM projetos.atividades a2
				     WHERE a2.cod_atendente = (SELECT ac.cd_responsavel 
											     FROM projetos.atividade_cronograma ac  
										        WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])." )
				       AND CAST(a2.dt_fim_real AS DATE) BETWEEN (SELECT ac.dt_inicio 
															       FROM projetos.atividade_cronograma ac  
															      WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])." )
													        AND (SELECT ac.dt_final 
															  	   FROM projetos.atividade_cronograma ac  
																  WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])." )
				       AND a2.numero NOT IN(SELECT aci2.cd_atividade 
				      FROM projetos.atividade_cronograma_item aci2
				      JOIN projetos.atividade_cronograma ac  
				        ON aci2.cd_atividade_cronograma = ac.cd_atividade_cronograma    
					 WHERE ac.cd_atividade_cronograma = ".intval($args['cd_atividade_cronograma'])."
				       AND aci2.dt_exclusao IS NULL
				       AND aci2.nr_prioridade_gerente IS NOT NULL)
					 GROUP BY a2.divisao
			) r
			  GROUP BY r.divisao
			  ORDER BY r.divisao ASC";
		
		$result = $this->db->query($qr_sql);
	}
	
}
?>