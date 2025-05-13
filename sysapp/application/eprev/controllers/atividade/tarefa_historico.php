<?php

class Tarefa_historico extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/tarefa_historico_model');
	}
	
	function index($cd_atividade, $cd_tarefa)
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;		
		
		$this->tarefa_historico_model->tarefa($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/tarefa_historico/index', $data);
	}
	
	function listar()
	{
		$result = null;
		$data = Array();
		$args = Array();
				
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);;
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		
		$this->tarefa_historico_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/tarefa_historico/index_result', $data);
	}
	
}

?>