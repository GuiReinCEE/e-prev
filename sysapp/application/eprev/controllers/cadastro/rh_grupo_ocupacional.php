<?php
class Rh_grupo_ocupacional extends Controller
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
			$this->load->view('cadastro/rh_grupo_ocupacional/index');
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
			$this->load->model('rh_avaliacao/grupo_ocupacional_model');

			$args = array();

			manter_filtros($args);

			$data['collection'] = $this->grupo_ocupacional_model->listar($args);

			$this->load->view('cadastro/rh_grupo_ocupacional/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_grupo_ocupacional = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/grupo_ocupacional_model');

			if(intval($cd_grupo_ocupacional) == 0)
			{
				$data['row'] = array(
					'cd_grupo_ocupacional' => intval($cd_grupo_ocupacional),
					'ds_grupo_ocupacional' => ''
				);
			}
			else
			{
				$data['row'] = $this->grupo_ocupacional_model->carrega($cd_grupo_ocupacional);
			}

			$this->load->view('cadastro/rh_grupo_ocupacional/cadastro', $data);
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
			$this->load->model('rh_avaliacao/grupo_ocupacional_model');

			$cd_grupo_ocupacional = $this->input->post('cd_grupo_ocupacional', TRUE);

			$args = array(
				'ds_grupo_ocupacional' => $this->input->post('ds_grupo_ocupacional', TRUE),
				'cd_usuario' 	       => $this->session->userdata('codigo')
			);

			if(intval($cd_grupo_ocupacional) == 0)
			{
				$this->grupo_ocupacional_model->salvar($args);
			}
			else
			{
				$this->grupo_ocupacional_model->atualizar($cd_grupo_ocupacional, $args);
			}

			redirect('cadastro/rh_grupo_ocupacional', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}