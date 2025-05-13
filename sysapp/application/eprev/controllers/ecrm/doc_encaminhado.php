<?php
class doc_encaminhado extends Controller
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

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $data = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia,
                'tipo_doc'              => $this->doc_encaminhado_model->get_tipo_doc()
            );

            $this->load->view('ecrm/doc_encaminhado/index', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('autoatendimento/doc_encaminhado_model');

        $args = array(
            'cd_empresa'                  => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado'       => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'             => $this->input->post('seq_dependencia', TRUE),
            'dt_encaminhamento_ini'       => $this->input->post('dt_encaminhamento_ini', TRUE),
            'dt_encaminhamento_fim'       => $this->input->post('dt_encaminhamento_fim', TRUE),
            'cd_doc_encaminhado_tipo_doc' => $this->input->post('cd_doc_encaminhado_tipo_doc', TRUE),
            'fl_cancelamento'             => $this->input->post('fl_cancelamento', TRUE),
            'fl_confirmacao'              => $this->input->post('fl_confirmacao', TRUE),
            'fl_envio_participante'       => $this->input->post('fl_envio_participante', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->doc_encaminhado_model->listar($args);

        $this->load->view('ecrm/doc_encaminhado/index_result', $data);
    }

    public function cadastro($cd_doc_encaminhado)
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $data['row']                    = $this->doc_encaminhado_model->carrega($cd_doc_encaminhado);
            $data['collection']             = $this->doc_encaminhado_model->listar_doc_encaminhado_arquivo($cd_doc_encaminhado);
            $data['tipo_protocolo_interno'] = $this->doc_encaminhado_model->tipo_solicitacao_protocolo_interno();

            $args = array(
                'cd_empresa'                  => $data['row']['cd_empresa'],
                'cd_registro_empregado'       => $data['row']['cd_registro_empregado'],
                'seq_dependencia'             => $data['row']['seq_dependencia'],
                'dt_encaminhamento_ini'       => '',
                'dt_encaminhamento_fim'       => '',
                'cd_doc_encaminhado_tipo_doc' => '',
                'fl_cancelamento'             => '',
                'fl_confirmacao'              => '',
                'fl_envio_participante'       => ''
            );


            $data['encaminhamento'] = $this->doc_encaminhado_model->listar($args, $cd_doc_encaminhado);

            $this->load->view('ecrm/doc_encaminhado/cadastro', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function confirmar($cd_doc_encaminhado)
    {
        if($this->permissao())
        {
            $this->load->model(array(
                'autoatendimento/doc_encaminhado_model',
                'projetos/eventos_email_model'
            ));

            $this->doc_encaminhado_model->confirmar($cd_doc_encaminhado, $this->session->userdata('codigo'));

            $this->doc_encaminhado_model->enviar($cd_doc_encaminhado, $this->session->userdata('codigo'));

            $participante = $this->doc_encaminhado_model->participante_email($cd_doc_encaminhado);

            if(count($participante) > 0)
            {
                $cd_evento  = 416;
                $cd_usuario = $this->session->userdata('codigo');

                $email = $this->eventos_email_model->carrega($cd_evento);

                $args = array(
                    'de'                    => 'Encaminhamento de Documento',
                    'assunto'               => $email['assunto'],
                    'para'                  => $participante['email'],
                    'cc'                    => $participante['email_profissional'],
                    'cco'                   => $email['cco'],
                    'texto'                 => $email['email'],
                    'cd_empresa'            => $participante['cd_empresa'],
                    'cd_registro_empregado' => $participante['cd_registro_empregado'],
                    'seq_dependencia'       => $participante['seq_dependencia'],
                    'tp_email'              => 'A',
                    'cd_divulgacao'         => ''
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
            }

            redirect('ecrm/doc_encaminhado');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cancelamento($cd_doc_encaminhado)
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $data['row']        = $this->doc_encaminhado_model->carrega($cd_doc_encaminhado);
            $data['collection'] = $this->doc_encaminhado_model->listar_doc_encaminhado_arquivo($cd_doc_encaminhado);

            $this->load->view('ecrm/doc_encaminhado/cancelar', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cancelar()
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $cd_doc_encaminhado = $this->input->post('cd_doc_encaminhado', TRUE);

            $args = array(
                'ds_justificativa' => $this->input->post('ds_justificativa', TRUE),
                'cd_usuario'       => $this->session->userdata('codigo')
            );

            $this->doc_encaminhado_model->cancelar($cd_doc_encaminhado, $args);

            redirect('ecrm/doc_encaminhado/cancelamento/'.$cd_doc_encaminhado);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function validado($cd_doc_encaminhado)
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $this->doc_encaminhado_model->validado_pelo_atendente($cd_doc_encaminhado, $this->session->userdata('codigo'));

            redirect('ecrm/doc_encaminhado/cadastro/'.$cd_doc_encaminhado);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar($cd_doc_encaminhado, $fl_enviar_email = 'S')
    {
    	if($this->permissao())
        {
            $this->load->model(array(
                'autoatendimento/doc_encaminhado_model',
                'projetos/eventos_email_model'
            ));

        	$this->doc_encaminhado_model->enviar($cd_doc_encaminhado, $this->session->userdata('codigo'));

            $participante = $this->doc_encaminhado_model->participante_email($cd_doc_encaminhado);
            
            if(count($participante) > 0 AND trim($fl_enviar_email) == 'S')
            {
                $cd_evento  = 415;
                $cd_usuario = $this->session->userdata('codigo');

                $email = $this->eventos_email_model->carrega($cd_evento);

                $tags = array('[OBS]');
                
                $subs = array(nl2br(trim($participante['ds_justificativa'])));

                $texto = str_replace($tags, $subs, $email['email']);

                $args = array(
                    'de'                    => 'Encaminhamento de Documento',
                    'assunto'               => $email['assunto'],
                    'para'                  => $participante['email'],
                    'cc'                    => $participante['email_profissional'],
                    'cco'                   => $email['cco'],
                    'texto'                 => $texto,
                    'cd_empresa'            => $participante['cd_empresa'],
                    'cd_registro_empregado' => $participante['cd_registro_empregado'],
                    'seq_dependencia'       => $participante['seq_dependencia'],
                    'tp_email'              => 'A',
                    'cd_divulgacao'         => ''
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
            }

        	redirect('ecrm/doc_encaminhado');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function gerar_protocolo_interno()
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $cd_doc_encaminhado      = $this->input->post('cd_doc_encaminhado', TRUE);
            $doc_encaminhado_arquivo = $this->input->post('doc_encaminhado_arquivo', TRUE);

            $row = $this->doc_encaminhado_model->carrega($cd_doc_encaminhado);

            $args = array(
                'cd_documento_recebido_tipo'       => 1,
                'cd_documento_recebido_tipo_solic' => $this->input->post('cd_documento_recebido_tipo_solic', TRUE),
                'cd_usuario'                       => $this->session->userdata('codigo')
            );

            $cd_documento_recebido = $this->doc_encaminhado_model->protocolo_interno($args);

            foreach ($doc_encaminhado_arquivo as $key => $item) 
            {
                $documento = $this->doc_encaminhado_model->get_doc_encaminhado_arquivo($item);

                $nr_folha_pdf = 1;

                if(trim(pathinfo($documento['ds_documento'], PATHINFO_EXTENSION)) == 'pdf')
                {
                    $pdftext = file_get_contents(base_url().'up/doc_encaminhado/'.$documento['ds_documento']);
                    $nr_folha_pdf = preg_match_all("/\/Page\W/", $pdftext, $dummy);
                }

                copy('./up/doc_encaminhado/'.$documento['ds_documento'], './up/documento_recebido/'.$documento['ds_documento']);

                $args = array(
                    'cd_documento_recebido' => $cd_documento_recebido,
                    'cd_empresa'            => $row['cd_empresa'], 
                    'cd_registro_empregado' => $row['cd_registro_empregado'],
                    'seq_dependencia'       => $row['seq_dependencia'],
                    'nome'                  => $row['nome'],
                    'cd_tipo_doc'           => $this->input->post('doc_encaminhado_'.$item, TRUE),
                    'arquivo'               => $documento['ds_documento'],
                    'arquivo_nome'          => $documento['ds_documento'],
                    'nr_folha'              => 1,
                    'nr_folha_pdf'          => $nr_folha_pdf,
                    'cd_usuario'            => $this->session->userdata('codigo')
                );

                $this->doc_encaminhado_model->protocolo_interno_documento($item, $args);
            }

            redirect('ecrm/cadastro_protocolo_interno/detalhe/'.$cd_documento_recebido);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar_documento_liquid()
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $cd_doc_encaminhado      = $this->input->post('cd_doc_encaminhado', TRUE);
            $doc_encaminhado_arquivo = $this->input->post('doc_encaminhado_arquivo', TRUE);

            $row = $this->doc_encaminhado_model->carrega($cd_doc_encaminhado);

            $pasta        = 'IMAGENS PREVIDENCIARIAS - FCEEE\ANO DE '.date('Y').'\\'.date('m').' - '.strtoupper(mes_extenso(intval(date('m')))).'\\'.date('dmY').'\\';
            $codigo_ficha = 1;
            $dir = '../cieprev/up/doc_encaminhado/';

            foreach ($doc_encaminhado_arquivo as $key => $item) 
            {
                $cd_tipo_doc = $this->input->post('doc_encaminhado_'.$item, TRUE);

                $documento = $this->doc_encaminhado_model->get_doc_encaminhado_arquivo($item);

                $extensao = pathinfo($documento['ds_documento']);

                $tipo_doc = $this->doc_encaminhado_model->get_documento_nome($cd_tipo_doc);

                $nome_arquivo = 
                    $row['cd_empresa'].'_'.
                    $row['cd_registro_empregado'].'_'.
                    $row['seq_dependencia'].'_'.
                    $cd_tipo_doc.'_'.
                    uniqid();

                $campos_ficha = 
                    $row['cd_empresa'].';'.
                    $row['cd_registro_empregado'].';'.
                    $row['seq_dependencia'].';'.
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
						
				$post = array(
					'token'        => '7a2584226d7f72f3a83920be80b2f33e',
					'path'         => utf8_encode($pasta),
					'id'           => $codigo_ficha,
					'campos'       => utf8_encode($campos_ficha),
					'ds_documento' => utf8_encode($nome_arquivo.'.'.$extensao['extension']),
					'file_base64'  => base64_encode(file_get_contents($dir.$documento['ds_documento']))
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
				
                $args = array(
                    'id_liquid'   => $id_liquid,
                    'cd_tipo_doc' => $this->input->post('doc_encaminhado_'.$item, TRUE)
                );

                $this->doc_encaminhado_model->liquid_documento($item, $args);

                redirect('ecrm/doc_encaminhado/cadastro/'.$cd_doc_encaminhado);
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function acompanhamento($cd_doc_encaminhado, $fl_andamento = 'N')
    {
        if($this->permissao())
        {
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $data['row']         = $this->doc_encaminhado_model->carrega($cd_doc_encaminhado);
            $data['collection']  = $this->doc_encaminhado_model->listar_acompanhamento($cd_doc_encaminhado);
            $data['fl_andamento'] = $fl_andamento;
            
            $this->load->view('ecrm/doc_encaminhado/acompanhamento', $data); 
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
            $this->load->model('autoatendimento/doc_encaminhado_model');

            $cd_doc_encaminhado = $this->input->post('cd_doc_encaminhado', TRUE);
            $fl_andamento        = $this->input->post('fl_andamento', TRUE);
            $ds_descricao       = $this->input->post('ds_descricao', TRUE);

            if(trim($fl_andamento) == 'S')
            {
                $this->doc_encaminhado_model->andamento($cd_doc_encaminhado, $ds_descricao, $this->session->userdata('codigo'));

                redirect('ecrm/doc_encaminhado/cadastro/'.$cd_doc_encaminhado);
            }
            else
            {
                $this->doc_encaminhado_model->salvar_acompanhamento($cd_doc_encaminhado, $ds_descricao, $this->session->userdata('codigo'));

                redirect('ecrm/doc_encaminhado/acompanhamento/'.$cd_doc_encaminhado);
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}