<?php
class sms extends Controller
{
	var $token_acesso;
	var $token_acesso_eletro;
	
	function __construct()
    {
        parent::Controller();

		$this->token_acesso        = md5('integracaoenviarSMS');       #"e7052c9e1f0c80647e2e76bae6aae08c"
		$this->token_acesso_eletro = md5('integracaoenviarSMSeletro'); #"221abaaca88230f68a6b8d735fff831e"
		
		$this->load->model('sms/sms_model');
    }
	
	function smsEletroIncluir()
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
		$ar_ret["cd_sms"]  = "0";
	
		$args["token"]                 = $this->input->post("token", TRUE); 
		$args["dt_agendado"]           = $this->input->post("dt_agendado", TRUE);
		$args["cd_sms_tipo"]           = $this->input->post("cd_sms_tipo", TRUE);
		$args["para"]                  = intval($this->input->post("para", TRUE));
		$args["assunto"]               = utf8_decode($this->input->post("assunto", TRUE));
		$args["conteudo"]              = utf8_decode($this->input->post("conteudo", TRUE));
		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		$args["usuario"]               = $this->input->post("usuario", TRUE);
		
		if($args["token"] == $this->token_acesso_eletro)
		{
			$fl_campo_obrigatorio = TRUE;
			$tam = intval(strlen($args["assunto"])) + intval(strlen($args["conteudo"]));
			$tam_limite = 160;
			
			if(intval($args["para"]) <= 0)
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campo obrigatorio PARA (numero celular + DDD) nao informado");
			}	
			elseif($tam < 1)
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campos ASSUNTO e CONTEUDO nao informado");
			}			
			elseif($tam > $tam_limite)
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Quantidade de caracteres do ASSUNTO mais o CONTEUDO e maior que o permitido (".$tam_limite.") ");
			}			
			/*elseif(trim($args["assunto"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatorio ASSUNTO nao informado");
			}*/			
			elseif(trim($args["conteudo"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$campo = "";
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatorio CONTEUDO nao informado");
			}
			
			if($fl_campo_obrigatorio)
			{
				$this->sms_model->smsEletroIncluir($result, $args);
				$ar_data = $result->row_array();				
				$cd_sms = $ar_data['id_sms'];
			
				if(intval($cd_sms) > 0)
				{
					$ar_ret["cd_sms"] = intval($cd_sms);
					$ar_ret["retorno"]  = "SMS registrado com sucesso";
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "2";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao inserir os dados do SMS");						
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
			$ar_ret["cd_erro"] = "3";
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");			
		}
		
		echo json_encode($ar_ret);			
	}	
	
	
	function smsEnviar($token = "", $cd_sms = 0)
    {
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]  = "N";
		$ar_ret["cd_erro"]  = "0";
		$ar_ret["retorno"]  = "";			
	
		$args["token"]  = trim($token); 
		$args["cd_sms"] = intval($cd_sms); 
		
		#print_r($args); exit;
		
		if($args["token"] == $this->token_acesso)
		{		
			if(intval($cd_sms) > 0)
			{
				$this->sms_model->getSMS($result, $args);
				$row = $result->row_array();				

				if(count($row) > 0)
				{
					$ar_retorno = $this->wsSendSMS($row['nr_telefone'], utf8_encode($row['ds_assunto']), utf8_encode($row['ds_conteudo']));	

					
					$ar_ret["fl_erro"] = $ar_retorno['fl_erro'];
					$ar_ret["cd_erro"] = ($ar_retorno['fl_erro'] == "S" ? "1" : "0");
					$ar_ret["retorno"] = $ar_retorno['retorno'];
					
					$args['ws_retorno'] = json_encode($ar_ret);
					$this->sms_model->setEnvioSMS($result, $args);					
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "2";
					$ar_ret["retorno"] = utf8_encode("ERRO: SMS nao registrado");			
				}					
			}
			else
			{
				$ar_ret["fl_erro"] = "S";
				$ar_ret["cd_erro"] = "3";
				$ar_ret["retorno"] = utf8_encode("ERRO: codigo SMS invalido");			
			}			
		}
		else
		{
			$ar_ret["fl_erro"] = "S";
			$ar_ret["cd_erro"] = "4";
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");			
		}
		
		echo json_encode($ar_ret);
    }	

	#### FUNCAO DE ENVIO - INTEGRAÇÃO ####
	
	private function wsSendSMS($para, $assunto, $conteudo)
	{
		$_URL_WS  = "https://ws.smartcomm.digital/";
		$_METODO  = "sms/envio";
		$_USUARIO = "sisfceee";
		$_SENHA   = "c8ml09";
		$_RETORNO['fl_erro'] = "N";
		$_RETORNO['retorno'] = "";
		
		if(intval($para) <= 0)
		{
			$_RETORNO['fl_erro'] = "S";
			$_RETORNO['retorno'] = utf8_encode('Numero do telefone invalido');
		}
		elseif(trim($conteudo) == "")
		{
			$_RETORNO['fl_erro'] = "S";
			$_RETORNO['retorno'] = utf8_encode('Conteudo invalido');
		}		
		else
		{
			//parâmetros para envio
			$parametros = array
			(
				'destinatarios' => Array(intval($para)),
				'assunto'       => $assunto,
				'mensagem'      => $conteudo,
				'data'          => date('dd/MM/yyyy'),
				'flash'         => FALSE #flash
			);
			
			//dados da requisição
			$dados = array
			(
				'http' => array
				(
				'header' => array
				(
					'Content-type: application/json',
					'Authorization: Basic '.base64_encode($_USUARIO.':'.$_SENHA)
				),
				'method'  => 'POST',
				'content' => json_encode($parametros),
				),
			);			
			
			
			
			//executa a requisição  
			/*
			set_error_handler(
				create_function(
					'$severity, $message, $file, $line',
					'throw new ErrorException($message, $severity, $severity, $file, $line);'
				)
			);
			*/
			
			try {
				$_RETORNO['retorno'] = file_get_contents($_URL_WS.$_METODO, false, stream_context_create($dados));
				
				#{"Lote":0,"StatusMensagem":"Mensagens enviadas","Status":1,"CreditosGastos":0,"Destinatarios":[{"Celular":51981883677,"Sequencia":603867194}]}
			}
			catch (Exception $e) {
				$_RETORNO['fl_erro'] = "S";
				$_RETORNO['retorno'] = utf8_encode($e->getMessage());
			}



			#print_r($_RETORNO); exit;

			#restore_error_handler();
			
			if ($_RETORNO['fl_erro'] == "N")
			{
				// checa retorno
				$_RETORNO['retorno'] = json_decode($_RETORNO['retorno']);
				if (!(json_last_error() === JSON_ERROR_NONE))
				{
					switch (json_last_error()) 
					{
						case JSON_ERROR_NONE:
							#'(JSON) Não ocorreu nenhum erro';
							$_RETORNO['retorno'] = ($_RETORNO['retorno']);
						break;
						case JSON_ERROR_DEPTH:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) A profundidade máxima da pilha foi excedida');
						break;
						case JSON_ERROR_STATE_MISMATCH:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Inválido ou mal formado');
						break;
						case JSON_ERROR_CTRL_CHAR:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
						break;
						case JSON_ERROR_SYNTAX:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Erro de sintaxe');
						break;
						case JSON_ERROR_UTF8:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
						break;
						default:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Erro não identificado');
						break;
					}
				}
				else
				{
					$_RETORNO['retorno'] = ($_RETORNO['retorno']);
				}
			}
		}
		
		return $_RETORNO;
	}	
	
	
	private function wsSendSMSBKP20220622($para, $assunto, $conteudo)
	{
		$_URL_WS  = "https://ws.smsdigital.com.br/";
		$_METODO  = "sms/envio";
		$_USUARIO = "sisfceee";
		$_SENHA   = "c8ml09";
		$_RETORNO['fl_erro'] = "N";
		$_RETORNO['retorno'] = "";
		
		if(intval($para) <= 0)
		{
			$_RETORNO['fl_erro'] = "S";
			$_RETORNO['retorno'] = utf8_encode('Numero do telefone invalido');
		}
		elseif(trim($conteudo) == "")
		{
			$_RETORNO['fl_erro'] = "S";
			$_RETORNO['retorno'] = utf8_encode('Conteudo invalido');
		}		
		else
		{
			//parâmetros para envio
			$parametros = Array(
							'destinatarios' => Array(intval($para)),
							'assunto'       => $assunto,
							'mensagem'      => $conteudo,
							'data'          => date('dd/MM/yyyy')
						);		
			
			//dados da requisição
			$dados = Array(
						'http' => Array(
									'header'  => Array('Content-type: application/json','Authorization: Basic '.base64_encode($_USUARIO.':'.$_SENHA)),
									'method'  => 'POST',
									'content' => json_encode($parametros)
								),
					);		
			
			
			//executa a requisição  
			set_error_handler(
				create_function(
					'$severity, $message, $file, $line',
					'throw new ErrorException($message, $severity, $severity, $file, $line);'
				)
			);		
			
			try {
				$_RETORNO['retorno'] = file_get_contents($_URL_WS.$_METODO, FALSE, stream_context_create($dados));	
				
				#{"Lote":0,"StatusMensagem":"Mensagens enviadas","Status":1,"CreditosGastos":0,"Destinatarios":[{"Celular":51981883677,"Sequencia":603867194}]}
			}
			catch (Exception $e) {
				$_RETORNO['fl_erro'] = "S";
				$_RETORNO['retorno'] = utf8_encode($e->getMessage());
			}

			print_r($_RETORNO); exit;

			restore_error_handler();
			
			if ($_RETORNO['fl_erro'] == "N")
			{
				// checa retorno
				$_RETORNO['retorno'] = json_decode($_RETORNO['retorno']);
				if (!(json_last_error() === JSON_ERROR_NONE))
				{
					switch (json_last_error()) 
					{
						case JSON_ERROR_NONE:
							#'(JSON) Não ocorreu nenhum erro';
							$_RETORNO['retorno'] = ($_RETORNO['retorno']);
						break;
						case JSON_ERROR_DEPTH:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) A profundidade máxima da pilha foi excedida');
						break;
						case JSON_ERROR_STATE_MISMATCH:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Inválido ou mal formado');
						break;
						case JSON_ERROR_CTRL_CHAR:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
						break;
						case JSON_ERROR_SYNTAX:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Erro de sintaxe');
						break;
						case JSON_ERROR_UTF8:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
						break;
						default:
							$_RETORNO['fl_erro'] = "S";
							$_RETORNO['retorno'] = utf8_encode('(JSON) Erro não identificado');
						break;
					}
				}
				else
				{
					$_RETORNO['retorno'] = ($_RETORNO['retorno']);
				}
			}
		}
		
		return $_RETORNO;
	}
}
?>