<?php
class Autoatendimento_menu extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

	private function get_status()
    {
    	return array(
    		array('value' => 'A', 'text' => 'Ativo'),
    		array('value' => 'D', 'text' => 'Desativado')
    	);
    }
	
	private function get_tipo_participante()
    {
    	return array(
    		array('value' => 'APOS', 'text' => 'Aposentado'),
    		array('value' => 'EXAU', 'text' => 'Ex Autárquico'),
    		array('value' => 'CTP',  'text' => 'CTP'),
    		array('value' => 'AUXD', 'text' => 'Auxilio Doença'),
    		array('value' => 'ATIV', 'text' => 'Ativo'),
    		array('value' => 'PENS', 'text' => 'Pensionista')
    	);
    }

    public function index()
    {
    	if(gerencia_in(array('GGS')))
    	{
    		$this->load->model('autoatendimento/menu_model');

    		$data = array();
			
			$data['fl_status'] = $this->get_status();
			
    		$this->load->view('servico/autoatendimento_menu/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('autoatendimento/menu_model');

    	$args = array();
		$data = array();
		
		$args['ds_menu']   = $this->input->post('ds_menu', TRUE);
		$args['fl_status'] = $this->input->post('fl_status', TRUE);
		
		manter_filtros($args);
		
		$data['collection'] = $this->menu_model->listar($args);
		
		foreach($data['collection'] as $key => $item)
		{
			$tipo_participante = $this->menu_model->menu_tipo_participante(intval($item['cd_menu']));

			$data['collection'][$key]['tipo_participante'] = array();

			foreach($tipo_participante as $key2 => $item2) 
			{
				$data['collection'][$key]['tipo_participante'][$key2] = $item2['text'];
			}
			
			$tipo_empresa = $this->menu_model->menu_patrocinadoras(intval($item['cd_menu']));

			$data['collection'][$key]['tipo_empresa'] = array();

			foreach($tipo_empresa as $key2 => $item2) 
			{
				$data['collection'][$key]['tipo_empresa'][$key2] = $item2['text'];
			}
			
			$submenu = $this->menu_model->sub_menu_listar(intval($item['cd_menu']));

			$data['collection'][$key]['submenu'] = array();

			foreach($submenu as $key2 => $item2) 
			{
				$data['collection'][$key]['submenu'][$key2] = $item2['ds_menu'];
			}
		}
		
		$this->load->view('servico/autoatendimento_menu/index_result', $data);
    }

    public function cadastro($cd_menu = 0)
    {
    	if(gerencia_in(array('GGS')))
    	{
    		$this->load->model('autoatendimento/menu_model');

			$data = array();
			
			$data['status'] = $this->get_status();
			
			$data['tipo_participante'] = $this->get_tipo_participante();
			
			$data['empresa'] = $this->menu_model->get_empresa();

			if(intval($cd_menu) == 0)
			{
				$ordem = $this->menu_model->get_menu_ordem();
				
				$data['row'] = array(
					'cd_menu'   => $cd_menu,
					'ds_codigo' => '',
					'ds_menu'	=> '',
					'nr_ordem'	=> (isset($ordem['nr_ordem']) ? $ordem['nr_ordem'] : 1),
					'fl_status'	=> 'A',
					'ds_href'	=> '',
					'ds_icone'	=> '',
					'ds_resumo'	=> ''
				);

				$data['menu_tipo_participante'] = array();
				$data['menu_patrocinadoras']    = array();
			}
			else
			{
				$data['row'] = $this->menu_model->carrega($cd_menu);

				$menu_tipo_participante = $this->menu_model->menu_tipo_participante($cd_menu);

				$data['menu_tipo_participante'] = array();

				foreach ($menu_tipo_participante as $key => $item) 
				{
					$data['menu_tipo_participante'][] = trim($item['tipo_participante']);
				}

				$menu_patrocinadoras = $this->menu_model->menu_patrocinadoras($cd_menu);

				$data['menu_patrocinadoras'] = array();

				foreach ($menu_patrocinadoras as $key => $item) 
				{
					$data['menu_patrocinadoras'][] = $item['cd_empresa'];
				}
			}

			$this->load->view('servico/autoatendimento_menu/cadastro', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function salvar()
	{
		if(gerencia_in(array('GGS')))
        {
    		$this->load->model('autoatendimento/menu_model');

			$args = array();
			
			$cd_menu = $this->input->post('cd_menu', TRUE);

			$args = array(
				'cd_menu_pai' => $this->input->post('cd_menu_pai', TRUE),
				'ds_codigo'   => $this->input->post('ds_codigo', TRUE),
				'ds_menu'     => $this->input->post('ds_menu', TRUE),
				'nr_ordem'    => $this->input->post('nr_ordem', TRUE),
				'fl_status'   => $this->input->post('fl_status', TRUE),
				'ds_href'     => $this->input->post('ds_href', TRUE),
				'ds_icone'    => $this->input->post('ds_icone', TRUE),
				'ds_resumo'   => $this->input->post('ds_resumo', TRUE),
				'cd_usuario'  => $this->session->userdata('codigo')
			);

			$tipo_participante = $this->input->post('tipo_participante', TRUE);

			if(!is_array($tipo_participante))
			{
				$args['tipo_participante'] = array();
			}
			else
			{
				$args['tipo_participante'] = $tipo_participante;
			}

			$empresa = $this->input->post('empresa', TRUE);

			if(!is_array($empresa))
			{
				$args['empresa'] = array();
			}
			else
			{
				$args['empresa'] = $empresa;
			}

			if(intval($cd_menu) == 0)
			{
				$cd_menu = $this->menu_model->salvar($args);
			}
			else
			{
				$this->menu_model->atualizar($cd_menu, $args);
			}

			if(intval($args['cd_menu_pai']) == 0)
			{
				redirect('servico/autoatendimento_menu/cadastro/'.$cd_menu);
			}
			else
			{
				redirect('servico/autoatendimento_menu/sub_menu/'.$args['cd_menu_pai']);
			}
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function sub_menu($cd_menu_pai, $cd_menu = 0)
	{
		if(gerencia_in(array('GGS')))
        {
			$this->load->model('autoatendimento/menu_model');

			$data = array();				
			
			$data['cadastro'] = $this->menu_model->carrega($cd_menu_pai);

			$data['status'] = $this->get_status();
			
			$data['tipo_participante'] = $this->menu_model->menu_tipo_participante($cd_menu_pai);
			
			$data['empresa'] = $this->menu_model->menu_patrocinadoras($cd_menu_pai);
			
			$data['collection'] = $this->menu_model->sub_menu_listar($cd_menu_pai);
			
			foreach($data['collection'] as $key => $item)
			{
				$tipo_participante = $this->menu_model->menu_tipo_participante(intval($item['cd_menu']));

				$data['collection'][$key]['tipo_participante'] = array();

				foreach($tipo_participante as $key2 => $item2) 
				{
					$data['collection'][$key]['tipo_participante'][$key2] = $item2['text'];
				}
				
				$tipo_empresa = $this->menu_model->menu_patrocinadoras(intval($item['cd_menu']));

				$data['collection'][$key]['tipo_empresa'] = array();

				foreach($tipo_empresa as $key2 => $item2) 
				{
					$data['collection'][$key]['tipo_empresa'][$key2] = $item2['text'];
				}
			}
			
			if(intval($cd_menu) == 0)
			{
				$ordem = $this->menu_model->get_menu_ordem($cd_menu_pai);
				
				$data['sub_menu'] = array(
					'cd_menu'     => $cd_menu,
					'cd_menu_pai' => $cd_menu_pai,
					'ds_codigo'   => '',
					'ds_menu'	  => '',
					'nr_ordem'	  => (isset($ordem['nr_ordem']) ? $ordem['nr_ordem'] : 1),
					'fl_status'	  => 'A',
					'ds_href'	  => '',
					'ds_icone'	  => '',
					'ds_resumo'	  => ''
				);

				$data['menu_tipo_participante'] = array();
				$data['menu_patrocinadoras']    = array();
			}
			else
			{
				$data['sub_menu'] = $this->menu_model->carrega($cd_menu);

				$menu_tipo_participante = $this->menu_model->menu_tipo_participante($cd_menu);

				$data['menu_tipo_participante'] = array();

				foreach ($menu_tipo_participante as $key => $item) 
				{
					$data['menu_tipo_participante'][] = trim($item['tipo_participante']);
				}

				$menu_patrocinadoras = $this->menu_model->menu_patrocinadoras($cd_menu);

				$data['menu_patrocinadoras'] = array();

				foreach ($menu_patrocinadoras as $key => $item) 
				{
					$data['menu_patrocinadoras'][] = $item['cd_empresa'];
				}
			}
			
			$this->load->view('servico/autoatendimento_menu/sub_menu', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }	
	}
	
	public function altera_ordem()
	{
		if(gerencia_in(array('GGS')))
        {
    		$this->load->model('autoatendimento/menu_model');
			
			$cd_menu    = $this->input->post('cd_menu', TRUE);
			$nr_ordem   = $this->input->post('nr_ordem', TRUE);
			$cd_usuario = $this->session->userdata('codigo');
			
			$this->menu_model->alterar_ordem($cd_menu, $nr_ordem, $cd_usuario);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}