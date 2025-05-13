<?php
class contrato extends Controller
{
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();
		
		$this->load->model("projetos/contrato_model");
	}

	function index()
	{
		if(gerencia_in(array('GGS')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->contrato_model->gerencias($result, $args);
			$data['arr_gerencias'] = $result->result_array();

			$this->load->view('cadastro/contrato/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function listar()
	{
		if(gerencia_in(array('GGS')))
		{
			$args = Array();
			$data = Array();
			$result = null;
					
			$args['cd_gerencia']         = $this->input->post("cd_gerencia", TRUE);   
			$args['ds_empresa']          = $this->input->post("ds_empresa", TRUE);   
			$args['ds_servico']          = $this->input->post("ds_servico", TRUE);  
			$args['status_contrato']     = $this->input->post("status_contrato", TRUE);  
			$args['fl_avaliar']     = $this->input->post("fl_avaliar", TRUE);  
			$args['dt_inicio_ini']       = $this->input->post("dt_inicio_ini", TRUE);  
			$args['dt_inicio_fim']       = $this->input->post("dt_inicio_fim", TRUE);  
			$args['dt_encerramento_ini'] = $this->input->post("dt_encerramento_ini", TRUE);  
			$args['dt_encerramento_fim'] = $this->input->post("dt_encerramento_fim", TRUE);  
			$args['dt_reajuste_ini']     = $this->input->post("dt_reajuste_ini", TRUE);  
			$args['dt_reajuste_fim']     = $this->input->post("dt_reajuste_fim", TRUE);  

			manter_filtros($args);
			
			$this->contrato_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('cadastro/contrato/partial_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function cadastro($cd_contrato = 0)
    {
		if(gerencia_in(array('GGS')))
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args['cd_contrato'] = intval($cd_contrato);
			
			$this->contrato_model->gerencias($result, $args);
			$data['arr_gerencias'] = $result->result_array();
			
			$this->contrato_model->contrato_pagamento($result, $args);
			$data['arr_contrato_pagamentos'] = $result->result_array();
			
			if ($cd_contrato == 0)
			{
				$data['row'] = Array(
				  'cd_contrato'           => 0,
				  'cd_contrato'           => '',
				  'ds_empresa'            => '',
				  'ds_servico'            => '',
				  'ds_valor'              => '',
				  'cd_contrato_pagamento' => '',
				  'dt_inicio'             => '',
				  'dt_encerramento'       => '',
				  'dt_reajuste'           => '',
				  'cd_divisao'            => ''
				);
			}
			else
			{		
				$data['row'] = $this->contrato_model->carrega($cd_contrato);
			}

			$this->load->view('cadastro/contrato/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

	function salvar()
	{
		if(gerencia_in(array('GGS')))
		{
			$args = Array();
			$data = Array();
			$result = null;

			$args['cd_contrato']           = $this->input->post('cd_contrato');
			$args['ds_empresa']            = $this->input->post('ds_empresa');
			$args['ds_servico']            = $this->input->post('ds_servico');
			$args['ds_valor']              = $this->input->post('ds_valor');
			$args['cd_contrato_pagamento'] = $this->input->post('cd_contrato_pagamento');
			$args['dt_inicio']             = $this->input->post('dt_inicio');
			$args['dt_encerramento']       = $this->input->post('dt_encerramento');
			$args['dt_reajuste']           = $this->input->post('dt_reajuste');
			$args['cd_divisao']            = $this->input->post('cd_divisao');
		
			$cd_contrato = $this->contrato_model->salvar($result, $args);

			redirect('cadastro/contrato/cadastro/'.$cd_contrato, 'refresh');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function adicionar_responsavel()
	{
		if(gerencia_in(array('GGS')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato'] = $this->input->post('cd_contrato');
			$args['cd_usuario']  = $this->input->post('cd_usuario');

		    $this->contrato_model->adicionar_responsavel($result, $args);

			redirect( 'cadastro/contrato/cadastro/'.intval($args['cd_contrato']), 'refresh' );
		
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function listar_responsaveis()
	{
		if(gerencia_in(array('GGS')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato'] = $this->input->post('cd_contrato');

		    $this->contrato_model->listar_responsaveis($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/contrato/responsaveis_result', $data);
		
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir_responsavel()
	{
		if(gerencia_in(array('GGS')))
		{		
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato_responsavel'] = $this->input->post('cd_contrato_responsavel');
			$args['cd_usuario']              = $this->session->userdata('codigo');

			$this->contrato_model->excluir_responsavel($result, $args);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
	}
	
	function excluir($cd_contrato)
	{
		if(gerencia_in(array('GGS')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_contrato'] = $cd_contrato;
			$args['cd_usuario']  = $this->session->userdata('codigo');

		    $this->contrato_model->excluir($result, $args);
			
			redirect('cadastro/contrato', 'refresh');
		
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function avaliadores($cd_contrato, $cd_contrato_avaliador = 0)
	{
		if(gerencia_in(array('GGS')))
		{
			$data['row'] = $this->contrato_model->carrega($cd_contrato);
			
           	$data['collection'] = $this->contrato_model->listar_avaliadores($cd_contrato);

           	$this->load->view('cadastro/contrato/avaliadores', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function get_usuarios()
    {		
		$this->load->model("projetos/contrato_model");

		$cd_gerencia = $this->input->post('cd_gerencia', TRUE);

		echo json_encode($this->contrato_model->get_usuarios($cd_gerencia));
    }

	public function salvar_avaliadores()
	{
		if(gerencia_in(array('GGS')))
		{
			$cd_contrato_avaliador = $this->input->post('cd_contrato_avaliador', TRUE); 

			$cd_contrato = $this->input->post('cd_contrato', TRUE);

			$args = array(
				'cd_contrato_avaliador' => $this->input->post('cd_contrato_avaliador', TRUE),
				'cd_contrato'           => $this->input->post('cd_contrato', TRUE),
				'cd_usuario'            => $this->input->post('cd_usuario', TRUE),
				'cd_usuario_inclusao'   => $this->session->userdata('codigo'),
			);
			
			if(intval($cd_contrato_avaliador) == 0)
			{    
				$cd_contrato_avaliador = $this->contrato_model->salvar_avaliador($args);
			}
			
			redirect('cadastro/contrato/avaliadores/'.$cd_contrato, 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir_avaliadores($cd_contrato, $cd_contrato_avaliador)
	{
		$this->contrato_model->excluir_avaliador($cd_contrato, $cd_contrato_avaliador, $this->session->userdata('codigo'));

		redirect('cadastro/contrato/avaliadores/'.$cd_contrato, 'refresh');
	}
}
?>