<?php
class Caderno_cci_integracao_indicador extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
	
	public function get_referencia_tabela()
	{
		return array(
    		array('value' => 'P', 'text' => 'Projetado'),
    		array('value' => 'I', 'text' => 'Índice'),
    		array('value' => 'B', 'text' => 'Benchmark'),
    		array('value' => 'E', 'text' => 'Estrutura')
    	);
	}
	
    public function index()
    {
    	if($this->get_permissao())
    	{
    		$data = array();
			
    		$this->load->view('servico/caderno_cci_integracao_indicador/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('gestao/caderno_cci_integracao_indicador_model');

    	$args = array();
		$data = array();
		
		$args = array(
			'ds_indicador'                        => $this->input->post('ds_indicador', TRUE),
			'ds_caderno_cci_integracao_indicador' => $this->input->post('ds_caderno_cci_integracao_indicador', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->caderno_cci_integracao_indicador_model->listar($args);

		$this->load->view('servico/caderno_cci_integracao_indicador/index_result', $data);
    }

    public function cadastro($cd_caderno_cci_integracao_indicador = 0)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('gestao/caderno_cci_integracao_indicador_model');

			$data = array();

			if(intval($cd_caderno_cci_integracao_indicador) == 0)
			{
				$data['row'] = array(
					'cd_caderno_cci_integracao_indicador' => $cd_caderno_cci_integracao_indicador,
					'ds_indicador'						  => '',
					'ds_caderno_cci_integracao_indicador' => ''
				);
			}
			else
			{
				$data['row'] = $this->caderno_cci_integracao_indicador_model->carrega($cd_caderno_cci_integracao_indicador);
			}
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}

    	$this->load->view('servico/caderno_cci_integracao_indicador/cadastro', $data);
    }

    public function salvar()
	{
		if($this->get_permissao())
        {
    		$this->load->model('gestao/caderno_cci_integracao_indicador_model');

    		$args = array();
			
			$cd_caderno_cci_integracao_indicador = $this->input->post('cd_caderno_cci_integracao_indicador', TRUE);

			$args = array(
				'ds_indicador'						  => $this->input->post('ds_indicador', TRUE),
				'ds_caderno_cci_integracao_indicador' => $this->input->post('ds_caderno_cci_integracao_indicador', TRUE),
				'cd_usuario' 						  => $this->session->userdata('codigo')
			);

			if(intval($cd_caderno_cci_integracao_indicador) == 0)
			{
				$cd_caderno_cci_integracao_indicador = $this->caderno_cci_integracao_indicador_model->salvar($args);
			}
			else
			{
				$this->caderno_cci_integracao_indicador_model->atualizar($cd_caderno_cci_integracao_indicador, $args);
			}
			
			redirect('servico/caderno_cci_integracao_indicador/cadastro/'.$cd_caderno_cci_integracao_indicador);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function campo_integracao($cd_caderno_cci_integracao_indicador, $cd_caderno_cci_integracao_indicador_campo = 0)
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/caderno_cci_integracao_indicador_model');

			$data = array();				
			
			$data['cadastro'] = $this->caderno_cci_integracao_indicador_model->carrega($cd_caderno_cci_integracao_indicador);
			
			$data['tipo'] = $this->get_referencia_tabela();
			
			$data['collection'] = $this->caderno_cci_integracao_indicador_model->integracao_listar($cd_caderno_cci_integracao_indicador);

			if(intval($cd_caderno_cci_integracao_indicador_campo) == 0)
			{				
				$data['campo_integracao'] = array(
					'cd_caderno_cci_integracao_indicador_campo' => $cd_caderno_cci_integracao_indicador_campo,
					'fl_referencia_tabela' 						=> '',
					'ds_caderno_cci_integracao_indicador_campo' => '',
					'cd_referencia_integracao' 					=> ''
				);
			}
			else
			{
				$data['campo_integracao'] = $this->caderno_cci_integracao_indicador_model->integracao_carrega($cd_caderno_cci_integracao_indicador_campo);
			}
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
		
    	$this->load->view('servico/caderno_cci_integracao_indicador/campo_integracao', $data);
	}
	
	public function campo_integracao_salvar()
	{
		if($this->get_permissao())
        {
			$this->load->model('gestao/caderno_cci_integracao_indicador_model');

			$args = array();
		
			$cd_caderno_cci_integracao_indicador 	   = $this->input->post('cd_caderno_cci_integracao_indicador', TRUE);
			$cd_caderno_cci_integracao_indicador_campo = $this->input->post('cd_caderno_cci_integracao_indicador_campo', TRUE);

			$args = array(
				'cd_caderno_cci_integracao_indicador'		=> $this->input->post('cd_caderno_cci_integracao_indicador', TRUE),
				'fl_referencia_tabela'	  					=> $this->input->post('fl_referencia_tabela', TRUE),
				'ds_caderno_cci_integracao_indicador_campo' => $this->input->post('ds_caderno_cci_integracao_indicador_campo', TRUE),
				'cd_referencia_integracao'  				=> $this->input->post('cd_referencia_integracao', TRUE),
				'cd_usuario' 								=> $this->session->userdata('codigo')
			);

			if(intval($cd_caderno_cci_integracao_indicador_campo) == 0)
			{
				$cd_caderno_cci_integracao_indicador_campo = $this->caderno_cci_integracao_indicador_model->integracao_salvar($args);
			}
			else
			{
				$this->caderno_cci_integracao_indicador_model->integracao_atualizar($cd_caderno_cci_integracao_indicador_campo, $args);
			}
			
			redirect('servico/caderno_cci_integracao_indicador/campo_integracao/'.$cd_caderno_cci_integracao_indicador);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}