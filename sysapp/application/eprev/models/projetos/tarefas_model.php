<?php
class Tarefas_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT t.codigo AS codigo, 
				   t.cd_atividade, 
				   t.cd_tarefa, 
				   t.descricao, 
				   t.casos_testes, 
				   t.cd_recurso,
				   ua.guerra AS guerra_usuario_atendente,
				   t.cd_mandante,
				   us.guerra AS guerra_usuario_solicitante,
				   TO_CHAR(dt_inicio_prev, 'DD/MM/YYYY') AS dt_inicio_prev, 
				   TO_CHAR(dt_fim_prev, 'DD/MM/YYYY') AS dt_fim_prev, 
				   TO_CHAR(dt_inicio_prog, 'DD/MM/YYYY') AS dt_inicio_prog,
				   TO_CHAR(dt_fim_prog, 'DD/MM/YYYY') AS dt_fim_prog,
				   TO_CHAR(dt_ok_anal, 'DD/MM/YYYY') AS dt_ok_anal,
				   TO_CHAR(t.dt_encaminhamento, 'DD/MM/YYYY') AS dt_encaminhamento, 
				   t.prioridade,
				   t.status_atual, 
				   l.descricao as status_descricao, 
				   t.resumo, 
				   t.fl_tarefa_tipo,
				   t.nr_nivel_prioridade,
				   CASE WHEN l.valor = 1 THEN 'blue'		
						WHEN l.valor = 2 THEN '#8B7D7B'
						WHEN l.valor = 3 THEN 'red'
						ELSE 'green'
					END AS status_cor,
					CASE WHEN l.valor = 1 THEN 'label label-info'
					     WHEN l.valor = 2 THEN 'label'
						 WHEN l.valor = 3 THEN 'label label-important'
						 WHEN l.valor = 4 THEN 'label label-warning'
						 WHEN l.valor = 5 THEN 'label label-info'
						 ELSE 'label label-success'
					END AS status_label,		
				   (SELECT COUNT(*)
				      FROM projetos.tarefa_anexo pta
					 WHERE pta.cd_tarefa = t.codigo
					   AND pta.dt_exclusao IS NULL) AS tl_anexos
			  FROM projetos.tarefas t
			  JOIN projetos.usuarios_controledi ua
				ON ua.codigo = t.cd_recurso
			  JOIN projetos.usuarios_controledi us
				ON us.codigo = t.cd_mandante
			  JOIN public.listas l
				ON l.codigo = t.status_atual
			 WHERE t.dt_exclusao IS NULL 
			   AND t.status_atual IN ('" . implode("','", $args['status_atual']) ."') 
			   AND ((t.cd_recurso = ".intval(usuario_id())." 
			         AND t.dt_encaminhamento IS NOT NULL) 
					 OR t.cd_mandante = ".intval(usuario_id())." ) 
			   AND ( t.cd_mandante = ".intval($args['cd_mandante'])." 
			         OR 0 = ".intval($args['cd_mandante'])." )
			   AND ( t.cd_recurso = ".intval($args['cd_recurso'])." 
			         OR 0 = ".intval($args['cd_recurso'])." )
			   AND ( COALESCE(t.prioridade,'N') = '".trim($args['prioridade'])."' 
			         OR '' = '".trim($args['prioridade'])."' ) 
			   AND ( t.cd_atividade = ".intval($args['cd_atividade'])."
  				     OR 0 = ".intval($args['cd_atividade'])." )
			   AND ( t.cd_tarefa = ".intval($args['cd_tarefa'])." 
				     OR 0 = ".intval($args['cd_tarefa'])." )
			   ".(trim($args['dt_encaminhamento_inicio']) !=''  ? "AND DATE_TRUNC('day',t.dt_encaminhamento) BETWEEN TO_DATE('".$args['dt_encaminhamento_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['dt_ok_anal_inicio']) !=''  ? "AND DATE_TRUNC('day',t.dt_ok_anal) BETWEEN TO_DATE('".$args['dt_ok_anal_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ok_anal_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY t.cd_atividade DESC
		";

		$result = $this->db->query($qr_sql);
	}
	
	function listar_solicitante(&$result, $args=array())
	{
		$qr_sql = " 
			SELECT DISTINCT u.codigo AS value, 
			       nome AS text 
			  FROM projetos.usuarios_controledi u 
			  JOIN projetos.tarefas t
  			    ON u.codigo = t.cd_mandante
			 WHERE NOT u.tipo IN ('X')
			 ORDER BY nome ASC;";
		
		$result = $this->db->query($qr_sql);
	}

	function listar_atendente(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT u.codigo AS value, 
			       nome AS text 
			  FROM projetos.usuarios_controledi u 
			  JOIN projetos.tarefas t 
			    ON u.codigo = t.cd_recurso
			 WHERE NOT u.tipo IN ('X')
			 ORDER BY nome ASC;";
		
		$result = $this->db->query($qr_sql);
	}
	
	function controle_listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT t.codigo AS codigo, 
				   t.cd_atividade, 
				   t.cd_tarefa, 
				   t.descricao, 
				   t.casos_testes, 
				   t.cd_recurso,
				   ua.guerra AS guerra_usuario_atendente,
				   t.cd_mandante,
				   us.guerra AS guerra_usuario_solicitante,
				   TO_CHAR(t.dt_inicio_prev, 'DD/MM/YYYY') AS dt_inicio_prev, 
				   TO_CHAR(t.dt_fim_prev, 'DD/MM/YYYY') AS dt_fim_prev, 
				   TO_CHAR(t.dt_inicio_prog, 'DD/MM/YYYY') AS dt_inicio_prog,
				   TO_CHAR(t.dt_fim_prog, 'DD/MM/YYYY') AS dt_fim_prog,
				   TO_CHAR(t.dt_ok_anal, 'DD/MM/YYYY') AS dt_ok_anal,
				   TO_CHAR(t.dt_encaminhamento, 'DD/MM/YYYY') AS dt_encaminhamento, 
				   t.prioridade,
				   t.status_atual, 
				   l.descricao as status_descricao, 
				   t.resumo, 
				   t.fl_tarefa_tipo,
				   t.nr_nivel_prioridade,
				   CASE WHEN l.valor = 1 THEN 'blue'		
						WHEN l.valor = 2 THEN '#8B7D7B'
						WHEN l.valor = 3 THEN 'red'
						ELSE 'green'
					END AS status_cor
			  FROM projetos.tarefas t
			  JOIN projetos.atividades a
				ON a.numero = t.cd_atividade
			   AND a.area = 'GI'					
			  JOIN projetos.usuarios_controledi ua
				ON ua.codigo = t.cd_recurso
			  JOIN projetos.usuarios_controledi us
				ON us.codigo = t.cd_mandante
			  JOIN public.listas l
				ON l.codigo = t.status_atual
	         WHERE t.dt_exclusao IS NULL 
			   AND (t.status_atual IN ('" . implode("','", $args['status_atual']) ."') )
			   AND ( t.cd_mandante = ".intval($args['cd_mandante'])." 
			         OR 0 =  ".intval($args['cd_mandante'])." )
			   AND ( t.cd_recurso = ".intval($args['cd_recurso'])." 
			         OR 0 = ".intval($args['cd_recurso'])." )
			   AND ( COALESCE(t.prioridade,'N') = '".trim($args['prioridade'])."'
			         OR '' = '".trim($args['prioridade'])."' ) 
			   ".(trim($args['dt_encaminhamento_inicio']) !=''  ? "AND DATE_TRUNC('day',t.dt_encaminhamento) BETWEEN TO_DATE('".$args['dt_encaminhamento_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['dt_ok_anal_inicio']) !=''  ? "AND DATE_TRUNC('day',t.dt_ok_anal) BETWEEN TO_DATE('".$args['dt_ok_anal_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ok_anal_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY t.cd_atividade DESC;";

		$result = $this->db->query($qr_sql);
	}	
	
	function tarefa( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT LOWER(t.fl_tarefa_tipo) AS fl_tarefa_tipo,
				   t.cd_atividade,
				   t.cd_tarefa,
				   t.programa,
				   t.fl_orientacao,
				   ct.nome_tarefa,
				   uc.nome AS analista,
				   uc2.nome AS programador,
				   CASE WHEN t.prioridade = 'S' THEN 'Sim'
				        WHEN t.prioridade = 'N' THEN 'Não'
						ELSE t.prioridade
				   END AS ds_prioridade, 
				   TO_CHAR(t.dt_inicio_prev, 'DD/MM/YYYY') AS dt_inicio_prev, 
				   TO_CHAR(t.dt_fim_prev, 'DD/MM/YYYY') AS dt_fim_prev, 
				   TO_CHAR(t.dt_inicio_prog, 'DD/MM/YYYY HH24:MI:SS') AS dt_inicio_prog,
				   TO_CHAR(t.dt_fim_prog, 'DD/MM/YYYY HH24:MI:SS') AS dt_fim_prog,
				   TO_CHAR(t.dt_ok_anal, 'DD/MM/YYYY HH24:MI:SS') AS dt_ok_anal,
				   t.resumo,
				   t.descricao,
				   t.casos_testes,
				   t.tabs_envolv,
				   t.ds_nome_tela,
				   t.ds_menu,
				   CASE WHEN t.fl_orientacao = 'P' THEN 'Paisagem'
				        WHEN t.fl_orientacao = 'R' THEN 'Retrato'
						ELSE t.fl_orientacao
				   END AS ds_orientacao,
				   t.ds_nome_tela,
				   t.ds_dir,
				   t.ds_nome_arq,
				   t.ds_delimitador,
				   CASE WHEN t.fl_largura = 'S' THEN 'Sim'
				        WHEN t.fl_largura = 'N' THEN 'Não'
						ELSE t.fl_largura
				   END AS ds_largura,
				   t.ds_ordem,
				   t.observacoes,
				   t.codigo,
				   t.cd_tipo_tarefa,
				   t.cd_mandante,
				   t.cd_recurso,
				   t.prioridade,
				   t.nr_nivel_prioridade,
				   t.fl_checklist,
				   t.dt_encaminhamento,
				   t.ds_nome_arq,
				   t.ds_dir,
				   t.ds_delimitador,
				   t.fl_largura,
				   t.status_atual,
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
	
	function lovs( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_tarefas_lovs,
				   ds_seq,
				   ds_tabela,
				   ds_campo_ori,
				   ds_campo_des
			  FROM projetos.tarefas_lovs
			 WHERE cd_tarefa    = ".intval($args['cd_tarefa'])."
			   AND cd_atividade = ".intval($args['cd_atividade']).";";
	
		$result = $this->db->query($qr_sql);		
	}
	
	function tabelas( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_tarefas_tabelas,
				   ds_banco, 
				   ds_tabela, 
				   ds_campo, 
				   ds_label,
				   fl_tipo,
				   fl_visivel,
				   fl_campo AS fl_campo_id,					
				   (CASE UPPER(fl_campo)
						 WHEN 'T' THEN 'Text Item'
					     WHEN 'L' THEN 'List Item'
					     WHEN 'C' THEN 'Check Box'
						 WHEN 'R' THEN 'Radio Group'
						 WHEN 'P' THEN 'Push Bottom'
						 WHEN 'D' THEN 'Display Item'
						 ELSE UPPER(fl_campo)
				     END) AS fl_campo,
					ds_vl_dominio,
					(CASE UPPER(fl_campo_de)
                          WHEN 'E' THEN 'Enable'
                          WHEN 'D' THEN 'Disable'
					      ELSE UPPER(fl_campo_de)
	                 END) AS fl_campo_de, 
					fl_campo_de AS fl_campo_de_id
			   FROM projetos.tarefas_tabelas
			  WHERE cd_atividade = ".intval($args['cd_atividade'])."
				AND cd_tarefa    = ".intval($args['cd_tarefa'])."
				AND fl_tipo      = 'T'
			  ORDER BY UPPER(ds_banco),
				       UPPER(ds_tabela),
                       UPPER(ds_campo)	";

		$result = $this->db->query($qr_sql);	
	}
	
	function ordenacao( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_tarefas_tabelas,
				   ds_banco, 
				   ds_tabela, 
				   ds_campo, 
				   ds_label,
				   fl_tipo,
				   nr_ordem,
				   (CASE UPPER(fl_campo)
				   	     WHEN 'T' THEN 'Text Item'
						 WHEN 'L' THEN 'List Item'
						 WHEN 'C' THEN 'Check Box'
						 WHEN 'R' THEN 'Radio Group'
						 WHEN 'P' THEN 'Push Bottom'
						 WHEN 'D' THEN 'Display Item'
						 ELSE UPPER(fl_campo)
					END) AS fl_campo,
				   ds_vl_dominio,
				   (CASE UPPER(fl_campo_de)
						 WHEN 'E' THEN 'Enable'
						 WHEN 'D' THEN 'Disable'
						 ELSE UPPER(fl_campo_de)
					END) AS	fl_campo_de
			   FROM projetos.tarefas_tabelas
			  WHERE cd_atividade = ".intval($args['cd_atividade'])."
				AND cd_tarefa    = ".intval($args['cd_tarefa'])."
				AND fl_tipo      = 'O'
		   ORDER BY nr_ordem";
		   
		$result = $this->db->query($qr_sql);	
	}
	
	function paremetros( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_tarefas_parametros,
				   ds_campo,
				   ds_tipo,
				   nr_ordem
			   FROM projetos.tarefas_parametros
			  WHERE cd_atividade = ".intval($args['cd_atividade'])."
				AND cd_tarefa    = ".intval($args['cd_tarefa']).";";
				
		$result = $this->db->query($qr_sql);
	}
	
	function tarefas_reports( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_tarefas_tabelas,
				   ds_banco,
				   ds_tabela,
				   ds_campo,
				   ds_label
			  FROM projetos.tarefas_tabelas
			 WHERE cd_atividade = ".intval($args['cd_atividade'])."
		   	   AND cd_tarefa    = ".intval($args['cd_tarefa'])."
			 ORDER BY UPPER(ds_banco),
		   		      UPPER(ds_tabela),
					  UPPER(ds_campo);";
					  
		$result = $this->db->query($qr_sql);
	}
	
	function tipos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_tarefas_layout,
				   ds_tipo
			  FROM projetos.tarefas_layout
			 WHERE cd_atividade = ".intval($args['cd_atividade'])."
		  	   AND cd_tarefa    = ".intval($args['cd_tarefa']).";";

		$result = $this->db->query($qr_sql);
	}
	
	function tipo_campos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_tarefas_layout_campo,
				   ds_nome,
				   ds_tamanho,
				   ds_caracteristica,
				   ds_formato,
				   ds_definicao
			  FROM projetos.tarefas_layout_campo
			 WHERE cd_atividade      = ".intval($args['cd_atividade'])."
			   AND cd_tarefa         = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_layout = ".intval($args['cd_tarefas_layout']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function anexos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM projetos.tarefa_anexo a
			 WHERE a.cd_tarefa = ". $args['codigo']."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC";
		$result = $this->db->query($qr_sql);
	}
	
	function programas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT programa AS value,
				   programa AS text 
			  FROM projetos.programas 
			 ORDER BY programa";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function analistas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   nome AS text
	          FROM projetos.usuarios_controledi 
		     WHERE tipo in('N','G') 
			   AND (SELECT area 
					  FROM projetos.atividades  
				     WHERE numero = ".intval($args['cd_atividade']).") IN (divisao, divisao_ant)
			 ORDER BY nome ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function programador(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   nome AS text
	          FROM projetos.usuarios_controledi 
		     WHERE (tipo <> 'X' OR codigo = (SELECT cd_recurso 
			                                   FROM projetos.tarefas
											  WHERE cd_tarefa = ".intval($args['cd_tarefa']).")) 
			    AND (SELECT area 
					   FROM projetos.atividades  
				      WHERE numero = ".intval($args['cd_atividade']).") IN (divisao, divisao_ant)
			 ORDER BY nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function tipo_tarefas(&$result, $args=array())
	{
		$qr_sql = "
			 SELECT cd_tarefa AS value, 
					nome_tarefa AS text
			   FROM projetos.cad_tarefas 
			  ORDER BY nome_tarefa ";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_tarefa']) == 0)
		{
			$retorno = intval($this->db->get_new_id("projetos.tarefas", "cd_tarefa"));

			$qr_sql = "
				INSERT INTO projetos.tarefas
				     (
					   cd_atividade,
					   cd_tarefa,
					   programa,
					   cd_tipo_tarefa,
					   cd_mandante ,
					   cd_recurso,
					   prioridade,
					   fl_checklist,
					   dt_inicio_prev,
					   dt_fim_prev,
					   resumo,
					   descricao,
					   casos_testes,
					   tabs_envolv,
					   fl_tarefa_tipo,
					   nr_nivel_prioridade,
					   status_atual,
					   ds_nome_tela,
					   ds_menu,
					   fl_orientacao,
					   ds_nome_arq,
					   ds_dir,
					   ds_delimitador,
					   fl_largura,
					   ds_ordem
					 )
				VALUES
				     (
						".intval($args['cd_atividade']).",
						".intval($retorno).",
						".(trim($args['programa']) != '' ? str_escape($args['programa']) : "DEFAULT").",
						".(trim($args['cd_tipo_tarefa']) != '' ? intval($args['cd_tipo_tarefa']) : "DEFAULT").",
						".(trim($args['cd_mandante']) != '' ? intval($args['cd_mandante']) : "DEFAULT").",
						".(trim($args['cd_recurso']) != '' ? intval($args['cd_recurso']) : "DEFAULT").",
						".(trim($args['prioridade']) != '' ? str_escape($args['prioridade']) : "DEFAULT").",
						".(trim($args['fl_checklist']) != '' ? str_escape($args['fl_checklist']) : "DEFAULT").",
						".(trim($args['dt_inicio_prev']) != '' ? "TO_DATE('".trim($args['dt_inicio_prev'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['dt_fim_prev']) != '' ? "TO_DATE('".trim($args['dt_fim_prev'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['resumo']) != '' ? str_escape($args['resumo']) : "DEFAULT").",
						".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "DEFAULT").",
						".(trim($args['casos_testes']) != '' ? str_escape($args['casos_testes']) : "DEFAULT").",
						".(trim($args['tabs_envolv']) != '' ? str_escape($args['tabs_envolv']) : "DEFAULT").",
						".(trim($args['fl_tarefa_tipo']) != '' ? str_escape($args['fl_tarefa_tipo']) : "DEFAULT").",
						".(trim($args['nr_nivel_prioridade']) != '' ? intval($args['nr_nivel_prioridade']) : "DEFAULT").",
						'AMAN',
						".(trim($args['ds_nome_tela']) != '' ? str_escape($args['ds_nome_tela']) : "DEFAULT").",
						".(trim($args['ds_menu']) != '' ? str_escape($args['ds_menu']) : "DEFAULT").",
						".(trim($args['fl_orientacao']) != '' ? str_escape($args['fl_orientacao']) : "DEFAULT").",
						".(trim($args['ds_nome_arq']) != '' ? str_escape($args['ds_nome_arq']) : "DEFAULT").",
						".(trim($args['ds_dir']) != '' ? str_escape($args['ds_dir']) : "DEFAULT").",
						".(trim($args['ds_delimitador']) != '' ? str_escape($args['ds_delimitador']) : "DEFAULT").",
						".(trim($args['fl_largura']) != '' ? str_escape($args['fl_largura']) : "DEFAULT").",
						".(trim($args['ds_ordem']) != '' ? str_escape($args['ds_ordem']) : "DEFAULT")."
					 );";
			
		}
		else
		{
			$retorno = $args['cd_tarefa'];
			
			$qr_sql = "
				UPDATE projetos.tarefas
				   SET programa       	   = ".(trim($args['programa']) != '' ? str_escape($args['programa']) : "DEFAULT").",
				       cd_tipo_tarefa      = ".(trim($args['cd_tipo_tarefa']) != '' ? intval($args['cd_tipo_tarefa']) : "DEFAULT").",
					   cd_mandante    	   =  ".(trim($args['cd_mandante']) != '' ? intval($args['cd_mandante']) : "DEFAULT").",
					   cd_recurso     	   = ".(trim($args['cd_recurso']) != '' ? intval($args['cd_recurso']) : "DEFAULT").",
					   prioridade     	   = ".(trim($args['prioridade']) != '' ? str_escape($args['prioridade']) : "DEFAULT").",
					   fl_checklist   	   = ".(trim($args['fl_checklist']) != '' ? str_escape($args['fl_checklist']) : "DEFAULT").",
					   dt_inicio_prev 	   = ".(trim($args['dt_inicio_prev']) != '' ? "TO_DATE('".trim($args['dt_inicio_prev'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   dt_fim_prev    	   = ".(trim($args['dt_fim_prev']) != '' ? "TO_DATE('".trim($args['dt_fim_prev'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   resumo         	   = ".(trim($args['resumo']) != '' ? str_escape($args['resumo']) : "DEFAULT").",
					   descricao      	   = ".(trim($args['descricao']) != '' ? str_escape($args['descricao']) : "DEFAULT").",
					   casos_testes   	   = ".(trim($args['casos_testes']) != '' ? str_escape($args['casos_testes']) : "DEFAULT").",
					   tabs_envolv    	   = ".(trim($args['tabs_envolv']) != '' ? str_escape($args['tabs_envolv']) : "DEFAULT").",
					   nr_nivel_prioridade = ".(trim($args['nr_nivel_prioridade']) != '' ? intval($args['nr_nivel_prioridade']) : "DEFAULT").",
					   ds_nome_tela    	   = ".(trim($args['ds_nome_tela']) != '' ? str_escape($args['ds_nome_tela']) : "DEFAULT").",
					   ds_menu       	   = ".(trim($args['ds_menu']) != '' ? str_escape($args['ds_menu']) : "DEFAULT").",
					   fl_orientacao       = ".(trim($args['fl_orientacao']) != '' ? str_escape($args['fl_orientacao']) : "DEFAULT").",
					   ds_nome_arq         = ".(trim($args['ds_nome_arq']) != '' ? str_escape($args['ds_nome_arq']) : "DEFAULT").",
					   ds_dir              = ".(trim($args['ds_dir']) != '' ? str_escape($args['ds_dir']) : "DEFAULT").",
					   ds_delimitador      = ".(trim($args['ds_delimitador']) != '' ? str_escape($args['ds_delimitador']) : "DEFAULT").",
					   fl_largura          = ".(trim($args['fl_largura']) != '' ? str_escape($args['fl_largura']) : "DEFAULT").",
					   ds_ordem            = ".(trim($args['ds_ordem']) != '' ? str_escape($args['ds_ordem']) : "DEFAULT")."
				 WHERE cd_atividade = ".intval($args['cd_atividade'])."
				   AND cd_tarefa = ".intval($retorno).";
				   
				   
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
					  ".intval($retorno).",
					  ".intval($args['cd_atividade']).",
					  ".intval($args['cd_recurso']).",
					  CURRENT_TIMESTAMP,
					  'Tarefa Alterada.',
					  'AMAN',
					  ".intval($args['cd_mandante'])."
					 ) ;";
		}

		$result = $this->db->query($qr_sql);
		
		return $retorno;
	}
	
	function salvar_lovs( &$result, $args=array() )
	{	
		$qr_sql = "
			INSERT INTO projetos.tarefas_lovs 
				 (
				   cd_atividade,
				   cd_tarefa,
				   ds_seq,
				   ds_tabela,
				   ds_campo_ori,
				   ds_campo_des
				 ) 
			VALUES 
				 (
					".intval($args['cd_atividade']).", 
					".intval($args['cd_tarefa']).", 
					".(trim($args['ds_seq']) != '' ? str_escape($args['ds_seq']) : "DEFAULT").",
					".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",
					".(trim($args['ds_campo_ori']) != '' ? str_escape($args['ds_campo_ori']) : "DEFAULT").",
					".(trim($args['ds_campo_des']) != '' ? str_escape($args['ds_campo_des']) : "DEFAULT")."
				   )";

		$result = $this->db->query($qr_sql);
	}
	
	function excluir_lovs( &$result, $args=array() )
	{
		$qr_sql = "
			DELETE
			  FROM projetos.tarefas_lovs 
			 WHERE cd_atividade    = ".intval($args['cd_atividade'])."
			   AND cd_tarefa       = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_lovs = ".intval($args['cd_tarefas_lovs']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_parametros( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.tarefas_parametros 
				 (
					cd_atividade,
					cd_tarefa,
					ds_campo, 
					ds_tipo, 
					nr_ordem
				 )  
			VALUES 
				 (
					".intval($args['cd_atividade']).", 
					".intval($args['cd_tarefa']).", 
					".(trim($args['ds_campo']) != '' ? str_escape($args['ds_campo']) : "DEFAULT").",
					".(trim($args['ds_tipo']) != '' ? str_escape($args['ds_tipo']) : "DEFAULT").",
					".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT")."
				  )";
				  
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_parametros( &$result, $args=array() )
	{
		$qr_sql = "
			DELETE
			  FROM projetos.tarefas_parametros 
			 WHERE cd_atividade          = ".intval($args['cd_atividade'])."
			   AND cd_tarefa             = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_parametros = ".intval($args['cd_tarefas_parametros']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function encaminhar( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.tarefas
		       SET dt_encaminhamento = CURRENT_TIMESTAMP,
		           status_atual      = 'AMAN'
		     WHERE cd_tarefa    = ".intval($args['cd_tarefa'])."
		       AND cd_atividade = ".intval($args['cd_atividade']).";";
		   
		$result = $this->db->query($qr_sql);
	}
	
	function conforme( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.tarefas 
		       SET dt_ok_anal   = CURRENT_TIMESTAMP, 
				   status_atual = 'CONC', 
				   prioridade = 'N' 
		     WHERE cd_tarefa    = ".intval($args['cd_tarefa'])."
			   AND cd_atividade = ".intval($args['cd_atividade']).";";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_nao_conforme( &$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.tarefa_historico 
				 ( 
				   cd_tarefa,  	
				   cd_atividade, 	
				   cd_recurso,   	
				   timestamp_alteracao,   	
				   descricao,  				
				   status_atual,
				   ds_obs,
				   cd_usuario_inclusao,
				   dt_inclusao
				 ) 
		    VALUES
				 ( 
				   ".intval($args['cd_tarefa']).", 
				   ".intval($args['cd_atividade']).", 
				   (SELECT cd_recurso 
					   FROM projetos.tarefas
					  WHERE cd_tarefa    = ".intval($args['cd_tarefa'])."
						AND cd_atividade = ".intval($args['cd_atividade'])."),
				   CURRENT_TIMESTAMP, 
				   'Tarefa Reaberta.', 
				   'AMAN',
				   ".str_escape($args['ds_obs']).",
				   (SELECT cd_mandante 
			          FROM projetos.tarefas
			         WHERE cd_tarefa    = ".intval($args['cd_tarefa'])."
			           AND cd_atividade = ".intval($args['cd_atividade'])."),
					CURRENT_TIMESTAMP
				);
				
			UPDATE projetos.tarefas 
			   SET dt_ok_anal   = NULL,
				   dt_fim_prog  = NULL,
				   status_atual = 'EMAN' 
			 WHERE cd_tarefa    = ".intval($args['cd_tarefa'])."
			   AND cd_atividade = ".intval($args['cd_atividade']).";	";
				
		$result = $this->db->query($qr_sql);
	}
	
	function tabelas_postgresql( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT n.nspname || '.' || c.relname AS ds_tabela  
			  FROM pg_namespace n, 
				   pg_class c
			 WHERE n.oid     = c.relnamespace
			   AND c.relkind = 'r'
			   AND n.nspname not like 'pg\\_%'
			   AND n.nspname != 'information_schema'
			 ORDER BY UPPER(nspname), 
					  UPPER(relname)";
		
		$result = $this->db->query($qr_sql);
	}
	
	function tabelas_oracle( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT esquema || '.' || tabela  AS ds_tabela
              FROM oracle.lista_tabela;";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function campos_postgresql( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.attname || '(' || t.typname || ')' AS ds_campo
			  FROM pg_class as c   
			  JOIN pg_attribute a
			    ON a.attrelid = c.oid 
			  JOIN pg_type t
			    ON a.atttypid = t.oid 
			  JOIN pg_namespace n 
			    ON n.oid = c.relnamespace
			 WHERE a.attnum > 0 
			   AND n.nspname = '".trim($args['nspname'])."' 
			   AND c.relname = '".trim($args['relname'])."'
			 ORDER BY a.attnum	";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function campos_oracle( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT coluna || '(' || tipo|| ')' AS ds_campo
			  FROM oracle.tabela_coluna('".trim($args['nspname'])."' ,'".trim($args['relname'])."') 
				AS (
					esquema TEXT, 
					tabela TEXT, 
					coluna TEXT, 
					tipo TEXT
				   );
			  ";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_relatorios( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.tarefas_tabelas 
				 (
					cd_atividade,
					cd_tarefa,
					ds_banco, 
					ds_tabela, 
					ds_campo,
					fl_tipo,
					ds_label
				 )
			VALUES 
				 (
					".intval($args['cd_atividade']).",
					".intval($args['cd_tarefa']).",
					'".trim($args['ds_banco'])."',
					'".trim($args['ds_tabela'])."',
					'".trim($args['ds_campo'])."',
					'O',
					'".trim($args['ds_label'])."'
				  )";

		$result = $this->db->query($qr_sql);
	}
	
	function excluir_relatorios( &$result, $args=array() )
	{
		$qr_sql = "
			DELETE 
			  FROM projetos.tarefas_tabelas 
			 WHERE cd_atividade       = ".intval($args['cd_atividade'])."
			   AND cd_tarefa          = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_tabelas = ".intval($args['cd_tarefas_tabelas']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_tipo( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.tarefas_layout
				 (
					cd_atividade,
					cd_tarefa,
					ds_tipo
				 )  
			VALUES 
				 (
					".intval($args['cd_atividade']).", 
					".intval($args['cd_tarefa']).", 
					'".trim($args['ds_tipo'])."'
				  );";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_campo( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.tarefas_layout_campo
				 (
					cd_atividade,
					cd_tarefa,
					cd_tarefas_layout,
					ds_nome,
					ds_tamanho,
					ds_caracteristica,
					ds_formato,
					ds_definicao
				 )  
		    VALUES 
			     (
					".intval($args['cd_atividade']).", 
					".intval($args['cd_tarefa']).", 
					".intval($args['cd_tarefas_layout']).", 
					".str_escape($args['ds_nome']).",
					".str_escape($args['ds_tamanho']).",
					".str_escape($args['ds_caracteristica']).",
					".str_escape($args['ds_formato']).",
					".str_escape($args['ds_definicao'])."
				  )";
			
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_tipo( &$result, $args=array() )
	{
		$qr_sql = "
			DELETE 
			  FROM projetos.tarefas_layout_campo
			 WHERE cd_tarefas_layout = ".intval($args['cd_tarefas_layout'])."
			   AND cd_atividade      = ".intval($args['cd_atividade'])."
			   AND cd_tarefa         = ".intval($args['cd_tarefa']).";
			   
			DELETE 
			  FROM projetos.tarefas_layout
			 WHERE cd_tarefas_layout = ".intval($args['cd_tarefas_layout'])."
			   AND cd_atividade      = ".intval($args['cd_atividade'])."
			   AND cd_tarefa         = ".intval($args['cd_tarefa']).";";
	
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_campo( &$result, $args=array() )
	{
		$qr_sql = "
			DELETE 
			  FROM projetos.tarefas_layout_campo
			 WHERE cd_tarefas_layout_campo = ".intval($args['cd_tarefas_layout_campo'])."
			   AND cd_atividade            = ".intval($args['cd_atividade'])."
			   AND cd_tarefa               = ".intval($args['cd_tarefa']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_tabela( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.tarefas_tabelas 
				 (
					cd_atividade,
					cd_tarefa,
					ds_banco, 
					ds_tabela, 
					ds_campo, 
					fl_tipo

				 )  
			VALUES 
				 (
					".intval($args['cd_atividade']).", 
					".intval($args['cd_tarefa']).", 
					".str_escape($args['ds_banco']).",
					".str_escape($args['ds_tabela']).",
					".str_escape($args['ds_campo']).",
					'T'
				  );";
				  
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_ordenacao( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.tarefas_tabelas 
				 (
					cd_atividade,
					cd_tarefa,
					ds_banco, 
					ds_tabela, 
					ds_campo, 
					nr_ordem,
					fl_tipo

				 )  
			VALUES 
				 (
					".intval($args['cd_atividade']).", 
					".intval($args['cd_tarefa']).", 
					".str_escape($args['ds_banco']).",
					".str_escape($args['ds_tabela']).",
					".str_escape($args['ds_campo']).",
					".intval($args['nr_ordem']).", 
					'O'
				  );";
				  
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_tabela( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.tarefas_tabelas 
			   SET ds_campo       = ".(trim($args['ds_campo']) != '' ? str_escape($args['ds_campo']) : "DEFAULT").",
			       fl_campo       = ".(trim($args['fl_campo']) != '' ? str_escape($args['fl_campo']) : "DEFAULT").",
				   ds_vl_dominio  = ".(trim($args['ds_vl_dominio']) != '' ? str_escape($args['ds_vl_dominio']) : "DEFAULT").",
				   fl_campo_de    = ".(trim($args['fl_campo_de']) != '' ? str_escape($args['fl_campo_de']) : "DEFAULT").",
				   ds_label       = ".(trim($args['ds_label']) != '' ? str_escape($args['ds_label']) : "DEFAULT").",
				   fl_visivel     = ".(trim($args['fl_visivel']) != '' ? str_escape($args['fl_visivel']) : "DEFAULT")."
			 WHERE cd_atividade       = ".intval($args['cd_atividade'])."
			   AND cd_tarefa          = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_tabelas = ".intval($args['cd_tarefas_tabelas']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_ordenacao( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.tarefas_tabelas 
			   SET nr_ordem       = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT")."
			 WHERE cd_atividade       = ".intval($args['cd_atividade'])."
			   AND cd_tarefa          = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_tabelas = ".intval($args['cd_tarefas_tabelas']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_relatorio( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.tarefas_tabelas 
			   SET ds_label    = ".(trim($args['ds_label']) != '' ? str_escape($args['ds_label']) : "DEFAULT").",
			       ds_campo    = ".(trim($args['ds_campo']) != '' ? str_escape($args['ds_campo']) : "DEFAULT")."
			 WHERE cd_atividade       = ".intval($args['cd_atividade'])."
			   AND cd_tarefa          = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_tabelas = ".intval($args['cd_tarefas_tabelas']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_tabela( &$result, $args=array() )
	{
		$qr_sql = "
			DELETE 
			  FROM projetos.tarefas_tabelas 
			 WHERE cd_atividade       = ".intval($args['cd_atividade'])."
			   AND cd_tarefa          = ".intval($args['cd_tarefa'])."
			   AND cd_tarefas_tabelas = ".intval($args['cd_tarefas_tabelas']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_tarefa( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.tarefas 
	           SET dt_exclusao = CURRENT_TIMESTAMP 
             WHERE cd_atividade = ".intval($args['cd_atividade'])."
			   AND cd_tarefa = ".intval($args['cd_tarefa']).";";
			   
		$result = $this->db->query($qr_sql);
	}
}
?>