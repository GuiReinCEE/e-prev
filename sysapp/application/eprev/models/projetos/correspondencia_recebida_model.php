<?php

class correspondencia_recebida_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function gerencia(&$result, $args=array())
    {
		$qr_sql = "
			SELECT codigo AS value, 
				   nome AS text 
			  FROM projetos.divisoes 
			 WHERE tipo = 'DIV'
			 ORDER BY nome";
			
		$result = $this->db->query($qr_sql);
	}
	
	function grupo(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_correspondencia_recebida_grupo AS value, 
				   ds_nome AS text 
			  FROM projetos.correspondencia_recebida_grupo 
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_nome";
			
		$result = $this->db->query($qr_sql);
	}
	
	function grupo_destino(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
			  FROM projetos.correspondencia_recebida_grupo_usuario crgu
			  JOIN projetos.correspondencia_recebida cr
			    ON cr.cd_correspondencia_recebida_grupo = crgu.cd_correspondencia_recebida_grupo
			 WHERE crgu.dt_exclusao IS NULL
			   AND cr.cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida'])."
			   AND crgu.cd_usuario = ".intval($args['cd_usuario']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT r.cd_correspondencia_recebida,
			       funcoes.nr_correspondencia_recebida(r.nr_ano, r.nr_numero) AS ano_numero,
				   d.nome AS gerencia_destino,
				   r.cd_gerencia_destino,
				   CASE WHEN r.dt_envio IS NULL THEN 'Aguardando Envio'
						WHEN r.dt_envio IS NOT NULL AND dt_recebido IS NULL THEN 'Aguardando Recebimento'
						WHEN r.dt_envio IS NOT NULL AND dt_recebido IS NOT NULL THEN 'Recebido'
				   END AS status,
				   CASE WHEN r.dt_envio IS NULL THEN 'label-important'
						WHEN r.dt_envio IS NOT NULL AND dt_recebido IS NULL THEN 'label-info'
						WHEN r.dt_envio IS NOT NULL AND dt_recebido IS NOT NULL THEN 'label-success'
				   END AS class_status,
				   (SELECT COUNT(*)
					  FROM projetos.correspondencia_recebida_item ri
					 WHERE ri.cd_correspondencia_recebida = r.cd_correspondencia_recebida
					   AND ri.dt_exclusao IS NULL) AS tl_itens,
				   (SELECT COUNT(*)
					  FROM projetos.correspondencia_recebida_item ri
					 WHERE ri.cd_correspondencia_recebida = r.cd_correspondencia_recebida
					   AND ri.dt_exclusao IS NULL
					   AND (ri.dt_recebido IS NOT NULL OR ri.dt_recusa IS NOT NULL)) AS tl_recebido_recusado,
				   (SELECT COUNT(*)
					  FROM projetos.correspondencia_recebida_item ri
					 WHERE ri.cd_correspondencia_recebida = r.cd_correspondencia_recebida
					   AND ri.dt_exclusao IS NULL
					   AND ri.dt_recebido IS NOT NULL) AS tl_recebido,
					(SELECT COUNT(*)
					  FROM projetos.correspondencia_recebida_item ri
					 WHERE ri.cd_correspondencia_recebida = r.cd_correspondencia_recebida
					   AND ri.dt_exclusao IS NULL
					   AND ri.dt_recusa IS NOT NULL) AS tl_recusado,
				   TO_CHAR(r.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(r.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome AS usuario_envio,
				   TO_CHAR(dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
				   uc2.nome AS usuario_recebido,
				   g.ds_nome AS grupo
			  FROM projetos.correspondencia_recebida r
			  LEFT JOIN projetos.divisoes d
				ON d.codigo = r.cd_gerencia_destino
			  LEFT JOIN projetos.correspondencia_recebida_grupo g
				ON g.cd_correspondencia_recebida_grupo = r.cd_correspondencia_recebida_grupo
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = cd_usuario_envio
			  LEFT JOIN projetos.usuarios_controledi uc2
				ON uc2.codigo = cd_usuario_recebido
			 WHERE r.dt_exclusao IS NULL
			 ".(trim($args['cd_gerencia']) != 'GFC' ? ($this->session->userdata('codigo') != 251 ? "AND (r.cd_gerencia_destino = '".trim($args['cd_gerencia'])."' 
			                                                                                         OR ".$this->session->userdata('codigo')." IN (SELECT rgu.cd_usuario
																																					 FROM projetos.correspondencia_recebida_grupo_usuario rgu
																																					WHERE dt_exclusao IS NULL
																																					  AND rgu.cd_correspondencia_recebida_grupo = r.cd_correspondencia_recebida_grupo)) 
			                                                                                        AND r.dt_envio IS NOT NULL" : "") : '')."
			 ".(trim($args['nr_numero']) != '' ? "AND r.nr_numero = ".intval($args['nr_numero']) : '')."
			 ".(trim($args['nr_ano']) != '' ? "AND r.nr_ano = ".intval($args['nr_ano']) : '')."
			 ".(trim($args['cd_gerencia_destino']) != '' ? "AND r.cd_gerencia_destino = '".trim($args['cd_gerencia_destino'])."'" : '')."
			 ".(trim($args['cd_correspondencia_recebida_grupo']) != '' ? "AND r.cd_correspondencia_recebida_grupo = '".trim($args['cd_correspondencia_recebida_grupo'])."'" : '')."
			 ".(trim($args['fl_status']) == 'AE' ? "AND dt_envio IS NULL" : '')."
			 ".(trim($args['fl_status']) == 'AR' ? "AND dt_envio IS NOT NULL 
			                                        AND dt_recebido IS NULL" : '')."
			 ".(trim($args['fl_status']) == 'RE' ? "AND dt_envio IS NOT NULL 
			                                        AND dt_recebido IS NOT NULL" : '')."
			 ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND CAST(r.dt_envio AS DATE) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(((trim($args['dt_inclusao_ini']) != "") AND (trim($args['dt_inclusao_fim']) != "")) ? " AND CAST(r.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "").";";
		#echo "<PRE>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}
	
	function listar_relatorio(&$result, $args=array())
	{
		$qr_sql = "
			SELECT funcoes.nr_correspondencia_recebida(r.nr_ano, r.nr_numero) AS ano_numero,
			       r.cd_correspondencia_recebida,
				   d.nome AS gerencia_destino,
				   r.cd_gerencia_destino,
				   CASE WHEN r.dt_envio IS NULL THEN 'Aguardando Envio'
					    WHEN r.dt_envio IS NOT NULL AND ri.dt_recebido IS NULL AND ri.dt_recusa IS NULL THEN 'Aguardando Recebimento'
				 	    WHEN r.dt_envio IS NOT NULL AND ri.dt_recebido IS NOT NULL THEN 'Recebido'
						WHEN r.dt_envio IS NOT NULL AND ri.dt_recusa IS NOT NULL THEN 'Recusado'
				   END AS status,
				   CASE WHEN r.dt_envio IS NULL THEN 'label-important'
						WHEN r.dt_envio IS NOT NULL AND ri.dt_recebido IS NULL AND ri.dt_recusa IS NULL THEN 'label-info'
						WHEN r.dt_envio IS NOT NULL AND ri.dt_recebido IS NOT NULL THEN  'label-success'
						WHEN r.dt_envio IS NOT NULL AND ri.dt_recusa IS NOT NULL THEN ''
				   END AS class_status,
				   TO_CHAR(r.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   uc.nome AS usuario_envio,
				   TO_CHAR(r.dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
				   uc2.nome AS usuario_recebido, 
				   TO_CHAR(ri.dt_correspondencia, 'DD/MM/YYYY HH24:MI') AS dt_correspondencia,
				   TO_CHAR(ri.dt_recusa, 'DD/MM/YYYY HH24:MI:SS') AS dt_recusa,
				   uc3.nome AS usuario_recusa,
				   ri.motivo_recusa,
				   ri.origem,
				   rt.ds_correspondencia_recebida_tipo,
				   ri.identificador,
				   ri.cd_empresa || '/' || ri.cd_registro_empregado || '/' || ri.seq_dependencia AS re,
				   ri.cd_registro_empregado,
				   ri.cd_empresa,
				   ri.seq_dependencia,
				   p.nome,
				   g.ds_nome AS grupo
			  FROM projetos.correspondencia_recebida_item ri
			  JOIN projetos.correspondencia_recebida r
				ON r.cd_correspondencia_recebida = ri.cd_correspondencia_recebida
			  LEFT JOIN projetos.correspondencia_recebida_grupo g
				ON g.cd_correspondencia_recebida_grupo = r.cd_correspondencia_recebida_grupo
			  LEFT JOIN projetos.divisoes d
				ON d.codigo = r.cd_gerencia_destino
			  JOIN projetos.correspondencia_recebida_tipo rt
				ON rt.cd_correspondencia_recebida_tipo = ri.cd_correspondencia_recebida_tipo
			  LEFT JOIN projetos.usuarios_controledi uc
				ON uc.codigo = r.cd_usuario_envio
			  LEFT JOIN projetos.usuarios_controledi uc2
				ON uc2.codigo = r.cd_usuario_recebido
			  LEFT JOIN projetos.usuarios_controledi uc3
				ON uc3.codigo = ri.cd_usuario_recusa
			  LEFT JOIN public.participantes p
				ON p.cd_registro_empregado = ri.cd_registro_empregado
			   AND p.cd_empresa            = ri.cd_empresa
			   AND p.seq_dependencia       = ri.seq_dependencia
			 WHERE ri.dt_exclusao IS NULL
			   AND r.dt_exclusao IS NULL
			   AND r.dt_envio IS NOT NULL
			   ".(trim($args['nr_numero']) != '' ? "AND r.nr_numero = ".intval($args['nr_numero']) : '')."
			   ".(trim($args['nr_ano']) != '' ? "AND r.nr_ano = ".intval($args['nr_ano']) : '')."
			   ".(trim($args['cd_gerencia_destino']) != '' ? "AND r.cd_gerencia_destino = '".trim($args['cd_gerencia_destino'])."'" : '')."
			   ".(trim($args['fl_status']) == 'AE' ? "AND r.dt_envio IS NULL" : '')."
			   ".(trim($args['fl_status']) == 'AR' ? "AND r.dt_envio IS NOT NULL 
			                                          AND r.dt_recebido IS NULL" : '')."
			   ".(trim($args['fl_status']) == 'RE' ? "AND r.dt_envio IS NOT NULL 
			                                          AND r.dt_recebido IS NOT NULL" : '')."
			   ".(((trim($args['dt_envio_ini']) != "") AND (trim($args['dt_envio_fim']) != "")) ? " AND CAST(r.dt_envio AS DATE) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_recebido_ini']) != "") AND (trim($args['dt_recebido_fim']) != "")) ? " AND CAST(r.dt_recebido AS DATE) BETWEEN TO_DATE('".$args['dt_recebido_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_recebido_fim']."', 'DD/MM/YYYY')" : "")."	
			   ".(trim($args['cd_usuario_envio']) != '' ? "AND r.cd_usuario_envio = ".intval($args['cd_usuario_envio']) : '')."
			   ".(trim($args['cd_usuario_recebido']) != '' ? "AND r.cd_usuario_recebido = ".intval($args['cd_usuario_recebido']) : '')."
			   ".(trim($args['cd_empresa']) != '' ? "AND ri.cd_empresa = ".intval($args['cd_empresa']) : '')."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND ri.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : '')."
			   ".(trim($args['seq_dependencia']) != '' ? "AND ri.seq_dependencia = ".intval($args['seq_dependencia']) : '')."
			   ".(trim($args['nome_participante']) != '' ? "AND funcoes.remove_acento(UPPER(p.nome)) LIKE funcoes.remove_acento(UPPER(('%".str_replace(' ','%', trim($args['nome_participante']))."%')))" : '')."
			   ".(trim($args['cd_correspondencia_recebida_tipo']) != '' ? "AND ri.cd_correspondencia_recebida_tipo = ".intval($args['cd_correspondencia_recebida_tipo']) : '')."
			   ".(trim($args['identificador']) != '' ? "AND funcoes.remove_acento(UPPER(ri.identificador)) LIKE funcoes.remove_acento(UPPER(('%".str_replace(' ','%', trim($args['identificador']))."%')))" : '')."
			   ".(trim($args['fl_recebido']) == 'S' ? "AND ri.dt_recebido IS NOT NULL" : '')."
			   ".(trim($args['fl_recebido']) == 'N' ? "AND ri.dt_recebido IS NULL" : '')."
			   ".(trim($args['fl_recusado']) == 'S' ? "AND ri.dt_recusa IS NOT NULL" : '')."
			   ".(trim($args['fl_recusado']) == 'N' ? "AND ri.dt_recusa IS NULL" : '').";";
				  
			$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT r.cd_correspondencia_recebida,
			       TO_CHAR(r.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   funcoes.nr_correspondencia_recebida(r.nr_ano, r.nr_numero) AS ano_numero,
				   r.cd_gerencia_destino,
				   d.nome AS gerencia_destino,
				   TO_CHAR(r.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome AS usuario_cadastro,
				   uc2.nome AS usuario_envio,
				   TO_CHAR(r.dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
				   uc3.nome AS usuario_recebido,
				   (SELECT COUNT(*)
				      FROM projetos.correspondencia_recebida_item
					 WHERE dt_exclusao IS NULL
					   AND cd_correspondencia_recebida = r.cd_correspondencia_recebida) AS tl_item,
				   r.cd_correspondencia_recebida_grupo,
				   g.ds_nome AS grupo
			  FROM projetos.correspondencia_recebida r
			  LEFT JOIN projetos.divisoes d
				ON d.codigo = r.cd_gerencia_destino
			  LEFT JOIN projetos.correspondencia_recebida_grupo g
				ON g.cd_correspondencia_recebida_grupo = r.cd_correspondencia_recebida_grupo
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = r.cd_usuario_inclusao 
			  LEFT JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = r.cd_usuario_envio 
			  LEFT JOIN projetos.usuarios_controledi uc3
			    ON uc3.codigo = r.cd_usuario_recebido 
			 WHERE r.cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']);
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_correspondencia_recebida']) == 0)
		{
			$cd_correspondencia_recebida = $this->db->get_new_id("projetos.correspondencia_recebida", "cd_correspondencia_recebida");
		
			$qr_sql = "
				INSERT INTO projetos.correspondencia_recebida
				     (
					   cd_correspondencia_recebida,
					   cd_gerencia_destino,
					   cd_correspondencia_recebida_grupo,
					   cd_usuario_inclusao
					 )
				VALUES 
					 (
					   ".intval($cd_correspondencia_recebida).",
					   ".(trim($args['cd_gerencia_destino']) != '' ? "'".trim($args['cd_gerencia_destino'])."'" : "DEFAULT")." ,
					   ".(trim($args['cd_correspondencia_recebida_grupo']) != '' ? "'".trim($args['cd_correspondencia_recebida_grupo'])."'" : "DEFAULT")." ,
					   ".intval($args['cd_usuario'])."
					 )";
		}
		else
		{
			$cd_correspondencia_recebida = intval($args['cd_correspondencia_recebida']);
			
			$qr_sql = "
				UPDATE projetos.correspondencia_recebida
				   SET cd_gerencia_destino               = ".(trim($args['cd_gerencia_destino']) != '' ? "'".trim($args['cd_gerencia_destino'])."'" : "DEFAULT")." ,
				       cd_correspondencia_recebida_grupo = ".(trim($args['cd_correspondencia_recebida_grupo']) != '' ? "'".trim($args['cd_correspondencia_recebida_grupo'])."'" : "DEFAULT")." 
				 WHERE cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_correspondencia_recebida;
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function tipos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_correspondencia_recebida_tipo AS value,
				   ds_correspondencia_recebida_tipo AS text
			  FROM projetos.correspondencia_recebida_tipo
			 WHERE dt_exclusao IS NULL";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_item(&$result, $args=array())
	{
		if(intval($args['cd_correspondencia_recebida_item']) == 0)
		{
			$qr_sql = "
				INSERT INTO projetos.correspondencia_recebida_item
				     (
						cd_correspondencia_recebida,
						dt_correspondencia,
						cd_correspondencia_recebida_tipo,
						origem,
						identificador,
						cd_usuario_inclusao
					 )
				VALUES
					 (
						".intval($args['cd_correspondencia_recebida']).",
						TO_TIMESTAMP('".$args['dt_correspondencia']." ".$args['hr_correspondencia']."','DD/MM/YYYY HH24:MI'),
						".intval($args['cd_correspondencia_recebida_tipo']).",
						'".trim($args['origem'])."',
						'".trim($args['identificador'])."',
						".intval($args['cd_usuario'])."
					 )";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.correspondencia_recebida_item
				   SET dt_correspondencia               = TO_TIMESTAMP('".$args['dt_correspondencia']." " .$args['hr_correspondencia']."','DD/MM/YYYY HH24:MI'),
				       cd_correspondencia_recebida_tipo = ".intval($args['cd_correspondencia_recebida_tipo']).",
					   origem                           = '".trim($args['origem'])."',
					   identificador                    = '".trim($args['identificador'])."'
				 WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function lista_itens(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ri.cd_correspondencia_recebida,
			       ri.cd_correspondencia_recebida_item,
				   TO_CHAR(ri.dt_correspondencia, 'DD/MM/YYYY HH24:MI') AS dt_correspondencia,
				   TO_CHAR(ri.dt_recebido, 'DD/MM/YYYY HH24:MI') AS dt_recebido,
				   TO_CHAR(ri.dt_recusa, 'DD/MM/YYYY HH24:MI:SS') AS dt_recusa,
				   TO_CHAR(ri.dt_recusa_ok, 'DD/MM/YYYY HH24:MI:SS') AS dt_recusa_ok,
				   TO_CHAR(ri.dt_origem, 'DD/MM/YYYY HH24:MI:SS') AS dt_origem,
				   ri.origem,
				   ds_correspondencia_recebida_tipo,
				   ri.identificador,
				   ri.cd_empresa,
				   ri.cd_registro_empregado,
				   ri.seq_dependencia,
				   p.nome,
				   ri.motivo_recusa,
				   uc.nome AS nome_recusa,
				   uc2.nome AS nome_recebido
			  FROM projetos.correspondencia_recebida_item ri
			  JOIN projetos.correspondencia_recebida_tipo rt
				ON rt.cd_correspondencia_recebida_tipo = ri.cd_correspondencia_recebida_tipo
			  LEFT JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ri.cd_usuario_recusa
			  LEFT JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = ri.cd_usuario_recebido
			  LEFT JOIN public.participantes p
				ON p.cd_registro_empregado = ri.cd_registro_empregado
			   AND p.cd_empresa            = ri.cd_empresa
			   AND p.seq_dependencia       = ri.seq_dependencia
			 WHERE ri.dt_exclusao IS NULL 
			   AND ri.cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function correspondencia_recebidas(&$result, $args=array())
	{
		$qr_sql = 
			"SELECT (COUNT(*) - SUM(CASE WHEN dt_recebido IS NOT NULL OR dt_recusa IS NOT NULL THEN 1
                                         ELSE 0 
                                    END)) AS tl
               FROM projetos.correspondencia_recebida_item 
              WHERE dt_exclusao IS NULL
			    AND cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']).";";
				
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_item(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_item
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_item(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_correspondencia_recebida_item,
				   TO_CHAR(dt_correspondencia, 'DD/MM/YYYY') AS dt_correspondencia,
				   TO_CHAR(dt_correspondencia, 'HH24:MI') AS hr_correspondencia,
				   origem,
				   cd_correspondencia_recebida_tipo,
				   identificador
			  FROM projetos.correspondencia_recebida_item
			 WHERE dt_exclusao IS NULL 
			   AND cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function enviar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida
			   SET cd_usuario_envio     = ".intval($args['cd_usuario']).",
				   dt_envio             = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function usuarios_envio(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT r.cd_usuario_envio AS value,
				   uc.nome AS text
			  FROM projetos.correspondencia_recebida r
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = r.cd_usuario_envio
			 WHERE r.dt_exclusao IS NULL
			 ORDER BY uc.nome, r.cd_usuario_envio;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function usuarios_recebido(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT r.cd_usuario_recebido AS value,
				   uc.nome AS text
			  FROM projetos.correspondencia_recebida r
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = r.cd_usuario_recebido
			 WHERE r.dt_exclusao IS NULL
			 ORDER BY uc.nome, r.cd_usuario_recebido;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_re(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_item
			   SET cd_empresa            = ".intval($args['cd_empresa']).",
			       cd_registro_empregado = ".intval($args['cd_registro_empregado']).",
				   seq_dependencia       = ".intval($args['seq_dependencia'])."
			 WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function receber_correspondencia(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_item
			   SET cd_usuario_recebido = ".intval($args['cd_usuario']).",
			       dt_recebido         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function receber_todas_correspondencia(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_item
			   SET cd_usuario_recebido = ".intval($args['cd_usuario']).",
			       dt_recebido         = CURRENT_TIMESTAMP
			 WHERE dt_recebido IS NULL
			   AND dt_exclusao IS NULL
			   AND dt_recusa   IS NULL
			   AND cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']).";
			
			UPDATE projetos.correspondencia_recebida
			   SET cd_usuario_recebido = ".intval($args['cd_usuario']).",
			       dt_recebido         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida = ".intval($args['cd_correspondencia_recebida']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function recusar_correspondecia(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_item
			   SET cd_usuario_recusa = ".intval($args['cd_usuario']).",
			       motivo_recusa     = ".str_escape($args['motivo_recusa']).",
			       dt_recusa         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function recusar_ok(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_item
			   SET cd_usuario_recusa_ok = ".intval($args['cd_usuario']).",
			       dt_recusa_ok         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function correspondecia_items_recusados(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencia_recebida_item
			   SET cd_usuario_origem = ".intval($args['cd_usuario']).",
			       dt_origem         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";
			 
			 INSERT INTO projetos.correspondencia_recebida_item
				     (
						cd_correspondencia_recebida,
						dt_correspondencia,
						cd_correspondencia_recebida_tipo,
						origem,
						identificador,
						cd_usuario_inclusao
					 )
			 SELECT ".intval($args['cd_correspondencia_recebida']).",
				    CURRENT_TIMESTAMP,
					cd_correspondencia_recebida_tipo,
					origem,
					identificador,
					".intval($args['cd_usuario'])."
			   FROM projetos.correspondencia_recebida_item
			  WHERE cd_correspondencia_recebida_item = ".intval($args['cd_correspondencia_recebida_item']).";";

		$result = $this->db->query($qr_sql);
	}
	
}

?>