<?php
class conteudo_site extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($cd_site=0,$cd_versao=0)
    {
		CheckLogin();
		$data = Array();
		$args = Array();
		$result = null;			

		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->model('projetos/Conteudo_site_model');
			$args['cd_site']   = intval($cd_site);
			$args['cd_versao'] = intval($cd_versao);				
			$data['cd_site']   = intval($cd_site);
			$data['cd_versao'] = intval($cd_versao);	
			
			$this->Conteudo_site_model->secaoCombo( $result, $args );
			$data['ar_secao'] = $result->result_array();			
			
			$this->load->view('ecrm/conteudo_site/index.php', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	

    function listar()
    {
        CheckLogin();
		
		if(gerencia_in(array('GI', 'GRI'))) 
		{		
			 $this->load->model('projetos/Conteudo_site_model');

			$args   = Array();
			$data   = Array();
			$result = null;

			$args["cd_versao"]   = intval($this->input->post("cd_versao", TRUE));
			$args["cd_site"]     = intval($this->input->post("cd_site", TRUE));
			$args["cd_secao"]    = $this->input->post("cd_secao", TRUE);
			$args["fl_excluido"] = $this->input->post("fl_excluido", TRUE);

			manter_filtros($args);

			$this->Conteudo_site_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			$this->load->view('ecrm/conteudo_site/partial_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }			
    }
	
	function detalhe($cd_site = 0, $cd_versao = 0, $cd_materia = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->model('projetos/Conteudo_site_model');
			$data = Array();
			$args = Array();
			$result = null;	

			$args['cd_site']   = intval($cd_site);
			$args['cd_versao'] = intval($cd_versao);
			$args['cd_materia'] = intval($cd_materia);			

			$data['cd_site']   = intval($cd_site);
			$data['cd_versao'] = intval($cd_versao);
			$data['cd_materia'] = intval($cd_materia);
			
			$this->Conteudo_site_model->secaoCombo( $result, $args );
			$data['ar_secao'] = $result->result_array();			
			
			if(intval($cd_materia) == 0)
			{
				$data['row'] = Array(
										'dt_inclusao'=>'',
										'dt_exclusao'=>'',
										'dt_alteracao'=>'',
										'cd_site'=>0,
										'cd_versao'=>0,
										'cd_materia'=>0,
										'ds_titulo'=>'',
										'ds_item_menu'=>'',
										'cd_secao'=>'',
										'nr_ordem'=>'',
										'fl_excluido'=>'',
										'conteudo_pagina'=>''				
									);
			}
			else
			{
				$this->Conteudo_site_model->Pagina($result, $args);
				$data['row'] = $result->row_array();	
			}
			
			$this->load->view('ecrm/conteudo_site/detalhe.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function salvar()
    {
		CheckLogin();
	
		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->model('projetos/Conteudo_site_model');

			$data = Array();
			$args = Array();
			$result = null;
			
			$args["cd_site"]         = $this->input->post("cd_site", TRUE);
			$args["cd_versao"]       = $this->input->post("cd_versao", TRUE);
			$args["cd_materia"]      = $this->input->post("cd_materia", TRUE);
			$args["ds_titulo"]       = $this->input->post("ds_titulo", TRUE);
			$args["ds_item_menu"]    = $this->input->post("ds_item_menu", TRUE);
			$args["cd_secao"]        = $this->input->post("cd_secao", TRUE);
			$args["nr_ordem"]        = $this->input->post("nr_ordem", TRUE);
			$args["fl_excluido"]     = $this->input->post("fl_excluido", TRUE);
			$args["conteudo_pagina"] = $this->input->post("conteudo_pagina", FALSE); #### conteudo html tem que ser FALSE ####
			
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$ar_conteudo = $this->Conteudo_site_model->salvar( $result, $args );
			redirect("ecrm/conteudo_site/detalhe/".implode("/",$ar_conteudo), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function historico($cd_site = 0, $cd_versao = 0, $cd_materia = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->model('projetos/Conteudo_site_model');
			$data = Array();
			$args = Array();
			$result = null;	

			$data['cd_site']   = intval($cd_site);
			$data['cd_versao'] = intval($cd_versao);
			$data['cd_materia'] = intval($cd_materia);
			
			$this->load->view('ecrm/conteudo_site/historico.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function historicoListar()
    {
        CheckLogin();
		
		if(gerencia_in(array('GI', 'GRI'))) 
		{		
			$this->load->model('projetos/Conteudo_site_model');

			$args   = Array();
			$data   = Array();
			$result = null;

			$args["cd_site"]    = $this->input->post("cd_site", TRUE);
			$args["cd_versao"]  = $this->input->post("cd_versao", TRUE);			
			$args["cd_materia"] = $this->input->post("cd_materia", TRUE);			
			$args["dt_ini"]     = $this->input->post("dt_ini", TRUE);			
			$args["dt_fim"]     = $this->input->post("dt_fim", TRUE);			
			
			manter_filtros($args);

			$this->Conteudo_site_model->historicoListar($result, $args);
			$data['collection'] = $result->result_array();


			$this->load->view('ecrm/conteudo_site/historico_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
}
?>