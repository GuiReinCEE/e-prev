<?php
class Processo extends Controller
{	
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();	
	}

	private function fl_permissao()
	{
		#COMITÊ DE QUALIDAE
		if($this->session->userdata('indic_12') == '*')
		{
			return true;
		}
		#ADMINISTRADORES DO E-PREV
		else if($this->session->userdata('indic_05') == 'S')
		{
			return true;
		}
    	#ADRIANA NOBRE NUNES
    	else if($this->session->userdata('codigo') == 26)
    	{
    		return true;
    	}
    	#RAQUEL CRISTIANE RODRIGUES RAMOS
    	else if($this->session->userdata('codigo') == 515)
    	{
    		return true;
    	}
		else
		{
			return false;
		}
	}

	private function fl_permissao_revisao()
	{
		#COMITÊ DE QUALIDAE
		if($this->session->userdata('indic_12') == '*')
		{
			return true;
		}
		#ADMINISTRADORES DO E-PREV
		else if($this->session->userdata('indic_05') == 'S')
		{
			return true;
		}
    	#ADRIANA NOBRE NUNES
    	else if($this->session->userdata('codigo') == 26)
    	{
    		return true;
    	}
    	#RAQUEL CRISTIANE RODRIGUES RAMOS
    	else if($this->session->userdata('codigo') == 515)
    	{
    		return true;
    	}
		#GERENTES
		else if($this->session->userdata('tipo') == 'G')
		{
			return true;
		}
		#SUBGERENTES
		else if($this->session->userdata('indic_01') == 'S') 
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
		if($this->fl_permissao())
		{			
			$this->load->view('gestao/processo/index');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}        
	}

	public function listar()
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$args = array(
				'fl_vigente'              => $this->input->post('fl_vigente', TRUE),
				'fl_versao_it'            => $this->input->post('fl_versao_it', TRUE),
				'cd_gerencia_responsavel' => ''
			);

			manter_filtros($args);

			$data['collection'] = $this->processos_model->listar($args);
			
			$this->load->view('gestao/processo/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 		
	}

	public function cadastro($cd_processo = 0)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	
			
			$data['responsavel'] = $this->processos_model->get_responsavel();
					
			$data['gerencia_envolvida'] = array();

			if(intval($cd_processo) == 0)
			{
				$data['row'] = array(
					'cd_processo'     => intval($cd_processo),
					'dt_ini_vigencia' => '',
					'dt_fim_vigencia' => '',
					'procedimento'    => '',
					'cod_responsavel' => '',
					'fl_versao_it'    => '',
					'envolvidos'      => array()
				);
			}
			else
			{
				$data['row'] = $this->processos_model->carrega(intval($cd_processo));

				$data['usuario_gerencia'] = $this->processos_model->get_usuario_gerencia($data['row']['cod_responsavel']);

				$data['gerencia_envolvida'] = array();

	            foreach($this->processos_model->get_gerencia_envolvida(intval($cd_processo)) as $item)
	            {               
	                $data['gerencia_envolvida'][] = $item['cd_gerencia'];
	            }  

	            $data['usuario_responsavel'] = array();

	            foreach($this->processos_model->get_usuario_responsavel(intval($cd_processo)) as $item)
	            {               
	                $data['usuario_responsavel'][] = $item['cd_usuario'];
	            }  
			}
			
			$this->load->view('gestao/processo/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 		
	}
	
	public function salvar()
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	
		
			$cd_processo = $this->input->post('cd_processo', TRUE);

			$args = array(
				'dt_ini_vigencia' => $this->input->post('dt_ini_vigencia', TRUE),
				'dt_fim_vigencia' => $this->input->post('dt_fim_vigencia', TRUE),
				'procedimento'    => $this->input->post('procedimento', TRUE),
				'data'            => $this->input->post('data', TRUE),
				'cod_responsavel' => $this->input->post('cod_responsavel', TRUE),
				'fl_versao_it'    => $this->input->post('fl_versao_it', TRUE),
				'envolvidos'      => '',
				'cd_usuario'      => $this->session->userdata('codigo')
			);

			if(!is_array($this->input->post('gerencia_envolvida', TRUE)))
			{
				$args['gerencia_envolvida'] = array();
			}
			else
			{
				$args['gerencia_envolvida'] = $this->input->post('gerencia_envolvida', TRUE);
			}

			$args['envolvidos'] = implode(',', $args['gerencia_envolvida']);

			if(intval($cd_processo) == 0)
			{	
				$cd_processo = $this->processos_model->salvar($args);
			}
			else
			{
				$row = $this->processos_model->carrega(intval($cd_processo));

				if((!is_array($this->input->post('usuario_responsavel', TRUE))) OR (trim($row['cod_responsavel']) != trim($args['cod_responsavel'])))
				{
					$args['usuario_responsavel'] = array();
				}
				else
				{
					$args['usuario_responsavel'] = $this->input->post('usuario_responsavel', TRUE);
				}

				$this->processos_model->atualizar($cd_processo, $args);
			}

			redirect('gestao/processo/cadastro/'.intval($cd_processo), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 			
	}

	public function indicador($cd_processo)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$data = array(
				'processo'   => $this->processos_model->carrega(intval($cd_processo)),
				'collection' => $this->processos_model->lista_indicador(intval($cd_processo))
			);

			$this->load->view('gestao/processo/indicador', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function instrumento($cd_processo, $cd_processos_instrumento_trabalho_anexo = 0)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$data = array(
				'processo'    => $this->processos_model->carrega(intval($cd_processo)),
				'collection'  => $this->processos_model->listar_instrumento(intval($cd_processo)),
				'instrumento' => array()
			);

			if(intval($cd_processos_instrumento_trabalho_anexo) > 0)
			{
				$data['instrumento'] = $this->processos_model->instrumento(intval($cd_processos_instrumento_trabalho_anexo));
			}

			$this->load->view('gestao/processo/instrumento', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function salvar_instrumento()
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
		
			$cd_processos_instrumento_trabalho_anexo = intval($this->input->post('cd_processos_instrumento_trabalho_anexo', TRUE));

			if(intval($cd_processos_instrumento_trabalho_anexo) == 0)
			{
				if($qt_arquivo > 0)
				{
					$nr_conta = 0;

					while($nr_conta < $qt_arquivo)
					{
						$args = array(
							'arquivo_nome' => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
							'arquivo'      => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
							'cd_processo'  => $this->input->post('cd_processo', TRUE),
							'cd_usuario'   => $this->session->userdata('codigo')
						);	

						$cd_processos_instrumento_trabalho_anexo = $this->processos_model->salvar_instrumento($args);
						
						$nr_conta++;
					}
				}

				if($qt_arquivo == 1)
				{

					redirect('gestao/processo/instrumento/'.intval($args['cd_processo']).'/'.$cd_processos_instrumento_trabalho_anexo, 'refresh');
				}
				else
				{
					redirect('gestao/processo/instrumento/'.intval($args['cd_processo']), 'refresh');
				}
			}	
			else
			{
				$args = array(
					'cd_processo'                             => $this->input->post('cd_processo', TRUE),
					'ds_processos_instrumento_trabalho_anexo' => $this->input->post('ds_processos_instrumento_trabalho_anexo', TRUE),
					'codigo'                                  => $this->input->post('codigo', TRUE),
					'cd_usuario'                              => $this->session->userdata('codigo')
				);

				$this->processos_model->atualizar_instrumento($cd_processos_instrumento_trabalho_anexo, $args);

				redirect('gestao/processo/instrumento/'.intval($args['cd_processo']), 'refresh');
			}		
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function excluir_instrumento($cd_processo, $cd_processos_instrumento_trabalho_anexo)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$this->processos_model->excluir_instrumento($cd_processos_instrumento_trabalho_anexo, $this->session->userdata('codigo'));
			
			redirect('gestao/processo/instrumento/'.intval($cd_processo), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function instrumento_enviar_email($cd_processo, $cd_processos_instrumento_trabalho_anexo)
	{
		if($this->fl_permissao())
		{
		   	$this->load->model(array(
	            'projetos/processos_model',
	            'projetos/eventos_email_model'
	        ));

	        $cd_evento = 236;

	        $args['cd_processos'] = $cd_processo;

	        $args['cd_processos_instrumento_trabalho_anexo'] = $cd_processos_instrumento_trabalho_anexo;

	        $email = $this->eventos_email_model->carrega($cd_evento);

			$instrumento = $this->processos_model->instrumento($cd_processos_instrumento_trabalho_anexo);
		
			$tags = array('[IT]', '[LINK]');

	        $subs = array(
	        	$instrumento['codigo'].' '.$instrumento['ds_processos_instrumento_trabalho_anexo'],  
	        	base_url().'up/processos/'.$instrumento['arquivo']
	    	);

	        $texto = str_replace($tags, $subs, $email['email']);

	        $cd_usuario = $this->session->userdata('codigo');

	        $args = array(
	            'de'      => 'Processos - IT',
	            'assunto' => str_replace('[IT]', $instrumento['ds_processos_instrumento_trabalho_anexo'], $email['assunto']),
	            'para'    => $email['para'],
	            'cc'      => $email['cc'],
	            'cco'     => $email['cco'],
	            'texto'   => $texto
	        );

	        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
	     
	        redirect('gestao/processo/instrumento/'.intval($cd_processo), 'refresh');
        }
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function fluxo($cd_processo, $cd_processos_fluxo_anexo = 0)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$data = array(
				'processo'   => $this->processos_model->carrega(intval($cd_processo)),
				'collection' => $this->processos_model->listar_fluxo(intval($cd_processo))
			);

			if(intval($cd_processos_fluxo_anexo) == 0)
			{
				$data['fluxo'] = array(
					'cd_processos_fluxo_anexo' => '',
					'codigo'				   => '',
					'ds_processos_fluxo_anexo' => '',
					'ds_link_interact'	   	   => '',
					'arquivo'                  => '',
					'arquivo_nome'             => ''
				);
			}
			else 
			{
				$data['fluxo'] = $this->processos_model->fluxo(intval($cd_processos_fluxo_anexo));
			}

			$this->load->view('gestao/processo/fluxo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}  
	}

	public function salvar_fluxo()
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
		
			$cd_processos_fluxo_anexo = intval($this->input->post('cd_processos_fluxo_anexo', TRUE));

			$args = array(
				'cd_processo'              => $this->input->post('cd_processo', TRUE),
				'ds_processos_fluxo_anexo' => $this->input->post('ds_processos_fluxo_anexo', TRUE),
				'codigo'                   => $this->input->post('codigo', TRUE),
				'arquivo'                  => $this->input->post('arquivo', TRUE),
				'arquivo_nome'             => $this->input->post('arquivo_nome', TRUE),
				'cd_usuario'               => $this->session->userdata('codigo'),
				'ds_link_interact'		   => $this->input->post('ds_link_interact', TRUE),
			);

			if(intval($cd_processos_fluxo_anexo) == 0)
			{
				/*
				if($qt_arquivo > 0)
				{
					$nr_conta = 0;

					while($nr_conta < $qt_arquivo)
					{
						$args = array(
							'arquivo_nome' => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
							'arquivo'      => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
							'cd_processo'  => $this->input->post('cd_processo', TRUE),
							'cd_usuario'   => $this->session->userdata('codigo')
						);	

						$cd_processos_fluxo_anexo = $this->processos_model->salvar_fluxo($args);
						
						$nr_conta++;
					}
				}
				*/
				//if($qt_arquivo == 1)

				$this->processos_model->salvar_fluxo($args);

				if(false)
				{
					redirect('gestao/processo/fluxo/'.intval($args['cd_processo']).'/'.$cd_processos_fluxo_anexo, 'refresh');
				}
				else
				{
					redirect('gestao/processo/fluxo/'.intval($args['cd_processo']), 'refresh');
				}
			}	
			else
			{
				$this->processos_model->atualizar_fluxo($cd_processos_fluxo_anexo, $args);

				redirect('gestao/processo/fluxo/'.intval($args['cd_processo']), 'refresh');
			}		
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function excluir_fluxo($cd_processo, $cd_processos_fluxo_anexo)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$this->processos_model->excluir_fluxo($cd_processos_fluxo_anexo, $this->session->userdata('codigo'));
			
			redirect('gestao/processo/fluxo/'.intval($cd_processo), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function fluxo_enviar_email($cd_processo, $cd_processos_fluxo_anexo)
	{
	   	$this->load->model(array(
            'projetos/processos_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 263;

        $email = $this->eventos_email_model->carrega($cd_evento);

		$fluxo = $this->processos_model->fluxo(intval($cd_processos_fluxo_anexo));
	
		$tags = array('[PROCEDIMENTO]', '[LINK]');

        $subs = array(
        	$fluxo['codigo'].' '.$fluxo['ds_processos_fluxo_anexo'],
        	base_url().'up/processos/' . $fluxo['arquivo']
    	);

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Processos - Fluxograma',
            'assunto' => str_replace('[PROCEDIMENTO]',$fluxo['ds_processos_fluxo_anexo'], $email['assunto']),
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
     
        redirect('gestao/processo/fluxo/'.intval($cd_processo), 'refresh');
	}

	public function pop($cd_processo, $cd_processos_pop_anexo = 0)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$data = array(
				'processo'   => $this->processos_model->carrega(intval($cd_processo)),
				'collection' => $this->processos_model->listar_pop(intval($cd_processo)),
				'pop'        => array()
			);

			if(intval($cd_processos_pop_anexo) > 0)
			{
				$data['pop'] = $this->processos_model->pop(intval($cd_processos_pop_anexo));
			}

			$this->load->view('gestao/processo/pop', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}  
	}

	public function salvar_pop()
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
		
			$cd_processos_pop_anexo = intval($this->input->post('cd_processos_pop_anexo', TRUE));

			if(intval($cd_processos_pop_anexo) == 0)
			{
				if($qt_arquivo > 0)
				{
					$nr_conta = 0;

					while($nr_conta < $qt_arquivo)
					{
						$args = array(
							'arquivo_nome' => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
							'arquivo'      => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
							'cd_processo'  => $this->input->post('cd_processo', TRUE),
							'cd_usuario'   => $this->session->userdata('codigo')
						);	

						$cd_processos_pop_anexo = $this->processos_model->salvar_pop($args);
						
						$nr_conta++;
					}
				}

				if($qt_arquivo == 1)
				{

					redirect('gestao/processo/pop/'.intval($args['cd_processo']).'/'.$cd_processos_pop_anexo, 'refresh');
				}
				else
				{
					redirect('gestao/processo/pop/'.intval($args['cd_processo']), 'refresh');
				}
			}	
			else
			{
				$args = array(
					'cd_processo'            => $this->input->post('cd_processo', TRUE),
					'ds_processos_pop_anexo' => $this->input->post('ds_processos_pop_anexo', TRUE),
					'codigo'                 => $this->input->post('codigo', TRUE),
					'cd_usuario'             => $this->session->userdata('codigo')
				);

				$this->processos_model->atualizar_pop($cd_processos_pop_anexo, $args);

				redirect('gestao/processo/pop/'.intval($args['cd_processo']), 'refresh');
			}		
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function pop_enviar_email($cd_processo, $cd_processos_pop_anexo)
	{
	   	$this->load->model(array(
            'projetos/processos_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 335;

        $email = $this->eventos_email_model->carrega($cd_evento);

		$pop = $this->processos_model->pop(intval($cd_processos_pop_anexo));
	
		$tags = array('[POP]', '[LINK]');

        $subs = array(
        	$pop['codigo'].' '.$pop['ds_processos_pop_anexo'],
        	base_url().'up/processos/'.$pop['arquivo']
    	);

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Processos - POP',
            'assunto' => str_replace('[POP]', $pop['ds_processos_pop_anexo'], $email['assunto']),
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
     
        redirect('gestao/processo/pop/'.intval($cd_processo), 'refresh');
	}

	public function excluir_pop($cd_processo, $cd_processos_pop_anexo)
	{
		if($this->fl_permissao())
		{
			$this->load->model('projetos/processos_model');	

			$this->processos_model->excluir_pop($cd_processos_pop_anexo, $this->session->userdata('codigo'));
			
			redirect('gestao/processo/pop/'.intval($cd_processo), 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function registro($cd_processo, $cd_processos_registro_anexo = 0)
    {
        if($this->fl_permissao())
        {
            $this->load->model('projetos/processos_model'); 

            $data = array(
                'processo'   => $this->processos_model->carrega(intval($cd_processo)),
                'collection' => $this->processos_model->listar_registro(intval($cd_processo)),
                'registro'   => array()
            );

            if(intval($cd_processos_registro_anexo) > 0)
            {
                $data['registro'] = $this->processos_model->registro(intval($cd_processos_registro_anexo));
            }

            $this->load->view('gestao/processo/registro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }  
    }

    public function salvar_registro()
    {
        if($this->fl_permissao())
        {
            $this->load->model('projetos/processos_model'); 

            $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
        
            $cd_processos_registro_anexo = intval($this->input->post('cd_processos_registro_anexo', TRUE));

            if(intval($cd_processos_registro_anexo) == 0)
            {
                if($qt_arquivo > 0)
                {
                    $nr_conta = 0;

                    while($nr_conta < $qt_arquivo)
                    {
                        $args = array(
                            'arquivo_nome' => $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE),
                            'arquivo'      => $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE),
                            'cd_processo'  => $this->input->post('cd_processo', TRUE),
                            'cd_usuario'   => $this->session->userdata('codigo')
                        );  

                        $cd_processos_registro_anexo = $this->processos_model->salvar_registro($args);
                        
                        $nr_conta++;
                    }
                }

                if($qt_arquivo == 1)
                {

                    redirect('gestao/processo/registro/'.intval($args['cd_processo']).'/'.$cd_processos_registro_anexo, 'refresh');
                }
                else
                {
                    redirect('gestao/processo/registro/'.intval($args['cd_processo']), 'refresh');
                }
            }   
            else
            {
                $args = array(
                    'cd_processo'                 => $this->input->post('cd_processo', TRUE),
                    'ds_processos_registro_anexo' => $this->input->post('ds_processos_registro_anexo', TRUE),
                    'codigo'                      => $this->input->post('codigo', TRUE),
                    'cd_usuario'                  => $this->session->userdata('codigo')
                );

                $this->processos_model->atualizar_registro($cd_processos_registro_anexo, $args);

                redirect('gestao/processo/registro/'.intval($args['cd_processo']), 'refresh');
            }       
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function registro_enviar_email($cd_processo, $cd_processos_registro_anexo)
    {
        $this->load->model(array(
            'projetos/processos_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 336;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $registro = $this->processos_model->registro(intval($cd_processos_registro_anexo));
    
        $tags = array('[REGISTRO]', '[LINK]');

        $subs = array(
            $registro['codigo'].' '.$registro['ds_processos_registro_anexo'],
            base_url().'up/processos/'.$registro['arquivo']
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Processos - Registro',
            'assunto' => str_replace('[REGISTRO]', $registro['ds_processos_registro_anexo'], $email['assunto']),
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
     
        redirect('gestao/processo/registro/'.intval($cd_processo), 'refresh');
    }

    public function excluir_registro($cd_processo, $cd_processos_registro_anexo)
    {
        if($this->fl_permissao())
        {
            $this->load->model('projetos/processos_model'); 

            $this->processos_model->excluir_registro($cd_processos_registro_anexo, $this->session->userdata('codigo'));
            
            redirect('gestao/processo/registro/'.intval($cd_processo), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

	public function revisao_historico($cd_processo)
	{
		$this->load->model(array(
			'projetos/processos_model',
			'projetos/processo_revisao_model'
		));

		$data = array(
			'processo'   => $this->processos_model->carrega(intval($cd_processo)),
			'collection' => $this->processo_revisao_model->listar_historico(intval($cd_processo))
		);

		$this->load->view('gestao/processo/revisao', $data);
	}
	
	public function mapa($fl_mostra_template = 'S')
	{
		$this->load->model('projetos/processos_model');	

		$data = array(
			'fl_mostra_template' => $fl_mostra_template,
			'gerencia'           => $this->processos_model->get_gerencia_responsavel()
		);
		        
		$this->load->view('gestao/processo/mapa', $data);
		
	}
	
	public function mapa_listar()
	{
		$this->load->model('projetos/processos_model');
		
		$args = array(
			'fl_vigente'              => 'S',
			'fl_versao_it'            => $this->input->post('fl_versao_it', TRUE),
			'cd_gerencia_responsavel' => $this->input->post('cd_gerencia_responsavel', TRUE)
			
		);

		manter_filtros($args);

		$collection = $this->processos_model->listar($args);

		foreach ($collection as $key => $item) 
		{
			$collection[$key]['fluxo'] = $this->processos_model->listar_fluxo($item['cd_processo']);

			$collection[$key]['instrumento'] = $this->processos_model->listar_instrumento($item['cd_processo']);

			$collection[$key]['pop'] = $this->processos_model->listar_pop($item['cd_processo']);

			$collection[$key]['registro'] = $this->processos_model->listar_registro($item['cd_processo']);

			$collection[$key]['indicador'] = $this->processos_model->lista_indicador($item['cd_processo'], 'G');
		}

		$data['collection'] = $collection;
       				
		$this->load->view('gestao/processo/mapa_result', $data);
	}	

	public function revisao()
	{
		if($this->fl_permissao_revisao())
		{			
			$data = array();

			$this->load->model('projetos/processo_revisao_model');

			$fl_gerencia = true;

			if(($this->session->userdata('indic_05') == 'S') OR ($this->session->userdata('indic_12') == '*'))
			{
				$fl_gerencia = false;
			}

			$cd_gerencia = $this->session->userdata('divisao');
			
			$data['processo'] = $this->processo_revisao_model->get_combo_processo($fl_gerencia, $cd_gerencia);

			$data['referencia'] = $this->processo_revisao_model->get_referencia();

			$this->load->view('gestao/processo_revisao/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}      
	}

	public function revisao_listar()
	{
		$data = array();

		if($this->fl_permissao_revisao())
		{	
			$this->load->model('projetos/processo_revisao_model');

			$args = array(
				'referencia'  => $this->input->post('referencia', TRUE),
				'cd_processo' => $this->input->post('cd_processo', TRUE),
				'fl_revisado' => $this->input->post('fl_revisado', TRUE)
			);

			manter_filtros($args);

			$fl_gerencia = true;

			if(($this->session->userdata('indic_05') == 'S') OR ($this->session->userdata('indic_12') == '*'))
			{
				$fl_gerencia = false;
			}

			$cd_usuario = $this->session->userdata('codigo');
		   
			$data['collection'] = $this->processo_revisao_model->listar($fl_gerencia, $cd_usuario, $args);
		}
		else
		{
			$data['collection'] = array();
		}

		$this->load->view('gestao/processo_revisao/index_result', $data);
	}

	public function revisao_cadastro($cd_processo_revisao)
	{
		if($this->fl_permissao_revisao())
		{	
			$this->load->model('projetos/processo_revisao_model');

			$data = array();

			$fl_gerencia = true;

			if($this->session->userdata('indic_05') == 'S')
			{
				$fl_gerencia = false;
			}

			$cd_usuario = $this->session->userdata('codigo');

			$data['row'] = $this->processo_revisao_model->carrega(intval($cd_processo_revisao), $fl_gerencia, $cd_usuario);

			if(count($data['row']) > 0)
			{
				$this->load->view('gestao/processo_revisao/cadastro', $data);
			}
			else
			{	
				exibir_mensagem('ACESSO NÃO PERMITIDO');
			}
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}

	public function revisao_salvar()
	{
		$this->load->model('projetos/processo_revisao_model');

		$cd_processo_revisao = $this->input->post('cd_processo_revisao', TRUE); 
		$args['cd_usuario']   = $this->session->userdata('codigo'); 

		if($this->session->userdata('indic_05') == 'S')
		{
			$fl_responsavel = true;
		}
		else
		{
			$revisao = $this->processo_revisao_model->get_responsavel($cd_processo_revisao, $args['cd_usuario']);

			if($revisao['tl'] > 0)
			{
				$fl_responsavel = true;
			}
			else
			{
				$fl_responsavel = false;
			}
		}
   
		if($this->fl_permissao_revisao() AND $fl_responsavel)
		{
			$args['fl_alterado'] = $this->input->post('fl_alterado', TRUE);
			$args['observacao']  = $this->input->post('observacao', TRUE);

			$this->processo_revisao_model->atualizar($cd_processo_revisao, $args);

			redirect('gestao/processo/revisao');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		} 
	}
}
?>