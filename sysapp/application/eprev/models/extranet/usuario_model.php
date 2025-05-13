<?php

class usuario_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT u.cd_usuario,
				   UPPER(funcoes.remove_acento(u.nome)) AS nome,
				   u.usuario,
				   p.sigla,
				   u.email,
				   u.telefone_1,
				   u.telefone_2,
				   u.cpf
			  FROM extranet.usuario u
			  JOIN public.patrocinadoras p
				ON p.cd_empresa = u.cd_empresa
			 WHERE u.dt_exclusao IS NULL
			 ".(trim($args['cd_empresa']) != '' ? "AND u.cd_empresa = ".intval($args['cd_empresa']) : '')."
			 ORDER BY u.nome ASC";
			
		$result = $this->db->query($qr_sql);	
	}

	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_usuario,
				   nome,
				   usuario,
				   senha,
				   fl_troca_senha,
				   cd_empresa,
				   email,
				   telefone_1,
				   telefone_2,
				   cpf
			  FROM extranet.usuario
			 WHERE cd_usuario = ".intval($args['cd_usuario']);
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{		
		if(intval($args['cd_usuario']) > 0)
		{
			$qr_sql = " 
						UPDATE extranet.usuario
						   SET usuario        = ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".str_replace(" ","_",trim($args['usuario']))."'))").",
						       senha          = ".(trim($args['senha']) == trim($args['senha_old']) ? "senha" : "MD5('".trim($args['senha'])."')").",
							   nome           = ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   fl_troca_senha = ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'").",
							   cd_empresa     = ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : intval($args['cd_empresa'])).",
							   email          = ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
							   cpf            = ".(trim($args['cpf']) == "" ? "DEFAULT" : "'".$args['cpf']."'").",
							   telefone_1     = ".(trim($args['telefone_1']) == "" ? "DEFAULT" : "'".$args['telefone_1']."'").",
							   telefone_2     = ".(trim($args['telefone_2']) == "" ? "DEFAULT" : "'".$args['telefone_2']."'")."   
						 WHERE cd_usuario = ".intval($args['cd_usuario']).";
					  ";		
		}
		else
		{
			$qr_sql = " 
						INSERT INTO extranet.usuario
						     (  
							   usuario,
							   senha,
							   nome, 
							   email, 
							   telefone_1, 
							   telefone_2, 
							   cpf, 
							   cd_empresa, 
							   fl_troca_senha
							 )
					    VALUES						
						     (
							   ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".str_replace(" ","_",trim($args['usuario']))."'))").",
							   ".(trim($args['senha']) == "" ? "DEFAULT" : "MD5('".trim($args['senha'])."')").",
							   ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
							   ".(trim($args['telefone_1']) == "" ? "DEFAULT" : "'".$args['telefone_1']."'").",
							   ".(trim($args['telefone_2']) == "" ? "DEFAULT" : "'".$args['telefone_2']."'").",
							   ".(trim($args['cpf']) == "" ? "DEFAULT" : "'".$args['cpf']."'").",
							   ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : intval($args['cd_empresa'])).",
							   ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'")."
							 );			
					  ";		
		}
		
		$this->db->query($qr_sql);	
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE extranet.usuario
			   SET dt_exclusao = CURRENT_TIMESTAMP
			 WHERE cd_usuario = ".intval($args['cd_usuario']);
			 
		$this->db->query($qr_sql);	
	}
}

?>