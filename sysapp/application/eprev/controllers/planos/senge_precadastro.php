<?php
class senge_precadastro extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/senge_precadastro_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GRI')))
		{		
			$this->load->view('planos/senge_precadastro/index');
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
			
			$this->senge_precadastro_model->listar($result, $args);
			$collection = $result->result_array();
			
			$i = 0;
			
			$data['collection'] = array();
			
			foreach($collection as $item)
			{
				$args['cd_senge_precadastro'] = $item['cd_senge_precadastro'];
				
				$this->senge_precadastro_model->acompanhamento($result, $args);
				$arr = $result->result_array();
			
				$data['collection'][$i] = $item;
				$data['collection'][$i]['arr_acompanhamento'] = $arr;
				
				$i++;
			}
			
			$this->load->view('planos/senge_precadastro/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function acompanhamento($cd_senge_precadastro)
    {	
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args['cd_senge_precadastro'] = intval($cd_senge_precadastro);
			
			$this->senge_precadastro_model->carrega($result, $args);
			$data['row'] = $result->row_array();	
			
			$this->senge_precadastro_model->acompanhamento($result, $args);
			$data['collection'] = $result->result_array();
				
			$this->load->view('planos/senge_precadastro/acompanhamento',$data);
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

			$args["cd_senge_precadastro"] = $this->input->post("cd_senge_precadastro", TRUE);
			$args["descricao"]               = $this->input->post("descricao", TRUE);
			$args["cd_usuario"]              = $this->session->userdata('codigo');
			
			$this->senge_precadastro_model->salvar_acompanhamento($result, $args);
			
			redirect("planos/senge_precadastro", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	
	function excluir_acompanhamento($cd_senge_precadastro, $cd_senge_precadastro_acompanhamento)
    {
		if(gerencia_in(array('GRI')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();

			$args["cd_senge_precadastro_acompanhamento"] = intval($cd_senge_precadastro_acompanhamento);
			$args['cd_usuario']                             = $this->session->userdata('codigo');
			
			$this->senge_precadastro_model->excluir_acompanhamento($result, $args);
			
			redirect("planos/senge_precadastro/acompanhamento/".$cd_senge_precadastro, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
}
?>