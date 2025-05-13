<?php
class clicksign extends Controller
{
	var $AR_TOKEN     = Array();
	var $FL_DEBUG 	  = FALSE;
	var $CD_AMBIENTE  = "PRODUCAO";
	#var $CD_AMBIENTE  = "DESENVOLVIMENTO";
	var $API_AMBIENTE = null;
	var $API_URL      = null;
	var $API_TOKEN    = null;	
	
	function __construct()
    {
        parent::Controller();
		
		$this->AR_TOKEN[] = md5('integracaoenviarCLICKSIGN');       #83eaa4b96dfed1a3a92238b43fe90cec
		$this->AR_TOKEN[] = md5('integracaoenviarCLICKSIGNeprev');  #9f815795413e11f45cf36720bd73e00f
		$this->AR_TOKEN[] = md5('integracaoenviarCLICKSIGNeletro'); #7276d8ca1a90585cffb624260eb76876
		
    	$this->load->model('clicksign/clicksign_model');
		
		$ar_cfg = $this->clicksign_model->getConfig($this->CD_AMBIENTE);
		
		$this->API_AMBIENTE = trim($ar_cfg['ds_ambiente']);	
		$this->API_URL      = trim($ar_cfg['ds_url']);		
		$this->API_TOKEN    = trim($ar_cfg['ds_token']);			
    }
	
