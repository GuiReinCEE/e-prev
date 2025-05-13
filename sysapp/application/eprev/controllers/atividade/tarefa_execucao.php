<?php

class Tarefa_execucao extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/tarefa_execucao_model');
	}
	
	function index($cd_atividade, $cd_tarefa)
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_tarefa']     = $cd_tarefa;
		$args['cd_atividade']  = $cd_atividade;		
		
		$this->tarefa_execucao_model->tarefa($result, $args);
		$data['row'] = $result->row_array();
		
		$this->tarefa_execucao_model->classificacao_tarefa($result, $args);
		$data['arr_classificacao'] = $result->result_array();
		
		$this->load->view('atividade/tarefa_execucao/index', $data);
	}
	
	function salvar()
	{
		$result = null;
		$data = Array();
		$args = Array();
		$args['cd_atividade']     = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']        = $this->input->post("cd_tarefa", TRUE);
		$args['observacoes']      = $this->input->post("observacoes", TRUE);
		$args['cd_classificacao'] = $this->input->post("cd_classificacao", TRUE);
		
		$this->tarefa_execucao_model->salvar($result, $args);
		
		redirect("atividade/tarefa_execucao/index/".intval($args["cd_atividade"])."/".$args["cd_tarefa"], "refresh");
	}
	
	function play($cd_atividade, $cd_tarefa, $cd_recurso)
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		$args['cd_recurso']   = $cd_recurso;
		
		$this->tarefa_execucao_model->play($result, $args);
		
		redirect("atividade/tarefa_execucao/index/".intval($args["cd_atividade"])."/".$args["cd_tarefa"], "refresh");
	}
	
	function confirmacao_pause($cd_atividade, $cd_tarefa)
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		
		$this->tarefa_execucao_model->tarefa($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/tarefa_execucao/pause', $data);
	}
	
	function confirmacao_stop($cd_atividade, $cd_tarefa)
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		
		$this->tarefa_execucao_model->tarefa($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/tarefa_execucao/stop', $data);
	}
	
	function pause()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$args['cd_recurso']   = $this->input->post("cd_recurso", TRUE);
		$args['ds_obs']       = $this->input->post("ds_obs", TRUE);
		
		$this->tarefa_execucao_model->pause($result, $args);
		
		redirect("atividade/tarefa_execucao/index/".intval($args["cd_atividade"])."/".$args["cd_tarefa"], "refresh");
	}
	
	function stop()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$args['cd_recurso']   = $this->input->post("cd_recurso", TRUE);
		$args['ds_obs']       = $this->input->post("ds_obs", TRUE);
		
		$this->tarefa_execucao_model->stop($result, $args);
		
		redirect("atividade/tarefa_execucao/index/".intval($args["cd_atividade"])."/".$args["cd_tarefa"], "refresh");
	}
	
}

?>