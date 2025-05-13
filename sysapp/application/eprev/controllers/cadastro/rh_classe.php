<?php
class Rh_classe extends Controller
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

	private function get_padrao()
	{
		return array(
			array('value' => 'A', 'text' => 'A'),
			array('value' => 'B', 'text' => 'B'),
			array('value' => 'C', 'text' => 'C'),
			array('value' => 'D', 'text' => 'D'),
			array('value' => 'E', 'text' => 'E'),
			array('value' => 'F', 'text' => 'F'),
			array('value' => 'G', 'text' => 'G'),
			array('value' => 'H', 'text' => 'H'),
			array('value' => 'I', 'text' => 'I'),
			array('value' => 'J', 'text' => 'J'),
			array('value' => 'K', 'text' => 'K'),
			array('value' => 'L', 'text' => 'L'),
			array('value' => 'M', 'text' => 'M'),
			array('value' => 'N', 'text' => 'N'),
			array('value' => 'O', 'text' => 'O'),
			array('value' => 'P', 'text' => 'P'),
			array('value' => 'Q', 'text' => 'Q'),
			array('value' => 'R', 'text' => 'R'),
			array('value' => 'S', 'text' => 'S')
		);
	}

	public function index()
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/classe_model');

			$data['cargo'] = $this->classe_model->get_cargo();

			$this->load->view('cadastro/rh_classe/index', $data);
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
			$this->load->model('rh_avaliacao/classe_model');

			$args = array(
				'cd_cargo' => $this->input->post('cd_cargo', TRUE)
			);

			manter_filtros($args);

			$data['collection'] = $this->classe_model->listar($args);

			foreach ($data['collection'] as $key => $item) 
            {
                $data['collection'][$key]['padrao'] = array();

                foreach ($this->classe_model->listar_padrao($item['cd_classe']) as $key2 => $item2)
                {
                    $data['collection'][$key]['padrao'][] = $item2['ds_padrao'];
                }       
            }

			$this->load->view('cadastro/rh_classe/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_classe = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/classe_model');

			$data = array(
				'cargo'  => $this->classe_model->get_cargo(),
				'padrao' => $this->get_padrao()
			);

			if(intval($cd_classe) == 0)
			{
				$data['row'] = array(
					'cd_classe' => intval($cd_classe),
					'ds_classe' => '',
					'cd_cargo'  => ''
				);

				$data['classe_padrao'] = array();
			}
			else
			{
				$data['row'] = $this->classe_model->carrega($cd_classe);

				$data['classe_padrao'] = array();

				foreach ($this->classe_model->listar_padrao($cd_classe) as $key => $item)
                {
                    $data['classe_padrao'][] = $item['ds_padrao'];
                }
			}

			$this->load->view('cadastro/rh_classe/cadastro', $data);
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
			$this->load->model('rh_avaliacao/classe_model');

			$cd_classe = $this->input->post('cd_classe', TRUE);

			$args = array(
				'ds_classe'  => $this->input->post('ds_classe', TRUE),
				'cd_cargo'   => $this->input->post('cd_cargo', TRUE),
				'padrao'     => (is_array($this->input->post('padrao', TRUE)) ? $this->input->post('padrao', TRUE) : array()),
				'cd_usuario' => $this->session->userdata('codigo')
			);

			if(intval($cd_classe) == 0)
			{
				$this->classe_model->salvar($args);
			}
			else
			{
				$this->classe_model->atualizar($cd_classe, $args);
			}

			redirect('cadastro/rh_classe', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}