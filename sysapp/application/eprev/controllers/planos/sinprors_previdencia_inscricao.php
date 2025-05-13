<?php
class sinprors_previdencia_inscricao extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('sinprors_previdencia/sinprors_previdencia_inscricao_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GRI', 'GI')))
		{		
			$this->load->view('planos/sinprors_previdencia_inscricao/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function listar()
    {		
		if(gerencia_in(array('GRI', 'GI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['dt_inscricao_ini']       = $this->input->post("dt_inscricao_ini", TRUE);   
			$args['dt_inscricao_fim']       = $this->input->post("dt_inscricao_fim", TRUE);
			$args['dt_inclusao_gap_ini']    = $this->input->post("dt_inclusao_gap_ini", TRUE);   
			$args['dt_inclusao_gap_fim']    = $this->input->post("dt_inclusao_gap_fim", TRUE);
			$args['dt_ingresso_eletro_ini'] = $this->input->post("dt_ingresso_eletro_ini", TRUE);   
			$args['dt_ingresso_eletro_fim'] = $this->input->post("dt_ingresso_eletro_fim", TRUE);   
			$args['fl_cadastro_gap']        = $this->input->post("fl_cadastro_gap", TRUE);   
			$args['fl_participante']        = $this->input->post("fl_participante", TRUE);   
			
			manter_filtros($args);
			
			$this->sinprors_previdencia_inscricao_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('planos/sinprors_previdencia_inscricao/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
}
?>