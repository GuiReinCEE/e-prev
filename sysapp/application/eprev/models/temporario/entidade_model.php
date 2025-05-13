<?php
class Entidade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function comboEntidade( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT cd_entidade AS value,
					       nome AS text
					  FROM temporario.entidade
					ORDER BY value
		          ";
		$result = $this->db->query($qr_sql);
	}

	function entidade(&$result, $args=array())
	{
		$qr_sql = "
					SELECT e.cd_entidade,
						   e.nome,
						   e.fl_escolha,

						  (SELECT COUNT(*)
							 FROM temporario.entidade_item_usuario eiu
							 JOIN temporario.entidade_item ei
							   ON ei.cd_entidade_item  = eiu.cd_entidade_item
							WHERE eiu.cd_entidade_item = ei.cd_entidade_item
							  AND eiu.dt_exclusao      IS NULL
							  AND eiu.cd_usuario       = ".intval($args['cd_usuario'])."
							  AND ei.cd_entidade       = e.cd_entidade)	AS fl_usuario					   
					  FROM temporario.entidade e
					 WHERE e.cd_entidade = ".intval($args['cd_entidade'])."
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ei.cd_entidade_item,
						   ei.descricao,
						   (SELECT cd_entidade_item_usuario
							  FROM temporario.entidade_item_usuario eiu
						     WHERE eiu.cd_entidade_item = ei.cd_entidade_item
							   AND eiu.cd_usuario       = ".intval($args['cd_usuario'])."
							   AND eiu.dt_exclusao      IS NULL) AS cd_entidade_item_usuario
					  FROM temporario.entidade_item ei
					  JOIN temporario.entidade e
					    ON e.cd_entidade = ei.cd_entidade
					 WHERE ei.cd_entidade = ".intval($args['cd_entidade'])."
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	

	function itemUsuario(&$result, $args=array())
	{
		$qr_sql = "
					SELECT eiu.cd_entidade_item,
					       eiu.cd_usuario,
						   uc.nome AS ds_usuario
					  FROM temporario.entidade_item_usuario eiu
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = eiu.cd_usuario
					 WHERE eiu.cd_entidade_item = ".intval($args['cd_entidade_item'])."
					   AND eiu.dt_exclusao IS NULL
					 ORDER BY ds_usuario
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}
	
	function incluiItemUsuario(&$result, $args=array())
	{
		if(intval($args['cd_entidade_item']) > 0)
		{
			$qr_sql = " 
						INSERT INTO temporario.entidade_item_usuario
						     (
                               cd_entidade_item, 
							   cd_usuario
							 )
                        VALUES 
						     (
							   ".intval($args['cd_entidade_item']).",
							   ".intval($args['cd_usuario'])."
							 );
					  ";			
			#echo "<PRE>$qr_sql</PRE>"; exit;
			
			$this->db->query($qr_sql);
		}
	}

	function excluirItemUsuario(&$result, $args=array())
	{
		if(intval($args['cd_entidade_item_usuario']) > 0)
		{
			$qr_sql = " 
						UPDATE temporario.entidade_item_usuario
						   SET dt_exclusao = CURRENT_TIMESTAMP
                         WHERE cd_entidade_item_usuario = ".intval($args['cd_entidade_item_usuario']).";
					  ";			
			#echo "<PRE>$qr_sql</PRE>"; exit;
			
			$this->db->query($qr_sql);
		}
	}	
	
	/*
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
	*/

}
?>