<?php
class Solic_fiscalizacao_audit_grupo extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	//GC E GC

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

	public function index($cd_solic_fiscalizacao_audit_grupo = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/solic_fiscalizacao_audit_grupo_model');

			$data['usuarios']   	   = $this->solic_fiscalizacao_audit_grupo_model->get_usuarios();
			$data['collection'] 	   = array();
			$data['integrantes_grupo'] = array();

			### CARREGA A BOX DO CADASTRO ###
			if(intval($cd_solic_fiscalizacao_audit_grupo) == 0)
			{
				$data['row'] = array(
					'cd_solic_fiscalizacao_audit_grupo' => 0,
					'ds_grupo' 							=> '',
				    'ds_email_grupo' 					=> ''
				);
			}
			else
			{
				$data['row'] = $this->solic_fiscalizacao_audit_grupo_model->carrega($cd_solic_fiscalizacao_audit_grupo);

				foreach ($this->solic_fiscalizacao_audit_grupo_model->get_integrantes_grupos($cd_solic_fiscalizacao_audit_grupo) as $key => $item) 
				{
					$data['integrantes_grupo'][] = $item['cd_usuario'];
				}
			}

			### MONTA A LISTA ###
			foreach ($this->solic_fiscalizacao_audit_grupo_model->get_grupos() as $key => $item)
			{
				$integrantes_grupo = array();

				foreach ($this->solic_fiscalizacao_audit_grupo_model->get_integrantes_grupos($item['cd_solic_fiscalizacao_audit_grupo']) as $key2 => $item2)
				{
					$integrantes_grupo[] = $item2['ds_integrante'];
				}

				$data['collection'][] = array(
					'cd_solic_fiscalizacao_audit_grupo' => $item['cd_solic_fiscalizacao_audit_grupo'],
					'ds_grupo' 							=> $item['ds_grupo'],
					'ds_email_grupo'    				=> $item['ds_email_grupo'],
					'integrantes_grupo' 				=> $integrantes_grupo
				);
			}

			$this->load->view('atividade/solic_fiscalizacao_audit_grupo/index', $data);
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
			$this->load->model('projetos/solic_fiscalizacao_audit_grupo_model');

			$cd_solic_fiscalizacao_audit_grupo = $this->input->post('cd_solic_fiscalizacao_audit_grupo', TRUE);
			$cd_usuario_grupo 				   = (is_array($this->input->post('cd_usuario_grupo', TRUE)) ? $this->input->post('cd_usuario_grupo', TRUE) : array());

			$args = array(
				'ds_grupo'         => $this->input->post('ds_grupo', TRUE),
				'ds_email_grupo'   => $this->input->post('ds_email_grupo', TRUE),
				'cd_usuario'       => $this->session->userdata('codigo')
			);

			### SALVA O GRUPO ###
			if(intval($cd_solic_fiscalizacao_audit_grupo) == 0)
			{
				$cd_solic_fiscalizacao_audit_grupo = $this->solic_fiscalizacao_audit_grupo_model->salvar($args);
			}
			else
			{
				$this->solic_fiscalizacao_audit_grupo_model->atualizar($cd_solic_fiscalizacao_audit_grupo, $args);
			}

			### SALVAR OS INTEGRANTES DO GRUPO ###
			if(count($cd_usuario_grupo) > 0)
			{
				$args['cd_usuario_grupo_u'] = implode(",", $cd_usuario_grupo);
				$args['cd_usuario_grupo_i'] = implode("),(", $cd_usuario_grupo);

				$this->solic_fiscalizacao_audit_grupo_model->salvar_integrantes_grupo($cd_solic_fiscalizacao_audit_grupo, $args);
			}
			else
			{
				$this->solic_fiscalizacao_audit_grupo_model->remover_integrantes_grupo($cd_solic_fiscalizacao_audit_grupo, $this->session->userdata('codigo'));
			}

			redirect('atividade/solic_fiscalizacao_audit_grupo', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}