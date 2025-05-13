<?php
class contracheque extends Controller
{
	function __construct()
	{
		parent::Controller();
		CheckLogin();
		$this->load->model('projetos/contracheque_model');
	}

	function index($cd_sessao = "")
	{
		$args = Array();
		$data = Array();
		$result = null;		

		if(session_id() == $cd_sessao)
		{
			$this->load->view('servico/contracheque/index');
		}
		else
		{
			$data["validar_login_ir_para"] = "servico/contracheque/index";
			$this->load->view('home/validar_login',$data);
		}
	}

	function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;			
		
		$args['cc_registro_empregado'] = (intval($this->input->post("cc_registro_empregado", TRUE)) > 0 ? intval($this->input->post("cc_registro_empregado", TRUE)) : intval($this->session->userdata('cd_registro_empregado')));
				
		$this->contracheque_model->cbCompetencia($result, $args);
		$data["ar_competencia"] = $result->result_array();		

		

		$args['cc_dt_pagamento'] = "";

		if(trim($this->input->post("cc_dt_pagamento", TRUE)) == "" AND intval($args['cc_registro_empregado']) > 0)
		{
			$args['cc_dt_pagamento'] = $data["ar_competencia"][0]["value"];
		}
		else
		{
			$args['cc_dt_pagamento'] = trim($this->input->post("cc_dt_pagamento", TRUE));
		}

		$data['cc_dt_pagamento']       = $args['cc_dt_pagamento'];
		$data['cc_registro_empregado'] = $args['cc_registro_empregado'];
		
		if(trim($args['cc_dt_pagamento']) != "")
		{
			$this->contracheque_model->listar($result, $args);
			$data["ar_contracheque"] = $result->result_array();
			
			$this->contracheque_model->listarBeneficios($result, $args);
			$data["ar_beneficio"] = $result->result_array();
		}
		else
		{
			$data["ar_contracheque"] = array();
			$data["ar_beneficio"] = array();
		}
		
		
		$this->load->view('servico/contracheque/index_result', $data);	
    }
}
?>