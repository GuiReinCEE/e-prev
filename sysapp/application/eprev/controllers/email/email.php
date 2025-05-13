<?php
class Email extends Controller
{
	var $token_acesso;
	
	function __construct()
    {
        parent::Controller();

        #PROGRAMA VB
        #6d49f10f233c4d5d25ec915085559f4a
		$this->token_acesso[] = md5('integracaoenviaremail');
		#PROGRAMA ELETRO
        #7a2584226d7f72f3a83920be80b2f33e
		$this->token_acesso[] = md5('integracaoenviaremaileletro');
		#PROGRAMA SIMULADOR 
        #7a2584226d7f72f3a83920be80b2f33e
		$this->token_acesso[] = md5('integracaoenviaremailsimulador');
		#PROGRAMA APP 
        #c1656f543fa6bc16aae79d1f128933f5
		$this->token_acesso[] = md5('integracaoenviaremailapp');
		#EXTRANET 
		$this->token_acesso[] = '0b8e7f1c2a504ab245f24d3f6cea2f2e';
		
		$this->load->model('email/email_model');
    }

    public function emailEletroIncluir()
	{
		$this->emailIncluir();
	}
	
	public function emailIncluir()
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
		$ar_ret["cd_email"] = "0";
	
		$args["token"]                 = $this->input->post("token", TRUE); 
		$args["dt_agendado"]           = $this->input->post("dt_agendado", TRUE);
		$args["de"]                    = utf8_decode($this->input->post("de", TRUE));
		$args["para"]                  = $this->input->post("para", TRUE);
		$args["cc"]                    = $this->input->post("cc", TRUE);
		$args["cco"]                   = $this->input->post("cco", TRUE);
		$args["assunto"]               = utf8_decode($this->input->post("assunto", TRUE));
		$args["conteudo"]              = utf8_decode($this->input->post("conteudo", TRUE));
		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		$args["usuario"]               = $this->input->post("usuario", TRUE);
		$args["fl_comprova"]           = $this->input->post("fl_comprova", TRUE); #### S: sim / N: nao
		$args["tp_email"]              = $this->input->post("tp_email", TRUE); ### F: fundacao / A: atendimento 
		$args["formato"]               = $this->input->post("formato", TRUE); ### TEXT / HTML
		$args["qt_anexo"]              = intval($this->input->post("qt_anexo", TRUE));
		$args["cd_evento"]             = intval($this->input->post("cd_evento", TRUE));
		
		$args["ar_anexo"]              = array();
		
		if(intval($args["qt_anexo"]) > 0)
		{
			$nr_idx   = 0;
			$nr_conta = 1;
			$nr_fim   = intval($args["qt_anexo"]);
			
			while($nr_conta <= $nr_fim)
			{
				$args["ar_anexo"][$nr_idx]['arquivo_nome'] = utf8_decode($this->input->post('arquivo_nome_'.$nr_conta, TRUE));
				$args["ar_anexo"][$nr_idx]['arquivo']      = $this->input->post('arquivo_'.$nr_conta, TRUE);
				
				$nr_conta++;
				$nr_idx++;
			}
		}
		
		#$args["ar_anexo"] = $this->input->post("ar_anexo", TRUE); ### ARQUIVOS ANEXOS EM BASE64 | CAMPOS (NOME, CONTEUDO)
		#$args["ar_anexo"] = json_decode($args["ar_anexo"], true);
		
		#print_r($args); exit;
		
		if(in_array($args["token"], $this->token_acesso))
		{
			$fl_campo_obrigatorio = TRUE;
			
			if(trim($args["de"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório DE não informado");
			}
			elseif((trim($args["para"]) == "") and (trim($args["cc"]) == ""))
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório PARA e CC não informado");
			}			
			elseif(trim($args["assunto"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório ASSUNTO não informado");
			}	
			elseif(trim($args["cd_evento"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório CÓD DO EVENTO não informado");
			}			
			elseif(trim($args["conteudo"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$campo = "";
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório CONTEUDO não informado");
			}
			elseif(trim($args["tp_email"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório TP_EMAIL não informado");
			}	
			elseif(!in_array(trim($args["tp_email"]), array("F","A")))
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório TP_EMAIL (F / A)");
			}			
			elseif(trim($args["formato"]) == "")
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório FORMATO não informado");
			}
			elseif(!in_array(trim($args["formato"]), array("TEXT", "HTML")))
			{
				$fl_campo_obrigatorio = FALSE;
				$ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório FORMATO (TEXT / HTML)");
			}				
			#### VALIDAR PARTICIPANTE 
			
			if($fl_campo_obrigatorio)
			{
				$cd_email = intval($this->email_model->emailIncluir($args));
			
				if(intval($cd_email) > 0)
				{
					$ar_ret["cd_email"] = intval($cd_email);
					$ar_ret["retorno"]  = "E-mail registrado com sucesso";
				}
				else
				{
					$ar_ret["fl_erro"] = "S";
					$ar_ret["cd_erro"] = "2";
					$ar_ret["retorno"] = utf8_encode("ERRO: ao inserir os dados do e-mail");						
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
			$ar_ret["retorno"] = utf8_encode("ERRO: acesso não permitido");			
		}
		
		echo json_encode($ar_ret);			
	}
	
	public function anexoListar($token = "", $cd_email = 0)
    {
		#http://www.e-prev.com.br/cieprev/index.php/email/email/anexoListar/6d49f10f233c4d5d25ec915085559f4a/6368637
		$args    = Array();
		$data    = Array();
		$ar_ret  = Array();
		$result  = null;
		
		$ar_ret["fl_erro"]  = "N";
		$ar_ret["cd_erro"]  = "0";
		$ar_ret["retorno"]  = "";			
		$ar_ret["qt_anexo"] = "0";
		$ar_ret["ar_anexo"] = Array();
	
		$args["token"]    = trim($token); 
		$args["cd_email"] = intval($cd_email); 
		
		#print_r($args); exit;
		
		if(in_array($args["token"], $this->token_acesso))
		{		
			if(intval($args["cd_email"]) > 0)
			{
				$this->email_model->anexoListar($result, $args);
				$data = $result->result_array();
				
				if (count($data) > 0)
				{
					$ar_ret["qt_anexo"] = count($data);
					
					foreach($data as $item)
					{
						$ar_cad = Array();
						foreach($item as $key => $value)
						{
							$ar_cad[$key] = ($key == "arquivo" ? $value : utf8_encode($value));
						}
						$ar_ret["ar_anexo"][] = $ar_cad;
					}
				}
				else
				{
					$ar_ret["fl_erro"]  = "S";
					$ar_ret["cd_erro"]  = "1";
					$ar_ret["retorno"]  = utf8_encode("ERRO: e-mail não encontrado");						
				}
			}
			else
			{
				$ar_ret["fl_erro"]  = "S";
				$ar_ret["cd_erro"]  = "2";
				$ar_ret["retorno"]  = utf8_encode("ERRO: código e-mail não informado");			
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

	function emailLogEnvio()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['token']    = utf8_decode($this->input->post('token', true)); 
		$args['tp_email'] = utf8_decode($this->input->post('tp_email', true)); 
		$args['log']      = utf8_decode($this->input->post('log', true)); 
		
		#$this->cpuscanner_model->setUsuarioEprev($result, $args);
		
		if(in_array($args["token"], $this->token_acesso))
		{
			
		}
		
		echo json_encode($args);

	}	
}
?>