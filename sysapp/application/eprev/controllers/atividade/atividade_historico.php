<?php

class Atividade_historico extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/atividade_historico_model');
	}
	
	function index($cd_atividade, $cd_gerencia)
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$data['cd_atividade'] = $cd_atividade;
		$data['cd_gerencia']  = $cd_gerencia;		
		
		$this->load->view('atividade/atividade_historico/index', $data);
	}
	
	function listar()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		
		$this->atividade_historico_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/atividade_historico/index_result', $data);
	}
	
	function prioridade_historico()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		
		$this->atividade_historico_model->prioridade_historico($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/atividade_historico/prioridade_historico_result', $data);
	}	
}

?>