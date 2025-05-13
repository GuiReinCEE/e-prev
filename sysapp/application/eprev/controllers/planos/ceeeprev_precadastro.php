<?php
class ceeeprev_precadastro extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/ceeeprev_precadastro_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GRI')))
		{		
			$this->load->view('planos/ceeeprev_precadastro/index');
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
			$args   = Array();
			$data   = Array();
			
			$args['dt_inclusao_ini'] = $this->input->post('dt_inclusao_ini',TRUE);
			$args['dt_inclusao_fim'] = $this->input->post('dt_inclusao_fim',TRUE);
			$args['fl_status']       = $this->input->post('fl_status',TRUE);
			
			manter_filtros($args);
			
			$this->ceeeprev_precadastro_model->listar($result, $args);
			$collection = $result->result_array();
			
			$i = 0;
			
			$data['collection'] = array();
			
			foreach($collection as $item)
			{
				$args['cd_ceeeprev_precadastro'] = $item['cd_ceeeprev_precadastro'];
				
				$this->ceeeprev_precadastro_model->acompanhamento($result, $args);
				$arr = $result->result_array();
			
				$data['collection'][$i] = $item;
				$data['collection'][$i]['arr_acompanhamento'] = $arr;
				
				$i++;
			}
			
			$this->load->view('planos/ceeeprev_precadastro/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function acompanhamento($cd_ceeeprev_precadastro)
    {	
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_ceeeprev_precadastro'] = intval($cd_ceeeprev_precadastro);
			
			$this->ceeeprev_precadastro_model->carrega($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->ceeeprev_precadastro_model->acompanhamento($result, $args);
			$data['collection'] = $result->result_array();
				
			$this->load->view('planos/ceeeprev_precadastro/acompanhamento',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function salvar_acompanhamento()
	{
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_ceeeprev_precadastro"] = $this->input->post("cd_ceeeprev_precadastro", TRUE);
			$args["descricao"]               = $this->input->post("descricao", TRUE);
			$args["cd_usuario"]              = $this->session->userdata('codigo');
			
			$this->ceeeprev_precadastro_model->salvar_acompanhamento($result, $args);
			
			redirect("planos/ceeeprev_precadastro", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	
	function excluir_acompanhamento($cd_ceeeprev_precadastro, $cd_ceeeprev_precadastro_acompanhamento)
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_ceeeprev_precadastro_acompanhamento"] = intval($cd_ceeeprev_precadastro_acompanhamento);
			$args['cd_usuario']                             = $this->session->userdata('codigo');
			
			$this->ceeeprev_precadastro_model->excluir_acompanhamento($result, $args);
			
			redirect("planos/ceeeprev_precadastro/acompanhamento/".$cd_ceeeprev_precadastro, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
?>