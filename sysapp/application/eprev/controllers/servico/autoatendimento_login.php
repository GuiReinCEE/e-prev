<?php
class Autoatendimento_login extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
    	if(gerencia_in(array('GGS')))
    	{
    		$this->load->model('autoatendimento/login_model');

    		$data = array(
				'cd_empresa' 			=> $cd_empresa, 
				'cd_registro_empregado' => $cd_registro_empregado, 
				'seq_dependencia' 		=> $seq_dependencia
			);
			
    		$this->load->view('servico/autoatendimento_login/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('autoatendimento/login_model');

    	$args = array();
		$data = array();
		
		$args['cd_empresa']            = $this->input->post('cd_empresa', TRUE);
		$args['cd_registro_empregado'] = $this->input->post('cd_registro_empregado', TRUE);
		$args['seq_dependencia']       = $this->input->post('seq_dependencia', TRUE);
		$args['dt_login_ini']              = $this->input->post('dt_login_ini', TRUE);
		$args['dt_login_fim']              = $this->input->post('dt_login_fim', TRUE);
		
		manter_filtros($args);
		
		$data['collection'] = $this->login_model->listar($args);

		$this->load->view('servico/autoatendimento_login/index_result', $data);
    }
	
	public function acesso($cd_login)
	{
		$this->load->model('autoatendimento/login_model');
		
		$data['login'] = $this->login_model->carregar($cd_login);
		
		$data['collection'] = $this->login_model->acesso_listar($cd_login);
		
		$this->load->view('servico/autoatendimento_login/acesso', $data);
	}
	
	public function acesso_quebrado($cd_login)
	{
		$this->load->model('autoatendimento/login_model');
		
		$data['login'] = $this->login_model->carregar($cd_login);
		
		$data['collection'] = $this->login_model->acesso_quebrado_listar($cd_login);
		
		$this->load->view('servico/autoatendimento_login/acesso_quebrado', $data);
	}
}