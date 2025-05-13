<?php
class Rh_cargo_area_atuacao extends Controller
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
			$this->load->model('rh_avaliacao/cargo_area_atuacao_model');

			$data = array(
				'gerencia' 			=> $this->cargo_area_atuacao_model->get_gerencia(),
				'grupo_ocupacional' => $this->cargo_area_atuacao_model->get_grupo_ocupacional(),
				'cargo' 			=> $this->cargo_area_atuacao_model->get_cargo(),
				'area_atuacao' 		=> $this->cargo_area_atuacao_model->get_area_atuacao()
			);

			$this->load->view('cadastro/rh_cargo_area_atuacao/index', $data);
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
			$this->load->model('rh_avaliacao/cargo_area_atuacao_model');

			$args = array(
			    'cd_gerencia' 		   => $this->input->post('cd_gerencia', TRUE),
			    'cd_cargo' 			   => $this->input->post('cd_cargo', TRUE),
			    'cd_area_atuacao' 	   => $this->input->post('cd_area_atuacao', TRUE),
			    'cd_grupo_ocupacional' => $this->input->post('cd_grupo_ocupacional', TRUE)
			);

			manter_filtros($args);

			$data['collection'] = $this->cargo_area_atuacao_model->listar($args);

			$this->load->view('cadastro/rh_cargo_area_atuacao/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_cargo_area_atuacao = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/cargo_area_atuacao_model');

			$data = array(
				'gerencia' => $this->cargo_area_atuacao_model->get_gerencia(),
				'cargo'    => $this->cargo_area_atuacao_model->get_cargo()
			);

			if(intval($cd_cargo_area_atuacao) == 0)
			{
				$data['row'] = array(
					'cd_cargo_area_atuacao'      => intval($cd_cargo_area_atuacao),
				    'cd_gerencia' 	             => '',
				    'cd_cargo' 		             => '',
				    'cd_area_atuacao'            => '',
					'ds_conhecimento_especifico' => ''
				);
			}
			else
			{
				$data['row'] = $this->cargo_area_atuacao_model->carrega($cd_cargo_area_atuacao);
			}

			$this->load->view('cadastro/rh_cargo_area_atuacao/cadastro', $data);
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
			$this->load->model('rh_avaliacao/cargo_area_atuacao_model');

			$cd_cargo_area_atuacao = $this->input->post('cd_cargo_area_atuacao', TRUE);

			$args = array(
			    'cd_gerencia'            	  => $this->input->post('cd_gerencia', TRUE),
			    'cd_cargo' 		              => $this->input->post('cd_cargo', TRUE),
			    'cd_area_atuacao'             => $this->input->post('cd_area_atuacao', TRUE),
			    'ds_conhecimento_especifico'  => $this->input->post('ds_conhecimento_especifico', TRUE),
			    'cd_usuario' 	              => $this->session->userdata('codigo')	
			);

			if(intval($cd_cargo_area_atuacao) == 0)
			{
				$this->cargo_area_atuacao_model->salvar($args);
			}
			else
			{
				$this->cargo_area_atuacao_model->atualizar($cd_cargo_area_atuacao, $args);
			}

			redirect('cadastro/rh_cargo_area_atuacao', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}