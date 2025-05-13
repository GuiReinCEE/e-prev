<?php
class Sms_divulgacao extends Controller
{
	function __construct()
    {
        parent::Controller();

		CheckLogin();
    }
	
	public function index()
    {
		
    }
	
	public function cadastro($cd_sms_divulgacao = 0)
	{
		$this->load->model('sms/sms_divulgacao_model');

		$data['collection'] = $this->sms_divulgacao_model->listar_participante($cd_sms_divulgacao);

		if(intval($cd_sms_divulgacao) == 0)
		{
			$data['row'] = array(
				'cd_sms_divulgacao' => 0,
				'ds_assunto' 		=> '',
				'ds_texto'      	=> '',
				'ds_url_link' 		=> '',
				'ds_avulso' 		=> '',
				'arquivo'      		=> '',
				'arquivo_nome' 		=> ''
			);
		}
		else
		{
			$data['row'] = $this->sms_divulgacao_model->carrega($cd_sms_divulgacao);
		}

		$this->load->view('sms/sms_divulgacao/cadastro', $data);
	}

	public function salvar()
	{
		$this->load->model('sms/sms_divulgacao_model');

		$cd_sms_divulgacao = $this->input->post('cd_sms_divulgacao', TRUE);
		$fl_arquivo 	   = $this->input->post('fl_arquivo', TRUE);

		$args = array(
			'ds_assunto'   => $this->input->post('ds_assunto', TRUE),
			'ds_url_link'  => $this->input->post('ds_url_link', TRUE),
			'ds_texto'     => $this->input->post('ds_texto', TRUE),
			'ds_avulso'    => $this->input->post('ds_avulso', TRUE),
			'arquivo'      => $this->input->post('arquivo', TRUE),
			'arquivo_nome' => $this->input->post('arquivo_nome', TRUE),
			'cd_usuario'   => $this->session->userdata('codigo')
		);

		if(intval($cd_sms_divulgacao) == 0)
		{
			$cd_sms_divulgacao = $this->sms_divulgacao_model->salvar($args);
		}
		else
		{
			$this->sms_divulgacao_model->atualizar($cd_sms_divulgacao, $args);
		}

		if(trim($fl_arquivo) == 'S')
		{
			$this->sms_divulgacao_model->limpar_tabela($cd_sms_divulgacao,$this->session->userdata('codigo'));

			$arquivo = base_url().'up/sms_divulgacao/'.$args['arquivo'];
			$csv 	 = file($arquivo);

			foreach ($csv as $key => $item) 
			{
				$column = str_getcsv($item, ';');

				$re = array(
					'cd_sms_divulgacao' 	=> $cd_sms_divulgacao,
					'cd_empresa' 			=> $column[0], 
					'cd_registro_empregado' => $column[1], 
					'seq_dependencia'  		=> $column[2],
					'cd_usuario' 			=> $this->session->userdata('codigo')
				);

				if(intval($re['cd_registro_empregado']) > 0)
				{
					$this->sms_divulgacao_model->salvar_participante($re);
				}
			}
		}

		redirect('sms/sms_divulgacao/cadastro/'.$cd_sms_divulgacao, 'refresh');
	}

