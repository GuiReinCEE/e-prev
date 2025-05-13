<?php
class Pedido_aposentadoria_ceeeprev extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	var $cd_doc;
	var $nome_doc;

	var $validador;

	private function get_permissao()
    {
        if(gerencia_in(array('GCM')))
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Cristiano
        else if($this->session->userdata('codigo') == 170)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_edicao()
    {
    	#Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Nalu Cristina Ribeiro das Neves	
        else if($this->session->userdata('codigo') == 75)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        #Kenia Oliveira Barbosa
        else if($this->session->userdata('codigo') == 429)
        {
            return TRUE;
        }
        #Gabriel Eliseu Lima da Luz
        else if($this->session->userdata('codigo') == 312)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Cristiano
        else if($this->session->userdata('codigo') == 170)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_ambiente()
	{
		if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') OR ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
		{
		    $ambiente = 'PRODUCAO';

			$this->validador    = 'ct@familiaprevidencia.com.br';

			//$this->validador    = 'lrodriguez@familiaprevidencia.com.br';
		}
		else
		{
		    $ambiente = 'DESENVOLVIMENTO';

		    $this->validador    = 'lrodriguez@familiaprevidencia.com.br';
		}

		return $ambiente;
	}

	private function rest_click($url, $post)
	{
		$ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $retorno_json = curl_exec($ch);

		$retorno_json_decode = json_decode($retorno_json, TRUE);

		if (!(json_last_error() === JSON_ERROR_NONE))
		{
			//$this->resultado['error']['status'] = 1;

			switch (json_last_error()) 
			{
				case JSON_ERROR_DEPTH:
					//$this->resultado['error']['mensagem'][] = utf8_encode('(JSON) A profundidade máxima da pilha foi excedida');
					echo '[1] - Erro no serviço.';
				break;
				case JSON_ERROR_STATE_MISMATCH:
					//$this->resultado['error']['mensagem'][] = utf8_encode('(JSON) Inválido ou mal formado');
					echo '[2] - Erro no serviço.';
				break;
				case JSON_ERROR_CTRL_CHAR:
					//$this->resultado['error']['mensagem'][] = utf8_encode('(JSON) Erro de caractere de controle, possivelmente codificado incorretamente');
					echo '[3] - Erro no serviço.';
				break;
				case JSON_ERROR_SYNTAX:
					//$this->resultado['error']['mensagem'][] = utf8_encode('(JSON) Erro de sintaxe');
					echo  '[4] - Erro no serviço.';
				break;
				case JSON_ERROR_UTF8:
					//$this->resultado['error']['mensagem'][] = utf8_encode('(JSON) Caracteres UTF-8 malformado, possivelmente codificado incorretamente');
					echo '[5] - Erro no serviço.';
				break;
				default:
					//$this->resultado['error']['mensagem'][] = utf8_encode('(JSON) Erro não identificado');
					echo '[6] - Erro no serviço.';
				break;
			}

			return array();
		}
		else
		{
			if(isset($retorno_json_decode['errors']) AND count($retorno_json_decode['errors']) > 0)
			{
				//$this->resultado['error']['status'] = 1;

				//$this->resultado['error']['mensagem'] = $retorno_json_decode['errors'];
				echo '[7] - Erro no serviço.';

				return array();
			}
			else if(isset($retorno_json_decode['error']) AND count($retorno_json_decode['error']) > 0)
			{
				//$this->resultado['error']['status'] = 1;

				//$this->resultado['error']['mensagem'] = $retorno_json_decode['error'];
				echo '[8] - Erro no serviço.';

				return array();
			}
			else
			{
				return array(
					'json'  => $retorno_json,
					'array' => $retorno_json_decode,
				);
			}
		}
	}

	private function assinar($ds_url, $ds_token, $email, $auths, $doc_key, $sign_as, $group, $cd_contrato_digital, $tp_assinatura, $celular = '', $nome = '', &$url = '')
	{
		$data = '
		{
		  "signer": {
			"email": "'.$email.'",
			'.(trim($celular) != '' ? '"phone_number": "'.$celular.'",' : '').'
			"auths": ["'.$auths.'"],
			'.(trim($nome) != '' ? '"name": "'.$nome.'",' : '').'
			"has_documentation": true
		  }
		}';

		$assinatura = $this->rest_click(trim($ds_url).'/signers?access_token='.trim($ds_token), $data);

		if(count($assinatura) > 0)
		{
			$data = '
			{
			  "list": {
				"document_key": "'.$doc_key.'",
				"signer_key": "'.$assinatura['array']['signer']['key'].'",
				"sign_as": "'.$sign_as.'",
				"group": "'.$group.'"
			  }
			}';

			$assinatura_list = $this->rest_click(trim($ds_url).'/lists?access_token='.trim($ds_token), $data);

			if(count($assinatura_list) > 0)
			{
				$args = array(
	    			'cd_contrato_digital' => $cd_contrato_digital,
	    			'tp_assinatura'       => $tp_assinatura,
	    			'id_assinador'        => $assinatura['array']['signer']['key'],
	    			'id_assinatura'       => $assinatura_list['array']['list']['request_signature_key'],
	    			'ds_url_assinatura'   => $assinatura_list['array']['list']['url'],
	    			'json_assinatura'     => $assinatura_list['json']
	    		);

	    		$this->pedido_aposentadoria_ceeeprev_model->salvar_contrato_digital_assinatura($args);

	    		if(trim($tp_assinatura) == 'P')
	    		{
	    			$ds_json = '
							{
							  "request_signature_key": "'.$assinatura_list['array']['list']['request_signature_key'].'"
							}	
					   ';

	    			$this->rest_click(trim($ds_url).'/notifications?access_token='.trim($ds_token), $ds_json);
	    		}

	    		return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

    private function get_percentual_adiantamento()
    {
    	return array(
    		array('value' => '1', 'text' => '1%'),
    		array('value' => '2', 'text' => '2%'),
    		array('value' => '3', 'text' => '3%'),
    		array('value' => '4', 'text' => '4%'),
    		array('value' => '5', 'text' => '5%'),
    		array('value' => '6', 'text' => '6%'),
    		array('value' => '7', 'text' => '7%'),
    		array('value' => '8', 'text' => '8%'),
    		array('value' => '9', 'text' => '9%'),
    		array('value' => '10', 'text' => '10%'),
    		array('value' => '11', 'text' => '11%'),
    		array('value' => '12', 'text' => '12%'),
    		array('value' => '13', 'text' => '13%'),
    		array('value' => '14', 'text' => '14%'),
    		array('value' => '15', 'text' => '15%'),
    		array('value' => '16', 'text' => '16%'),
    		array('value' => '17', 'text' => '17%'),
    		array('value' => '18', 'text' => '18%'),
    		array('value' => '19', 'text' => '19%'),
    		array('value' => '20', 'text' => '20%'),
        );
    }

    private function get_status()
    {
    	return array(
    		array('value' => 'S', 'text' => 'Solicitado'),
    		array('value' => 'A', 'text' => 'Em Análise'),
    		array('value' => 'S', 'text' => 'Em Assinatura'),
    		array('value' => 'I', 'text' => 'Deferido'),
    		array('value' => 'D', 'text' => 'Indeferido')
    	);
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
	{
		if($this->get_permissao())
		{
			$data = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia,
                'status'                => $this->get_percentual_adiantamento()
            );

			$this->load->view('ecrm/pedido_aposentadoria_ceeeprev/index', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar($data = array())
	{
		$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

		$args = array(
			'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
            'dt_encaminhamento_ini' => $this->input->post('dt_encaminhamento_ini', TRUE),
            'dt_encaminhamento_fim' => $this->input->post('dt_encaminhamento_fim', TRUE),
            'tp_status'             => $this->input->post('tp_status', TRUE),
            'fl_deferido'           => $this->input->post('fl_deferido', TRUE),
            'fl_indeferido'         => $this->input->post('fl_indeferido', TRUE),
		);

        manter_filtros($args);

		$data['collection'] = $this->pedido_aposentadoria_ceeeprev_model->listar($args);
		
		$this->load->view('ecrm/pedido_aposentadoria_ceeeprev/index_result', $data);
	}

	public function cadastro($cd_pedido_aposentadoria_ceeeprev = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

			$data['estado_civil']            = $this->pedido_aposentadoria_ceeeprev_model->get_estado_civil();
			$data['banco']                   = $this->pedido_aposentadoria_ceeeprev_model->get_instituicao_financeira();
			$data['percentual_adiantamento'] = $this->get_percentual_adiantamento();

			if(intval($cd_pedido_aposentadoria_ceeeprev) == 0)
			{
				$data['row'] = array();
			}
			else
			{
				$data['row'] = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);
			}

			$this->load->view('ecrm/pedido_aposentadoria_ceeeprev/cadastro', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

			$cd_pedido_aposentadoria_ceeeprev = $this->input->post('cd_pedido_aposentadoria_ceeeprev');

        	$args = array(
        		'ds_nome'                  => $this->input->post('ds_nome'),
        		'dt_nascimento'            => $this->input->post('dt_nascimento'),
        		'ds_cpf'                   => $this->input->post('ds_cpf'),
        		'ds_estado_civil'          => $this->input->post('ds_estado_civil'),
        		'ds_naturalidade'          => $this->input->post('ds_naturalidade'),
        		'ds_nacionalidade'         => $this->input->post('ds_nacionalidade'),
        		'ds_endereco'              => $this->input->post('ds_endereco'),
        		'nr_endereco'              => $this->input->post('nr_endereco'),
        		'ds_complemento_endereco'  => $this->input->post('ds_complemento_endereco'),
        		'ds_bairro'                => $this->input->post('ds_bairro'),
        		'ds_cidade'                => $this->input->post('ds_cidade'),
        		'ds_uf'                    => $this->input->post('ds_uf'),
        		'ds_cep'                   => $this->input->post('ds_cep'),
        		'ds_telefone1'             => $this->input->post('ds_telefone1'),
        		'ds_telefone2'             => $this->input->post('ds_telefone2'),
        		'ds_celular'               => $this->input->post('ds_celular'),
        		'ds_email1'                => $this->input->post('ds_email1'),
        		'ds_email2'                => $this->input->post('ds_email2'),
        		'ds_banco'                 => $this->input->post('ds_banco'),
        		'ds_agencia'               => $this->input->post('ds_agencia'),
        		'ds_conta'                 => $this->input->post('ds_conta'),
        		'fl_adiantamento_cip'      => $this->input->post('fl_adiantamento_cip'),
        		'nr_adiantamento_cip'      => floatval($this->input->post('nr_adiantamento_cip')),
        		'fl_reversao_beneficio'    => $this->input->post('fl_reversao_beneficio'),
        		'fl_politicamente_exposta' => $this->input->post('fl_politicamente_exposta'),
        		'fl_us_person'             => $this->input->post('fl_us_person'),
        		'arquivo_conta_bancaria'   => $this->input->post('arquivo_conta_bancaria'),
        		'arquivo_doc_identidade'   => $this->input->post('arquivo_doc_identidade'),
        		'arquivo_doc_cpf'          => $this->input->post('arquivo_doc_cpf'),
        		'arquivo_recisao_contrato' => $this->input->post('arquivo_recisao_contrato'),
        		'arquivo_simulacao'        => $this->input->post('arquivo_simulacao'),
        		'cd_usuario'               => $this->session->userdata('codigo')       
        	);

        	if(intval($cd_pedido_aposentadoria_ceeeprev) == 0)
        	{
        		//$this->pedido_aposentadoria_ceeeprev_model->salvar($args);
        	}
        	else 
        	{
        		$this->pedido_aposentadoria_ceeeprev_model->atualizar($cd_pedido_aposentadoria_ceeeprev, $args);
        	}

        	redirect('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function dependente($cd_pedido_aposentadoria_ceeeprev, $cd_pedido_aposentadoria_ceeeprev_dependente = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

			$data['cadastro']        = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);
			$data['grau_parentesco'] = $this->pedido_aposentadoria_ceeeprev_model->get_grau_parentesco();
			$data['estado_civil']    = $this->pedido_aposentadoria_ceeeprev_model->get_estado_civil();
			$data['collection']      = $this->pedido_aposentadoria_ceeeprev_model->listar_dependente($cd_pedido_aposentadoria_ceeeprev);

			if(intval($cd_pedido_aposentadoria_ceeeprev_dependente) == 0)
			{
				$data['row'] = array(
					'cd_pedido_aposentadoria_ceeeprev_dependente' => $cd_pedido_aposentadoria_ceeeprev_dependente,
					'ds_nome'                                     => '', 
            		'dt_nascimento'                               => '', 
            		'ds_sexo'                                     => '',
            		'ds_grau_parentesco'                          => '', 
            		'ds_estado_civil'                             => '', 
            		'fl_incapaz'                                  => '', 
            		'fl_estudante'                                => '', 
            		'fl_guarda_juridica'                          => '', 
            		'fl_previdenciario'                           => '', 
            		'fl_imposto_renda'                            => ''
				);
			}
			else
			{
				$data['row'] = $this->pedido_aposentadoria_ceeeprev_model->carrega_dependente($cd_pedido_aposentadoria_ceeeprev_dependente);
			}

			$this->load->view('ecrm/pedido_aposentadoria_ceeeprev/dependente', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_dependente()
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

			$cd_pedido_aposentadoria_ceeeprev            = $this->input->post('cd_pedido_aposentadoria_ceeeprev');
			$cd_pedido_aposentadoria_ceeeprev_dependente = $this->input->post('cd_pedido_aposentadoria_ceeeprev_dependente');

        	$args = array(
        		'ds_nome'             => $this->input->post('ds_nome'),
        		'dt_nascimento'       => $this->input->post('dt_nascimento'),
        		'ds_sexo'             => $this->input->post('ds_sexo'),
        		'ds_grau_parentesco'  => $this->input->post('ds_grau_parentesco'),
        		'ds_estado_civil'     => $this->input->post('ds_estado_civil'),
        		'fl_incapaz'          => $this->input->post('fl_incapaz'),
        		'fl_estudante'        => $this->input->post('fl_estudante'),
        		'fl_guarda_juridica'  => $this->input->post('fl_guarda_juridica'),
        		'fl_previdenciario'   => $this->input->post('fl_previdenciario'),
        		'fl_imposto_renda'    => $this->input->post('fl_imposto_renda'),
        		'cd_usuario'          => $this->session->userdata('codigo')
        	);

        	if(intval($cd_pedido_aposentadoria_ceeeprev_dependente) == 0)
        	{
        		$this->pedido_aposentadoria_ceeeprev_model->salvar_dependente($cd_pedido_aposentadoria_ceeeprev, $args);
        	}
        	else 
        	{
        		$this->pedido_aposentadoria_ceeeprev_model->atualizar_dependente($cd_pedido_aposentadoria_ceeeprev_dependente, $args);
        	}

        	redirect('ecrm/pedido_aposentadoria_ceeeprev/dependente/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function excluir_dependente($cd_pedido_aposentadoria_ceeeprev, $cd_pedido_aposentadoria_ceeeprev_dependente)
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');
			
			$this->pedido_aposentadoria_ceeeprev_model->excluir_dependente($cd_pedido_aposentadoria_ceeeprev_dependente, $this->session->userdata('codigo'));
        	
        	redirect('ecrm/pedido_aposentadoria_ceeeprev/dependente/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev, $cd_pedido_aposentadoria_ceeeprev_dependente_prev = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

			$data['cadastro']        = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);
			$data['grau_parentesco'] = $this->pedido_aposentadoria_ceeeprev_model->get_grau_parentesco();
			$data['estado_civil']    = $this->pedido_aposentadoria_ceeeprev_model->get_estado_civil();
			$data['collection']      = $this->pedido_aposentadoria_ceeeprev_model->listar_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev);

			if(intval($cd_pedido_aposentadoria_ceeeprev_dependente_prev) == 0)
			{
				$data['row'] = array(
					'cd_pedido_aposentadoria_ceeeprev_dependente_prev' => $cd_pedido_aposentadoria_ceeeprev_dependente_prev,
					'ds_nome'                                          => '', 
            		'dt_nascimento'                                    => '', 
            		'ds_sexo'                                          => '',
            		'ds_grau_parentesco'                               => '', 
            		'ds_estado_civil'                                  => '', 
            		'fl_incapaz'                                       => '', 
            		'fl_estudante'                                     => '', 
            		'fl_guarda_juridica'                               => '', 
            		'fl_previdenciario'                                => '', 
            		'fl_imposto_renda'                                 => ''
				);
			}
			else
			{
				$data['row'] = $this->pedido_aposentadoria_ceeeprev_model->carrega_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev_dependente_prev);
			}

			$this->load->view('ecrm/pedido_aposentadoria_ceeeprev/dependente_previdenciario', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_dependente_previdenciario()
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

			$cd_pedido_aposentadoria_ceeeprev                 = $this->input->post('cd_pedido_aposentadoria_ceeeprev');
			$cd_pedido_aposentadoria_ceeeprev_dependente_prev = $this->input->post('cd_pedido_aposentadoria_ceeeprev_dependente_prev');

        	$args = array(
        		'ds_nome'             => $this->input->post('ds_nome'),
        		'dt_nascimento'       => $this->input->post('dt_nascimento'),
        		'ds_sexo'             => $this->input->post('ds_sexo'),
        		'ds_grau_parentesco'  => $this->input->post('ds_grau_parentesco'),
        		'ds_estado_civil'     => $this->input->post('ds_estado_civil'),
        		'fl_incapaz'          => $this->input->post('fl_incapaz'),
        		'fl_estudante'        => $this->input->post('fl_estudante'),
        		'fl_guarda_juridica'  => $this->input->post('fl_guarda_juridica'),
        		'fl_previdenciario'   => $this->input->post('fl_previdenciario'),
        		'fl_imposto_renda'    => $this->input->post('fl_imposto_renda'),
        		'cd_usuario'          => $this->session->userdata('codigo')
        	);

        	if(intval($cd_pedido_aposentadoria_ceeeprev_dependente_prev) == 0)
        	{
        		$this->pedido_aposentadoria_ceeeprev_model->salvar_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev, $args);
        	}
        	else 
        	{
        		$this->pedido_aposentadoria_ceeeprev_model->atualizar_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev_dependente_prev, $args);
        	}

        	redirect('ecrm/pedido_aposentadoria_ceeeprev/dependente_previdenciario/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function excluir_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev, $cd_pedido_aposentadoria_ceeeprev_dependente_prev)
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');
			
			$this->pedido_aposentadoria_ceeeprev_model->excluir_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev_dependente_prev, $this->session->userdata('codigo'));
        	
        	redirect('ecrm/pedido_aposentadoria_ceeeprev/dependente_previdenciario/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function analise($cd_pedido_aposentadoria_ceeeprev)
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model(array(
				'autoatendimento/pedido_aposentadoria_ceeeprev_model', 
				'projetos/eventos_email_model'
			));
			
			$this->pedido_aposentadoria_ceeeprev_model->analise($cd_pedido_aposentadoria_ceeeprev, $this->session->userdata('codigo'));

			$pedido_aposentadoria = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);

			$cd_evento  = 427;
            $cd_usuario = $this->session->userdata('codigo');

            $email = $this->eventos_email_model->carrega($cd_evento);

            $args = array(
                'de'                    => 'Pedido de Aposentadoria CeeePrev',
                'assunto'               => $email['assunto'],
                'para'                  => $pedido_aposentadoria['ds_email1'],
                'cc'                    => $pedido_aposentadoria['ds_email2'],
                'cco'                   => $email['cco'],
                'texto'                 => $email['email'],
                'cd_empresa'            => $pedido_aposentadoria['cd_empresa'],
                'cd_registro_empregado' => $pedido_aposentadoria['cd_registro_empregado'],
                'seq_dependencia'       => $pedido_aposentadoria['seq_dependencia'],
                'tp_email'              => 'A',
                'cd_divulgacao'         => ''
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
        	
        	redirect('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function indeferir()
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model(array(
				'autoatendimento/pedido_aposentadoria_ceeeprev_model', 
				'projetos/eventos_email_model'
			));

			$cd_pedido_aposentadoria_ceeeprev = $this->input->post('cd_pedido_aposentadoria_ceeeprev');
			$ds_motivo_indeferido             = $this->input->post('ds_motivo_indeferido');
			   
			$this->pedido_aposentadoria_ceeeprev_model->indeferir(
				$cd_pedido_aposentadoria_ceeeprev, 
				$ds_motivo_indeferido, 
				$this->session->userdata('codigo')
			);

			$pedido_aposentadoria = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);

			$cd_evento  = 428;
            $cd_usuario = $this->session->userdata('codigo');

            $email = $this->eventos_email_model->carrega($cd_evento);

            $args = array(
                'de'                    => 'Pedido de Aposentadoria CeeePrev',
                'assunto'               => $email['assunto'],
                'para'                  => $pedido_aposentadoria['ds_email1'],
                'cc'                    => $pedido_aposentadoria['ds_email2'],
                'cco'                   => $email['cco'],
                'texto'                 => str_replace('[OBS]', $ds_motivo_indeferido, $email['email']),
                'cd_empresa'            => $pedido_aposentadoria['cd_empresa'],
                'cd_registro_empregado' => $pedido_aposentadoria['cd_registro_empregado'],
                'seq_dependencia'       => $pedido_aposentadoria['seq_dependencia'],
                'tp_email'              => 'A',
                'cd_divulgacao'         => ''
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
        	
        	redirect('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function assinatura($cd_pedido_aposentadoria_ceeeprev)
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

			$pedido_aposentadoria = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);

			if(trim($pedido_aposentadoria['dt_assinatura']) == '')
			{
				if((trim($pedido_aposentadoria['ds_telefone1']) != '') AND (trim($pedido_aposentadoria['ds_email1']) != ''))
				{
					$config = $this->pedido_aposentadoria_ceeeprev_model->get($this->get_ambiente());

					$arquivo = $this->formulario($cd_pedido_aposentadoria_ceeeprev, 'S');

					$dt_limite = new DateTime('+10 day');

					$data = '
					{
						"document":{
							"path":"/PARTICIPANTES/'.str_pad($pedido_aposentadoria['cd_empresa'], 2, '0', STR_PAD_LEFT).'-'.str_pad($pedido_aposentadoria['cd_registro_empregado'], 6, '0', STR_PAD_LEFT).'-'.str_pad($pedido_aposentadoria['seq_dependencia'], 2, '0', STR_PAD_LEFT).'/'.str_pad($this->cd_doc, 4, '0', STR_PAD_LEFT).'/'.str_replace("-","_",str_replace(" ","_",$this->nome_doc))."-".str_replace(' ','_',$pedido_aposentadoria['ds_nome']).date('YmdHis').'.pdf",
							"content_base64":"data:application/pdf;base64,'.$arquivo.'",
							"deadline_at":"'.$dt_limite->format('Y-m-d').'T23:59:59-03:00",
							"remind_interval":"2",
							"auto_close":"true",
							"sequence_enabled":"true",
							"signable_group":null,
							"locale":"pt-BR"
						}
					}';

					$doc = $this->rest_click(trim($config['ds_url']).'/documents?access_token='.trim($config['ds_token']), $data);

					if(count($doc) > 0)
					{
						$doc_key = $doc['array']['document']['key'];

						$args = array(
							'ip'                    => $_SERVER['REMOTE_ADDR'],
							'dt_limite'             => $dt_limite->format('d/m/Y').' 23:59:59',
							'cd_empresa'            => intval($pedido_aposentadoria['cd_empresa']),
							'cd_registro_empregado' => intval($pedido_aposentadoria['cd_registro_empregado']),
							'seq_dependencia'       => intval($pedido_aposentadoria['seq_dependencia']),
							'cd_doc'                => intval($this->cd_doc),
							'id_doc'                => $doc_key,
							'json_doc'              => $doc['json']
						);

						$cd_contrato_digital = $this->pedido_aposentadoria_ceeeprev_model->salvar_contrato_digital($args);

						$this->pedido_aposentadoria_ceeeprev_model->protocolos_assinatura_docs(
							$pedido_aposentadoria['cd_empresa'], 
							$pedido_aposentadoria['cd_registro_empregado'], 
							$pedido_aposentadoria['seq_dependencia'], 
							$doc_key,
							$this->nome_doc
						);

						$url_assinatura = '';

						$assinado = $this->assinar(
							$config['ds_url'], 
							$config['ds_token'], 
							$pedido_aposentadoria['ds_email1'], 
							'sms', 
							$doc_key,
							'sign',
							1,
							$cd_contrato_digital, 
							'P',
							intval(str_replace(" ", "",str_replace("(","",str_replace(")","",(trim($pedido_aposentadoria['ds_telefone1'])))))),
							$pedido_aposentadoria['ds_nome'],
							$url_assinatura
						);

						if($assinado)
						{
							$assinado = $this->assinar(
								$config['ds_url'], 
								$config['ds_token'], 
								$this->validador, 
								'email', 
								$doc_key, 
								'validator',
								2,
								$cd_contrato_digital, 
								'V'
							);

							if($assinado)
							{

								$this->pedido_aposentadoria_ceeeprev_model->assinatura($cd_pedido_aposentadoria_ceeeprev, $this->session->userdata('codigo'));

								redirect('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$cd_pedido_aposentadoria_ceeeprev);

							}
							else
							{
								exibir_mensagem('ERRO AO ENCAMINHAR PARA ASSINATURA [3]');
							}
						}
						else
						{
							exibir_mensagem('ERRO AO ENCAMINHAR PARA ASSINATURA [2]');
						}
					}
					else
					{
						exibir_mensagem('ERRO AO ENCAMINHAR PARA ASSINATURA [1]');
					}
				}
				else
				{
					exibir_mensagem('PARA ASSINAR DEVE INFORMAR TELEFONE1 E EMAIL1');
				}
			}
			else
			{
				exibir_mensagem('DOCUMENTO JÁ FOI ENCAMINHADO PARA ASSINATURA.');
			}
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function deferido($cd_pedido_aposentadoria_ceeeprev)
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model(array(
				'autoatendimento/pedido_aposentadoria_ceeeprev_model', 
				'projetos/eventos_email_model'
			));
			
			$this->pedido_aposentadoria_ceeeprev_model->deferido($cd_pedido_aposentadoria_ceeeprev, $this->session->userdata('codigo'));

			$pedido_aposentadoria = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);

			$cd_evento  = 429;
            $cd_usuario = $this->session->userdata('codigo');

            $email = $this->eventos_email_model->carrega($cd_evento);

            $args = array(
                'de'                    => 'Pedido de Aposentadoria CeeePrev',
                'assunto'               => $email['assunto'],
                'para'                  => $pedido_aposentadoria['ds_email1'],
                'cc'                    => $pedido_aposentadoria['ds_email2'],
                'cco'                   => $email['cco'],
                'texto'                 => $email['email'],
                'cd_empresa'            => $pedido_aposentadoria['cd_empresa'],
                'cd_registro_empregado' => $pedido_aposentadoria['cd_registro_empregado'],
                'seq_dependencia'       => $pedido_aposentadoria['seq_dependencia'],
                'tp_email'              => 'A',
                'cd_divulgacao'         => ''
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->enviar_documento_liquid($pedido_aposentadoria);
        	
        	redirect('ecrm/pedido_aposentadoria_ceeeprev/cadastro/'.$cd_pedido_aposentadoria_ceeeprev);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function formulario($cd_pedido_aposentadoria_ceeeprev, $fl_assinar = 'N')
	{
		if($this->get_permissao())
		{
			$this->cd_doc   = 233;
			$this->nome_doc = 'PEDIDO DE BENEFICIO';

			$caminho = './up/pedido_aposentadoria_ceeeprev/';

			$this->load->plugin(array('fpdf', 'qrcode'));

			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');
			
			$pedido_aposentadoria      = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);
			$dependente                = $this->pedido_aposentadoria_ceeeprev_model->listar_dependente($cd_pedido_aposentadoria_ceeeprev);
			$dependente_previdenciario = $this->pedido_aposentadoria_ceeeprev_model->listar_dependente_previdenciario($cd_pedido_aposentadoria_ceeeprev);
   
	        $ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');
	        $ob_pdf->SetMargins(10, 14, 5); 		
	        $ob_pdf->AddPage();  

			$ob_pdf->setXY(0,0);
			$ob_pdf->Image(
				'./img/pedido_aposentadoria/ceeeprev.jpg', 
				$ob_pdf->GetX(), 
				$ob_pdf->GetY(), 
				$ob_pdf->ConvertSize(800), 
				$ob_pdf->ConvertSize(1131),
				'',
				'',
				false
			);
			
			$qrcode = new QRcode(utf8_encode('14-'.$pedido_aposentadoria['cd_empresa'].'-'.$pedido_aposentadoria['cd_registro_empregado'].'-'.$pedido_aposentadoria['seq_dependencia'].'-CADP0303-'.$this->cd_doc), 'L');
			$qrcode->disableBorder();
			$qrcode->displayFPDF($ob_pdf,182.5,10,15);

			$ob_pdf->SetFont('segoeuib', '', 13.5);

			if(trim($pedido_aposentadoria['tp_pedido_aposentadoria']) == 'N')
            {
            	$ob_pdf->Text(94, 36.5, 'NORMAL');
            }
            else if(trim($pedido_aposentadoria['tp_pedido_aposentadoria']) == 'A')
            {
            	$ob_pdf->Text(90, 36.5, 'ANTECIPADA');
            }

			$ob_pdf->SetFont('segoeuil', '', 8);

            $ob_pdf->Text(27, 47, trim($pedido_aposentadoria['cd_empresa']));
            $ob_pdf->Text(33, 47, trim($pedido_aposentadoria['cd_registro_empregado']));
            $ob_pdf->Text(45, 47, trim($pedido_aposentadoria['seq_dependencia']));
            $ob_pdf->Text(67, 47, trim($pedido_aposentadoria['ds_nome']));
            $ob_pdf->Text(181, 47, trim($pedido_aposentadoria['dt_nascimento']));

            $ob_pdf->Text(16, 56.5, trim($pedido_aposentadoria['ds_cpf']));
            $ob_pdf->Text(59, 56.5, trim($pedido_aposentadoria['ds_estado_civil']));
            $ob_pdf->Text(111, 56.5, trim($pedido_aposentadoria['ds_naturalidade']));
            $ob_pdf->Text(174, 56.5, trim($pedido_aposentadoria['ds_nacionalidade']));

            $ob_pdf->Text(37, 65.5, trim($pedido_aposentadoria['ds_endereco']));
            $ob_pdf->Text(166, 65.5, trim($pedido_aposentadoria['nr_endereco']));

            $ob_pdf->Text(27, 74.5, trim($pedido_aposentadoria['ds_complemento_endereco']));
            $ob_pdf->Text(119, 74.5, trim($pedido_aposentadoria['ds_bairro']));

            $ob_pdf->Text(19, 83.5, trim($pedido_aposentadoria['ds_cidade']));
            $ob_pdf->Text(126, 83.5, trim($pedido_aposentadoria['ds_uf']));
            $ob_pdf->Text(162, 83.5, trim($pedido_aposentadoria['ds_cep']));

            $ob_pdf->Text(30, 92.5, trim($pedido_aposentadoria['ds_telefone1']));
            $ob_pdf->Text(96, 92.5, trim($pedido_aposentadoria['ds_telefone2']));
            $ob_pdf->Text(158, 92.5, trim($pedido_aposentadoria['ds_celular']));

            $ob_pdf->Text(19, 102, trim($pedido_aposentadoria['ds_email1']));
            $ob_pdf->Text(121, 102, trim($pedido_aposentadoria['ds_email2']));

            $ob_pdf->Text(19, 117.5, trim($pedido_aposentadoria['ds_banco']));
            $ob_pdf->Text(87, 117.5, trim($pedido_aposentadoria['ds_agencia']));
            $ob_pdf->Text(151, 117.5, trim($pedido_aposentadoria['ds_conta']));

            if(trim($pedido_aposentadoria['fl_adiantamento_cip']) == 'N')
            {
            	$ob_pdf->Text(117, 123.5, 'X');
            }
            else if(trim($pedido_aposentadoria['fl_adiantamento_cip']) == 'S')
            {
            	$ob_pdf->Text(132, 123.5, 'X');
            	$ob_pdf->Text(170, 123.5, $pedido_aposentadoria['nr_adiantamento_cip']);
            }

            if(trim($pedido_aposentadoria['fl_reversao_beneficio']) == 'N')
            {
            	$ob_pdf->Text(80, 129.5, 'X');
            }
            else if(trim($pedido_aposentadoria['fl_reversao_beneficio']) == 'S')
            {
            	$ob_pdf->Text(100, 129.5, 'X');
            }

            if(trim($pedido_aposentadoria['fl_reversao_beneficio']) == 'N')
            {
            	$ob_pdf->Text(80, 129.5, 'X');
            }
            else if(trim($pedido_aposentadoria['fl_reversao_beneficio']) == 'S')
            {
            	$ob_pdf->Text(100, 129.5, 'X');
            }

            if(trim($pedido_aposentadoria['fl_politicamente_exposta']) == 'N')
            {
            	$ob_pdf->Text(65.5, 136, 'X');
            }
            else if(trim($pedido_aposentadoria['fl_politicamente_exposta']) == 'S')
            {
            	$ob_pdf->Text(85.5, 136, 'X');
            }

            if(trim($pedido_aposentadoria['fl_us_person']) == 'N')
            {
            	$ob_pdf->Text(81.5, 153.5, 'X');
            }
            else if(trim($pedido_aposentadoria['fl_us_person']) == 'S')
            {
            	$ob_pdf->Text(101.5, 153.5, 'X');
            }

            $ob_pdf->AddPage();

            $ob_pdf->SetFont('segoeuib', '', 13.5);

            $ob_pdf->MultiCell(190, 7, "DEPENDENTES IR", '0', 'C');

            $ob_pdf->setY($ob_pdf->getY() + 5);

            $ob_pdf->SetFont('segoeuib', '', 9);
            $ob_pdf->SetWidths(array(41, 34, 13, 33, 32, 16, 18));
			$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
			
			$ob_pdf->Row(array('Nome', 'Dt. Nascimento', 'Sexo', 'Grau de Parentesco', 'Estado Cívil', 'Incapaz', 'Estudante'));
			
			$ob_pdf->SetFont('segoeuil', '', 9);

			$ob_pdf->SetAligns(array('L', 'C', 'C', 'L', 'L', 'C', 'C'));

			foreach ($dependente as $item)
	        {
				$ob_pdf->Row(array(
					$item['ds_nome'], 
					$item['dt_nascimento'], 
					$item['ds_sexo'],
					$item['ds_grau_parentesco'],
					$item['ds_estado_civil'],
					$item['fl_incapaz'],
					$item['fl_estudante']
				));
			}

			$ob_pdf->AddPage();

            $ob_pdf->SetFont('segoeuib', '', 13.5);

            $ob_pdf->MultiCell(190, 7, "DEPENDENTES PREVIDENCIÁRIO", '0', 'C');

            $ob_pdf->setY($ob_pdf->getY() + 5);

            $ob_pdf->SetFont('segoeuib', '', 9);
            $ob_pdf->SetWidths(array(44, 37, 16, 36, 35, 19));
			$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
			
			$ob_pdf->Row(array('Nome', 'Dt. Nascimento', 'Sexo', 'Grau de Parentesco', 'Estado Cívil', 'Incapaz'));
			
			$ob_pdf->SetFont('segoeuil', '', 9);

			$ob_pdf->SetAligns(array('L', 'C', 'C', 'L', 'L', 'C'));

			foreach ($dependente_previdenciario as $item)
	        {
				$ob_pdf->Row(array(
					$item['ds_nome'], 
					$item['dt_nascimento'], 
					$item['ds_sexo'],
					$item['ds_grau_parentesco'],
					$item['ds_estado_civil'],
					$item['fl_incapaz']
				));
			}

			$name_file = 'pedido_aposentadoria_'.$pedido_aposentadoria['cd_empresa'].'_'.$pedido_aposentadoria['cd_registro_empregado'].'_'.$pedido_aposentadoria['seq_dependencia'].'_'.date('dmYHis').'.pdf';

			$ob_pdf->Output($caminho.$name_file, 'F');

			if(trim($pedido_aposentadoria['arquivo_simulacao']) != '')
			{
				$pagina[0] = $caminho.$name_file;
				$pagina[1] = $caminho.$pedido_aposentadoria['arquivo_simulacao'];

				$this->merge_pdf($caminho, $name_file, $pagina);
			}

			if($fl_assinar == 'N')
			{
				header('Content-Type: application/pdf');
                header('Cache-Control: public, must-revalidate');
                header('Pragma: hack');
                header('Content-Disposition: inline; filename="'.$name_file.'"');
                header('Content-Transfer-Encoding: binary');        
                
                readfile($caminho.$name_file);
			}
			else
			{
				/*
				header('Content-Type: application/pdf');
				header("Cache-Control: public, must-revalidate");
				header("Pragma: hack");
				header('Content-Disposition: inline; filename="doc.pdf"');
				header("Content-Transfer-Encoding: binary");
				echo base64_decode(base64_encode(file_get_contents($caminho.$name_file)));
				exit;
				*/

				return base64_encode(file_get_contents($caminho.$name_file));
				//return base64_encode($ob_pdf->Output('pedido_beneficio.pdf', 'S'));	
			}
			
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	private function merge_pdf($caminho, $name_file, $pagina)
	{
		$this->load->plugin('PDFMerger');

		$ob_pdf = new PDFMerger_pi;

        $ob_pdf->addPDFArray($pagina)->merge('file', $caminho.$name_file);
 
        unset($ob_pdf);
	}

	public function liquid($cd_pedido_aposentadoria_ceeeprev)
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');
			
			$pedido_aposentadoria = $this->pedido_aposentadoria_ceeeprev_model->carrega($cd_pedido_aposentadoria_ceeeprev);
			
			$this->enviar_documento_liquid($pedido_aposentadoria);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	private function enviar_documento_liquid($pedido_aposentadoria)
	{
		$this->load->model('autoatendimento/pedido_aposentadoria_ceeeprev_model');

		$pasta        = 'IMAGENS PREVIDENCIARIAS - FCEEE\ANO DE '.date('Y').'\\'.date('m').' - '.strtoupper(mes_extenso(intval(date('m')))).'\\'.date('dmY').'\\';
        $codigo_ficha = 1;
        $dir = '../cieprev/up/pedido_aposentadoria_ceeeprev/';

        if(trim($pedido_aposentadoria['arquivo_doc_identidade']) != '')
        {
        	$cd_tipo_doc = 1;

        	$tipo_doc = $this->pedido_aposentadoria_ceeeprev_model->get_documento_nome($cd_tipo_doc);

        	$extensao = pathinfo($pedido_aposentadoria['arquivo_doc_identidade']);

        	$nome_arquivo = 
                $pedido_aposentadoria['cd_empresa'].'_'.
                $pedido_aposentadoria['cd_registro_empregado'].'_'.
                $pedido_aposentadoria['seq_dependencia'].'_'.
                $cd_tipo_doc.'_'.
                uniqid();

            $campos_ficha = 
                $pedido_aposentadoria['cd_empresa'].';'.
                $pedido_aposentadoria['cd_registro_empregado'].';'.
                $pedido_aposentadoria['seq_dependencia'].';'.
                $cd_tipo_doc.';'.
                $tipo_doc['nome_documento'].';'.
                date('d/m/Y').';'.
                date('d/m/Y').';'.
                ';'.
                ';'.
                ';'.
                ';'.
                $nome_arquivo.';'.
                $nome_arquivo.';'.
                ';'.
                ';'.
                ';'.
                $this->session->userdata('usuario').' - '.$this->session->userdata('divisao').';'.
                'Documentos Encaminhados (e-prev)';

            $this->set_liquid($nome_arquivo, $extensao['extension'], $pasta, $codigo_ficha, $campos_ficha, $dir, $pedido_aposentadoria['arquivo_doc_identidade']);
        }

        if(trim($pedido_aposentadoria['arquivo_conta_bancaria']) != '')
        {
        	$cd_tipo_doc = 248;

        	$tipo_doc = $this->pedido_aposentadoria_ceeeprev_model->get_documento_nome($cd_tipo_doc);

        	$extensao = pathinfo($pedido_aposentadoria['arquivo_conta_bancaria']);

        	$nome_arquivo = 
                $pedido_aposentadoria['cd_empresa'].'_'.
                $pedido_aposentadoria['cd_registro_empregado'].'_'.
                $pedido_aposentadoria['seq_dependencia'].'_'.
                $cd_tipo_doc.'_'.
                uniqid();

            $campos_ficha = 
                $pedido_aposentadoria['cd_empresa'].';'.
                $pedido_aposentadoria['cd_registro_empregado'].';'.
                $pedido_aposentadoria['seq_dependencia'].';'.
                $cd_tipo_doc.';'.
                $tipo_doc['nome_documento'].';'.
                date('d/m/Y').';'.
                date('d/m/Y').';'.
                ';'.
                ';'.
                ';'.
                ';'.
                $nome_arquivo.';'.
                $nome_arquivo.';'.
                ';'.
                ';'.
                ';'.
                $this->session->userdata('usuario').' - '.$this->session->userdata('divisao').';'.
                'Documentos Encaminhados (e-prev)';

            $this->set_liquid($nome_arquivo, $extensao['extension'], $pasta, $codigo_ficha, $campos_ficha, $dir, $pedido_aposentadoria['arquivo_conta_bancaria']);
        }

        if(trim($pedido_aposentadoria['arquivo_doc_cpf']) != '')
        {
        	$cd_tipo_doc = 610;

        	$tipo_doc = $this->pedido_aposentadoria_ceeeprev_model->get_documento_nome($cd_tipo_doc);

        	$extensao = pathinfo($pedido_aposentadoria['arquivo_doc_cpf']);

        	$nome_arquivo = 
                $pedido_aposentadoria['cd_empresa'].'_'.
                $pedido_aposentadoria['cd_registro_empregado'].'_'.
                $pedido_aposentadoria['seq_dependencia'].'_'.
                $cd_tipo_doc.'_'.
                uniqid();

            $campos_ficha = 
                $pedido_aposentadoria['cd_empresa'].';'.
                $pedido_aposentadoria['cd_registro_empregado'].';'.
                $pedido_aposentadoria['seq_dependencia'].';'.
                $cd_tipo_doc.';'.
                $tipo_doc['nome_documento'].';'.
                date('d/m/Y').';'.
                date('d/m/Y').';'.
                ';'.
                ';'.
                ';'.
                ';'.
                $nome_arquivo.';'.
                $nome_arquivo.';'.
                ';'.
                ';'.
                ';'.
                $this->session->userdata('usuario').' - '.$this->session->userdata('divisao').';'.
                'Documentos Encaminhados (e-prev)';

            $this->set_liquid($nome_arquivo, $extensao['extension'], $pasta, $codigo_ficha, $campos_ficha, $dir, $pedido_aposentadoria['arquivo_doc_cpf']);
        }
	}

	private function set_liquid($nome_arquivo, $extension, $pasta, $codigo_ficha, $campos_ficha, $dir, $ds_documento)
	{		
		$post = array(
			'token'        => '7a2584226d7f72f3a83920be80b2f33e',
			'path'         => utf8_encode($pasta),
			'id'           => $codigo_ficha,
			'campos'       => utf8_encode($campos_ficha),
			'ds_documento' => utf8_encode($nome_arquivo.'.'.$extension),
			'file_base64'  => base64_encode(file_get_contents($dir.$ds_documento))
		);
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://www.e-prev.com.br/webapp/srvweb/index.php/liquid_suite_set_file');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$retorno_json = curl_exec($ch);
	
		curl_close($ch);
		
		$json = json_decode($retorno_json, true);
		
		$id_liquid = $json['result']['id'];
	}
}
?>