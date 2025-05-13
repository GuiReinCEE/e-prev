<?php
class Reuniao_sistema_gestao extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function permissao()
    {
    	#COMITÊ DE QUALIDAE
    	if($this->session->userdata('indic_12') == '*')
    	{
    		return true;
    	}
    	#ADMINISTRADORES DO E-PREV
    	else if($this->session->userdata('indic_05') == 'S')
    	{
    		return true;
    	}
    	#CARLA GOMES DA SILVA
    	else if($this->session->userdata('codigo') == 352)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    public function index()
    {
    	$this->load->model('gestao/reuniao_sistema_gestao_model');

		$data  = array();

		$data['tipo'] = $this->reuniao_sistema_gestao_model->get_tipo_reuniao();
							
		$this->load->view('gestao/reuniao_sistema_gestao/index', $data);
    }

    public function listar()
    {		
    	$this->load->model('gestao/reuniao_sistema_gestao_model');

		$args = array();
		$data = array();
				
		$args['dt_ini']                         = $this->input->post('dt_ini', TRUE);
		$args['dt_fim']                         = $this->input->post('dt_fim', TRUE);
		$args['cd_reuniao_sistema_gestao_tipo'] = $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE);
		
		manter_filtros($args);
		
		$data['collection'] = $this->reuniao_sistema_gestao_model->listar($args);

		foreach($data['collection'] as $key => $item)
		{
			$processo = $this->reuniao_sistema_gestao_model->get_processo_checked($item['cd_reuniao_sistema_gestao']);
				
			$data['collection'][$key]['processo'] = array();

			foreach($processo as $item2)
			{				
				$data['collection'][$key]['processo'][] = $item2['processo'];
			}		
		}
		
		$this->load->view('gestao/reuniao_sistema_gestao/index_result', $data);
    }

    public function cadastro($cd_reuniao_sistema_gestao = 0)
	{
		if($this->permissao())
		{
			$this->load->model('gestao/reuniao_sistema_gestao_model');

			$data = array();

			$data['tipo'] = $this->reuniao_sistema_gestao_model->get_tipo_reuniao();
			
			$data['processo_checked'] = array();
			$data['indicador_checked']  = array();

			if(intval($cd_reuniao_sistema_gestao) == 0)
			{
				$data['processo'] = $this->reuniao_sistema_gestao_model->get_processo();

				$data['row'] = array(
					'cd_reuniao_sistema_gestao'      => intval($cd_reuniao_sistema_gestao),
					'dt_reuniao_sistema_gestao'      => '',
					'cd_reuniao_sistema_gestao_tipo' => '',
					'arquivo'                        => '',
					'arquivo_nome'                   => '',
					'dt_encerramento'                => ''
				);
			}
			else
			{
				
				$processo = $this->reuniao_sistema_gestao_model->get_processo_checked($cd_reuniao_sistema_gestao);
				
				foreach($processo as $item)
				{				
					$data['processo_checked'][] = $item['processo'];
			
					$indicador_check = $this->reuniao_sistema_gestao_model->get_indicador_checked(intval($item['cd_processo']), $cd_reuniao_sistema_gestao);

					foreach ($indicador_check as $key2 => $indicador_check_item) 
					{
						$data['indicador_checked'][] = $indicador_check_item['ds_indicador'];
					}
				}	

				$data['row'] = $this->reuniao_sistema_gestao_model->carrega($cd_reuniao_sistema_gestao);
			}
			
			$this->load->view('gestao/reuniao_sistema_gestao/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');

			$args = array();
			
			$cd_reuniao_sistema_gestao = $this->input->post('cd_reuniao_sistema_gestao', TRUE);

            $args['dt_reuniao_sistema_gestao']      = $this->input->post('dt_reuniao_sistema_gestao', TRUE);
            $args['cd_reuniao_sistema_gestao_tipo'] = $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE);
            $args['arquivo_nome']                   = $this->input->post('arquivo_nome', TRUE);
            $args['arquivo']                        = $this->input->post('arquivo', TRUE);
			$args['cd_usuario']                     = $this->session->userdata('codigo');
			
			$processo_checked = $this->input->post('processo_checked', TRUE);

			$fl_tipo = false;
			
			$args['indicador_igp'] = array();			
			
			if(intval($cd_reuniao_sistema_gestao) == 0)
			{
				if(!is_array($processo_checked))
				{
					$args['processo_checked'] = array();
				}
				else
				{
					$args['processo_checked'] = $processo_checked;
				}

				$cd_reuniao_sistema_gestao = $this->reuniao_sistema_gestao_model->salvar($args, $fl_tipo);
	
				$this->atualiza_indicador($cd_reuniao_sistema_gestao);
			}
			else
			{
				$this->reuniao_sistema_gestao_model->atualizar($cd_reuniao_sistema_gestao, $args);
			}
			
			redirect('gestao/reuniao_sistema_gestao/cadastro/'.$cd_reuniao_sistema_gestao);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_reuniao_sistema_gestao)
	{
		if ($this->permissao())
        {
			$this->load->model('gestao/reuniao_sistema_gestao_model');

			$this->reuniao_sistema_gestao_model->excluir(intval($cd_reuniao_sistema_gestao), $this->session->userdata('codigo'));
			
			redirect('gestao/reuniao_sistema_gestao', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function anexo($cd_reuniao_sistema_gestao)
	{
		if ($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');

			$data = array();

			$data['row'] = $this->reuniao_sistema_gestao_model->carrega(intval($cd_reuniao_sistema_gestao));

			$this->load->view('gestao/reuniao_sistema_gestao/anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function anexo_listar($cd_reuniao_sistema_gestao)
	{
		$this->load->model('gestao/reuniao_sistema_gestao_model');

		$data = array();

		$data['collection'] = $this->reuniao_sistema_gestao_model->anexo_listar(intval($cd_reuniao_sistema_gestao));
		
		$this->load->view('gestao/reuniao_sistema_gestao/anexo_result', $data);
	}

	public function anexo_salvar()
	{
		if($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');
			
			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
			
			$cd_reuniao_sistema_gestao = $this->input->post('cd_reuniao_sistema_gestao', TRUE);

			if($qt_arquivo > 0)
			{
				$nr_conta = 0;

				while($nr_conta < $qt_arquivo)
				{
					$args = array();		
					
					$args['arquivo_nome']              = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
					$args['arquivo']                   = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
					$args['cd_usuario']                = $this->session->userdata('codigo');
					
					$this->reuniao_sistema_gestao_model->anexo_salvar(intval($cd_reuniao_sistema_gestao), $args);
					
					$nr_conta++;
				}
			}

			redirect('gestao/reuniao_sistema_gestao/anexo/'.intval($cd_reuniao_sistema_gestao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function anexo_excluir($cd_reuniao_sistema_gestao, $cd_reuniao_sistema_gestao_anexo)
	{
		if ($this->permissao())
        {
			$this->load->model('gestao/reuniao_sistema_gestao_model');

			$this->reuniao_sistema_gestao_model->anexo_excluir(intval($cd_reuniao_sistema_gestao_anexo), $this->session->userdata('codigo'));
			
			redirect('gestao/reuniao_sistema_gestao/anexo/'.intval($cd_reuniao_sistema_gestao), 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function processo($cd_reuniao_sistema_gestao)
	{
		if ($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');

			$data = array();

			$data['row'] = $this->reuniao_sistema_gestao_model->carrega(intval($cd_reuniao_sistema_gestao));

			$data['processo'] = $this->reuniao_sistema_gestao_model->get_processo();

			$processo = $this->reuniao_sistema_gestao_model->get_processo_checked($cd_reuniao_sistema_gestao);
				
			$data['processo_checked'] = array();

			foreach($processo as $item)
			{				
				$data['processo_checked'][] = $item['cd_processo'];
			}	
		
			$this->load->view('gestao/reuniao_sistema_gestao/processo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function processo_salvar()
	{
		if($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');

        	$args = array();
			
			$cd_reuniao_sistema_gestao = $this->input->post('cd_reuniao_sistema_gestao', TRUE);

			$args['cd_usuario'] = $this->session->userdata('codigo');
			
			$processo_checked = $this->input->post('processo_checked', TRUE);

			if(!is_array($processo_checked))
			{
				$args['processo_checked'] = array();
			}
			else
			{
				$args['processo_checked'] = $processo_checked;
			}

			$this->reuniao_sistema_gestao_model->atualizar_processo($cd_reuniao_sistema_gestao, $args);

			$this->atualiza_indicador($cd_reuniao_sistema_gestao, false);

			redirect('gestao/reuniao_sistema_gestao/processo/'.$cd_reuniao_sistema_gestao);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function indicador($cd_reuniao_sistema_gestao)
	{
		if ($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');

			$data = array();

			$data['row'] = $this->reuniao_sistema_gestao_model->carrega(intval($cd_reuniao_sistema_gestao));

			$processo = $this->reuniao_sistema_gestao_model->get_processo_checked($cd_reuniao_sistema_gestao);

			$data['indicador']       = array();
			$data['indicador_check'] = array();

			foreach ($processo as $key => $processo_item) 
			{
				$indicador = $this->reuniao_sistema_gestao_model->get_indicador($processo_item['cd_processo']);

				$indicador_check = $this->reuniao_sistema_gestao_model->get_indicador_checked(intval($processo_item['cd_processo']), $cd_reuniao_sistema_gestao);

				foreach ($indicador as $key => $indicador_item) 
				{
					$data['indicador'][] = $indicador_item;
				}

				foreach ($indicador_check as $key2 => $indicador_check_item) 
				{
					$data['indicador_check'][] = $indicador_check_item['cd_indicador'];
				}
			}

			$this->load->view('gestao/reuniao_sistema_gestao/indicador', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function indicador_salvar()
	{
		if ($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');

        	$args = array();
			
			$cd_reuniao_sistema_gestao = $this->input->post('cd_reuniao_sistema_gestao', TRUE);

			$args['cd_usuario'] = $this->session->userdata('codigo');
			
			$indicador_checked = $this->input->post('indicador_checked', TRUE);

			if(!is_array($indicador_checked))
			{
				$args['indicador_checked'] = array();
			}
			else
			{
				$args['indicador_checked'] = $indicador_checked;
			}

			$this->reuniao_sistema_gestao_model->atualizar_indicador($cd_reuniao_sistema_gestao, $args);

			$this->atualiza_indicador($cd_reuniao_sistema_gestao);
			
			redirect('gestao/reuniao_sistema_gestao/indicador/'.$cd_reuniao_sistema_gestao);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function encerrar($cd_reuniao_sistema_gestao)
	{
		if ($this->permissao())
        {
			$this->load->model('gestao/reuniao_sistema_gestao_model');

			$this->reuniao_sistema_gestao_model->encerrar(intval($cd_reuniao_sistema_gestao), $this->session->userdata('codigo'));
			
			redirect('gestao/reuniao_sistema_gestao/cadastro/'.intval($cd_reuniao_sistema_gestao), 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function atualiza_indicador($cd_reuniao_sistema_gestao, $ir_cadastro = true)
	{
		if ($this->permissao())
        {
        	$this->load->model('gestao/reuniao_sistema_gestao_model');

        	$cd_usuario = $this->session->userdata('codigo');

        	$reuniao_sistema_gestao = $this->reuniao_sistema_gestao_model->carrega($cd_reuniao_sistema_gestao);

			$processo = $this->reuniao_sistema_gestao_model->get_processo_checked($cd_reuniao_sistema_gestao);
	
			foreach ($processo as $key => $processo_item) 
			{
				$indicador = $this->reuniao_sistema_gestao_model->get_indicador_checked(intval($processo_item['cd_processo']), $cd_reuniao_sistema_gestao);
			
				foreach ($indicador as $key2 => $indicador_item) 
				{					
					$indicador_tabela = $this->reuniao_sistema_gestao_model->indicador_tabela(intval($indicador_item['cd_indicador_tabela']));

					$indicador_tabela = array_map("arrayToUTF8", $indicador_tabela);		

					$parametro = $this->reuniao_sistema_gestao_model->indicador_parametro(intval($indicador_item['cd_indicador_tabela']));

					$indicador_tabela['parametro'] = array();

					foreach($parametro as $key3 => $parametro_item)
					{
						$indicador_tabela['parametro'][$parametro_item['nr_linha']][$parametro_item['nr_coluna']] = array_map("arrayToUTF8", $parametro_item);	
					}

					$row = $this->reuniao_sistema_gestao_model->get_reuniao_sistema_gestao_indicador_tabela(intval($indicador_item['cd_indicador']), intval($processo_item['cd_reuniao_sistema_gestao_processo']));

					if((count($row) > 0) AND ($row['cd_reuniao_sistema_gestao_indicador_tabela'] > 0))
					{
						$this->reuniao_sistema_gestao_model->atualizar_indicador_tabela($row['cd_reuniao_sistema_gestao_indicador_tabela'], $cd_usuario, json_encode($indicador_tabela));
					}
					else
					{
						$this->reuniao_sistema_gestao_model->salvar_indicador(intval($indicador_item['cd_indicador']), intval($processo_item['cd_reuniao_sistema_gestao_processo']), $cd_usuario, json_encode($indicador_tabela));
					}
				}
			}
		
			$this->reuniao_sistema_gestao_model->atualizar_apresentacao($cd_reuniao_sistema_gestao, $cd_usuario);

			if($ir_cadastro)
			{
				redirect('gestao/reuniao_sistema_gestao/cadastro/'.$cd_reuniao_sistema_gestao);
			}
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function cadastro_ordem($cd_reuniao_sistema_gestao)
	{
		if($this->permissao())
		{
			$this->load->model('gestao/reuniao_sistema_gestao_model');

			$data = array();

			$data['processo'] = $this->reuniao_sistema_gestao_model->get_processo_checked($cd_reuniao_sistema_gestao);

			$data['row'] = $this->reuniao_sistema_gestao_model->carrega($cd_reuniao_sistema_gestao);
						
			$this->load->view('gestao/reuniao_sistema_gestao/cadastro_ordem', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    public function salvar_cadastro_ordem()
	{
		if($this->permissao())
		{
			$this->load->model('gestao/reuniao_sistema_gestao_model');
						
			$data = array();
			$args = array();
			
        	$cd_usuario = $this->session->userdata('codigo');
			
			$cd_reuniao_sistema_gestao = $this->input->post('cd_reuniao_sistema_gestao', TRUE);
			
			$data['processo'] = $this->reuniao_sistema_gestao_model->get_processo_checked($cd_reuniao_sistema_gestao);
			
			foreach($data['processo'] as $item)
			{
				$args['nr_ordem']    = $this->input->post($item['cd_processo'], TRUE);
				$args['cd_processo'] = $this->input->post('processo_'.$item['cd_processo'], TRUE);
				
				$this->reuniao_sistema_gestao_model->salvar_cadastro_ordem($cd_reuniao_sistema_gestao, $cd_usuario, $args);
			}
			
			redirect('gestao/reuniao_sistema_gestao/cadastro_ordem/'.intval($cd_reuniao_sistema_gestao), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function apresentacao($cd_reuniao_sistema_gestao, $cd_processo = 0)
	{
		$this->load->model('gestao/reuniao_sistema_gestao_model');

		$data['cd_processo'] = $cd_processo;

		$data['reuniao_sistema_gestao'] = $this->reuniao_sistema_gestao_model->carrega(intval($cd_reuniao_sistema_gestao));

		$data['dropdown_processo'] = $this->reuniao_sistema_gestao_model->get_processo_checked(intval($cd_reuniao_sistema_gestao));

		$data['processo'] = $this->reuniao_sistema_gestao_model->get_processo_checked(intval($cd_reuniao_sistema_gestao), intval($cd_processo));

		$qt_indicador = 0;

		foreach ($data['processo']  as $key => $item) 
		{
			$data['processo'][$key]['indicador'] = $this->reuniao_sistema_gestao_model->get_processo_indicador($item['cd_reuniao_sistema_gestao_processo'], $cd_reuniao_sistema_gestao);

			$qt_indicador += count($data['processo'][$key]['indicador']);
		}

		$data['qt_indicador'] = $qt_indicador;

		$this->load->view('gestao/reuniao_sistema_gestao/apresentacao', $data);
	}

	public function apresentacao_indicador()
	{
		$this->load->helper('reuniao_gestao_indicador');

		$this->load->model('gestao/reuniao_sistema_gestao_model');

		$cd_indicador                       = $this->input->post('cd_indicador', TRUE);
		$cd_reuniao_sistema_gestao_processo = $this->input->post('cd_reuniao_sistema_gestao_processo', TRUE);

		$row = $this->reuniao_sistema_gestao_model->get_gestao_indicador($cd_indicador, $cd_reuniao_sistema_gestao_processo); 

		$data['indicador'] = json_decode($row['parametro'], true);

		$data['grafico'] = get_grafico_indicador($data['indicador']);
		$data['tabela']  = get_tabela_indicador($data['indicador'], TRUE);

		$this->load->view('gestao/reuniao_sistema_gestao/apresentacao_result', $data);
	}

	public function reuniao_gestao()
	{
		$this->load->model('gestao/reuniao_sistema_gestao_model');

		$data = array();

		$data['tipo'] = $this->reuniao_sistema_gestao_model->get_tipo_reuniao();
							
		$this->load->view('gestao/reuniao_sistema_gestao/reuniao_gestao', $data);
	}

	public function reuniao_gestao_listar()
	{
		$this->load->model('gestao/reuniao_sistema_gestao_model');

		$args = array(
			'dt_ini'                         => $this->input->post('dt_ini', TRUE),
			'dt_fim'                         => $this->input->post('dt_fim', TRUE),
			'cd_reuniao_sistema_gestao_tipo' => $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->reuniao_sistema_gestao_model->listar_reuniao_gestao($args);

		foreach($data['collection'] as $key => $item)
		{
			$processo = $this->reuniao_sistema_gestao_model->get_processo_checked($item['cd_reuniao_sistema_gestao']);
				
			$data['collection'][$key]['processo'] = array();

			foreach($processo as $item2)
			{				
				$data['collection'][$key]['processo'][] = $item2['processo'];
			}		
		}
		
		$this->load->view('gestao/reuniao_sistema_gestao/reuniao_gestao_result', $data);
	}

	public function enviar_todos($cd_reuniao_sistema_gestao)
	{
		if ($this->permissao())
        {
        	$this->load->model(array(
                'gestao/reuniao_sistema_gestao_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 234;

            $email = $this->eventos_email_model->carrega($cd_evento);

			$reuniao_sistema_gestao = $this->reuniao_sistema_gestao_model->carrega(intval($cd_reuniao_sistema_gestao));

			$tags = array('[DATA]', '[LINK]');
            $subs = array($reuniao_sistema_gestao['dt_reuniao_sistema_gestao'],  site_url('gestao/reuniao_sistema_gestao/reuniao_gestao'));

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Reunião Sistema de Gestão',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

			
			redirect('gestao/reuniao_sistema_gestao', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}