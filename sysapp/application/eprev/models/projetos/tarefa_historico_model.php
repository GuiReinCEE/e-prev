<?php
class Tarefa_historico_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
		
	function listar(&$result, $args=array())
	{
		$qr_sql = " 
			SELECT h.cd_atividade,  
		  	       h.cd_tarefa,  
		  	       h.cd_recurso,  
		  	       TO_CHAR(h.dt_inclusao,'DD/MM/YYYY - HH24:MI:SS') AS data,  
		  	       h.descricao,     
		  	       u.nome AS responsavel,     
		  	       CASE WHEN (h.status_atual='AMAN') THEN 'Aguardando Manutenção'  
		  	            WHEN (h.status_atual='EMAN') THEN 'Em Manutenção'  
		  	            WHEN (h.status_atual='LIBE') THEN 'Liberada'  
		  	            WHEN (h.status_atual='CONC') THEN 'Concluída'  
		 		        WHEN (h.status_atual='CANC') THEN 'Cancelada'
						WHEN (h.status_atual='AGDF') THEN 'Aguardando Definição'
						WHEN (h.status_atual='SUSP' AND (SELECT status_atual 
						                                   FROM projetos.atividades 
														  WHERE numero = h.cd_atividade) = 'SUSP') THEN 'Atividade Suspensa'
						WHEN (h.status_atual='SUSP') THEN 'Em Manutenção (Pausa)' 	
		            END AS status_atual,
				   CASE WHEN l.valor = 1 THEN 'blue'		
						WHEN l.valor = 2 THEN '#8B7D7B'
						WHEN l.valor = 3 THEN 'red'
						ELSE 'green'
					END AS status_cor,	
	               h.ds_obs AS motivo,
				   t.observacoes,
				   t.resumo
		      FROM projetos.tarefa_historico h 
			  LEFT OUTER JOIN listas l
				ON l.codigo = h.status_atual
			  JOIN projetos.tarefas t
			    ON t.cd_atividade = h.cd_atividade
			   AND t.cd_tarefa = h.cd_tarefa
		 	  JOIN projetos.usuarios_controledi u	  
		        ON h.cd_usuario_inclusao = u.codigo 
			 WHERE h.cd_tarefa 	  = ".intval($args['cd_tarefa'])."
			   AND h.cd_atividade = ".intval($args['cd_atividade'])."
		     ORDER BY timestamp_alteracao";
		
		$result = $this->db->query($qr_sql);
	}
	
	function tarefa(&$result, $args=array())
	{
		$qr_sql = "
			SELECT LOWER(t.fl_tarefa_tipo) AS fl_tarefa_tipo,
			       t.fl_checklist,
				   t.cd_atividade,
				   t.cd_tarefa,
				   t.codigo,
				   t.dt_fim_prog,
				   ct.nome_tarefa,
				   uc.nome AS analista,
				   uc2.nome AS programador,
				   CASE WHEN (t.status_atual='AMAN') THEN 'Aguardando Manutenção'  
		  	            WHEN (t.status_atual='EMAN') THEN 'Em Manutenção'  
		  	            WHEN (t.status_atual='LIBE') THEN 'Liberada'  
		  	            WHEN (t.status_atual='CONC') THEN 'Concluída'  
		 		        WHEN (t.status_atual='CANC') THEN 'Cancelada'
						WHEN (t.status_atual='AGDF') THEN 'Aguardando Definição'
						WHEN (t.status_atual='SUSP' AND (SELECT status_atual 
						                                   FROM projetos.atividades 
														  WHERE numero = t.cd_atividade) = 'SUSP') THEN 'Atividade Suspensa'
						WHEN (t.status_atual='SUSP') THEN 'Em Manutenção (Pausa)' 	
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
}