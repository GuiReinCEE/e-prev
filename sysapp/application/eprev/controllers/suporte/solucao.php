<?php
class solucao extends Controller
{
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/solucao_suporte_model');
	}
	
	function index()
	{
		$args = Array();
		$data = Array();
		$result = null;

		$this->solucao_suporte_model->categoria( $result, $args );
		$data['arr_categoria'] = $result->result_array();
		
		$this->load->view('suporte/solucao/index', $data);
	}

	function listar()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['numero']           = $this->input->post("numero");
		$args['dt_cadastro_ini']  = $this->input->post("dt_cadastro_ini");
		$args['dt_cadastro_fim']  = $this->input->post("dt_cadastro_fim");
		$args['dt_conclusao_ini'] = $this->input->post("dt_conclusao_ini");
		$args['dt_conclusao_fim'] = $this->input->post("dt_conclusao_fim");
		$args['descricao']        = $this->input->post("descricao");
		$args['solucao']          = $this->input->post("solucao");
		$args['cd_categoria']     = $this->input->post("cd_categoria");
		$args['assunto']          = $this->input->post("assunto");

		$this->solucao_suporte_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view('suporte/solucao/partial_result', $data);
	}
}
?>