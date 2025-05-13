<?php
class Tarefa_checklist_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function tarefa(&$result, $args=array())
	{
		$qr_sql = "
			SELECT LOWER(t.fl_tarefa_tipo) AS fl_tarefa_tipo,
			       t.fl_checklist,
				   t.cd_atividade,
				   t.cd_tarefa,
				   t.cd_recurso,
				   t.programa,
				   TO_CHAR(t.dt_inicio_prev,'DD/MM/YYYY') AS dt_inicio_prev,
				   TO_CHAR(t.dt_fim_prev,'DD/MM/YYYY') AS dt_fim_prev,
				   TO_CHAR(t.dt_hr_inicio,'DD/MM/YYYY HH24:MI:SS') AS dt_hora_inicio,
				   TO_CHAR(t.dt_hr_fim,'DD/MM/YYYY HH24:MI:SS') AS dt_hora_fim,
				   TO_CHAR(t.hr_inicio,'HH24:MI:SS') AS hr_inicio,
				   TO_CHAR(t.dt_fim,'DD/MM/YYYY') AS dt_fim,
				   TO_CHAR(t.hr_fim,'HH24:MI:SS') AS hr_fim,	
				   TO_CHAR(t.dt_inicio_prog,'DD/MM/YYYY HH24:MI:SS') as dt_inicio_prog,
				   TO_CHAR(t.dt_fim_prog,'DD/MM/YYYY HH24:MI:SS') as dt_fim_prog,
				   TO_CHAR(t.dt_ok_anal,'DD/MM/YYYY HH24:MI:SS') as dt_ok_anal,
				   t.duracao,
				   t.descricao,
				   t.observacoes,
				   t.casos_testes,
				   t.tabs_envolv,
				   t.imagem,
				   t.cd_mandante,
				   t.cd_tipo_tarefa,
				   t.cd_classificacao,
				   ct.nome_tarefa,
				   uc.nome AS analista,
				   uc2.nome AS programador,
				   t.codigo AS codigo_tarefa,
				   t.fl_tarefa_tipo,
				   t.status_atual AS fl_status,
				   (SELECT th.status_atual 
	                  FROM projetos.tarefa_historico th
					 WHERE th.cd_atividade = t.cd_atividade
		               AND th.cd_tarefa    = t.cd_tarefa
					   AND th.timestamp_alteracao = (SELECT MAX(th2.timestamp_alteracao)
	                                                   FROM projetos.tarefa_historico th2
					                                  WHERE th2.cd_atividade = t.cd_atividade
		                                                AND th2.cd_tarefa    = t.cd_tarefa
					                                    AND th2.cd_recurso   = t.cd_recurso))  AS fl_status_old,
				   CASE WHEN UPPER(t.fl_tarefa_tipo) IN ('R','F','A') THEN 2
                        ELSE 1
                   END AS tipo,
				   CASE WHEN COALESCE((SELECT COUNT(*)
						                  FROM projetos.tarefa_checklist ptc
										  JOIN projetos.tarefa_checklist_pergunta ptcp
											ON ptc.cd_tarefa_checklist=ptcp.cd_tarefa_checklist
										 WHERE ptcp.fl_ativo = 'S'
										   AND ptc.fl_ativo = 'S'
										   AND ptc.cd_tarefa_checklist_tipo = (CASE WHEN UPPER(t.fl_tarefa_tipo) IN ('R','F','A') THEN 2 ELSE 1 END)),0)
						 
											 -
									
							  COALESCE((SELECT COUNT(*)
										  FROM projetos.tarefa_checklist_resposta tcr
						                 WHERE tcr.cd_tarefas = t.codigo
						                   AND tcr.fl_resposta IS NOT NULL),0) <> 0 
						 THEN 'N' 
						 ELSE 'S' 
				   END AS fl_resposta_checklist,
			       CASE WHEN (t.status_atual='AMAN') THEN 'Aguardando Manutenзгo'  
		  	            WHEN (t.status_atual='EMAN') THEN 'Em Manutenзгo'  
		  	            WHEN (t.status_atual='LIBE') THEN 'Liberada'  
		  	            WHEN (t.status_atual='CONC') THEN 'Concluнda'  
		 		        WHEN (t.status_atual='CANC') THEN 'Cancelada'
						WHEN (t.status_atual='AGDF') THEN 'Aguardando Definiзгo'
						WHEN (t.status_atual='SUSP' AND (SELECT status_atual 
						                                   FROM projetos.atividades 
														  WHERE numero = t.cd_atividade) = 'SUSP') THEN 'Atividade Suspensa'
						WHEN (t.status_atual='SUSP') THEN 'Em Manutenзгo (Pausa)' 	
		            END AS status_atual,
				   CASE WHEN l.valor = 1 THEN 'blue'		
						WHEN l.valor = 2 THEN '#8B7D7B'
						WHEN l.valor = 3 THEN 'red'
						ELSE 'green'
					END AS status_cor
			  FROM projetos.tarefas t
			  LEFT OUTER JOIN listas l
				ON l.codigo = t.status_atual
			  LEFT JOIN projetos.cad_tarefas ct
			    ON t.cd_tipo_tarefa = ct.cd_tarefa
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = t.cd_mandante
			  JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = t.cd_recurso
			 WHERE t.cd_tarefa 	  = ".intval($args['cd_tarefa'])."
			   AND t.cd_atividade = ".intval($args['cd_atividade']).";";

		$result = $this->db->query($qr_sql);
	}
	
	function listar_grupos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_tarefa_checklist_grupo,
			       ds_grupo
			  FROM projetos.tarefa_checklist_grupo
			 WHERE cd_tarefa_checklist_tipo = ".$args['tipo']."
			 ORDER BY nr_ordem";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_perguntas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ptc.cd_tarefa_checklist,
				   ptcp.cd_tarefa_checklist_pergunta,
				   ptcp.ds_pergunta,
				   tcr.fl_resposta,
				   tcr.fl_especialista
			  FROM projetos.tarefa_checklist ptc
			  JOIN projetos.tarefa_checklist_pergunta ptcp
				ON ptc.cd_tarefa_checklist=ptcp.cd_tarefa_checklist
			  LEFT JOIN projetos.tarefa_checklist_resposta tcr
                ON tcr.cd_tarefa_checklist_pergunta = ptcp.cd_tarefa_checklist_pergunta
               AND tcr.cd_tarefas = ".intval($args['cd_tarefa'])."
			 WHERE ptcp.fl_ativo = 'S'
			   AND ptc.fl_ativo = 'S'
			   AND ptc.cd_tarefa_checklist_tipo = ".intval($args['tipo'])."
			   AND (ptcp.cd_tarefa_checklist_grupo = ".intval($args['cd_tarefa_checklist_grupo'])." OR 0 = ".intval($args['cd_tarefa_checklist_grupo'])." )
			 ORDER BY ptcp.nr_ordem;";
				
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			DELETE FROM projetos.tarefa_checklist_resposta
			 WHERE cd_tarefas = ".intval($args['codigo_tarefa'])."
			   AND cd_tarefa_checklist_pergunta = ".intval($args['cd_tarefa_checklist_pergunta']).";
			
			INSERT INTO projetos.tarefa_checklist_resposta
			     (
	               fl_resposta, 
				   fl_especialista,
	               cd_tarefa_checklist_pergunta,
	               cd_tarefas
                 )
   			VALUES
   			     (
				   ".str_escape($args['fl_resposta']).",
	   			   ".str_escape($args['fl_especialista']).",
	   			   ".intval($args['cd_tarefa_checklist_pergunta']).",
	   			   ".intval($args['codigo_tarefa'])."
   			     );
			";
			
		$this->db->query($qr_sql);
	}
}
?>