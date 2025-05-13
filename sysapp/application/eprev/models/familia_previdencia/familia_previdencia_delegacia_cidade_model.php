<?php
class Familia_previdencia_delegacia_cidade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function delegaciaCombo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT cd_delegacia AS value, 
					       nome AS text 
					  FROM familia_previdencia.delegacia 
					 WHERE dt_exclusao IS NULL
					 ORDER BY nome
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT dc.cd_delegacia_cidade, 
					       dc.nome, 
						   dc.cd_delegacia,
						   d.nome AS ds_delegacia
                      FROM familia_previdencia.delegacia_cidade dc
					  LEFT JOIN familia_previdencia.delegacia d
						ON d.cd_delegacia = dc.cd_delegacia
					 WHERE dc.dt_exclusao IS NULL
					 ".(intval($args['cd_delegacia']) > 0 ? "AND dc.cd_delegacia = ".intval($args['cd_delegacia']) : "")."
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	
	
	
	function cidade(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_delegacia_cidade, 
					       nome,
						   cd_delegacia,
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
                      FROM familia_previdencia.delegacia_cidade
					 WHERE cd_delegacia_cidade = ".intval($args['cd_delegacia_cidade'])."
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	

	function salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_delegacia_cidade']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE familia_previdencia.delegacia_cidade
						   SET nome         = ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   cd_delegacia = ".(intval($args['cd_delegacia']) == 0 ? "DEFAULT" : intval($args['cd_delegacia']))."
						 WHERE cd_delegacia_cidade = ".intval($args['cd_delegacia_cidade']).";
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_delegacia_cidade']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("familia_previdencia.delegacia_cidade", "cd_delegacia_cidade"));
			$qr_sql = " 
						INSERT INTO familia_previdencia.delegacia_cidade
						     (  
							   cd_delegacia_cidade, 
							   nome,
							   cd_delegacia
							 )
					    VALUES						
						     (
							   ".$new_id.",
							   ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   ".(intval($args['cd_delegacia']) == 0 ? "DEFAULT" : intval($args['cd_delegacia']))."
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
		if(intval($args['cd_delegacia_cidade']) > 0)
		{
			$qr_sql = " 
						UPDATE familia_previdencia.delegacia_cidade
						   SET dt_exclusao = CURRENT_TIMESTAMP
						 WHERE cd_delegacia_cidade = ".intval($args['cd_delegacia_cidade'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}		

}
?>