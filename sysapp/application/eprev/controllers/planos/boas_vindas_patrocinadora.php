<?php
class boas_vindas_patrocinadora extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/boas_vindas_controle_patrocinadora_model');
    }
	
    function index($cd_plano = "", $cd_plano_empresa = "")
    {
		if(gerencia_in(array('GAP')))
		{
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$data['cd_plano']         = $cd_plano;
			$data['cd_plano_empresa'] = $cd_plano_empresa;
			
			$this->load->view('planos/boas_vindas_patrocinadora/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function listar()
    {
		if(gerencia_in(array('GAP')))
		{
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$args['cd_plano']        = $this->input->post("cd_plano", TRUE);
			$args['cd_empresa']      = $this->input->post("cd_plano_empresa", TRUE);
			$args['dt_ini_ingresso'] = $this->input->post("dt_ini_ingresso", TRUE);
			$args['dt_fim_ingresso'] = $this->input->post("dt_fim_ingresso", TRUE);
			$args['fl_email']        = $this->input->post("fl_email", TRUE);
			$args['fl_certificado']  = $this->input->post("fl_certificado", TRUE);
			$args['fl_enviado']      = $this->input->post("fl_enviado", TRUE);
			$args['fl_inscricao']    = $this->input->post("fl_inscricao", TRUE);
			$args['fl_gerado']       = $this->input->post("fl_gerado", TRUE);
			$args['fl_eletronico']   = $this->input->post("fl_eletronico", TRUE);
			
			manter_filtros($args);
			
			$this->boas_vindas_controle_patrocinadora_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('planos/boas_vindas_patrocinadora/index_result', $data);			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function enviar()
    {
		if(gerencia_in(array('GAP')))
		{
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$args['part_selecionado'] = $this->input->post('part_selecionado', TRUE);
			$args['cd_empresa']       = $this->input->post('cd_empresa', TRUE);
			$args['cd_plano']         = $this->input->post('cd_plano', TRUE);
			$args['cd_usuario']       = $this->session->userdata('codigo');
			
			$this->boas_vindas_controle_patrocinadora_model->enviar($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
}

?>