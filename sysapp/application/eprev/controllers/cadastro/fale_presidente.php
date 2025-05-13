<?php
class Fale_presidente extends Controller
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

		$this->load->view('cadastro/fale_presidente/cadastro', $data);
    }

    public function salvar()
    {
    	$this->load->model('projetos/eventos_email_model');

    	$cd_evento = 304;

    	$email = $this->eventos_email_model->carrega($cd_evento);

    	$tags = array('[DS_NOME]', '[DS_ASSUNTO]');
        $subs = array(
        	"",#$this->session->userdata('nome'),
        	$this->input->post('ds_assunto', TRUE)
        );

		$assunto = str_replace($tags, $subs, $email['assunto']);

		$tags = array('[DS_DESCRICAO]', '[DS_NOME]');
        $subs = array(
        	$this->input->post('ds_descricao', TRUE),
        	""#$this->session->userdata('nome')
        );

		$texto = str_replace($tags, $subs, $email['email']);

		$cd_usuario = 999999; #$this->session->userdata('codigo');
			
		$args = array(
			'de'      => 'Fale com o presidente',
			'assunto' => $assunto,
			'para'    => $email['para'],
			'cc'      => "", #$this->session->userdata('usuario').'@eletroceee.com.br',
			'cco'     => "", #$email['cco'],
			'texto'   => $texto
		);

		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

		redirect('cadastro/fale_presidente/index/S', 'refresh');
    }
}