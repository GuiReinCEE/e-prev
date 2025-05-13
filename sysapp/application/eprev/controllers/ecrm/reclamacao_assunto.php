<?php
class reclamacao_assunto extends controller
{
	function __construct()
	{
		parent::controller();
		CheckLogin();
	}

	public function permissao()
	{
		if(gerencia_in(array('GCM')))
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
		if($this->permissao())
		{
			$this->load->view('ecrm/reclamacao_assunto/index');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}

	}

	public function listar()
	{
		$this->load->model('projetos/reclamacao_assunto_model');

		$data['collection'] = $this->reclamacao_assunto_model->listar();

		$this->load->view('ecrm/reclamacao_assunto/index_result', $data);
	}

	public function cadastro($cd_reclamacao_assunto = 0)
	{
		if($this->permissao())
		{
			if(intval($cd_reclamacao_assunto) == 0)
			{
				$data['row'] = array(
					'cd_reclamacao_assunto' => intval($cd_reclamacao_assunto),
					'ds_reclamacao_assunto' => ''
				);
			}
			else 
			{
				$this->load->model('projetos/reclamacao_assunto_model');

				$data['row'] = $this->reclamacao_assunto_model->carrega($cd_reclamacao_assunto);
			}

			$this->load->view('ecrm/reclamacao_assunto/cadastro', $data);
			
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
			$this->load->model('projetos/reclamacao_assunto_model');

			$cd_reclamacao_assunto = $this->input->post('cd_reclamacao_assunto', TRUE);	

			$args = array(
				'ds_reclamacao_assunto' => $this->input->post('ds_reclamacao_assunto', TRUE),
				'cd_usuario'            => $this->session->userdata('codigo')
			);

			if($cd_reclamacao_assunto == 0)
			{
				$this->reclamacao_assunto_model->salvar($args);
			}
			else
			{
				$this->reclamacao_assunto_model->atualizar($cd_reclamacao_assunto, $args);
			}

			redirect('ecrm/reclamacao_assunto');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}

	}

	public function excluir($cd_reclamacao_assunto)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/reclamacao_assunto_model');

			$this->reclamacao_assunto_model->excluir($cd_reclamacao_assunto, $this->session->userdata('codigo'));

			redirect('ecrm/reclamacao_assunto');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}

	}
}


?>