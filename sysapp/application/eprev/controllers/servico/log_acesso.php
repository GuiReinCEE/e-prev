<?php
class Log_acesso extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
    	if($this->session->userdata('indic_05') == 'S')
    	{
    		$data = array();
			
    		$this->load->view('servico/log_acesso/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('projetos/log_acesso_menu_model');

    	$args = array();
		$data = array();

		$args = array(
			'dt_acesso_ini' => $this->input->post('dt_acesso_ini', TRUE),
			'dt_acesso_fim' => $this->input->post('dt_acesso_fim', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->log_acesso_menu_model->listar($args);

		$this->load->view('servico/log_acesso/index_result', $data);
    }

    public function menu()
    {
    	if($this->session->userdata('indic_05') == 'S')
    	{
    		$data = array();
			
    		$this->load->view('servico/log_acesso/menu', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }
    public function listar_menu()
    {
    	$this->load->model('projetos/log_acesso_menu_model');

    	$args = array();
		$data = array();

		$args = array(
			'nr_ano' => $this->input->post('nr_ano', TRUE),
			'nr_mes' => $this->input->post('nr_mes', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->log_acesso_menu_model->listar_menu($args);

		$this->load->view('servico/log_acesso/menu_result', $data);
    }
}