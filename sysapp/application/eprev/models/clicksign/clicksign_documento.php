<?php
class clicksign_documento extends Controller
{
	var $FL_DEBUG 	  = FALSE;
	var $CD_AMBIENTE  = "PRODUCAO";
	#var $CD_AMBIENTE  = "DESENVOLVIMENTO";
	var $API_AMBIENTE = null;
	var $API_URL      = null;
	var $API_TOKEN    = null;	
	
	function __construct()
    {
        parent::Controller();
		
		
    	$this->load->model('clicksign/clicksign_model');
    	$this->load->model('clicksign/clicksign_documento_model');
		$this->load->model('projetos/Digitalizado_model');
		$this->load->model('projetos/documento_recebido_model');
		
		$ar_cfg = $this->clicksign_model->getConfig($this->CD_AMBIENTE);
		
		$this->API_AMBIENTE = trim($ar_cfg['ds_ambiente']);	
		$this->API_URL      = trim($ar_cfg['ds_url']);		
		$this->API_TOKEN    = trim($ar_cfg['ds_token']);			
    }
	
    function index($id_documento = "")
    {
        CheckLogin();
		$args = Array();
		$data = Array();		
		$data["id_documento"] = trim($id_documento);
		
		$data['tipo_solicitacao'] = $this->Digitalizado_model->carregar_tipo_solicitacao();
		
        $this->load->view('clicksign/clicksign_documento/index.php', $data);
    }
	
    function listar()
    {
        CheckLogin();
     
		$args = Array();
		$data = Array();
		$result = null;

		$args["fl_status"]               = $this->input->post("fl_status", TRUE);
		$args["dt_inclusao_ini"]         = $this->input->post("dt_inclusao_ini", TRUE);
		$args["dt_inclusao_fim"]         = $this->input->post("dt_inclusao_fim", TRUE);
		
		manter_filtros($args);

		$args["id_doc"]                        = $this->input->post("id_doc", TRUE);
		$args['cd_usuario_documento_gerencia'] = $this->input->post("cd_usuario_documento_gerencia", TRUE);   
		$args['cd_usuario_documento']          = $this->input->post("cd_usuario_documento", TRUE);  
		
		#print_r($args); exit;
		
		if($this->input->post("fl_documento_admin", TRUE) == "S")
		{
			$this->clicksign_documento_model->listarAdmin($result, $args);
		}
		else
		{
			$this->clicksign_documento_model->listar($result, $args);
		}
		
		
		$data['collection'] = $result->result_array();
		
		$this->load->view('clicksign/clicksign_documento/index_result', $data);

    }

