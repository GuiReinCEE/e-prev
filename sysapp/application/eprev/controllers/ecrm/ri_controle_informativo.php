<?php
class ri_controle_informativo extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		$this->load->model('crm/controle_informativo_model');
		CheckLogin();
    }

    function index()
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$data   = array();
			$args   = array();
			
			$this->load->view('ecrm/ri_controle_informativo/index.php', $data);
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
			
			$args["ds_informativo"]                = $this->input->post("ds_informativo", TRUE);
			$args["cd_controle_informativo_tipo"]  = $this->input->post("cd_controle_informativo_tipo", TRUE);
			$args["dt_ini"]                        = $this->input->post("dt_ini", TRUE);
			$args["dt_fim"]                        = $this->input->post("dt_fim", TRUE);
			$fl_json                               = $this->input->post("fl_json");
			
			manter_filtros($args);
			
			$this->controle_informativo_model->listar($result, $args);
			$data['ar_reg'] = $result->result_array();	
			
			if($fl_json == "S")
			{
				$ar_reg_json = Array();
				foreach ($data['ar_reg'] as $item)
				{
					$ar_reg_json[] = array_map("arrayToUTF8", $item);		
				}
				echo json_encode($ar_reg_json);
			}
			else
			{
				$this->load->view('ecrm/ri_controle_informativo/index_result', $data);  
			}				
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
	
	function detalhe($cd_controle_informativo = 0)
	{
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_controle_informativo"] = intval($cd_controle_informativo);
			
			if(intval($args["cd_controle_informativo"]) == 0)
			{
				$data['row'] = array(
					'cd_controle_informativo'      => intval($cd_controle_informativo),
					'cd_controle_informativo_tipo' => 0, 
					'ds_informativo'       => "",
					'dt_envio_limite'      => "",
					'fl_envio'             => "",
					'dt_envio'             => "",
					'nr_exemplar'          => 0,
					'nr_publico'           => 0, 
					'nr_retrabalho'        => 0,
					'nr_reclamacao'        => 0,
					'observacao'           => "",
					'dt_inclusao'          => "",
					'cd_usuario_inclusao'  => "",
					'dt_alteracao'         => "",
					'cd_usuario_alteracao' => "",
					'dt_exclusao'          => "",
					'cd_usuario_exclusao'  => "",
				);
			}
			else
			{
				$this->controle_informativo_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->load->view('ecrm/ri_controle_informativo/detalhe', $data);
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
			
			$args["cd_controle_informativo"]      = $this->input->post("cd_controle_informativo", TRUE);
			$args["ds_informativo"]               = $this->input->post("ds_informativo", TRUE);
			$args["cd_controle_informativo_tipo"] = $this->input->post("cd_controle_informativo_tipo", TRUE);
			$args["dt_envio_limite"]              = $this->input->post("dt_envio_limite", TRUE);
			$args["dt_envio"]                     = $this->input->post("dt_envio", TRUE);
			$args["fl_envio"]                     = $this->input->post("fl_envio", TRUE);
			$args["nr_exemplar"]                  = $this->input->post("nr_exemplar", TRUE);
			$args["nr_publico"]                   = $this->input->post("nr_publico", TRUE);
			$args["nr_retrabalho"]                = $this->input->post("nr_retrabalho", TRUE);
			$args["nr_reclamacao"]                = $this->input->post("nr_reclamacao", TRUE);
			$args["observacao"]                   = $this->input->post("observacao", TRUE);
			$args["cd_usuario"]                   = $this->session->userdata('codigo');
			
			$cd_controle_informativo = $this->controle_informativo_model->salvar($result, $args);
			
			redirect("ecrm/ri_controle_informativo/detalhe/".intval($cd_controle_informativo), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function excluir($cd_controle_informativo = 0)
	{
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_controle_informativo"] = intval($cd_controle_informativo);
			$args["cd_usuario"]              = $this->session->userdata('codigo');
			
			$this->controle_informativo_model->excluir($result, $args);
			
			redirect("ecrm/ri_controle_informativo", "refresh");
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
			
			$this->load->view('ecrm/ri_controle_informativo/resumo.php', $data);
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
			
			$args["nr_ano"]                       = $this->input->post("nr_ano", TRUE);
			$args["nr_mes"]                       = $this->input->post("nr_mes", TRUE);
			$args["cd_controle_informativo_tipo"] = $this->input->post("cd_controle_informativo_tipo", TRUE);
			$fl_json                              = $this->input->post("fl_json");
			
			manter_filtros($args);
			
			$this->controle_informativo_model->resumoListar($result, $args);
			$data['ar_reg'] = $result->result_array();	
			
			if($fl_json == "S")
			{
				echo json_encode($data['ar_reg']);
			}
			else
			{
				$this->load->view('ecrm/ri_controle_informativo/resumo_result', $data);
			}				
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
}
