<?php
class legal extends Controller 
{
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();
		
		$this->load->model("projetos/atividades_model");
	}

	function index()
	{
		$this->load->view('atividade/legal/index');
	}

	function listar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args['nao_verificado']         = $this->input->post("nao_verificado");
		$args['nao_pertinente']         = $this->input->post("nao_pertinente");
		$args['pertinente_sem_reflexo'] = $this->input->post("pertinente_sem_reflexo");
		$args['pertinente_com_reflexo'] = $this->input->post("pertinente_com_reflexo");
		$args['tipo_usuario']           = $this->session->userdata('tipo');
		$args['cd_gerencia']               = $this->session->userdata('divisao');
		$args['cd_usuario']             = $this->session->userdata('codigo');
		$args['dt_ini']                 = $this->input->post("dt_ini");
		$args['dt_fim']                 = $this->input->post("dt_fim");

		$this->atividades_model->listar_legal($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('atividade/legal/index_result', $data);
	}
}
?>