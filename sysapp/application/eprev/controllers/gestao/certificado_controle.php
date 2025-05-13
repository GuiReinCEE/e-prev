<?php
class Certificado_controle extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
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

    private function get_permissao_lista()
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
		if($this->get_permissao_lista())
		{
			$this->load->model('gestao/certificado_controle_model');

			$data = array(
				'tipo_certificao' => $this->certificado_controle_model->get_tipo(),
				'cargo'           => $this->certificado_controle_model->get_cargo()
			);

			$this->load->view('gestao/certificado_controle/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {			
    	$this->load->model('gestao/certificado_controle_model');

		$args = array(
			'cd_certificado_controle_cargo' => $this->input->post('cd_certificado_controle_cargo', TRUE),
			'cd_certificado_controle_tipo'  => $this->input->post('cd_certificado_controle_tipo', TRUE),
			'cpf'                           => $this->input->post('cpf', TRUE),
			'nome'                          => $this->input->post('nome', TRUE),
			'dt_certificao_ini'             => $this->input->post('dt_certificao_ini', TRUE),
			'dt_certificao_fim'             => $this->input->post('dt_certificao_fim', TRUE),
			'dt_expira_certificado_ini'     => $this->input->post('dt_expira_certificado_ini', TRUE),
			'dt_inclusao_ini'               => $this->input->post('dt_inclusao_ini', TRUE),
			'dt_inclusao_fim'               => $this->input->post('dt_inclusao_fim', TRUE),
			'fl_certificado'                => $this->input->post('fl_certificado', TRUE),
			'fl_recertificado'              => $this->input->post('fl_recertificado', TRUE),
			'fl_posse'                      => $this->input->post('fl_posse', TRUE),
			'fl_pontuacao'                  => $this->input->post('fl_pontuacao', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->certificado_controle_model->listar($args);
		
		$this->load->view('gestao/certificado_controle/index_result', $data);
    }

    public function cadastro($cd_certificado_controle = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/certificado_controle_model');

			$data = array(
				'tipo_certificao' => $this->certificado_controle_model->get_tipo(),
				'cargo'           => $this->certificado_controle_model->get_cargo()
			);
			
			if(intval($cd_certificado_controle) == 0)
			{
				$data['row'] = array(
					'cd_certificado_controle'       => intval($cd_certificado_controle),
					'cpf'                           => '',
					'nome'                          => '',
					'dt_nascimento'                 => '',
					'dt_posse'                      => '',
					'dt_posse_fim'                  => '',
					'dt_certificao'                 => '',
					'dt_expira_certificado'         => '',
					'arquivo'                       => '',
					'arquivo_nome'                  => '',
					'cd_certificado_controle_cargo' => '',
					'cd_certificado_controle_tipo'  => '',
					'cd_certificado_controle_pai'   => '',
					'fl_pontuacao'                  => 'N',
					'fl_indicado'                   => '',
					'nr_pontuacao_1'                => 0,
					'nr_pontuacao_2'                => 0,
					'nr_pontuacao_3'                => 0
				); 
			}
			else
			{
				$data['row'] = $this->certificado_controle_model->carrega($cd_certificado_controle);
			}
			
			$this->load->view('gestao/certificado_controle/cadastro', $data);
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
			$this->load->model('gestao/certificado_controle_model');

			$cd_certificado_controle = $this->input->post('cd_certificado_controle', TRUE);

			$args = array(
				'cd_certificado_controle_pai'   => $this->input->post('cd_certificado_controle_pai', TRUE),
				'cpf'                           => $this->input->post('cpf', TRUE),
				'nome'                          => $this->input->post('nome', TRUE),
				'dt_nascimento'                 => $this->input->post('dt_nascimento', TRUE),
				'dt_posse'                      => $this->input->post('dt_posse', TRUE),
				'dt_posse_fim'                  => $this->input->post('dt_posse_fim', TRUE),
				'dt_certificao'                 => $this->input->post('dt_certificao', TRUE),
				'dt_expira_certificado'         => $this->input->post('dt_expira_certificado', TRUE),
				'arquivo'                       => $this->input->post('arquivo', TRUE),
				'arquivo_nome'                  => $this->input->post('arquivo_nome', TRUE),
				'cd_certificado_controle_cargo' => $this->input->post('cd_certificado_controle_cargo', TRUE),
				'cd_certificado_controle_tipo'  => $this->input->post('cd_certificado_controle_tipo', TRUE),
				'fl_indicado'                   => $this->input->post('fl_indicado', TRUE),
				'nr_pontuacao_1'                => $this->input->post('nr_pontuacao_1', TRUE),
				'nr_pontuacao_2'                => $this->input->post('nr_pontuacao_2', TRUE),
				'nr_pontuacao_3'                => $this->input->post('nr_pontuacao_3', TRUE),
				'cd_usuario'                    => $this->session->userdata('codigo')
			);

			if(intval($cd_certificado_controle) == 0)
			{
				$cd_certificado_controle = $this->certificado_controle_model->salvar($args);
			}
			else
			{
				$this->certificado_controle_model->atualizar(intval($cd_certificado_controle), $args);
			}
			
			if(intval($args['cd_certificado_controle_pai']) == 0)
			{
				redirect('gestao/certificado_controle', 'refresh');
			}
			else
			{
				redirect('gestao/certificado_controle/cadastro/'.$cd_certificado_controle, 'refresh');
			}
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function excluir($cd_certificado_controle)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/certificado_controle_model');

			$this->certificado_controle_model->excluir($cd_certificado_controle, $this->session->userdata('codigo'));
			
			redirect("gestao/certificado_controle", "refresh");
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function busca_participante()
	{
		$this->load->model('gestao/certificado_controle_model');

		$row = $this->certificado_controle_model->busca_participante($this->input->post('cpf', TRUE));

		echo json_encode($row);
	}
}