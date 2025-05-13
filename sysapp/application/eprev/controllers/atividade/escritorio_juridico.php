<?php
class escritorio_juridico extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('escritorio_juridico/escritorio_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GP')))
		{							
			$this->load->view('atividade/escritorio_juridico/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function listar()
    {		
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["nome_fantasia"] = $this->input->post("nome_fantasia", TRUE);
			$args["representante"] = $this->input->post("representante", TRUE);
			$args["fl_ativo"]      = $this->input->post("fl_ativo", TRUE);
			
			$this->escritorio_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/escritorio_juridico/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function ativar()
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_escritorio_oracle"] = $this->input->post("cd_escritorio_oracle", TRUE);
			$args['cd_usuario']           = $this->session->userdata('codigo');

			$args['cd_escritorio'] = $this->escritorio_model->ativar($result, $args);
			$this->escritorio_model->monta_menu($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function desativar()
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_escritorio"] = $this->input->post("cd_escritorio", TRUE);
			$args['cd_usuario']    = $this->session->userdata('codigo');
			
			$this->escritorio_model->desativar($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function reativar()
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_escritorio"] = $this->input->post("cd_escritorio", TRUE);
			$args['cd_usuario']    = $this->session->userdata('codigo');
			
			$this->escritorio_model->reativar($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}
?>