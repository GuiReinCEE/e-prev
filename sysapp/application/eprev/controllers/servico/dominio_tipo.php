<?php
class Dominio_tipo extends Controller
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
			$this->load->model('informatica/dominio_tipo_model');

            $data['usuario'] = $this->dominio_tipo_model->gerencia_unidade();

            $this->load->view('servico/dominio_tipo/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		$this->load->model('informatica/dominio_tipo_model');

    	$args = array(
            'cd_usuario_responsavel' => $this->input->post('cd_usuario_responsavel', TRUE),
    		'cd_usuario_substituto'  => $this->input->post('cd_usuario_substituto', TRUE)
    	);
				
		manter_filtros($args);

		$data['collection'] = $this->dominio_tipo_model->listar($args);

        $this->load->view('servico/dominio_tipo/index_result', $data);
    }

    public function cadastro($cd_dominio_tipo = 0)
    {
    	if($this->get_permissao())
        {
	    	$this->load->model('informatica/dominio_tipo_model');

            $data['usuario'] = $this->dominio_tipo_model->gerencia_unidade();

            if(intval($cd_dominio_tipo) == 0)
			{
				$data['row'] = array(
					'cd_dominio_tipo'           => intval($cd_dominio_tipo),
					'ds_dominio_tipo'           => '',
					'cd_usuario_substituto'     => '',
					'cd_usuario_responsavel'    => '',
                    'nr_dias'                   => ''
				);
			}
			else
			{
				$data['row'] = $this->dominio_tipo_model->carrega($cd_dominio_tipo);
			}

			$this->load->view('servico/dominio_tipo/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
    	$this->load->model('informatica/dominio_tipo_model');

    	$cd_dominio_tipo = $this->input->post('cd_dominio_tipo', TRUE);

    	$args = array(
    		'ds_dominio_tipo'        => $this->input->post('ds_dominio_tipo', TRUE),
    		'cd_usuario_substituto'  => $this->input->post('cd_usuario_substituto', TRUE),
    		'cd_usuario_responsavel' => $this->input->post('cd_usuario_responsavel', TRUE),
            'nr_dias'                => $this->input->post('nr_dias', TRUE),
    		'cd_usuario'             => $this->session->userdata('codigo')
    	);

    	if(intval($cd_dominio_tipo) == 0)
		{
    		$args['cd_dominio_tipo'] = $this->dominio_tipo_model->salvar($args);
        } 
		else
		{
			$this->dominio_tipo_model->atualizar(intval($cd_dominio_tipo), $args);
		}

		redirect('servico/dominio_tipo', 'refresh');
    }
}
?>