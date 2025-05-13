<?php
class Root_site_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT TO_CHAR(rs.cd_site,'FM00') AS cd_site,
						   TO_CHAR(rs.cd_versao,'FM00') AS cd_versao,
						   l.descricao AS descricao,
						   TO_CHAR(rs.dt_versao, 'DD/MM/YYYY') AS dt_versao,
						   rs.situacao,
						   rs.endereco,
						   rs.tit_capa
					  FROM projetos.root_site rs, 
						   public.listas l  
					 WHERE rs.situacao    = l.codigo 
					   AND l.categoria    = 'SITE' 
					   AND l.dt_exclusao  IS NULL
					   AND rs.dt_exclusao IS NULL
					 ORDER BY rs.cd_site, 
				 			  rs.cd_versao DESC 
		          ";

		$result = $this->db->query($qr_sql);
	}
	
	function site(&$result, $args=array())
	{
		$qr_sql = "
					SELECT rs.cd_site,
						   rs.cd_versao,
						   rs.endereco,
						   rs.tit_capa,
						   rs.texto_capa
					  FROM projetos.root_site rs
					 WHERE rs.cd_site   = ".intval($args['cd_site'])."
					   AND rs.cd_versao = ".intval($args['cd_versao'])."
		          ";

		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		$retorno = array(0,0);
		
		if(intval($args['cd_site']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE projetos.root_site
						   SET endereco               = ".(trim($args['endereco']) == "" ? "DEFAULT" : "'".$args['endereco']."'").",
						       tit_capa               = ".(trim($args['tit_capa']) == "" ? "DEFAULT" : "'".$args['tit_capa']."'").",
						       texto_capa             = ".(trim($args['texto_capa']) == "" ? "DEFAULT" : "'".$args['texto_capa']."'").",
							   dt_atualizacao         = CURRENT_TIMESTAMP,
							   cd_usuario_atualizacao = ".intval($args["cd_usuario"])."
						WHERE cd_site   = ".intval($args['cd_site'])."
					      AND cd_versao = ".intval($args['cd_versao'])."
					  ";		
			$this->db->query($qr_sql);
			$retorno = array(intval($args['cd_site']),intval($args['cd_versao']));	
		}
		else
		{
			##INSERT
		}
		
		#echo "<pre>$qr_sql</pre>";exit;
		
		return $retorno;
	}	
	
	function excluir(&$result, $args=array())
	{
		if(intval($args['cd_site']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.root_site
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
							   cd_usuario_exclusao = ".intval($args["cd_usuario"])."
						 WHERE cd_site   = ".intval($args['cd_site'])."
					       AND cd_versao = ".intval($args['cd_versao'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	

	
	function historicoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_root_site_jn, 
					       MD5(cd_root_site_jn::TEXT) AS cd_jn,
					       TO_CHAR(dt_root_site_jn,'DD/MM/YYYY HH24:MI:SS') AS dt_jn, 
						   tp_root_site_jn, 
						   cd_site, 
						   cd_versao, 
						   dt_versao, 
						   tit_capa, 
						   texto_capa, 
						   dt_atualizacao, 
						   cd_usuario_atualizacao, 
						   funcoes.get_usuario_nome(cd_usuario_atualizacao) AS usuario_atualizacao,
						   dt_exclusao, 
						   cd_usuario_exclusao
					  FROM projetos.root_site_jn rs
					 WHERE cd_site   = ".intval($args['cd_site'])."
					   AND cd_versao = ".intval($args['cd_versao'])."
					   ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', dt_root_site_jn) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
					 ORDER BY dt_root_site_jn DESC
		          ";
		#echo "<PRE>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}	
}
?>