<?php
class Rh_area_atuacao extends Controller
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

			$this->load->view('cadastro/rh_area_atuacao/index');
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
			$this->load->model('rh_avaliacao/area_atuacao_model');

			$args = array();

			manter_filtros($args);

			$data['collection'] = $this->area_atuacao_model->listar($args);

			$this->load->view('cadastro/rh_area_atuacao/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_area_atuacao = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/area_atuacao_model');

			if(intval($cd_area_atuacao) == 0)
			{
				$data['row'] = array(
					'cd_area_atuacao' => intval($cd_area_atuacao),
					'ds_area_atuacao' => ''
				);
			}
			else
			{
				$data['row'] = $this->area_atuacao_model->carrega($cd_area_atuacao);
			}

			$this->load->view('cadastro/rh_area_atuacao/cadastro', $data);
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
			$this->load->model('rh_avaliacao/area_atuacao_model');

			$cd_area_atuacao = $this->input->post('cd_area_atuacao', TRUE);

			$args = array(
				'ds_area_atuacao'  => $this->input->post('ds_area_atuacao', TRUE),
				'cd_usuario' 	   => $this->session->userdata('codigo')
			);

			if(intval($cd_area_atuacao) == 0)
			{
				$this->area_atuacao_model->salvar($args);
			}
			else
			{
				$this->area_atuacao_model->atualizar($cd_area_atuacao, $args);
			}

			redirect('cadastro/rh_area_atuacao', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}