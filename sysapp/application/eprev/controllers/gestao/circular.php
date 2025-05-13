<?php

class circular extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('gestao/circular_model');
    }
	
	public function index()
    {
		$args = Array();
		$data = Array();
		$result = null;

		$this->load->view('gestao/circular/index', $data);
    }
	
	public function listar()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['nr_circular'] = $this->input->post("nr_circular", TRUE);   
		$args['nr_ano']      = $this->input->post("nr_ano", TRUE);   
		$args['dt_ini']      = $this->input->post("dt_ini", TRUE);   
		$args['dt_fim']      = $this->input->post("dt_fim", TRUE);   
		$args['ds_circular'] = $this->input->post("ds_circular", TRUE);   
		
		manter_filtros($args);

		$this->circular_model->listar($result, $args);
		$data['collection'] = $result->result_array();

		$this->load->view('gestao/circular/index_result', $data);
    }
	
	public function cadastro($cd_circular = 0)
    {
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_circular'] = intval($cd_circular);
			
			if(intval($args['cd_circular']) == 0)
			{
				$data['row'] = array(
					'cd_circular'             => intval($args['cd_circular']),
					'nr_ano'                  => '',
					'nr_circular'             => '',
					'dt_circular'             => '',
					'ds_circular'             => '',
					'fl_situacao'             => 'N',
					'cd_circular_abrangencia' => 1,
					'observacao'              => '',
					'arquivo'                 => '',
					'arquivo_nome'            => ''
				);
			}
			else
			{
				$this->circular_model->carrega($result, $args);
                $data['row'] = $result->row_array();
			}
			
			$this->load->view('gestao/circular/cadastro', $data);
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
			
			$args['cd_circular']             = $this->input->post("cd_circular", TRUE); 
			$args['nr_ano']                  = $this->input->post("nr_ano", TRUE); 
			$args['nr_circular']             = $this->input->post("nr_circular", TRUE); 
			$args['dt_circular']             = $this->input->post("dt_circular", TRUE); 
			$args['ds_circular']             = $this->input->post("ds_circular", TRUE); 
			$args['fl_situacao']             = $this->input->post("fl_situacao", TRUE); 
			$args['cd_circular_abrangencia'] = $this->input->post("cd_circular_abrangencia", TRUE); 
			$args['observacao']              = $this->input->post("observacao", TRUE); 
			$args['arquivo']                 = $this->input->post("arquivo", TRUE); 
			$args['arquivo_nome']            = $this->input->post("arquivo_nome", TRUE); 
			$args['cd_usuario']              = $this->session->userdata('codigo');
		
			$cd_circular = $this->circular_model->salvar($result, $args);
			
			redirect("gestao/circular/cadastro/".$cd_circular, "refresh");
		}
		else
		{
			$this->circular_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
	}
	
	public function excluir($cd_circular)
	{
		if(gerencia_in(array('GC')))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_circular'] = intval($cd_circular);
			$args['cd_usuario']  = $this->session->userdata('codigo');
			
			$this->circular_model->excluir($result, $args);
			
			redirect("gestao/circular", "refresh");
		}
		else
		{
			$this->circular_model->carrega($result, $args);
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
			
			$this->circular_model->divulgar($result, $args);
			
			redirect("gestao/circular", "refresh");
		}
		else
		{
			$this->circular_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
	}
}

?>