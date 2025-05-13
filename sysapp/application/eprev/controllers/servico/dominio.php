<?php
class Dominio extends Controller
{
    function __construct()
    {
		parent::Controller();

		CheckLogin();
	}

    private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index()
    {
    	if($this->get_permissao())
        {
			$this->load->model('informatica/dominio_model');
            
            $data['tipo_dominio'] = $this->dominio_model->tipo_dominio();

            $this->load->view('servico/dominio/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$this->load->model('informatica/dominio_model');

    	$args = array(
            'dt_dominio_renovacao_ini' => $this->input->post('dt_dominio_renovacao_ini', TRUE),
    		'dt_dominio_renovacao_fim' => $this->input->post('dt_dominio_renovacao_fim', TRUE),
            'cd_dominio_tipo'          => $this->input->post('cd_dominio_tipo', TRUE)
    	);
				
		manter_filtros($args);

		$data['collection'] = $this->dominio_model->listar($args);

        $this->load->view('servico/dominio/index_result', $data);
    }

    public function cadastro($cd_dominio = 0)
    {
    	if($this->get_permissao())
        {
            $this->load->model('informatica/dominio_model');

            $data['tipo_dominio'] = $this->dominio_model->tipo_dominio();

	    	if(intval($cd_dominio) == 0)
			{
				$data['row'] = array(
					'cd_dominio'           => intval($cd_dominio),
					'ds_dominio'           => '',
					'dt_dominio_renovacao' => '',
					'descricao'            => '',
					'cd_usuario_aprovacao' => '',
                    'cd_dominio_tipo'      => '',
	 				'dt_aprovacao'         => ''   
				);
			}
			else
			{
				$data['row'] = $this->dominio_model->carrega($cd_dominio);
			}

			$this->load->view('servico/dominio/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
    	$this->load->model('informatica/dominio_model');

    	$cd_dominio = $this->input->post('cd_dominio', TRUE);

    	$args = array(
    		'ds_dominio'           => $this->input->post('ds_dominio', TRUE),
    		'dt_dominio_renovacao' => $this->input->post('dt_dominio_renovacao', TRUE),
    		'descricao'            => $this->input->post('descricao', TRUE),
            'cd_dominio_tipo'      => $this->input->post('cd_dominio_tipo', TRUE), 
    		'cd_usuario'           => $this->session->userdata('codigo')
    	);

    	if(intval($cd_dominio) == 0)
		{
    		$args['cd_dominio'] = $this->dominio_model->salvar($args);

    		$this->dominio_model->salvar_renovacao($args);
		} 
		else
		{
			$this->dominio_model->atualizar(intval($cd_dominio), $args);
		}

		redirect('servico/dominio/', 'refresh');
    }

    public function renovacao($cd_dominio, $cd_dominio_renovacao = 0)
    {
    	if($this->get_permissao())
        {
		    $this->load->model('informatica/dominio_model');

            $data['row'] = $this->dominio_model->carrega($cd_dominio);

            $data['collection'] = $this->dominio_model->renovacao_listar($cd_dominio);

            if(intval($cd_dominio_renovacao) == 0)
            {
                $data['renovacao'] = array(
                    'cd_dominio_renovacao' => intval($cd_dominio_renovacao),
                    'dt_dominio_renovacao' => ''
                );
            }
            else
            {
               $data['renovacao'] = $this->dominio_model->carrega_renovacao($cd_dominio_renovacao);
            }

            $this->load->view('servico/dominio/renovacao', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function salvar_renovacao()
	{
		$this->load->model('informatica/dominio_model');

        $cd_dominio_renovacao = $this->input->post('cd_dominio_renovacao', TRUE);

        $args = array(
     		'cd_dominio'		   => $this->input->post('cd_dominio', TRUE),
    		'dt_dominio_renovacao' => $this->input->post('dt_dominio_renovacao', TRUE),
    		'cd_usuario'           => $this->session->userdata('codigo')
    	);

        if(intval($cd_dominio_renovacao) == 0)
        {
            $cd_dominio_renovacao = $this->dominio_model->salvar_renovacao($args);
        }
        else
        {
           $this->dominio_model->atualizar_renovacao($cd_dominio_renovacao, $args);
        }

        redirect('servico/dominio/renovacao/'.$args['cd_dominio'], 'refresh');
	}

    public function anexo($cd_dominio)
    {
        if($this->get_permissao())
        {
            $this->load->model('informatica/dominio_model');

            $data['row'] = $this->dominio_model->carrega($cd_dominio);

            $data['arquivo'] = $this->dominio_model->anexo_carrega($cd_dominio);

            $data['collection'] = $this->dominio_model->anexo_listar($cd_dominio);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }

        $this->load->view('servico/dominio/anexo', $data);
    }

    public function anexo_salvar()
    {
        $this->load->model('informatica/dominio_model');
        
        $cd_dominio = $this->input->post('cd_dominio', TRUE);
        
        $dominio    = $this->dominio_model->carrega($cd_dominio);
            
        $args = array(
            'cd_dominio'          => $this->input->post('cd_dominio', TRUE),
            'arquivo_nome'        => $this->input->post('arquivo_nome', TRUE),
            'arquivo'             => $this->input->post('arquivo', TRUE),
            'ds_dominio_arquivo'  => $this->input->post('ds_dominio_arquivo', TRUE),
            'cd_usuario'          => $this->session->userdata('codigo')
        ); 
                              
        $this->dominio_model->anexo_salvar($args);
               
        redirect('servico/dominio/anexo/'.$cd_dominio, 'refresh');
    }

    public function anexo_excluir($cd_dominio, $cd_dominio_anexo)
    {
        $this->load->model('informatica/dominio_model');

        $this->dominio_model->anexo_excluir($cd_dominio, $cd_dominio_anexo, $this->session->userdata('codigo'));
        
        redirect('servico/dominio/anexo/'.$cd_dominio, 'refresh');
    }
}
?>