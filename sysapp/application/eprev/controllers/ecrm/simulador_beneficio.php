<?php
class simulador_beneficio extends Controller
{
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();
	}

	function index()
	{
		if(gerencia_in(array('GCM','GAP.','DE', 'GRSC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$this->load->view('ecrm/simulador_beneficio/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	function listar()
	{
		if(gerencia_in(array('GCM','GAP.','DE')))
		{
			$args = Array();
			$data = Array();
			$result = null;

			//$this->session->userdata('codigo')
			
			#### CEEEPrev ####
			$data["collection"][] = array("plano" => "CEEEPrev - Novo Participante", "url" => 'http://www.ceeeprev.com.br/?page_id=3314', "fl_participante" => false);
			$data["collection"][] = array("plano" => "CEEEPrev - Participante", "url" => 'https://www.fundacaoceee.com.br/auto_atendimento_simulador.php?USER_MD5='.MD5($this->session->userdata('usuario')), "fl_participante" => true);
			
			#### CRMPrev ####
			$data["collection"][] = array("plano" => "CRMPrev - Novo Participante", "url" => 'http://www.crmprev.com.br/?page_id=3314', "fl_participante" => false);
			$data["collection"][] = array("plano" => "CRMPrev - Participante", "url" => 'https://www.fundacaoceee.com.br/auto_atendimento_simulador.php?USER_MD5='.MD5($this->session->userdata('usuario')), "fl_participante" => true);	

			#### INPELPrev ####
			$data["collection"][] = array("plano" => "Família Corporativo - Novo Participante", "url" => 'https://www.familiaprevidencia.com.br', "fl_participante" => false);
			$data["collection"][] = array("plano" => "Família Corporativo - Participante", "url" => 'https://www.fundacaoceee.com.br/auto_atendimento_simulador.php?USER_MD5='.MD5($this->session->userdata('usuario')), "fl_participante" => true);				
			
			#### SENGE ####
			$data["collection"][] = array("plano" => "SENGE - Novo Participante", "url" => 'https://www.fundacaoceee.com.br/simulador_senge.php', "fl_participante" => false);
			$data["collection"][] = array("plano" => "SENGE - Participante", "url" => 'https://www.fundacaoceee.com.br/auto_atendimento_simulador.php?USER_MD5='.MD5($this->session->userdata('usuario')), "fl_participante" => true);
			
			//#### SINPRORS ####
			//$data["collection"][] = array("plano" => "SINPRORS - Participante", "url" => 'https://www.fundacaoceee.com.br/auto_atendimento_simulador.php', "fl_participante" => true);	

			#### FAMILIA ####
			$data["collection"][] = array("plano" => "Família - Novo Participante", "url" => 'https://www.familiaprevidencia.com.br', "fl_participante" => false);
			$data["collection"][] = array("plano" => "Família - Participante", "url" => 'https://www.fundacaoceee.com.br/auto_atendimento_simulador.php?USER_MD5='.MD5($this->session->userdata('usuario')), "fl_participante" => true);			
			
			$this->load->view('ecrm/simulador_beneficio/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
}
?>