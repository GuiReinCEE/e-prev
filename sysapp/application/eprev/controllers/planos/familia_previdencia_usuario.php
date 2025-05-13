<?php
class familia_previdencia_usuario extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		$this->load->model('familia_previdencia/Familia_previdencia_usuario_model');
		$args=array();	
	
		if(gerencia_in(array('GNR')))
		{
			$data = Array();			
			$this->load->view('planos/familia_previdencia_usuario/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function listar()
    {
        CheckLogin();
        $this->load->model('familia_previdencia/Familia_previdencia_usuario_model');
		
		if(gerencia_in(array('GNR')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$this->Familia_previdencia_usuario_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('planos/familia_previdencia_usuario/index_partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function cadastro($cd_usuario = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GNR')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_usuario_model');
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$data['cd_usuario'] = intval($cd_usuario);
			
			if(intval($cd_usuario) == 0)
			{
				$data['row'] = Array('cd_usuario'  => intval($cd_usuario) , 
					                 'usuario'     => '', 
									 'senha'       => '',  
									 'tp_usuario'  => '', 
									 'nome'        => '',  
									 'email'       => '', 
									 'telefone_1'  => '', 
									 'telefone_2'  => '', 
									 'funcao'      => '', 
									 'delegacia'   => '',
									 'fl_troca_senha' => '',
									 'dt_inclusao' => '',
									 'dt_exclusao' => ''
									);
			}
			else
			{
				$args['cd_usuario'] = intval($cd_usuario);
				$this->Familia_previdencia_usuario_model->usuario($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('planos/familia_previdencia_usuario/cadastro.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function salvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GNR')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_usuario_model');

			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_usuario"]     = $this->input->post("cd_usuario", TRUE);
			$args["nome"]           = $this->input->post("nome", TRUE);
			$args["usuario"]        = $this->input->post("usuario", TRUE);
			$args["senha"]          = $this->input->post("senha", TRUE);
			$args["senha_old"]      = $this->input->post("senha_old", TRUE);
			$args["usuario"]        = $this->input->post("usuario", TRUE);
			$args["tp_usuario"]     = $this->input->post("tp_usuario", TRUE);
			$args["fl_troca_senha"] = $this->input->post("fl_troca_senha", TRUE);
			$args["email"]          = $this->input->post("email", TRUE);
			$args["telefone_1"]     = $this->input->post("telefone_1", TRUE);
			$args["telefone_2"]     = $this->input->post("telefone_2", TRUE);
			$args["funcao"]         = $this->input->post("funcao", TRUE);
			$args["delegacia"]      = $this->input->post("delegacia", TRUE);

			
			$cd_usuario_new = $this->Familia_previdencia_usuario_model->salvar($result, $args);
			redirect("planos/familia_previdencia_usuario/cadastro/".$cd_usuario_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
    
    function excluir($cd_usuario = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GNR')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_usuario_model');

			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_usuario"] = intval($cd_usuario);
			$this->Familia_previdencia_usuario_model->excluir($result, $args);
			redirect("planos/familia_previdencia_usuario/cadastro/".$cd_usuario, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
