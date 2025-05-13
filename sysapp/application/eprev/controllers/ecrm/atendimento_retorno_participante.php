<?php
class Atendimento_retorno_participante extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	public function index()
	{
		$data['drop'] = array(
			array('value' => 'S', 'text' => 'Sim'),
			array('value' => 'N', 'text' => 'Não')
		);

		$this->load->view('ecrm/atendimento_retorno_participante/index', $data);
	}

	public function listar()
	{
		$this->load->model('projetos/atendimento_retorno_participante_model');

		$args = array(
			'cd_usuario' 			=> $this->session->userdata('codigo'),
			'cd_empresa' 			=> $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia' 		=> $this->input->post('seq_dependencia', TRUE),
			'nome' 					=> $this->input->post('nome', TRUE),
			'dt_retorno_ini' 		=> $this->input->post('dt_retorno_ini', TRUE),
			'dt_retorno_fim' 		=> $this->input->post('dt_retorno_fim', TRUE),
			'fl_retorno' 			=> $this->input->post('fl_retorno', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->atendimento_retorno_participante_model->listar($args);

		$this->load->view('ecrm/atendimento_retorno_participante/index_result', $data);
	}

	public function cadastro($cd_atendimento_retorno_participante)
	{
		$this->load->model('projetos/atendimento_retorno_participante_model');

		$data['row'] = $this->atendimento_retorno_participante_model->carrega($cd_atendimento_retorno_participante);

		$this->load->view('ecrm/atendimento_retorno_participante/cadastro', $data);
	}

	public function salvar()
	{
		$this->load->model('projetos/atendimento_retorno_participante_model');

		$cd_atendimento_retorno_participante = $this->input->post('cd_atendimento_retorno_participante', TRUE);

		$args = array(
			'dt_retorno'    => $this->input->post('dt_retorno', TRUE),
			'ds_observacao' => $this->input->post('ds_observacao', TRUE),
			'cd_usuario'    => $this->session->userdata('codigo')
		);

		$this->atendimento_retorno_participante_model->salvar($cd_atendimento_retorno_participante, $args);

		redirect('ecrm/atendimento_retorno_participante/cadastro/'.intval($cd_atendimento_retorno_participante), 'refresh');
	}
}