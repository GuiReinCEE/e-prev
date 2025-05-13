<?php
class Rh_cargo extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao()
	{
		if($this->session->userdata('indic_09') == '*')
		{
			return TRUE;
		}
		else if($this->session->userdata('indic_05') == 'S')
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
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/cargo_model');

			$data = array(
				'grupo_ocupacional' => $this->cargo_model->get_grupo_ocupacional(),
				'formacao' 			=> $this->cargo_model->get_formacao()
			);

			$this->load->view('cadastro/rh_cargo/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar()
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/cargo_model');

			$args = array(
				'cd_grupo_ocupacional' => $this->input->post('cd_grupo_ocupacional', TRUE),
				'cd_formacao' 		   => $this->input->post('cd_formacao', TRUE)
			);

			manter_filtros($args);

			$data['collection'] = $this->cargo_model->listar($args);

			$this->load->view('cadastro/rh_cargo/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_cargo = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/cargo_model');

			$data = array(
				'grupo_ocupacional' => $this->cargo_model->get_grupo_ocupacional(),
				'formacao' 			=> $this->cargo_model->get_formacao()
			);

			if(intval($cd_cargo) == 0)
			{
				$data['row'] = array(
					'cd_cargo' 	               => intval($cd_cargo),
					'ds_cargo'         	       => '',
					'cd_grupo_ocupacional'     => '',
					'cd_formacao'              => '',
					'ds_conhecimento_generico' => ''
				);
			}
			else
			{
				$data['row'] = $this->cargo_model->carrega($cd_cargo);
			}

			$this->load->view('cadastro/rh_cargo/cadastro', $data);
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
			$this->load->model('rh_avaliacao/cargo_model');

			$cd_cargo = $this->input->post('cd_cargo', TRUE);

			$args = array(
				'ds_cargo' 	               => $this->input->post('ds_cargo', TRUE),
				'cd_grupo_ocupacional'     => $this->input->post('cd_grupo_ocupacional', TRUE),
				'cd_formacao'              => $this->input->post('cd_formacao', TRUE),
				'ds_conhecimento_generico' => $this->input->post('ds_conhecimento_generico', TRUE),
				'cd_usuario'               => $this->session->userdata('codigo')
			);

			if(intval($cd_cargo) == 0)
			{
				$this->cargo_model->salvar($args);
			}
			else
			{
				$this->cargo_model->atualizar($cd_cargo, $args);
			}

			redirect('cadastro/rh_cargo', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}