<?php
class contribuicao_instituidor_mensal extends Controller
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
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$data['cd_plano'] = $cd_plano;
			$data['cd_plano_empresa'] = $cd_plano_empresa;
			$data['nr_mes'] = $nr_mes;
			$data['nr_ano'] = $nr_ano;
			
			$this->load->view('planos/contribuicao_instituidor_mensal/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}		
		
    }	
	
	function mensal()
	{
		CheckLogin();
		if(gerencia_in(array('GFC','GP')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
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

			
			$data['ar_contribuicao_mensal']['PMBDL'] = array('TOTAL' => 0, 'EMAIL' => 0);
			$data['ar_contribuicao_mensal']['PMDCC'] = array('TOTAL' => 0, 'EMAIL' => 0);
			$args['cd_contribuicao_controle_tipo'] = array("'PMBDL'","'PMDCC'");
			$args['codigo_lancamento'] = array(
												7  => array('PMBDL' => 2400, 'PMDCC' => 2410), #SENGE
												8  => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINPRO
												10 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTAE
												12 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTEP
												19 => array('PMBDL' => 2502, 'PMDCC' => 2501), #AFCEEE
												20 => array('PMBDL' => 2502, 'PMDCC' => 2501) #SINTEC
											  );			
			
			#### BUSCA LISTA GERADO ####
			$args['fl_email_enviado'] = "";
			$this->Contribuicao_instituidor_mensal_model->contribuicao_controle($result, $args);
			$data['ar_contribuicao_controle'] = $result->result_array();
			$data['fl_gerado'] = (count($data['ar_contribuicao_controle']) > 0 ? true : false);
			
			#### BUSCA LISTA GERADO E ENVIADO ####
			$args['fl_email_enviado'] = "S";
			$this->Contribuicao_instituidor_mensal_model->contribuicao_controle($result, $args);
			$data['ar_contribuicao_controle_enviado'] = $result->result_array();
			$data['fl_enviado'] = (count($data['ar_contribuicao_controle_enviado']) > 0 ? true : false);	

			#### BUSCA TOTAIS BDL, BCO ####
			$args['fl_email'] = "";
			$this->Contribuicao_instituidor_mensal_model->mensal($result, $args);
			$ar_mensal = $result->result_array();		
			foreach($ar_mensal as $ar_item )
			{
				$data['ar_contribuicao_mensal'][$ar_item['tp_pagamento']]['TOTAL'] = $ar_item['qt_total'];	
			}
			
			#### BUSCA TOTAIS BDL, BCO COM EMAIL ####
			$args['fl_email'] = "S";
			$this->Contribuicao_instituidor_mensal_model->mensal($result, $args);
			$ar_mensal_email = $result->result_array();		
			foreach($ar_mensal_email as $ar_item )
			{
				$data['ar_contribuicao_mensal'][$ar_item['tp_pagamento']]['EMAIL'] = $ar_item['qt_total'];	
			}			

			#### BUSCA TOTAIS BDL, BCO COMPETENCIA ANTERIOR ####
			$this->Contribuicao_instituidor_mensal_model->mensal_anterior($result, $args);
			$ar_mensal_anterior = $result->result_array();		
			$data['ar_contribuicao_mensal_anterior']['PMBDL'] = array('TOTAL' => 0);
			$data['ar_contribuicao_mensal_anterior']['PMDCC'] = array('TOTAL' => 0);
			foreach($ar_mensal_anterior as $ar_item )
			{
				$data['ar_contribuicao_mensal_anterior'][$ar_item['tp_pagamento']]['TOTAL'] = $ar_item['qt_total'];	
			}
			
			#### BUSCA TOTAIS BDL, BCO CADASTRO ####
			$this->Contribuicao_instituidor_mensal_model->mensal_cadastro($result, $args);
			$ar_mensal_cadastro = $result->result_array();		
			$data['ar_contribuicao_mensal_cadastro']['BDL']['NORMAL']    = 0;
			$data['ar_contribuicao_mensal_cadastro']['BDL']['INSTITUTO'] = 0;
			$data['ar_contribuicao_mensal_cadastro']['BDL']['TOTAL']     = 0; 
			$data['ar_contribuicao_mensal_cadastro']['BCO']['NORMAL']    = 0;
			$data['ar_contribuicao_mensal_cadastro']['BCO']['INSTITUTO'] = 0;
			$data['ar_contribuicao_mensal_cadastro']['BCO']['TOTAL']     = 0;
			foreach($ar_mensal_cadastro as $ar_item )
			{
				$data['ar_contribuicao_mensal_cadastro'][$ar_item['tp_pagamento']]['NORMAL']    = (intval($ar_item['qt_total']) - intval($ar_item['qt_instituto']));
				$data['ar_contribuicao_mensal_cadastro'][$ar_item['tp_pagamento']]['INSTITUTO'] = intval($ar_item['qt_instituto']);
				$data['ar_contribuicao_mensal_cadastro'][$ar_item['tp_pagamento']]['TOTAL']     = intval($ar_item['qt_total']);
				
				#$data['ar_contribuicao_mensal_cadastro'][$ar_item['tp_pagamento']]['TOTAL'] = $ar_item['qt_total'];	
			}			
			
			$this->load->view('planos/contribuicao_instituidor_mensal/index_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}		
	}
	
	function sem_email()
	{
		CheckLogin();
		if(gerencia_in(array('GFC','GP')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
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
			
			$args['codigo_lancamento'] = array(
												7  => array('PMBDL' => 2400, 'PMDCC' => 2410), #SENGE
												8  => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINPRO
												10 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTAE
												12 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTEP
												19 => array('PMBDL' => 2502, 'PMDCC' => 2501), #AFCEEE
												20 => array('PMBDL' => 2502, 'PMDCC' => 2501) #SITEC
											  );	
											  
			$this->Contribuicao_instituidor_mensal_model->sem_email($result, $args);
			$data['ar_lista'] = $result->result_array();
			
			$this->load->view('planos/contribuicao_instituidor_mensal/index_sem_email_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}		
	}	
	
	function enviarEmailCadastro()
	{
		CheckLogin();
		if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
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
			
			$args['codigo_lancamento'] = array(
												7  => array('PMBDL' => 2400, 'PMDCC' => 2410), #SENGE
												8  => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINPRO
												10 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTAE
												12 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTEP
												19 => array('PMBDL' => 2502, 'PMDCC' => 2501), #AFCEEE
												20 => array('PMBDL' => 2502, 'PMDCC' => 2501) #SINTEC
											  );	
											  
			$this->Contribuicao_instituidor_mensal_model->sem_email($result, $args);
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
				echo $this->Contribuicao_instituidor_mensal_model->enviarEmailCadastro($result, $args);
			}
			
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}		
	}	
	
	function gerar()
	{
		CheckLogin();
		if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
			$result = null;
			$data = Array();
			$args = Array();
			
			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
			$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
			$args["cd_plano"]   = $this->input->post("cd_plano", TRUE);
			$args["nr_mes"]     = $this->input->post("nr_mes", TRUE);
			$args["nr_ano"]     = $this->input->post("nr_ano", TRUE);
			
			manter_filtros($args);	
			
			$args['codigo_lancamento'] = array(
												7  => array('PMBDL' => 2400, 'PMDCC' => 2410), #SENGE
												8  => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINPRO
												10 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTAE
												12 => array('PMBDL' => 2450, 'PMDCC' => 2460), #SINTEP
												19 => array('PMBDL' => 2502, 'PMDCC' => 2501), #AFCEEE
												20 => array('PMBDL' => 2502, 'PMDCC' => 2501) #SITEC
											  );	
			$args["cd_usuario"] = $this->session->userdata('codigo');	

			echo $this->Contribuicao_instituidor_mensal_model->gerar($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}		
	}

	function enviarEmail()
	{
		CheckLogin();
		if(gerencia_in(array('GFC')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
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

			echo $this->Contribuicao_instituidor_mensal_model->enviarEmail($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}		
	}	

    function relatorio($cd_plano = "", $cd_plano_empresa = "", $nr_mes = "", $nr_ano = "", $fl_retornou = "")
    {
		CheckLogin();
		if(gerencia_in(array('GFC', 'GP', 'GE')))
		{
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
			$result = null;
			$args = Array();	
			$data = Array();			
			
			$data['cd_plano'] = $cd_plano;
			$data['cd_plano_empresa'] = $cd_plano_empresa;
			$data['nr_mes'] = $nr_mes;
			$data['nr_ano'] = $nr_ano;			
			$data['fl_retornou'] = $fl_retornou;			
			
			$this->load->view('planos/contribuicao_instituidor_mensal/relatorio.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}		
		
    }
	
    function relatorioListar()
    {
        CheckLogin();
		if(gerencia_in(array('GFC', 'GP', 'GE')))
		{		
			$this->load->model('projetos/Contribuicao_instituidor_mensal_model');
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
			
			$this->Contribuicao_instituidor_mensal_model->relatorioListar($result, $args);
			$data['collection'] = $result->result_array();
			$this->load->view('planos/contribuicao_instituidor_mensal/relatorio_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}			
    }	
}
