<?php
class intranet_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_link($cd_intranet)
	{
		$qr_sql = "
			SELECT cd_intranet,
			       cd_gerencia,
			       texto_link,
			       link
			  FROM projetos.intranet_link 
			 WHERE dt_exclusao IS NULL
			   AND cd_intranet = ".intval($cd_intranet)."
			 ORDER BY nr_ordem DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_menu($cd_gerencia, $cd_intranet = 0)
	{
		$qr_sql = "
			SELECT cd_intranet,
			       titulo
			  FROM projetos.intranet 
			 WHERE dt_exclusao IS NULL
			   AND cd_gerencia     = '".trim($cd_gerencia)."'
			   AND cd_intranet_pai = ".intval($cd_intranet)."
			 ORDER BY COALESCE(nr_ordem, 0) DESC, 
			       titulo;";

		if(intval($cd_intranet) == 0)
		{
			return $this->db->query($qr_sql)->row_array();
		}
		else
		{
			return $this->db->query($qr_sql)->result_array();
		}	
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT id.cd_intranet,
			       id.cd_intranet_pai,
				   (SELECT ipv.cd_intranet_pai FROM projetos.intranet ipv WHERE ipv.cd_intranet = id.cd_intranet_pai) AS cd_intranet_voltar,
				   id.cd_gerencia,
				   id.titulo,
				   COALESCE(id.nr_ordem,0) AS nr_ordem,
				   TO_CHAR(id.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   CASE WHEN id.dt_alteracao < (SELECT MAX(il.dt_inclusao) 
					                              FROM projetos.intranet_link il
					                             WHERE il.dt_exclusao             IS NULL
					                               AND COALESCE(il.cd_intranet,0) = id.cd_intranet)
						THEN (SELECT TO_CHAR(MAX(il.dt_inclusao) , 'DD/MM/YYYY HH24:MI') 
					            FROM projetos.intranet_link il
		                       WHERE il.dt_exclusao             IS NULL
				                 AND COALESCE(il.cd_intranet,0) = id.cd_intranet)
					    ELSE TO_CHAR(id.dt_alteracao, 'DD/MM/YYYY HH24:MI')
					END AS dt_alteracao,
					CASE WHEN id.dt_alteracao < (SELECT MAX(il.dt_inclusao) 
					                               FROM projetos.intranet_link il
					                              WHERE il.dt_exclusao             IS NULL
					                                AND COALESCE(il.cd_intranet,0) = id.cd_intranet)
						 THEN (SELECT MAX(uc2.nome)
					             FROM projetos.intranet_link il
								 LEFT JOIN projetos.usuarios_controledi uc2
								   ON uc2.codigo = cd_usuario_alteracao
		                        WHERE il.dt_exclusao             IS NULL
				                  AND COALESCE(il.cd_intranet,0) = id.cd_intranet
								  AND il.dt_inclusao = (SELECT MAX(il.dt_inclusao) 
					                                      FROM projetos.intranet_link il
					                                     WHERE il.dt_exclusao             IS NULL
					                                       AND COALESCE(il.cd_intranet,0) = id.cd_intranet))
					     ELSE uc.nome
					 END AS usuario_alteracao
			  FROM projetos.intranet id
			  LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = id.cd_usuario_alteracao			  
			 WHERE id.cd_gerencia = '".trim($args['cd_gerencia'])."'
			   AND id.dt_exclusao IS NULL
			   AND COALESCE(id.cd_intranet_pai,0) = ".(intval($args['cd_intranet']) > 0 ? intval($args['cd_intranet']) : " (SELECT ip.cd_intranet
																															  FROM projetos.intranet ip
																															 WHERE ip.dt_exclusao IS NULL
																															   AND ip.cd_gerencia     = '".trim($args['cd_gerencia'])."'
																															   AND COALESCE(ip.cd_intranet_pai,0) = 0
																															 LIMIT 1)")."
			 ORDER BY COALESCE(id.nr_ordem,0) DESC, titulo ASC";

		#echo "<PRE>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}
	
	function editar_ordem_principal( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.intranet
			   SET cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP,
				   nr_ordem             = ".intval($args['nr_ordem'])."
			 WHERE cd_intranet = ".intval($args['cd_intranet'])."";	

		$this->db->query($qr_sql);
	}
	
	function listar_subitem( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT id.cd_intranet,
				   id.cd_gerencia,
				   id.titulo,
				   TO_CHAR(id.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao
			  FROM projetos.intranet id
			 WHERE id.cd_gerencia                     = '".trim($args['cd_gerencia'])."'
			   AND id.dt_exclusao             IS NULL
			   AND COALESCE(id.cd_intranet_pai,0) = ".intval($args['cd_intranet_pai'])."
			 ORDER BY COALESCE(id.nr_ordem,0) DESC, titulo ASC";
		
		$result = $this->db->query($qr_sql);
	}	
	
	function itens_superior( &$result, $args=array() )
	{
		$qr_sql = "
					WITH RECURSIVE q AS (
							SELECT h, 
								   0 AS level, 
								   ARRAY[h.cd_intranet] AS breadcrumb
							  FROM projetos.intranet h
							 WHERE h.dt_exclusao IS NULL
							   AND h.cd_gerencia = '".trim($args['cd_gerencia'])."'
							   AND COALESCE(h.cd_intranet_pai,0) = 0
							 UNION ALL
							SELECT hi, 
								   q.level + 1 AS level, 
								   breadcrumb || cd_intranet
							  FROM q
							  JOIN projetos.intranet hi
								ON hi.cd_intranet_pai = (q.h).cd_intranet
							 WHERE hi.dt_exclusao IS NULL
							   AND hi.cd_gerencia = '".trim($args['cd_gerencia'])."'
					)
					SELECT REPEAT('.....', level) || (q.h).titulo AS text,
						   (q.h).cd_intranet AS value
					  FROM q
					 WHERE (q.h).cd_intranet <> ".intval($args['cd_intranet'])."
					ORDER BY breadcrumb::TEXT		
                  ";		
		#echo $qr_sql; exit;		  
		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_intranet,
				   cd_intranet_pai,
				   titulo,
				   conteudo,
				   arquivo,
				   arquivo_nome,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   TO_CHAR(dt_exclusao, 'DD/MM/YYYY HH24:MI') AS dt_exclusao
			  FROM projetos.intranet
			 WHERE cd_gerencia = '".trim($args['cd_gerencia'])."'
			   AND cd_intranet = ".intval($args['cd_intranet'])."";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array()  )
	{
		if(intval($args['cd_intranet']) == 0)
		{
			$qr_sql = "
				INSERT INTO projetos.intranet 
					 (  
					   cd_intranet_pai, 
					   cd_gerencia,
					   titulo, 
					   conteudo, 
					   arquivo,
					   arquivo_nome,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao,
					   dt_inclusao, 
					   dt_alteracao,
					   nr_ordem
					 )	
				VALUES 
					 (
					   ".(trim($args['cd_intranet_pai']) != '' ? intval($args['cd_intranet_pai']) : "DEFAULT").", 
					   '".trim($args['cd_gerencia'])."',
					   ".str_escape(trim($args['titulo'])).", 
					   ".(trim($args['conteudo']) != '' ? "'".trim($args['conteudo'])."'" : "DEFAULT").", 
					   ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").", 
					   ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").", 
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario']).",
					   CURRENT_TIMESTAMP, 
					   CURRENT_TIMESTAMP,
					   (COALESCE((SELECT MAX(nr_ordem)
									FROM projetos.intranet
								   WHERE cd_gerencia = '".trim($args['cd_gerencia'])."'
									 AND dt_exclusao IS NULL),-1)+ 1 )
					 ); ";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.intranet 
		           SET cd_intranet_pai      = ".intval($args['cd_intranet_pai']).",
					   titulo               = '".trim($args['titulo'])."', 
		               conteudo             = ".(trim($args['conteudo']) != '' ? "'".trim($args['conteudo'])."'" : "DEFAULT").", 
					   arquivo              = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").", 
					   arquivo_nome         = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").", 
		               cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
		         WHERE cd_intranet = ".intval($args['cd_intranet'])." 
		     	   AND cd_gerencia = '".trim($args['cd_gerencia'])."';";
		}

		$this->db->query($qr_sql);
	}
	
	function excluir( &$result, $args=array()  )
	{
		$qr_sql = "
			UPDATE projetos.intranet 
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_intranet = ".intval($args['cd_intranet'])." 
			   AND cd_gerencia = '".trim($args['cd_gerencia'])."';";
		
		$this->db->query($qr_sql);
	}
	
	function carrega_intranet_link(&$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_intranet_link,
				   texto_link,
				   link,
				   nr_ordem,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao
			  FROM projetos.intranet_link
			 WHERE cd_intranet = ".intval($args['cd_intranet'])."
			   AND dt_exclusao IS NULL
			 ORDER BY COALESCE(nr_ordem,0) DESC";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_link( &$result, $args=array() )
	{
		if(intval($args['cd_intranet_link']) > 0)
		{
			$qr_sql = "
				UPDATE projetos.intranet_link
				   SET texto_link           = ".(trim($args['texto_link']) != '' ? "'".trim($args['texto_link'])."'" : "DEFAULT").", 
				       nr_ordem             = ".intval($args['nr_ordem']).",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_intranet_link = ".intval($args['cd_intranet_link'])."";	
		}
		else
		{
			$qr_sql = "
				INSERT INTO projetos.intranet_link
				     (
					   cd_intranet,
					   cd_gerencia,
					   texto_link,
					   link,
					   nr_ordem,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao,
					   dt_inclusao, 
					   dt_alteracao
					 )
			    VALUES
					 (
					   ".intval($args['cd_intranet']).",
					   '".trim($args['cd_gerencia'])."',
					   ".(trim($args['texto_link']) != '' ? "'".trim($args['texto_link'])."'" : "DEFAULT").", 
					   ".(trim($args['link']) != '' ? "'".str_replace('///','//',str_replace('\\','/',$args['link']))."'" : "DEFAULT").", 
					   (COALESCE((SELECT MAX(nr_ordem)
									FROM projetos.intranet_link
								   WHERE cd_intranet = ".intval($args['cd_intranet'])."
									 AND dt_exclusao IS NULL),-1)+ 1 ), 
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario']).",
					   CURRENT_TIMESTAMP, 
					   CURRENT_TIMESTAMP
					 )
			   ";
		}

		$this->db->query($qr_sql);
	}
	
	function excluir_link( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.intranet_link
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_intranet_link = ".intval($args['cd_intranet_link'])."";	
			 
		$this->db->query($qr_sql);
	}
	
	function editar_ordem( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE projetos.intranet_link
			   SET cd_usuario_alteracao = ".intval($args['cd_usuario']).",
			       dt_alteracao         = CURRENT_TIMESTAMP,
				   nr_ordem             = ".intval($args['nr_ordem'])."
			 WHERE cd_intranet_link = ".intval($args['cd_intranet_link'])."";	

		$this->db->query($qr_sql);
	}
	
	function gerencia_principal( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_intranet
					  FROM projetos.intranet i
					 WHERE i.dt_exclusao IS NULL   
					   AND i.cd_gerencia = '".trim($args['cd_gerencia'])."'
					   AND i.cd_intranet_pai = (SELECT ii.cd_intranet
					                              FROM projetos.intranet ii
					                             WHERE ii.dt_exclusao IS NULL   
					                               AND ii.cd_gerencia = i.cd_gerencia
					                               AND COALESCE(ii.cd_intranet_pai,0) = 0
					                             ORDER BY COALESCE(ii.nr_ordem,0) DESC
					                             LIMIT 1)
                     ORDER BY COALESCE(i.nr_ordem,0) DESC, i.titulo ASC
			      ";
		$result = $this->db->query($qr_sql);
	}
	
	function gerencia_titulo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_intranet,
				   COALESCE(cd_intranet_pai,0) AS cd_intranet_pai,
				   cd_gerencia, 
				   titulo, 
				   conteudo,  
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MM') AS dt_inclusao,
				   arquivo_nome,
				   CASE WHEN LOWER(SUBSTRING(arquivo_nome,STRPOS(arquivo_nome, '.'), LENGTH(arquivo_nome))) IN ('.jpg', '.jpeg', '.png', '.gif', '.bmp')
					    THEN 'IMG'
					    ELSE 'DOC'
					END AS tp_imagem
			  FROM projetos.intranet
		     WHERE cd_intranet = ".intval($args['cd_intranet'])."	   
	    	   AND dt_exclusao IS NULL ";
			
		$result = $this->db->query($qr_sql);
	}
	
	function gerencia_pag_internas( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_intranet, 
				   titulo 
			  FROM projetos.intranet 
			 WHERE dt_exclusao IS NULL   
			   AND cd_gerencia = '".trim($args['cd_gerencia'])."'
			   AND COALESCE(cd_intranet_pai,0) = ".intval($args['cd_intranet'])."
			 ORDER BY COALESCE(nr_ordem,0) DESC, titulo ASC ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function gerencia_menu( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_intranet, 
				   titulo 
			  FROM projetos.intranet 
			 WHERE dt_exclusao IS NULL   
			   AND cd_gerencia  = '".trim($args['cd_gerencia'])."'
			   AND COALESCE(cd_intranet_pai,0) = 0
			 ORDER BY COALESCE(nr_ordem,0) DESC, titulo ASC";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function setItemPai( &$result, $args=array()  )
	{
		$qr_sql = "
					UPDATE projetos.intranet 
					   SET cd_intranet_pai = ".intval($args['cd_intranet_pai'])."
					 WHERE cd_intranet = ".intval($args['cd_intranet'])." 
			      ";
		
		$this->db->query($qr_sql);
	}	
}
?>