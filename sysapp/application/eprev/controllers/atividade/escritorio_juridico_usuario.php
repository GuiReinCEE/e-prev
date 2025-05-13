<?php
class escritorio_juridico_usuario extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('escritorio_juridico/usuario_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GP')))
		{		
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$this->usuario_model->escritorio($result, $args);
			$data['arr_escritorio'] = $result->result_array();
		
			$this->load->view('atividade/escritorio_juridico_usuario/index', $data);
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
			
			$args["cd_escritorio"] = $this->input->post("cd_escritorio", TRUE);
			
			$this->usuario_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/escritorio_juridico_usuario/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function cadastro($cd_usuario = 0)
    {	
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_usuario'] = intval($cd_usuario);
			
			$this->usuario_model->escritorio($result, $args);
			$data['arr_escritorio'] = $result->result_array();
			
			if(intval($cd_usuario) == 0)
			{
				$data['row'] = Array(
					'cd_usuario'     => intval($cd_usuario), 
					'cd_escritorio'  => '',
					'senha'          => '',  
					'nome'           => '',  
					'email'          => '', 
					'telefone1'     => '', 
					'telefone2'     => '', 
					'fl_troca_senha' => '',
					'dt_inclusao'    => '',
					'dt_exclusao'    => ''
				);
			}
			else
			{
				$this->usuario_model->carrega($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('atividade/escritorio_juridico_usuario/cadastro',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function salvar()
    {	
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_usuario"]          = $this->input->post("cd_usuario", TRUE);
			$args["cd_escritorio"]       = $this->input->post("cd_escritorio", TRUE);
			$args["nome"]                = $this->input->post("nome", TRUE);
			$args["cpf"]                 = $this->input->post("cpf", TRUE);
			$args["senha"]               = $this->input->post("senha", TRUE);
			$args["senha_old"]           = $this->input->post("senha_old", TRUE);
			$args["usuario"]             = $this->input->post("usuario", TRUE);
			$args["fl_troca_senha"]      = $this->input->post("fl_troca_senha", TRUE);
			$args["email"]               = $this->input->post("email", TRUE);
			$args["telefone1"]           = $this->input->post("telefone1", TRUE);
			$args["telefone2"]           = $this->input->post("telefone2", TRUE);
			$args['cd_usuario_inclusao'] = $this->session->userdata('codigo');
			
			$this->usuario_model->salvar($result, $args);
			
			redirect("atividade/escritorio_juridico_usuario", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function excluir($cd_usuario = 0)
    {
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_usuario"]          = intval($cd_usuario);
			$args['cd_usuario_exclusao'] = $this->session->userdata('codigo');
			
			$this->usuario_model->excluir($result, $args);
			
			redirect("atividade/escritorio_juridico_usuario", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
?>