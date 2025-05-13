<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Simplelogin Class
 *
 * Makes authentication simple
 *
 * Simplelogin is released to the public domain
 * (use it however you want to)
 * 
 * Simplelogin expects this database setup
 * (if you are not using this setup you may
 * need to do some tweaking)
 */
class Simplelogin
{
	var $CI;
	var $user_table = 'projetos.usuarios_controledi';

	function Simplelogin()
	{
		// get_instance does not work well in PHP 4
		// you end up with two instances
		// of the CI object and missing data
		// when you call get_instance in the constructor
		// $this->CI =& get_instance();
	}

	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($user = '', $password = '', $ip = '', $passport = '', $force=false)
	{
		// Put here for PHP 4 users
		$this->CI =& get_instance();

		if($user=='' && $password=='' && $ip!='')
		{
			// verifica auto login relacionado ao IP
			return $this->_login_auto($ip);
		}
		else if($user!='' && $password!='')
		{
			return $this->_login($user, $password, $force);
		}
		else if( $passport !='' )
		{
			return $this->_login_with_passport($passport);
		}
	}

	private function _login($user, $password, $force=false)
	{
		// Make sure login info was sent
		if($user == '' OR $password == '')
		{
			return false;
		}

		//Check if already logged in
		if(($this->CI->session->userdata('usuario') == $user) and (!$force))
		{
			//User is already logged in.
			#echo " | aqui 0 | ";
			return true;
		}


		#echo " | aqui 1 | ";
		// verifica opção de login do usuário
		$sql = "SELECT fl_ldap_autenticar, senha_md5 FROM projetos.usuarios_controledi WHERE tipo NOT IN ('X') AND usuario=" . $this->CI->db->escape( $user ) . ";";
		$query = $this->CI->db->query($sql);
		$result = $query->result_array();
		
		if( sizeof($result)==0 )
		{
			return FALSE;
		}
		else if( $result[0]['fl_ldap_autenticar']=="S" )
		{
			$auth_user = "FCEEE\\".$user;
			$ar_srv[0] = "10.63.255.13"; #'srvmail';
			#$ar_srv[1] = 'srvcontrole';
	
			// Tenta se conectar com o servidor 
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
	
			// Tenta autenticar no servidor 
			if (!($bind=@ldap_bind($connect, $auth_user, $password))) 
			{
				return FALSE; 
			} 
		}
		else
		{
			#echo " | aqui 2 | ".md5($password)." | ".$result[0]["senha_md5"];
			
			if(!(md5($password) == $result[0]["senha_md5"]))
			{
				#echo " | aqui 3 | ";
				return FALSE;
			}
		}

		
		// CHEGOU AQUI, A AUTENTICAÇÃO ESTÁ CORRETA
		
		//Check against user table
		$this->CI->db->select('codigo, nome, usuario, divisao, tipo, senha_md5, guerra'); 
		$this->CI->db->where('usuario', $user); 
		$query = $this->CI->db->getwhere($this->user_table);

		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			// Destroy old session
			$this->CI->session->sess_destroy();

			// Create a fresh, brand new session
			$this->CI->session->sess_create();

			// Remove the password field
			unset( $row['senha_md5'] );

			// Set session data
			$this->CI->session->set_userdata( $row );

			// Set logged_in to true
			$this->CI->session->set_userdata(array('logged_in' => true));

			// Login was successful			
			return true;
		}
		else
		{
			// No database result found
			return false;
		}
	}

	/**
	 * Login para estrangeiros no site
	 *
	 * @param string $passport composto por CODIGO e USERNAME criptografado com MD5
	 * @return boolean
	 */
	private function _login_with_passport( $passport )
	{
		// verifica opção de login do usuário
		$sql = "SELECT * FROM projetos.usuarios_controledi WHERE tipo NOT IN ('X') AND md5(codigo::varchar || usuario) = " . $this->CI->db->escape( $passport ) . ";";
		$query = $this->CI->db->query($sql);
		$result = $query->result_array();
		
		if( sizeof($result)==0 )
		{
			return FALSE;
		}
		else
		{
			// TUDO CERTO, PODE CONTINUAR ...
		}
		
		// CHEGOU AQUI, A AUTENTICAÇÃO ESTÁ CORRETA
		//Check against user table

		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			// Destroy old session
			$this->CI->session->sess_destroy();

			// Create a fresh, brand new session
			$this->CI->session->sess_create();

			// Set session data
			$this->CI->session->set_userdata( $row );

			// Set logged_in to true
			$this->CI->session->set_userdata(array('logged_in' => true));

			// Login was successful			
			return true;
		}
		else
		{
			// No database result found
			return false;
		}
	}

/*	private function _login($user, $password)
	{
		//Make sure login info was sent
		if($user == '' OR $password == '')
		{
			return false;
		}

		//Check if already logged in
		if($this->CI->session->userdata('usuario') == $user)
		{
			//User is already logged in.
			return true;
		}

		//Check against user table
		$this->CI->db->select('codigo, nome, usuario, divisao, senha_md5'); 
		$this->CI->db->where('usuario', $user); 
		$query = $this->CI->db->getwhere($this->user_table);

		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			// Check against password
			if(md5($password) != $row['senha_md5'])
			{
				return false;
			}

			// Destroy old session
			$this->CI->session->sess_destroy();

			// Create a fresh, brand new session
			$this->CI->session->sess_create();

			// Remove the password field
			unset( $row['senha_md5'] );

			// Set session data
			$this->CI->session->set_userdata( $row );

			// Set logged_in to true
			$this->CI->session->set_userdata(array('logged_in' => true));
			// Login was successful			
			return true;
		}
		else
		{
			// No database result found
			return false;
		}
	}*/

	private function _login_auto($ip)
	{
		$sql="SELECT *
		FROM projetos.usuarios_controledi
		WHERE estacao_trabalho='" . pg_escape_string($ip) . "' 
		AND tipo not in ('X', 'T')
		AND CAST(dt_hora_scanner_computador AS DATE) = CURRENT_DATE
		AND opt_interatividade = 'S'
		AND 0 = 1
		ORDER BY dt_ult_login DESC";

		$q = $this->CI->db->query($sql);

		// Verifica no banco de dados se o IP está relacionado a auto login
		// $usuario = Usuario::autologin($ip);
		$usuario = $q->row_array();

		//Check if already logged in
		/*if($this->CI->session->userdata('estacao_trabalho') == $ip)
		{
			echo "<div style='display:none;'>" . '{autologin:2}' . '</div>';
			//User is already logged in.
			return false;
		}*/

		if (sizeof($usuario)>0)
		{
			// Destroy old session
			$this->CI->session->sess_destroy();

			// Create a fresh, brand new session
			$this->CI->session->sess_create();

			// Remove the password field
			unset( $usuario['senha_md5'] );
			unset( $usuario['senha'] );

			// Set session data
			$this->CI->session->set_userdata( $usuario );

			// Set logged_in to true
			$this->CI->session->set_userdata(array('logged_in' => true));

			// Login was successful
			return true;
		}
		else
		{
			//echo "<div style='display:none;'>" . '{autologin:4}' . '</div>';
			// No database result found
			return false;
		}
	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		// Put here for PHP 4 users
		$this->CI =& get_instance();

		$this->CI->session->set_userdata(array('logged_in' => false));
		
		//Destroy session
		$this->CI->session->sess_destroy();

		$_SESSION = array();
		unset($_SESSION);
		
		@session_unset();
		@session_destroy();
	}
}
?>