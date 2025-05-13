<?php
class Cenario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$sql = "
			SELECT cd_edicao,
				   tit_capa, 
				   TO_CHAR(dt_edicao, 'DD/MM/YYYY HH24:MI') AS dt_edicao,
				   texto_capa
			  FROM projetos.edicao_cenario
			 WHERE UPPER(tit_capa) LIKE UPPER('%".trim($args["nome"])."%')
			   AND dt_exclusao     IS NULL
			   ".(trim($args['cd_edicao']) != '' ? "AND cd_edicao = ".intval($args['cd_edicao']) : '')."
			   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', dt_edicao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "").";";
			   
		$result = $this->db->query($sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ec.cd_edicao, 
				   TO_CHAR(ec.dt_edicao, 'DD/MM/YYYY HH24:MI') AS dt_edicao, 
				   TO_CHAR(ec.dt_exclusao, 'DD/MM/YYYY HH24:MI') AS dt_exclusao, 
				   ec.tit_capa, 
				   ec.texto_capa,
				   TO_CHAR(ec.dt_envio_email, 'DD/MM/YYYY HH24:MI') AS dt_envio_email, 
				   uc.nome
			  FROM projetos.edicao_cenario ec
			  LEFT JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ec.cd_usuario_envio_email
			 WHERE ec.cd_edicao = ".intval($args['cd_edicao']).";";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_edicao']) == 0)
		{
			$codigo = intval($this->db->get_new_id("projetos.edicao_cenario", "cd_edicao"));
			
			$qr_sql = " 
				INSERT INTO projetos.edicao_cenario
					 (
					   cd_edicao,
					   tit_capa,
					   texto_capa
					 )
				VALUES
					 (
					   ".$codigo.",
					   ".(trim($args['tit_capa'])   == "" ? "DEFAULT" : "'".$args['tit_capa']."'").",
					   ".(trim($args['texto_capa']) == "" ? "DEFAULT" : "'".$args['texto_capa']."'")."
					 );";				
		}
		else
		{
			$qr_sql = " 
				UPDATE projetos.edicao_cenario
				   SET tit_capa   = ".(trim($args['tit_capa'])   == "" ? "DEFAULT" : "'".$args['tit_capa']."'").",
					   texto_capa = ".(trim($args['texto_capa']) == "" ? "DEFAULT" : "'".$args['texto_capa']."'")."
				 WHERE cd_edicao = ".$args['cd_edicao'].";";	
				   
			$codigo = $args['cd_edicao'];
		}
		
		$this->db->query($qr_sql);	
		
		return $codigo;
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE projetos.edicao_cenario
			   SET dt_exclusao = CURRENT_TIMESTAMP
			 WHERE cd_edicao = ".$args['cd_edicao'].";";	
			
		$this->db->query($qr_sql);	
	}	
	
	function listar_conteudo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_cenario, 
			       titulo, 
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao, 
				   TO_CHAR(dt_exclusao, 'DD/MM/YYYY') AS dt_exclusao, 
				   cd_edicao
              FROM projetos.cenario
             WHERE cd_edicao = ".intval($args['cd_edicao']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function secao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
			       descricao as text
		      FROM listas
			 WHERE categoria = 'SCEN'  
			 ORDER BY descricao";
			
		$result = $this->db->query($qr_sql);
	}
	
	function divisao(&$result, $args=array())
	{
		$qr_sql = "
					SELECT d.codigo AS value,
						   d.nome AS text
					  FROM projetos.divisoes d
					 WHERE d.tipo = 'DIV'
					   AND (d.codigo IN (SELECT DISTINCT uc.divisao
					                       FROM projetos.usuarios_controledi uc
					                     WHERE uc.tipo IN ('D','G','N','P','U'))
					    OR 0 < (SELECT COUNT(*)
					              FROM projetos.cenario_areas ca
					             WHERE ca.cd_cenario = ".intval($args['cd_cenario'])."
					               AND ca.cd_divisao = d.codigo
					               AND ca.dt_exclusao IS NULL))
					 ORDER BY text	 
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_conteudo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_cenario,
			       cd_edicao,
				   titulo,
				   referencia,
				   fonte,
				   cd_secao,
				   conteudo,
				   link1,
				   link2,
				   link3,
				   link4,
				   pertinencia,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MM') AS data_inc,
				   TO_CHAR(dt_exclusao, 'DD/MM/YYYY') AS dt_exclusao,
				   TO_CHAR(dt_legal, 'DD/MM/YYYY') AS dt_legal,
				   TO_CHAR(dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
				   TO_CHAR(dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao
			  FROM projetos.cenario
			 WHERE cd_cenario = ".intval($args['cd_cenario'])."
			   AND cd_edicao  = ".intval($args['cd_edicao']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function areas_indicadas(&$result, $args=array())
	{
		$qr_sql = "
		    SELECT cd_divisao
			  FROM projetos.cenario_areas 	
			 WHERE cd_cenario  = ".intval($args['cd_cenario'])."			   
			   AND dt_exclusao IS NULL;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_conteudo(&$result, $args=array())
	{
		if(intval($args['cd_cenario']) > 0)
		{
			$qr_sql = "
				UPDATE projetos.cenario
				   SET titulo           = ".(trim($args['titulo']) != '' ? str_escape($args['titulo']) : "DEFAULT").",
					   conteudo         = ".(trim($args['conteudo']) != '' ? str_escape($args['conteudo']) : "DEFAULT").",
					   dt_exclusao      = ".(trim($args['dt_exclusao']) != '' ? "TO_DATE('".$args['dt_exclusao']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   cd_usuario       = ".intval($args['cd_usuario']).",
					   referencia       = ".(trim($args['referencia']) != '' ? str_escape($args['referencia']) : "DEFAULT").",
					   fonte            = ".(trim($args['fonte']) != '' ? str_escape($args['fonte']) : "DEFAULT").",
					   dt_prevista      = ".(trim($args['dt_prevista']) != '' ? "TO_DATE('".$args['dt_prevista']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   dt_legal         = ".(trim($args['dt_legal']) != '' ? "TO_DATE('".$args['dt_legal']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   dt_implementacao = ".(trim($args['dt_implementacao']) != '' ? "TO_DATE('".$args['dt_implementacao']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   pertinencia      = ".(trim($args['pertinencia']) != '' ? "'".intval($args['pertinencia'])."'" : "DEFAULT").",
					   link1            = ".(trim($args['link1']) != '' ? str_escape($args['link1']) : "DEFAULT").",
					   link2            = ".(trim($args['link2']) != '' ? str_escape($args['link2']) : "DEFAULT").",
					   link3            = ".(trim($args['link3']) != '' ? str_escape($args['link3']) : "DEFAULT").",
					   link4            = ".(trim($args['link4']) != '' ? str_escape($args['link4']) : "DEFAULT").",
					   cd_secao         = ".(trim($args['cd_secao']) != '' ? "'".trim($args['cd_secao'])."'" : "DEFAULT").",
					   indic_aa         = ".(in_array('GA', $args['divisao']) ? "'S'" : "DEFAULT").",  -- GA
					   indic_acs        = ".(in_array('GRI', $args['divisao']) ? "'S'" : "DEFAULT").", -- GRI
					   indic_aj         = ".(in_array('GJ', $args['divisao']) ? "'S'" : "DEFAULT").",  -- GJ
					   indic_da         = ".(in_array('GAD', $args['divisao']) ? "'S'" : "DEFAULT").", -- GAD
					   indic_dap        = ".(in_array('GAP', $args['divisao']) ? "'S'" : "DEFAULT").", -- GAP
					   indic_db         = ".(in_array('GB', $args['divisao']) ? "'S'" : "DEFAULT").",  -- GB
					   indic_dcg        = ".(in_array('GC', $args['divisao']) ? "'S'" : "DEFAULT").",  -- GC
					   indic_df         = ".(in_array('GF', $args['divisao']) ? "'S'" : "DEFAULT").",  -- GF
					   indic_di         = ".(in_array('GI', $args['divisao']) ? "'S'" : "DEFAULT").",  -- GI
					   indic_din        = ".(in_array('GIN', $args['divisao']) ? "'S'" : "DEFAULT").", -- GIN
					   indic_drh        = ".(in_array('GAD', $args['divisao']) ? "'S'" : "DEFAULT").", -- GAD
					   indic_sg         = ".(in_array('SG', $args['divisao']) ? "'S'" : "DEFAULT")."   -- SG
			     WHERE cd_cenario = ".intval($args['cd_cenario']).";";
					   
			$qr_sql .= "
				UPDATE projetos.cenario_areas
				   SET dt_exclusao = CURRENT_TIMESTAMP
				 WHERE cd_divisao NOT IN ('".implode("', '", $args['divisao'])."')
				   AND cd_cenario = ".intval($args['cd_cenario']).";";	
			
			foreach($args['divisao'] as $item)
			{			
				$qr_sql .= "
					INSERT INTO projetos.cenario_areas
						 (
						   cd_cenario,
						   cd_divisao
						 )
					SELECT ".intval($args['cd_cenario']).",
						   '".trim($item)."'
					 WHERE 0 = 
					     (
					     SELECT COUNT(*)
					       FROM projetos.cenario_areas
					      WHERE cd_cenario = ".intval($args['cd_cenario'])."
					        AND cd_divisao = '".trim($item)."'
					        AND dt_exclusao IS NULL
					     );";
			}	
			
			$cd_cenario = intval($args['cd_cenario']);
		}
		else
		{
			$qr_sql = "SELECT NEXTVAL('projetos.cenario_cd_cenario_seq') AS ins_id;";
			$result = $this->db->query($qr_sql);
			$arr = $result->row_array();
			$cd_cenario = $arr['ins_id'];
			
			$qr_sql = "
				INSERT INTO projetos.cenario 
				     (
					   cd_cenario,
					   cd_edicao,
					   titulo,
					   conteudo,
					   dt_exclusao,
					   cd_usuario,
					   referencia,
					   fonte,
					   dt_prevista,
					   dt_legal,
					   dt_implementacao,
					   pertinencia,
					   link1,
					   link2,
					   link3,
					   link4,
					   cd_secao,
					   indic_aa,  -- GA
					   indic_acs, -- GRI
					   indic_aj,  -- GJ
					   indic_da,  -- GAD
					   indic_dap, -- GAP
					   indic_db,  -- GB
					   indic_dcg, -- GC
					   indic_df,  -- GF
					   indic_di,  -- GI
					   indic_din, -- GIN
					   indic_drh, -- GAD
					   indic_sg   -- SG
					 )
				VALUES
				     (
					   ".intval($cd_cenario).",
					   ".intval($args['cd_edicao']).",
					   ".(trim($args['titulo']) != '' ? str_escape($args['titulo']) : "DEFAULT").",
					   ".(trim($args['conteudo']) != '' ? str_escape($args['conteudo']) : "DEFAULT").",
					   ".(trim($args['dt_exclusao']) != '' ? "TO_DATE('".$args['dt_exclusao']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".(trim($args['referencia']) != '' ? str_escape($args['referencia']) : "DEFAULT").",
					   ".(trim($args['fonte']) != '' ? str_escape($args['fonte']) : "DEFAULT").",
					   ".(trim($args['dt_prevista']) != '' ? "TO_DATE('".$args['dt_prevista']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['dt_legal']) != '' ? "TO_DATE('".$args['dt_legal']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['dt_implementacao']) != '' ? "TO_DATE('".$args['dt_implementacao']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['pertinencia']) != '' ? "'".intval($args['pertinencia'])."'" : "DEFAULT").",
					   ".(trim($args['link1']) != '' ? str_escape($args['link1']) : "DEFAULT").",
					   ".(trim($args['link2']) != '' ? str_escape($args['link2']) : "DEFAULT").",
					   ".(trim($args['link3']) != '' ? str_escape($args['link3']) : "DEFAULT").",
					   ".(trim($args['link4']) != '' ? str_escape($args['link4']) : "DEFAULT").",
					   ".(trim($args['cd_secao']) != '' ? "'".trim($args['cd_secao'])."'" : "DEFAULT").",
					   ".(in_array('GA', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GRI', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GJ', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GAD', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GAP', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GB', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GC', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GF', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GI', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GIN', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('GAD', $args['divisao']) ? "'S'" : "DEFAULT").",
					   ".(in_array('SG', $args['divisao']) ? "'S'" : "DEFAULT")."
					 );";	
			
			foreach($args['divisao'] as $item)
			{
				$qr_sql .= "
					INSERT INTO projetos.cenario_areas
						 (
						   cd_cenario,
						   cd_divisao
						 )
					VALUES
						 (
						   ".intval($cd_cenario).",
						   '".trim($item)."'
						 );";
			}
		}
	
		$result = $this->db->query($qr_sql);
		
		return $cd_cenario;
	}
	
	function excluir_conteudo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.cenario
			   SET dt_exclusao = CURRENT_TIMESTAMP
			 WHERE cd_cenario = ".intval($args['cd_cenario'])."
			   AND cd_edicao  = ".intval($args['cd_edicao']).";";	
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_cenario_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.cenario_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.cd_cenario = ".intval($args['cd_cenario'])."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.cenario_anexo
			     (
					cd_cenario,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_cenario']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.cenario_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_cenario_anexo = ".intval($args['cd_cenario_anexo']).";";
		$this->db->query($qr_sql);
	}
	
	function cenario(&$result, $args=array())
	{
		$qr_sql = "
			SELECT titulo,
			       cd_cenario,
				   referencia
			  FROM projetos.cenario
			 WHERE cd_cenario NOT IN (SELECT cd_cenario FROM projetos.cenario WHERE dt_exclusao > '2000-01-01') 
			   AND cd_secao = 'LGIN' 
			   AND cd_edicao = ".intval($args['cd_edicao'])."
			 ORDER BY cd_cenario DESC;";
		
		$result = $this->db->query($qr_sql);	
	}
	
	function usuarios(&$result, $args=array())
	{
		$qr_sql = "
			SELECT usuario, 
				   nome
		      FROM projetos.usuarios_controledi 
			 WHERE tipo in ('U', 'N', 'G', 'D')
			 ORDER by divisao;";
		$result = $this->db->query($qr_sql);
	}
	
	function envia_email(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.envia_emails
			     (
				   dt_envio,
				   de,
				   para,
				   cc,
				   cco,
				   assunto,
				   texto,
				   cd_evento
				 )
			VALUES
			     (
				   CURRENT_TIMESTAMP,
				   'Cenário Legal',
				   '".trim($args['para'])."',
				   '',
				   '',
				   '".trim($args['assunto'])."',
				   '".trim($args['mensagem'])."',
				   140
				 );";
		$result = $this->db->query($qr_sql);	
	}
	
	function edicao_envia_email(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.edicao_cenario
			   SET dt_envio_email = CURRENT_TIMESTAMP,
			       cd_usuario_envio_email = ".intval($args['cd_usuario'])."
		     WHERE cd_edicao = ".intval($args['cd_edicao']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function usuario_divisao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo, 
			       usuario, 
				   guerra, 
				   divisao 
		      FROM projetos.usuarios_controledi 
			 WHERE divisao IN (".$args['areas_indicadas'].") 
			   AND indic_03 = '*' 
			   AND tipo <> 'X';";
			   
		$result = $this->db->query($qr_sql);	
	}
	
	function max_atividade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT max(numero) AS cd_ativ 
			  FROM projetos.atividades;";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function nova_atividade(&$result, $args=array())
	{
		$numero = intval($this->db->get_new_id("projetos.atividades", "numero"));
	
		$qr_sql = "
			INSERT INTO projetos.atividades
			     (
				   numero,
				   tipo,
				   dt_cad,
				   descricao,
				   area,
				   divisao,
				   status_atual,
				   tipo_solicitacao,
				   titulo,
				   cod_solicitante,
				   cod_atendente,
				   cd_cenario
				 )
			VALUES
			     (
				   ".intval($numero).",
				   'L',
				   CURRENT_TIMESTAMP,
				   '".$args['descricao']."',
				   (SELECT divisao FROM projetos.usuarios_controledi WHERE codigo = ".$args['codigo']."),
				   (SELECT divisao FROM projetos.usuarios_controledi WHERE codigo = 98),
				   'AIGC',
				   'VP',
				   'Verificação de Procedência',
				   98,
				   ".intval($args['codigo']).",
				   ".intval($args['cd_cenario'])."
				 );";
			
		$result = $this->db->query($qr_sql);
		
		return $numero;
	}
	
	function atividade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.numero,
			       a.descricao AS descati, 
				   u1.usuario AS solicitante, 
				   u1.nome AS nomesolic,
				   u2.usuario AS atendente, 
				   u2.nome AS nomeatend, 
				   a.status_atual,
				   u1.formato_mensagem AS fmens_solic, 
				   u1.e_mail_alternativo AS emailalt_solic,
				   u2.formato_mensagem AS fmens_atend, 
				   u2.e_mail_alternativo AS emailalt_atend
			  FROM projetos.atividades a
			  JOIN projetos.usuarios_controledi u1
			    ON u1.codigo = a.cod_solicitante
			  JOIN projetos.usuarios_controledi u2
			    ON u2.codigo = a.cod_atendente
		     WHERE a.numero = ".intval($args['numero'])."";
			
		$result = $this->db->query($qr_sql);
	}
	
	function ultima_edicao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_edicao, 
			       tit_capa,
				   texto_capa
			  FROM projetos.edicao_cenario
			 WHERE cd_edicao IN (SELECT MAX(cd_edicao) FROM projetos.edicao_cenario WHERE dt_exclusao IS NULL)";
			
		$result = $this->db->query($qr_sql);
	}
	
	function ponto_vista(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_cenario, 
			       titulo, 
				   conteudo
              FROM projetos.cenario
             WHERE cd_edicao = ".intval($args['cd_edicao'])." 
			   and cd_secao = 'PVST';";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function max_cenario(&$result, $args=array())
	{
		$qr_sql = "
			SELECT MAX(cd_cenario) AS cd_cenario
			  FROM projetos.cenario
			 WHERE cd_edicao  = ".intval($args['cd_edicao'])."
			   AND cd_secao = 'LGIN'
			   AND dt_exclusao IS NULL;";
		$result = $this->db->query($qr_sql);
	}
	
	function agenda(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_cenario, 
			       titulo, 
				   conteudo
			  FROM projetos.cenario
			 WHERE cd_edicao = ".intval($args['cd_edicao'])."
			   AND cd_secao = 'AGEN'";
			
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_conteudo_lgin(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_cenario,
			       cd_edicao,
				   titulo,
				   referencia,
				   fonte,
				   cd_secao,
				   conteudo,
				   link1,
				   link2,
				   link3,
				   link4,
				   pertinencia,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MM') AS data_inc,
				   TO_CHAR(dt_exclusao, 'DD/MM/YYYY') AS dt_exclusao,
				   TO_CHAR(dt_legal, 'DD/MM/YYYY') AS dt_legal,
				   TO_CHAR(dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
				   TO_CHAR(dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao
			  FROM projetos.cenario
			 WHERE cd_secao = 'LGIN'
			   AND cd_cenario = ".intval($args['cd_cenario'])."
			   AND cd_edicao  = ".intval($args['cd_edicao']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function edicoes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_edicao, 
			       tit_capa
			  FROM projetos.edicao_cenario
		     WHERE dt_edicao >= DATE_TRUNC('month', CURRENT_DATE - '1 year'::interval)
			 ORDER BY cd_edicao desc";
			
		$result = $this->db->query($qr_sql);
	}

}
?>