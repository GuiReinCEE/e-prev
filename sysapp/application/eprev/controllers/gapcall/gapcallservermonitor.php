<?php
class Gapcallservermonitor extends Controller
{
	var $token_acesso;
	
	function __construct()
    {
        parent::Controller();

        #PROGRAMA VB
        #"defa664439ea213792b14361f108e81c"
		$this->token_acesso[] = md5('integracaogapcall');
		
		$this->load->model('projetos/gapcallservermonitor_model');
    }
	
	public function monitoraRamais($token = "")
    {
		#http://www.e-prev.com.br/cieprev/index.php/gapcallservermonitor/gapcallservermonitor/monitoraRamais/defa664439ea213792b14361f108e81c
		
		/*
            SELECT usuario,
                   nr_ramal_callcenter,
                   nr_ip_callcenter,
                   dt_login_callcenter,
                   dt_monitor_callcenter
              FROM projetos.usuarios_controledi
             WHERE nr_ramal_callcenter IS NOT NULL
               AND dt_login_callcenter IS NOT NULL
               AND dt_monitor_callcenter IS NULL
             ORDER BY nr_ramal_callcenter		
		*/
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]   = "N";
		$ar_ret["cd_erro"]   = "0";
		$ar_ret["retorno"]   = "";			
		$ar_ret["fl_logado"] = "N";
	
		$args["token"]      = trim($token); 
		$args["ip_cliente"] = trim($ip_cliente); 
		
		if(in_array($args["token"], $this->token_acesso))
		{		
			if(trim($ip_cliente) != "")
			{
				$this->gapcall_model->testaCliente($result, $args);
				$data = $result->row_array();
				
				if (count($data) > 0)
				{
					$ar_ret["fl_logado"] = $data["fl_logado"];
				}
				else
				{
					$ar_ret["fl_erro"]  = "S";
					$ar_ret["cd_erro"]  = "1";
					$ar_ret["retorno"]  = utf8_encode("ERRO: IP não encontrado");						
				}
			}
			else
			{
				$ar_ret["fl_erro"]  = "S";
				$ar_ret["cd_erro"]  = "2";
				$ar_ret["retorno"]  = utf8_encode("ERRO: IP não informado");			
			}			
		}
		else
		{
			$ar_ret["fl_erro"]  = "S";
			$ar_ret["cd_erro"]  = "3";
			$ar_ret["retorno"]  = utf8_encode("ERRO: acesso não permitido");			
		}
		
		echo json_encode($ar_ret);
    }	
	
	public function iniciaConexao($token = "", $ip_cliente = "", $nr_ramal = 0)
    {
		#http://www.e-prev.com.br/cieprev/index.php/gapcall/gapcall/iniciaConexao/defa664439ea213792b14361f108e81c/10.63.255.150/5030
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]   = "N";
		$ar_ret["cd_erro"]   = "0";
		$ar_ret["retorno"]   = "";			
	
		$args["token"]      = trim($token); 
		$args["ip_cliente"] = trim($ip_cliente); 
		$args["nr_ramal"]   = trim($nr_ramal); 
		
		if(in_array($args["token"], $this->token_acesso))
		{		
			if(trim($ip_cliente) != "")
			{
				if(intval($nr_ramal) > 0)
				{
					$this->gapcall_model->iniciaConexao($args);
				}
				else
				{
					$ar_ret["fl_erro"]  = "S";
					$ar_ret["cd_erro"]  = "1";
					$ar_ret["retorno"]  = utf8_encode("ERRO: RAMAL não informado");						
				}
			}
			else
			{
				$ar_ret["fl_erro"]  = "S";
				$ar_ret["cd_erro"]  = "2";
				$ar_ret["retorno"]  = utf8_encode("ERRO: IP não informado");			
			}			
		}
		else
		{
			$ar_ret["fl_erro"]  = "S";
			$ar_ret["cd_erro"]  = "3";
			$ar_ret["retorno"]  = utf8_encode("ERRO: acesso não permitido");			
		}
		
		echo json_encode($ar_ret);
    }	
	
	public function encerrar($token = "", $ip_cliente = "", $nr_ramal = 0)
    {
		#http://www.e-prev.com.br/cieprev/index.php/gapcall/gapcall/encerrar/defa664439ea213792b14361f108e81c/10.63.255.150/5030
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]   = "N";
		$ar_ret["cd_erro"]   = "0";
		$ar_ret["retorno"]   = "";			
	
		$args["token"]      = trim($token); 
		$args["ip_cliente"] = trim($ip_cliente); 
		$args["nr_ramal"]   = trim($nr_ramal); 
		
		if(in_array($args["token"], $this->token_acesso))
		{		
			if(trim($ip_cliente) != "")
			{
				if(intval($nr_ramal) > 0)
				{
					$this->gapcall_model->encerrar($args);
				}
				else
				{
					$ar_ret["fl_erro"]  = "S";
					$ar_ret["cd_erro"]  = "1";
					$ar_ret["retorno"]  = utf8_encode("ERRO: RAMAL não informado");						
				}
			}
			else
			{
				$ar_ret["fl_erro"]  = "S";
				$ar_ret["cd_erro"]  = "2";
				$ar_ret["retorno"]  = utf8_encode("ERRO: IP não informado");			
			}			
		}
		else
		{
			$ar_ret["fl_erro"]  = "S";
			$ar_ret["cd_erro"]  = "3";
			$ar_ret["retorno"]  = utf8_encode("ERRO: acesso não permitido");			
		}
		
		echo json_encode($ar_ret);
    }	
}
?>