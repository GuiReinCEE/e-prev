<?php
class ri_controle_evento extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		$this->load->model('crm/controle_evento_model');
		CheckLogin();
    }

    function index()
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$this->load->view('ecrm/ri_controle_evento/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
    function listar()
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args["ds_evento"]                = $this->input->post("ds_evento", TRUE);
			$args["cd_controle_evento_tipo"]  = $this->input->post("cd_controle_evento_tipo", TRUE);
			$args["cd_controle_evento_local"] = $this->input->post("cd_controle_evento_local", TRUE);
			$args["dt_ini"]                   = $this->input->post("dt_ini", TRUE);
			$args["dt_fim"]                   = $this->input->post("dt_fim", TRUE);
			
			manter_filtros($args);
			
			$this->controle_evento_model->listar($result, $args);
			$data['ar_reg'] = $result->result_array();	
			
			$this->load->view('ecrm/ri_controle_evento/index_result', $data);  
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
	function detalhe($cd_controle_evento = 0)
	{
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_controle_evento"] = intval($cd_controle_evento);
			
			if(intval($args["cd_controle_evento"]) == 0)
			{
				$data['row'] = array(
					'cd_controle_evento'       => intval($cd_controle_evento),
					'cd_controle_evento_tipo'  => 0, 
					'cd_controle_evento_local' => 0, 
					'dt_evento'                => "", 
					'ds_evento'                => "",
					'nr_convidado'             => 0,
					'nr_estimado'              => 0, 
					'nr_presente'              => 0,
					'nr_respondente'           => 0,
					'nr_satisfeito'            => 0,
					'dt_inclusao'              => "",
					'cd_usuario_inclusao'      => "",
					'dt_alteracao'             => "",
					'cd_usuario_alteracao'     => "",
					'dt_exclusao'              => "",
					'cd_usuario_exclusao'      => "",
					'obs'                      => ""
				);
			}
			else
			{
				$this->controle_evento_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('ecrm/ri_controle_evento/detalhe', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar()
	{
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_controle_evento"]       = $this->input->post("cd_controle_evento", TRUE);
			$args["dt_evento"]                = $this->input->post("dt_evento", TRUE);
			$args["ds_evento"]                = $this->input->post("ds_evento", TRUE);
			$args["cd_controle_evento_tipo"]  = $this->input->post("cd_controle_evento_tipo", TRUE);
			$args["cd_controle_evento_local"] = $this->input->post("cd_controle_evento_local", TRUE);
			$args["nr_convidado"]             = $this->input->post("nr_convidado", TRUE);
			$args["nr_estimado"]              = $this->input->post("nr_estimado", TRUE);
			$args["nr_presente"]              = $this->input->post("nr_presente", TRUE);
			$args["nr_respondente"]           = $this->input->post("nr_respondente", TRUE);
			$args["nr_satisfeito"]            = $this->input->post("nr_satisfeito", TRUE);
			$args["obs"]                      = $this->input->post("obs", TRUE);
			$args["cd_usuario"]               = $this->session->userdata('codigo');
			
			$cd_controle_evento = $this->controle_evento_model->salvar($result, $args);
			
			redirect("ecrm/ri_controle_evento/detalhe/".intval($cd_controle_evento), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}	
	
	function excluir($cd_controle_evento = 0)
	{
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_controle_evento"] = intval($cd_controle_evento);
			$args["cd_usuario"]         = $this->session->userdata('codigo');
			
			$this->controle_evento_model->excluir($result, $args);
			
			redirect("ecrm/ri_controle_evento", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
    function resumo()
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$this->load->view('ecrm/ri_controle_evento/resumo.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

    function resumoListar()
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$args["nr_ano"]                  = $this->input->post("nr_ano", TRUE);
			$args["nr_mes"]                  = $this->input->post("nr_mes", TRUE);
			$args["cd_controle_evento_tipo"] = $this->input->post("cd_controle_evento_tipo", TRUE);
			$fl_json                         = $this->input->post("fl_json");
			
			manter_filtros($args);
			
			$this->controle_evento_model->resumoListar($result, $args);
			$data['ar_reg'] = $result->result_array();	
			
			if($fl_json == "S")
			{
				echo json_encode($data['ar_reg']);
			}
			else
			{
				$this->load->view('ecrm/ri_controle_evento/resumo_result', $data);  
			}			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	

}
