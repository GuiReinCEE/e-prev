<?php
class urlcurta extends Controller
{
	var $token_acesso;
	
	function __construct()
    {
        parent::Controller();

		#PROGRAMA ELETRO
        #91e534dd7f7fd2d541d4e526ed9a20b1
		$this->token_acesso[] = md5('integracaourleletro');
		
		$this->load->model('projetos/link_rastreado_model');
    }

	public function geraURL()
	{
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]  = "N";
		$ar_ret["cd_erro"]  = "0";
		$ar_ret["retorno"]  = "";
		$ar_ret["cd_url_curta"]  = "";
		$ar_ret["ds_url_curta"]  = "";
		$ar_ret["ds_url_origem"] = "";
	
		$args["token"]                 = $this->input->post("token", TRUE); 
		$args["ds_url"]                = $this->input->post("url", TRUE);
		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		
		#print_r($args); exit;
		
		if(in_array($args["token"], $this->token_acesso))
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(trim($args["ds_url"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório URL não informado");
			}
			
			if($fl_campo_obrigatorio)
			{
				$this->link_rastreado_model->gerar($result, $args);
				$ar_url = $result->row_array();	

				if(count($ar_url) > 0)
				{
					if(trim($ar_url["ds_link"]) != "")
					{
						$ar_ret["cd_url_curta"] = str_replace("http://fprev.com.br/?","",$ar_url["ds_link"]);
						$ar_ret["cd_url_curta"] = str_replace("https://fprev.com.br/?","",$ar_ret["cd_url_curta"]);
						$ar_ret["cd_url_curta"] = str_replace("http://10.63.255.222/fceee/?","",$ar_ret["cd_url_curta"]);						
						
						$ar_ret["ds_url_curta"]  = $ar_url["ds_link"];
						$ar_ret["ds_url_origem"] = $args["ds_url"];
						$ar_ret["retorno"]       = "URL encurtada com sucesso";
					}
					else
					{
						$ar_ret["fl_erro"] = "S";
						$ar_ret["cd_erro"] = "2";
						$ar_ret["retorno"] = utf8_encode("ERRO: ao gerar URL");						
					}
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "3";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao gerar URL");						
				}				
			}
			else
			{
				$ar_ret["fl_erro"] = "S";
				$ar_ret["cd_erro"] = "1";
			}			
		}
		else
		{
			$ar_ret["fl_erro"] = "S";
			$ar_ret["cd_erro"] = "4";
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso não permitido");			
		}
		
		echo json_encode($ar_ret);			
	}
}
?>