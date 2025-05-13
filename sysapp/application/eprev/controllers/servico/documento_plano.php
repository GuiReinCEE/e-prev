<?php
class Documento_plano extends Controller
{
    function __construct()
    {
		parent::Controller();

		CheckLogin();
	}

    private function get_permissao()
    {
        if(gerencia_in(array('GTI', 'GCM')))
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
            $this->load->model('autoatendimento/documento_plano_model');

            $data['site'] = $this->documento_plano_model->get_documento_plano();

			$this->load->view('servico/documento_plano/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function listar()
    {
		$this->load->model('autoatendimento/documento_plano_model');

        $args = array(
            'cd_documento_plano' => $this->input->post('cd_documento_plano', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->documento_plano_model->listar($args);
        
        foreach ($data['collection'] as $key => $item)
        {
            $documento_plano_arquivo = $this->documento_plano_model->get_documento_plano_tipo($item['cd_documento_plano']);

            $data['collection'][$key]['documento'] = array();
            
            foreach ($documento_plano_arquivo as $documento)
            {
                $data['collection'][$key]['documento'][] = $documento['ds_documento_plano_tipo']; 
            }
        }
        
        $this->load->view('servico/documento_plano/index_result', $data);
    }
    
    public function cadastro($cd_documento_plano)
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/documento_plano_model');

            $data = array(
                'tipo_documento' => $this->documento_plano_model->get_tipo_documento(),
                'row'            => $this->documento_plano_model->carrega($cd_documento_plano)
            );

            $documento_plano = $this->documento_plano_model->get_documento_plano_tipo($cd_documento_plano);

            $data['collection'] = array();

            foreach ($documento_plano as $key => $item) 
            {
                $data['collection'][] = $this->documento_plano_model->get_documento_plano_arquivo($cd_documento_plano, $item['cd_documento_plano_tipo']);
            }

            $this->load->view('servico/documento_plano/cadastro', $data);
    
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
            $this->load->model('autoatendimento/documento_plano_model');

            $cd_documento_plano = $this->input->post('cd_documento_plano', TRUE);

            $args = array(
                'cd_documento_plano'      => $cd_documento_plano,
                'cd_documento_plano_tipo' => $this->input->post('cd_documento_plano_tipo', TRUE),
                'arquivo_nome'           => $this->input->post('arquivo_nome', TRUE),
                'arquivo'                => $this->input->post('arquivo', TRUE),
                'cd_usuario'             => $this->session->userdata('codigo')
            );

            $this->documento_plano_model->salvar_arquivo($args);

            redirect('servico/documento_plano/cadastro/'.$cd_documento_plano, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_documento_plano, $cd_documento_plano_arquivo)
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/documento_plano_model');

            $this->documento_plano_model->excluir($cd_documento_plano_arquivo, $this->session->userdata('codigo'));
            
            redirect('servico/documento_plano/cadastro/'.$cd_documento_plano, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function todos($cd_documento_plano, $cd_documento_plano_tipo)
    {
        $this->load->model('autoatendimento/documento_plano_model');

        $tipo_documento = $this->documento_plano_model->get_tipo_documento_nome($cd_documento_plano_tipo);

        $data = array(
            'ds_documento' => $tipo_documento['ds_documento_plano_tipo'],
            'row'          => $this->documento_plano_model->carrega($cd_documento_plano)
        );

        $data['collection'] = $this->documento_plano_model->get_documento_plano_arquivo($cd_documento_plano, $cd_documento_plano_tipo, false);

        $this->load->view('servico/documento_plano/todos', $data);
    }
}
?>