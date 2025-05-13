<?php
class Divulgacao_grupo_mkt extends Controller
{
	function __construct()
	{
		parent::controller();
		CheckLogin();
	}

	private function permissao()
	{
    	if(gerencia_in(array('GTI', 'GCM')))
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
			$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$data['grupo'] = array(
				array('value' => 'I', 'text' => 'Importaчуo Email'),
				array('value' => 'P', 'text' => 'Importaчуo RE'),
				array('value' => 'C', 'text' => 'Configuraчуo')
			);

			$this->load->view('ecrm/divulgacao_grupo_mkt/index', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function listar($data = array())
	{
		$this->load->model('projetos/divulgacao_grupo_mkt_model');

		$args['grupo'] = $this->input->post('tp_grupo', true);

		$data['collection'] = $this->divulgacao_grupo_mkt_model->listar($args);
		
		$this->load->view('ecrm/divulgacao_grupo_mkt/index_result', $data);
	}

	public function cadastro($cd_divulgacao_grupo = 0)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$data['grupo'] = array(
				array('value' => 'I', 'text' => 'Importaчуo Email'),
				array('value' => 'P', 'text' => 'Importaчуo RE'),
				array('value' => 'C', 'text' => 'Configuraчуo')
			);

			if(intval($cd_divulgacao_grupo) == 0)
			{
				$data['row'] = array(
					'cd_divulgacao_grupo' => intval($cd_divulgacao_grupo),
					'ds_divulgacao_grupo' => '',
					'qt_registro'         => '',
					'tp_grupo'			  => ''
				);
			}
			else 
			{
				$data['row'] = $this->divulgacao_grupo_mkt_model->carrega($cd_divulgacao_grupo);
			}

			$this->load->view('ecrm/divulgacao_grupo_mkt/cadastro', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function salvar()
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');
			
			$cd_divulgacao_grupo = $this->input->post('cd_divulgacao_grupo', TRUE);
			$args = array(
				'ds_divulgacao_grupo' => $this->input->post('ds_divulgacao_grupo', TRUE),
				'cd_usuario'          => $this->session->userdata('codigo'),
				'tp_grupo'		      => $this->input->post('tp_grupo')
			);

			if($cd_divulgacao_grupo == 0)
			{
				if(trim($args['tp_grupo']) == 'I')
				{
					$args['sql'] = "
						SELECT NULL AS cd_plano,
						       NULL AS cd_empresa,
						       NULL AS cd_registro_empregado,
						       NULL AS seq_dependencia,
						       NULL AS nome,
						       lower(funcoes.remove_acento(ds_divulgacao_grupo_email)) AS email,
						       NULL AS email_profissional,
						       NULL AS re_cripto
						  FROM projetos.divulgacao_grupo_email
						 WHERE dt_exclusao         IS NULL
						   AND ds_divulgacao_grupo_email LIKE '%@%'
						   AND CHAR_LENGTH(ds_divulgacao_grupo_email) - CHAR_LENGTH(REPLACE(ds_divulgacao_grupo_email, '@', '')) = 1
						   AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}";
				}
				else if(trim($args['tp_grupo']) == 'P')
				{
					$args['sql'] = "
						SELECT p.cd_plano,
						       p.cd_empresa,
						       p.cd_registro_empregado,
						       p.seq_dependencia,
						       p.nome,
						       p.email,
						       p.email_profissional,
						       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
						  FROM projetos.divulgacao_grupo_participante g
						  JOIN participantes p 
						    ON p.cd_empresa            = g.cd_empresa
						   AND p.cd_registro_empregado = g.cd_registro_empregado
						   AND p.seq_dependencia       = g.seq_dependencia
						 WHERE g.dt_exclusao         IS NULL
						   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
						   AND g.cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}";
				}
				else
				{
					$args['sql'] = "
						SELECT p.cd_plano,
						       p.cd_empresa,
						       p.cd_registro_empregado,
						       p.seq_dependencia,
						       p.nome,
						       p.email,
						       p.email_profissional,
						       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto
						  FROM public.participantes p
						  JOIN public.titulares t 
						    ON t.cd_empresa            = p.cd_empresa
						   AND t.cd_registro_empregado = p.cd_registro_empregado
						   AND t.seq_dependencia       = p.seq_dependencia
						 WHERE p.dt_obito   IS NULL
						   AND p.cd_plano > 0
						   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%')
						   AND (
						   		p.cd_empresa IN (
							   		SELECT cd_empresa
		                              FROM projetos.divulgacao_grupo_empresa
		                             WHERE dt_exclusao IS NULL
		                               AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}
						   		)
						   		OR
						   		(SELECT COUNT(*)
		                           FROM projetos.divulgacao_grupo_empresa
		                          WHERE dt_exclusao IS NULL
		                            AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}) = 0
						   )
						   AND (
						   		p.cd_plano IN (
						   			SELECT cd_plano
	                              	  FROM projetos.divulgacao_grupo_plano
	                             	 WHERE dt_exclusao IS NULL
	                               	  AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}
						        )
						        OR
						   		(SELECT COUNT(*)
		                           FROM projetos.divulgacao_grupo_plano
		                          WHERE dt_exclusao IS NULL
		                            AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}) = 0
						   )
						   AND (
						   		projetos.participante_tipo(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (
						   			SELECT ds_tipo
	                              	  FROM projetos.divulgacao_grupo_tipo
	                             	 WHERE dt_exclusao IS NULL
	                               	  AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}
						        )
						        OR
						   		(SELECT COUNT(*)
		                           FROM projetos.divulgacao_grupo_tipo
		                          WHERE dt_exclusao IS NULL
		                            AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}) = 0
						   )
						   AND (
						   		UPPER(COALESCE(p.cidade,'')) IN (
						   			SELECT ds_cidade
	                              	  FROM projetos.divulgacao_grupo_cidade
	                             	 WHERE dt_exclusao IS NULL
	                               	  AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}
						        )
						        OR
						   		(SELECT COUNT(*)
		                           FROM projetos.divulgacao_grupo_cidade
		                          WHERE dt_exclusao IS NULL
		                            AND cd_divulgacao_grupo = {CD_DIVULGACAO_GRUPO}) = 0
						   )";
				}

				$cd_divulgacao_grupo = $this->divulgacao_grupo_mkt_model->salvar($args);
			}
			else
			{
				$this->divulgacao_grupo_mkt_model->atualizar($cd_divulgacao_grupo, $args);
			}

			if(trim($args['tp_grupo']) == 'I')
			{
	        	redirect('ecrm/divulgacao_grupo_mkt/importacao/'.$cd_divulgacao_grupo);
        	}
        	else if(trim($args['tp_grupo']) == 'P')
			{
	        	redirect('ecrm/divulgacao_grupo_mkt/importacao_participante/'.$cd_divulgacao_grupo);
        	}
        	else
        	{
        		redirect('ecrm/divulgacao_grupo_mkt/configuracao/'.$cd_divulgacao_grupo);
        	}
        }
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function excluir($cd_divulgacao_grupo = 0)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');
			$this->divulgacao_grupo_mkt_model->excluir($cd_divulgacao_grupo, $this->session->userdata('codigo'));

			redirect('ecrm/divulgacao_grupo_mkt');
		}
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function importacao($cd_divulgacao_grupo)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$data['anexo'] = array(
		 		'cd_divulgacao_grupo' => intval($cd_divulgacao_grupo),
		 		'arquivo'             => '',
		 		'arquivo_nome'        => ''
			);

			$data['row'] = $this->divulgacao_grupo_mkt_model->carrega($cd_divulgacao_grupo);
			$data['collection'] = $this->divulgacao_grupo_mkt_model->listar_email($cd_divulgacao_grupo);

			$this->load->view('ecrm/divulgacao_grupo_mkt/importacao', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function salvar_email()
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$arquivo = base_url().'up/grupo_email_mkt/'.$this->input->post('arquivo', true);
			$csv 	 = file($arquivo);
			$cd_divulgacao_grupo = $this->input->post('cd_divulgacao_grupo');

			$args['cd_usuario'] =  $this->session->userdata('codigo');

			foreach ($csv as $key => $item) 
			{
				if(trim($item) != '')
				{
					$args['ds_divulgacao_grupo_email'] = utf8_encode($item);

					$this->divulgacao_grupo_mkt_model->anexo_salvar($cd_divulgacao_grupo, $args);
				}
			}

			$this->divulgacao_grupo_mkt_model->atualizar_grupo($cd_divulgacao_grupo, $args);
			$this->divulgacao_grupo_mkt_model->atualiza_registro($cd_divulgacao_grupo);

			redirect('ecrm/divulgacao_grupo_mkt/importacao/'.$cd_divulgacao_grupo);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function excluir_email($cd_divulgacao_grupo,$cd_divulgacao_grupo_email)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');
			$this->divulgacao_grupo_mkt_model->excluir_email($cd_divulgacao_grupo_email, $this->session->userdata('codigo'));

			redirect('ecrm/divulgacao_grupo_mkt/importacao/'.$cd_divulgacao_grupo);

			$this->divulgacao_grupo_mkt_model->atualiza_qt_registro($cd_divulgacao_grupo);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function importacao_participante($cd_divulgacao_grupo)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$data['anexo'] = array(
		 		'cd_divulgacao_grupo' => intval($cd_divulgacao_grupo),
		 		'arquivo'             => '',
		 		'arquivo_nome'        => ''
			);

			$data['row'] = $this->divulgacao_grupo_mkt_model->carrega($cd_divulgacao_grupo);
			$data['collection'] = $this->divulgacao_grupo_mkt_model->listar_res($cd_divulgacao_grupo);

			$this->load->view('ecrm/divulgacao_grupo_mkt/importacao_participante', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function salvar_participante()
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$arquivo = base_url().'up/grupo_email_mkt/'.$this->input->post('arquivo', true);
			$csv 	 = file($arquivo);
			$cd_divulgacao_grupo = $this->input->post('cd_divulgacao_grupo');

			foreach ($csv as $key => $item) 
			{
				list($empresa, $re, $seq) = explode(';', $item);

				$args = array(
				 	'cd_empresa'            => $empresa,
				 	'cd_registro_empregado' => $re,
				 	'seq_dependencia'       => $seq,
				 	'cd_usuario'            => $this->session->userdata('codigo')
				);

				$this->divulgacao_grupo_mkt_model->participante_salvar($cd_divulgacao_grupo, $args);
			}

			$this->divulgacao_grupo_mkt_model->atualizar_grupo($cd_divulgacao_grupo, $args);
			$this->divulgacao_grupo_mkt_model->atualiza_registro($cd_divulgacao_grupo);

			redirect('ecrm/divulgacao_grupo_mkt/importacao_participante/'.$cd_divulgacao_grupo);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function atualiza_registro($cd_divulgacao_grupo, $tipo)
	{
		if($this->permissao())
        {
        	$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$this->divulgacao_grupo_mkt_model->atualiza_registro($cd_divulgacao_grupo);
			
			if(trim($tipo) == 'I')
			{
				redirect('ecrm/divulgacao_grupo_mkt/importacao/'.intval($cd_divulgacao_grupo), 'refresh');
			}
			else if(trim($tipo) == 'P')
			{
				redirect('ecrm/divulgacao_grupo_mkt/importacao_participante/'.intval($cd_divulgacao_grupo), 'refresh');
			}
			else if(trim($tipo) == 'C')
			{
				redirect('ecrm/divulgacao_grupo_mkt/configuracao/'.intval($cd_divulgacao_grupo), 'refresh');
			}
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function excluir_participante($cd_divulgacao_grupo,$cd_divulgacao_grupo_participante)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');
			$this->divulgacao_grupo_mkt_model->excluir_participante($cd_divulgacao_grupo_participante, $this->session->userdata('codigo'));

			redirect('ecrm/divulgacao_grupo_mkt/importacao_participante/'.$cd_divulgacao_grupo);

			$this->divulgacao_grupo_mkt_model->atualiza_qt_registro($cd_divulgacao_grupo);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

    public function configuracao($cd_divulgacao_grupo)
	{
		if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');

			$data['row'] = $this->divulgacao_grupo_mkt_model->carrega($cd_divulgacao_grupo);
			$data['collection'] = $this->divulgacao_grupo_mkt_model->listar_participantes($data['row']['qr_sql']);

			$data['empresa']   = $this->divulgacao_grupo_mkt_model->get_empresa();
			$data['plano']     = $this->divulgacao_grupo_mkt_model->get_plano();
			$data['tipo']      = array(
				array('value' => 'ATIV', 'text' => 'Ativo'),
				array('value' => 'APOS', 'text' => 'Aposentado'),
				array('value' => 'PENS', 'text' => 'Pensionista'),
				array('value' => 'EXAU', 'text' => 'Ex-Autсrquico'),
				array('value' => 'AUXD', 'text' => 'Auxilio Doenчa')
			);
			
			$data['row']['cd_empresa'] = array();

			foreach ($this->divulgacao_grupo_mkt_model->carrega_empresa($cd_divulgacao_grupo) as $key => $item) 
			{
				$data['row']['cd_empresa'][$key] = $item['cd_empresa'];
			}

			$data['row']['cd_plano'] = array();

			foreach ($this->divulgacao_grupo_mkt_model->carrega_plano($cd_divulgacao_grupo) as $key => $item) 
			{
				$data['row']['cd_plano'][$key] = $item['cd_plano'];
			}

			$data['row']['ds_tipo'] = array();

			foreach ($this->divulgacao_grupo_mkt_model->carrega_tipo($cd_divulgacao_grupo) as $key => $item) 
			{
				$data['row']['ds_tipo'][$key] = $item['ds_tipo'];
			}

			$cidade = array();

			$data['row']['ds_cidade'] = '';

			foreach ($this->divulgacao_grupo_mkt_model->carrega_cidade($cd_divulgacao_grupo) as $key => $item) 
			{
				$cidade[] = $item['ds_cidade'];
			}

			$data['row']['ds_cidade'] = implode($cidade, ';');

			$this->load->view('ecrm/divulgacao_grupo_mkt/configuracao', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

    public function salvar_configuracao()
    {
    	$this->load->model('projetos/divulgacao_grupo_mkt_model');

    	$cd_divulgacao_grupo = $this->input->post('cd_divulgacao_grupo', TRUE);

    	$ds_cidade = $this->input->post('ds_cidade', TRUE);

		$args = array(
			'cd_empresa' 	=> (is_array($this->input->post('cd_empresa', TRUE)) ? $this->input->post('cd_empresa', TRUE) : array()),
			'cd_plano'   	=> (is_array($this->input->post('cd_plano', TRUE)) ? $this->input->post('cd_plano', TRUE) : array()),
			'ds_tipo'	 	=> (is_array($this->input->post('ds_tipo')) ? $this->input->post('ds_tipo', TRUE) : array()),
			'ds_cidade'  	=> (trim($ds_cidade) != '' ? explode(';', $ds_cidade) : array()),
			'cd_usuario' 	=> $this->session->userdata('codigo'),
		);

		$this->divulgacao_grupo_mkt_model->configuracao_salvar($cd_divulgacao_grupo, $args);
		$this->divulgacao_grupo_mkt_model->atualiza_registro($cd_divulgacao_grupo);

		redirect('ecrm/divulgacao_grupo_mkt/configuracao/'.$cd_divulgacao_grupo);
    }

    public function excluir_grupo($cd_divulgacao_grupo)
    {
    	if($this->permissao())
		{
			$this->load->model('projetos/divulgacao_grupo_mkt_model');
			$this->divulgacao_grupo_mkt_model->excluir_grupo($cd_divulgacao_grupo, $this->session->userdata('codigo'));

			redirect('ecrm/divulgacao_grupo_mkt/index/'.$cd_divulgacao_grupo);
		}
		else
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
    }
}
?>