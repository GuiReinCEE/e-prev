<?php
class Pauta_sg_integrante extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

    private function get_colegiado()
    {
    	return array(
    		array('value' => 'DE', 'text' => 'Diretoria Executiva'),
    		array('value' => 'CF', 'text' => 'Conselho Fiscal'),
    		array('value' => 'CD', 'text' => 'Conselho Deliberativo')
    	);
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
		if($this->get_permissao())
		{
			$data['colegiado'] = $this->get_colegiado();
			$data['removido']  = array(
				array('value' => 'S', 'text' => 'Sim'),
				array('value' => 'N', 'text' => 'Não')
			);

			$this->load->view('gestao/pauta_sg_integrante/index', $data);
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
			$this->load->model('gestao/pauta_sg_integrante_model');

			$args = array(
				'fl_colegiado' => $this->input->post('fl_colegiado', TRUE),
				'fl_removido'  => $this->input->post('fl_removido', TRUE)
			);

			manter_filtros($args);

			$data['collection'] = $this->pauta_sg_integrante_model->listar($args);

			$this->load->view('gestao/pauta_sg_integrante/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cadastro($cd_pauta_sg_integrante = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_integrante_model');

			$data['colegiado'] = $this->get_colegiado();
			$data['titulares'] = array();

			$data['drop'] 	   = array(
				array('value' => 'S', 'text' => 'Sim'),
				array('value' => 'N', 'text' => 'Não')
			);

			$data['drop_tipo'] = array(
				array('value' => 'T', 'text' => 'Titular'),
				array('value' => 'S', 'text' => 'Suplente')
			);

			$data['drop_indicado_eleito'] = array(
				array('value' => 'I', 'text' => 'Indicado'),
				array('value' => 'E', 'text' => 'Eleito')
			);

			if(intval($cd_pauta_sg_integrante) == 0)
			{
				$data['row'] = array(
					'cd_pauta_sg_integrante' 		 => '',
					'fl_colegiado' 			 		 => '',
					'fl_presidente' 		 		 => '',
					'fl_secretaria' 		 		 => '',
					'fl_tipo'				 		 => '',
					'cd_pauta_sg_integrante_titular' => '',
					'ds_pauta_sg_integrante' 		 => '',
					'fl_indicado_eleito'             => '',
					'email'                          => '',
					'celular'                       => '',
					'cargo'                          => ''
				);
			}
			else
			{
				$data['row']       = $this->pauta_sg_integrante_model->carrega($cd_pauta_sg_integrante);
				$data['titulares'] = $this->pauta_sg_integrante_model->get_titulares($data['row']['fl_colegiado'], $data['row']['cd_pauta_sg_integrante_titular']);
			}

			$this->load->view('gestao/pauta_sg_integrante/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    public function set_titulares()
    {
        $this->load->model('gestao/pauta_sg_integrante_model');

        $fl_colegiado = $this->input->post('fl_colegiado', TRUE);

        $titulares = $this->pauta_sg_integrante_model->get_titulares($fl_colegiado);

        $row = array();
        
		foreach ($titulares as $item)
		{
			$row[] = array_map('arrayToUTF8', $item);		
		}

        echo json_encode($row);
    }

	public function salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_integrante_model');

			$cd_pauta_sg_integrante = $this->input->post('cd_pauta_sg_integrante', TRUE);

			$args = array(
				'fl_colegiado' 			 		 => $this->input->post('fl_colegiado', TRUE),
				'fl_presidente' 		 		 => $this->input->post('fl_presidente', TRUE),
				'fl_secretaria' 		 		 => $this->input->post('fl_secretaria', TRUE),
				'fl_indicado_eleito' 		 	 => $this->input->post('fl_indicado_eleito', TRUE),
				'ds_pauta_sg_integrante' 		 => $this->input->post('ds_pauta_sg_integrante', TRUE),
				'fl_tipo' 						 => $this->input->post('fl_tipo', TRUE),
				'email' 						 => $this->input->post('email', TRUE),
				'celular' 						 => $this->input->post('celular', TRUE),
				'cargo' 						 => $this->input->post('cargo', TRUE),
				'cd_pauta_sg_integrante_titular' => intval($this->input->post('cd_pauta_sg_integrante_titular', TRUE)),
				'cd_usuario'			 		 => $this->session->userdata('codigo')
			);

			if(intval($cd_pauta_sg_integrante) == 0)
			{
				$this->pauta_sg_integrante_model->salvar($args);
			}
			else
			{
				$this->pauta_sg_integrante_model->atualizar($cd_pauta_sg_integrante, $args);
			}

			redirect('gestao/pauta_sg_integrante', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function remover($cd_pauta_sg_integrante)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_integrante_model');

			$this->pauta_sg_integrante_model->remover($cd_pauta_sg_integrante, $this->session->userdata('codigo'));

			redirect('gestao/pauta_sg_integrante', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function ativar($cd_pauta_sg_integrante)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_integrante_model');

			$this->pauta_sg_integrante_model->ativar($cd_pauta_sg_integrante);

			redirect('gestao/pauta_sg_integrante', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}