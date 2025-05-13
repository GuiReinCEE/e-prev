<?php
class Autoatendimento_login_erro extends Controller {

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

    		$this->load->view('servico/autoatendimento_login_erro/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('autoatendimento/login_erro_model');

    	$args = array();
		$data = array();
		
		$args['cd_empresa']            = $this->input->post('cd_empresa', TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado', TRUE);
		$args['seq_dependencia']       = $this->input->post('seq_dependencia', TRUE);
		$args['dt_inclusao_ini']       = $this->input->post('dt_inclusao_ini', TRUE);
		$args['dt_inclusao_fim']       = $this->input->post('dt_inclusao_fim', TRUE);
		
		manter_filtros($args);
		
		$data['collection'] = $this->login_erro_model->listar($args);

		$this->load->view('servico/autoatendimento_login_erro/index_result', $data);
    }
}