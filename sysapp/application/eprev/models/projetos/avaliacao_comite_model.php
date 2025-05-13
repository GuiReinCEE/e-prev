<?php
class Avaliacao_comite_model extends Model
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
			 ORDER BY nome ASC;";
		
		$result = $this->db->query($qr_sql);
	}	
	
	function ano(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT dt_periodo AS value,
				   dt_periodo AS text
			  FROM projetos.avaliacao_capa
			 ORDER BY dt_periodo DESC;";
		
		$result = $this->db->query($qr_sql);
	}	
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT c.cd_avaliacao_capa,
				   uc.nome,
				   c.avaliador_responsavel_comite,
				   c.status AS fl_status,
				   CASE WHEN c.tipo_promocao = 'H' THEN 'Horizontal'
						WHEN c.tipo_promocao = 'V' THEN 'Vertical'
				   END AS tipo_promocao,
				   CASE WHEN c.tipo_promocao = 'H' THEN 'label label-success'
				        ELSE 'label label-info'
				   END AS cor_tipo_promocao,
				   CASE WHEN c.dt_publicacao IS NOT NULL THEN 'label'
				        WHEN c.status = 'A' THEN 'label label-inverse'
						WHEN c.status = 'F' THEN 'label label-info'
						WHEN c.status = 'S' THEN 'label label-warning'
						WHEN c.status = 'E' THEN 'label label-important'
						WHEN c.status = 'C' THEN 'label label-success'
				   END AS cor_status,
				   CASE WHEN c.dt_publicacao IS NOT NULL THEN 'Avaliaчуo Finalizada'
				        WHEN c.status = 'A' THEN 'Avaliaчуo Iniciada'
						WHEN c.status = 'F' THEN 'Encaminhado ao Superior'
						WHEN c.status = 'S' THEN 'Encaminhado ao Comitъ'
						WHEN c.status = 'E' THEN 'Aguardando nomeaчуo do Comitъ'
						WHEN c.status = 'C' THEN 'Aprovado pelo Comitъ'
				   END AS status,
				   (SELECT COUNT(*)
				      FROM projetos.avaliacao_comite ac
					 WHERE ac.dt_exclusao IS NULL
					   AND ac.fl_responsavel = 'S'
					   AND ac.cd_avaliacao_capa = c.cd_avaliacao_capa) AS tl_responsavel
			  FROM projetos.avaliacao_capa c
			  JOIN projetos.usuarios_controledi uc
				ON c.cd_usuario_avaliado = uc.codigo
			 WHERE 1 = 1
			   ".(trim($args['cd_usuario_gerencia']) != '' ? "AND uc.divisao = '".trim($args['cd_usuario_gerencia'])."'" : '')."
			   ".(trim($args['cd_usuario']) != '' ? "AND c.cd_usuario_avaliado = '".trim($args['cd_usuario'])."'" : '')."
			   ".(trim($args['ano']) != '' ? "AND c.dt_periodo = ".trim($args['ano']) : '')."
			   ".(trim($args['fl_status']) == 'N' ? "AND c.dt_publicacao IS NOT NULL" : '')."
			   ".(((trim($args["fl_status"]) != "") and (trim($args["fl_status"]) != "N")) ? " AND c.status = '".trim($args['fl_status'])."' AND c.dt_publicacao IS NULL" : "")."
			   ".(trim($args['fl_tipo']) != '' ? "AND c.tipo_promocao = '".trim($args['fl_tipo'])."'" : '')."
			 ORDER BY uc.nome ASC;";
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT c.cd_avaliacao_capa,
				   uc.nome,
				   c.avaliador_responsavel_comite,
				   c.status AS fl_status,
				   CASE WHEN c.tipo_promocao = 'H' THEN 'Horizontal'
						WHEN c.tipo_promocao = 'V' THEN 'Vertical'
				   END AS tipo_promocao,
				   CASE WHEN c.tipo_promocao = 'H' THEN 'label label-success'
				        ELSE 'label label-info'
				   END AS cor_tipo_promocao,
				   CASE WHEN c.dt_publicacao IS NOT NULL THEN 'label'
				        WHEN c.status = 'A' THEN 'label label-inverse'
						WHEN c.status = 'F' THEN 'label label-info'
						WHEN c.status = 'S' THEN 'label label-warning'
						WHEN c.status = 'E' THEN 'label label-important'
						WHEN c.status = 'C' THEN 'label label-success'
				   END AS cor_status,
				   CASE WHEN c.dt_publicacao IS NOT NULL THEN 'Avaliaчуo Finalizada'
				        WHEN c.status = 'A' THEN 'Avaliaчуo Iniciada'
						WHEN c.status = 'F' THEN 'Encaminhado ao Superior'
						WHEN c.status = 'S' THEN 'Encaminhado ao Comitъ'
						WHEN c.status = 'E' THEN 'Aguardando nomeaчуo do Comitъ'
						WHEN c.status = 'C' THEN 'Aprovado pelo Comitъ'
				   END AS status,
				   CASE WHEN (SELECT COUNT(*)
								 FROM projetos.avaliacao_comite ac
								WHERE ac.dt_exclusao IS NULL
								  AND ac.fl_responsavel = 'S'
								  AND ac.cd_avaliacao_capa = c.cd_avaliacao_capa) > 0 OR c.avaliador_responsavel_comite = 'S' THEN 1
					   ELSE   0
				    END AS tl_responsavel
			  FROM projetos.avaliacao_capa c
			  JOIN projetos.usuarios_controledi uc
				ON c.cd_usuario_avaliado = uc.codigo
			 WHERE c.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function comite(&$result, $args=array())
	{
		$qr_sql = "
			SELECT c.fl_responsavel,
			       c.cd_avaliacao_comite,
                   uc.nome,
				   CASE WHEN 1 = (SELECT COUNT(*) 
									FROM projetos.avaliacao a
								   WHERE a.cd_avaliacao_capa = c.cd_avaliacao_capa 
									 AND a.cd_usuario_avaliador = c.cd_usuario_avaliador
									 AND a.tipo = 'C' 
									 AND a.dt_conclusao IS NOT NULL) THEN 'S'
						ELSE 'N'
				   END AS fl_avaliou
              FROM projetos.avaliacao_comite c
              JOIN projetos.usuarios_controledi uc
                ON c.cd_usuario_avaliador = uc.codigo
             WHERE c.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
			   AND c.dt_exclusao IS NULL
			 ORDER BY uc.nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function avaliador(&$result, $args=array())
	{
		$qr_sql = "
			SELECT uc.nome
              FROM projetos.avaliacao a
              JOIN projetos.usuarios_controledi uc
                ON a.cd_usuario_avaliador = uc.codigo
             WHERE a.tipo = 'S' 
			   AND a.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function usuario_comite(&$result, $args=array())
	{
		$qr_sql = "
			SELECT uc.codigo AS value,
				   uc.nome AS text
			  FROM projetos.usuarios_controledi uc
			 WHERE uc.tipo NOT IN ('X', 'T')
			   AND uc.codigo != (SELECT c.cd_usuario_avaliado
			                       FROM projetos.avaliacao_capa c
								  WHERE c.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).")
			   AND uc.codigo != (SELECT a.cd_usuario_avaliador
			                       FROM projetos.avaliacao a
								  WHERE a.tipo = 'S'  
								    AND a.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).")
			   AND uc.codigo NOT IN (SELECT ac.cd_usuario_avaliador
			                           FROM projetos.avaliacao_comite ac
									  WHERE ac.dt_exclusao IS NULL
									    AND ac.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).")
			 ORDER BY uc.nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "";
	
		if(trim($args['fl_responsavel']) == 'S')
		{
			$qr_sql .= "
				UPDATE projetos.avaliacao_comite
				   SET fl_responsavel = 'N'
				 WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
				   AND dt_exclusao IS NULL;
				UPDATE projetos.avaliacao_capa
			       SET avaliador_responsavel_comite = 'N'
			     WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";";
		}
		
		$qr_sql .= "
			INSERT INTO projetos.avaliacao_comite
				 ( 
				   cd_usuario_avaliador,
				   fl_responsavel,
				   cd_avaliacao_capa
				 )
			VALUES
			     (
				   ".intval($args['cd_usuario_avaliador']).",
				   '".trim($args['fl_responsavel'])."',
				   ".intval($args['cd_avaliacao_capa'])."
				 )";
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.avaliacao_comite
			   SET dt_exclusao = CURRENT_TIMESTAMP
			 WHERE cd_avaliacao_comite = ".intval($args['cd_avaliacao_comite']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function alterar_responsavel(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.avaliacao_comite
			   SET fl_responsavel = 'N'
			 WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
			   AND dt_exclusao IS NULL;
			UPDATE projetos.avaliacao_comite
			   SET fl_responsavel = 'S'
			 WHERE cd_avaliacao_comite = ".intval($args['cd_avaliacao_comite']).";
			UPDATE projetos.avaliacao_capa
			   SET avaliador_responsavel_comite = 'N'
			 WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function alterar_responsavel_avaliador(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.avaliacao_comite
			   SET fl_responsavel = 'N'
			 WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
			   AND dt_exclusao IS NULL;
			UPDATE projetos.avaliacao_capa
			   SET avaliador_responsavel_comite = 'S'
			 WHERE cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function encaminhar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT projetos.avaliacao_comite_encaminha(".intval($args['cd_avaliacao_capa']).", ".intval($args['cd_usuario']).");
			SELECT projetos.avaliacao_comite_envia_email(".intval($args['cd_avaliacao_capa']).", ".intval($args['cd_usuario']).");";
			
		$result = $this->db->query($qr_sql);
	}
	
	function enviar_email(&$result, $args=array())
	{
		$qr_sql = "
			SELECT projetos.avaliacao_comite_envia_email(".intval($args['cd_avaliacao_capa']).", ".intval($args['cd_usuario']).");";
			
		$result = $this->db->query($qr_sql);
	}
}
?>