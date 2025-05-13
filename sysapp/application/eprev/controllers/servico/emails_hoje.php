<?php
class Emails_hoje extends Controller
{
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
	
	private function filtro_enviado()
    {
    	return array(
    		array('value' => 'S', 'text' => 'Sim'),
    		array('value' => 'N', 'text' => 'Não')
    	);
    }
    
	public function index()
    {
		if($this->get_permissao())
		{
			$this->load->model('projetos/emails_hoje_model');
			
			$data = array();
			
			$data['fl_enviado'] = $this->filtro_enviado();
			$data['cd_evento']  = $this->emails_hoje_model->get_evento();
			$data['cd_divulgacao']  = $this->emails_hoje_model->get_divulgacao();
			
			$this->load->view('servico/emails_hoje/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {
		$this->load->model('projetos/emails_hoje_model');
			
		$args = array();

		$args = array(
			'assunto'       => $this->input->post('assunto', TRUE),
			'fl_enviado'    => $this->input->post('fl_enviado', TRUE),
			'cd_evento'     => $this->input->post('cd_evento', TRUE),
			'cd_divulgacao' => $this->input->post('cd_divulgacao', TRUE)
		);

		manter_filtros($args);
		
		$data['collection'] = $this->emails_hoje_model->listar($args);
		
		$this->load->view('servico/emails_hoje/index_result', $data);		
    }
}