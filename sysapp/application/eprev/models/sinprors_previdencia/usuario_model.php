<?php
class Usuario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_usuario,
				   tp_usuario,
				   fl_troca_senha,
				   cpf,
				   usuario,
				   nome,
				   email,
				   telefone_1,
				   telefone_2,
				   CASE WHEN tp_usuario = 'F' THEN 'Fundaчуo CEEE'
						WHEN tp_usuario = 'C' THEN 'Consultor'
						WHEN tp_usuario = 'S' THEN 'SINPRO'
						ELSE ''
				   END AS ds_tipo_usuario,
				   cd_empresa
			  FROM sinprors_previdencia.usuario
			 WHERE dt_exclusao IS NULL;";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_usuario, 
				   usuario, 
				   senha, 
				   tp_usuario, 
				   fl_troca_senha, 
				   nome, 
				   email, 
				   telefone_1, 
				   telefone_2, 
				   cpf,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
				   cd_empresa
			  FROM sinprors_previdencia.usuario
			 WHERE cd_usuario = ".intval($args['cd_usuario']).";";
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{		
		if(intval($args['cd_usuario']) > 0)
		{
			$qr_sql = " 
				UPDATE sinprors_previdencia.usuario
				   SET usuario        = ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".str_replace(" ","_",trim($args['usuario']))."'))").",
				       cpf            = ".(trim($args['cpf']) == "" ? "DEFAULT" : "'".$args['cpf']."'").",
					   senha          = ".(trim($args['senha']) == trim($args['senha_old']) ? "senha" : "MD5('".trim($args['senha'])."')").",
					   tp_usuario     = ".(trim($args['tp_usuario']) == "" ? "DEFAULT" : "'".$args['tp_usuario']."'").",
					   cd_empresa     = ".(intval($args['cd_empresa']) == 0 ? "DEFAULT" : intval($args['cd_empresa'])).",
					   nome           = ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
					   email          = ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
					   telefone_1     = ".(trim($args['telefone_1']) == "" ? "DEFAULT" : "'".$args['telefone_1']."'").",
					   telefone_2     = ".(trim($args['telefone_2']) == "" ? "DEFAULT" : "'".$args['telefone_2']."'").",
					   fl_troca_senha = ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'")."
				 WHERE cd_usuario = ".intval($args['cd_usuario']).";";		
		}
		else
		{
			$qr_sql = " 
				INSERT INTO sinprors_previdencia.usuario
					 (  
					   usuario,
					   cpf,
					   senha,
					   tp_usuario,
					   cd_empresa,
					   nome, 
					   email, 
					   telefone_1, 
					   telefone_2, 
					   fl_troca_senha,
					   cd_usuario_inclusao
					 )
				VALUES						
					 (
		
					   ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".str_replace(" ","_",trim($args['usuario']))."'))").",
					   ".(trim($args['cpf']) == "" ? "DEFAULT" : "'".$args['cpf']."'").",
					   ".(trim($args['senha']) == "" ? "DEFAULT" : "MD5('".trim($args['senha'])."')").",
					   ".(trim($args['tp_usuario']) == "" ? "DEFAULT" : "'".$args['tp_usuario']."'").",
					   ".(intval($args['cd_empresa']) == 0 ? "DEFAULT" : intval($args['cd_empresa'])).",
					   ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
					   ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
					   ".(trim($args['telefone_1']) == "" ? "DEFAULT" : "'".$args['telefone_1']."'").",
					   ".(trim($args['telefone_2']) == "" ? "DEFAULT" : "'".$args['telefone_2']."'").",
					   ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'").",
					   ".intval($args['cd_usuario_inclusao'])."
					 );			
			  ";	
		}
		
		$this->db->query($qr_sql);	
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE sinprors_previdencia.usuario
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
			 WHERE cd_usuario = ".intval($args['cd_usuario']).";";			
		$this->db->query($qr_sql);
	}	
}
?>