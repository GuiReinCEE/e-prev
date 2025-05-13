<?php
class Familia_previdencia_delegacia_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT d.cd_delegacia, 
					       d.nome,
						   d.endereco,  
						   d.cidade,
						   d.uf,
						   d.telefone,
						   d.email
                      FROM familia_previdencia.delegacia d
					 WHERE d.dt_exclusao IS NULL
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	
	

	function delegacia(&$result, $args=array())
	{
		$qr_sql = "
					SELECT d.cd_delegacia, 
					       d.nome,
						   d.endereco,  
						   d.cidade,
						   d.uf,
						   d.telefone,
						   d.email,
						   TO_CHAR(d.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(d.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
                      FROM familia_previdencia.delegacia d
					 WHERE d.cd_delegacia = ".intval($args['cd_delegacia'])."
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	

	function salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_delegacia']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE familia_previdencia.delegacia
						   SET nome     = ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   endereco = ".(trim($args['endereco']) == "" ? "DEFAULT" : "'".$args['endereco']."'").",
							   cidade   = ".(trim($args['cidade']) == "" ? "DEFAULT" : "'".$args['cidade']."'").",
							   uf       = ".(trim($args['uf']) == "" ? "DEFAULT" : "'".$args['uf']."'").",
							   telefone = ".(trim($args['telefone']) == "" ? "DEFAULT" : "'".$args['telefone']."'").",
							   email    = ".(trim($args['email']) == "" ? "DEFAULT" : "'".$args['email']."'")."
						 WHERE cd_delegacia = ".intval($args['cd_delegacia']).";
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_delegacia']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("familia_previdencia.delegacia", "cd_delegacia"));
			$qr_sql = " 
						INSERT INTO familia_previdencia.delegacia
						     (  
							   cd_delegacia, 
							   nome,
							   endereco,  
							   cidade,
							   uf,
							   telefone,
							   email
							 )
					    VALUES						
						     (
							   ".$new_id.",
							   ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   ".(trim($args['endereco']) == "" ? "DEFAULT" : "'".$args['endereco']."'").",
							   ".(trim($args['cidade']) == "" ? "DEFAULT" : "'".$args['cidade']."'").",
							   ".(trim($args['uf']) == "" ? "DEFAULT" : "'".$args['uf']."'").",
							   ".(trim($args['telefone']) == "" ? "DEFAULT" : "'".$args['telefone']."'").",
							   ".(trim($args['email']) == "" ? "DEFAULT" : "'".$args['email']."'")."
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
		if(intval($args['cd_delegacia']) > 0)
		{
			$qr_sql = " 
						UPDATE familia_previdencia.delegacia
						   SET dt_exclusao = CURRENT_TIMESTAMP
						 WHERE cd_delegacia = ".intval($args['cd_delegacia'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}		

}
?>