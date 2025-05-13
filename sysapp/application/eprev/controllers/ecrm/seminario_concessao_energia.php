<?php
class seminario_concessao_energia extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		CheckLogin();
		
		$data=array();
		$this->load->view('ecrm/seminario_concessao_energia/index.php', $data);
	}

	function listar()
    {
        CheckLogin();
		$this->load->model('acs/Seminario_concessao_energia_model');
        
        $data['collection'] = array();
        $result = null;

        // --------------------------

        $count = 0;
        $args['page'] = $this->input->post('current_page');

        $this->Seminario_concessao_energia_model->listar( $result, $count, $args );

        $data['quantos'] = $count;
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('ecrm/seminario_concessao_energia/partial_result', $data);
    }
	
	function presente()
    {
        CheckLogin();
		
		$this->load->model('acs/Seminario_concessao_energia_model');
        
		$args['cd_inscricao'] = $this->input->post('cd_inscricao');
		$args['fl_presente'] = $this->input->post('fl_presente');

        $this->Seminario_concessao_energia_model->presente($args);
    }
	
	function excluir()
    {
        CheckLogin();
		
		$this->load->model('acs/Seminario_concessao_energia_model');
        
		$args['cd_inscricao'] = $this->input->post('cd_inscricao');

        $this->Seminario_concessao_energia_model->excluir($args);
    }	
}
?>