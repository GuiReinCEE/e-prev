<?php

	function valida_ldap($auth_user,$auth_pass)
	{ 
		global $db;
		$auth_user = "FCEEE\\".$auth_user;
		#$ar_srv[0] = "10.63.255.2"; #'srvmail';
		$ar_srv[0] = "10.63.255.13"; 
		$ar_srv[1] = '10.63.255.50';

		// Tenta se conectar com o servidor 
		$fp = fsockopen($ar_srv[0], 389, $errno, $errstr, 5);
		if (!$fp) 
		{
			$fp = fsockopen($ar_srv[1], 389, $errno, $errstr, 5);
			if (!$fp) 
			{	
				return FALSE; 
			}	
			else
			{
				$ldap_server = $ar_srv[1];
			}				
		}
		else
		{
			$ldap_server = $ar_srv[0];
		}	

		if (!($connect=@ldap_connect($ldap_server))) 
		{ 
			return FALSE; 
		}
		else
		{
			$ldap_server = $ar_srv[0];
		}		
		
		/*
		if (!($connect=@ldap_connect($ar_srv[0]))) 
		{ 
			if (!($connect=@ldap_connect($ar_srv[1]))) 
			{ 
				return FALSE; 
			} 
			else
			{
				$ldap_server = $ar_srv[1];
			}					
		}
		else
		{
			$ldap_server = $ar_srv[0];
		}		
		*/
		
		// Tenta autenticar no servidor 
		if (!($bind=@ldap_bind($connect, $auth_user, $auth_pass))) 
		{ 
			//echo $bind;
			//echo ldap_err2str(ldap_errno($connect)); 
			//exit;
			$qr_execute = "
							INSERT INTO projetos.log
							     (
								   tipo, 
								   local, 
								   descricao, 
								   dt_cadastro
								 )
							VALUES 
							     (
								   'ERRO', 
								   '/controle_projetos/inc/ldap.php', 
								   '".str_replace("FCEEE\\","",$auth_user)." => ".ldap_err2str(ldap_errno($connect))."', 
								   CURRENT_TIMESTAMP
								 );			
			              ";
			@pg_query($db,$qr_execute); 

			return FALSE; 
		} 
		else
		{
			return TRUE; 
		}

	}// fim funcao conectar ldap 

	
?>