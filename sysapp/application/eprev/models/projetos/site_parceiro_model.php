<?php
class Site_parceiro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	
	function cadastro(&$result, $args=array())
	{
		$qr_sql = "
					SELECT sp.cd_site_parceiro, 
					       sp.nome, 
						   sp.url, 
						   sp.img_parceiro, 
						   TO_CHAR(sp.dt_libera,'DD/MM/YYYY HH24:MI') AS dt_libera, 
						   sp.cd_usuario_libera, 
						   TO_CHAR(sp.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao, 
						   sp.cd_usuario_inclusao, 
						   TO_CHAR(sp.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao, 
						   sp.cd_usuario_exclusao,
						   CASE WHEN sp.dt_libera IS NOT NULL
						        THEN 'S'
								ELSE 'N'
						   END AS fl_libera,
						   nr_ordem
					  FROM projetos.site_parceiro sp		
					 WHERE sp.cd_site_parceiro = ".intval($args['cd_site_parceiro'])."
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	

	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT sp.cd_site_parceiro, 
					       sp.nome, 
						   sp.url, 
						   sp.img_parceiro, 
						   TO_CHAR(sp.dt_libera,'DD/MM/YYYY HH24:MI') AS dt_libera, 
						   sp.cd_usuario_libera, 
						   TO_CHAR(sp.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao, 
						   sp.cd_usuario_inclusao, 
						   TO_CHAR(sp.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao, 
						   sp.cd_usuario_exclusao,
                           nr_ordem						   
					  FROM projetos.site_parceiro sp	
					 WHERE sp.dt_exclusao IS NULL
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_site_parceiro']) > 0)
		{
			#### UPDATE ####
			$qr_sql = " 
						UPDATE projetos.site_parceiro
						   SET nome              = ".(trim($args['nome']) == "" ? "DEFAULT" : "'".$args['nome']."'").",
						       url               = ".(trim($args['url']) == "" ? "DEFAULT" : "'".$args['url']."'").",
							   dt_libera         = ".(trim($args['fl_libera']) == "S" ? "CURRENT_TIMESTAMP" : "NULL").", 
							   cd_usuario_libera = ".(trim($args['fl_libera']) == "S" ? (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) : "NULL").",
							   nr_ordem          = ".(intval($args['nr_ordem']) == 0 ? "DEFAULT" : $args['nr_ordem'])."
						 WHERE cd_site_parceiro = ".intval($args['cd_site_parceiro'])."			
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_site_parceiro']);	
		}
		else
		{
			#### INSERT ####
			$new_id = intval($this->db->get_new_id("projetos.site_parceiro", "cd_site_parceiro"));
			$qr_sql = " 
						INSERT INTO projetos.site_parceiro
						     (
							   cd_site_parceiro, 
							   nome, 
							   url, 
							   dt_libera, 
							   cd_usuario_libera,
							   cd_usuario_inclusao,
							   nr_ordem
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['nome']) == "" ? "DEFAULT" : "'".$args['nome']."'").",
							   ".(trim($args['url']) == "" ? "DEFAULT" : "'".$args['url']."'").",							   
							   ".(trim($args['fl_libera']) == "S" ? "CURRENT_TIMESTAMP" : "NULL").", 							   
							   ".(trim($args['fl_libera']) == "S" ? (intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']) : "NULL").",							   
							   ".(intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']).",
							   ".(intval($args['nr_ordem']) == 0 ? "DEFAULT" : $args['nr_ordem'])."
							 );
					  ";
			$this->db->query($qr_sql);	
			$retorno = $new_id;			
		}
		
		#echo "<pre>$qr_sql</pre>";
		#exit;
		
		return $retorno;
	}

	function excluir(&$result, $args=array())
	{
		if(trim($args['cd_site_parceiro']) != "")
		{
			$qr_sql = " 
						UPDATE projetos.site_parceiro
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE MD5(CAST(cd_site_parceiro AS TEXT)) = '".$args['cd_site_parceiro']."'
					  ";			
			#echo "<PRE>$qr_sql</PRE>"; exit;
			
			$this->db->query($qr_sql);
		}
	}
	

}
?>