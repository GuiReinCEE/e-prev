<?php
class Cadastro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT c.cd_cadastro, 
					       c.cd_cadastro_origem, 
						   c.nome, 
						   c.cargo, 
						   c.empresa, 
						   c.endereco, 
					       c.cep, 
						   c.cidade, 
						   c.uf, 
						   c.pais, 
						   c.telefone_ddd, 
						   c.telefone, 
						   c.celular_ddd, 
						   c.celular, 
					       c.email, 
						   c.dt_inclusao, 
						   c.cd_usuario_inclusao, 
						   TO_CHAR(c.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao, 
						   c.cd_usuario_exclusao,
						   co.origem
					  FROM acs.cadastro c
					  JOIN acs.cadastro_origem co
					    ON co.cd_cadastro_origem = c.cd_cadastro_origem
					 WHERE c.dt_exclusao IS NULL
					   AND co.dt_exclusao IS NULL
					   {ORIGEM}
					   {NOME}
					   {EMPRESA}
		          ";
		$qr_sql = str_replace("{ORIGEM}",(trim($args["cd_cadastro_origem"]) != "" ? " AND c.cd_cadastro_origem = '".$args["cd_cadastro_origem"]."'" : ""),$qr_sql);				  
		$qr_sql = str_replace("{NOME}",(trim($args["nome"]) != "" ? " AND UPPER(c.nome) LIKE UPPER('%".$args["cd_cadastro_origem"]."%')" : ""),$qr_sql);				  
		$qr_sql = str_replace("{EMPRESA}",(trim($args["empresa"]) != "" ? " AND UPPER(c.empresa) LIKE UPPER('%".$args["empresa"]."%')" : ""),$qr_sql);				  
				  
		$result = $this->db->query($qr_sql);
	}
		
	function cadastro( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT c.cd_cadastro, 
					       c.cd_cadastro_origem, 
						   c.nome, 
						   c.cargo, 
						   c.empresa, 
						   c.endereco, 
					       c.cep, 
						   c.cidade, 
						   c.uf, 
						   c.pais, 
						   c.telefone_ddd, 
						   c.telefone, 
						   c.celular_ddd, 
						   c.celular, 
					       c.email, 
						   c.dt_inclusao, 
						   c.cd_usuario_inclusao, 
						   TO_CHAR(c.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
						   c.cd_usuario_exclusao
					  FROM acs.cadastro c
					 WHERE c.cd_cadastro = ".intval($args['cd_cadastro'])."
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	

	function cadastroSalvar(&$result, $args=array())
	{
		if(intval($args['cd_cadastro']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE acs.cadastro
						   SET cd_cadastro_origem = ".(trim($args['cd_cadastro_origem']) == "" ? "DEFAULT" : $args['cd_cadastro_origem']).",
						       nome               = ".(trim($args['nome']) == "" ? "DEFAULT" : "'".$args['nome']."'").",
						       cargo              = ".(trim($args['cargo']) == "" ? "DEFAULT" : "'".$args['cargo']."'").",
						       empresa            = ".(trim($args['empresa']) == "" ? "DEFAULT" : "'".$args['empresa']."'").",
						       cep                = ".(trim($args['cep']) == "" ? "DEFAULT" : "'".$args['cep']."'").",
						       cidade             = ".(trim($args['cidade']) == "" ? "DEFAULT" : "'".$args['cidade']."'").",
						       uf                 = ".(trim($args['uf']) == "" ? "DEFAULT" : "'".$args['uf']."'").",
						       pais               = ".(trim($args['pais']) == "" ? "DEFAULT" : "'".$args['pais']."'").",
						       telefone_ddd       = ".(trim($args['telefone_ddd']) == "" ? "DEFAULT" : "'".$args['telefone_ddd']."'").",
						       telefone           = ".(trim($args['telefone']) == "" ? "DEFAULT" : "'".$args['telefone']."'").",
						       celular_ddd        = ".(trim($args['celular_ddd']) == "" ? "DEFAULT" : "'".$args['celular_ddd']."'").",
						       celular            = ".(trim($args['celular']) == "" ? "DEFAULT" : "'".$args['celular']."'").",
						       email              = ".(trim($args['email']) == "" ? "DEFAULT" : "'".$args['email']."'")."
						 WHERE cd_cadastro = ".intval($args['cd_cadastro'])."
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_cadastro']);
		}
		else
		{
			###INSERT NÃO IMPLEMENTADO
			$retorno = 0;
		}
		
		#echo "<pre>$qr_sql</pre>";exit;
		
		return $retorno;
	}	
	
	function excluir(&$result, $args=array())
	{
		if(intval($args['cd_cadastro']) > 0)
		{
			$qr_sql = " 
						UPDATE acs.cadastro
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE cd_cadastro = ".intval($args['cd_cadastro'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
}
?>
