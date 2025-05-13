<?php
class Regulamento_alteracao_atividade extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao($cd_regulamento_alteracao_atividade)
	{
		$this->load->model('gestao/regulamento_alteracao_atividade_model');

		$row = $this->regulamento_alteracao_atividade_model->get_usuario_responsavel($cd_regulamento_alteracao_atividade, $this->session->userdata('codigo'));

		if(intval($row['responsavel']) > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function index($cd_regulamento_alteracao_atividade)
	{
		if($this->get_permissao($cd_regulamento_alteracao_atividade))
		{
			$this->load->model('gestao/regulamento_alteracao_atividade_model');

			$data['row'] 			= $this->regulamento_alteracao_atividade_model->carrega($cd_regulamento_alteracao_atividade, $this->session->userdata('divisao'));
			$data['atividade_tipo'] = $this->regulamento_alteracao_atividade_model->get_atividade_tipo();

			$cd_regulamento_alteracao_unidade_basica_pai = $data['row']['cd_regulamento_alteracao_unidade_basica'];

			if(intval($data['row']['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
			{
				$cd_regulamento_alteracao_unidade_basica_pai = $data['row']['cd_regulamento_alteracao_unidade_basica_pai'];
			}

			$data['unidade_basica'] = $this->regulamento_alteracao_atividade_model->carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica_pai);

			$this->load->view('atividade/regulamento_alteracao_atividade/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		$cd_regulamento_alteracao_atividade = $this->input->post('cd_regulamento_alteracao_atividade', TRUE);

		if($this->get_permissao($cd_regulamento_alteracao_atividade))
		{
			$this->load->model('gestao/regulamento_alteracao_atividade_model');

			$cd_regulamento_alteracao_atividade_gerencia = $this->input->post('cd_regulamento_alteracao_atividade_gerencia', TRUE);

			$args = array(
				'cd_regulamento_alteracao_atividade_tipo' => $this->input->post('cd_regulamento_alteracao_atividade_tipo', TRUE),
				'dt_prevista' 							  => $this->input->post('dt_prevista', TRUE),
				'dt_implementacao' 						  => $this->input->post('dt_implementacao', TRUE),
				'cd_usuario' 							  => $this->session->userdata('codigo')
			);

			$this->regulamento_alteracao_atividade_model->salvar($cd_regulamento_alteracao_atividade_gerencia, $args);

			$fl_pertinencia = $this->input->post('fl_pertinencia', TRUE);

			if(trim($fl_pertinencia) == 'N')
			{
				$this->envia_email_pertinencia($cd_regulamento_alteracao_atividade);
			}

			if(trim($args['dt_implementacao']) != '')
			{
				$this->envia_email_implementacao($cd_regulamento_alteracao_atividade);
			}

			redirect('atividade/regulamento_alteracao_atividade/index/'.$cd_regulamento_alteracao_atividade, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	private function envia_email_pertinencia($cd_regulamento_alteracao_atividade)
	{
		$this->load->model('projetos/eventos_email_model');

        $cd_evento = 387;

        $email = $this->eventos_email_model->carrega($cd_evento);
        $row   = $this->regulamento_alteracao_atividade_model->carrega($cd_regulamento_alteracao_atividade, $this->session->userdata('divisao'));

		$cd_regulamento_alteracao_unidade_basica_pai = $row['cd_regulamento_alteracao_unidade_basica'];

		if(intval($row['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
		{
			$cd_regulamento_alteracao_unidade_basica_pai = $row['cd_regulamento_alteracao_unidade_basica_pai'];
		}

		$unidade_basica = $this->regulamento_alteracao_atividade_model->carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica_pai);
      
        $tags = array('[NOME]', '[PERTINENCIA]', '[DT_PREVISTA]', '[LINK]');

        $subs = array(
            $unidade_basica['ds_regulamento_tipo'],
            (trim($row['dt_prevista']) != '' ? $row['ds_regulamento_alteracao_atividade_tipo'].',' : $row['ds_regulamento_alteracao_atividade_tipo']),
            (trim($row['dt_prevista']) != '' ? 'com data prevista de '.$row['dt_prevista'] : ''),
            site_url('planos/regulamento_alteracao/atividades/'.intval($unidade_basica['cd_regulamento_alteracao']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Regulamento de Plano - Atividade',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);     
	}

	private function envia_email_implementacao($cd_regulamento_alteracao_atividade)
	{
		$this->load->model('projetos/eventos_email_model');

        $cd_evento = 388;

        $email = $this->eventos_email_model->carrega($cd_evento);
        $row   = $this->regulamento_alteracao_atividade_model->carrega($cd_regulamento_alteracao_atividade, $this->session->userdata('divisao'));

		$cd_regulamento_alteracao_unidade_basica_pai = $row['cd_regulamento_alteracao_unidade_basica'];

		if(intval($row['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
		{
			$cd_regulamento_alteracao_unidade_basica_pai = $row['cd_regulamento_alteracao_unidade_basica_pai'];
		}

		$unidade_basica = $this->regulamento_alteracao_atividade_model->carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica_pai);
      
        $tags = array('[NOME]', '[DT_IMPLEMENTACAO]', '[LINK]');

        $subs = array(
            $unidade_basica['ds_regulamento_tipo'],
            $row['dt_implementacao'],
            site_url('planos/regulamento_alteracao/atividades/'.intval($unidade_basica['cd_regulamento_alteracao']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Regulamento de Plano - Atividade',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);     
	}

	public function acompanhamento($cd_regulamento_alteracao_atividade, $cd_regulamento_alteracao_atividade_acompanhamento = 0)
	{
		if($this->get_permissao($cd_regulamento_alteracao_atividade))
		{
			$this->load->model('gestao/regulamento_alteracao_atividade_model');

			$data['atividade'] 	= $this->regulamento_alteracao_atividade_model->carrega($cd_regulamento_alteracao_atividade, $this->session->userdata('divisao'));
			$data['collection'] = $this->regulamento_alteracao_atividade_model->listar_acompanhamento($data['atividade']['cd_regulamento_alteracao_atividade_gerencia']);
			$data['cd_usuario'] = $this->session->userdata('codigo');

			$cd_regulamento_alteracao_unidade_basica_pai = $data['atividade']['cd_regulamento_alteracao_unidade_basica'];

			if(intval($data['atividade']['cd_regulamento_alteracao_unidade_basica_pai']) > 0)
			{
				$cd_regulamento_alteracao_unidade_basica_pai = $data['atividade']['cd_regulamento_alteracao_unidade_basica_pai'];
			}

			$data['unidade_basica'] = $this->regulamento_alteracao_atividade_model->carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica_pai);

			if(intval($cd_regulamento_alteracao_atividade_acompanhamento) == 0)
			{
				$data['row'] = array(
					'cd_regulamento_alteracao_atividade_acompanhamento' => '',
					'ds_regulamento_alteracao_atividade_acompanhamento' => ''
				);
			}
			else
			{
				$data['row'] = $this->regulamento_alteracao_atividade_model->carrega_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento);
			}

			$this->load->view('atividade/regulamento_alteracao_atividade/acompanhamento', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_acompanhamento()
	{
		$cd_regulamento_alteracao_atividade = $this->input->post('cd_regulamento_alteracao_atividade', TRUE);

		if($this->get_permissao($cd_regulamento_alteracao_atividade))
		{
			$this->load->model('gestao/regulamento_alteracao_atividade_model');

			$cd_regulamento_alteracao_atividade_acompanhamento = $this->input->post('cd_regulamento_alteracao_atividade_acompanhamento', TRUE);

			$args = array(
				'cd_regulamento_alteracao_atividade_gerencia' 		=> $this->input->post('cd_regulamento_alteracao_atividade_gerencia', TRUE),
				'ds_regulamento_alteracao_atividade_acompanhamento' => $this->input->post('ds_regulamento_alteracao_atividade_acompanhamento', TRUE),
				'cd_usuario' 										=> $this->session->userdata('codigo')
			);

			if(intval($cd_regulamento_alteracao_atividade_acompanhamento) == 0)
			{
				$this->regulamento_alteracao_atividade_model->salvar_acompanhamento($args);
			}
			else
			{
				$this->regulamento_alteracao_atividade_model->atualizar_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento, $args);
			}

			redirect('atividade/regulamento_alteracao_atividade/acompanhamento/'.$cd_regulamento_alteracao_atividade, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function minhas()
	{
		$this->load->model('gestao/regulamento_alteracao_atividade_model');

		$data['drop'] = array(
			array('value' => 'S', 'text' => 'Sim'),
			array('value' => 'N', 'text' => 'Não'),
		);

		$data['drop_tipo'] = $this->regulamento_alteracao_atividade_model->get_tipo();

		$this->load->view('atividade/regulamento_alteracao_atividade/minhas', $data);
	}

	public function listar_minhas()
	{
		$this->load->model('gestao/regulamento_alteracao_atividade_model');

		$args = array(
			'fl_respondido' 						  => $this->input->post('fl_respondido', TRUE),
			'cd_regulamento_alteracao_atividade_tipo' => $this->input->post('cd_regulamento_alteracao_atividade_tipo', TRUE),
			'fl_implementado' 						  => $this->input->post('fl_implementado', TRUE),
			'dt_prevista_ini' 						  => $this->input->post('dt_prevista_ini', TRUE),
			'dt_prevista_fim' 						  => $this->input->post('dt_prevista_fim', TRUE),
			'dt_implementa_ini' 					  => $this->input->post('dt_implementa_ini', TRUE),
			'dt_implementa_fim' 					  => $this->input->post('dt_implementa_fim', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->regulamento_alteracao_atividade_model->listar_minhas($this->session->userdata('codigo'), $args);

		$this->load->view('atividade/regulamento_alteracao_atividade/minhas_result', $data);
	}
}