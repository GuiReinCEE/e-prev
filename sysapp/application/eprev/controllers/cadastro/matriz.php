<?php
class matriz extends Controller
{
    function __construct()
    {
        parent::Controller();
        CheckLogin();
		
        $this->load->model('projetos/matriz_model');
    }
    
    function index()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$result = null;
			$args = array();
			$data = array();
			
			$this->matriz_model->classes($result, $args);
			$data['ar_classes'] = $result->result_array();
			
			$this->matriz_model->faixas($result, $args);
			$data['ar_faixas'] = $result->result_array();
		
			$this->load->view('cadastro/matriz/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function lista_colaboradores()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args["cd_usuario_gerencia"] = $this->input->post("cd_usuario_gerencia", TRUE);
			$args["cd_usuario"]          = $this->input->post("cd_usuario", TRUE);
			$args["fl_tipo"]             = $this->input->post("fl_tipo", TRUE);
			$args["cd_familia"]          = $this->input->post("cd_familia", TRUE);
			$args["faixa"]                = $this->input->post("faixa", TRUE);
			
			manter_filtros($args);
			
			$this->matriz_model->lista_colaboradores($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('cadastro/matriz/colaboradores_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro_colaborador($cd_usuario)
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_usuario'] = intval($cd_usuario);

			$this->matriz_model->cd_usuario_matriz($result, $args);
			$arr = $result->row_array();
			
			if(count($arr) > 0) 
			{
				$args['cd_usuario_matriz'] = intval($arr['cd_usuario_matriz']);
			}     
			else 
			{
				$args['cd_usuario_matriz'] = 0;
			}
			
			$this->matriz_model->usuario($result, $args);
			$usuario = $result->row_array();
	 
			$this->matriz_model->escolaridade($result, $args);
			$data['arr_escolaridade'] = $result->result_array();
			
			$this->matriz_model->classe_faixa($result, $args);
			$data['arr_matriz_salarial'] = $result->result_array();
			
			$data['divisao']    = $usuario['divisao'];
			$data['nome']       = $usuario['nome'];
			$data['cd_usuario'] = $usuario['codigo'];
			
			if($args['cd_usuario_matriz'] == 0 )
			{
				$data['row'] = Array(
									'cd_usuario_matriz'  => '0',
									'cd_escolaridade'    => '',
									'cd_matriz_salarial' => '',
									'dt_promocao'        => '',
									'dt_admissao'        => '',
									'tipo_promocao'      => ''
								);
			}
			else
			{
				$this->matriz_model->carrega_colaboradores($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('cadastro/matriz/cadastro_colaborador', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function salvar_colaborador()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_usuario_matriz"]   = $this->input->post("cd_usuario_matriz", TRUE);
			$args["cd_usuario"]          = $this->input->post("cd_usuario", TRUE);
			$args["cd_escolaridade"]     = $this->input->post("cd_escolaridade", TRUE);
			$args["cd_matriz_salarial"]  = $this->input->post("cd_matriz_salarial", TRUE);
			$args["dt_admissao"]         = $this->input->post("dt_admissao", TRUE);
			$args["dt_promocao"]         = $this->input->post("dt_promocao", TRUE);
			$args["tipo_promocao"]       = $this->input->post("tipo_promocao", TRUE);
			$args["cd_usuario_cadastro"] = $this->session->userdata("codigo");
			
			$this->matriz_model->salvar_colaborador($result, $args);
			redirect("cadastro/matriz/index/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function matriz_salarial()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$this->load->view('cadastro/matriz/matriz_salarial');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function lista_matriz_salarial()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$result = null;
			$args = array();
			$data = array();
			
			$this->matriz_model->classes($result, $args);
			$data['collection'] = $result->result_array();
			
			for($i=0; $i < count($data['collection']); $i++)
			{            
				$args['cd_familia'] = $data['collection'][$i]['value'];
				
				$this->matriz_model->lista_matriz_salarial($result, $args);
							
				$data['collection'][$i]['matriz'] = $result->result_array();
			}
			
			$this->load->view('cadastro/matriz/matriz_salarial_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function cadastro_matriz($cd_familia)
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$result = null;
			$args = array();
			$data = array();
		   
			$data['cd_familia'] = $cd_familia;
			
			$this->load->view('cadastro/matriz/cadastro_matriz', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function listar_cadastro_matriz()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args["cd_familia"] = $this->input->post("cd_familia", TRUE);
			
			$this->matriz_model->lista_matriz_salarial($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('cadastro/matriz/cadastro_matriz_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
    
    function salvar_matriz_salarial()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args["cd_familia"] = $this->input->post("cd_familia", TRUE);
			$args["faixa"]      = $this->input->post("faixa", TRUE);
			$args["vl_ini"]     = $this->input->post("vl_ini", TRUE);
			$args["vl_fim"]     = $this->input->post("vl_fim", TRUE);
			$args["cd_usuario"] = $this->session->userdata("codigo");
			
			$this->matriz_model->salvar_matriz_salarial($result, $args);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>