	function recusado($id_documento)
	{
		$_RETORNO['fl_erro']     = "N";
		$_RETORNO['retorno']     = array();
		
		if(CheckLogin())
		{
			try {
				
				$ch = curl_init("https://www.e-prev.com.br/cieprev/index.php/clicksign/clicksign/documento_situacao");			   
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,"token=9f815795413e11f45cf36720bd73e00f"."&cd_documento=".$id_documento);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','cache-control: no-cache'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				curl_close($ch);

				#print_r($response);	exit;				

				$_RETORNO['retorno'] = $response;
				#{"code":0,"data":{"campaignId":"65086211","messageId":"33926066","status":2},"message":""}

			}
			catch (Exception $e) {
				$_RETORNO['fl_erro'] = "S";
				$_RETORNO['retorno'] = utf8_encode($e->getMessage());
			}

			if ($_RETORNO['fl_erro'] == "N")
			{
				// checa retorno
				$_RETORNO['retorno'] = json_decode($_RETORNO['retorno'],true);
				
				#print_r($_RETORNO['retorno']);
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
		else
		{
			$_RETORNO['fl_erro'] = "S";
			$_RETORNO['retorno'] = "Usuario nao logado";			
		}
		
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($_RETORNO['retorno']);
	}
	
    function protocolo()
    {
        CheckLogin();
		$ar_documento = array();
	
		$arq_selecionado  = $this->input->post("arq_selecionado", TRUE);
		$doc_selecionado  = $this->input->post("doc_selecionado", TRUE);
		$part_selecionado = $this->input->post("part_selecionado", TRUE);
		
		$ar_arq  = explode(",",$arq_selecionado);
		$ar_doc  = explode(",",$doc_selecionado);
		$ar_part = array_map("explodePipe", explode(",",$part_selecionado));
		
		$args   = Array();
		$data   = Array();
		$result = null;		
		
		#echo "<PRE>";
		#print_r($_POST);
		#print_r($ar_arq);
		#print_r($ar_doc);
		#print_r($ar_part);
		
		#### PREPARA LISTA DE DOCUMENTOS PARA O PROTOCOLO ####
		$nr_conta = 0;
		foreach($ar_arq as $item)
		{
			$ar_documento[$nr_conta]['id_doc']                     = $item;
			$ar_documento[$nr_conta]['url_download']               = "";
			
			#### CONFIGURAÇÃO PARA O PROTOCOLO ####
			$ar_documento[$nr_conta]['cd_documento_recebido_item'] = 0;
			$ar_documento[$nr_conta]['cd_tipo_doc']                = $ar_doc[$nr_conta];
			$ar_documento[$nr_conta]['cd_empresa']                 = $ar_part[$nr_conta][0];
			$ar_documento[$nr_conta]['cd_registro_empregado']      = $ar_part[$nr_conta][1];
			$ar_documento[$nr_conta]['seq_dependencia']            = $ar_part[$nr_conta][2];
			$ar_documento[$nr_conta]['nome']                       = $ar_part[$nr_conta][3];
			$ar_documento[$nr_conta]['ds_observacao']              = "";
			$ar_documento[$nr_conta]['nr_folha']                   = 1;
			$ar_documento[$nr_conta]['arquivo']                    = md5($ar_documento[$nr_conta]['id_doc']).".pdf";
			$ar_documento[$nr_conta]['arquivo_nome']               = "";
			$ar_documento[$nr_conta]['cd_usuario_cadastro']        = usuario_id();
			
			$id_documento = $ar_documento[$nr_conta]['id_doc'] ;
			
			$_RETORNO['fl_erro']     = "N";
			$_RETORNO['retorno']     = array();
			
			try {
				
				$ch = curl_init("https://www.e-prev.com.br/cieprev/index.php/clicksign/clicksign/documento_situacao");			   
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,"token=9f815795413e11f45cf36720bd73e00f"."&cd_documento=".$id_documento);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded','cache-control: no-cache'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				curl_close($ch);

				#print_r($response);	exit;				

				$_RETORNO['retorno'] = $response;
				#{"code":0,"data":{"campaignId":"65086211","messageId":"33926066","status":2},"message":""}

			}
			catch (Exception $e) {
				$_RETORNO['fl_erro'] = "S";
				$_RETORNO['retorno'] = utf8_encode($e->getMessage());
			}

			if ($_RETORNO['fl_erro'] == "N")
			{
				// checa retorno
				$_RETORNO['retorno'] = json_decode($_RETORNO['retorno'],true);
				
				#print_r($_RETORNO['retorno']);
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
					
					if($_RETORNO['retorno']['fl_status'] == "CLOSED")
					{
						$ar_documento[$nr_conta]['url_download'] = $_RETORNO['retorno']['url_down'];
						$ar_documento[$nr_conta]['arquivo_nome'] = $_RETORNO['retorno']['ds_documento'];
					}
				}
			}		
			
			#print_r($_RETORNO['retorno']);

			$nr_conta++;
		}
		
		#echo "<PRE><HR>";print_r($ar_documento);exit;
		
		#### FAZ DOWNLOAD DOS ARQUIVOS PARA O PROTOCOLO ####
		foreach($ar_documento as $item)
		{
			$dir = "../cieprev/up/documento_recebido/";
			$url = $item['url_download'];
			$arq = $item['arquivo'];
	
			#### SALVA ARQUIVO NA PASTA ###
			$ch = curl_init($url);
			$fp = fopen($dir.$arq, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);	

			#echo "https://www.e-prev.com.br/cieprev/up/documento_recebido/".$arq."<BR>";
		}			

		#### GERA PROTOCOLO ####
		if(count($ar_documento) > 0)
		{
			$args = Array();
            $result = null;
			
			$args['cd_documento_recebido']             = 0;
			$args["cd_documento_recebido_tipo"]        = 1; #Central de Atendimento
			$args["cd_documento_recebido_grupo_envio"] = "";
			$args["cd_documento_recebido_tipo_solic"]  = $this->input->post("cd_documento_recebido_tipo_solic", TRUE);
			$args["cd_usuario_cadastro"]               = usuario_id();
			$args["cd_usuario"]                        = usuario_id();
			
			$cd_documento_recebido = $this->documento_recebido_model->incluir_protocolo($result, $args);		
			
			if($cd_documento_recebido > 0)
			{
				#### ADICIONA DOCUMENTOS ####				
				foreach($ar_documento as $args)
				{
					$args["cd_documento_recebido"] = $cd_documento_recebido;
					$args["cd_usuario"] = $args['cd_usuario_cadastro'];
					$this->documento_recebido_model->adicionar_documento($result, $args);

					#### VINCULA O DOCUMENTO ASSINADO AO PROTOCOLO INTERNO ####
					$this->clicksign_documento_model->salvarProtocoloInterno($args);
				}
				
				redirect( "ecrm/cadastro_protocolo_interno/detalhe/".$cd_documento_recebido, "refresh" );
			}
		}		

    }	
}
?>