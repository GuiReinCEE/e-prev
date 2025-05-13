<?php

class resolucao_diretoria extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('gestao/resolucao_diretoria_model');
    }
	
	public function index()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$this->load->view('gestao/resolucao_diretoria/index', $data);
    }
	
	public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['nr_resolucao_diretoria'] = $this->input->post("nr_resolucao_diretoria", TRUE);   
		$args['nr_ano']      = $this->input->post("nr_ano", TRUE);   
		$args['dt_ini']      = $this->input->post("dt_ini", TRUE);   
		$args['dt_fim']      = $this->input->post("dt_fim", TRUE);   
		$args['ds_resolucao_diretoria'] = $this->input->post("ds_resolucao_diretoria", TRUE);   
		
		manter_filtros($args);

		$this->resolucao_diretoria_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('gestao/resolucao_diretoria/index_result', $data);
    }
	
	public function cadastro($cd_resolucao_diretoria = 0)
    {
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_resolucao_diretoria'] = intval($cd_resolucao_diretoria);
			
			if(intval($args['cd_resolucao_diretoria']) == 0)
			{
				$data['row'] = array(
					'cd_resolucao_diretoria'             => intval($args['cd_resolucao_diretoria']),
					'nr_ano'                             => '',
					'nr_resolucao_diretoria'             => '',
					'dt_resolucao_diretoria'             => '',
					'ds_resolucao_diretoria'             => '',
					'fl_situacao'                        => 'N',
					'cd_resolucao_diretoria_abrangencia' => 1,
					'observacao'                         => '',
					'arquivo'                            => '',
					'arquivo_nome'                       => '',
					'nr_ata'                             => '',
					'rds'                                => '',
					'area'                               => ''
				);
			}
			else
			{
				$this->resolucao_diretoria_model->carrega($result, $args);
                $data['row'] = $result->row_array();
			}
			
			$this->load->view('gestao/resolucao_diretoria/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
	
	public function salvar()
    {
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_resolucao_diretoria']             = $this->input->post("cd_resolucao_diretoria", TRUE); 
			$args['nr_resolucao_diretoria']             = $this->input->post("nr_resolucao_diretoria", TRUE);   
			$args['nr_ano']                             = $this->input->post("nr_ano", TRUE); 
			$args['dt_resolucao_diretoria']             = $this->input->post("dt_resolucao_diretoria", TRUE); 
			$args['ds_resolucao_diretoria']             = $this->input->post("ds_resolucao_diretoria", TRUE); 
			$args['fl_situacao']                        = $this->input->post("fl_situacao", TRUE); 
			$args['cd_resolucao_diretoria_abrangencia'] = $this->input->post("cd_resolucao_diretoria_abrangencia", TRUE); 
			$args['observacao']                         = $this->input->post("observacao", TRUE); 
			$args['arquivo']                            = $this->input->post("arquivo", TRUE); 
			$args['arquivo_nome']                       = $this->input->post("arquivo_nome", TRUE); 
			$args['nr_ata']                             = $this->input->post("nr_ata", TRUE); 
			$args['rds']                                = $this->input->post("rds", TRUE); 
			$args['area']                               = $this->input->post("area", TRUE); 
			$args['cd_usuario']                         = $this->session->userdata('codigo');
		
			$cd_resolucao_diretoria = $this->resolucao_diretoria_model->salvar($result, $args);
			
			redirect("gestao/resolucao_diretoria/cadastro/".$cd_resolucao_diretoria, "refresh");
		}
		else
		{
			$this->resolucao_diretoria_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
	}
	
	public function excluir($cd_resolucao_diretoria)
	{
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_resolucao_diretoria'] = intval($cd_resolucao_diretoria);
			$args['cd_usuario']  = $this->session->userdata('codigo');
			
			$this->resolucao_diretoria_model->excluir($result, $args);
			
			redirect("gestao/resolucao_diretoria", "refresh");
		}
		else
		{
			$this->resolucao_diretoria_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
	}
	
	public function divulgar()
	{
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$arr                = (is_array($this->input->post("arr")) ? $this->input->post("arr") : array());
			$args['cd_usuario'] = $this->session->userdata('codigo');
			
			$i = 0;
			
			$args['arr'] = array();
			
			foreach($arr as $item)
			{
				$args['arr'][$i] = $item;
				
				$i++;
			}
			
			$this->resolucao_diretoria_model->divulgar($result, $args);
			
			redirect("gestao/resolucao_diretoria", "refresh");
		}
		else
		{
			$this->resolucao_diretoria_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
	}
}

?>