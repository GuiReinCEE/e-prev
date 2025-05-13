<?php
class Canal_sugestao extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index($fl_solicitacao = 'N')
    {
    	$data['row'] = array(
			'ds_assunto'     => '',
			'ds_descricao'   => '',
			'fl_solicitacao' => $fl_solicitacao
		);

		$this->load->view('cadastro/canal_sugestao/cadastro', $data);
    }

    public function salvar()
    {
    	$this->load->model('projetos/eventos_email_model');

    	$cd_evento = 307;

    	$email = $this->eventos_email_model->carrega($cd_evento);

		$assunto = str_replace('[DS_ASSUNTO]', $this->input->post('ds_assunto', TRUE), $email['assunto']);

		$texto = str_replace('[DS_DESCRICAO]', $this->input->post('ds_descricao', TRUE), $email['email']);

		$cd_usuario = $this->session->userdata('codigo');
			
		$args = array(
			'de'      => 'Canal de sugestões',
			'assunto' => $assunto,
			'para'    => $email['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);

		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

		redirect('cadastro/canal_sugestao/index/S', 'refresh');
    }
}