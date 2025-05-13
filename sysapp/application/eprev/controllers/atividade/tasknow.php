<?php
class tasknow extends Controller
{
    function __construct()
    {
        parent::Controller();
		#$this->load->model('projetos/tasknow_model');
    }

	function setAtividade()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['cd_atividade'] = intval(utf8_decode($this->input->post('cd_atividade', true))); 
		$args['ds_atendente'] = utf8_decode($this->input->post('ds_atendente', true)); 
		
		#$this->tasknow->setAtividade($result, $args);
		
		echo json_encode($args);
	}
	
	function getAtividade()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['ds_atendente'] = utf8_decode($this->input->post('ds_atendente', true)); 
		
		#$this->tasknow->getAtividade($result, $args);
		
		$args['cd_atividade'] = 51234;
		
		echo json_encode($args);
	}

	function getMonitor()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['ds_atendente'] = utf8_decode($this->input->post('ds_atendente', true));
		
		#$this->tasknow->getMonitor($result, $args);
		
		$ar_ret = Array("qt_atividade" => 20, "cd_atividade" => 51234, "cd_gerencia" => "GFC");
		
		echo json_encode($ar_ret);
	}	
}
?>