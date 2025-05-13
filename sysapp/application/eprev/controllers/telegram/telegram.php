<?php
class Telegram extends Controller
{
	var $token_eprev;
	
	function __construct()
    {
        parent::Controller();

		$this->token_eprev  = md5('integracaoenviartelegrameprev'); #"b83a4417d8c4514d9fbfe935b3f4963c"
    }

	public function sendBotEprev()
	{
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
		$args["texto"]   = ($this->input->post("texto", TRUE)).chr(10)."[TL]";		
		
		if($args["token"] == $this->token_eprev)
		{
			#### CURL PARA CHATGOOGLE ####	
			$ar_curl_campos = array (
				"token"=>"ef7b464876e696d6339e3def252ea917",
				"texto"=>$args["texto"]
			);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://www.e-prev.com.br/cieprev/index.php/chatgoogle/chatgoogle/sendBotEprev");
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$ar_curl_campos);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				
			$_RETORNO = curl_exec($ch);
			curl_close ($ch);
			
			$ar_ret["cd_telegram"] = "0";
			$ar_ret["retorno"]     = "Mensagem enviada com sucesso";			
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