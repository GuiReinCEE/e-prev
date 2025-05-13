<?php
class familiavendas_integracao extends Controller
{
	function __construct()
	{
		parent::Controller();
		
		$this->token_acesso = md5('integracaocadastrooracle'); #7c97bf441ef21a93c91d4524039a0e01
	}

	function index() #$token = "", $cpf = ""
    {
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"] = "N";
		$ar_ret["cd_erro"] = "0";
		$ar_ret["retorno"] = "";
		$ar_ret["dado"]    = Array();
	
		$args["token"]  = $this->input->post("token", TRUE); 
		$args["codigo"] = $this->input->post("codigo", TRUE);
		
		#print_r($args); #exit;
		
		if($args["token"] == $this->token_acesso)
		{		
			if(trim($args["codigo"]) != "")
			{
				$this->load->model('familiavendas/solicitacao_model');
				$data = $this->solicitacao_model->cadastro(297,"");
				#print_r($data); exit;
				
				if (count($data) > 0)
				{
					$ar_cad = Array();
					foreach($data as $key => $value)
					{
						$ar_cad[$key] = utf8_encode($value);
					}
				
					$ar_ret["dado"] = $ar_cad;
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "1";
					$ar_ret["retorno"] = utf8_encode("ERRO: codigo não encontrado");						
				}
				
				#print_r($ar_ret); exit;
			}
			else
			{
				$ar_ret["fl_erro"] = "S";
				$ar_ret["cd_erro"] = "2";
				$ar_ret["retorno"] = utf8_encode("ERRO: codigo não informado");			
			}			
		}
		else
		{
			$ar_ret["fl_erro"] = "S";
			$ar_ret["cd_erro"] = "3";
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso não permitido");			
		}
		
		echo json_encode($ar_ret);
    }	
}