<?php
class familia_previdencia_delegacia_cidade extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		$this->load->model('familia_previdencia/Familia_previdencia_delegacia_cidade_model');
		$args=array();	
	
		if(gerencia_in(array('GRI')))
		{
			$data = Array();			
			
			$this->Familia_previdencia_delegacia_cidade_model->delegaciaCombo($result, $args);
			$data['delegacia_dd'] = $result->result_array();				
			
			$this->load->view('planos/familia_previdencia_delegacia_cidade/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function listar()
    {
        CheckLogin();
        $this->load->model('familia_previdencia/Familia_previdencia_delegacia_cidade_model');
		
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_delegacia"] = $this->input->post("cd_delegacia", TRUE);
			
			manter_filtros($args);
			
			$this->Familia_previdencia_delegacia_cidade_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('planos/familia_previdencia_delegacia_cidade/index_partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function cadastro($cd_delegacia_cidade = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_delegacia_cidade_model');
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$data['cd_delegacia_cidade'] = intval($cd_delegacia_cidade);
			
			$this->Familia_previdencia_delegacia_cidade_model->delegaciaCombo($result, $args);
			$data['delegacia_dd'] = $result->result_array();				
			
			if(intval($cd_delegacia_cidade) == 0)
			{
				$data['row'] = Array('cd_delegacia_cidade'  => intval($cd_delegacia_cidade) , 
					                 'nome'         => '',
					                 'cd_delegacia' => '',
									 'dt_inclusao'  => '',
									 'dt_exclusao'  => ''
									);
			}
			else
			{
				$args['cd_delegacia_cidade'] = intval($cd_delegacia_cidade);
				$this->Familia_previdencia_delegacia_cidade_model->cidade($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('planos/familia_previdencia_delegacia_cidade/cadastro.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	

    function salvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_delegacia_cidade_model');

			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_delegacia_cidade"] = $this->input->post("cd_delegacia_cidade", TRUE);
			$args["nome"]                = $this->input->post("nome", TRUE);
			$args["cd_delegacia"]        = $this->input->post("cd_delegacia", TRUE);
			
			$cd_cidade_new = $this->Familia_previdencia_delegacia_cidade_model->salvar($result, $args);
			redirect("planos/familia_previdencia_delegacia_cidade/cadastro/".$cd_cidade_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
    
    function excluir($cd_delegacia_cidade = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GRI')))
		{
			$this->load->model('familia_previdencia/Familia_previdencia_delegacia_cidade_model');

			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_delegacia_cidade"] = intval($cd_delegacia_cidade);
			$this->Familia_previdencia_delegacia_cidade_model->excluir($result, $args);
			redirect("planos/familia_previdencia_delegacia_cidade/cadastro/".$cd_delegacia_cidade, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
