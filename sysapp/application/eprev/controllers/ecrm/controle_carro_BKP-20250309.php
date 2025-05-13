<?php
class Controle_carro extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GCM', 'GFC')))
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
			$this->load->model('projetos/controle_carro_model');

			$data = array(
				'destino'   => $this->controle_carro_model->get_destino(),
				'motivo'    => $this->controle_carro_model->get_motivo(),
				'motorista' => $this->controle_carro_model->get_motorista()
			);
								
			$this->load->view('ecrm/controle_carro/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {		
    	$this->load->model('projetos/controle_carro_model');

    	$args = array(
			'dt_saida_ini'                => $this->input->post('dt_saida_ini', TRUE),
			'dt_saida_fim'                => $this->input->post('dt_saida_fim', TRUE),
			'dt_retorno_ini'              => $this->input->post('dt_retorno_ini', TRUE),
			'dt_retorno_fim'              => $this->input->post('dt_retorno_fim', TRUE),
			'cd_controle_carro_destino'   => $this->input->post('cd_controle_carro_destino', TRUE),
			'cd_controle_carro_motivo'    => $this->input->post('cd_controle_carro_motivo', TRUE),
			'cd_controle_carro_motorista' => $this->input->post('cd_controle_carro_motorista', TRUE)
    	);

		manter_filtros($args);

		$data['collection'] = $this->controle_carro_model->listar($this->session->userdata('divisao'), $args);

		$this->load->view('ecrm/controle_carro/index_result', $data);
	}

	public function cadastro($cd_controle_carro = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/controle_carro_model');

			$data['veiculo'] = $this->controle_carro_model->get_veiculo();

			if(intval($cd_controle_carro) == 0)
			{
				if($this->session->userdata('divisao') == 'GCM')
				{
					$cd_controle_carro_veiculo = 1;
				}
				else if($this->session->userdata('divisao') == 'GFC')
				{
					$cd_controle_carro_veiculo = 4;
				}
				else
				{
					$cd_controle_carro_veiculo = '';
				}

				$data['row'] = array(
					'cd_controle_carro'           => intval($cd_controle_carro),
					'cd_controle_carro_veiculo'   => $cd_controle_carro_veiculo,
					'nr_km_saida'                 => '',
					'dt_saida'                    => '',
					'hr_saida'                    => '',
					'cd_controle_carro_destino'   => '',
					'cd_controle_carro_motivo'    => '',
					'nr_km_retorno'               => '',
					'dt_retorno'                  => '',
					'hr_retorno'                  => '',
					'cd_controle_carro_motorista' => '',
					'ds_observacao'               => ''
				);
			}
			else
			{	
				$data['row'] = $this->controle_carro_model->carrega(intval($cd_controle_carro));
			}
			
			$this->load->view('ecrm/controle_carro/cadastro', $data);
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
			$this->load->model('projetos/controle_carro_model');

			$cd_controle_carro = $this->input->post('cd_controle_carro', TRUE);

			$args = array(
				'nr_km_saida'                 => $this->input->post('nr_km_saida', TRUE),
				'dt_saida'                    => $this->input->post('dt_saida', TRUE).' '.$this->input->post('hr_saida', TRUE),
				'cd_controle_carro_destino'   => $this->input->post('cd_controle_carro_destino', TRUE),
				'cd_controle_carro_motivo'    => $this->input->post('cd_controle_carro_motivo', TRUE),
				'nr_km_retorno'               => $this->input->post('nr_km_retorno', TRUE),
				'dt_retorno'                  => $this->input->post('dt_retorno', TRUE).' '.$this->input->post('hr_retorno', TRUE),
				'cd_controle_carro_motorista' => $this->input->post('cd_controle_carro_motorista', TRUE),
				'cd_controle_carro_veiculo'   => $this->input->post('cd_controle_carro_veiculo', TRUE),
				'ds_observacao'               => $this->input->post('ds_observacao', TRUE),
				'cd_gerencia'                 => $this->session->userdata('divisao'),
				'cd_usuario'                  => $this->session->userdata('codigo')
			);

			if(intval($cd_controle_carro) == 0)
			{
				$cd_controle_carro = $this->controle_carro_model->salvar($args);
			}
			else
			{
				$this->controle_carro_model->atualizar($cd_controle_carro, $args);
			}

			redirect('ecrm/controle_carro/cadastro/'.$cd_controle_carro, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function excluir($cd_controle_carro)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/controle_carro_model');
			
			$this->controle_carro_model->excluir($cd_controle_carro, $this->session->userdata('codigo'));
			
			redirect('ecrm/controle_carro', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function abastecimento($cd_controle_carro, $cd_controle_carro_abastecimento = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/controle_carro_model');

			$data = array(
				'row'        => $this->controle_carro_model->carrega($cd_controle_carro),
				'collection' => $this->controle_carro_model->abastecimento_listar($cd_controle_carro)
			);

			if(intval($cd_controle_carro_abastecimento) == 0)
			{
				$data['abastecimento'] = array(
					'cd_controle_carro_abastecimento' => intval($cd_controle_carro_abastecimento),
					'cd_controle_carro'               => intval($cd_controle_carro),
					'nr_km'                           => '',
					'nr_valor'                        => '',
					'nr_litro'                        => '',
					'dt_abastecimento'                => '',
					'hr_abastecimento'                => ''
				);
			}
			else
			{
				$data['abastecimento'] = $this->controle_carro_model->abastecimento($cd_controle_carro_abastecimento);
			}

			$this->load->view('ecrm/controle_carro/abastecimento', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function abastecimento_salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/controle_carro_model');
			
			$cd_controle_carro_abastecimento = $this->input->post('cd_controle_carro_abastecimento', TRUE);

			$args = array(
				'cd_controle_carro' => $this->input->post('cd_controle_carro', TRUE),
				'nr_km'             => $this->input->post('nr_km', TRUE),
				'nr_valor'          => $this->input->post('nr_valor', TRUE),
				'nr_litro'          => $this->input->post('nr_litro', TRUE),
				'dt_abastecimento'  => $this->input->post('dt_abastecimento', TRUE).' '.$this->input->post('hr_abastecimento', TRUE),
				'cd_usuario'        => $this->session->userdata('codigo')
			);

			if(intval($cd_controle_carro_abastecimento) == 0)
			{
				$this->controle_carro_model->abastecimento_salvar($args);
			}
			else
			{
				$this->controle_carro_model->abastecimento_atualizar(intval($cd_controle_carro_abastecimento), $args);
			}
			
			redirect('ecrm/controle_carro/abastecimento/'.$args['cd_controle_carro'] , 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function abastecimento_excluir($cd_controle_carro, $cd_controle_carro_abastecimento)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/controle_carro_model');
			
			$this->controle_carro_model->abastecimento_excluir($cd_controle_carro_abastecimento, $this->session->userdata('codigo'));
			
			redirect('ecrm/controle_carro/abastecimento/'.$cd_controle_carro, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}