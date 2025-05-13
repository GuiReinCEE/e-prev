<?php
class dialogo_inscricao extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GRI','DE')))
		{		
			$this->load->model('acs/Dialogo_model');
		   
			$result = null;
			$data   = Array();
			$args   = Array();
			
			$this->Dialogo_model->comboEdicao( $result, $args );
			$data['ar_edicao'] = $result->result_array();			
			
			$this->load->view('ecrm/dialogo_inscricao/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function inscricaoListar()
    {
        CheckLogin();
        $this->load->model('acs/Dialogo_model');

        
        $result = null;
		$data   = Array();
		$args   = Array();
		
		$args["cd_dialogo"] = $this->input->post('cd_dialogo', TRUE);
		$args["fl_presente"] = $this->input->post('fl_presente', TRUE);
		
		manter_filtros($args);
		
        $this->Dialogo_model->listar_inscricao( $result, $args );
		$data['collection'] = $result->result_array();
        $this->load->view('ecrm/dialogo_inscricao/index_result', $data);
    }	
	
	
    function inscricaoExcluir($cd_dialogo_inscricao = 0)
    {
        CheckLogin();
        if(gerencia_in(array('GRI')))
		{
			$this->load->model('acs/Dialogo_model');
			$result = null;
			$data   = Array();
			$args   = Array();
			
			$args["cd_dialogo_inscricao"] = intval($cd_dialogo_inscricao);
			$args["cd_usuario"]           = $this->session->userdata('codigo');
			$this->Dialogo_model->inscricaoExcluir($result, $args);
			redirect("ecrm/dialogo_inscricao", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }		
	
    function cadastro($cd_dialogo_inscricao = 0)
    {
        CheckLogin();
        $this->load->model('acs/Dialogo_model');

        $data['collection'] = array();
        $result = null;
		$args=array();	
		$args['cd_dialogo_inscricao'] = intval($cd_dialogo_inscricao);
		
        $this->Dialogo_model->inscricao( $result, $args );
		$data['row'] = $result->row_array();
        $this->load->view('ecrm/dialogo_inscricao/cadastro.php',$data);
    }	

	function setPresente()
    {
		CheckLogin();
		$this->load->model('acs/Dialogo_model');

		$result = null;
		$args = Array();

		$args['cd_dialogo_inscricao'] = $this->input->post('cd_dialogo_inscricao');
		$args['fl_presente']          = $this->input->post('fl_presente');

        $fl_retorno = $this->Dialogo_model->setPresente($args);
		
		echo $fl_retorno;
    }
	
	function enviaCertificado()
    {
		CheckLogin();
		$this->load->model('acs/Dialogo_model');

		$result = null;
		$args = Array();

		$args['cd_certificado'] = $this->input->post('cd_certificado');

        $fl_retorno = $this->Dialogo_model->enviaCertificado($args);
		
		echo $fl_retorno;
    }

	function enviaCertificadoLista()
    {
		CheckLogin();
		$this->load->model('acs/Dialogo_model');

		$result = null;
		$args = Array();

        $fl_retorno = $this->Dialogo_model->enviaCertificadoLista($args);
		
		echo $fl_retorno;
    }	
}
