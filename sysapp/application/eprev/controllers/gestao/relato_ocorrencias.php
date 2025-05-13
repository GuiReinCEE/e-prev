<?php
class Relato_ocorrencias extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao($cd_relato_ocorrencias = 0)
	{
		$row = $this->relato_ocorrencias_model->get_permissao($cd_relato_ocorrencias, $this->session->userdata('codigo'));

		if(intval($row['qt_relato']) > 0)
		{
			return TRUE;
		}
		else if(trim($this->session->userdata('indic_12')) == '*')
		{
			return TRUE;
		}
		else if(trim($this->session->userdata('codigo')) == 251)
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
		$data['drop'] = array(
			array('value' => 'S', 'text' => 'Sim'),
			array('value' => 'N', 'text' => 'Não')
		);

		$this->load->view('gestao/relato_ocorrencias/index', $data);
	}

	public function listar()
	{
		$this->load->model('gestao/relato_ocorrencias_model');

		$args = array(
			'dt_inclusao_ini' 	 => $this->input->post('dt_inclusao_ini', TRUE),
			'dt_inclusao_fim'	 => $this->input->post('dt_inclusao_fim', TRUE),
			'dt_verificacao_ini' => $this->input->post('dt_verificacao_ini', TRUE),
			'dt_verificacao_fim' => $this->input->post('dt_verificacao_fim', TRUE),
			'fl_verificado' 	 => $this->input->post('fl_verificado', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->relato_ocorrencias_model->listar($this->session->userdata('codigo'), $args);

		$this->load->view('gestao/relato_ocorrencias/index_result', $data);
	}

	public function cadastro($cd_relato_ocorrencias = 0)
	{
		$this->load->model('gestao/relato_ocorrencias_model');

		if(intval($cd_relato_ocorrencias) == 0 OR $this->get_permissao($cd_relato_ocorrencias))
		{
			if(intval($cd_relato_ocorrencias) == 0)
			{
				$data['row'] = array(
					'cd_relato_ocorrencias'  => 0,
					'ds_relato_ocorrencias'  => '',
					'cd_usuario_inclusao'    => 0,
					'dt_verificacao' 		 => '',
					'ds_usuario_verificacao' => '',
					'ds_verificacao' 		 => ''
				);
			}
			else
			{
				$data['row'] = $this->relato_ocorrencias_model->carrega($cd_relato_ocorrencias);
			}

			$data['fl_membro_comite'] = $this->get_permissao();
			$data['cd_usuario'] 	  = $this->session->userdata('codigo');

			$this->load->view('gestao/relato_ocorrencias/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		$this->load->model('gestao/relato_ocorrencias_model');

		$cd_relato_ocorrencias = $this->input->post('cd_relato_ocorrencias', TRUE);

		if(intval($cd_relato_ocorrencias) == 0 OR $this->get_permissao($cd_relato_ocorrencias))
		{
			$args = array(
				'ds_relato_ocorrencias' => $this->input->post('ds_relato_ocorrencias', TRUE),
				'cd_usuario' 			=> $this->session->userdata('codigo')
			);

			if(intval($cd_relato_ocorrencias) == 0)
			{
				$cd_relato_ocorrencias = $this->relato_ocorrencias_model->salvar($args);

				$this->envia_email_comite($cd_relato_ocorrencias);
			}
			else
			{
				$this->relato_ocorrencias_model->atualizar($cd_relato_ocorrencias, $args);
			}

			redirect('gestao/relato_ocorrencias/cadastro/'.$cd_relato_ocorrencias, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    private function envia_email_comite($cd_relato_ocorrencias)
    {
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 389;

        $email = $this->eventos_email_model->carrega($cd_evento);
      
        $tags = '[LINK]';
        $subs = site_url('gestao/relato_ocorrencias/cadastro/'.intval($cd_relato_ocorrencias));

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Relato de Ocorrências',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);        
    }

	public function salvar_verificacao()
	{
		$this->load->model('gestao/relato_ocorrencias_model');

		if($this->get_permissao())
		{
			$cd_relato_ocorrencias = $this->input->post('cd_relato_ocorrencias', TRUE);

			$args = array(
				'dt_verificacao' => $this->input->post('dt_verificacao', TRUE),
				'ds_verificacao' => $this->input->post('ds_verificacao', TRUE),
				'cd_usuario' 	 => $this->session->userdata('codigo')
			);

			$this->relato_ocorrencias_model->salvar_verificacao($cd_relato_ocorrencias, $args);

			redirect('gestao/relato_ocorrencias/cadastro/'.$cd_relato_ocorrencias, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function anexo($cd_relato_ocorrencias)
	{
		$this->load->model('gestao/relato_ocorrencias_model');

		if(intval($cd_relato_ocorrencias) == 0 OR $this->get_permissao($cd_relato_ocorrencias))
		{
			$data['row'] = $this->relato_ocorrencias_model->carrega($cd_relato_ocorrencias);

			$data['collection'] = $this->relato_ocorrencias_model->lista_anexo($cd_relato_ocorrencias);

			$data['fl_membro_comite'] = $this->get_permissao();
			$data['cd_usuario'] 	  = $this->session->userdata('codigo');

			$this->load->view('gestao/relato_ocorrencias/anexo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar_anexo()
	{
        $this->load->model('gestao/relato_ocorrencias_model');

        $cd_relato_ocorrencias = $this->input->post('cd_relato_ocorrencias', TRUE);

		if(intval($cd_relato_ocorrencias) == 0 OR $this->get_permissao($cd_relato_ocorrencias))
        {
    		$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

    		$cd_usuario = $this->session->userdata('codigo');
    		
    		if($qt_arquivo > 0)
    		{
    			$nr_conta = 0;
    			
    			while($nr_conta < $qt_arquivo)
    			{    				
    				$args['arquivo_nome'] = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
    				$args['arquivo']      = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
    				
    				$this->relato_ocorrencias_model->salvar_anexo($cd_relato_ocorrencias, $cd_usuario, $args);
    				
    				$nr_conta++;
    			}
    		}
    		
    		redirect('gestao/relato_ocorrencias/anexo/'.intval($cd_relato_ocorrencias), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function excluir_anexo($cd_relato_ocorrencias, $cd_relato_ocorrencias_anexo)
	{
        $this->load->model('gestao/relato_ocorrencias_model');

		if(intval($cd_relato_ocorrencias) == 0 OR $this->get_permissao($cd_relato_ocorrencias))
        {
    		$this->relato_ocorrencias_model->excluir_anexo($cd_relato_ocorrencias_anexo, $this->session->userdata('codigo'));
    		
    		redirect('gestao/relato_ocorrencias/anexo/'.intval($cd_relato_ocorrencias), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}		

}