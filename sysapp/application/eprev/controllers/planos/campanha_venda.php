<?php
class campanha_venda extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('expansao/campanha_venda_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GCM', 'GTI')))
		{		
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$this->campanha_venda_model->empresa($result, $args);
			$data['arr_empresa'] = $result->result_array();
		
			$this->load->view('planos/campanha_venda/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function listar()
    {		
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["dt_ini"]     = $this->input->post("dt_ini", TRUE);
			$args["dt_fim"]     = $this->input->post("dt_fim", TRUE);
			$args["cd_empresa"] = (trim($this->input->post("cd_empresa", TRUE)) != "" ? explode(",",$this->input->post("cd_empresa", TRUE)) : array());
			
			manter_filtros($args);
			
			$this->campanha_venda_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('planos/campanha_venda/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function cadastro($cd_campanha_venda = 0)
    {	
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_campanha_venda'] = intval($cd_campanha_venda);
			
			$this->campanha_venda_model->empresa($result, $args);
			$data['arr_empresa'] = $result->result_array();
			
			if(intval($cd_campanha_venda) == 0)
			{
				$data['row'] = Array(
					'cd_campanha_venda' => $args['cd_campanha_venda'], 
					'cd_empresa'        => '',
				    'ds_campanha_venda' => '', 
					'dt_inicio'         => '',  
					'dt_fim'            => '',
					'dt_cadastro'       => '',
					'dt_ingresso'       => ''
				);
			}
			else
			{
				$this->campanha_venda_model->carrega($result, $args);
				$data['row'] = $result->row_array();	
			}
			
			$this->load->view('planos/campanha_venda/cadastro',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function salvar()
    {	
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_campanha_venda"] = $this->input->post("cd_campanha_venda", TRUE);
			$args["cd_empresa"]        = $this->input->post("cd_empresa", TRUE);
			$args["ds_campanha_venda"] = $this->input->post("ds_campanha_venda", TRUE);
			$args["dt_inicio"]         = $this->input->post("dt_inicio", TRUE);
			$args["dt_final"]          = $this->input->post("dt_final", TRUE);
			$args["dt_cadastro"]       = $this->input->post("dt_cadastro", TRUE);
			$args["dt_ingresso"]       = $this->input->post("dt_ingresso", TRUE);
			$args['cd_usuario']        = $this->session->userdata('codigo');
			
			$this->campanha_venda_model->salvar($result, $args);
			
			redirect("planos/campanha_venda", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function excluir($cd_campanha_venda)
    {	
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_campanha_venda"] = $cd_campanha_venda;
			$args['cd_usuario']        = $this->session->userdata('codigo');
			
			$this->campanha_venda_model->excluir($result, $args);
			
			redirect("planos/campanha_venda", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function familia()
    {
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
					
			$args['cd_empresa'] = array(8, 10, 11, 12, 14, 19, 20, 24, 25, 26, 27, 28, 29, 30, 31);	

			$this->campanha_venda_model->comboCampanha($result, $args);
			$data['ar_campanha'] = $result->result_array();
					
			$this->campanha_venda_model->comboIdade($result, $args);
			$data['ar_idade'] = $result->result_array();	

			$this->campanha_venda_model->comboRenda($result, $args);
			$data['ar_renda'] = $result->result_array();				
					
			$this->campanha_venda_model->comboDelegacia($result, $args);
			$data['ar_delegacia'] = $result->result_array();
			
			$this->campanha_venda_model->comboCidade($result, $args);
			$data['ar_cidade'] = $result->result_array();
			
			$this->campanha_venda_model->comboTipoParticipante($result, $args);
			$data['ar_tipo_participante'] = $result->result_array();			

			$this->load->view('planos/campanha_venda/familia',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function familia_listar()
    {		
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_empresa"]          = (trim($this->input->post("cd_empresa", TRUE)) != "" ? explode(",",$this->input->post("cd_empresa", TRUE)) : array());
			$args["nome"]                = $this->input->post("nome", TRUE);
			$args["cpf"]                 = $this->input->post("cpf", TRUE);
			$args["ar_origem"]           = $this->input->post("ar_origem", TRUE);
			$args["ar_cidade"]           = $this->input->post("ar_cidade", TRUE);
			$args["ar_idade"]            = $this->input->post("ar_idade", TRUE);
			$args["ar_renda"]            = $this->input->post("ar_renda", TRUE);
			$args["ar_idade_dependente"] = $this->input->post("ar_idade_dependente", TRUE);
			$args["ar_tipo_participante"] = $this->input->post("ar_tipo_participante", TRUE);
			$args["ar_delegacia"]        = $this->input->post("ar_delegacia", TRUE);
			$args["bairro"]              = $this->input->post("bairro", TRUE);
			$args["cd_campanha_venda"]   = $this->input->post("cd_campanha_venda", TRUE);
			$args["fl_incluido"]         = $this->input->post("fl_incluido", TRUE);
			
			manter_filtros($args);
						
			$this->campanha_venda_model->carrega($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->campanha_venda_model->familia_listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('planos/campanha_venda/familia_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function salvar_item()
    {	
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_campanha_venda"] = $this->input->post("cd_campanha_venda", TRUE);
			$args["cpf"]               = $this->input->post("cpf", TRUE);
			$args["ds_origem"]         = $this->input->post("ds_origem", TRUE);
			$args["cd_origem"]         = $this->input->post("cd_origem", TRUE);
			$args['cd_usuario']        = $this->session->userdata('codigo');
			
			$cd_campanha_venda_item = $this->campanha_venda_model->salvar_item($result, $args);
			
			echo json_encode(array('value' => $cd_campanha_venda_item));
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function excluir_item()
    {	
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_campanha_venda"]      = $this->input->post("cd_campanha_venda", TRUE);
			$args["cd_campanha_venda_item"] = $this->input->post("cd_campanha_venda_item", TRUE);
			$args['cd_usuario']             = $this->session->userdata('codigo');
			
			$this->campanha_venda_model->excluir_item($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function salvar_all_item()
	{
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_campanha_venda"] = $this->input->post("cd_campanha_venda", TRUE);
			$args['cd_usuario']        = $this->session->userdata('codigo');			

			$arr_checked = (is_array($this->input->post("arr_checked")) ? $this->input->post("arr_checked") : array());
			$arr         = (is_array($this->input->post("arr")) ? $this->input->post("arr") : array());

			foreach($arr_checked as $item)
			{		
				$explode = explode(',', $item);

				$args["cpf"]               = $explode[0];
				$args["ds_origem"]         = $explode[1];
				$args["cd_origem"]         = $explode[2];

				$cd_campanha_venda_item = $this->campanha_venda_model->salvar_item($result, $args);
			}
			
			foreach($arr as $item)
			{				
				$explode = explode(',', $item);

				$args["cpf"]               = $explode[0];
				$args["ds_origem"]         = $explode[1];
				$args["cd_origem"]         = $explode[2];

				$cd_campanha_venda_item = $this->campanha_venda_model->excluir_item_origem($result, $args);
			}

			//$this->campanha_venda_model->salvar_all_item($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function fechar_campanha()
	{
		if(gerencia_in(array('GCM', 'GTI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_campanha_venda"] = $this->input->post("cd_campanha_venda", TRUE);
			$args['cd_usuario']        = $this->session->userdata('codigo');
			
			$this->campanha_venda_model->fechar_campanha($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
}
?>