<?php
class familiavendas_solicitacao extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function permissao()
    {
        if(gerencia_in(array('GCM')))
    	{
    		return true;
    	}
        else
        {
            return false;
        }
    }

	public function index()
	{
		if($this->permissao())
		{
			$data = array();

			$this->load->view('ecrm/familiavendas_solicitacao/index',$data);
		}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar()
	{
		$this->load->model('familiavendas/solicitacao_model');

		$args = array(
			'dt_inclusao_ini'	=> $this->input->post('dt_inclusao_ini', TRUE),
			'dt_inclusao_fim'	=> $this->input->post('dt_inclusao_fim', TRUE),
			'nr_protocolo'		=> $this->input->post('nr_protocolo', TRUE),
			'tp_status'         => $this->input->post('tp_status',TRUE)
		);

		manter_filtros($args);

		$data['collection']  = $this->solicitacao_model->listar($args);

		$this->load->view('ecrm/familiavendas_solicitacao/index_result', $data);
	}

	public function cadastro($cd_app_solicitacao)
	{
		if($this->permissao())
		{
			$this->load->model('familiavendas/solicitacao_model');
			
			$data['ar_instituidor']  = $this->solicitacao_model->instituidor();
			$data['ar_estado_civil'] = $this->solicitacao_model->estado_civil();

			$data['row']  = $this->solicitacao_model->cadastro($cd_app_solicitacao);

			$this->load->view('ecrm/familiavendas_solicitacao/cadastro', $data);
		}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_cadastro()
	{	
		if($this->permissao())
		{	
			$cd_app_solicitacao = $this->input->post('cd_app_solicitacao', TRUE);

			$this->load->model('familiavendas/solicitacao_model');

			$args = array(		
				'ds_nome'					       => $this->input->post('ds_nome', TRUE),
				'cd_instituidor'				   => $this->input->post('cd_instituidor', TRUE),
				'dt_nascimento'				       => $this->input->post('dt_nascimento', TRUE),
				'ds_associado'			  	       => $this->input->post('ds_associado', TRUE),
				'ds_vinculo_associado'	           => $this->input->post('ds_vinculo_associado', TRUE),
				'ds_vinculo_grau'	               => $this->input->post('ds_vinculo_grau', TRUE),
				'nr_contrib_primeira'		       => app_decimal_para_db($this->input->post('nr_contrib_primeira', TRUE)),
				'nr_contrib_mensal'			       => app_decimal_para_db($this->input->post('nr_contrib_mensal', TRUE)),
				'nr_contrib_extra_inicial'         => app_decimal_para_db($this->input->post('nr_contrib_extra_inicial', TRUE)),
				'tp_forma_pagamento_primeira'	   => $this->input->post('tp_forma_pagamento_primeira', TRUE),
				'tp_forma_pagamento_mensal'		   => $this->input->post('tp_forma_pagamento_mensal', TRUE),
				'tp_forma_pagamento_extra_inicial' => $this->input->post('tp_forma_pagamento_extra_inicial', TRUE),
				'ds_cpf'				  	    => $this->input->post('ds_cpf', TRUE),	
				'cpf_indicacao'				  	    => $this->input->post('cpf_indicacao', TRUE),	
				'ds_rg'						    => $this->input->post('ds_rg', TRUE),
				'ds_orgao_expedidor'		    => $this->input->post('ds_orgao_expedidor', TRUE),
				'dt_expedicao'				    => $this->input->post('dt_expedicao', TRUE),
				'ds_nome_pai'				    => $this->input->post('ds_nome_pai', TRUE),
				'ds_nome_mae'				    => $this->input->post('ds_nome_mae', TRUE),
				'ds_naturalidade'			    => $this->input->post('ds_naturalidade', TRUE),
				'ds_nacionalidade'			    => $this->input->post('ds_nacionalidade', TRUE),
				'ds_nome_representante_legal'   => $this->input->post('ds_nome_representante_legal', TRUE),
				'ds_cpf_representante_legal'    => $this->input->post('ds_cpf_representante_legal', TRUE),
				'email_representante_legal'     => $this->input->post('email_representante_legal', TRUE),
				'telefone_representante_legal'  => $this->input->post('telefone_representante_legal', TRUE),				
				'ds_cep'					    => $this->input->post('ds_cep', TRUE),
				'ds_endereco'				    => $this->input->post('ds_endereco', TRUE),
				'nr_endereco'				    => $this->input->post('nr_endereco', TRUE),
				'ds_complemento'			    => $this->input->post('ds_complemento', TRUE),
				'ds_bairro'					    => $this->input->post('ds_bairro', TRUE),
				'ds_cidade'					    => $this->input->post('ds_cidade', TRUE),
				'ds_uf'						    => $this->input->post('ds_uf', TRUE),
				'ds_celular'				    => $this->input->post('ds_celular', TRUE),
				'ds_telefone'				    => $this->input->post('ds_telefone', TRUE),
				'ds_email'					    => $this->input->post('ds_email', TRUE),
				'fl_ppe'					    => $this->input->post('fl_ppe', TRUE),
				'fl_usperson'				    => $this->input->post('fl_usperson', TRUE),
				'fl_tributacao'				    => $this->input->post('fl_tributacao', TRUE),
				'ds_nome_folha_pagamento'       => $this->input->post('ds_nome_folha_pagamento', TRUE),
				'cpf_folha_pagamento'           => $this->input->post('cpf_folha_pagamento', TRUE),
				'ds_empresa_folha_pagamento'    => $this->input->post('ds_empresa_folha_pagamento', TRUE),
				'email_folha_pagamento'         => $this->input->post('email_folha_pagamento', TRUE),
				'telefone_folha_pagamento'      => $this->input->post('telefone_folha_pagamento', TRUE),
				'ds_nome_debito_conta'          => $this->input->post('ds_nome_debito_conta', TRUE),
				'cpf_debito_conta'              => $this->input->post('cpf_debito_conta', TRUE),
				'email_debito_conta'            => $this->input->post('email_debito_conta', TRUE),
				'telefone_debito_conta'         => $this->input->post('telefone_debito_conta', TRUE),
				'agencia_debito_conta'          => $this->input->post('agencia_debito_conta', TRUE),
				'conta_corrente_debito_conta'   => $this->input->post('conta_corrente_debito_conta', TRUE),				
				'beneficiario_1_nome' 			=> $this->input->post('beneficiario_1_nome',TRUE), 
				'beneficiario_1_dt_nascimento' 	=> $this->input->post('beneficiario_1_dt_nascimento',TRUE), 
				'beneficiario_1_sexo' 			=> $this->input->post('beneficiario_1_sexo',TRUE), 
				'beneficiario_1_cpf' 			=> $this->input->post('beneficiario_1_cpf',TRUE), 
				'beneficiario_1_beneficio' 	    => $this->input->post('beneficiario_1_beneficio',TRUE), 
				'beneficiario_2_nome' 			=> $this->input->post('beneficiario_2_nome',TRUE), 
				'beneficiario_2_dt_nascimento' 	=> $this->input->post('beneficiario_2_dt_nascimento',TRUE), 
				'beneficiario_2_sexo' 			=> $this->input->post('beneficiario_2_sexo',TRUE), 
				'beneficiario_2_cpf' 			=> $this->input->post('beneficiario_2_cpf',TRUE), 
				'beneficiario_2_beneficio' 		=> $this->input->post('beneficiario_2_beneficio',TRUE), 
				'beneficiario_3_nome' 			=> $this->input->post('beneficiario_3_nome',TRUE), 
				'beneficiario_3_dt_nascimento' 	=> $this->input->post('beneficiario_3_dt_nascimento',TRUE), 
				'beneficiario_3_sexo' 			=> $this->input->post('beneficiario_3_sexo',TRUE), 
				'beneficiario_3_cpf' 			=> $this->input->post('beneficiario_3_cpf',TRUE), 
				'beneficiario_3_beneficio' 	    => $this->input->post('beneficiario_3_beneficio',TRUE), 
				'beneficiario_4_nome' 			=> $this->input->post('beneficiario_4_nome',TRUE), 
				'beneficiario_4_dt_nascimento' 	=> $this->input->post('beneficiario_4_dt_nascimento',TRUE), 
				'beneficiario_4_sexo' 			=> $this->input->post('beneficiario_4_sexo',TRUE),	
				'beneficiario_4_cpf' 			=> $this->input->post('beneficiario_4_cpf',TRUE), 
				'beneficiario_4_beneficio' 	    => $this->input->post('beneficiario_4_beneficio',TRUE), 
				'ds_nome_vendedor' 			    => $this->input->post('ds_nome_vendedor',TRUE),	
				'ds_vendedor_celular' 		    => $this->input->post('ds_vendedor_celular',TRUE),	
				'ds_vendedor_email' 		    => $this->input->post('ds_vendedor_email',TRUE),	
				'dt_recebimento'       			=> $this->input->post('dt_recebimento',TRUE),	
				'indicacao_interna_nome'        => $this->input->post('indicacao_interna_nome',TRUE),	
				'indicacao_interna_cpf'       	=> $this->input->post('indicacao_interna_cpf',TRUE),
				'ds_nome_social'            	=> $this->input->post('ds_nome_social',TRUE),
				'fl_lgpd'                   	=> $this->input->post('fl_lgpd',TRUE),
				'cd_usuario'				  	=> $this->session->userdata('codigo')
			);

			$this->solicitacao_model->atualizar($cd_app_solicitacao, $args);

			redirect('ecrm/familiavendas_solicitacao/cadastro/'.$cd_app_solicitacao);
		}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function acompanhamento($cd_app_solicitacao)
	{
		if($this->permissao())
		{
			$this->load->model('familiavendas/solicitacao_model');

			$data['collection'] = $this->solicitacao_model->listar_acompanhamento($cd_app_solicitacao);

			$data['row']  = $this->solicitacao_model->acompanhamento($cd_app_solicitacao);

			$this->load->view('ecrm/familiavendas_solicitacao/acompanhamento', $data);
		}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_acompanhamento()
	{
		if($this->permissao())
		{
			$this->load->model('familiavendas/solicitacao_model');

			$cd_app_solicitacao = $this->input->post('cd_app_solicitacao', TRUE);

			$args = array(
				'ds_app_solicitacao_acompanhamento' => $this->input->post('ds_app_solicitacao_acompanhamento', TRUE),
				'cd_usuario'   						=> $this->session->userdata('codigo'),
				'cd_app_solicitacao'				=> $cd_app_solicitacao
			);

			$this->solicitacao_model->salvar_acompanhamento($args);

			redirect('ecrm/familiavendas_solicitacao/acompanhamento/'.$cd_app_solicitacao);
		}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    public function em_analise($cd_app_solicitacao)
    {
    	if($this->permissao())
		{
	        $this->load->model('familiavendas/solicitacao_model');

	        $cd_usuario = $this->session->userdata('codigo');

	        $this->solicitacao_model->em_analise($cd_app_solicitacao, $cd_usuario);

	        redirect('ecrm/familiavendas_solicitacao/cadastro/'.$cd_app_solicitacao);
    	}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function concluir($cd_app_solicitacao)
    {
    	if($this->permissao())
		{
	        $this->load->model('familiavendas/solicitacao_model');

	        $cd_usuario = $this->session->userdata('codigo');

	        $this->solicitacao_model->concluir($cd_app_solicitacao, $cd_usuario);

	        redirect('ecrm/familiavendas_solicitacao/cadastro/'.$cd_app_solicitacao);
    	}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
    
    public function cancelar($cd_app_solicitacao)
    {
    	if($this->permissao())
		{
	        $this->load->model('familiavendas/solicitacao_model');

	        $cd_usuario = $this->session->userdata('codigo');

	        $this->solicitacao_model->cancelar($cd_app_solicitacao,$cd_usuario);

	        redirect('ecrm/familiavendas_solicitacao/cadastro/'.$cd_app_solicitacao);
    	}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
	
	
    public function assinar($cd_app_solicitacao)
    {
    	if($this->permissao())
		{
	        $this->load->model('familiavendas/solicitacao_model');

	        $cd_usuario = $this->session->userdata('codigo');
			$ar_cad  = $this->solicitacao_model->cadastro($cd_app_solicitacao);
			
			#echo "<PRE>"; 
			#print_r($ar_cad);
			
			$i = 0;
			$ar_sign = Array();
			if($ar_cad["fl_menor"] == "N")
			{	
				$ar_sign[$i]["nome"]    = $ar_cad["ds_nome"];
				$ar_sign[$i]["email"]   = $ar_cad["ds_email"];
				$ar_sign[$i]["celular"] = $ar_cad["ds_celular"];
			}
			else
			{
				$ar_sign[$i]["nome"]    = $ar_cad["ds_nome_representante_legal"];
				$ar_sign[$i]["email"]   = $ar_cad["email_representante_legal"];
				$ar_sign[$i]["celular"] = $ar_cad["telefone_representante_legal"];
			}

			if(($ar_cad['tp_forma_pagamento_primeira'] == 'FOL') OR ($ar_cad['tp_forma_pagamento_mensal'] == 'FOL') OR ($ar_cad['tp_forma_pagamento_extra_inicial'] == 'FOL'))
			{
				$i++;
				$ar_sign[$i]["nome"]    = $ar_cad["ds_nome_folha_pagamento"];
				$ar_sign[$i]["email"]   = $ar_cad["email_folha_pagamento"];
				$ar_sign[$i]["celular"] = $ar_cad["telefone_folha_pagamento"];				
			}
			
			if(($ar_cad['tp_forma_pagamento_primeira'] == 'DCC') OR ($ar_cad['tp_forma_pagamento_mensal'] == 'DCC') OR ($ar_cad['tp_forma_pagamento_extra_inicial'] == 'DCC'))
			{
				$i++;
				$ar_sign[$i]["nome"]    = $ar_cad["ds_nome_debito_conta"];
				$ar_sign[$i]["email"]   = $ar_cad["email_debito_conta"];
				$ar_sign[$i]["celular"] = $ar_cad["telefone_debito_conta"];
			}
			
			$i++;
			$ar_sign[$i]["nome"]    = $ar_cad["ds_nome_vendedor"];
			$ar_sign[$i]["email"]   = $ar_cad["ds_vendedor_email"];
			$ar_sign[$i]["celular"] = $ar_cad["ds_vendedor_celular"];		

			#### ELIMINA SIGNATARIOS DUPLICADOS (NOME, EMAIL E TELEFONE IGUAIS) ####
			$ar_sign = array_map("unserialize", array_unique(array_map("serialize", $ar_sign)));			
			
			$ds_signatario = '"qt_signatario" : '.count($ar_sign).',';
			$x = 1;
			foreach($ar_sign as $item)
			{
				if((trim($item["nome"]) == "") OR (trim($item["email"]) == "") OR (trim($item["celular"]) == ""))
				{
					echo "ERRO: DADOS DOS SIGNATARIOS INCOMPLETOS"; 
					exit;
				}
				else
				{
					$ds_signatario.= '
										"ds_nome_'.$x.'"   : "'.$item["nome"].'",
										"ds_email_'.$x.'" : "'.$item["email"].'",
										"nr_telefone_'.$x.'" : "'.trim(str_replace(" ","",str_replace("(","",str_replace(")","",$item["celular"])))).'",
									 ';
					
					$x++;
				}
			}
			
			#### BUSCA FORMULARIO PREENCHIDO ####
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://app.eletroceee.com.br/srvfamiliavendas/index.php/solicitacao/formulario_inscricao/'.$ar_cad['cd_app_solicitacao_md5']);
			curl_setopt($ch, CURLOPT_POST,   1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$_RETORNO_PDF   = curl_exec($ch);
			$_RT_STATUS = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close ($ch);
			
			if(intval($_RT_STATUS) == 200)
			{
				$finfo = new finfo(FILEINFO_MIME);
				if(strtolower(trim($finfo->buffer($_RETORNO_PDF))) != 'application/pdf; charset=binary')
				{
					echo 'ERRO NO FORMULARIO PDF '.$finfo->buffer($_RETORNO_PDF);
					EXIT;
				}
				
				$dt_limite = new DateTime('+20 day');
				
				$data_string = '
					{ 
						"token"         : "83eaa4b96dfed1a3a92238b43fe90cec",
						"usuario"       : "'.$this->session->userdata('usuario').'",
						"deadline_at"   : "'.$dt_limite->format('Y-m-d').'",
						"deadline_hr"   : "23:59:59",
						"path"          : "/VENDAS/'.date('Ymd').'/PEDIDO_INSCRICAO-APP-'.str_replace(" ","_",trim($ar_cad["ds_nome"])).'-'.date("YmdHis").'.pdf",
						'.$ds_signatario.'
						"content_base64" : "'.base64_encode($_RETORNO_PDF).'"
					}			
				';
				#echo $data_string;
				
				
				#### ENCAMINHA DOCUMENTO PARA ASSINATURA ####
				$ch = curl_init("https://www.e-prev.com.br/cieprev/index.php/clicksign/clicksign/documento");			   
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$_RETORNO = curl_exec($ch);
				$_RT_STATUS = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);	

				#print_r($_RT_STATUS);	
				#print_r($_RETORNO);		

				if(intval($_RT_STATUS) == 200)
				{
					#$finfo = new finfo(FILEINFO_MIME); ECHO strtolower(trim($finfo->buffer($_RETORNO)));
					
					$_JSON = json_decode($_RETORNO,TRUE);
					if (!(json_last_error() === JSON_ERROR_NONE))
					{
						switch (json_last_error()) 
						{
							case JSON_ERROR_DEPTH:
								echo '(JSON) A profundidade maxima da pilha foi excedida';						
							break;
							case JSON_ERROR_STATE_MISMATCH:
								echo '(JSON) Invalido ou mal formado';						
							break;
							case JSON_ERROR_CTRL_CHAR:
								echo '(JSON) Erro de caractere de controle, possivelmente codificado incorretamente';							
							break;
							case JSON_ERROR_SYNTAX:
								echo '(JSON) Erro de sintaxe';							
							break;
							case JSON_ERROR_UTF8:
								echo '(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente';						
							break;
							default:
								echo '(JSON) Erro nao identificado';						
							break;
						}
						exit;
					}		
					else
					{
						#{"fl_erro":"S","cd_erro":"4","retorno":"ERRO: acesso nao permitido","cd_documento":""}
						if($_JSON["fl_erro"] == "S")
						{
							echo $_JSON["cd_erro"]." - ".$_JSON["retorno"];
							exit;
						}
						else
						{
							#### GRAVA ID DOCUMENTO CLICKSIGN ####
							$this->solicitacao_model->setIDdocAssinar($cd_app_solicitacao, $_JSON["cd_documento"]);
							
							redirect('ecrm/familiavendas_solicitacao/cadastro/'.$cd_app_solicitacao);
						}
					}
				}
				else 
				{
					echo "ERRO: CLICKSIGN"; EXIT;
				}
			}
			else 
			{
				echo "ERRO: FORMULARIO PDF"; EXIT;
			}			
    	}
		else
		{
		    exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
}