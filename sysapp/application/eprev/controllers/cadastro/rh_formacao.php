<?php
class Rh_formacao extends Controller
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
			$this->load->view('cadastro/rh_formacao/index');
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
			$this->load->model('rh_avaliacao/formacao_model');

			$args = array();

			manter_filtros($args);

			$data['collection'] = $this->formacao_model->listar($args);

			$this->load->view('cadastro/rh_formacao/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_formacao = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/formacao_model');

			$data['drop_nivel'] = array(
				array('value' => 'M', 'text' => 'Médio'),
				array('value' => 'S', 'text' => 'Superior')
			);

			if(intval($cd_formacao) == 0)
			{
				$data['row'] = array(
					'cd_formacao' => intval($cd_formacao),
					'ds_formacao' => ''
				);
			}
			else
			{
				$data['row'] = $this->formacao_model->carrega($cd_formacao);
			}

			$this->load->view('cadastro/rh_formacao/cadastro', $data);
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
			$this->load->model('rh_avaliacao/formacao_model');

			$cd_formacao = $this->input->post('cd_formacao', TRUE);

			$args = array(
				'ds_formacao' => $this->input->post('ds_formacao', TRUE),
				'cd_usuario'  => $this->session->userdata('codigo'),
				'tp_nivel' 	  => $this->input->post('tp_nivel', TRUE)
			);

			if(intval($cd_formacao) == 0)
			{
				$this->formacao_model->salvar($args);
			}
			else
			{
				$this->formacao_model->atualizar($cd_formacao, $args);
			}

			redirect('cadastro/rh_formacao', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}