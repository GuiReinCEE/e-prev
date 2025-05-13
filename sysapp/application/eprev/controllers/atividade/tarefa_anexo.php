<?php

class Tarefa_anexo extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/tarefa_anexo_model');
	}
	
	function index($cd_atividade, $cd_tarefa)
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_tarefa']     = $cd_tarefa;
		$args['cd_atividade']  = $cd_atividade;		
		
		$this->tarefa_anexo_model->tarefa($result, $args);
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/tarefa_anexo/index', $data);
	}
	
	function listar()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['codigo']    = $this->input->post("codigo", TRUE);
		$args['cd_tarefa'] = $this->input->post("cd_tarefa", TRUE);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$this->tarefa_anexo_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->tarefa_anexo_model->permissao_excluir($result, $args);
		$arr = $result->row_array();
		
		$data['fl_excluir'] = false;
		
		if((intval($arr['cd_mandante']) == $cd_usuario OR intval($arr['cd_recurso']) == $cd_usuario) AND $arr['dt_fim_prog'] == '' )
		{
			$data['fl_excluir'] = true;
		}
		
		$this->load->view('atividade/tarefa_anexo/index_result', $data);
	}
	
	function salvar()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$qt_arquivo = intval($this->input->post("arquivo_m_count", TRUE));
		
		if($qt_arquivo > 0)
		{
			$nr_conta = 0;
			while($nr_conta < $qt_arquivo)
			{
				$result = null;
				$data = Array();
				$args = Array();		
				
				$args['arquivo_nome']  = $this->input->post("arquivo_m_".$nr_conta."_name", TRUE);
				$args['arquivo']       = $this->input->post("arquivo_m_".$nr_conta."_tmpname", TRUE);
				
				$args['cd_tarefa']     = $this->input->post("cd_tarefa", TRUE);
				$args["cd_atividade"]  = $this->input->post("cd_atividade", TRUE);
				$args["codigo"]        = $this->input->post("codigo", TRUE);
				$args["cd_usuario"]    = $this->session->userdata('codigo');
				
				$this->tarefa_anexo_model->salvar($result, $args);
				
				$nr_conta++;
			}
		}
		
		redirect("atividade/tarefa_anexo/index/".intval($args["cd_atividade"])."/".$args["cd_tarefa"], "refresh");
	}
	
	function excluir($cd_atividade, $cd_tarefa, $cd_tarefa_anexo)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_tarefa']       = $cd_tarefa;
		$args['cd_atividade']    = $cd_atividade;
		$args['cd_tarefa_anexo'] = $cd_tarefa_anexo;
		$args["cd_usuario"]      = $this->session->userdata('codigo');

		$this->tarefa_anexo_model->excluir($result, $args);
		
		redirect("atividade/tarefa_anexo/index/".intval($args["cd_atividade"])."/".$args["cd_tarefa"], "refresh");
	}
}


?>