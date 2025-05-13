<?php
class Autoatendimento_patrocinadoras_libera extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
    	if(gerencia_in(array('GGS')))
    	{
    		$data = array();
			
    		$this->load->view('servico/autoatendimento_patrocinadoras_libera/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }
	
    public function listar()
    {
    	$this->load->model('autoatendimento/patrocinadoras_libera_model');

		$data = array();
		
		$data['collection'] = $this->patrocinadoras_libera_model->listar();
		
		$this->load->view('servico/autoatendimento_patrocinadoras_libera/index_result', $data);
    }

    public function cadastro($cd_patrocinadoras_libera = 0)
    {
    	if(gerencia_in(array('GGS')))
    	{
    		$this->load->model('autoatendimento/patrocinadoras_libera_model');

			$data = array();

			if(intval($cd_patrocinadoras_libera) == 0)
			{
				$ordem = $this->patrocinadoras_libera_model->get_menu_ordem();
				
				$data['empresa'] = $this->patrocinadoras_libera_model->get_empresa();
			
				$data['row'] = array(
					'cd_patrocinadoras_libera' => $cd_patrocinadoras_libera,
					'nr_ano'                   => '',
					'ds_patrocinadoras_libera' => '',
					'nr_ordem'				   => (isset($ordem['nr_ordem']) ? $ordem['nr_ordem'] : 1),
					'nome_empresa'			   => ''
				);
			}
			else
			{
				$data['row'] = $this->patrocinadoras_libera_model->carrega($cd_patrocinadoras_libera);
			}

			$this->load->view('servico/autoatendimento_patrocinadoras_libera/cadastro', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function salvar()
	{
		if(gerencia_in(array('GGS')))
        {
    		$this->load->model('autoatendimento/patrocinadoras_libera_model');

			$args = array();
			
			$cd_patrocinadoras_libera = $this->input->post('cd_patrocinadoras_libera', TRUE);

			$args = array(
				'ds_codigo'   			   => $this->input->post('ds_codigo', TRUE),
				'nr_ano'     			   => $this->input->post('nr_ano', TRUE),
				'nr_ordem'    			   => $this->input->post('nr_ordem', TRUE),
				'ds_patrocinadoras_libera' => $this->input->post('ds_patrocinadoras_libera', TRUE),
				'cd_empresa'     		   => $this->input->post('cd_empresa', TRUE),
				'cd_usuario'  			   => $this->session->userdata('codigo')
			);
			
			if(intval($cd_patrocinadoras_libera) == 0)
			{
				$cd_patrocinadoras_libera = $this->patrocinadoras_libera_model->salvar($args);
			}
			else
			{
				$this->patrocinadoras_libera_model->atualizar($cd_patrocinadoras_libera, $args);
			}
			
			redirect('servico/autoatendimento_patrocinadoras_libera');
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function altera_ordem()
	{
		$this->load->model('autoatendimento/patrocinadoras_libera_model');
			
		$cd_patrocinadoras_libera = $this->input->post('cd_patrocinadoras_libera', TRUE);
		$nr_ordem   			  = $this->input->post('nr_ordem', TRUE);
		$cd_usuario 			  = $this->session->userdata('codigo');
		
		$this->patrocinadoras_libera_model->alterar_ordem($cd_patrocinadoras_libera, $nr_ordem, $cd_usuario);
	}
}