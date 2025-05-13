<?php
class Plano_unico_cgtee_interesse extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/plano_unico_cgtee_interesse_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GRI')))
		{		
			$this->load->view('planos/plano_unico_cgtee_interesse/index');
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
			
			$this->plano_unico_cgtee_interesse_model->listar($result, $args);
			$collection = $result->result_array();
			
			$i = 0;
			
			$data['collection'] = array();
			
			foreach($collection as $item)
			{
				$args['cd_plano_unico_cgtee_interesse'] = $item['cd_plano_unico_cgtee_interesse'];
				
				$this->plano_unico_cgtee_interesse_model->acompanhamento($result, $args);
				$arr = $result->result_array();
			
				$data['collection'][$i] = $item;
				$data['collection'][$i]['arr_acompanhamento'] = $arr;
				
				$i++;
			}
			
			$this->load->view('planos/plano_unico_cgtee_interesse/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function acompanhamento($cd_plano_unico_cgtee_interesse)
    {	
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_plano_unico_cgtee_interesse'] = intval($cd_plano_unico_cgtee_interesse);
			
			$this->plano_unico_cgtee_interesse_model->carrega($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->plano_unico_cgtee_interesse_model->acompanhamento($result, $args);
			$data['collection'] = $result->result_array();
				
			$this->load->view('planos/plano_unico_cgtee_interesse/acompanhamento',$data);
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

			$args["cd_plano_unico_cgtee_interesse"] = $this->input->post("cd_plano_unico_cgtee_interesse", TRUE);
			$args["descricao"]               = $this->input->post("descricao", TRUE);
			$args["cd_usuario"]              = $this->session->userdata('codigo');
			
			$this->plano_unico_cgtee_interesse_model->salvar_acompanhamento($result, $args);
			
			redirect("planos/plano_unico_cgtee_interesse", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	
	function excluir_acompanhamento($cd_plano_unico_cgtee_interesse, $cd_plano_unico_cgtee_interesse_acompanhamento)
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_plano_unico_cgtee_interesse_acompanhamento"] = intval($cd_plano_unico_cgtee_interesse_acompanhamento);
			$args['cd_usuario']                             = $this->session->userdata('codigo');
			
			$this->plano_unico_cgtee_interesse_model->excluir_acompanhamento($result, $args);
			
			redirect("planos/plano_unico_cgtee_interesse/acompanhamento/".$cd_plano_unico_cgtee_interesse, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
?>