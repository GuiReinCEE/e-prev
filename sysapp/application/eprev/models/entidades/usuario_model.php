<?php
class Usuario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function entidade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_entidade AS value,
				   ds_entidade AS text
			  FROM entidades.entidade
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_entidade;";

		$result = $this->db->query($qr_sql);
	}	
		
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT u.cd_usuario,
				   u.fl_troca_senha,
				   u.cpf,
				   u.nome,
				   u.email,
				   u.telefone1,
				   u.telefone2,
				   e.ds_entidade
			  FROM entidades.usuario u
			  JOIN entidades.entidade e
			    ON e.cd_entidade = u.cd_entidade
			 WHERE u.dt_exclusao IS NULL
			   AND e.dt_exclusao IS NULL
			 ".(trim($args['cd_entidade']) != '' ? "AND u.cd_entidade = ".intval($args['cd_entidade']) : '')."";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_usuario, 
				   cd_entidade, 
				   senha, 
				   fl_troca_senha, 
				   nome, 
				   email, 
				   telefone1, 
				   telefone2, 
				   cpf,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
			  FROM entidades.usuario
			 WHERE cd_usuario = ".intval($args['cd_usuario']).";";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{		
		if(intval($args['cd_usuario']) > 0)
		{
			$qr_sql = " 
				UPDATE entidades.usuario
				   SET cpf            = ".(trim($args['cpf']) == "" ? "DEFAULT" : "'".$args['cpf']."'").",
				       cd_entidade    = ".(trim($args['cd_entidade']) == "" ? "DEFAULT" : intval($args['cd_entidade'])).",
					   senha          = ".(trim($args['senha']) == trim($args['senha_old']) ? "senha" : "MD5(MD5('".trim($args['senha'])."'))").",
					   nome           = ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
					   email          = ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
					   telefone1      = ".(trim($args['telefone1']) == "" ? "DEFAULT" : "'".$args['telefone1']."'").",
					   telefone2      = ".(trim($args['telefone2']) == "" ? "DEFAULT" : "'".$args['telefone2']."'").",
					   fl_troca_senha = ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'")."
				 WHERE cd_usuario = ".intval($args['cd_usuario']).";";		
		}
		else
		{
			$qr_sql = " 
				INSERT INTO entidades.usuario
					 (  
					   cpf,
					   cd_entidade,
					   senha,
					   nome, 
					   email, 
					   telefone1, 
					   telefone2, 
					   fl_troca_senha
					 )
				VALUES						
					 (
		
					   ".(trim($args['cpf']) == "" ? "DEFAULT" : "'".$args['cpf']."'").",
					   ".(trim($args['cd_entidade']) == "" ? "DEFAULT" : intval($args['cd_entidade'])).",
					   ".(trim($args['senha']) == "" ? "DEFAULT" : "MD5(MD5('".trim($args['senha'])."'))").",
					   ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
					   ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
					   ".(trim($args['telefone1']) == "" ? "DEFAULT" : "'".$args['telefone1']."'").",
					   ".(trim($args['telefone2']) == "" ? "DEFAULT" : "'".$args['telefone2']."'").",
					   ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'")."
					 );			
			  ";	
		}
		
		$this->db->query($qr_sql);	
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE entidades.usuario
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
			 WHERE cd_usuario = ".intval($args['cd_usuario']).";";			
		$this->db->query($qr_sql);
	}	
}
?>