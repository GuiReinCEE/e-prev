<?php
class Telegram extends Controller
{
	var $API_URL = 'https://api.telegram.org/bot';
	
	var $API_URL_ELETRO;
	var $BOT_TOKEN_ELETRO = '1228646493:AAF-YEvSPrBck4ecdiedEO_QAGY61TF65eM'; #@eletroffpBot
	
	var $API_URL_EPREV;
	var $BOT_TOKEN_EPREV = '1216913435:AAFp9bQ7a1vhRhGMc7268YJjAQ1O9iB296Y'; #@eprevffpBot
	
	var $API_URL_APP;
	var $BOT_TOKEN_APP = '1257911006:AAFWY1yJ91cl_uh4sr-6Y0yZqPfMRznPt1Q'; #@appffpBot	

	var $token_eletro;
	var $token_eprev;
	var $token_app;
	
	function __construct()
    {
        parent::Controller();
		
		$this->API_URL_ELETRO = $this->API_URL.$this->BOT_TOKEN_ELETRO;
		$this->API_URL_EPREV  = $this->API_URL.$this->BOT_TOKEN_EPREV;
		$this->API_URL_APP    = $this->API_URL.$this->BOT_TOKEN_APP;

		$this->token_eletro = md5('integracaoenviartelegrameletroDESATIVADO'); #"512f947fc969f948dc2ccc661ef237e2" 
		$this->token_eprev  = md5('integracaoenviartelegrameprev'); #"b83a4417d8c4514d9fbfe935b3f4963c"
		$this->token_app    = md5('integracaoenviartelegramapp'); #"7d34b15c7bb44bce00c1ef2cd56dd46c"
    }
	
	public function sendBotEletro()
	{
		$this->sendBot($this->API_URL_ELETRO, $this->token_eletro);			
	}

	public function sendBotEprev()
	{
		$this->sendBot($this->API_URL_EPREV, $this->token_eprev);			
	}
	
	public function sendBotApp()
	{
		$this->sendBot($this->API_URL_APP, $this->token_app);			
	}	
	
	private function enviarMensagem($id_pessoa, $mensagem, $url_api) 
	{
		$retorno = FALSE;
		try
		{
			$url = $url_api."/sendMessage";
			$ar_data = array("chat_id" => $id_pessoa, "text" => $mensagem);
			$data_json = json_encode($ar_data);   

			#echo $url."<br>";		
			#echo $data_json."<br>";
			#exit;	
			#"{"ok":true,"result":{"message_id":18,"from":{"id":1228646493,"is_bot":true,"first_name":"eletrobot","username":"eletroffpBot"},"chat":{"id":588887506,"first_name":"Cristiano","last_name":"Jacobsen","type":"private"},"date":1590583533,"text":"oi, tudo bom"}}"			
			#"{"ok":false,"error_code":502,"description":"Bad Gateway"}"
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);    
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_json)));                                                                                                                   
			 
			$output = curl_exec($ch);
			curl_close($ch);
			
			#var_dump($output)."<hr>";
			
			$retorno = TRUE;
		} 
		catch (Exception $e) 
		{
			$retorno = FALSE;
		}
		
		return $output;
		//return $retorno;
	}	
	
	private function sendBot($_URL_API, $_TOKEN)
	{
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["cd_telegram"] = "0";
		$ar_ret["dt_log"]      = date("Y-m-d H:i:s");
		$ar_ret["fl_erro"]     = "N";
		$ar_ret["cd_erro"]     = "0";
		$ar_ret["retorno"]     = "";
	
		$args["token"]   = $this->input->post("token", TRUE); 
		$args["ar_para"] = explode(";",trim($this->input->post("para", TRUE)));
		$args["texto"]   = ($this->input->post("texto", TRUE));
		
		#print_r($args); #exit;
		
		if($args["token"] == $_TOKEN)
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(count($args["ar_para"]) == 0)
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório PARA não informado");
			}
			elseif(trim($args["ar_para"][0]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório PARA não informado");
			}
			elseif(trim($args["texto"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$campo = "";
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório TEXTO não informado");
			}
			elseif(strlen($args["texto"]) > 4000)
			{
				$fl_campo_obrigatorio = FALSE;
				$campo = "";
				$ar_ret["retorno"] = utf8_encode("ERRO: o tamanho máximo do campo TEXTO é de 4000 caracteres");
			}				
			
			
			if($fl_campo_obrigatorio)
			{
				#enviarMensagem($id_pessoa, $mensagem, $url_api)
				foreach($args["ar_para"] as $para)
				{
					$retorno = $this->enviarMensagem($para, $args["texto"], $_URL_API);
					
					#echo $para." => ".$retorno."<BR>";
				} 
				
				#$ar_ret["retorno"]     = "Mensagem enviada com sucesso: ".$retorno." ".str_replace(chr(10),"",print_r($args,true));
				$ar_ret["cd_telegram"] = "0";
				$ar_ret["retorno"]     = "Mensagem enviada com sucesso";
				/*
				if(intval($cd_telegram) > 0)
				{
					$ar_ret["retorno"]     = "Mensagem enviada com sucesso";
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "2";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao enviar a mensagem");						
				}
				*/ 
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
			$ar_ret["cd_erro"] = "3";
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");			
		}
		
		echo json_encode($ar_ret);			
	}



}
?>