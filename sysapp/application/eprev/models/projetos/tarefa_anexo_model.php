<?php
class Tarefa_anexo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_tarefa_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.tarefa_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.cd_tarefa = ". $args['codigo']."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.tarefa_anexo
			     (
					cd_tarefa,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['codigo']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.tarefa_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_tarefa_anexo = ".intval($args['cd_tarefa_anexo']).";";
		$this->db->query($qr_sql);
	}
	
	function permissao_excluir(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_mandante,
				   cd_recurso,
				   dt_fim_prog
			  FROM projetos.tarefas
			 WHERE codigo = ".intval($args['codigo']).";";

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
}

?>