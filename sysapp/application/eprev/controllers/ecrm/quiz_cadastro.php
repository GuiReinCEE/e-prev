<?php
class quiz_cadastro extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		$data = Array();			
		$this->load->view('ecrm/quiz_cadastro/index.php',$data);
    }	
	
    function inscricaoListar()
    {
        CheckLogin();
        $this->load->model('acs/Quiz_model');

        $data['collection'] = array();
        $result = null;
		$args=array();
		
        $this->Quiz_model->quizCadastroListar( $result, $args );
		$data['collection'] = $result->result_array();
        $this->load->view('ecrm/quiz_cadastro/index_result', $data);
    }	
	
    
}
