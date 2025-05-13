<?php
class senge_usuario extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('senge_previdencia/usuario_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GCM')))
		{		
			$this->load->view('planos/senge_usuario/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function listar()
    {		
		if(gerencia_in(array('GCM')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$this->usuario_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('planos/senge_usuario/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function cadastro($cd_usuario = 0)
    {	
		if(gerencia_in(array('GCM')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_usuario'] = intval($cd_usuario);
			
			if(intval($cd_usuario) == 0)
			{
				$data['row'] = Array(
					'cd_usuario'     => intval($cd_usuario), 
				    'usuario'        => '', 
					'senha'          => '',  
					'tp_usuario'     => '', 
					'nome'           => '',  
					'email'          => '', 
					'telefone_1'     => '', 
					'telefone_2'     => '', 
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
			$this->load->view('planos/senge_usuario/cadastro',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function salvar()
    {	
		if(gerencia_in(array('GCM')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_usuario"]          = $this->input->post("cd_usuario", TRUE);
			$args["nome"]                = $this->input->post("nome", TRUE);
			$args["cpf"]                 = $this->input->post("cpf", TRUE);
			$args["usuario"]             = $this->input->post("usuario", TRUE);
			$args["senha"]               = $this->input->post("senha", TRUE);
			$args["senha_old"]           = $this->input->post("senha_old", TRUE);
			$args["usuario"]             = $this->input->post("usuario", TRUE);
			$args["tp_usuario"]          = $this->input->post("tp_usuario", TRUE);
			$args["fl_troca_senha"]      = $this->input->post("fl_troca_senha", TRUE);
			$args["email"]               = $this->input->post("email", TRUE);
			$args["telefone_1"]          = $this->input->post("telefone_1", TRUE);
			$args["telefone_2"]          = $this->input->post("telefone_2", TRUE);
			$args['cd_usuario_inclusao'] = $this->session->userdata('codigo');
			
			$this->usuario_model->salvar($result, $args);
			redirect("planos/senge_usuario", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function excluir($cd_usuario = 0)
    {
		if(gerencia_in(array('GCM')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_usuario"]          = intval($cd_usuario);
			$args['cd_usuario_exclusao'] = $this->session->userdata('codigo');
			
			$this->usuario_model->excluir($result, $args);
			
			redirect("planos/senge_usuario", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
?>