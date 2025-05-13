<?php
class Cadastro_colaborador extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
    	if($this->session->userdata('indic_09') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
        {
            return TRUE;
        }
		else if(gerencia_in(array('GTI')))
        {
			return TRUE;
		}
        else
        {
            return FALSE;
        }
    }

    private function get_tipo()
    {
    	return array(
    		array('value' => 'C', 'text' => 'Colaborador'),
    		array('value' => 'P', 'text' => 'Prestador de Serviчo'),
            array('value' => 'D', 'text' => 'Diretoria Executiva'),
    		array('value' => 'E', 'text' => 'Estagiсrio')
    	);
    }
	
	private function get_status()
    {
    	return array(
    		array('value' => 'C', 'text' => 'Aguardando Solicitaчуo de Usuсrio'),
    		array('value' => 'U', 'text' => 'Usuсrio Solicitado'),
    		array('value' => 'I', 'text' => 'Liberado pela Infra'),
    		array('value' => 'E', 'text' => 'Liberado pelo Eletro'),
    		array('value' => 'L', 'text' => 'Liberado')
    	);
    }
	
    public function index()
    {
        if($this->get_permissao())
		{
			$this->load->model(array(
				'projetos/cadastro_colaborador_model', 
				'projetos/divisoes'
			));

			$data = array(
				'gerencia' => $this->cadastro_colaborador_model->get_gerencia(),
				'tipo'     => $this->get_tipo(),
				'status'   => $this->get_status()
			);
		
    		$this->load->view('cadastro/cadastro_colaborador/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
    }

    public function listar()
    {
    	$this->load->model('projetos/cadastro_colaborador_model');

		$args = array(
			'ds_nome'	  => $this->input->post('ds_nome', TRUE),
			'cd_gerencia' => $this->input->post('cd_gerencia', TRUE),
			'fl_tipo'     => $this->input->post('fl_tipo', TRUE),
			'fl_status'	  => $this->input->post('status', TRUE)
		);
		
		manter_filtros($args);
		
		$data['collection'] = $this->cadastro_colaborador_model->listar($args);
				 
    	$this->load->view('cadastro/cadastro_colaborador/index_result', $data);
    }

    public function cadastro($cd_cadastro_colaborador = 0)
    {
    	if($this->get_permissao())
    	{
    		$this->load->model(array(
    			'projetos/cadastro_colaborador_model',
    			'projetos/divisoes'
    		));

			$data = array(
				'gerencia' => $this->cadastro_colaborador_model->get_gerencia(),
				'cargo'    => $this->cadastro_colaborador_model->get_cargo(),
				'tipo'     => $this->get_tipo()

			);

			if(intval($cd_cadastro_colaborador) == 0)
			{
				$data['row'] = array(
					'cd_cadastro_colaborador'    => $cd_cadastro_colaborador,
					'ds_nome' 				     => '',   
					'dt_nascimento'              => '',
					'fl_tipo'		             => '',
					'cd_gerencia'			     => '',
				    'dt_admissao'			     => '',
				    'cd_cargo'		     	     => '',
				    'ds_observacao'			     => '',
				    'cd_usuario_enviado'         => '',
				    'dt_enviado' 			     => '',
					'cd_usuario_liberado_infra'  => '',
					'dt_liberado_infra'          => '',
					'cd_usuario_liberado_eletro' => '',
					'dt_liberado_eletro'         => '',
					'nr_ramal'                   => ''
				);
			}
			else
			{
				$data['row'] = $this->cadastro_colaborador_model->carrega($cd_cadastro_colaborador);
			}
			
			$this->load->view('cadastro/cadastro_colaborador/cadastro', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NУO PERMITIDO');
    	}
    }

    public function salvar()
	{
		if($this->get_permissao())
        {
    		$this->load->model('projetos/cadastro_colaborador_model');

			$args = array();
			
			$cd_cadastro_colaborador = $this->input->post('cd_cadastro_colaborador', TRUE);

			$args = array(
				'cd_cadastro_colaborador' => $cd_cadastro_colaborador,
				'ds_nome' 				  => $this->input->post('ds_nome', TRUE),   
				'dt_nascimento'           => $this->input->post('dt_nascimento', TRUE),
				'fl_tipo'		          => $this->input->post('fl_tipo', TRUE),
				'cd_gerencia'			  => $this->input->post('cd_gerencia', TRUE),
				'dt_admissao'			  => $this->input->post('dt_admissao', TRUE),
				'cd_cargo'		     	  => $this->input->post('cd_cargo', TRUE),
				'ds_observacao'			  => $this->input->post('ds_observacao', TRUE),
				'cd_usuario' 			  => $this->session->userdata('codigo')
			);

			if(intval($cd_cadastro_colaborador) == 0)
			{
				$cd_cadastro_colaborador = $this->cadastro_colaborador_model->salvar($args);
			}
			else
			{
				$this->cadastro_colaborador_model->atualizar($cd_cadastro_colaborador, $args);
			}
			
			redirect('cadastro/cadastro_colaborador/cadastro/'.$cd_cadastro_colaborador);
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}
	
	public function atualizar_usuario()
	{
		if($this->get_permissao())
        {
    		$this->load->model('projetos/cadastro_colaborador_model');
			
			$cd_cadastro_colaborador = $this->input->post('cd_cadastro_colaborador', TRUE);	
			
			$args = array(
				'cd_cadastro_colaborador' => $cd_cadastro_colaborador,
				'ds_usuario'			  => $this->input->post('ds_usuario', TRUE),
				'senha_rede'			  => $this->input->post('senha_rede', TRUE),
				'nr_ramal'			      => $this->input->post('nr_ramal', TRUE),
				'cd_usuario'			  => $this->session->userdata('codigo'),
                'fl_usuario_sa'           => $this->input->post('fl_usuario_sa', TRUE)
			);
			
			$this->cadastro_colaborador_model->atualizar_usuario($args);
		
			redirect('cadastro/cadastro_colaborador/cadastro/'.$cd_cadastro_colaborador);
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}
	
	public function atualizar_usuario_infra()
	{
		if($this->get_permissao())
        {
    		$this->load->model('projetos/cadastro_colaborador_model');
			
			$cd_cadastro_colaborador = $this->input->post('cd_cadastro_colaborador', TRUE);	
			
			$args = array(
				'cd_cadastro_colaborador' => $cd_cadastro_colaborador,
				'senha_eletro'	          => $this->input->post('senha_eletro', TRUE),
				'cd_usuario'			  => $this->session->userdata('codigo')
			);
			
			$this->cadastro_colaborador_model->atualizar_usuario_infra($args);
		
			redirect('cadastro/cadastro_colaborador/cadastro/'.$cd_cadastro_colaborador);
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}
	
	public function atualizar_usuario_eletro()
	{
		if($this->get_permissao())
        {
    		$this->load->model('projetos/cadastro_colaborador_model');
			
			$cd_cadastro_colaborador = $this->input->post('cd_cadastro_colaborador', TRUE);	
			
			$args = array(
				'cd_cadastro_colaborador' => $cd_cadastro_colaborador,
				'cd_usuario'			  => $this->session->userdata('codigo')
			);
			
			$this->cadastro_colaborador_model->atualizar_usuario_eletro($args);
		
			redirect('cadastro/cadastro_colaborador');
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}

	public function solicitar_usuario($cd_cadastro_colaborador)
	{
		if($this->get_permissao())
        {
    		$this->load->model(array(
    			'projetos/cadastro_colaborador_model', 
    			'projetos/eventos_email_model'
    		));

    		$cd_evento = 199;

    		$email = $this->eventos_email_model->carrega($cd_evento);

    		$row = $this->cadastro_colaborador_model->carrega($cd_cadastro_colaborador);

    		$tags = array('[NOME]', '[DT_NASCIMENTO]', '[TIPO]', '[DT_ADMISSAO]', '[GERENCIA]', '[CARGO]', '[OBS]', '[LINK]');

            $subs = array(
            	$row['ds_nome'],
            	$row['dt_nascimento'],
            	$row['ds_tipo'],
            	$row['dt_admissao'],
            	$row['cd_gerencia'],
            	$row['nome_cargo'],
            	$row['ds_observacao'],
            	site_url('cadastro/cadastro_colaborador/cadastro/'.intval($row['cd_cadastro_colaborador']))
            );

            $texto = str_replace($tags, $subs, $email['email']);

			$this->cadastro_colaborador_model->solicitar_usuario(intval($cd_cadastro_colaborador), $this->session->userdata('codigo'));

			$args = array(
                'de'      => 'Cadastro Colaborador',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $this->session->userdata('codigo'), $args);
			
			redirect('cadastro/cadastro_colaborador/cadastro/'.$cd_cadastro_colaborador);
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}

	public function liberar_usuario_rede($cd_cadastro_colaborador)
	{
		if($this->get_permissao())
        {
    		$this->load->model(array(
    			'projetos/cadastro_colaborador_model', 
    			'projetos/eventos_email_model'
    		));

    		$cd_evento = 200;

    		$email = $this->eventos_email_model->carrega($cd_evento);

    		$row = $this->cadastro_colaborador_model->carrega($cd_cadastro_colaborador);

    		$tags = array('[NOME]', '[DT_NASCIMENTO]', '[TIPO]', '[DT_ADMISSAO]', '[GERENCIA]', '[CARGO]', '[OBS]', '[USUARIO]', '[SENHA_REDE]', '[LINK]');

            $subs = array(
            	$row['ds_nome'],
            	$row['dt_nascimento'],
            	$row['ds_tipo'],
            	$row['dt_admissao'],
            	$row['cd_gerencia'],
            	$row['nome_cargo'],
            	$row['ds_observacao'], 
            	$row['ds_usuario'], 
            	$row['senha_rede'], 
            	site_url('cadastro/cadastro_colaborador/cadastro/'.intval($row['cd_cadastro_colaborador']))
           	);

            $texto = str_replace($tags, $subs, $email['email']);

            $this->cadastro_colaborador_model->liberar_usuario_rede(intval($cd_cadastro_colaborador), $this->session->userdata('codigo'));

			$args = array(
                'de'      => 'Cadastro Colaborador',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $this->session->userdata('codigo'), $args);
			
			redirect('cadastro/cadastro_colaborador/cadastro/'.$cd_cadastro_colaborador);
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}
	
	public function liberar_usuario_eletro($cd_cadastro_colaborador)
	{
		if($this->get_permissao())
        {
    		$this->load->model(array(
    			'projetos/cadastro_colaborador_model', 
    			'projetos/eventos_email_model'
    		));

    		$cd_evento = 201;

    		$email = $this->eventos_email_model->carrega($cd_evento);

    		$row = $this->cadastro_colaborador_model->carrega($cd_cadastro_colaborador);

    		$tags = array('[NOME]', '[DT_NASCIMENTO]', '[TIPO]', '[DT_ADMISSAO]', '[GERENCIA]', '[CARGO]', '[OBS]', '[USUARIO]', '[SENHA_REDE]', '[SENHA_ELETRO]', '[LINK]');

            $subs = array(
            	$row['ds_nome'],
            	$row['dt_nascimento'],
            	$row['ds_tipo'],
            	$row['dt_admissao'],
            	$row['cd_gerencia'],
            	$row['nome_cargo'],
            	$row['ds_observacao'], 
            	$row['ds_usuario'], 
            	$row['senha_rede'], 
            	$row['senha_eletro'], 
            	site_url('cadastro/cadastro_colaborador/cadastro/'.intval($row['cd_cadastro_colaborador']))
            );

            $texto = str_replace($tags, $subs, $email['email']);

			$this->cadastro_colaborador_model->liberar_usuario_eletro(intval($cd_cadastro_colaborador), $this->session->userdata('codigo'));

			$args = array(
                'de'      => 'Cadastro Colaborador',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $this->session->userdata('codigo'), $args);
			
			redirect('cadastro/cadastro_colaborador/cadastro/'.$cd_cadastro_colaborador);
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}

	public function liberar_usuario_eprev($cd_cadastro_colaborador)
	{
		if($this->get_permissao())
        {
    		$this->load->model(array(
    			'projetos/cadastro_colaborador_model', 
    			'projetos/eventos_email_model'
    		));

    		$cd_evento = 202;

    		$email = $this->eventos_email_model->carrega($cd_evento);

    		$row = $this->cadastro_colaborador_model->carrega($cd_cadastro_colaborador);

    		$tags = array('[NOME]', '[DT_NASCIMENTO]', '[TIPO]', '[DT_ADMISSAO]', '[GERENCIA]', '[CARGO]', '[OBS]', '[USUARIO]', '[SENHA_REDE]', '[SENHA_ELETRO]', '[NR_RAMAL]');

            $subs = array(
            	$row['ds_nome'],
            	$row['dt_nascimento'],
            	$row['ds_tipo'],
            	$row['dt_admissao'],
            	$row['cd_gerencia'],
            	$row['nome_cargo'],
            	$row['ds_observacao'], 
            	$row['ds_usuario'], 
            	$row['senha_rede'], 
            	$row['senha_eletro'],
            	$row['nr_ramal']
           	);

            $texto = str_replace($tags, $subs, $email['email']);

			$this->cadastro_colaborador_model->liberar_usuario_eprev(intval($cd_cadastro_colaborador), $this->session->userdata('codigo'));

			$args = array(
                'de'      => 'Cadastro Colaborador',
                'assunto' => $email['assunto'],
                'para'    => $row['ds_usuario'].'@eletroceee.com.br;'.$email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $this->session->userdata('codigo'), $args);
        
			redirect('cadastro/cadastro_colaborador/cadastro/'.$cd_cadastro_colaborador);
		}
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
	}
}
?>