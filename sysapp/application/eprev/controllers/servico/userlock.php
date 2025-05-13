<?php

class userlock extends Controller
{
	var $UL_HOST;
	var $UL_DOMAIN;
	var $UL_BASEDN;
	
	var $token_setferias;

    function __construct()
    {
        parent::Controller();

		$this->UL_HOST     = '10.63.255.50';
		$this->UL_DOMAIN   = 'eletroceee.com.br';
		$this->UL_BASEDN   = 'DC=eletroceee,DC=com,DC=br';
		
		$this->token_setferias = md5('integracaosetferias.exe'); #"0fecc25da87bc27327b282a6b7b6fe42" 

        $this->load->model('servico/userlock_model');
        $this->load->model('rh_avaliacao/rh_model');
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GTI','GS')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function index()
    {
        CheckLogin();
		
		if($this->get_permissao())
        {
            $data = array();
            
            $this->load->view('servico/userlock/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function ferias_listar()
    {
        CheckLogin();
		
		$timeZone = new DateTimeZone('UTC');
		
		$args = array();
        $data = array();
        $result = null;

        $args['validar_login_usuario'] = $this->input->post("validar_login_usuario", TRUE);
        $args['validar_login_senha']   = $this->input->post("validar_login_senha", TRUE);
        
        $this->userlock_model->ferias_listar($result, $args);
        $ar_result = $result->result_array();
        
		$ar_ret = Array();
		foreach($ar_result as $item)
		{
			/** Assumido que $dataEntrada e $dataSaida estao em formato dia/mes/ano */
			$dt_agr = DateTime::createFromFormat ('d/m/Y', date('d/m/Y'), $timeZone);
			$dt_ini = DateTime::createFromFormat ('d/m/Y', $item['dt_ferias_ini'], $timeZone);
			$dt_fim = DateTime::createFromFormat ('d/m/Y', $item['dt_ferias_fim'], $timeZone); 				
			
			$item['FL_VPN']      = $this->checkGrupo($item['usuario'],'vpn_fceee',$args['validar_login_usuario'],$args['validar_login_senha']);
			$item['FL_ULFERIAS'] = $this->checkGrupo($item['usuario'],'UL - Férias',$args['validar_login_usuario'],$args['validar_login_senha']);
			
			$FL_VPN      = $item['FL_VPN'];
			$FL_BLOQUEAR = FALSE;
			$FL_LIBERAR  = FALSE;
			$FL_FERIAS   = FALSE;			
			
			if (($dt_ini <= $dt_agr) AND ($dt_fim >= $dt_agr) AND ($FL_VPN == TRUE))
			{
				$FL_BLOQUEAR = TRUE;
			}
			elseif (!(($dt_ini <= $dt_agr) AND ($dt_fim >= $dt_agr)) AND ($dt_ini <= $dt_agr) AND ($FL_VPN == FALSE))
			{
				$FL_LIBERAR = TRUE;
			}
			elseif (($dt_ini <= $dt_agr) AND ($dt_fim >= $dt_agr))
			{
				$FL_FERIAS = TRUE;
			}			
			
			$item['FL_BLOQUEAR'] = $FL_BLOQUEAR;
			$item['FL_LIBERAR']  = $FL_LIBERAR;
			$item['FL_FERIAS']   = $FL_FERIAS;
			
			$ar_ret[] = $item;
		}
		
		$data['collection'] = $ar_ret;
        $this->load->view('servico/userlock/ferias_result', $data);
              
    }
	
    function acesso()
    {
        CheckLogin();
		
		if($this->get_permissao())
        {
            $data = array();
			
			$data['gerencia'] = $this->rh_model->get_gerencia_usuario();
            
            $this->load->view('servico/userlock/acesso', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
    
    function acesso_listar()
    {
		CheckLogin();
		
		$args = array();
        $data = array();
        $result = null;

        $args['fl_ativo']              = 'S';
		$args['cd_gerencia']           = $this->input->post("cd_gerencia", TRUE);
        $args['validar_login_usuario'] = $this->input->post("validar_login_usuario", TRUE);
        $args['validar_login_senha']   = $this->input->post("validar_login_senha", TRUE);
        
        $ar_result = $this->rh_model->listar($args);
        
		$ar_ret = Array();
		foreach($ar_result as $item)
		{
			$item['FL_VPN']        = $this->checkGrupo($item['usuario'],'vpn_fceee',$args['validar_login_usuario'],$args['validar_login_senha']);
			$item['FL_ULFERIAS']   = $this->checkGrupo($item['usuario'],'UL - Férias',$args['validar_login_usuario'],$args['validar_login_senha']);
			$item['FL_ULBLOQUEIO'] = $this->checkGrupo($item['usuario'],'UL - Bloqueio',$args['validar_login_usuario'],$args['validar_login_senha']);
			$item['FL_ULMFA']      = $this->checkGrupo($item['usuario'],'UL - MFA',$args['validar_login_usuario'],$args['validar_login_senha']);
			$item['FL_ULSABADO']   = $this->checkGrupo($item['usuario'],'UL - Sabado',$args['validar_login_usuario'],$args['validar_login_senha']);
			
			$ar_ret[] = $item;
		}
		
		$data['collection'] = $ar_ret;
        $this->load->view('servico/userlock/acesso_result', $data);
              
    }	
	

	function feriasBot()
	{
		#### UTILIZADO POR APLICATIVO RODANDO NO SERVIDOR DE MICROSOFT AD 10.63.255.50 (setFerias.exe) #####
		
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;

		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		
		
		$ar_ret["ar_usuario"]  = array();
		$ar_ret["dt_log"]      = date("Y-m-d H:i:s");
		$ar_ret["fl_erro"]     = "N";
		$ar_ret["cd_erro"]     = "0";
		$ar_ret["retorno"]     = "";
	
		$args["token"]     = $this->input->post("token", TRUE); 
		$args["fl_ferias"] = strtoupper((trim($this->input->post("fl_ferias", TRUE)) == "" ? "S" : trim($this->input->post("fl_ferias", TRUE)))); 
		
		#print_r($args); #exit;
		
		if($args["token"] == $this->token_setferias)
		{
			$this->userlock_model->listarFeriasBot($result, $args);
			$ar_result = $result->result_array();			
			
			$ar_ret["ar_usuario"]  = $ar_result;
			$ar_ret["retorno"]     = "Lista gerada";			
		}
		else
		{
			$ar_ret["fl_erro"] = "S";
			$ar_ret["cd_erro"] = "1";
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");			
		}
		
		echo json_encode($ar_ret);			
	}

#############################################################################################
	function checkGrupo($usuario, $g, $u, $p)
	{ 
		$user     = trim($u); 
		$password = trim($p); 
		$host     = $this->UL_HOST; 
		$domain   = $this->UL_DOMAIN;
		$basedn   = $this->UL_BASEDN;
		$group    = utf8_encode($g);

		#echo $group."<HR>";
		#print_r($user).br();
		#print_r($usuario).br();
		#print_r($password).br();	
		#print_r($basedn).br();	
		#exit;
		 
		$ad = ldap_connect($host) or die('Could not connect to LDAP server.');
		 
		ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
		 
		ldap_bind($ad, $user."@".$domain, $password) or die('Could not bind to AD.');
		
		$userdn = $this->getDN($ad, $usuario, $basedn);
		#echo $userdn."<BR>";


		$FL_RET = FALSE;
		if ($this->checkGroupEx($ad, $userdn, $this->getDN($ad, $group, $basedn)))
		{
			#echo "You're authorized as " . $this->getCN($userdn);
			$FL_RET = TRUE;
		}
		else
		{
			#echo 'Authorization failed';
			$FL_RET = FALSE;
		}
		 
		ldap_unbind($ad);
		
		return $FL_RET;
	}

	/**
	 * This function searchs in LDAP tree entry specified by samaccountname and
	 * returns its DN or epmty string on failure.
	 *
	 * @param resource $ad
	 *          An LDAP link identifier, returned by ldap_connect().
	 * @param string $samaccountname
	 *          The sAMAccountName, logon name.
	 * @param string $basedn
	 *          The base DN for the directory.
	 * @return string
	 */
	function getDN($ad, $samaccountname, $basedn)
	{
		$result = ldap_search($ad, $basedn, "(samaccountname=$samaccountname)", array('dn'));
		if (! $result)
		{
			return '';
		}
	 
		$entries = ldap_get_entries($ad, $result);
		if ($entries['count'] > 0)
		{
			return $entries[0]['dn'];
		}
	 
		return '';
	}
	 
	/**
	 * This function retrieves and returns Common Name from a given Distinguished
	 * Name.
	 *
	 * @param string $dn
	 *          The Distinguished Name.
	 * @return string The Common Name.
	 */
	function getCN($dn)
	{
		preg_match('/[^,]*/', $dn, $matchs, PREG_OFFSET_CAPTURE, 3);
		return $matchs[0][0];
	}
	 
	/**
	 * This function checks group membership of the user, searching only in
	 * specified group (not recursively).
	 *
	 * @param resource $ad
	 *          An LDAP link identifier, returned by ldap_connect().
	 * @param string $userdn
	 *          The user Distinguished Name.
	 * @param string $groupdn
	 *          The group Distinguished Name.
	 * @return boolean Return true if user is a member of group, and false if not
	 *         a member.
	 */
	function checkGroup($ad, $userdn, $groupdn)
	{
		$result = ldap_read($ad, $userdn, "(memberof=$groupdn)", array('members'));
		if (! $result)
		{
			return false;
		}
	 
		$entries = ldap_get_entries($ad, $result);
	 
		return ($entries['count'] > 0);
	}
	 
	/**
	 * This function checks group membership of the user, searching in specified
	 * group and groups which is its members (recursively).
	 *
	 * @param resource $ad
	 *          An LDAP link identifier, returned by ldap_connect().
	 * @param string $userdn
	 *          The user Distinguished Name.
	 * @param string $groupdn
	 *          The group Distinguished Name.
	 * @return boolean Return true if user is a member of group, and false if not
	 *         a member.
	 */
	function checkGroupEx($ad, $userdn, $groupdn)
	{
		$result = ldap_read($ad, $userdn, '(objectclass=*)', array('memberof'));
		
		if (! $result)
		{
			return false;
		}

		$entries = ldap_get_entries($ad, $result);
		
		if ($entries['count'] <= 0)
		{
			return false;
		}

		if (empty($entries[0]['memberof']))
		{
			return false;
		}

		for ($i = 0; $i < $entries[0]['memberof']['count']; $i ++)
		{
			#echo  $entries[0]['memberof'][$i]."<BR>";
			
			if ($entries[0]['memberof'][$i] == $groupdn)
			{
				return true;
			}
			elseif ($this->checkGroupEx($ad, $entries[0]['memberof'][$i], $groupdn))
			{
				return true;
			}
		}

		return false;
	}
	 
}
?>