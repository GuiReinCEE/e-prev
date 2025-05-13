<?php
class contribuicao_instituidor_primeiro extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index($cd_plano = "", $cd_plano_empresa = "", $nr_mes = "", $nr_ano = "")
    {
		CheckLogin();
		if(gerencia_in(array('GFC','GP')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$data['cd_plano'] = $cd_plano;
			$data['cd_plano_empresa'] = $cd_plano_empresa;
			$data['nr_mes'] = $nr_mes;
			$data['nr_ano'] = $nr_ano;
			
			$this->load->view('planos/contribuicao_instituidor_primeiro/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
		
    }	
	
	function primeiro_pagamento()
	{
		CheckLogin();
		if(gerencia_in(array('GFC','GP')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$data = Array();
			$args = Array();
			
			$data["CD_EMPRESA"] = $this->input->post("cd_empresa", TRUE);
			$data["CD_PLANO"]   = $this->input->post("cd_plano", TRUE);
			$data["NR_MES"]     = $this->input->post("nr_mes", TRUE);
			$data["NR_ANO"]     = $this->input->post("nr_ano", TRUE);			
			
			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
			$args["cd_plano"]   = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]     = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]     = $this->input->post("nr_ano", TRUE);
			
			manter_filtros($args);			
			
			$args['cd_contribuicao_controle_tipo'] = "'1PBDL','1PDCC'";
			$args['fl_email_enviado'] = "";
			$this->Contribuicao_instituidor_primeiro_model->contribuicao_controle($result, $args);
			$data['ar_contribuicao_controle'] = $result->result_array();
			
			$data['fl_gerado'] = (count($data['ar_contribuicao_controle']) > 0 ? true : false);
			
			
			$this->Contribuicao_instituidor_primeiro_model->forma_pagamento($result, $args);
			$data['ar_forma_pagamento'] = $result->result_array();				

			$this->Contribuicao_instituidor_primeiro_model->cadastro($result, $args);
			$data['ar_cadastro'] = $result->row_array();		

			$this->Contribuicao_instituidor_primeiro_model->geracao($result, $args);
			$data['ar_geracao'] = $result->row_array();	

			$data['ar_financeiro'] = Array();
			$data['ar_financeiro_email'] = Array();
			
			$this->Contribuicao_instituidor_primeiro_model->financeiro_envio($result, $args);
			$data['ar_financeiro_envio'] = $result->row_array();	
			
			if(count($data['ar_financeiro_envio']) > 0)
			{
				$data['fl_enviado'] = true;
				
				foreach($data['ar_forma_pagamento'] as $ar_item )
				{
					$data['ar_financeiro'][$ar_item['forma_pagamento']]       = Array('qt_total'=>0,'vl_total'=>0);	
					$data['ar_financeiro_email'][$ar_item['forma_pagamento']] = Array('qt_total'=>0,'vl_total'=>0);	
				}			
			
				$data['ar_financeiro']['usuario_envio'] = $data['ar_financeiro_envio']['usuario_envio'];
				
				$data['ar_financeiro']['BDL'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_bdl_enviado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_bdl_enviado']);
				$data['ar_financeiro_email']['BDL'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_bdl_enviado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_bdl_enviado']);

				$data['ar_financeiro']['BCO'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_debito_cc_enviado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_debito_cc_enviado']);
				$data['ar_financeiro_email']['BCO'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_debito_cc_enviado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_debito_cc_enviado']);
				
				$data['ar_financeiro']['FOL'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_folha_gerado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_folha_gerado']);
				$data['ar_financeiro_email']['FOL'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_folha_gerado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_folha_gerado']);
				
				$data['ar_financeiro']['CHQ'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_cheque_gerado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_cheque_gerado']);
				$data['ar_financeiro_email']['CHQ'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_cheque_gerado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_cheque_gerado']);				

				$data['ar_financeiro']['DEP'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_deposito_gerado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_deposito_gerado']);
				$data['ar_financeiro_email']['DEP'] = Array('qt_total'=>$data['ar_financeiro_envio']['tot_deposito_gerado'],'vl_total'=>$data['ar_financeiro_envio']['vlr_deposito_gerado']);								
			}
			else
			{
				$data['fl_enviado'] = false;
				$data['ar_financeiro']['usuario_envio'] = "";
				foreach($data['ar_forma_pagamento'] as $ar_item )
				{
					$args['forma_pagamento'] = $ar_item['forma_pagamento'];
				
					$args['fl_email'] = "";
					$this->Contribuicao_instituidor_primeiro_model->financeiro($result, $args);
					$data['ar_financeiro'][$ar_item['forma_pagamento']] = $result->row_array();	

					$args['fl_email'] = "S";
					$this->Contribuicao_instituidor_primeiro_model->financeiro($result, $args);
					$data['ar_financeiro_email'][$ar_item['forma_pagamento']] = $result->row_array();					
				}			
			}
			
			$this->load->view('planos/contribuicao_instituidor_primeiro/index_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
	function sem_email()
	{
		CheckLogin();
		if(gerencia_in(array('GFC','GP')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$data = Array();
			$args = Array();
			
			$data["CD_EMPRESA"] = $this->input->post("cd_empresa", TRUE);
			$data["CD_PLANO"]   = $this->input->post("cd_plano", TRUE);
			$data["NR_MES"]     = $this->input->post("nr_mes", TRUE);
			$data["NR_ANO"]     = $this->input->post("nr_ano", TRUE);			
			
			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
			$args["cd_plano"]   = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]     = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]     = $this->input->post("nr_ano", TRUE);
			
			manter_filtros($args);			
			
			$args['forma_pagamento'] = "";
			$this->Contribuicao_instituidor_primeiro_model->sem_email($result, $args);
			$data['ar_lista'] = $result->result_array();
			
			$this->load->view('planos/contribuicao_instituidor_primeiro/index_sem_email_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
	function enviarEmailCadastro()
	{
		CheckLogin();
		if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$data = Array();
			$args = Array();
			
			$data["CD_EMPRESA"] = $this->input->post("cd_empresa", TRUE);
			$data["CD_PLANO"]   = $this->input->post("cd_plano", TRUE);
			$data["NR_MES"]     = $this->input->post("nr_mes", TRUE);
			$data["NR_ANO"]     = $this->input->post("nr_ano", TRUE);			
			
			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
			$args["cd_plano"]   = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]     = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]     = $this->input->post("nr_ano", TRUE);
			
			manter_filtros($args);			
			
			$args["cd_usuario"] = $this->session->userdata('codigo');	
			$args['forma_pagamento'] = "";
			$this->Contribuicao_instituidor_primeiro_model->sem_email($result, $args);
			$ar_lista = $result->result_array();
			$lista = "";
			foreach($ar_lista as $ar_item)
			{
				$part = "[".$ar_item['forma_pagamento']."] ".$ar_item['cd_empresa']."/".$ar_item['cd_registro_empregado']."/".$ar_item['seq_dependencia']." - ".$ar_item['nome'];
				$lista = ($lista == "" ? $part.chr(10) : $lista.$part.chr(10));
			}

			if(trim($lista) != "")
			{
				$result = null;
				$data = Array();
				$args = Array();
				$args['lista'] = $lista;
				echo $this->Contribuicao_instituidor_primeiro_model->enviarEmailCadastro($result, $args);
			}
			
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
	function gerar()
	{
		CheckLogin();
		if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$data = Array();
			$args = Array();
			
			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
			$args["cd_plano"]   = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]     = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]     = $this->input->post("nr_ano", TRUE);
			
			manter_filtros($args);	

			$args["cd_usuario"] = $this->session->userdata('codigo');			

			echo $this->Contribuicao_instituidor_primeiro_model->gerar($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function enviarEmail()
	{
		CheckLogin();
		if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$data = Array();
			$args = Array();
			
			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"]       = $this->input->post("cd_empresa", TRUE);
			$args["cd_plano"]         = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]           = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]           = $this->input->post("nr_ano", TRUE);
			$args["cd_usuario"]       = $this->session->userdata('codigo');
			
			manter_filtros($args);	

			$args["cd_usuario"] = $this->session->userdata('codigo');			

			echo $this->Contribuicao_instituidor_primeiro_model->enviarEmail($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
		
    function relatorio($cd_plano = "", $cd_plano_empresa = "", $nr_mes = "", $nr_ano = "", $fl_retornou = "")
    {
		CheckLogin();
		if(gerencia_in(array('GFC','GP')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$data['cd_plano'] = $cd_plano;
			$data['cd_plano_empresa'] = $cd_plano_empresa;
			$data['nr_mes'] = $nr_mes;
			$data['nr_ano'] = $nr_ano;			
			$data['fl_retornou'] = $fl_retornou;			
			
			$this->load->view('planos/contribuicao_instituidor_primeiro/relatorio.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
		
    }
	
    function relatorioListar()
    {
        CheckLogin();
		if(gerencia_in(array('GFC','GP')))
		{		
			$this->load->model('projetos/Contribuicao_instituidor_primeiro_model');
			$result = null;
			$args = Array();	
			$data = Array();	

			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
			$args["cd_plano"]    = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]      = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]      = $this->input->post("nr_ano", TRUE);
			$args["fl_retornou"] = $this->input->post("fl_retornou", TRUE);
			
			manter_filtros($args);
			
			$this->Contribuicao_instituidor_primeiro_model->relatorioListar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('planos/contribuicao_instituidor_primeiro/relatorio_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
    }	
}
