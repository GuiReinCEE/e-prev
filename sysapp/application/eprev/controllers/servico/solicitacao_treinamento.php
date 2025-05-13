<?php
class Solicitacao_treinamento extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	public function index()
	{
		$this->load->view('servico/solicitacao_treinamento/index');
	}

	public function listar()
	{
		$this->load->model('projetos/solicitacao_treinamento_model');

		$args = array();

		manter_filtros($args);

		$data['collection'] = $this->solicitacao_treinamento_model->listar($this->session->userdata('codigo'), $args);

		$data['usuario_rh'] = ($this->session->userdata('indic_09') == '*' ? TRUE : FALSE);

		$this->load->view('servico/solicitacao_treinamento/index_result', $data);
	}

	public function cadastro($cd_solicitacao_treinamento = 0)
	{
		$this->load->model('projetos/solicitacao_treinamento_model');

		$data['drop_uf'] = $this->solicitacao_treinamento_model->lista_uf();

		$data['cd_usuario'] = $this->session->userdata('codigo');
		$data['usuario_rh'] = ($this->session->userdata('indic_09') == '*' ? TRUE : FALSE);

		$data['drop'] = array(
			array('value' => 'S', 'text' => 'Sim'),
			array('value' => 'N', 'text' => 'Não')
		);
		
		if(intval($cd_solicitacao_treinamento) == 0)
		{
			$data['row'] = array(
				'cd_solicitacao_treinamento' 	  => 0,
				'cd_treinamento_colaborador_tipo' => 0,
				'ds_evento' 					  => '',
				'ds_promotor' 					  => '',
				'ds_endereco' 					  => '',
				'ds_cidade' 					  => '',
				'ds_uf' 						  => '',
				'dt_inicio' 					  => '',
				'dt_final' 						  => '',
				'nr_hr_final' 					  => '',
				'nr_carga_horaria' 				  => '',
				'arquivo' 						  => '',
				'arquivo_nome' 					  => '',
				
				'cd_usuario_inclusao' 			  => 0,
				'ds_class_status' 				  => '',
				'ds_status' 					  => '',
				'dt_validacao' 					  => '',
				'ds_usuario_validacao' 			  => '',
				'ds_pertinente' 				  => '',
				'ds_class_status' 				  => '',
				'ds_descricao' 					  => ''
			);
		}
		else
		{
			$data['row'] = $this->solicitacao_treinamento_model->carrega($cd_solicitacao_treinamento);
		}

		$this->load->view('servico/solicitacao_treinamento/cadastro', $data);
	}

	public function salvar()
	{
		$this->load->model('projetos/solicitacao_treinamento_model');

		$cd_solicitacao_treinamento = $this->input->post('cd_solicitacao_treinamento', TRUE);

		$args = array(
			'cd_treinamento_colaborador_tipo' => $this->input->post('cd_treinamento_colaborador_tipo', TRUE),
			'ds_evento' 					  => $this->input->post('ds_evento', TRUE),
			'ds_promotor' 					  => $this->input->post('ds_promotor', TRUE),
			'ds_endereco' 					  => $this->input->post('ds_endereco', TRUE),
			'ds_cidade' 					  => $this->input->post('ds_cidade', TRUE),
			'ds_uf' 						  => $this->input->post('ds_uf', TRUE),
			'dt_inicio' 					  => $this->input->post('dt_inicio', TRUE),
			'dt_final' 						  => $this->input->post('dt_final', TRUE),
			'nr_hr_final' 					  => $this->input->post('nr_hr_final', TRUE),
			'nr_carga_horaria' 				  => $this->input->post('nr_carga_horaria', TRUE),
			'arquivo' 						  => $this->input->post('arquivo', TRUE),
			'arquivo_nome' 					  => $this->input->post('arquivo_nome', TRUE),
			'cd_usuario' 					  => $this->session->userdata('codigo')
		);

		if(intval($cd_solicitacao_treinamento) == 0)
		{
			$cd_solicitacao_treinamento = $this->solicitacao_treinamento_model->salvar($args);

			$this->envia_email_rh($cd_solicitacao_treinamento);
		}
		else
		{
			$this->solicitacao_treinamento_model->atualizar($cd_solicitacao_treinamento, $args);
		}

		redirect('servico/solicitacao_treinamento', 'refresh');
	}

	private function envia_email_rh($cd_solicitacao_treinamento)
	{
        $this->load->model('projetos/eventos_email_model');
        
        $cd_evento = 383;
        
        $email = $this->eventos_email_model->carrega($cd_evento);

        $cd_usuario = $this->session->userdata('codigo');  

        $tags = '[LINK]';

        $subs = site_url('servico/solicitacao_treinamento/cadastro/'.intval($cd_solicitacao_treinamento));

        $texto = str_replace($tags, $subs, $email['email']);
        
        $args = array(
            'de'      => 'Treinamentos',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );
        
        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}

	public function salvar_validacao()
	{
		$this->load->model('projetos/solicitacao_treinamento_model');

		$cd_solicitacao_treinamento = $this->input->post('cd_solicitacao_treinamento', TRUE);

		$args = array(
			'fl_pertinente' => $this->input->post('fl_pertinente', TRUE),
			'ds_descricao' 	=> $this->input->post('ds_descricao', TRUE),
			'cd_usuario' 	=> $this->session->userdata('codigo')
		);

		$this->solicitacao_treinamento_model->salvar_validacao($cd_solicitacao_treinamento, $args);

		if(trim($args['fl_pertinente']) == 'S')
		{
			$this->salvar_meu_treinamento($cd_solicitacao_treinamento);
		}

		$this->envia_email_solicitante($cd_solicitacao_treinamento);

		redirect('servico/solicitacao_treinamento', 'refresh');
	}

	private function salvar_meu_treinamento($cd_solicitacao_treinamento)
	{
		$row = $this->solicitacao_treinamento_model->carrega($cd_solicitacao_treinamento);

    	$args = array(
            'nome' 							  => $row['ds_evento'],
            'promotor' 						  => $row['ds_promotor'],
            'endereco' 						  => $row['ds_endereco'],
            'cidade' 						  => $row['ds_cidade'],
            'uf' 							  => $row['ds_uf'],
            'dt_inicio' 					  => $row['dt_inicio'],
            'dt_final' 						  => $row['dt_final'],
            'hr_final' 						  => $row['nr_hr_final'],
            'carga_horaria' 				  => $row['nr_carga_horaria'],
            'cd_treinamento_colaborador_tipo' => $row['cd_treinamento_colaborador_tipo'],
            'arquivo' 				          => $row['arquivo'],
            'arquivo_nome' 				      => $row['arquivo_nome'],
            'fl_cadastro_rh' 				  => 'N',
            'fl_certificado'   				  => 'S',
            'ds_justificativa' 				  => '',
            'cd_empresa' 					  => 9,
            'seq_dependencia' 				  => 0,
            'cd_usuario'                      => $row['cd_usuario_inclusao'],
            'ds_nome_usuario'                 => $row['ds_usuario_inclusao'],
            'cd_registro_empregado'           => $row['cd_registro_empregado'],
            'cd_gerencia'                     => $row['cd_gerencia']            
    	);

    	$cd_treinamento_colaborador = $this->solicitacao_treinamento_model->salvar_treinamento($args);

    	$row = $this->solicitacao_treinamento_model->get_numero_treinamento_colaborador($cd_treinamento_colaborador);

    	$args['numero'] = $row['numero'];
    	$args['ano']    = $row['ano'];

    	$cd_treinamento_colaborador_item = $this->solicitacao_treinamento_model->salvar_colaborador($args);
	}

	private function envia_email_solicitante($cd_solicitacao_treinamento)
	{
        $this->load->model('projetos/eventos_email_model');
        
        $cd_evento = 384;
        
        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->solicitacao_treinamento_model->carrega($cd_solicitacao_treinamento);

        $cd_usuario = $this->session->userdata('codigo');  

        $tags = '[LINK]';

        $subs = site_url('servico/solicitacao_treinamento/cadastro/'.intval($cd_solicitacao_treinamento));

        $texto = str_replace($tags, $subs, $email['email']);
        
        $args = array(
            'de'      => 'Treinamentos',
            'assunto' => $email['assunto'],
            'para'    => strtolower($row['usuario_inclusao']).'@eletroceee.com.br',
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );
        
        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	}
}