	public function publico($cd_sms_divulgacao, $cd_sms_divulgacao_grupo = 0)
	{
		$this->load->model('sms/sms_divulgacao_model');

		$data['collection'] = $this->sms_divulgacao_model->publico_listar($cd_sms_divulgacao);

		foreach ($data['collection'] as $key => $item) 
		{
			$data['collection'][$key]['empresa'] = array();

			foreach ($this->sms_divulgacao_model->carrega_empresa($item['cd_sms_divulgacao_grupo']) as $key1 => $item1) 
			{
				$data['collection'][$key]['empresa'][]    = $item1['sigla'];
			}

			$data['collection'][$key]['plano'] = array();
			
			foreach ($this->sms_divulgacao_model->carrega_plano($item['cd_sms_divulgacao_grupo']) as $key2 => $item2) 
			{
				$data['collection'][$key]['plano'][]    = $item2['descricao'];
			}

			$data['collection'][$key]['tipo'] = array();

			foreach ($this->sms_divulgacao_model->carrega_tipo($item['cd_sms_divulgacao_grupo']) as $key3 => $item3) 
			{
				$data['collection'][$key]['tipo'][] = $item3['tipo'];
			}

			$data['collection'][$key]['cidade'] = array();

			$cidade = array();

			foreach ($this->sms_divulgacao_model->carrega_cidade($item['cd_sms_divulgacao_grupo']) as $key4 => $item4) 
			{
				$cidade[] = array(
					'cd_sms_divulgacao_grupo_cidade'  => $item4['cd_sms_divulgacao_grupo_cidade'],
					'ds_cidade' 			  		  => trim($item4['ds_cidade']),
					'nr_participantes_cidade' 		  => $item4['nr_participantes_cidade'],
					'nr_participantes_cidade_contato' => $item4['nr_participantes_cidade_contato']
				);

				$data['collection'][$key]['cidade'] = $cidade;
			}
		}

		if(intval($cd_sms_divulgacao_grupo) == 0)
		{
			$data['row'] = array(
				'cd_sms_divulgacao' 	  => $cd_sms_divulgacao,
				'cd_sms_divulgacao_grupo' => 0,
				'cd_empresa' 			  => array(),
				'cd_plano' 				  => array(),
				'ds_tipo' 				  => array(),
				'ds_cidade' 			  => ''
			);
		}
		else
		{
			$data['row'] = $this->sms_divulgacao_model->publico_carrega($cd_sms_divulgacao_grupo);

			$data['row']['cd_empresa'] = array();

			foreach ($this->sms_divulgacao_model->carrega_empresa($cd_sms_divulgacao_grupo) as $key => $item) 
			{
				$data['row']['cd_empresa'][] = $item['cd_empresa'];
			}

			$data['row']['cd_plano'] = array();
			
			foreach ($this->sms_divulgacao_model->carrega_plano($cd_sms_divulgacao_grupo) as $key => $item) 
			{
				$data['row']['cd_plano'][] = $item['cd_plano'];
			}

			$data['row']['ds_tipo'] = array();

			foreach ($this->sms_divulgacao_model->carrega_tipo($cd_sms_divulgacao_grupo) as $key => $item) 
			{
				$data['row']['ds_tipo'][] = $item['ds_tipo'];
			}

			$data['row']['ds_cidade'] = '';

			$cidade = array();

			foreach ($this->sms_divulgacao_model->carrega_cidade($cd_sms_divulgacao_grupo) as $key => $item) 
			{
				$cidade[] = trim($item['ds_cidade']);

				$data['row']['ds_cidade'] = implode(';', $cidade);
			}
		}

		$data['empresa'] = $this->sms_divulgacao_model->get_empresa();
		$data['plano']   = $this->sms_divulgacao_model->get_plano();

		$data['tipo'] = array(
			array('value' => 'ATIV', 'text' => 'Ativo'),
			array('value' => 'APOS', 'text' => 'Aposentado'),
			array('value' => 'PENS', 'text' => 'Pensionista'),
			array('value' => 'EXAU', 'text' => 'Ex-Autárquico'),
			array('value' => 'AUXD', 'text' => 'Auxilio Doença')
		);

		$this->load->view('sms/sms_divulgacao/publico', $data);
	}

	public function publico_salvar()
	{
		$this->load->model('sms/sms_divulgacao_model');

		$cd_sms_divulgacao_grupo = $this->input->post('cd_sms_divulgacao_grupo', TRUE);

		$ds_cidade = $this->input->post('ds_cidade', TRUE);

		$args = array(
			'cd_sms_divulgacao' => $this->input->post('cd_sms_divulgacao', TRUE),
			'cd_empresa' 		=> (is_array($this->input->post('cd_empresa', TRUE)) ? $this->input->post('cd_empresa', TRUE) : array()),
			'cd_plano' 	 		=> (is_array($this->input->post('cd_plano', TRUE)) ? $this->input->post('cd_plano', TRUE) : array()),
			'ds_tipo' 	 		=> (is_array($this->input->post('ds_tipo', TRUE)) ? $this->input->post('ds_tipo', TRUE) : array()),
			'ds_cidade'  		=> (trim($ds_cidade) != '' ? explode(';', $ds_cidade) : array()),
			'cd_usuario' 		=> $this->session->userdata('codigo')
		);

		foreach ($args['ds_cidade'] as $key => $item) 
		{
			$cidades 				 = array();
			$cidades[] 				 = "'".$item."'";
			$nr_participantes_cidade = array();

			$nr_participantes_cidade = $this->sms_divulgacao_model->carrega_participantes(
				$args['cd_empresa'], 
				$args['cd_plano'], 
				$args['ds_tipo'],
				array(trim($item))
			);

			$nr_participantes_cidade_contato = $this->sms_divulgacao_model->carrega_participantes_contato(
				$args['cd_empresa'], 
				$args['cd_plano'], 
				$args['ds_tipo'],
				array(trim($item))
			);

			foreach ($nr_participantes_cidade as $key2 => $item2) 
			{
				$cidades[] = $item2;
			}

			foreach ($nr_participantes_cidade_contato as $key3 => $item3) 
			{
				$cidades[] = $item3;

				$args['cidades'][] = implode(",", $cidades);
			}
		}

		$nr_participantes = $this->sms_divulgacao_model->carrega_participantes(
			$args['cd_empresa'], 
			$args['cd_plano'], 
			$args['ds_tipo'],
			$args['ds_cidade']
		);

		foreach ($nr_participantes as $key => $item) 
		{
			$args['nr_participantes'] = $item;
		}

		$nr_participantes_contato = $this->sms_divulgacao_model->carrega_participantes_contato(
			$args['cd_empresa'], 
			$args['cd_plano'], 
			$args['ds_tipo'],
			$args['ds_cidade']
		);

		foreach ($nr_participantes_contato as $key => $item) 
		{
			$args['nr_participantes_contato'] = $item;
		}

		if(intval($cd_sms_divulgacao_grupo) == 0)
		{
			$this->sms_divulgacao_model->publico_salvar($args);
		}
		else
		{
			$this->sms_divulgacao_model->publico_atualizar($cd_sms_divulgacao_grupo, $args);

			foreach ($this->sms_divulgacao_model->carrega_cidade($cd_sms_divulgacao_grupo) as $key => $item) 
			{
				$nr_participantes_cidade = $this->sms_divulgacao_model->carrega_participantes(
					$args['cd_empresa'], 
					$args['cd_plano'], 
					$args['ds_tipo'],
					array($item['ds_cidade'])
				);

				$nr_participantes_cidade_contato = $this->sms_divulgacao_model->carrega_participantes_contato(
					$args['cd_empresa'], 
					$args['cd_plano'], 
					$args['ds_tipo'],
					array($item['ds_cidade'])
				);

				$this->sms_divulgacao_model->atualizar_participantes_cidade(
					$item['cd_sms_divulgacao_grupo_cidade'], 
					$nr_participantes_cidade['count'], 
					$nr_participantes_cidade_contato['count'], 
					$this->session->userdata('codigo')
				);
			}
		}

		redirect('sms/sms_divulgacao/publico/'.$args['cd_sms_divulgacao'], 'refresh');
	}

