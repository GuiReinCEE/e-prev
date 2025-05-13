<?php
class familia_previdencia_cadastro extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		$this->load->model('familia_previdencia/Familia_previdencia_cadastro_model');
		$args=array();	
	
		if(gerencia_in(array('GRI','GAP')))
		{
			$data = Array();			
			$this->load->view('planos/familia_previdencia_cadastro/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function listar()
    {
        CheckLogin();
        $this->load->model('familia_previdencia/Familia_previdencia_cadastro_model');
		
		if(gerencia_in(array('GRI','GAP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$this->Familia_previdencia_cadastro_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('planos/familia_previdencia_cadastro/index_partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    	
}
