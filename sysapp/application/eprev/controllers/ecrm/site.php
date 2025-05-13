<?php
class site extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		$data = Array();
		$args = Array();
		$result = null;			

		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->view('ecrm/site/index.php');
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
			$this->load->model('projetos/Root_site_model');

			$args   = Array();
			$data   = Array();
			$result = null;

			manter_filtros($args);

			$this->Root_site_model->listar( $result, $args );
			$data['collection'] = $result->result_array();


			$this->load->view('ecrm/site/partial_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function detalhe($cd_site = 0, $cd_versao = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->model('projetos/Root_site_model');
			$data = Array();
			$args = Array();
			$result = null;	
			$data['cd_site']   = intval($cd_site);
			$data['cd_versao'] = intval($cd_versao);
			
			if(intval($cd_site) == 0)
			{
				$data['row'] = Array('cd_site'=>0,
				                     'cd_versao'=>0,
									 'endereco'=>'',
									 'tit_capa'=>'',
									 'texto_capa'=>'');
				
			}
			else
			{
				$args['cd_site']   = intval($cd_site);
				$args['cd_versao'] = intval($cd_versao);
				$this->Root_site_model->Site($result, $args);
				$data['row'] = $result->row_array();	
			}
			
			$this->load->view('ecrm/site/detalhe.php',$data);
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
			$this->load->model('projetos/Root_site_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args["cd_site"]    = $this->input->post("cd_site", TRUE);
			$args["cd_versao"]  = $this->input->post("cd_versao", TRUE);
			$args["tit_capa"]   = $this->input->post("tit_capa", TRUE);
			$args["texto_capa"] = $this->input->post("texto_capa", FALSE); #### conteudo html tem que ser FALSE ####
			$args["endereco"]   = $this->input->post("endereco", TRUE);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$ar_site = $this->Root_site_model->salvar( $result, $args );
			redirect("ecrm/site/detalhe/".implode("/",$ar_site), "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function excluir($cd_site = 0, $cd_versao = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->model('projetos/Root_site_model');

			$data = Array();
			$args = Array();
			$result = null;

			$args['cd_site']    = intval($cd_site);
			$args['cd_versao']  = intval($cd_versao);
			$args["cd_usuario"] = $this->session->userdata('codigo');
			
			$this->Root_site_model->excluir( $result, $args );
			redirect("ecrm/site", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	
	
	function historico($cd_site = 0, $cd_versao = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GI', 'GRI'))) 
		{
			$this->load->model('projetos/Root_site_model');
			$data = Array();
			$args = Array();
			$result = null;	
			$data['cd_site']   = intval($cd_site);
			$data['cd_versao'] = intval($cd_versao);
			
			$this->load->view('ecrm/site/historico.php',$data);
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
			$this->load->model('projetos/Root_site_model');

			$args   = Array();
			$data   = Array();
			$result = null;

			$args["cd_site"]   = $this->input->post("cd_site", TRUE);
			$args["cd_versao"] = $this->input->post("cd_versao", TRUE);			
			$args["dt_ini"]    = $this->input->post("dt_ini", TRUE);			
			$args["dt_fim"]    = $this->input->post("dt_fim", TRUE);			
			
			manter_filtros($args);

			$this->Root_site_model->historicoListar($result, $args);
			$data['collection'] = $result->result_array();


			$this->load->view('ecrm/site/historico_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	
}
?>