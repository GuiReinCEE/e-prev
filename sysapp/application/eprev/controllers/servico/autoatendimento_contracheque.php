<?php
class Autoatendimento_contracheque extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }
	
	private function get_tipo()
    {
    	return array(
    		array('value' => 'M', 'text' => 'Mensal'),
    		array('value' => 'B', 'text' => 'Anual')
    	);
    }
	
    public function index()
    {
		$data = array();

		$data['tipo'] = $this->get_tipo();
		
		$this->load->view('servico/autoatendimento_contracheque/index', $data);
    }	
	
    public function listar()
    {		
		$this->load->model('autoatendimento/contracheque_imagem');

		$data = array();
		$args = array();
		
		$args['fl_tipo'] = trim($this->input->post('fl_tipo', TRUE));
		
		manter_filtros($args);
		
		$data['collection'] = $this->contracheque_imagem->listar($args);
		
		$this->load->view('servico/autoatendimento_contracheque/index_result', $data);
	}
	
    public function cadastro($cd_contracheque_imagem = 0)
    {
		$this->load->model('autoatendimento/contracheque_imagem');
		
		$data = array();
		
		$data['tipo'] = $this->get_tipo();
		
		if(intval($cd_contracheque_imagem) == 0)
		{
			$data['row'] = array(
				'cd_contracheque_imagem' => 0,
				'fl_tipo' 				 => '',
				'dt_referencia' 		 => '',
				'arquivo'         		 => '',
				'arquivo_nome'         	 => ''
			); 
		}
		else			
		{
			$data['row'] = $this->contracheque_imagem->carregar($cd_contracheque_imagem);
		}
		
		$this->load->view('servico/autoatendimento_contracheque/cadastro', $data);
    }	
	
    public function salvar()
    {
		$this->load->model('autoatendimento/contracheque_imagem');
		
		$args = array();
		
		$args['fl_tipo']			 	= trim($this->input->post('fl_tipo', TRUE));
		$args['dt_referencia']		 	= trim($this->input->post('dt_referencia', TRUE));
		$args['arquivo']		 		= trim($this->input->post('arquivo', TRUE));
		$args['arquivo_nome']		 	= trim($this->input->post('arquivo_nome', TRUE));
		$cd_usuario	     			 	= $this->session->userdata('codigo');
		$cd_contracheque_imagem 		= trim($this->input->post('cd_contracheque_imagem', TRUE));
		
		if(intval($cd_contracheque_imagem) == 0)
		{
			$this->contracheque_imagem->salvar($cd_usuario, $args);
		}
		else
		{
			$this->contracheque_imagem->alterar($cd_contracheque_imagem, $cd_usuario, $args);
		}
		redirect('servico/autoatendimento_contracheque', 'refresh');
	}	
}
