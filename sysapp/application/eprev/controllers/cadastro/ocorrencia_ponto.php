<?php
class Ocorrencia_ponto extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	public function index($cd_usuario, $cd_ocorrencia_ponto = 0)
	{
		$this->load->model(array('rh_avaliacao/ocorrencia_ponto_model', 'rh_avaliacao/rh_model'));

		if(intval($cd_ocorrencia_ponto) == 0)
		{
			$data['row'] = array(
				'cd_ocorrencia_ponto' 	   => 0,
			   	'dt_referencia' 		   => '',
			   	'cd_ocorrencia_ponto_tipo' => '',
			   	'nr_quantidade' 		   => ''
			);
		}
		else
		{
			$data['row'] = $this->ocorrencia_ponto_model->carrega($cd_ocorrencia_ponto);
		}

		$data['usuario'] 	= $this->rh_model->carrega($cd_usuario);
		$data['collection'] = $this->ocorrencia_ponto_model->listar_ocorrencia($cd_usuario);

		$this->load->view('cadastro/ocorrencia_ponto/index', $data);
	}

	public function salvar()
	{
		$this->load->model('rh_avaliacao/ocorrencia_ponto_model');

		$cd_ocorrencia_ponto = $this->input->post('cd_ocorrencia_ponto', TRUE);

		$args = array(
		   'cd_usuario_cadastro'	  => $this->input->post('cd_usuario', TRUE),
		   'dt_referencia' 			  => $this->input->post('dt_referencia', TRUE),
		   'cd_ocorrencia_ponto_tipo' => $this->input->post('cd_ocorrencia_ponto_tipo', TRUE),
		   'nr_quantidade' 			  => $this->input->post('nr_quantidade', TRUE),
		   'cd_usuario' 			  => $this->session->userdata('codigo')
		);

		if(intval($cd_ocorrencia_ponto) == 0)
		{
			$this->ocorrencia_ponto_model->salvar($args);
		}
		else
		{
			$this->ocorrencia_ponto_model->atualizar($cd_ocorrencia_ponto, $args);
		}

		redirect('cadastro/ocorrencia_ponto/index/'.$args['cd_usuario_cadastro'], 'refresh');
	}
}