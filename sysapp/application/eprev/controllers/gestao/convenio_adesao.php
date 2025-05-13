<?php
class Convenio_adesao extends Controller
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
            //asilva
            if($this->session->userdata('codigo') == 3)
            {
                return TRUE;
            }
            //Renata Opitz
            else if($this->session->userdata('codigo') == 468)
            {
                return TRUE;
            }
			#Vanessa Silva Alves
			else if($this->session->userdata('codigo') == 424)
			{
				return true;
			}
			#Vitoria Vidal Medeiros da Silva
            else if($this->session->userdata('codigo') == 431)
            {
                return true;
            }
			#Regis Rodrigues da Silveira
            else if($this->session->userdata('codigo') == 411)
            {
                return true;
            }
            #Bruna Gomes
            else if($this->session->userdata('codigo') == 497)
            {
                return true;
            }
			#Julia Gabrieli Freitas de Oliveira
            else if($this->session->userdata('codigo') == 489)
            {
                return true;
            }
            else if($this->session->userdata('codigo') == 251)
            {
                return TRUE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    public function intranet()
    {
    	$this->load->model('gestao/convenio_adesao_model');

    	$args = array(
			'cd_plano' 	 => '',
			'cd_empresa' => ''
		);

		$data['collection'] = array();

		$planos = $this->convenio_adesao_model->get_planos();

		foreach ($planos as $key => $item) 
        {
        	$data['collection'][$item['cd_plano']]['ds_plano'] = $item['descricao'];

        	$empresa = $this->convenio_adesao_model->get_empresa_plano($item['cd_plano']);

        	$data['collection'][$item['cd_plano']]['convenio'] = array();

        	foreach ($empresa as $key2 => $item2) 
        	{
        		$row = $this->convenio_adesao_model->listar($item['cd_plano'], $item2['cd_empresa'], $args);

        		if(count($row) > 0)
        		{
        			$versoes_anteriores = $this->convenio_adesao_model->listar_anteriores(
						$row['cd_convenio_adesao'], 
						$item['cd_plano'], 
						$item2['cd_empresa']
					);

        			$data['collection'][$item['cd_plano']]['convenio'][$item2['cd_empresa']] = $row;

        			$data['collection'][$item['cd_plano']]['convenio'][$item2['cd_empresa']]['versoes_anteriores'] = $versoes_anteriores;
        		}        		
        	}
        }

        $this->load->view('gestao/convenio_adesao/intranet', $data);
    }

	public function index()
	{
		$this->load->view('gestao/convenio_adesao/index');
	}

	public function listar()
	{
		$this->load->model('gestao/convenio_adesao_model');

		$args = array(
			'cd_plano' 	 => $this->input->post('cd_plano', TRUE),
			'cd_empresa' => $this->input->post('cd_plano_empresa', TRUE)
		);

		$empresa_plano = $this->convenio_adesao_model->get_empresa_plano();

		manter_filtros($args);

		$data['collection'] = array();

		foreach ($empresa_plano as $key => $item)
		{
			$row = $this->convenio_adesao_model->listar($item['cd_plano'], $item['cd_empresa'], $args);

			if(count($row) > 0)
            {
                $data['collection'][] = $row;
            }
		}

		$this->load->view('gestao/convenio_adesao/index_result', $data);
	}

	public function cadastro($cd_convenio_adesao = 0)
	{
		$this->load->model('gestao/convenio_adesao_model');

		if(intval($cd_convenio_adesao) == 0)
		{
			$data['row'] = array(
				'cd_convenio_adesao' 			         => '',
				'cd_empresa' 					         => '',
				'cd_plano' 						         => '',
				'ds_convenio_adesao' 			         => '',
				'arquivo' 						         => '',
				'arquivo_nome' 					         => '',
				'arquivo_aprovacao' 			         => '',
				'arquivo_aprovacao_nome' 		         => '',
				'arquivo_termo_aditivo' 		         => '',
				'arquivo_termo_aditivo_nome' 	         => '',
				'arquivo_portaria_aprovacao' 	         => '',
				'arquivo_portaria_aprovacao_nome'        => '',
				'arquivo_termo_adesao' 			         => '',
				'arquivo_termo_adesao_nome' 	         => '',
				'arquivo_portaria_aprovacao_adesao' 	 => '',
				'arquivo_portaria_aprovacao_adesao_nome' => '',
				'fl_lgpd'                                => '',
				'dt_envio'                               => ''
			);

			$data['collection'] = array();
            $data['fl_editar']  = TRUE;
		}
		else
		{
			$data['row'] 		= $this->convenio_adesao_model->carrega($cd_convenio_adesao);
			$data['collection'] = $this->convenio_adesao_model->listar_anteriores(
				$cd_convenio_adesao, 
				$data['row']['cd_plano'], 
				$data['row']['cd_empresa']
			);
            $data['fl_editar']  = FALSE;

            if(gerencia_in(array('GC')) AND trim($data['row']['dt_envio']) == '')
            {
                $data['fl_editar'] = TRUE;
            }
		}

		$this->load->view('gestao/convenio_adesao/cadastro', $data);
	}

	public function salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/convenio_adesao_model');

			$cd_convenio_adesao = $this->input->post('cd_convenio_adesao', TRUE);

			$args = array(
				'cd_empresa' 					         => $this->input->post('cd_plano_empresa', TRUE),
				'cd_plano' 						         => $this->input->post('cd_plano', TRUE),
				'ds_convenio_adesao' 			         => $this->input->post('ds_convenio_adesao', TRUE),
				'arquivo' 						         => $this->input->post('arquivo', TRUE),
				'arquivo_nome' 					         => $this->input->post('arquivo_nome', TRUE),
				'arquivo_aprovacao' 			         => $this->input->post('arquivo_aprovacao', TRUE),
				'arquivo_aprovacao_nome' 		         => $this->input->post('arquivo_aprovacao_nome', TRUE),
				'arquivo_termo_aditivo' 		         => $this->input->post('arquivo_termo_aditivo', TRUE),
				'arquivo_termo_aditivo_nome' 	         => $this->input->post('arquivo_termo_aditivo_nome', TRUE),
				'arquivo_portaria_aprovacao' 	         => $this->input->post('arquivo_portaria_aprovacao', TRUE),
				'arquivo_portaria_aprovacao_nome'        => $this->input->post('arquivo_portaria_aprovacao_nome', TRUE),
				'arquivo_termo_adesao' 			         => $this->input->post('arquivo_termo_adesao', TRUE),
				'arquivo_termo_adesao_nome' 	         => $this->input->post('arquivo_termo_adesao_nome', TRUE),
				'arquivo_portaria_aprovacao_adesao' 	 => $this->input->post('arquivo_portaria_aprovacao_adesao', TRUE),
				'arquivo_portaria_aprovacao_adesao_nome' => $this->input->post('arquivo_portaria_aprovacao_adesao_nome', TRUE),
				'fl_lgpd'                                => $this->input->post('fl_lgpd', TRUE),
				'cd_usuario' 					         => $this->session->userdata('codigo')
			);

			if(intval($cd_convenio_adesao) == 0)
			{
				$this->convenio_adesao_model->salvar($args);
			}
			else
			{
				$this->convenio_adesao_model->atualizar($cd_convenio_adesao, $args);
			}

			redirect('gestao/convenio_adesao', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    public function enviar($cd_convenio_adesao)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/convenio_adesao_model'
            ));

            $row = $this->convenio_adesao_model->carrega($cd_convenio_adesao);

            $cd_evento = 370;
            
            $email = $this->eventos_email_model->carrega($cd_evento);

            $tags = array('[DS_EMPRESA]', '[DS_PLANO]', '[LINK]');

            $subs = array(
                $row['empresa'], 
                $row['plano'], 
                site_url('ecrm/intranet/pagina/INST/10465')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Propostas ao Regulamento',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->convenio_adesao_model->enviar($cd_convenio_adesao, $cd_usuario);

            redirect('gestao/convenio_adesao/cadastro/'.$cd_convenio_adesao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}