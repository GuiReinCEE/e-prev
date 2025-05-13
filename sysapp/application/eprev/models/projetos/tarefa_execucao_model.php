<?php
class Tarefa_execucao_model extends Model
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
				   (SELECT COUNT(*)
				      FROM projetos.tarefa_anexo pta
					 WHERE pta.cd_tarefa = t.codigo
					   AND pta.dt_exclusao IS NULL) AS tl_anexos,
				   (SELECT th.status_atual 
	                  FROM projetos.tarefa_historico th
					 WHERE th.cd_atividade = t.cd_atividade
		               AND th.cd_tarefa    = t.cd_tarefa
					   AND th.dt_inclusao = (SELECT MAX(th2.dt_inclusao)
	                                           FROM projetos.tarefa_historico th2
					                          WHERE th2.cd_atividade = t.cd_atividade
		                                        AND th2.cd_tarefa    = t.cd_tarefa
					                            AND th2.cd_recurso   = t.cd_recurso)
					 ORDER BY dt_inclusao DESC
					 LIMIT 1)  AS fl_status_old,
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
	
	function classificacao_tarefa(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
                   descricao AS text   
              FROM listas  
             WHERE categoria = 'TTAR'
             ORDER BY descricao";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.tarefas 
               SET observacoes 	    = ".(trim($args['observacoes']) != '' ? str_escape($args['observacoes']) : "DEFAULT").",
                   cd_classificacao = ".(trim($args['cd_classificacao']) != '' ? "'".trim($args['cd_classificacao'])."'" : "DEFAULT")."
             WHERE cd_atividade	 = ".intval($args['cd_atividade'])." 
               AND cd_tarefa     = ".intval($args['cd_tarefa']).";";

		$result = $this->db->query($qr_sql);
	}
	
	function play(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.tarefas 
			   SET dt_inicio_prog = CURRENT_TIMESTAMP, 
			       status_atual   = 'EMAN' 
			 WHERE cd_tarefa      = ".intval($args['cd_tarefa'])." 
			   AND cd_atividade   = ".intval($args['cd_atividade']).";
			   
			UPDATE projetos.atividades 
			   SET status_atual = 'EMAN' 
			 WHERE numero = ".intval($args['cd_atividade']).";
			 
			INSERT INTO projetos.tarefa_historico 
				   ( 
					  cd_tarefa,  	
					  cd_atividade, 	
					  cd_recurso,   	
					  timestamp_alteracao,   	
					  descricao,  				
					  status_atual,
					  cd_usuario_inclusao
				   ) 
			VALUES
				   ( 
					  ".intval($args['cd_tarefa'])." ,
					  ".intval($args['cd_atividade']).",
					  ".intval($args['cd_recurso']).",
					  CURRENT_TIMESTAMP, 
					  'Tarefa Iniciada.', 
					  'EMAN',
					  ".intval($args['cd_recurso'])."
				   );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function pause(&$result, $args=array())
	{

		$qr_sql = "
			UPDATE projetos.tarefas 
			   SET status_atual   = 'SUSP' 
			 WHERE cd_tarefa      = ".intval($args['cd_tarefa'])." 
			   AND cd_atividade   = ".intval($args['cd_atividade']).";
			 
			INSERT INTO projetos.tarefa_historico 
				   ( 
					  cd_tarefa,  	
					  cd_atividade, 	
					  cd_recurso,   	
					  timestamp_alteracao,   	
					  descricao,  				
					  status_atual,
					  ds_obs,
					  cd_usuario_inclusao
				   ) 
			VALUES
				   ( 
					  ".intval($args['cd_tarefa'])." ,
					  ".intval($args['cd_atividade']).",
					  ".intval($args['cd_recurso']).",
					  CURRENT_TIMESTAMP, 
					  'Tarefa Pausada.', 
					  'SUSP',
					  ".str_escape($args['ds_obs']).",
					  ".intval($args['cd_recurso'])."
				   );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function stop(&$result, $args=array())
	{

		$qr_sql = "
			UPDATE projetos.tarefas 
			   SET dt_fim_prog    = CURRENT_TIMESTAMP, 
			       status_atual   = 'LIBE' 
			 WHERE cd_tarefa      = ".intval($args['cd_tarefa'])." 
			   AND cd_atividade   = ".intval($args['cd_atividade']).";
			 
			INSERT INTO projetos.tarefa_historico 
				   ( 
					  cd_tarefa,  	
					  cd_atividade, 	
					  cd_recurso,   	
					  timestamp_alteracao,   	
					  descricao,  				
					  status_atual,
					  ds_obs,
					  cd_usuario_inclusao
				   ) 
			VALUES
				   ( 
					  ".intval($args['cd_tarefa'])." ,
					  ".intval($args['cd_atividade']).",
					  ".intval($args['cd_recurso']).",
					  CURRENT_TIMESTAMP, 
					  'Tarefa Liberada.', 
					  'LIBE',
					  ".str_escape($args['ds_obs']).",
					  ".intval($args['cd_recurso'])."
				   );";
		
		$result = $this->db->query($qr_sql);
	}
}
?>