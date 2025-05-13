<?php
class Controle_rds_solicitacao extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao()
	{
		if(gerencia_in(array('GC')))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function index()
	{
		$data = array(
			'nr_ano' => ''
		);

		$this->load->view('gestao/controle_rds_solicitacao/index',$data);
	}

	public function listar()
	{
		$this->load->model('gestao/controle_rds_solicitacao_model');

		$args = array(
			'nr_ano' => $this->input->post('nr_ano', TRUE),
		);

		manter_filtros($args);

		$data['collection']   = $this->controle_rds_solicitacao_model->listar($args);
		$data['fl_permissao'] = $this->get_permissao();

		$this->load->view('gestao/controle_rds_solicitacao/index_result', $data);
	}

	public function cadastro($cd_controle_rds_solicitacao = 0)
	{
		$this->load->model('gestao/controle_rds_solicitacao_model');

		if(intval($cd_controle_rds_solicitacao) == 0)
		{
			$data['row'] = array(
				'cd_controle_rds_solicitacao' => 0,
				'ds_controle_rds_solicitacao' => '',
				'dt_controle_rds_solicitacao' => '',
				'nr_controle_rds_solicitacao' => '',
				'cd_gerencia' 				  => $this->session->userdata('divisao')
			);
		}
		else
		{
			$data['row'] 	  = $this->controle_rds_solicitacao_model->carrega($cd_controle_rds_solicitacao);
		}

		$data['gerencia'] 	  = $this->controle_rds_solicitacao_model->get_gerencia();
		$data['fl_permissao'] = $this->get_permissao();

		$this->load->view('gestao/controle_rds_solicitacao/cadastro', $data);
	}

	public function salvar()
	{
		$this->load->model('gestao/controle_rds_solicitacao_model');

		$cd_controle_rds_solicitacao = $this->input->post('cd_controle_rds_solicitacao', TRUE);

		$args = array(
			'ds_controle_rds_solicitacao'    => $this->input->post('ds_controle_rds_solicitacao', TRUE),
			'dt_controle_rds_solicitacao'    => $this->input->post('dt_controle_rds_solicitacao', TRUE),
			'cd_gerencia' 					 => $this->input->post('cd_gerencia', TRUE),
			'cd_usuario'     				 => $this->session->userdata('codigo')
		);

		if(intval($cd_controle_rds_solicitacao) == 0)
		{
			$cd_controle_rds_solicitacao = $this->controle_rds_solicitacao_model->salvar($args);

			#$this->envia_email_solicitação($cd_controle_rds_solicitacao);
		}
		else
		{
			$this->controle_rds_solicitacao_model->atualizar($cd_controle_rds_solicitacao, $args);
		}

		redirect('gestao/controle_rds_solicitacao', 'refresh');
	}

	private function envia_email_solicitação($cd_controle_rds_solicitacao)
	{
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 382;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = '[LINK]';
		$subs = site_url('gestao/controle_rds_solicitacao/cadastro/'.$cd_controle_rds_solicitacao);

        $texto = str_replace($tags, $subs, $email['email']);
        
        $cd_usuario = $this->session->userdata('codigo');

		$args = array(
			'de'      => 'Solicitação de Número de RDS',
			'assunto' => $email['assunto'],
			'para'    => $email['para'],
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
        );
        
        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}
}