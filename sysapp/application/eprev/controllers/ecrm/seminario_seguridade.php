<?php
class seminario_seguridade extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		CheckLogin();
		
		$data=array();
		$this->load->view('ecrm/seminario_seguridade/index.php', $data);
	}

	function listar()
    {
        CheckLogin();
		$this->load->model('acs/Seminario_seguridade_model');
        
        $data['collection'] = array();
        $result = null;

        // --------------------------

        $count = 0;
        $args['nr_ano_edicao'] = $this->input->post('nr_ano_edicao');
		
		manter_filtros($args);

        $this->Seminario_seguridade_model->listar( $result, $count, $args );

        $data['quantos'] = $count;
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('ecrm/seminario_seguridade/partial_result', $data);
    }
	
	function presente()
    {
        CheckLogin();
		
		$this->load->model('acs/Seminario_seguridade_model');
        
		$args['cd_inscricao'] = $this->input->post('cd_inscricao');
		$args['fl_presente'] = $this->input->post('fl_presente');

        $this->Seminario_seguridade_model->presente($args);
    }
	
	function excluir($cd_inscricao)
    {
        CheckLogin();
		
		$this->load->model('acs/Seminario_seguridade_model');
        
		$args['cd_inscricao'] = intval($cd_inscricao);
		$args['fl_presente'] = $this->input->post('fl_presente');

        $this->Seminario_seguridade_model->excluir($args);
		
		redirect("ecrm/seminario_seguridade/", "refresh");
    }	

	function certificado()
	{
        CheckLogin();
		
		$this->load->model('acs/Seminario_seguridade_model');
        
		$args = Array();
		
		$args['cd_inscricao'] = $this->input->post('cd_inscricao');

        $this->Seminario_seguridade_model->certificado($args);		
	}
}
?>