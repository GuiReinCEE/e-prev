<?php
class Autoatendimento_acesso extends Controller {

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
			
    		$this->load->view('servico/autoatendimento_acesso/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('autoatendimento/acesso_model');

    	$args = array();
		$data = array();
		
		$args['dt_acesso_ini']         = $this->input->post('dt_acesso_ini', TRUE);
		$args['dt_acesso_fim']         = $this->input->post('dt_acesso_fim', TRUE);
		$args['dt_login_ini']          = $this->input->post('dt_login_ini', TRUE);
		$args['dt_login_fim']          = $this->input->post('dt_login_fim', TRUE);
		$args['cd_empresa']            = $this->input->post('cd_empresa', TRUE);
		$args['seq_dependencia']       = $this->input->post('seq_dependencia', TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado', TRUE);
		
		manter_filtros($args);
		
		$data['collection'] = $this->acesso_model->listar($args);
		
		$this->load->view('servico/autoatendimento_acesso/index_result', $data);
    }
}