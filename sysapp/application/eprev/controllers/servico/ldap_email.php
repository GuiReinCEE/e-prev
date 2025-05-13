<?php
class Ldap_email extends Controller
{
	function __construct()
    {
		parent::Controller();
	}

	var $ldap_server = "10.63.255.13"; 
	var $dominio     = "FCEEE\\"; 
	var $auth_user   = "FCEEE\eprev"; 
	var $auth_pass   = "@fcc8ml09"; 
	var $base_dn     = "DC=eletroceee,DC=com,DC=br"; 
	var $conn        = null;

	private function connect()
	{
		if ($this->conn=@ldap_connect($this->ldap_server))
		{
			ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);

			if ($bind=@ldap_bind($this->conn, $this->auth_user, $this->auth_pass))
			{
				return true;
			}
			else
			{
				echo '1- SEM CONEXÃO COM LDAP (BIND)'.br();
				return false;
			}
		}
		else
		{
			echo '2- SEM CONEXÃO COM LDAP (CONNECT)'.br();
			return false;
		}
	}

	private function get_grupos()
	{
		$grupos = array();

		if ($search=@ldap_search($this->conn, $this->base_dn, '(&(objectClass=group))')) 
	 	{
	 		$number_returned = ldap_count_entries($this->conn, $search);
		 	$info            = ldap_get_entries($this->conn, $search);

		 	foreach ($info as $key => $item) 
		   	{
		   		if(isset($item['mailnickname']))
		   		{
		   			$grupos[strtoupper(utf8_decode($item['mailnickname'][0]))] = utf8_decode($item['dn']);
		   		}
		   	}
	 	}
	 	else
	 	{
	 		echo '3- ERRO NA SEARCH DOS GRUPOS'.br();
	 	}

	 	return $grupos;
	}

	private function verifica_grupos($usuario, $grupo)
	{
		$filter = '(&(objectClass=group)(memberOf='.$grupo.'))';

		if ($search=@ldap_search($this->conn, $this->base_dn, $filter)) 
	 	{
	 		$number_returned = ldap_count_entries($this->conn, $search);
		 	$info            = ldap_get_entries($this->conn, $search);

		 	foreach ($info as $key => $value) 
		   	{
		   		if(isset($value['mailnickname']))
		   		{
		   			if($this->verifica_user($usuario, $value['dn']) > 0)
		   			{
		   				break;
		   			}
		   		}
		   	}

	 	}
	 	else
	 	{
	 		echo '4- ERRO NA SEARCH DOS GRUPOS'.br();
	 	}
	}

	private function verifica_user($usuario, $grupo)
	{
		$filter = '(&(objectClass=user)(samaccountname='.$usuario.')(memberOf='.$grupo.'))';
		
		if ($search=@ldap_search($this->conn, $this->base_dn, $filter)) 
	 	{
	 		$number_returned = ldap_count_entries($this->conn, $search);
		 	$info            = ldap_get_entries($this->conn, $search);
		 
		 	if(intval($info['count']) > 0)
		 	{
		 		echo intval($info['count']); 
		 		exit;
		 	}
		 	else
		 	{
		 		$this->verifica_grupos($usuario, $grupo);
		 	}
	 	}
	 	else
	 	{
	 		echo '5- ERRO NA SEARCH DO USUÁRIO GRUPO'.br();
	 	}
	}

	public function index($usuario, $email)
	{
		if($this->connect())
		{
			$grupos = $this->get_grupos();
			
			if(isset($grupos[strtoupper($email)]))
			{
				echo intval($this->verifica_user($usuario, $grupos[strtoupper($email)]));
			}	
			else
			{
				echo '6- ERRO AO ENCONTRAR O GRUPO'.br();
			}

			ldap_close($this->conn);
		}
	}
}