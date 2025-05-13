<?php

class Atividade_acompanhamento extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
		$this->load->model('projetos/atividade_acompanhamento_model');
	}
	
	function index($cd_atividade, $cd_gerencia)
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$data['cd_atividade'] = $cd_atividade;
		$data['cd_gerencia']  = $cd_gerencia;		
		
		$this->load->view('atividade/atividade_acompanhamento/index', $data);
	}
	
	function listar()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->atividade_acompanhamento_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$data['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->load->view('atividade/atividade_acompanhamento/index_result', $data);
	}
	
	function salvar()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['ds_atividade_acompanhamento'] = $this->input->post("ds_atividade_acompanhamento", TRUE);
		$args['cd_atividade']                = $this->input->post("cd_atividade", TRUE);
		$args["cd_gerencia"]                 = $this->input->post("cd_gerencia", TRUE);
		$args["cd_usuario"]                  = $this->session->userdata('codigo');
		
		$this->atividade_acompanhamento_model->salvar($result, $args);
		
		$this->enviar_novo_acompanhamento($args);
		
		redirect("atividade/atividade_acompanhamento/index/".intval($args["cd_atividade"])."/".$args["cd_gerencia"], "refresh");
	}
	
	function excluir($cd_atividade, $cd_gerencia, $cd_atividade_acompanhamento)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade_acompanhamento'] = $cd_atividade_acompanhamento;
		$args['cd_atividade']       = $cd_atividade;
		$args['cd_gerencia']        = $cd_gerencia;
		$args['cd_usuario']         = $this->session->userdata('codigo');

		$this->atividade_acompanhamento_model->excluir($result, $args);
		
		redirect('atividade/atividade_acompanhamento/index/'.intval($args['cd_atividade']).'/'.$args['cd_gerencia'], 'refresh');
	}
	
	public function enviar_novo_acompanhamento($args)
    {
		$this->load->model('projetos/eventos_email_model');
		
		$cd_evento = 223;
		
		$cd_atividade = intval($args['cd_atividade']);
		
		$ds_atividade_acompanhamento = trim($args['ds_atividade_acompanhamento']);
		
		$email = $this->eventos_email_model->carrega($cd_evento);
		
		$acompanhamento = $this->atividade_acompanhamento_model->acompanhamento_email($cd_atividade);
		
		$email_para = $this->atividade_acompanhamento_model->get_emails($acompanhamento);
		
		$tags = array('[NUMERO_ATIVIDADE]', '[SOLICITANTE]', '[ATENDENTE]', '[STATUS]', '[ACOMPANHAMENTO]', '[LINK]');
        $subs = array($cd_atividade, $acompanhamento['solicitante'], $acompanhamento['atendente'].($acompanhamento['substituto'] != '' ? ' / '.$acompanhamento['substituto'] : ''), $acompanhamento['status'], nl2br($ds_atividade_acompanhamento), site_url('atividade/atividade_acompanhamento/index/'.intval($acompanhamento['numero']).'/'.trim($acompanhamento['area'])));

		$texto = str_replace($tags, $subs, $email['email']);
		
		$assunto = str_replace('[NUMERO_ATIVIDADE]', $cd_atividade, $email['assunto']);
		
		$cd_usuario = $this->session->userdata('codigo');
		
		$args = array(
			'de'      => 'Atividade - Acompanhamento',
			'assunto' => $assunto,
			'para'    => $email_para['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);
		
		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
}
?>