<?php
class Documento_protocolo_conferencia extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	public function index($nr_mes = '', $nr_ano = '')
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		if(trim($nr_ano) == '' AND trim($nr_mes) == '')
		{
			$row = $this->documento_protocolo_conf_gerencia_model->get_mes_ano_conferir($this->session->userdata('divisao'));

			$nr_ano = (isset($row['ano_referencia']) ? $row['ano_referencia'] : 0);
			$nr_mes = (isset($row['mes_referencia']) ? $row['mes_referencia'] : 0);

			$data['fl_status'] = '';
		}
		else
		{
			$data['fl_status'] = 'P';
		}

		$data['nr_ano'] = $nr_ano;
		$data['nr_mes'] = $nr_mes;

		$data['ano'] = $this->documento_protocolo_conf_gerencia_model->get_ano_relatorio($this->session->userdata('divisao'));

		$data['drop_status'] = array(
			array('value' => 'P', 'text' => 'Pendente'),
			array('value' => 'C', 'text' => 'Conferido'),
			array('value' => 'A', 'text' => 'Ajustes')
		);

		$this->load->view('atividade/documento_protocolo_conferencia/index', $data);
	}

	public function listar()
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		$args = array(
			'mes_referencia' => $this->input->post('mes_referencia', TRUE),
			'ano_referencia' => $this->input->post('ano_referencia', TRUE),
			'fl_status' 	 => $this->input->post('fl_status', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->documento_protocolo_conf_gerencia_model->listar_docs($this->session->userdata('codigo'), $args);

		$data['drop_status'] = array(
			'P' => 'Pendente',
			'C' => 'Conferido',
			'A' => 'Ajustar'
		);

		$this->load->view('atividade/documento_protocolo_conferencia/index_result',$data);
	}

	public function salvar_conferencia()
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		$cd_documento_protocolo_conf_gerencia_item = $this->input->post('cd_documento_protocolo_conf_gerencia_item', TRUE);
		$fl_status 								   = $this->input->post('fl_status', TRUE);

		$this->documento_protocolo_conf_gerencia_model->salvar_conferencia($cd_documento_protocolo_conf_gerencia_item, $fl_status, $this->session->userdata('codigo'));

		$row = $this->documento_protocolo_conf_gerencia_model->carrega_doc($cd_documento_protocolo_conf_gerencia_item);

		$data['dt_conferencia'] = $row['dt_conferencia']; 

		echo json_encode($data);
	}

	public function ajuste($cd_documento_protocolo_conf_gerencia_item)
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		$data['row'] = $this->documento_protocolo_conf_gerencia_model->carrega_doc($cd_documento_protocolo_conf_gerencia_item);

		$this->load->view('atividade/documento_protocolo_conferencia/ajuste',$data);
	}

	public function salvar_ajuste()
	{
		$this->load->model('projetos/documento_protocolo_conf_gerencia_model');

		$cd_documento_protocolo_conf_gerencia_item = $this->input->post('cd_documento_protocolo_conf_gerencia_item', TRUE);

		$args = array(
			'ds_ajuste'  		 => $this->input->post('ds_ajuste', TRUE),
			'fl_status'  		 => 'A',
			'cd_usuario' 		 => $this->session->userdata('codigo')
		);

		$this->documento_protocolo_conf_gerencia_model->salvar_ajuste($cd_documento_protocolo_conf_gerencia_item, $args);

		$acompanhamento = array(
			'cd_documento_protocolo_conf_gerencia_item' => $cd_documento_protocolo_conf_gerencia_item,
			'ds_acompanhamento'  						=> 'Solicitação de ajuste : '.$this->input->post('ds_ajuste', TRUE),
			'fl_acompanhamento' 						=> 'S',
			'tp_acompanhamento'                         => 'S',
			'cd_usuario' 		 						=> $this->session->userdata('codigo')
		);

		$this->documento_protocolo_conf_gerencia_model->salvar_acompanhamento($acompanhamento);

		$this->envia_email_ajuste($cd_documento_protocolo_conf_gerencia_item);

		redirect('atividade/documento_protocolo_conferencia', 'refresh');
	}

	private function envia_email_ajuste($cd_documento_protocolo_conf_gerencia_item)
	{
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 380;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = '[LINK]';

        $subs = site_url('ecrm/documento_protocolo_conf_gerencia/acompanhamento/'.intval($cd_documento_protocolo_conf_gerencia_item));

        $texto   = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Conferência de Documento',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}
}