	private function submit_clicksign_simples($url, $fl_patch = false)
	{
		$ch = curl_init($url);			   
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		if($fl_patch)
		{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$_RT_STATUS = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		#print_r($response);
		#echo "<hr>";
		#if(!array_key_exists("errors", $_AR_DOC["ARRAY"]))
		
		if(intval($_RT_STATUS) == 200)
		{	
			$a = $response;
			$b = json_decode($response,TRUE);
			if (!(json_last_error() === JSON_ERROR_NONE))
			{
				switch (json_last_error()) 
				{
					case JSON_ERROR_DEPTH:
						echo utf8_encode('(JSON) A profundidade máxima da pilha foi excedida');
					break;
					case JSON_ERROR_STATE_MISMATCH:
						echo utf8_encode('(JSON) Inválido ou mal formado');
					break;
					case JSON_ERROR_CTRL_CHAR:
						echo utf8_encode('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
					break;
					case JSON_ERROR_SYNTAX:
						echo utf8_encode('(JSON) Erro de sintaxe');
					break;
					case JSON_ERROR_UTF8:
						echo utf8_encode('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
					break;
					default:
						echo utf8_encode('(JSON) Erro não identificado');
					break;
				}
			}			
		}
		else
		{
			$b["errors"] = array("RESPONSE => ".intval($_RT_STATUS));
			$a = json_encode($b);
		}
		
		return array("JSON" => $a, "ARRAY" => $b);
	}	
	
	private function submit_clicksign($data_string,$url)
	{
		$ch = curl_init($url);			   
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		#print_r($response);
		#echo "<hr>";
		
		$a = $response;
		$b = json_decode($response,TRUE);
		if (!(json_last_error() === JSON_ERROR_NONE))
		{
			switch (json_last_error()) 
			{
				case JSON_ERROR_DEPTH:
					echo utf8_encode('(JSON) A profundidade máxima da pilha foi excedida');
				break;
				case JSON_ERROR_STATE_MISMATCH:
					echo utf8_encode('(JSON) Inválido ou mal formado');
				break;
				case JSON_ERROR_CTRL_CHAR:
					echo utf8_encode('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
				break;
				case JSON_ERROR_SYNTAX:
					echo utf8_encode('(JSON) Erro de sintaxe');
				break;
				case JSON_ERROR_UTF8:
					echo utf8_encode('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
				break;
				default:
					echo utf8_encode('(JSON) Erro não identificado');
				break;
			}
		}			
		
		return array("JSON" => $a, "ARRAY" => $b);
	}	

	private function assinador($_NOME, $_EMAIL, $_TELEFONE, $TIPO_ASSINATURA, $TIPO_TOKEN, $_GRUPO, $_CD_DOCUMENTO)
	{
		echo ($this->FL_DEBUG ? "ASSINADOR <BR>" : "");
		
		if(in_array(strtolower(trim($TIPO_ASSINATURA)), array("sign","witness","validator")))
		{
			$TIPO_ASSINATURA = strtolower(trim($TIPO_ASSINATURA));
		}
		else
		{
			$TIPO_ASSINATURA = "sign";
		}	

		if(in_array(strtolower(trim($TIPO_TOKEN)), array("sms","email")))
		{
			$TIPO_TOKEN = strtolower(trim($TIPO_TOKEN));
		}
		else
		{
			$TIPO_TOKEN = "sms";
		}		
		
		if (intval($_GRUPO) < 1)
		{
			$_GRUPO = 1;
		}		

/*		
		#### AJUSTE PARA USUARIO FNEVES - NAO FUNCIONA SMS ####
		if (in_array(trim(strtolower($_EMAIL)), array("fneves@familiaprevidencia.com.br","fneves@eletroceee.com.br")))
		{
			$TIPO_TOKEN = "email";
		}		
*/

		#### BUSCA CONFIGURACAO DO SIGNATARIO CADASTRADO ####
		$ar_sig_config = $this->clicksign_model->getSignatarioConfig($_EMAIL, $TIPO_TOKEN);		
		
		#### CHECA OPCAO FORCADA DE TOKEN POR EMAIL ####
		if(array_key_exists("fl_token_email_forcado", $ar_sig_config)) 
		{
			if(isset($ar_sig_config["fl_token_email_forcado"]))
			{
				if(trim($ar_sig_config["fl_token_email_forcado"]) == "S")
				{
					$TIPO_TOKEN = "email";
				}
			}
		}		
		
		$ar_sig["id_signatario"] = "";
		if($this->CD_AMBIENTE == "PRODUCAO")
		{
			#### BUSCA SIGNATARIO CADASTRADO ####
			$ar_sig = $this->clicksign_model->getSignatario($_EMAIL, $TIPO_TOKEN);
			
			if(!array_key_exists("id_signatario", $ar_sig)) 
			{
				$ar_sig["id_signatario"] = "";
			}			
		}

		if(trim($ar_sig["id_signatario"]) != "")
		{
			$ar_ass["ARRAY"]['signer']['key'] = $ar_sig["id_signatario"];
		}
		else
		{
			#### CRIAR ASSINADOR ####
			$ds_json = '
						{
							"signer": {
								"email": "'.$_EMAIL.'",
								'.($TIPO_TOKEN == "email" ? '"auths": ["email"],' : '"auths": ["sms"],').'
								'.($TIPO_TOKEN == "email" ? '' : '"phone_number": "'.$_TELEFONE.'",').'
								'.(trim($_NOME) != '' ? '"name": "'.$_NOME.'",' : '').'
								"has_documentation": true,
								"send_email":"true"
							}
						}	
					   ';		
			
			$ar_ass = $this->submit_clicksign($ds_json, $this->API_URL."/signers?access_token=".$this->API_TOKEN);			
		}

		echo ($this->FL_DEBUG ? print_r($ar_ass,TRUE) : "");
		
		#### ADD ASSINADOR ####
		$ds_json = '
					{
						"list": {
							"document_key": "'.$_CD_DOCUMENTO.'",
							"signer_key": "'.$ar_ass["ARRAY"]['signer']['key'].'",
							"sign_as": "'.$TIPO_ASSINATURA.'",
							"group": "'.$_GRUPO.'"
						}
					}	
				   ';	
		$ar_add_ass = $this->submit_clicksign($ds_json, $this->API_URL."/lists?access_token=".$this->API_TOKEN);	
		echo ($this->FL_DEBUG ? print_r($ar_add_ass,TRUE) : "");	
		
		return array('ar_ass' => $ar_ass, 'ar_add_ass' => $ar_add_ass);
	}	
	
	private function notificar($id_signer)
	{
		echo ($this->FL_DEBUG ? "ENVIA EMAIL ASSINADOR <BR>" : "");
		#### ENVIA EMAIL ASSINADOR ####
		$ds_json = '
						{
						  "request_signature_key": "'.$id_signer.'"
						}	
					   ';
		$ar_notifica = $this->submit_clicksign($ds_json, $this->API_URL."/notifications?access_token=".$this->API_TOKEN);
		echo ($this->FL_DEBUG ? print_r($ar_notifica,TRUE) : "");		
		
		return $ar_notifica;
	}	

	#######################################################################################################################

	function documento()
	{
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		#### GRAVA LOG EXECUCAO ####
		$ds_arq_log = "/u/www/_a/_log-clicksign-eletro-post-".date("Ymd").".txt";
		$ob_arq = fopen($ds_arq_log, "a");
		fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
		fwrite($ob_arq, print_r($_POST,true).chr(10));
		fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
							
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]      = "N";
		$ar_ret["cd_erro"]      = "0";
		$ar_ret["retorno"]      = "";
		$ar_ret["cd_documento"] = "";
	
		#### IDENTIFICACAO ####
		$args["token"]          = $this->input->post("token", TRUE); 
		$args["usuario"]        = $this->input->post("usuario", TRUE);
	
		#### DOCUMENTO ####
		$args["path"]           = $this->input->post("path", TRUE);
		$args["content_base64"] = $this->input->post("content_base64", TRUE);
		$args["deadline_at"]    = $this->input->post("deadline_at", TRUE);
		$args["deadline_hr"]    = (trim($this->input->post("deadline_hr", TRUE)) == "" ? "23:59:59" : trim($this->input->post("deadline_hr", TRUE)));
		
		#### SIGNATARIOS ####
		$args["qt_signatario"]  = $this->input->post("qt_signatario", TRUE);
		$ar_user = Array();
		for($i=1; $i <= intval($args["qt_signatario"]); $i++)
		{
			$ar_user[] = array(
								"nome"  => trim(strtoupper(trim($this->input->post('ds_nome_'.$i)))),
								"email" => strtolower(trim($this->input->post('ds_email_'.$i))),
								"nr_telefone" => trim(str_replace("(","",str_replace(")","",(trim($this->input->post('nr_telefone_'.$i)))))),
								"tp_assinatura" => strtolower(trim($this->input->post('tp_assinatura_'.$i))),
								"tp_token"  => strtolower(trim($this->input->post('tp_token_'.$i))),
								"grupo" => strtolower(trim($this->input->post('nr_grupo_'.$i)))
							); 
		}	

		#### VALIDAR SIGNATARIOS #####
		/*
		criar regra de validação dos signatários
		
		email => OBRIGATÓRIO
		
		nr_telefone => OBRIGATÓRIO se tp_assinatura = SMS
		
		*/
		
		#if($args["token"] == $this->token_acesso_eletro)
		if (in_array($args["token"], $this->AR_TOKEN))
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(trim($args["path"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campo obrigatorio PATH nao informado");
			}	
			elseif(trim($args["content_base64"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$campo = "";
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatorio CONTENT_BASE64 nao informado");
			}
			elseif(trim($args["deadline_at"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$campo = "";
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatorio DEADLINE_AT nao informado");
			}			
			elseif(intval($args["qt_signatario"]) < 1)
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatorio QT_SIGNATARIO nao informado");
			}
			elseif(count($ar_user) == 0)
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatorio DADOS SIGNATARIOS nao informado");
			}			
			
			if($fl_campo_obrigatorio)
			{
				#### CRIA DOCUMENTO ####
				$ds_json = '
							{
								"document": {
									"path":"'.trim($args["path"]).'",
									"content_base64":"data:application/pdf;base64,'.trim($args["content_base64"]).'",
									"deadline_at":"'.trim($args["deadline_at"]).' T'.(trim($args["deadline_hr"])).'-03:00",
									"remind_interval":"2",
									"auto_close":"true",
									"sequence_enabled":"true",
									"signable_group":null,
									"locale":"pt-BR"
								}
							}
						   ';
   
				$_AR_DOC = $this->submit_clicksign($ds_json, $this->API_URL."/documents?access_token=".$this->API_TOKEN);
				
				#### GRAVA LOG EXECUCAO ####
				$ds_arq_log = "/u/www/_a/_log-clicksign-eletro-".date("Ymd").".txt";
				$ob_arq = fopen($ds_arq_log, "a");
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
				fwrite($ob_arq, print_r($args,true).chr(10));	
				fwrite($ob_arq, print_r($ar_user,true).chr(10));					
				fwrite($ob_arq, print_r($ds_json,true).chr(10));		
				fwrite($ob_arq, print_r($_AR_DOC,true).chr(10));
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
					
									
				
				if(!array_key_exists("errors", $_AR_DOC["ARRAY"]))
				{				
					#### ADICIONAR SIGNATARIOS ####
					$_AR_ASSINADOR = Array();
					foreach($ar_user as $ar_item)
					{
						$_AR_ASSINADOR[] = $this->assinador($ar_item['nome'], $ar_item['email'], $ar_item['nr_telefone'], $ar_item['tp_assinatura'], $ar_item['tp_token'], $ar_item['grupo'], trim($_AR_DOC["ARRAY"]['document']['key']));
					}				
				
					#### NOTIFICAR SIGNATARIOS ####
					foreach($_AR_ASSINADOR as $ar_item)
					{
						$this->notificar($ar_item['ar_add_ass']["ARRAY"]['list']['request_signature_key']);
					}				
					
					#### GRAVA LOG EXECUCAO ####
					$ds_arq_log = "/u/www/_a/_log-clicksign-eletro-".date("Ymd").".txt";
					$ob_arq = fopen($ds_arq_log, "a");
					fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
					fwrite($ob_arq, print_r($_AR_ASSINADOR,true).chr(10));
					fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
					
					
					$cd_documento = $_AR_DOC["ARRAY"]['document']['key'];
				
					if(trim($cd_documento) != "")
					{
						$ar_ret["cd_documento"] = trim($cd_documento);
						$ar_ret["retorno"]  = "DOCUMENTO registrado com sucesso";
						
						#### GRAVA DADOS DO DOCUMENTO ####
						$_AR_LOG['AR_DOC']    = $_AR_DOC;
						$_AR_LOG['POST']      = print_r($_POST,true);
						$_AR_LOG['dt_limite'] = trim($args["deadline_at"]).' '.trim($args["deadline_hr"]);
						$this->clicksign_model->salvarDocumento($_AR_LOG);
					}
					else
					{
						$ar_ret["fl_erro"] = "S";
						$ar_ret["cd_erro"] = "2";
						$ar_ret["retorno"] = utf8_encode("ERRO: ao inserir os dados na CLICKSIGN");						
					}
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "3";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao inserir os dados na CLICKSIGN");
					$ar_ret["ds_erro_click"] = (implode(" | ", $_AR_DOC["ARRAY"]["errors"]));
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
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");			
		}
		
		echo json_encode($ar_ret);			
	}	

	function documento_situacao()
	{
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]      = "N";
		$ar_ret["cd_erro"]      = "0";
		$ar_ret["retorno"]      = "";
		$ar_ret["cd_documento"] = "";
	
		$args["token"]          = $this->input->post("token", TRUE); 
		$args["cd_documento"]   = $this->input->post("cd_documento", TRUE);
		$args["usuario"]        = $this->input->post("usuario", TRUE);
		
		#if($args["token"] == $this->token_acesso_eletro)
		if (in_array($args["token"], $this->AR_TOKEN))
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(trim($args["cd_documento"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campo obrigatorio CODIGO DOCUMENTO nao informado");
			}	

			if($fl_campo_obrigatorio)
			{
				$_AR_DOC = $this->submit_clicksign_simples($this->API_URL."/documents/".trim($args["cd_documento"])."?access_token=".$this->API_TOKEN);
				
				#### GRAVA LOG EXECUCAO ####
				$ds_arq_log = "/u/www/_a/_log-clicksign-eletro-situacao-".date("Ymd").".txt";
				$ob_arq = fopen($ds_arq_log, "a");
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
				fwrite($ob_arq, print_r($args,true).chr(10));		
				fwrite($ob_arq, print_r($_AR_DOC,true).chr(10));
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));			
				
				if(!array_key_exists("errors", $_AR_DOC["ARRAY"]))
				{				
					$ar_signatario = array();
					foreach($_AR_DOC["ARRAY"]["document"]["signers"] as $item)
					{
						$fl_sign = 0;
						
						if(array_key_exists("signature", $item))
						{
							$fl_sign = count($item["signature"]);
						}
						
						$ar_signatario[] = array(
													"id_signatario" => $item['request_signature_key'],
													"grupo"         => $item["group"],
													"nome"          => $item["name"],
													"email"         => $item["email"],
													"celular"       => $item["phone_number"],
													"fl_sign"       => ($fl_sign > 0 ? "S" : "N"),
													"url_sign"      => $item["url"]
										   );
					}				
					
					
					$cd_documento = $_AR_DOC["ARRAY"]['document']['key'];			
					
					$url_down = "";
					if(array_key_exists("signed_file_url", $_AR_DOC["ARRAY"]["document"]["downloads"]))
					{
						$url_down = $_AR_DOC["ARRAY"]["document"]["downloads"]["signed_file_url"];
					}
					
					/*
					Possíveis valores:
					- running: Documento em processo de assinatura.
					- closed: Documento finalizado.
					- canceled: Documento cancelado.
					*/	
					
					$fl_evento = trim(strtoupper($_AR_DOC["ARRAY"]['document']['events'][0]['name']));

					$ds_status = "Erro: status não identificado";
					$fl_status = trim(strtoupper($_AR_DOC["ARRAY"]['document']['status']));
					if($fl_status == "RUNNING")
					{
						$ds_status = "Documento em processo de assinatura";
					}
					elseif($fl_status == "CANCELED")
					{
						$ds_status = "Documento cancelado";
					}
					elseif(($fl_evento == "DEADLINE") AND ($fl_status == "CLOSED"))
					{
						$ds_status = "Documento cancelado";
						$fl_status = "CANCELED";
					}					
					elseif($fl_status == "CLOSED")
					{
						$ds_status = "Documento finalizado";
					}						
					
					if(trim($cd_documento) != "")
					{
						$ar_ret["cd_documento"] = trim($args["cd_documento"]);
						$ar_ret["fl_status"]    = $fl_status;
						$ar_ret["fl_evento"]    = $fl_evento; 
						$ar_ret["ds_status"]    = $ds_status; 
						$ar_ret["url_down"]		= $url_down; 
						$ar_ret["ar_sign"]      = $ar_signatario;
						$ar_ret["retorno"]      = "Busca DOCUMENTO realizada com sucesso";
					}
					else
					{
						$ar_ret["fl_erro"] = "S";
						$ar_ret["cd_erro"] = "2";
						$ar_ret["retorno"] = utf8_encode("ERRO: ao consultar os dados na CLICKSIGN");						
					}
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "3";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao consultar os dados na CLICKSIGN");		
					$ar_ret["ds_erro_click"] = (implode(" | ", $_AR_DOC["ARRAY"]["errors"]));
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
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");			
		}
		
		echo json_encode($ar_ret);			
	}	

	function documento_down()
	{
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]      = "N";
		$ar_ret["cd_erro"]      = "0";
		$ar_ret["retorno"]      = "";
		$ar_ret["cd_documento"] = "";
	
		$args["token"]          = $this->input->post("token", TRUE); 
		$args["cd_documento"]   = $this->input->post("cd_documento", TRUE);
		$args["usuario"]        = $this->input->post("usuario", TRUE);
		
		#if($args["token"] == $this->token_acesso_eletro)
		if (in_array($args["token"], $this->AR_TOKEN))
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(trim($args["cd_documento"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campo obrigatorio CODIGO DOCUMENTO nao informado");
			}	

			if($fl_campo_obrigatorio)
			{
				$_AR_DOC = $this->submit_clicksign_simples($this->API_URL."/documents/".trim($args["cd_documento"])."?access_token=".$this->API_TOKEN);
				
				#### GRAVA LOG EXECUCAO ####
				$ds_arq_log = "/u/www/_a/_log-clicksign-eletro-down-".date("Ymd").".txt";
				$ob_arq = fopen($ds_arq_log, "a");
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
				fwrite($ob_arq, print_r($args,true).chr(10));		
				fwrite($ob_arq, print_r($_AR_DOC,true).chr(10));
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));			

				if(!array_key_exists("errors", $_AR_DOC["ARRAY"]))
				{
					$cd_documento = $_AR_DOC["ARRAY"]['document']['key'];			
					
					$arq_base64 = "";
					$ar_file = $_AR_DOC["ARRAY"]["document"]["downloads"];
					if(array_key_exists("signed_file_url", $ar_file))
					{
						$url_down = $_AR_DOC["ARRAY"]["document"]["downloads"]["signed_file_url"];
						
						$ob_curl = curl_init($_AR_DOC["ARRAY"]["document"]["downloads"]["signed_file_url"]);
						curl_setopt($ob_curl, CURLOPT_RETURNTRANSFER, true);
						$rt_curl = curl_exec($ob_curl);
						$arq_base64 =  base64_encode($rt_curl);
						curl_close($ob_curl);					
					}
					
					if(trim($arq_base64) != "")
					{
						$ar_ret["cd_documento"] = trim($args["cd_documento"]);
						$ar_ret["url_down"]		= $url_down; 
						$ar_ret["ar_base64"]    = $arq_base64;
						$ar_ret["retorno"]      = "Busca DOCUMENTO realizada com sucesso";
					}
					else
					{
						$ar_ret["fl_erro"] = "S";
						$ar_ret["cd_erro"] = "2";
						$ar_ret["retorno"] = utf8_encode("ERRO: ao consultar os dados na CLICKSIGN");						
					}
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "3";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao consultar os dados na CLICKSIGN");	
					$ar_ret["ds_erro_click"] = (implode(" | ", $_AR_DOC["ARRAY"]["errors"]));
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
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");			
		}
		
		echo json_encode($ar_ret);			
	}	

	function documento_notificar()
	{
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]      = "N";
		$ar_ret["cd_erro"]      = "0";
		$ar_ret["retorno"]      = "";
		$ar_ret["cd_documento"] = "";
	
		$args["token"]          = $this->input->post("token", TRUE); 
		$args["cd_documento"]   = $this->input->post("cd_documento", TRUE);
		$args["id_signatario"]  = $this->input->post("id_signatario", TRUE);
		$args["usuario"]        = $this->input->post("usuario", TRUE);
		
		#if($args["token"] == $this->token_acesso_eletro)
		if (in_array($args["token"], $this->AR_TOKEN))
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(trim($args["cd_documento"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campo obrigatorio CODIGO DOCUMENTO nao informado");
			}	
			elseif(trim($args["id_signatario"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campo obrigatorio ID SIGNATARIO nao informado");
			}			

			if($fl_campo_obrigatorio)
			{
				$_AR_DOC = $this->notificar($args["id_signatario"]);
				
				#### GRAVA LOG EXECUCAO ####
				$ds_arq_log = "/u/www/_a/_log-clicksign-eletro-notificar-".date("Ymd").".txt";
				$ob_arq = fopen($ds_arq_log, "a");
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
				fwrite($ob_arq, print_r($args,true).chr(10));		
				fwrite($ob_arq, print_r($_AR_DOC,true).chr(10));
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));			

				if(!array_key_exists("errors", $_AR_DOC["ARRAY"]))
				{
					$ar_ret["cd_documento"]  = trim($args["cd_documento"]);
					$ar_ret["id_signatario"] = trim($args["id_signatario"]);
					$ar_ret["retorno"]       = "NOTICACAO realizada com sucesso";
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "2";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao consultar os dados na CLICKSIGN");	
					$ar_ret["ds_erro_click"] = (implode(" | ", $_AR_DOC["ARRAY"]["errors"]));
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

	function documento_cancelar()
	{
		$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
		
		#print_r($_POST); exit;
		
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]      = "N";
		$ar_ret["cd_erro"]      = "0";
		$ar_ret["retorno"]      = "";
		$ar_ret["cd_documento"] = "";
	
		$args["token"]          = $this->input->post("token", TRUE); 
		$args["cd_documento"]   = $this->input->post("cd_documento", TRUE);
		$args["usuario"]        = $this->input->post("usuario", TRUE);
		
		#if($args["token"] == $this->token_acesso_eletro)
		if (in_array($args["token"], $this->AR_TOKEN))
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(trim($args["cd_documento"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: Campo obrigatorio CODIGO DOCUMENTO nao informado");
			}	

			if($fl_campo_obrigatorio)
			{
				$_AR_DOC = $this->submit_clicksign_simples($this->API_URL."/documents/".trim($args["cd_documento"])."/cancel?access_token=".$this->API_TOKEN, TRUE);
				
				#### GRAVA LOG EXECUCAO ####
				$ds_arq_log = "/u/www/_a/_log-clicksign-eletro-cancelar-".date("Ymd").".txt";
				$ob_arq = fopen($ds_arq_log, "a");
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));
				fwrite($ob_arq, print_r($args,true).chr(10));		
				fwrite($ob_arq, print_r($_AR_DOC,true).chr(10));
				fwrite($ob_arq, '########################################################################################'.chr(10).chr(10));			

				if(!array_key_exists("errors", $_AR_DOC["ARRAY"]))
				{
					$ar_ret["cd_documento"]  = trim($args["cd_documento"]);
					$ar_ret["retorno"]       = "Documento CANCELADO com sucesso";
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "2";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao consultar os dados na CLICKSIGN");	
					$ar_ret["ds_erro_click"] = (implode(" | ", $_AR_DOC["ARRAY"]["errors"]));
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
}
?>