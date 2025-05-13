<?php
class Treinamento_colaborador_formulario extends Controller {

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

    private function get_respondente()
    {
    	return array(
    		array('value' => 'C', 'text' => 'Colaborador'),
    		array('value' => 'G', 'text' => 'Gestor')
    	);
    }

    private function get_opcao()
    {
    	return array(
    		array('value' => 'S', 'text' => 'Sim'),
    		array('value' => 'N', 'text' => 'Não')
    	);
    }

    public function index()
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('projetos/treinamento_colaborador_formulario_model');

    		$data = array();
			
			$data['tipo'] 	     = $this->treinamento_colaborador_formulario_model->get_treinamento_tipo();
			$data['respondente'] = $this->get_respondente();
			
    		$this->load->view('cadastro/treinamento_colaborador_formulario/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('projetos/treinamento_colaborador_formulario_model');

    	$args = array();
		$data = array();

		$args = array(
			'ds_treinamento_colaborador_formulario' => $this->input->post('ds_treinamento_colaborador_formulario', TRUE),
			'cd_treinamento_colaborador_tipo'       => $this->input->post('cd_treinamento_colaborador_tipo', TRUE),
			'fl_enviar_para'                        => $this->input->post('fl_enviar_para', TRUE),
			'nr_dias_envio'							=> $this->input->post('nr_dias_envio', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->treinamento_colaborador_formulario_model->listar($args);

		foreach($data['collection'] as $key => $item)
		{
			$tipo = $this->treinamento_colaborador_formulario_model->get_formulario_tipo(intval($item['cd_treinamento_colaborador_formulario']));

			$data['collection'][$key]['tipo'] = array();

			foreach($tipo as $key2 => $item2) 
			{
				$data['collection'][$key]['tipo'][$key2] = $item2['ds_treinamento_colaborador_tipo'];
			}
		}

		$this->load->view('cadastro/treinamento_colaborador_formulario/index_result', $data);
    }

    public function cadastro($cd_treinamento_colaborador_formulario = 0)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model('projetos/treinamento_colaborador_formulario_model');

			$data = array();
			
			$data['tipo'] 	     = $this->treinamento_colaborador_formulario_model->get_treinamento_tipo();
			$data['respondente'] = $this->get_respondente();

			$data['tipo_checked'] = array();

			if(intval($cd_treinamento_colaborador_formulario) == 0)
			{
				$data['row'] = array(
					'cd_treinamento_colaborador_formulario' => $cd_treinamento_colaborador_formulario,
					'ds_treinamento_colaborador_formulario' => '',   
					'fl_enviar_para'                   		=> '',
					'nr_dias_envio'							=> ''
				);
			}
			else
			{
				$data['row'] = $this->treinamento_colaborador_formulario_model->carrega($cd_treinamento_colaborador_formulario);

				$tipo = $this->treinamento_colaborador_formulario_model->get_formulario_tipo(intval($cd_treinamento_colaborador_formulario));

				foreach($tipo as $item)
				{				
					$data['tipo_checked'][] = $item['cd_treinamento_colaborador_tipo'];
				}	
			}

			$this->load->view('cadastro/treinamento_colaborador_formulario/cadastro', $data);
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
        	$this->load->model('projetos/treinamento_colaborador_formulario_model');

			$args = array();
			
			$cd_treinamento_colaborador_formulario = $this->input->post('cd_treinamento_colaborador_formulario', TRUE);

			$tipo = $this->input->post('tipo', TRUE);

			$args = array(
				'ds_treinamento_colaborador_formulario' => $this->input->post('ds_treinamento_colaborador_formulario', TRUE),
				'fl_enviar_para'                        => $this->input->post('fl_enviar_para', TRUE),
				'tipo'                                  => (is_array($tipo) ? $tipo : array()),
				'nr_dias_envio'							=> $this->input->post('nr_dias_envio', TRUE),
				'cd_usuario' 							=> $this->session->userdata('codigo')
			);

			if(intval($cd_treinamento_colaborador_formulario) == 0)
			{
				$cd_treinamento_colaborador_formulario = $this->treinamento_colaborador_formulario_model->salvar($args);
			}
			else
			{
				$this->treinamento_colaborador_formulario_model->atualizar($cd_treinamento_colaborador_formulario, $args);
			}
			
			redirect('cadastro/treinamento_colaborador_formulario/cadastro/'.$cd_treinamento_colaborador_formulario);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
			
	public function estrutura($cd_treinamento_colaborador_formulario, $cd_treinamento_colaborador_formulario_estrutura = 0)
	{
		if($this->get_permissao())
        {
			$this->load->model('projetos/treinamento_colaborador_formulario_model');

			$data = array();				
			
			$data['cadastro'] = $this->treinamento_colaborador_formulario_model->carrega($cd_treinamento_colaborador_formulario);
			
			$tipo = $this->treinamento_colaborador_formulario_model->get_formulario_tipo(intval($cd_treinamento_colaborador_formulario));

			$data['cadastro']['tipo'] = array();

			foreach($tipo as $key => $item) 
			{
				$data['cadastro']['tipo'][$key] = $item['ds_treinamento_colaborador_tipo'];
			}
			
			$data['tipo'] = $this->treinamento_colaborador_formulario_model->get_estrutura_tipo();

			$data['collection'] = $this->treinamento_colaborador_formulario_model->estrutura_listar($cd_treinamento_colaborador_formulario);

			foreach($data['collection'] as $key => $item)
			{				
				$sub_estrutura = $this->treinamento_colaborador_formulario_model->estrutura_listar($cd_treinamento_colaborador_formulario, $item['cd_treinamento_colaborador_formulario_estrutura']);

				$data['collection'][$key]['sub_estrutura'] = array();

				foreach($sub_estrutura as $key2 => $item2)
				{
					$data['collection'][$key]['sub_estrutura'][$key2] = $item2['nr_treinamento_colaborador_formulario_estrutura'].') '.$item2['ds_treinamento_colaborador_formulario_estrutura'];
				}
			}
			
			$data['obrigatorio'] = $this->get_opcao();
			
			if(intval($cd_treinamento_colaborador_formulario_estrutura) == 0)
			{
				$ordem = $this->treinamento_colaborador_formulario_model->get_estrutura_ordem($cd_treinamento_colaborador_formulario);
				
				$data['estrutura'] = array(
					'cd_treinamento_colaborador_formulario_estrutura' 	   => $cd_treinamento_colaborador_formulario_estrutura,
					'nr_treinamento_colaborador_formulario_estrutura'	   => (isset($ordem['nr_treinamento_colaborador_formulario_estrutura']) ? $ordem['nr_treinamento_colaborador_formulario_estrutura'] : 1),
					'ds_treinamento_colaborador_formulario_estrutura' 	   => '',
					'cd_treinamento_colaborador_formulario_estrutura_tipo' => '',
					'fl_obrigatorio'									   => ''
				);
			}
			else
			{
				$data['estrutura'] = $this->treinamento_colaborador_formulario_model->estrutura_carrega($cd_treinamento_colaborador_formulario_estrutura);
			}

			$this->load->view('cadastro/treinamento_colaborador_formulario/estrutura', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function estrutura_salvar()
	{
		if($this->get_permissao())
        {
        	$this->load->model('projetos/treinamento_colaborador_formulario_model');

			$args = array();
		
			$cd_treinamento_colaborador_formulario_estrutura = $this->input->post('cd_treinamento_colaborador_formulario_estrutura', TRUE);
			$cd_treinamento_colaborador_formulario           = $this->input->post('cd_treinamento_colaborador_formulario', TRUE);

			$args = array(
				'cd_treinamento_colaborador_formulario'				   => $this->input->post('cd_treinamento_colaborador_formulario', TRUE),
				'ds_treinamento_colaborador_formulario_estrutura' 	   => $this->input->post('ds_treinamento_colaborador_formulario_estrutura', TRUE),
				'nr_treinamento_colaborador_formulario_estrutura'	   => $this->input->post('nr_treinamento_colaborador_formulario_estrutura', TRUE),
				'cd_treinamento_colaborador_formulario_estrutura_tipo' => $this->input->post('cd_treinamento_colaborador_formulario_estrutura_tipo', TRUE),
				'cd_treinamento_colaborador_formulario_estrutura_pai'  => $this->input->post('cd_treinamento_colaborador_formulario_estrutura_pai', TRUE),
				'fl_obrigatorio'									   => $this->input->post('fl_obrigatorio', TRUE),
				'cd_usuario' 										   => $this->session->userdata('codigo')
			);

			if(intval($cd_treinamento_colaborador_formulario_estrutura) == 0)
			{
				$cd_treinamento_colaborador_formulario_estrutura = $this->treinamento_colaborador_formulario_model->estrutura_salvar($args);
			}
			else
			{
				$this->treinamento_colaborador_formulario_model->estrutura_atualizar($cd_treinamento_colaborador_formulario_estrutura, $args);
			}
			
			if(intval($args['cd_treinamento_colaborador_formulario_estrutura_pai']) == 0)
			{
				redirect('cadastro/treinamento_colaborador_formulario/estrutura/'.$cd_treinamento_colaborador_formulario);
			}
			else
			{
				redirect('cadastro/treinamento_colaborador_formulario/sub_estrutura/'.$cd_treinamento_colaborador_formulario.'/'.$args['cd_treinamento_colaborador_formulario_estrutura_pai']);
			}
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function altera_ordem()
	{
		if($this->get_permissao())
        {
        	$this->load->model('projetos/treinamento_colaborador_formulario_model');
			
			$cd_treinamento_colaborador_formulario_estrutura = $this->input->post('cd_treinamento_colaborador_formulario_estrutura', TRUE);
			$nr_treinamento_colaborador_formulario_estrutura = $this->input->post('nr_treinamento_colaborador_formulario_estrutura', TRUE);
			$cd_usuario                                      = $this->session->userdata('codigo');
			
			$this->treinamento_colaborador_formulario_model->alterar_ordem($cd_treinamento_colaborador_formulario_estrutura, $nr_treinamento_colaborador_formulario_estrutura, $cd_usuario);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function sub_estrutura($cd_treinamento_colaborador_formulario, $cd_treinamento_colaborador_formulario_estrutura_pai, $cd_treinamento_colaborador_formulario_estrutura = 0)
	{
		if($this->get_permissao())
        {
			$this->load->model('projetos/treinamento_colaborador_formulario_model');

			$data = array();				
			
			$data['cadastro'] = $this->treinamento_colaborador_formulario_model->carrega($cd_treinamento_colaborador_formulario);
				
			$tipo = $this->treinamento_colaborador_formulario_model->get_formulario_tipo(intval($cd_treinamento_colaborador_formulario));

			$data['cadastro']['tipo'] = array();

			foreach($tipo as $key => $item) 
			{
				$data['cadastro']['tipo'][$key] = $item['ds_treinamento_colaborador_tipo'];
			}
		
			$data['estrutura'] = $this->treinamento_colaborador_formulario_model->estrutura_carrega($cd_treinamento_colaborador_formulario_estrutura_pai);

			$data['collection'] = $this->treinamento_colaborador_formulario_model->estrutura_listar($cd_treinamento_colaborador_formulario, $cd_treinamento_colaborador_formulario_estrutura_pai);
			
			if(intval($cd_treinamento_colaborador_formulario_estrutura) == 0)
			{
				$ordem = $this->treinamento_colaborador_formulario_model->get_estrutura_ordem($cd_treinamento_colaborador_formulario, $cd_treinamento_colaborador_formulario_estrutura_pai);
			
				$data['sub_estrutura'] = array(
					'cd_treinamento_colaborador_formulario_estrutura' 	   => $cd_treinamento_colaborador_formulario_estrutura,
					'nr_treinamento_colaborador_formulario_estrutura'	   => (isset($ordem['nr_treinamento_colaborador_formulario_estrutura']) ? $ordem['nr_treinamento_colaborador_formulario_estrutura'] : 1),
					'ds_treinamento_colaborador_formulario_estrutura' 	   => '',
					'cd_treinamento_colaborador_formulario_estrutura_pai'  => $cd_treinamento_colaborador_formulario_estrutura_pai,
					'cd_treinamento_colaborador_formulario_estrutura_tipo' => $data['estrutura']['cd_treinamento_colaborador_formulario_estrutura_tipo']
				);
			}
			else
			{
				$data['sub_estrutura'] = $this->treinamento_colaborador_formulario_model->estrutura_carrega($cd_treinamento_colaborador_formulario_estrutura);
			}

			$this->load->view('cadastro/treinamento_colaborador_formulario/sub_estrutura', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function configurar($cd_treinamento_colaborador_formulario, $cd_treinamento_colaborador_formulario_estrutura, $cd_treinamento_colaborador_formulario_estrutura_conf = 0)
	{
		if($this->get_permissao())
        {
			$this->load->model('projetos/treinamento_colaborador_formulario_model');

			$data = array();				
			
			$data['cadastro'] = $this->treinamento_colaborador_formulario_model->carrega($cd_treinamento_colaborador_formulario);

			$tipo = $this->treinamento_colaborador_formulario_model->get_formulario_tipo(intval($cd_treinamento_colaborador_formulario));

			$data['cadastro']['tipo'] = array();

			foreach($tipo as $key => $item) 
			{
				$data['cadastro']['tipo'][$key] = $item['ds_treinamento_colaborador_tipo'];
			}
			
			$estrutura = $this->treinamento_colaborador_formulario_model->estrutura_carrega($cd_treinamento_colaborador_formulario_estrutura);
			
			$configurar = $this->treinamento_colaborador_formulario_model->configurar_carrega($cd_treinamento_colaborador_formulario_estrutura);
			
			$data['collection'] = $this->treinamento_colaborador_formulario_model->configurar_listar($cd_treinamento_colaborador_formulario_estrutura);
			
			if(intval($estrutura['cd_treinamento_colaborador_formulario_estrutura_pai']) == 0)
			{
				$data['estrutura'] = $estrutura;
			}
			else
			{
				$data['sub_estrutura'] = $estrutura;
				
				$data['estrutura'] = $this->treinamento_colaborador_formulario_model->estrutura_carrega($estrutura['cd_treinamento_colaborador_formulario_estrutura_pai']);
			}
			
			$data['campo_adicional'] = $this->get_opcao();
			
			if(intval($cd_treinamento_colaborador_formulario_estrutura_conf) == 0)
			{
				$ordem = $this->treinamento_colaborador_formulario_model->get_configurar_ordem($cd_treinamento_colaborador_formulario_estrutura);
				
				$data['configurar'] = array(
					'cd_treinamento_colaborador_formulario_estrutura'		   => $estrutura['cd_treinamento_colaborador_formulario_estrutura'],
					'cd_treinamento_colaborador_formulario_estrutura_conf' 	   => $cd_treinamento_colaborador_formulario_estrutura_conf,
					'nr_treinamento_colaborador_formulario_estrutura_conf'	   => (isset($ordem['nr_treinamento_colaborador_formulario_estrutura_conf']) ? $ordem['nr_treinamento_colaborador_formulario_estrutura_conf'] : 1),
					'ds_treinamento_colaborador_formulario_estrutura_conf' 	   => '',
					'fl_campo_adicional'									   => ''
				);
			}
			else
			{
				$data['configurar'] = $this->treinamento_colaborador_formulario_model->configurar_carrega($cd_treinamento_colaborador_formulario_estrutura_conf);
			}

			$this->load->view('cadastro/treinamento_colaborador_formulario/configurar', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function configurar_salvar()
	{
		if($this->get_permissao())
        {
        	$this->load->model('projetos/treinamento_colaborador_formulario_model');

			$args = array();
		
			$cd_treinamento_colaborador_formulario           	  = $this->input->post('cd_treinamento_colaborador_formulario', TRUE);
			$cd_treinamento_colaborador_formulario_estrutura      = $this->input->post('cd_treinamento_colaborador_formulario_estrutura', TRUE);
			$cd_treinamento_colaborador_formulario_estrutura_conf = $this->input->post('cd_treinamento_colaborador_formulario_estrutura_conf', TRUE);
			
			$args = array(
				'nr_treinamento_colaborador_formulario_estrutura_conf' => $this->input->post('nr_treinamento_colaborador_formulario_estrutura_conf', TRUE),
				'ds_treinamento_colaborador_formulario_estrutura_conf' => $this->input->post('ds_treinamento_colaborador_formulario_estrutura_conf', TRUE),
				'cd_treinamento_colaborador_formulario_estrutura'      => $this->input->post('cd_treinamento_colaborador_formulario_estrutura', TRUE), 
				'fl_campo_adicional'								   => $this->input->post('fl_campo_adicional', TRUE),
				'cd_usuario' 										   => $this->session->userdata('codigo')
			);

			if(intval($cd_treinamento_colaborador_formulario_estrutura_conf) == 0)
			{
				$cd_treinamento_colaborador_formulario_estrutura_conf = $this->treinamento_colaborador_formulario_model->configurar_salvar($args);
			}
			else
			{
				$this->treinamento_colaborador_formulario_model->configurar_atualizar($cd_treinamento_colaborador_formulario_estrutura_conf, $args);
			}
			
			redirect('cadastro/treinamento_colaborador_formulario/configurar/'.$cd_treinamento_colaborador_formulario.'/'.$args['cd_treinamento_colaborador_formulario_estrutura']);

		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function altera_ordem_configurar()
	{
		if($this->get_permissao())
        {
        	$this->load->model('projetos/treinamento_colaborador_formulario_model');
			
			$cd_treinamento_colaborador_formulario_estrutura_conf = $this->input->post('cd_treinamento_colaborador_formulario_estrutura_conf', TRUE);
			$nr_treinamento_colaborador_formulario_estrutura_conf = $this->input->post('nr_treinamento_colaborador_formulario_estrutura_conf', TRUE);
			$cd_usuario                                           = $this->session->userdata('codigo');
			
			$this->treinamento_colaborador_formulario_model->altera_ordem_configurar($cd_treinamento_colaborador_formulario_estrutura_conf, $nr_treinamento_colaborador_formulario_estrutura_conf, $cd_usuario);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function visualizar($cd_treinamento_colaborador_formulario, $fl_debug = 'N')
	{
		if($this->get_permissao())
        {
			$data = array();

			$this->load->library('gera_avaliacao_treinamento');

			$data['formulario'] = $this->gera_avaliacao_treinamento->monta_formulario($cd_treinamento_colaborador_formulario);

			if(trim($fl_debug) == 'N')
			{
				$this->load->view('cadastro/treinamento_colaborador_formulario/visualizar', $data);
			}
			else
			{
				echo '<pre>';
				print_r($data['formulario']);
				echo '</pre>';
				exit;
			}
		}
		else
		{	
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}		
	}
	
}