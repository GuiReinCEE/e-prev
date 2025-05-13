<?php
class Protocolo_digitalizacao_expedida extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

	public function get_tipo()
	{
    	return array(
    		array('value' => 'P', 'text' => 'Papel'),
    		array('value' => 'D', 'text' => 'Digital')
    	);
	}
	
	public function get_gerado()
	{
    	return array(
    		array('value' => 'S', 'text' => 'Sim'),
    		array('value' => 'N', 'text' => 'Não')
    	);
	}
	
    public function index()
    {
		$this->load->model('projetos/protocolo_digitalizacao_expedida_model');

		$data = array();
		
		$data['fl_gerado'] = $this->get_gerado();
		
		$data['tipo'] = $this->get_tipo();
		
		$this->load->view('ecrm/protocolo_digitalizacao_expedida/index', $data);
    }

    public function listar()
    {
    	$this->load->model('projetos/protocolo_digitalizacao_expedida_model');

    	$args = array();
		$data = array();

		$args['tipo']      = $this->input->post('tipo', TRUE);
		$args['numero']    = $this->input->post('numero', TRUE);
		$args['ano']       = $this->input->post('ano', TRUE);
		$args['dt_ini']	   = $this->input->post('dt_ini', TRUE);
		$args['dt_fim']	   = $this->input->post('dt_fim', TRUE);
		$args['fl_gerado'] = $this->input->post('fl_gerado', TRUE);
		
		manter_filtros($args);
        
        $data['collection'] = $this->protocolo_digitalizacao_expedida_model->listar($args, $this->session->userdata('codigo'));
		
		$this->load->view('ecrm/protocolo_digitalizacao_expedida/index_result', $data);
    }
	
	public function salvar()
	{
		$this->load->model('projetos/protocolo_digitalizacao_expedida_model');

		$documento = explode(',', $this->input->post('documento', TRUE));
		
		$cd_protocolo_digitalizacao_expedida = $this->protocolo_digitalizacao_expedida_model->salvar($documento, $this->session->userdata('codigo'));
	
		redirect('ecrm/protocolo_digitalizacao_expedida/cadastro/'.$cd_protocolo_digitalizacao_expedida, 'refresh');
	}
	
	public function cadastro($cd_protocolo_digitalizacao_expedida)
	{
		$this->load->model('projetos/protocolo_digitalizacao_expedida_model');
		
		$data  = array();
		
		$data['tipo'] = $this->protocolo_digitalizacao_expedida_model->get_tipo();

		$data['discriminacao'] = $this->protocolo_digitalizacao_expedida_model->get_discriminacao();
		
		$data['cd_protocolo_digitalizacao_expedida'] = $cd_protocolo_digitalizacao_expedida;
					
		$this->load->view('ecrm/protocolo_digitalizacao_expedida/cadastro', $data);
	}
	
	public function gerar_protocolo_expedido()
	{
		$this->load->model('projetos/protocolo_digitalizacao_expedida_model');
		
		$args = array();

		$args['cd_protocolo_digitalizacao_expedida'] 	= $this->input->post('cd_protocolo_digitalizacao_expedida', TRUE);
		$args['cd_atendimento_protocolo_tipo'] 			= $this->input->post('cd_atendimento_protocolo_tipo', TRUE);
		$args['cd_atendimento_protocolo_discriminacao'] = $this->input->post('cd_atendimento_protocolo_discriminacao', TRUE);
		$args['ds_identificacao']						= $this->input->post('ds_identificacao', TRUE);
        $args['cd_usuario'] 							= intval($this->session->userdata('codigo'));
		
		$this->protocolo_digitalizacao_expedida_model->gerar_protocolo_expedido($args);
		
		redirect('ecrm/protocolo_digitalizacao_expedida', 'refresh');
	}
}