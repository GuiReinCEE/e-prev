<?php
class Eventos extends Controller {

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

	private function get_tipo()
    {
    	return array(
    		array('value' => 'E', 'text' => 'Email'),
    		array('value' => 'N', 'text' => 'Padrão')
    	);
    }
	
    public function index()
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('projetos/eventos_email_model');

    		$data = array();
			
			$data['fl_tipo'] = $this->get_tipo();
			
    		$this->load->view('servico/eventos/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('projetos/eventos_email_model');

    	$args = array();
		$data = array();
		
		$args['nome'] 	 = $this->input->post('nome', TRUE);
		$args['assunto'] = $this->input->post('assunto', TRUE);
		$args['fl_tipo'] = $this->input->post('fl_tipo', TRUE);
		
		manter_filtros($args);
		
		$data['collection'] = $this->eventos_email_model->listar($args);

		$this->load->view('servico/eventos/index_result', $data);
    }

    public function cadastro($cd_evento = 0)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('projetos/eventos_email_model');

			$data = array();
			
			$data['tipo'] = $this->get_tipo();

			if(intval($cd_evento) == 0)
			{
				$data['row'] = array(
					'cd_evento' => $cd_evento,
					'nome' 		=> '',
					'assunto'	=> '',
					'para'		=> '',
					'cc'		=> '',
					'cco'		=> '',
					'email'		=> '',
					'fl_tipo'	=> 'E'
				);
			}
			else
			{
				$data['row'] = $this->eventos_email_model->carrega($cd_evento);
			}

			$this->load->view('servico/eventos/cadastro', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function salvar()
	{
		if($this->get_permissao())
        {
        	$this->load->model('projetos/eventos_email_model');

			$args = array();
			
			$cd_evento = $this->input->post('cd_evento', TRUE);

			$args = array(
				'nome' 		 => $this->input->post('nome', TRUE),
				'assunto'    => $this->input->post('assunto', TRUE),
				'para'       => $this->input->post('para', TRUE),
				'cc'         => $this->input->post('cc', TRUE),
				'cco'        => $this->input->post('cco', TRUE),
				'email'      => $this->input->post('email', TRUE),
				'fl_tipo' 	 => $this->input->post('fl_tipo', TRUE),
				'cd_usuario' => $this->session->userdata('codigo')
			);

			if(intval($cd_evento) == 0)
			{
				$cd_evento = $data['collection'] = $this->eventos_email_model->salvar($args);
			}
			else
			{
				$this->eventos_email_model->atualizar($cd_evento, $args);
			}
			
			redirect('servico/eventos');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function envia_email($cd_evento)
	{
		if($this->get_permissao())
    	{
    		$this->load->model('projetos/eventos_email_model');			
			
			$data['cadastro'] = $this->eventos_email_model->carrega($cd_evento);
		
    		$this->load->view('servico/eventos/envia_email', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
	}
	
	public function envia_email_listar($cd_evento)
	{
		$this->load->model('projetos/eventos_email_model');

    	$args = array();
		$data = array();
		
		$args["dt_envio_ini"]         = $this->input->post("dt_envio_ini", TRUE);
		$args["dt_envio_fim"]         = $this->input->post("dt_envio_fim", TRUE);
		$args["dt_email_enviado_ini"] = $this->input->post("dt_email_enviado_ini", TRUE);
		$args["dt_email_enviado_fim"] = $this->input->post("dt_email_enviado_fim", TRUE);

		manter_filtros($args);
		
		$data['collection'] = $this->eventos_email_model->envia_email_listar($cd_evento, $args);

		$this->load->view('servico/eventos/envia_email_result', $data);
	}
	
}