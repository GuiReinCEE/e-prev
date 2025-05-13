<?php
class ferias extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('public/benef_rh_ferias_model');
    }

    function index()
    {
        $this->load->view('servico/ferias/index');
    }

    function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
	
		$args['cd_gerencia'] = $this->session->userdata('divisao');

        $this->benef_rh_ferias_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view('servico/ferias/index_result', $data);
    }
}
?>