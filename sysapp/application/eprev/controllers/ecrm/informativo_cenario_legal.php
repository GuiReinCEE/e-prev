<?php
class Informativo_cenario_legal extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GC', 'GFC')))
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
		if($this->get_permissao())
		{
			$this->load->view('ecrm/informativo_cenario_legal/index');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
	
	public function listar()
    {
		$this->load->model('projetos/cenario_model');

		$args = array(
			'nome'      => $this->input->post('nome', TRUE),
			'cd_edicao' => $this->input->post('cd_edicao', TRUE),
			'dt_ini'    => $this->input->post('dt_ini', TRUE),
			'dt_fim'    => $this->input->post('dt_fim', TRUE),
			'conteudo'  => $this->input->post('conteudo', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->cenario_model->listar($args);

		foreach($data['collection'] as $key => $item) 
		{
			$conteudo = $this->cenario_model->listar_conteudo($item['cd_edicao']);

			$data['collection'][$key]['conteudo'] = array();

			foreach($conteudo  as $key2 => $item2)
			{
				$link = anchor(site_url('ecrm/informativo_cenario_legal/conteudo_cadastro/'.$item['cd_edicao'].'/'.$item2['cd_cenario']), $item2['titulo']);

				$data['collection'][$key]['conteudo'][] = $link.(trim($item2['dt_cancelamento']) != '' ? ' <span class="label label-important">Cancelada : '.$item2['dt_cancelamento'].'</span>' : '');
			}	
		}

		$this->load->view('ecrm/informativo_cenario_legal/index_result', $data);
    }

	public function cadastro($cd_edicao = 0)
    {
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			if(intval($cd_edicao) == 0)
			{
				$row = $this->cenario_model->carrega_anterior();

				$data['row'] = array(
					'cd_edicao'   => intval($cd_edicao),
					'dt_edicao'   => '',
					'dt_exclusao' => '',
					'tit_capa'    => '',
					'texto_capa'  => (count($row) > 0 ? trim($row['texto_capa']) : '')
				);						
			}
			else
			{
				$data['row'] = $this->cenario_model->carrega(intval($cd_edicao));
			}
			
			$this->load->view('ecrm/informativo_cenario_legal/cadastro',$data);
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
			$this->load->model('projetos/cenario_model');

			$cd_edicao = $this->input->post('cd_edicao', TRUE);

			$args = array(
				'tit_capa'   => $this->input->post('tit_capa', TRUE),
				'texto_capa' => $this->input->post('texto_capa', TRUE),
				'cd_usuario' => $this->session->userdata('codigo')
			);

			if(intval($cd_edicao) == 0)
			{
				$cd_edicao = $this->cenario_model->salvar($args);
			}
			else
			{
				$this->cenario_model->atualizar($cd_edicao, $args);
			}
			
			redirect('ecrm/informativo_cenario_legal/cadastro/'.$cd_edicao, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	

    public function excluir($cd_edicao)
    {
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$this->cenario_model->excluir($cd_edicao, $this->session->userdata('codigo'));
			
			redirect('ecrm/informativo_cenario_legal', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }	
	
	public function conteudo($cd_edicao)
    {
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$data['row'] = $this->cenario_model->carrega(intval($cd_edicao));

			$data['collection'] = $this->cenario_model->listar_conteudo($cd_edicao);

			$this->load->view('ecrm/informativo_cenario_legal/conteudo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
	
	public function conteudo_cadastro($cd_edicao, $cd_cenario = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$data = array(
				'edicao'      => $this->cenario_model->carrega(intval($cd_edicao)),
				'legislacao'  => $this->cenario_model->listar_lgin($cd_cenario),   
				'secao'       => $this->cenario_model->get_secao(),
				'divisao'     => $this->cenario_model->get_divisao($cd_cenario),
				'pertinencia' => $this->cenario_model->get_pertinencia($cd_cenario)
			);

			$data['cenario_gerencia'] = array();

			if(intval($cd_cenario) == 0)
			{
				$data['row'] = array(
					'cd_cenario'            => intval($cd_cenario),
					'cd_edicao'             => intval($cd_edicao),
					'titulo'                => '',
					'referencia'            => '',
					'fonte'                 => 'Site Fiscodata',
					'cd_secao'              => '',
					'cd_cenario_referencia' => '',
					'conteudo'              => '',
					'link1'                 => 'http://www.fiscodata.com.br',
					'link2'                 => '',
					'link3'                 => '',
					'link4'                 => '',
					'pertinencia'           => '',
					'dt_prevista'           => '',
					'dt_legal'              => '',
					'dt_implementacao'      => '',
					'dt_exclusao'           => '',
					'dt_cancelamento'       => '',
					'tl_area_enviar'        => 0,
					'arquivo'               => '',
					'arquivo_nome'          => ''
				);
			}
			else
			{
				$data['row'] = $this->cenario_model->carrega_conteudo(intval($cd_cenario));
				
				foreach($this->cenario_model->cenario_gerencia(intval($cd_cenario)) as $item)
				{
					$data['cenario_gerencia'][] = $item['cd_gerencia'];
				}
			}
			
			$this->load->view('ecrm/informativo_cenario_legal/conteudo_cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function salvar_conteudo()
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$cenario_gerencia = $this->input->post('gerencia', TRUE);

			$cd_cenario = $this->input->post('cd_cenario', TRUE);

			$args = array(
				'cd_edicao'             => $this->input->post('cd_edicao', TRUE),
				'titulo'                => $this->input->post('titulo', TRUE),
				'referencia'            => $this->input->post('referencia', TRUE),
				'fonte'                 => $this->input->post('fonte', TRUE),
				'cd_secao'              => $this->input->post('cd_secao', TRUE),
				'conteudo'              => $this->input->post('conteudo_pagina', TRUE),
				'link1'                 => $this->input->post('link1', TRUE),
				'link2'                 => $this->input->post('link2', TRUE),
				'link3'                 => $this->input->post('link3', TRUE),
				'cd_cenario_referencia' => $this->input->post('cd_cenario_referencia', TRUE),
				'link4'                 => $this->input->post('link4', TRUE),
				'pertinencia'           => $this->input->post('pertinencia', TRUE),
				'dt_prevista'           => $this->input->post('dt_prevista', TRUE),
				'dt_legal'              => $this->input->post('dt_legal', TRUE),
				'dt_implementacao'      => $this->input->post('dt_implementacao', TRUE),
				'arquivo'               => $this->input->post('arquivo', TRUE),
				'arquivo_nome'          => $this->input->post('arquivo_nome', TRUE),
				'cenario_gerencia'      => (is_array($cenario_gerencia) ? $cenario_gerencia : array()),
				'cd_usuario'            => $this->session->userdata('codigo')
			);  

			if(intval($cd_cenario) == 0)
			{
				$cd_cenario = $this->cenario_model->salvar_conteudo($args);
			}
			else
			{
				$this->cenario_model->atualizar_conteudo($cd_cenario, $args);
			}

			if(trim($args['arquivo_nome']) != '' AND (intval($args['pertinencia']) == 1 OR intval($args['pertinencia']) == 2))
            {
            	$this->load->plugin('encoding_pi');
            	
            	$cenario = $this->cenario_model->carrega_conteudo(
					intval($cd_cenario)
				);

				$caminho_cenario = '../eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CENARIO-LEGAL/'.$cenario['ds_ano_edicao'];

	            if(!is_dir($caminho_cenario))
	            {
	                mkdir($caminho_cenario, 0777);
	            }

	            $caminho_cenario .= '/'.$cenario['ds_mes_edicao'].' - '.fixUTF8(mes_extenso($cenario['ds_mes_edicao']));

	            if(!is_dir($caminho_cenario))
	            {
	                mkdir($caminho_cenario, 0777);
	            }

	            copy('../cieprev/up/cenario/'.$args['arquivo'], $caminho_cenario.'/'.fixUTF8($args['arquivo_nome']));
			}

			redirect('ecrm/informativo_cenario_legal/conteudo_cadastro/'.$args['cd_edicao'].'/'.$cd_cenario, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function excluir_conteudo($cd_edicao, $cd_cenario)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$this->cenario_model->excluir_conteudo($cd_cenario, $this->session->userdata('codigo'));
			
			redirect('ecrm/informativo_cenario_legal/conteudo/'.$cd_edicao, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function cancelar_conteudo($cd_edicao, $cd_cenario)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$this->cenario_model->cancelar_conteudo($cd_cenario, $this->session->userdata('codigo'));
			
			redirect('ecrm/informativo_cenario_legal/conteudo/'.$cd_edicao, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function anexo($cd_edicao, $cd_cenario)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$data = array(
				'edicao'     => $this->cenario_model->carrega(intval($cd_edicao)),
				'cenario'    => $this->cenario_model->carrega_conteudo(intval($cd_cenario)),
				'collection' => $this->cenario_model->listar_anexo(intval($cd_cenario))
			);
			
			$this->load->view('ecrm/informativo_cenario_legal/anexo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function salvar_anexo()
	{
		if($this->get_permissao())
		{
	        $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

	        $cd_cenario = $this->input->post('cd_cenario', TRUE);
            $cd_edicao  = $this->input->post('cd_edicao', TRUE);
	        
	        if($qt_arquivo > 0)
			{
				$this->load->model('projetos/cenario_model');

				$nr_conta = 0;

				while($nr_conta < $qt_arquivo)
				{
					$args = array(
						'arquivo_nome' => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
						'arquivo'      => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
						'cd_usuario'   => $this->session->userdata('codigo')
					);
					
					$this->cenario_model->salvar_anexo($cd_cenario, $args);
					
					$nr_conta++;
				}
			}
			
			redirect('ecrm/informativo_cenario_legal/anexo/'.intval($cd_edicao).'/'.intval($cd_cenario), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
	
	public function excluir_anexo($cd_edicao, $cd_cenario, $cd_cenario_anexo)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$this->cenario_model->excluir_anexo($cd_cenario_anexo, $this->session->userdata('codigo'));
			
			redirect('ecrm/informativo_cenario_legal/anexo/'.intval($cd_edicao).'/'.$cd_cenario, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    public function enviar_email($cd_edicao)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$this->cenario_model->edicao_envia_email($cd_edicao, $this->session->userdata('codigo'));

			redirect('ecrm/informativo_cenario_legal/conteudo/'.intval($cd_edicao), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function enviar_atividade($cd_edicao, $cd_cenario)
	{
		if($this->get_permissao())
		{
			$this->load->model('projetos/cenario_model');

			$this->cenario_model->enviar_atividade($cd_cenario, $cd_edicao, $this->session->userdata('codigo'));

			redirect('ecrm/informativo_cenario_legal/conteudo_cadastro/'.intval($cd_edicao).'/'.intval($cd_cenario), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function capa($cd_edicao = 0)
	{
		$this->load->model('projetos/cenario_model');

		if(intval($cd_edicao) == 0)
		{
			$edicao = $this->cenario_model->get_ultima_edicao();

			$cd_edicao = $edicao['cd_edicao'];
		}

		$data['edicao'] = $this->cenario_model->carrega($cd_edicao);
		
		$data['collection'] = $this->cenario_model->get_legislacao($cd_edicao);

		foreach($data['collection'] as $key => $item)
		{
			$cenario_gerencia = array();
			
			foreach ($this->cenario_model->cenario_gerencia($item['cd_cenario']) as  $item2) 
			{
				$cenario_gerencia[] = $item2['cd_gerencia'];
			}

		    $data['collection'][$key]['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$this->load->view('ecrm/informativo_cenario_legal/capa', $data);
	}
	
	public function ponto_vista($cd_edicao = 0)
	{
		$this->load->model('projetos/cenario_model');

		if(intval($cd_edicao) == 0)
		{
			$edicao = $this->cenario_model->get_ultima_edicao();

			$cd_edicao = $edicao['cd_edicao'];
		}
		
		$data['edicao'] = $this->cenario_model->carrega($cd_edicao);
		
		$data['ponto_vista'] = $this->cenario_model->get_ponto_vista($cd_edicao);
	
		$data['collection'] = $this->cenario_model->get_legislacao($cd_edicao);

		foreach($data['collection'] as $key => $item)
		{
			$cenario_gerencia = array();
			
			foreach ($this->cenario_model->cenario_gerencia($item['cd_cenario']) as  $item2) 
			{
				$cenario_gerencia[] = $item2['cd_gerencia'];
			}

		    $data['collection'][$key]['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$this->load->view('ecrm/informativo_cenario_legal/ponto_vista', $data);
	}
	
	public function legislacao($cd_edicao = 0, $cd_cenario = 0)
	{
		$this->load->model('projetos/cenario_model');

		if(intval($cd_edicao) == 0)
		{
			$edicao = $this->cenario_model->get_ultima_edicao();

			$cd_edicao = $edicao['cd_edicao'];
		}
		
		$data['edicao'] = $this->cenario_model->carrega($cd_edicao);
		
		if(intval($cd_cenario) == 0)
		{
			$cenario = $this->cenario_model->get_ultimo_cenario($cd_edicao);
			
			$cd_cenario = $cenario['cd_cenario'];
		}
				
		$data['legislacao'] = $this->cenario_model->get_conteudo_legislacao($cd_cenario);

		if(count($data['legislacao']) > 0)
		{
			$cenario_gerencia = array();
				
			foreach($this->cenario_model->cenario_gerencia($cd_cenario) as $item)
			{
				$cenario_gerencia[] = $item['cd_gerencia'];
			}
			
			$data['legislacao']['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$data['collection_anexo'] = $this->cenario_model->listar_anexo($cd_cenario);
		
		$data['collection'] = $this->cenario_model->get_legislacao($cd_edicao);
	
		foreach($data['collection'] as $key => $item)
		{
			$cenario_gerencia = array();
			
			foreach ($this->cenario_model->cenario_gerencia($item['cd_cenario']) as  $item2) 
			{
				$cenario_gerencia[] = $item2['cd_gerencia'];
			}

		    $data['collection'][$key]['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$this->load->view('ecrm/informativo_cenario_legal/legislacao', $data);
	}
	
	public function agenda($cd_edicao = 0)
	{
		$this->load->model('projetos/cenario_model');

		if(intval($cd_edicao) == 0)
		{
			$edicao = $this->cenario_model->get_ultima_edicao();

			$cd_edicao = $edicao['cd_edicao'];
		}
		
		$data['edicao'] = $this->cenario_model->carrega($cd_edicao);
		
		$data['agenda'] = $this->cenario_model->get_agenda($cd_edicao);
	
		$data['collection'] = $this->cenario_model->get_legislacao($cd_edicao);
	
		foreach($data['collection'] as $key => $item)
		{
			$cenario_gerencia = array();
			
			foreach ($this->cenario_model->cenario_gerencia($item['cd_cenario']) as  $item2) 
			{
				$cenario_gerencia[] = $item2['cd_gerencia'];
			}

		    $data['collection'][$key]['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$this->load->view('ecrm/informativo_cenario_legal/agenda', $data);
	}
	
	public function edicoes($cd_edicao = 0)
	{
		$this->load->model('projetos/cenario_model');

		if(intval($cd_edicao) == 0)
		{
			$edicao = $this->cenario_model->get_ultima_edicao();

			$cd_edicao = $edicao['cd_edicao'];
		}
		
		$data['edicao'] = $this->cenario_model->carrega($cd_edicao);
		
		$data['edicoes'] = $this->cenario_model->get_edicoes();
	
		$data['collection'] = $this->cenario_model->get_legislacao($cd_edicao);
	
		foreach($data['collection'] as $key => $item)
		{
			$cenario_gerencia = array();
			
			foreach ($this->cenario_model->cenario_gerencia($item['cd_cenario']) as  $item2) 
			{
				$cenario_gerencia[] = $item2['cd_gerencia'];
			}

		    $data['collection'][$key]['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$this->load->view('ecrm/informativo_cenario_legal/edicoes', $data);
	}

	public function email_anexo($cd_edicao, $cd_cenario)
    {
		if($this->get_permissao())
		{
			 $this->load->model(array(
                'projetos/eventos_email_model',
                'projetos/cenario_model'
            ));

            $cd_evento = 272;

            $email       = $this->eventos_email_model->carrega($cd_evento);
			$cenario     = $this->cenario_model->carrega_conteudo(intval($cd_cenario));
			$envia_email = '';

			$link = '';

		  	if(trim($cenario['cd_secao']) == 'PVST')
		  	{
		  		$link = site_url('ecrm/informativo_cenario_legal/ponto_vista/'.$cenario['cd_edicao']);
		  	}
		  	else if(trim($cenario['cd_secao']) == 'AGEN')
		  	{
		  		$link = site_url('ecrm/informativo_cenario_legal/agenda/'.$cenario['cd_edicao']);
		  	}
		  	else if(trim($cenario['cd_secao']) == 'LGIN')
		  	{
		  		$link = site_url('ecrm/informativo_cenario_legal/legislacao/'.$cenario['cd_edicao'].'/'.$cenario['cd_cenario']);
		  	}
		  	else
		  	{
		  		$link = site_url('ecrm/informativo_cenario_legal/edicoes/'.$cenario['cd_edicao']);
		  	}
			
		  	$cenario_gerencia = $this->cenario_model->cenario_gerencia(intval($cd_cenario));

		  	if(intval($cenario['pertinencia']) == 3)
		  	{
		  		$envia_email = 'todos@eletroceee.com.br';
		  	}
		  	else
		  	{
				foreach($cenario_gerencia as $key => $item)
				{
					$envia_email .= str_replace(".", "", strtolower($item['cd_gerencia'])).'@eletroceee.com.br'.(isset($cenario_gerencia[($key+1)]) ? ';' : '');
				}
		  	}

            $texto = str_replace('[LINK]', $link, $email['email']);           

            $cd_usuario = $this->session->userdata('codigo');
            
            $args = array(
                'de'      => 'Cenário Legal',
                'assunto' => str_replace('[TITULO_CENARIO]', $cenario['titulo'], $email['assunto']),
                'para'    => $envia_email,
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );
        
            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            redirect('ecrm/informativo_cenario_legal/anexo/'.intval($cd_edicao).'/'.intval($cd_cenario), 'refresh');
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
	

	public function consulta_normativo($cd_edicao = '')
	{
		$data['cd_edicao'] = $cd_edicao;

		$this->load->view('ecrm/informativo_cenario_legal/consulta_normativo', $data);
	}

	public function consulta_normativo_listar()
    {
		$this->load->model('projetos/cenario_model');

		$args = array(
			'nome'      => $this->input->post('nome', TRUE),
			'cd_edicao' => $this->input->post('cd_edicao', TRUE),
			'dt_ini'    => $this->input->post('dt_ini', TRUE),
			'dt_fim'    => $this->input->post('dt_fim', TRUE),
			'conteudo'  => $this->input->post('conteudo', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->cenario_model->consulta_normativo_listar($args);

		$this->load->view('ecrm/informativo_cenario_legal/consulta_normativo_result', $data);
    }

    public function normativo($cd_edicao = 0, $cd_cenario = 0)
	{
		$this->load->model('projetos/cenario_model');

		if(intval($cd_edicao) == 0)
		{
			$edicao = $this->cenario_model->get_ultima_edicao();

			$cd_edicao = $edicao['cd_edicao'];
		}
		
		$data['edicao'] = $this->cenario_model->carrega($cd_edicao);
		
		if(intval($cd_cenario) == 0)
		{
			$cenario = $this->cenario_model->get_ultimo_cenario($cd_edicao);
			
			$cd_cenario = $cenario['cd_cenario'];
		}
				
		$data['legislacao'] = $this->cenario_model->get_conteudo_legislacao($cd_cenario);

		if(count($data['legislacao']) > 0)
		{
			$cenario_gerencia = array();
				
			foreach($this->cenario_model->cenario_gerencia($cd_cenario) as $item)
			{
				$cenario_gerencia[] = $item['cd_gerencia'];
			}
			
			$data['legislacao']['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$data['collection_anexo'] = $this->cenario_model->listar_anexo($cd_cenario);
		
		$data['collection'] = $this->cenario_model->get_legislacao($cd_edicao);
	
		foreach($data['collection'] as $key => $item)
		{
			$cenario_gerencia = array();
			
			foreach ($this->cenario_model->cenario_gerencia($item['cd_cenario']) as  $item2) 
			{
				$cenario_gerencia[] = $item2['cd_gerencia'];
			}

		    $data['collection'][$key]['gerencia'] = implode(', ', $cenario_gerencia);
		}
		
		$this->load->view('ecrm/informativo_cenario_legal/normativo', $data);
	}
}
?>