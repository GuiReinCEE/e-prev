<?php
class Lista_negra_divulgacao extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
    	$this->load->view('ecrm/lista_negra_divulgacao/index');
    }

    public function listar()
    {		
    	$this->load->model('projetos/lista_negra_divulgacao_model');

		$args = array(
			'ds_lista_negra_divulgacao' => $this->input->post('ds_lista_negra_divulgacao', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->lista_negra_divulgacao_model->listar($args);

		foreach ($data['collection'] as $key => $item) 
		{
			$data['collection'][$key]['emails'] = array();
			
			$collection = $this->lista_negra_divulgacao_model->listar_email($item['cd_lista_negra_divulgacao']);

			foreach ($collection as $email) 
			{
				$data['collection'][$key]['emails'][] = $email['ds_lista_negra_divulgacao_email'];
			}
		}

		$this->load->view('ecrm/lista_negra_divulgacao/index_result', $data);
    }

    public function cadastro($cd_lista_negra_divulgacao = 0)
	{
		$this->load->model('projetos/lista_negra_divulgacao_model');

		$data['collection'] = array();

		if(intval($cd_lista_negra_divulgacao) == 0)
		{
			$data['row'] = array(
				'cd_lista_negra_divulgacao' => intval($cd_lista_negra_divulgacao),
				'ds_lista_negra_divulgacao' => ''				
			);
		}
		else
		{
			$data['row'] = $this->lista_negra_divulgacao_model->carrega($cd_lista_negra_divulgacao);
		}
		
		$this->load->view('ecrm/lista_negra_divulgacao/cadastro', $data);				
	}

	public function salvar()
	{
		$this->load->model('projetos/lista_negra_divulgacao_model');

		$cd_lista_negra_divulgacao = $this->input->post('cd_lista_negra_divulgacao', TRUE);

        $args = array(
        	'ds_lista_negra_divulgacao'      => $this->input->post('ds_lista_negra_divulgacao', TRUE),
        	'ds_lista_negra_divulgacao_email'=> $this->input->post('ds_lista_negra_divulgacao_email', TRUE),
        	'cd_usuario'                     => $this->session->userdata('codigo')
		);
				
		if(intval($cd_lista_negra_divulgacao) == 0)
		{
			$cd_lista_negra_divulgacao = $this->lista_negra_divulgacao_model->salvar($args);
		}
		else
		{
			$this->lista_negra_divulgacao_model->atualizar($cd_lista_negra_divulgacao, $args);			
		}
		
		redirect('ecrm/lista_negra_divulgacao/email/'.$cd_lista_negra_divulgacao);
		
	}

	public function email($cd_lista_negra_divulgacao, $cd_lista_negra_divulgacao_email = 0)
	{
		$this->load->model('projetos/lista_negra_divulgacao_model');

		$data = array(
			'grupo'       => $this->lista_negra_divulgacao_model->carrega($cd_lista_negra_divulgacao),
		    'collection'  => $this->lista_negra_divulgacao_model->listar_email($cd_lista_negra_divulgacao)
		);

		if(intval($cd_lista_negra_divulgacao_email) == 0)
		{
			$data['row'] = array(
				'cd_lista_negra_divulgacao_email' => intval($cd_lista_negra_divulgacao_email),
				'ds_lista_negra_divulgacao_email' => ''				
			);
		}
		else
		{
			$data['row'] = $this->lista_negra_divulgacao_model->carrega_email($cd_lista_negra_divulgacao_email);
		}
		
		$this->load->view('ecrm/lista_negra_divulgacao/email', $data);				
	}

	public function salvar_email()
	{
		$this->load->model('projetos/lista_negra_divulgacao_model');

		$cd_lista_negra_divulgacao       = $this->input->post('cd_lista_negra_divulgacao', TRUE);
		$cd_lista_negra_divulgacao_email = $this->input->post('cd_lista_negra_divulgacao_email', TRUE);

        $args = array(
        	'ds_lista_negra_divulgacao_email'=> $this->input->post('ds_lista_negra_divulgacao_email', TRUE),
        	'cd_usuario'                     => $this->session->userdata('codigo')
		);
				
		if(intval($cd_lista_negra_divulgacao_email) == 0)
		{
			$cd_lista_negra_divulgacao_email = $this->lista_negra_divulgacao_model->salvar_email($cd_lista_negra_divulgacao, $args);
		}
		else
		{
			$this->lista_negra_divulgacao_model->atualizar_email($cd_lista_negra_divulgacao_email, $args);			
		}
		
		redirect('ecrm/lista_negra_divulgacao/email/'.$cd_lista_negra_divulgacao);
		
	}

	public function excluir($cd_lista_negra_divulgacao)
	{
		$this->load->model('projetos/lista_negra_divulgacao_model');

		$this->lista_negra_divulgacao_model->excluir($cd_lista_negra_divulgacao, $this->session->userdata('codigo'));
				
		redirect('ecrm/lista_negra_divulgacao');
	}

	public function excluir_email($cd_lista_negra_divulgacao, $cd_lista_negra_divulgacao_email)
	{
		$this->load->model('projetos/lista_negra_divulgacao_model');

		$this->lista_negra_divulgacao_model->excluir_email($cd_lista_negra_divulgacao_email, $this->session->userdata('codigo'));
				
		redirect('ecrm/lista_negra_divulgacao/email/'.$cd_lista_negra_divulgacao);
	}
}
?>