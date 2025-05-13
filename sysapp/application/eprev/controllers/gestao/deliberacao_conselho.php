<?php

class deliberacao_conselho extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('gestao/deliberacao_conselho_model');
    }
	
	public function index()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$this->load->view('gestao/deliberacao_conselho/index', $data);
    }
	
	public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['nr_deliberacao_conselho'] = $this->input->post("nr_deliberacao_conselho", TRUE);   
		$args['nr_ano']      = $this->input->post("nr_ano", TRUE);   
		$args['dt_ini']      = $this->input->post("dt_ini", TRUE);   
		$args['dt_fim']      = $this->input->post("dt_fim", TRUE);   
		$args['ds_deliberacao_conselho'] = $this->input->post("ds_deliberacao_conselho", TRUE);   
		
		manter_filtros($args);

		$this->deliberacao_conselho_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('gestao/deliberacao_conselho/index_result', $data);
    }
	
	public function cadastro($cd_deliberacao_conselho = 0)
    {
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_deliberacao_conselho'] = intval($cd_deliberacao_conselho);
			
			if(intval($args['cd_deliberacao_conselho']) == 0)
			{
				$data['row'] = array(
					'cd_deliberacao_conselho'             => intval($args['cd_deliberacao_conselho']),
					'nr_ano'                             => '',
					'nr_deliberacao_conselho'             => '',
					'dt_deliberacao_conselho'             => '',
					'ds_deliberacao_conselho'             => '',
					'fl_situacao'                        => 'N',
					'cd_deliberacao_conselho_abrangencia' => 1,
					'observacao'                         => '',
					'arquivo'                            => '',
					'arquivo_nome'                       => '',
					'nr_ata'                             => ''
				);
			}
			else
			{
				$this->deliberacao_conselho_model->carrega($result, $args);
                $data['row'] = $result->row_array();
			}
			
			$this->load->view('gestao/deliberacao_conselho/cadastro', $data);
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
			
			$args['cd_deliberacao_conselho']             = $this->input->post("cd_deliberacao_conselho", TRUE); 
			$args['nr_deliberacao_conselho']             = $this->input->post("nr_deliberacao_conselho", TRUE);   
			$args['nr_ano']                             = $this->input->post("nr_ano", TRUE); 
			$args['dt_deliberacao_conselho']             = $this->input->post("dt_deliberacao_conselho", TRUE); 
			$args['ds_deliberacao_conselho']             = $this->input->post("ds_deliberacao_conselho", TRUE); 
			$args['fl_situacao']                        = $this->input->post("fl_situacao", TRUE); 
			$args['cd_deliberacao_conselho_abrangencia'] = $this->input->post("cd_deliberacao_conselho_abrangencia", TRUE); 
			$args['observacao']                         = $this->input->post("observacao", TRUE); 
			$args['arquivo']                            = $this->input->post("arquivo", TRUE); 
			$args['arquivo_nome']                       = $this->input->post("arquivo_nome", TRUE); 
			$args['nr_ata']                             = $this->input->post("nr_ata", TRUE); 
			$args['rds']                                = $this->input->post("rds", TRUE); 
			$args['area']                               = $this->input->post("area", TRUE); 
			$args['cd_usuario']                         = $this->session->userdata('codigo');
		
			$cd_deliberacao_conselho = $this->deliberacao_conselho_model->salvar($result, $args);
			
			redirect("gestao/deliberacao_conselho/cadastro/".$cd_deliberacao_conselho, "refresh");
		}
		else
		{
			$this->deliberacao_conselho_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
	}
	
	public function excluir($cd_deliberacao_conselho)
	{
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_deliberacao_conselho'] = intval($cd_deliberacao_conselho);
			$args['cd_usuario']  = $this->session->userdata('codigo');
			
			$this->deliberacao_conselho_model->excluir($result, $args);
			
			redirect("gestao/deliberacao_conselho", "refresh");
		}
		else
		{
			$this->deliberacao_conselho_model->carrega($result, $args);
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
			
			$this->deliberacao_conselho_model->divulgar($result, $args);
			
			redirect("gestao/deliberacao_conselho", "refresh");
		}
		else
		{
			$this->deliberacao_conselho_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
	}
}

?>