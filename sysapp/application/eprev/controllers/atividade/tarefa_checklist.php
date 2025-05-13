<?php

class Tarefa_checklist extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/tarefa_checklist_model');
	}
	
	function index($cd_atividade, $cd_tarefa)
    {
		$result = null;
		$data = Array();
		$args = Array();
		$data['collection'] = Array();
		
		$args['cd_tarefa']     = $cd_tarefa;
		$args['cd_atividade']  = $cd_atividade;		
		
		$this->tarefa_checklist_model->tarefa($result, $args);
		$data['row'] = $result->row_array();
		
		$args['tipo'] = $data['row']['tipo'];
		
		$this->tarefa_checklist_model->listar_grupos($result, $args);
		$grupos = $result->result_array();
		
		$i = 0;
		
		foreach($grupos as $item)
		{
			$data['collection'][$i]['ds_grupo'] = $item['ds_grupo'];
			
			$args['cd_tarefa_checklist_grupo'] = $item['cd_tarefa_checklist_grupo'];
			$args['cd_tarefa'] = $data['row']['codigo_tarefa'];
			
			$this->tarefa_checklist_model->listar_perguntas($result, $args);			
			$data['collection'][$i]['perguntas'] = $result->result_array();
				
			$i++;
		}
				
		$this->load->view('atividade/tarefa_checklist/index', $data);
	}
	
	function salvar()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$args['cd_atividade']  = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']     = $this->input->post("cd_tarefa", TRUE);
		$args['codigo_tarefa'] = $this->input->post("codigo_tarefa", TRUE);
		$arr_resposta          = $this->input->post("fl_resposta", TRUE);
		$arr_especialista      = $this->input->post("fl_especialista", TRUE);
		
		foreach($arr_resposta as $key => $item)
		{
			$args['cd_tarefa_checklist_pergunta'] = $key;
			$args['fl_resposta']                  = $item;
			$args['fl_especialista']              = (isset($arr_especialista[$key]) ? $arr_especialista[$key] : '');
			
			$this->tarefa_checklist_model->salvar($result, $args);
		}
		
		redirect("atividade/tarefa_checklist/index/".intval($args["cd_atividade"])."/".$args["cd_tarefa"], "refresh");
	}
	
}

?>