	public function atualizar_participantes()
	{
		$this->load->model('sms/sms_divulgacao_model');

		$cd_sms_divulgacao_grupo = $this->input->post('cd_sms_divulgacao_grupo', TRUE);

		$row['cd_empresa'] = array();

		foreach ($this->sms_divulgacao_model->carrega_empresa($cd_sms_divulgacao_grupo) as $key1 => $item1) 
		{
			$row['cd_empresa'][] = $item1['cd_empresa'];
		}

		$row['cd_plano'] = array();
		
		foreach ($this->sms_divulgacao_model->carrega_plano($cd_sms_divulgacao_grupo) as $key2 => $item2) 
		{
			$row['cd_plano'][] = $item2['cd_plano'];
		}

		$row['ds_tipo'] = array();

		foreach ($this->sms_divulgacao_model->carrega_tipo($cd_sms_divulgacao_grupo) as $key3 => $item3) 
		{
			$row['ds_tipo'][] = $item3['ds_tipo'];
		}

		$row['ds_cidade'] = array();

		$cidades = array();

		foreach ($this->sms_divulgacao_model->carrega_cidade($cd_sms_divulgacao_grupo) as $key4 => $item4) 
		{
			$nr_participantes_cidade = $this->sms_divulgacao_model->carrega_participantes(
				$row['cd_empresa'], 
				$row['cd_plano'], 
				$row['ds_tipo'],
				array($item4['ds_cidade'])
			);

			$nr_participantes_cidade_contato = $this->sms_divulgacao_model->carrega_participantes_contato(
				$row['cd_empresa'], 
				$row['cd_plano'], 
				$row['ds_tipo'],
				array($item4['ds_cidade'])
			);

			$cidades[] = array(
				'cd_sms_divulgacao_grupo_cidade'  => $item4['cd_sms_divulgacao_grupo_cidade'],
				'ds_cidade' 					  => $item4['ds_cidade'],
				'nr_participantes_cidade' 		  => $nr_participantes_cidade['count'],
				'nr_participantes_cidade_contato' => $nr_participantes_cidade_contato['count']
			);

			$row['ds_cidade'][] = trim($item4['ds_cidade']);
		}

		$nr_participantes = $this->sms_divulgacao_model->carrega_participantes(
			$row['cd_empresa'], 
			$row['cd_plano'], 
			$row['ds_tipo'],
			$row['ds_cidade']
		);

		$nr_participante['nr_participantes'] = $nr_participantes['count'];

		$nr_participantes_contato = $this->sms_divulgacao_model->carrega_participantes_contato(
			$row['cd_empresa'], 
			$row['cd_plano'], 
			$row['ds_tipo'],
			$row['ds_cidade']
		);

		foreach ($nr_participantes_contato as $key => $item) 
		{
			$nr_participante['nr_participantes_contato'] = $item;
		}

		$nr_participante['cidade'] = $cidades;

		$this->sms_divulgacao_model->atualizar_participantes(
			$cd_sms_divulgacao_grupo, 
			$nr_participante['nr_participantes'], 
			$nr_participante['nr_participantes_contato'], 
			$this->session->userdata('codigo')
		);

		foreach ($cidades as $key => $item) 
		{
			$this->sms_divulgacao_model->atualizar_participantes_cidade(
				$item['cd_sms_divulgacao_grupo_cidade'], 
				$item['nr_participantes_cidade'], 
				$item['nr_participantes_cidade_contato'], 
				$this->session->userdata('codigo')
			);
		}

		echo json_encode($nr_participante);
	}
}
?>