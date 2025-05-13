<?php
class Conteudo_site_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT 	cs.cd_materia,
					        cs.cd_site,
							cs.cd_versao,					
				            cs.titulo,
			                cs.cd_secao,
							l.descricao AS ds_secao,
			                cs.ordem,
			                TO_CHAR(cs.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			                TO_CHAR(cs.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
			                TO_CHAR(cs.dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
			                l.valor,
			                cs.cd_versao,
			                cs.cd_site
			           FROM projetos.conteudo_site cs
					   JOIN public.listas l
					     ON l.codigo    = cs.cd_secao
			            AND l.categoria = 'SSIT' 
						AND l.tipo      <> 'A' -- SECOES DO AUTOATENDIMENTO
			          WHERE cs.cd_site   = ".intval($args['cd_site'])."
			            AND cs.cd_versao = ".intval($args['cd_versao'])."
						".(trim($args["cd_secao"]) != "" ? "AND cs.cd_secao = '".trim($args["cd_secao"]) ."'": "")."
						".(trim($args["fl_excluido"]) == "S" ? "AND cs.dt_exclusao IS NOT NULL": "")."
						".(trim($args["fl_excluido"]) == "N" ? "AND cs.dt_exclusao IS NULL": "")."
					  ORDER BY ds_secao,
					           cs.ordem
		          ";

		$result = $this->db->query($qr_sql);	

		#echo "<pre>$qr_sql</pre>";#exit;		
	}
	
	function pagina( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT 	cs.cd_materia,
					        cs.cd_site,
							cs.cd_versao,
			                cs.cd_secao,
			                cs.ordem AS nr_ordem,
			                TO_CHAR(cs.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			                TO_CHAR(cs.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
			                TO_CHAR(cs.dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
							CASE WHEN cs.dt_exclusao IS NULL THEN 'N' ELSE 'S' END AS fl_excluido,
			                cs.titulo AS ds_titulo,
							cs.conteudo AS conteudo_pagina,
			                cs.item_menu AS ds_item_menu
			           FROM projetos.conteudo_site cs
			          WHERE cs.cd_site    = ".intval($args['cd_site'])."
			            AND cs.cd_versao  = ".intval($args['cd_versao'])."
			            AND cs.cd_materia = ".intval($args['cd_materia'])."
		          ";

		$result = $this->db->query($qr_sql);		
	}	
	
	function salvar(&$result, $args=array())
	{
		$retorno = array(0,0);
		
		if((intval($args['cd_site']) > 0) and (intval($args['cd_versao']) > 0) and (intval($args['cd_materia']) > 0))
		{
			##UPDATE
			$qr_sql = " 
						UPDATE projetos.conteudo_site
						   SET titulo    = ".(trim($args['ds_titulo']) == "" ? "DEFAULT" : "'".$args['ds_titulo']."'").",
						       item_menu = ".(trim($args['ds_item_menu']) == "" ? "DEFAULT" : "'".$args['ds_item_menu']."'").",
						       cd_secao  = ".(trim($args['cd_secao']) == "" ? "DEFAULT" : "'".$args['cd_secao']."'").",
						       conteudo  = ".(trim($args['conteudo_pagina']) == "" ? "DEFAULT" : "'".$args['conteudo_pagina']."'").",
						       ordem     = ".(intval($args['nr_ordem']) == 0 ? "DEFAULT" : intval($args['nr_ordem'])).",
							   dt_alteracao           = CURRENT_TIMESTAMP,
							   cd_usuario_atualizacao = ".intval($args["cd_usuario"]).",							   
							   ".(trim($args["fl_excluido"]) == "S" ? "dt_exclusao = CURRENT_TIMESTAMP, cd_usuario_exclusao = ".intval($args["cd_usuario"]) : "dt_exclusao = NULL, cd_usuario_exclusao = NULL")."
						 WHERE cd_site    = ".intval($args['cd_site'])."
					       AND cd_versao  = ".intval($args['cd_versao'])."
					       AND cd_materia = ".intval($args['cd_materia'])."
					  ";		
			$this->db->query($qr_sql);
			$retorno = array(intval($args['cd_site']),intval($args['cd_versao']),intval($args['cd_materia']));	
		}
		else
		{
			##INSERT
			$qr_sql = " 
						INSERT INTO projetos.conteudo_site
						     (
							   cd_materia, 
							   cd_site,
							   cd_versao,							   
							   titulo, 
							   item_menu, 
							   cd_secao, 
                               conteudo,
							   ordem,
							   cd_usuario,    							   
							   cd_usuario_atualizacao,    
							   dt_exclusao,
							   cd_usuario_exclusao							   
							 )
                        VALUES 
						     (
							   (COALESCE((SELECT MAX(cd_materia)
						                    FROM projetos.conteudo_site
						                   WHERE cd_site   = ".intval($args['cd_site'])."
					                         AND cd_versao = ".intval($args['cd_versao'])."),0) + 1),
							   ".intval($args['cd_site']).",
							   ".intval($args['cd_versao']).",
							   ".(trim($args['ds_titulo']) == "" ? "DEFAULT" : "'".$args['ds_titulo']."'").",
							   ".(trim($args['ds_item_menu']) == "" ? "DEFAULT" : "'".$args['ds_item_menu']."'").",
							   ".(trim($args['cd_secao']) == "" ? "DEFAULT" : "'".$args['cd_secao']."'").",
							   ".(trim($args['conteudo_pagina']) == "" ? "DEFAULT" : "'".$args['conteudo_pagina']."'").",
							   ".(intval($args['nr_ordem']) == 0 ? "DEFAULT" : intval($args['nr_ordem'])).",
							   ".intval($args["cd_usuario"]).",
							   ".intval($args["cd_usuario"]).",
							   ".(trim($args["fl_excluido"]) == "S" ? "CURRENT_TIMESTAMP, ".intval($args["cd_usuario"]) : "NULL, NULL")."
							 );			
					  ";
			$this->db->query($qr_sql);	
			
			$qr_id = "
						SELECT MAX(cd_materia) AS cd_materia
						  FROM projetos.conteudo_site
						 WHERE cd_site    = ".intval($args['cd_site'])."
					       AND cd_versao  = ".intval($args['cd_versao'])."
						   AND cd_usuario = ".intval($args["cd_usuario"])."
			          ";
			$ob_resul = $this->db->query($qr_id);
			$ar_reg = $ob_resul->row_array();			
			
			#echo "<pre>".print_r($ar_reg,true)."</pre>"; #exit;
			
			$retorno = array(intval($args['cd_site']),intval($args['cd_versao']),intval($ar_reg['cd_materia']));			
		}
		
		#echo "<pre>$qr_sql</pre>";exit;
		
		return $retorno;
	}
	
	function secaoCombo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT DISTINCT cs.cd_secao AS value,
						   l.descricao AS text
					  FROM projetos.conteudo_site cs					   
					  JOIN public.listas l
						ON l.codigo    = cs.cd_secao
					   AND l.categoria = 'SSIT' 
					   AND l.tipo      <> 'A' -- SECOES DO AUTOATENDIMENTO			          
					 WHERE cs.cd_site   = ".intval($args['cd_site'])."
					   AND cs.cd_versao = ".intval($args['cd_versao'])."
					 ORDER BY text					 
		          ";
		$result = $this->db->query($qr_sql);
	}

	function historicoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_conteudo_site_jn, 
					       MD5(cd_conteudo_site_jn::TEXT) AS cd_jn,
					       TO_CHAR(dt_conteudo_site_jn,'DD/MM/YYYY HH24:MI:SS') AS dt_jn, 
						   tp_conteudo_site_jn, 
						   cd_secao,
						   cd_materia,
						   dt_alteracao, 
						   cd_usuario_atualizacao, 
						   funcoes.get_usuario_nome(cd_usuario_atualizacao) AS usuario_atualizacao,
						   dt_exclusao, 
						   cd_usuario_exclusao
					  FROM projetos.conteudo_site_jn
					 WHERE cd_site    = ".intval($args['cd_site'])."
					   AND cd_versao  = ".intval($args['cd_versao'])."
					   AND cd_materia = ".intval($args['cd_materia'])."
					   ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', dt_conteudo_site_jn) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
					 ORDER BY dt_conteudo_site_jn DESC
		          ";
		#echo "<PRE>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}	
}
?>