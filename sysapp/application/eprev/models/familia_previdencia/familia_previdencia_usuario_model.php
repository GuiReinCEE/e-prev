<?php
class Familia_previdencia_usuario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_usuario, 
					       usuario, 
						   senha, 
						   tp_usuario, 
						   CASE WHEN tp_usuario = 'F' THEN 'Fundação CEEE'
						        WHEN tp_usuario = 'A' THEN 'AFCEEE'
								ELSE ''
						   END AS ds_tipo_usuario,
						   nome, 
						   email, 
						   telefone_1, 
						   telefone_2, 
						   funcao, 
						   delegacia
                      FROM familia_previdencia.usuario
					 WHERE dt_exclusao IS NULL
					   AND cd_usuario NOT IN (99,999) -- usuarios de sistema
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	
	function usuario(&$result, $args=array())
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
						   funcao, 
						   delegacia,
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
                      FROM familia_previdencia.usuario
					 WHERE cd_usuario = ".intval($args['cd_usuario'])."
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	
	function salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_usuario']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE familia_previdencia.usuario
						   SET usuario        = ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".str_replace(" ","_",trim($args['usuario']))."'))").",
						       senha          = ".(trim($args['senha']) == trim($args['senha_old']) ? "senha" : "MD5('".trim($args['senha'])."')").",
							   tp_usuario     = ".(trim($args['tp_usuario']) == "" ? "DEFAULT" : "'".$args['tp_usuario']."'").",
							   nome           = ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   email          = ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
							   telefone_1     = ".(trim($args['telefone_1']) == "" ? "DEFAULT" : "'".$args['telefone_1']."'").",
							   telefone_2     = ".(trim($args['telefone_2']) == "" ? "DEFAULT" : "'".$args['telefone_2']."'").",
							   funcao         = ".(trim($args['funcao']) == "" ? "DEFAULT" : "'".$args['funcao']."'").",
							   delegacia      = ".(trim($args['delegacia']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['delegacia']."'))").",
							   fl_troca_senha = ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'")."
						 WHERE cd_usuario = ".intval($args['cd_usuario']).";
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_usuario']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("familia_previdencia.usuario", "cd_usuario"));
			$qr_sql = " 
						INSERT INTO familia_previdencia.usuario
						     (  
							   cd_usuario, 
							   usuario,
							   senha,
							   tp_usuario, 
							   nome, 
							   email, 
							   telefone_1, 
							   telefone_2, 
							   funcao, 
							   delegacia, 
							   fl_troca_senha
							 )
					    VALUES						
						     (
							   ".$new_id.",
							   ".(trim($args['usuario']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".str_replace(" ","_",trim($args['usuario']))."'))").",
							   ".(trim($args['senha']) == "" ? "DEFAULT" : "MD5('".trim($args['senha'])."')").",
							   ".(trim($args['tp_usuario']) == "" ? "DEFAULT" : "'".$args['tp_usuario']."'").",
							   ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",
							   ".(trim($args['email']) == "" ? "DEFAULT" : "LOWER(funcoes.remove_acento('".$args['email']."'))").",
							   ".(trim($args['telefone_1']) == "" ? "DEFAULT" : "'".$args['telefone_1']."'").",
							   ".(trim($args['telefone_2']) == "" ? "DEFAULT" : "'".$args['telefone_2']."'").",
							   ".(trim($args['funcao']) == "" ? "DEFAULT" : "'".$args['funcao']."'").",
							   ".(trim($args['delegacia']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['delegacia']."'))").",
							   ".(trim($args['fl_troca_senha']) == "" ? "DEFAULT" : "'".$args['fl_troca_senha']."'")."
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
		if(intval($args['cd_usuario']) > 0)
		{
			$qr_sql = " 
						UPDATE familia_previdencia.usuario
						   SET dt_exclusao = CURRENT_TIMESTAMP
						 WHERE cd_usuario = ".intval($args['cd_usuario'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}		
}
?>