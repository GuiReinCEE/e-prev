<?php
class Pendencia_minha extends Controller
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
            $this->load->view('servico/pendencia_minha/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function listar()
    {
		$this->load->model('gestao/pendencia_minha_model');

    	$args = array(
            'cd_pendencia_minha' => $this->input->post('cd_pendencia_minha', TRUE)
    	);
			
		manter_filtros($args);

		$data['collection'] = $this->pendencia_minha_model->listar_pendencias($args);

        $this->load->view('servico/pendencia_minha/index_result', $data);
    }

    public function cadastro($cd_pendencia_minha = '')
    {
    	if($this->get_permissao())
        {
            if(trim($cd_pendencia_minha) == '')
    		{
    			$data['row'] = array(
    				'cd_pendencia_minha' => trim($cd_pendencia_minha),
    				'ds_pendencia_minha' => ''
    			);  
    		}
    		else
    		{
    			$this->load->model('gestao/pendencia_minha_model');

    			$data['row'] = $this->pendencia_minha_model->carrega($cd_pendencia_minha);
            }

    		$this->load->view('servico/pendencia_minha/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
    	$this->load->model('gestao/pendencia_minha_model');

        $cd_pendencia_minha = $this->input->post('cd_pendencia_minha_old', TRUE);

    	$args = array(
            'cd_pendencia_minha'  => strtoupper($this->input->post('cd_pendencia_minha', TRUE)),
    		'ds_pendencia_minha'  => $this->input->post('ds_pendencia_minha', TRUE),
    		'cd_usuario'          => $this->session->userdata('codigo')
    	);

        if(trim($cd_pendencia_minha) == '')
        {
            $cd_pendencia_minha = $this->pendencia_minha_model->salvar($args);
        }
        else
        {
            $this->pendencia_minha_model->atualizar(trim($cd_pendencia_minha), $args); 
        }

		redirect('servico/pendencia_minha', 'refresh');
    }
}
?>