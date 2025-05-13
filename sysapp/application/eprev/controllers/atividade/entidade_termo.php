<?php
class entidade_termo extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('entidades/termo_model');
    }
	
	function index()
    {
		if(gerencia_in(array('GP')))
		{							
			$this->load->view('atividade/entidade_termo/index');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function listar()
    {		
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["dt_alteracao_ini"] = $this->input->post("dt_alteracao_ini", TRUE);
			$args["dt_alteracao_fim"] = $this->input->post("dt_alteracao_fim", TRUE);
			
			manter_filtros($args);
			
			$this->termo_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('atividade/entidade_termo/index_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }
	
	function cadastro($cd_termo = 0)
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_termo"] = $cd_termo;
			
			if(intval($args["cd_termo"]) == 0)
			{
				$data['row'] = array(
					'cd_termo'     => intval($cd_termo),
					'dt_inicial'   => '',
					'dt_final'     => '',
					'ds_termo'     => '',
					'nr_dia_termo' => ''
				);
			}
			else
			{
				$this->termo_model->carrega($result, $args);
                $data['row'] = $result->row_array();
			}
			
			$this->load->view('atividade/entidade_termo/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	function salvar()
	{
		if(gerencia_in(array('GP')))
		{
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$args["cd_termo"]     = $this->input->post("cd_termo", TRUE);
			$args["dt_inicial"]   = $this->input->post("dt_inicial", TRUE);
			$args["dt_final"]     = $this->input->post("dt_final", TRUE);
			$args["ds_termo"]     = $this->input->post("ds_termo", TRUE);
			$args["nr_dia_termo"] = $this->input->post("nr_dia_termo", TRUE);
			$args["cd_usuario"]   = $this->session->userdata('codigo');
			
			$this->termo_model->salvar($result, $args);
			
			redirect("atividade/entidade_termo", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}
?>