<?php

class reclamacao_responder extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/reclamacao_responder_model');
    }
	
	public function index()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$this->reclamacao_responder_model->classificacao($result, $args);
		$data['arr_classificacao'] = $result->result_array();

		$this->load->view('ecrm/reclamacao_responder/index', $data);
    }
	
	public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['nr_ano'] 						  = $this->input->post("nr_ano", TRUE);   
		$args['nr_numero']                        = $this->input->post("nr_numero", TRUE);   
		$args['cd_reclamacao_analise_classifica'] = $this->input->post("cd_reclamacao_analise_classifica", TRUE);   
		$args['dt_envio_ini'] 					  = $this->input->post("dt_envio_ini", TRUE);   
		$args['dt_envio_fim'] 					  = $this->input->post("dt_envio_fim", TRUE);   
		$args['dt_limite_ini'] 					  = $this->input->post("dt_limite_ini", TRUE);   
		$args['dt_limite_fim'] 					  = $this->input->post("dt_limite_fim", TRUE);   
		$args['dt_prorrogacao_ini'] 			  = $this->input->post("dt_prorrogacao_ini", TRUE);   
		$args['dt_prorrogacao_fim'] 			  = $this->input->post("dt_prorrogacao_fim", TRUE);   
		$args['dt_retorno_ini'] 				  = $this->input->post("dt_retorno_ini", TRUE);   
		$args['dt_retorno_fim'] 				  = $this->input->post("dt_retorno_fim", TRUE);   
		$args['fl_retornado'] 			    	  = $this->input->post("fl_retornado", TRUE);   
		$args['fl_atrasado'] 		  		      = $this->input->post("fl_atrasado", TRUE);  
		$args['cd_usuario']                       = $this->session->userdata('codigo');
		
		manter_filtros($args);

		$this->reclamacao_responder_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('ecrm/reclamacao_responder/index_result', $data);
    }
	
	function cadastro($cd_reclamacao_analise)
    {
		$args = Array();
		$data = Array();
		$result = null;

		$args['cd_reclamacao_analise'] = intval($cd_reclamacao_analise);
		
		$this->reclamacao_responder_model->carrega($result, $args);
		$data['row'] = $result->row_array();

		$this->load->view('ecrm/reclamacao_responder/cadastro', $data);
    }
	
	function listar_reclamacao()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_reclamacao_analise'] = $this->input->post("cd_reclamacao_analise", TRUE);   
		
		$this->reclamacao_responder_model->reclamacao($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/reclamacao_responder/cadastro_result', $data);
	}
    
    function parecer($cd_reclamacao_analise_item)
	{
        $args = Array();
		$data = Array();
		$result = null;
        
        $args['cd_reclamacao_analise_item'] = $cd_reclamacao_analise_item;  
        
        $this->reclamacao_responder_model->parecer($result, $args);
		$data['row'] = $result->row_array();

		$this->load->view('ecrm/reclamacao_responder/parecer', $data);
    }
   
	function salvar_nc()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_reclamacao_analise_item'] = $this->input->post("cd_reclamacao_analise_item", TRUE);   
		$args['nr_ano_nc']                  = $this->input->post("nr_ano_nc", TRUE);   
		$args['nr_nc']                      = $this->input->post("nr_nc", TRUE);  
        $args['nr_ano_sap']                 = '';   
		$args['nr_sap']                     = '';  
        $args['ds_retorno']                 = utf8_decode($this->input->post("ds_retorno", TRUE));  
		$args['cd_usuario']                 = $this->session->userdata('codigo');

		$this->reclamacao_responder_model->verifica_nc($result, $args);
		$row = $result->row_array();
		
		if((intval($row['tl']) > 0) OR ((trim($args['nr_nc']) == '') AND (trim($args['nr_ano_nc']) == '')))
		{
			$this->reclamacao_responder_model->salvar_retorno($result, $args);
		
			echo 1;
		}
		else
		{
			echo 0;
		}
	}
    
    function salvar_sap()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_reclamacao_analise_item'] = $this->input->post("cd_reclamacao_analise_item", TRUE);   
		$args['nr_ano_sap']                 = $this->input->post("nr_ano_sap", TRUE);   
		$args['nr_sap']                     = $this->input->post("nr_sap", TRUE);  
        $args['nr_ano_nc']                  = '';   
		$args['nr_nc']                      = '';  
        $args['ds_retorno']                 = utf8_decode($this->input->post("ds_retorno", TRUE));  
		$args['cd_usuario']                 = $this->session->userdata('codigo');

		$this->reclamacao_responder_model->verifica_sap($result, $args);
		$row = $result->row_array();
		
		if((intval($row['tl']) > 0) OR ((trim($args['nr_sap']) == '') AND (trim($args['nr_ano_sap']) == '')))
		{
			$this->reclamacao_responder_model->salvar_retorno($result, $args);
		
			echo 1;
		}
		else
		{
			echo 0;
		}
	}
    
    function salvar_retorno()
    {
        $args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_reclamacao_analise_item'] = $this->input->post("cd_reclamacao_analise_item", TRUE);  
        $args['nr_ano_nc']                  = '';   
		$args['nr_nc']                      = '';  
        $args['nr_ano_sap']                 = '';   
		$args['nr_sap']                     = '';  
        $args['ds_retorno']                 = utf8_decode($this->input->post("ds_retorno", TRUE));  
		$args['cd_usuario']                 = $this->session->userdata('codigo');

		$this->reclamacao_responder_model->salvar_retorno($result, $args);
    }
	
	function retorno($cd_reclamacao_analise)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_reclamacao_analise'] = $cd_reclamacao_analise;   
		$args['cd_usuario']            = $this->session->userdata('codigo');
		
		$this->reclamacao_responder_model->retorno($result, $args);
		
		redirect("ecrm/reclamacao_responder/cadastro/".$cd_reclamacao_analise, "refresh");
	}
	
}
?>