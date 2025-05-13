<?php
class pagamento_impressao extends Controller
{
    var $ar_status = Array();
	
	function __construct()
    {
        parent::Controller();
		
        $this->ar_status[] = array('value' => 'N', 'text' => 'OK');
        $this->ar_status[] = array('value' => 'S', 'text' => 'ERRO');		
    }

    function index($cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "")
    {
		CheckLogin();
	
		$data['ar_status'] = $this->ar_status;
	
		if(gerencia_in(array('GTI', 'GRSC', 'GFC')))
		{
			$data['cd_empresa']            = $cd_empresa;
			$data['cd_registro_empregado'] = $cd_registro_empregado;
			$data['seq_dependencia']       = $seq_dependencia;			
			$this->load->view('ecrm/pagamento_impressao/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }


    function listar()
    {
        CheckLogin();
		
		if(gerencia_in(array('GTI', 'GRSC', 'GFC')))
		{		
			$this->load->model('projetos/Pagamento_impressao_model');

			$result = null;
			$data = Array();
			$args = Array();
			
			$args["cd_auto_atendimento_pagamento_impressao"] = $this->input->post("cd_auto_atendimento_pagamento_impressao", TRUE);
			
			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["dt_impressao_ini"]      = $this->input->post("dt_impressao_ini", TRUE);
			$args["dt_impressao_fim"]      = $this->input->post("dt_impressao_fim", TRUE);
			$args["dt_vencimento_ini"]     = $this->input->post("dt_vencimento_ini", TRUE);
			$args["dt_vencimento_fim"]     = $this->input->post("dt_vencimento_fim", TRUE);
			$args["cpf"]                   = $this->input->post("cpf", TRUE);
			$args["cd_plano"]              = $this->input->post("cd_plano", TRUE);
			$args["cd_plano_empresa"]      = $this->input->post("cd_plano_empresa", TRUE);
			$args["fl_erro_registro"]      = $this->input->post("fl_erro_registro", TRUE);

			manter_filtros($args);
			
			$this->Pagamento_impressao_model->listar($result, $args);
			$data['collection'] = $result->result_array();		
			
			$this->load->view('ecrm/pagamento_impressao/index_result', $data);
		
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
    function cadastro($cd_auto_atendimento_pagamento_impressao = 0)
    {
        CheckLogin();
		
		$data['ar_status'] = $this->ar_status;
		
		if(gerencia_in(array('GTI', 'GRSC', 'GFC')))
		{		
			$this->load->model('projetos/Pagamento_impressao_model');

			$result = null;
			$data = Array();
			$args = Array();

			$args["cd_auto_atendimento_pagamento_impressao"] = intval($cd_auto_atendimento_pagamento_impressao);
			$args["cd_empresa"]            = "";
			$args["cd_registro_empregado"] = "";
			$args["seq_dependencia"]       = "";
			$args["dt_impressao_ini"]      = "";
			$args["dt_impressao_fim"]      = "";
			$args["dt_vencimento_ini"]     = "";
			$args["dt_vencimento_fim"]     = "";
			$args["cpf"]                   = "";
			$args["cd_plano"]              = "";
			$args["cd_plano_empresa"]      = "";
			$args["fl_erro_registro"]      = "";

			$this->Pagamento_impressao_model->listar($result, $args);
			$data['row'] = $result->row_array();		
			
			$this->load->view('ecrm/pagamento_impressao/cadastro', $data);
		
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }	
}
