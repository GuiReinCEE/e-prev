<?php
class Controle_documento_controladoria extends Controller
{
    function __construct()
    {
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao()
    {
        if(gerencia_in(array('GC')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function tipo()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_documento_controladoria_model');

            $data = array(
				'tipo'     => $this->controle_documento_controladoria_model->get_tipo(),
				'usuario' => $this->controle_documento_controladoria_model->get_usuario()
            );

            $this->load->view('gestao/controle_documento_controladoria/tipo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function tipo_listar()
    {
		$this->load->model('gestao/controle_documento_controladoria_model');

    	$args = array(
    		'cd_controle_documento_controladoria_tipo' => $this->input->post('cd_controle_documento_controladoria_tipo', TRUE),
    		'cd_usuario'                               => $this->input->post('cd_usuario', TRUE)
		);
			
		manter_filtros($args);

		$data['collection'] = $this->controle_documento_controladoria_model->tipo_listar($args);

		foreach($data['collection'] as $key => $item)
		{
			$data['collection'][$key]['usuario'] = array();

			foreach($this->controle_documento_controladoria_model->get_usuario_check($item['cd_controle_documento_controladoria_tipo'], $args) as $usuario)
			{				
				$data['collection'][$key]['usuario'][] = $usuario['ds_usuario'];
			}		
		}

        $this->load->view('gestao/controle_documento_controladoria/tipo_result', $data);
    }

    public function tipo_cadastro($cd_controle_documento_controladoria_tipo = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_documento_controladoria_model');

            $data['usuario_com_acesso_check'] = array();

            if(intval($cd_controle_documento_controladoria_tipo) == 0)
            {
                $data['row'] = array(
                    'cd_controle_documento_controladoria_tipo' => intval($cd_controle_documento_controladoria_tipo),
                    'ds_controle_documento_controladoria_tipo' => ''
                ); 
            }
            else
            {
               	$data['row'] = $this->controle_documento_controladoria_model->tipo_carrega($cd_controle_documento_controladoria_tipo);

               	$usuario_com_acesso_check = $this->controle_documento_controladoria_model->get_usuario_check($cd_controle_documento_controladoria_tipo);

				foreach ($usuario_com_acesso_check as $key => $item) 
				{
					$data['usuario_com_acesso_check'][] = $item['cd_usuario'];
				}
            }

            $data['usuario'] = $this->controle_documento_controladoria_model->get_usuario($data['usuario_com_acesso_check']);

    		$this->load->view('gestao/controle_documento_controladoria/tipo_cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function tipo_salvar()
    {
    	if($this->get_permissao())
        {
            $this->load->model('gestao/controle_documento_controladoria_model');

        	$cd_controle_documento_controladoria_tipo = $this->input->post('cd_controle_documento_controladoria_tipo', TRUE);

        	$args = array(
                'cd_controle_documento_controladoria_tipo' => $this->input->post('cd_controle_documento_controladoria_tipo', TRUE),
                'ds_controle_documento_controladoria_tipo' => $this->input->post('ds_controle_documento_controladoria_tipo', TRUE),
        		'cd_usuario'                          => $this->session->userdata('codigo')
        	);

        	$usuario = $this->input->post('usuario', TRUE);

        	if(!is_array($usuario))
			{
				$args['usuario'] = array();
			}
			else
			{
				$args['usuario'] = $usuario;
			}

        	if(intval($cd_controle_documento_controladoria_tipo) == 0)
    		{
        		$this->controle_documento_controladoria_model->tipo_salvar($args);
            } 
    		else
    		{
    			$this->controle_documento_controladoria_model->tipo_atualizar(intval($cd_controle_documento_controladoria_tipo), $args);
    		}

    		redirect('gestao/controle_documento_controladoria/tipo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_documento_controladoria_model');
            
            $data['doc_tipo'] = $this->controle_documento_controladoria_model->get_tipo();

            $this->load->view('gestao/controle_documento_controladoria/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/controle_documento_controladoria_model');

        $args = array(
            'cd_controle_documento_controladoria_tipo' => $this->input->post('cd_controle_documento_controladoria_tipo', TRUE),
            'dt_inclusao_ini'                          => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim'                          => $this->input->post('dt_inclusao_fim', TRUE),
            'dt_envio_ini'                             => $this->input->post('dt_envio_ini', TRUE),
            'dt_envio_fim'                             => $this->input->post('dt_envio_fim', TRUE),
            'fl_envio'                                 => $this->input->post('fl_envio', TRUE)
        );
                
        manter_filtros($args);

        $data['collection'] = $this->controle_documento_controladoria_model->listar($args);

        $this->load->view('gestao/controle_documento_controladoria/index_result', $data);
    }
    
    public function cadastro($cd_controle_documento_controladoria_tipo, $cd_controle_documento_controladoria = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_documento_controladoria_model');

            $data['collection'] = $this->controle_documento_controladoria_model->lista_cadastro($cd_controle_documento_controladoria_tipo);                    
            
            $row = $this->controle_documento_controladoria_model->tipo_carrega($cd_controle_documento_controladoria_tipo);

            $data['row'] = array(
                'cd_controle_documento_controladoria'      => intval($cd_controle_documento_controladoria),
                'cd_controle_documento_controladoria_tipo' => intval($cd_controle_documento_controladoria_tipo),
                'ds_controle_documento_controladoria'      => '',
                'ds_controle_documento_controladoria_tipo' => $row['ds_controle_documento_controladoria_tipo'],
                'arquivo'                                  => '',
                'arquivo_nome'                             => '',
                'dt_referencia'                            => ''
            );         
            
            $this->load->view('gestao/controle_documento_controladoria/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_documento_controladoria_model');

            $cd_controle_documento_controladoria_tipo = $this->input->post('cd_controle_documento_controladoria_tipo', TRUE);
            $cd_controle_documento_controladoria = $this->input->post('cd_controle_documento_controladoria', TRUE);

            $args = array(
                'cd_controle_documento_controladoria'      => intval($cd_controle_documento_controladoria),
                'ds_controle_documento_controladoria'      => $this->input->post('ds_controle_documento_controladoria', TRUE),
                'cd_controle_documento_controladoria_tipo' => $this->input->post('cd_controle_documento_controladoria_tipo', TRUE),
                'arquivo'                                  => $this->input->post('arquivo', TRUE),
                'arquivo_nome'                             => $this->input->post('arquivo_nome', TRUE),
                'dt_referencia'                            => $this->input->post('dt_referencia', TRUE),
                'cd_usuario'                               => $this->session->userdata('codigo')
            );

            if(intval($cd_controle_documento_controladoria) == 0)
            {
                $cd_controle_documento_controladoria = $this->controle_documento_controladoria_model->salvar($args);
            }
           
            redirect('gestao/controle_documento_controladoria/cadastro/'.$cd_controle_documento_controladoria_tipo, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_controle_documento_controladoria)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/controle_documento_controladoria_model');

            $this->controle_documento_controladoria_model->excluir(
                $cd_controle_documento_controladoria, 
                $this->session->userdata('codigo')
            );
            
            redirect('gestao/controle_documento_controladoria', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar_email($cd_controle_documento_controladoria)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/controle_documento_controladoria_model'
            ));

            $cd_evento = 262;
            
            $email = $this->eventos_email_model->carrega($cd_evento);
            
            $controle_documento = $this->controle_documento_controladoria_model->carrega($cd_controle_documento_controladoria);

            $destino = $this->controle_documento_controladoria_model->get_usuario_check(
                $controle_documento['cd_controle_documento_controladoria_tipo']
            );
            
            $tags = array('[TIPO_DOCUMENTO]', '[DESCRICAO]', '[LINK]');

            $subs = array(
                $controle_documento['ds_controle_documento_controladoria_tipo'], 
                $controle_documento['ds_controle_documento_controladoria'], 
                site_url('gestao/controle_documento_controladoria/minhas')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $tags = array('[TIPO_DOCUMENTO]', '[MES_ANO]');

            $subs = array(
                $controle_documento['ds_controle_documento_controladoria_tipo'], 
                (trim($controle_documento['ds_referencia_email']) != '' ? '('.$controle_documento['ds_referencia_email'].')' : '') 
            );

            $assunto = str_replace($tags, $subs, $email['assunto']);

            $envia_email = '';

            foreach ($destino as $key => $item) 
            {
                $envia_email .= strtolower($item['usuario']).'@eletroceee.com.br';

                if(isset($destino[($key+1)]))
                {
                    $envia_email .= ';';
                }
            }

            $cd_usuario = $this->session->userdata('codigo');
            
            $args = array(
                'de'      => 'Controle de Documento GC',
                'assunto' => $assunto,
                'para'    => $email['para'],
                'cc'      => $envia_email,
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->controle_documento_controladoria_model->envio_email(
                $cd_controle_documento_controladoria, 
                $this->session->userdata('codigo')
            );

            if(trim($controle_documento['ds_caminho']) != '')
            {
                $this->envio_pydio($cd_controle_documento_controladoria);
            }

            redirect('gestao/controle_documento_controladoria/cadastro/'.$controle_documento['cd_controle_documento_controladoria_tipo'], 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function envio_pydio($cd_controle_documento_controladoria)
    {
        $this->load->plugin('encoding_pi');

        $this->load->model('gestao/controle_documento_controladoria_model');

        $controle_documento = $this->controle_documento_controladoria_model->carrega($cd_controle_documento_controladoria);

        copy('../cieprev/up/controle_documento_controladoria/'.$controle_documento['arquivo'], '../eletroceee/pydio/data/'.$controle_documento['ds_caminho'].'/'.fixUTF8($controle_documento['arquivo_nome']));
    }

    public function minhas()
    {   
        $this->load->model('gestao/controle_documento_controladoria_model');

        $data['doc_tipo'] = $this->controle_documento_controladoria_model->get_tipo_minhas($this->session->userdata('codigo'));

        $this->load->view('gestao/controle_documento_controladoria/minhas', $data);
    }

    public function minhas_listar()
    {
        $this->load->model('gestao/controle_documento_controladoria_model');

        $args = array(
            'cd_controle_documento_controladoria_tipo' => $this->input->post('cd_controle_documento_controladoria_tipo', TRUE),
            'cd_usuario'                              => $this->session->userdata('codigo')
        );
                
        manter_filtros($args);

        $data['collection'] = $this->controle_documento_controladoria_model->minhas_listar($args);
         
        $this->load->view('gestao/controle_documento_controladoria/minhas_result', $data);
    }

    public function documentos($cd_controle_documento_controladoria_tipo)
    {
        $this->load->model('gestao/controle_documento_controladoria_model');

        $data = array(
            'collection' => $this->controle_documento_controladoria_model->documentos($cd_controle_documento_controladoria_tipo),
            'row'        => $this->controle_documento_controladoria_model->tipo_carrega($cd_controle_documento_controladoria_tipo)
        );               

        $this->load->view('gestao/controle_documento_controladoria/documentos', $data);
    }

}
?>