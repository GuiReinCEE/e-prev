<?php
class Municipio_arq_env extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao()
    {
        if(gerencia_in(array('GCM', 'GFC', 'GAP.')))
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Cristiano
        else if($this->session->userdata('codigo') == 170)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_edicao()
    {
    	#Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Nalu Cristina Ribeiro das Neves	
        else if($this->session->userdata('codigo') == 75)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        #Kenia Oliveira Barbosa
        else if($this->session->userdata('codigo') == 429)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Cristiano
        else if($this->session->userdata('codigo') == 170)
        {
            return TRUE;
        }
        #Mariana Chagas Abbott
        else if($this->session->userdata('codigo') == 414)
        {
            return TRUE;
        }
        #Carine do Valle Teobaldi
        else if($this->session->userdata('codigo') == 376)
        {
            return TRUE;
        }
		#William Guimaraes da Rocha
        else if($this->session->userdata('codigo') == 475)
        {
            return TRUE;
        }
		#Alexsandro de Souza Rocha
        else if($this->session->userdata('codigo') == 29)
        {
            return TRUE;
        }		
        else
        {
            return FALSE;
        }
    }

    private function get_status()
    {
    	return array(
    		array('value' => 'E', 'text' => 'Encaminhado'),
    		array('value' => 'A', 'text' => 'Aceito'),
    		array('value' => 'R', 'text' => 'Recusado')
    	);
    }

    public function index($cd_empresa = '')
	{
		if($this->get_permissao())
		{
			$this->load->model('extranet_new/municipio_arq_env_model');

			$data = array(
                'cd_empresa' => $cd_empresa,
                'empresa'    => $this->municipio_arq_env_model->get_empresa(),
                'status'     => $this->get_status()
            );

			$this->load->view('ecrm/municipio_arq_env/index', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function listar($data = array())
	{
		$this->load->model('extranet_new/municipio_arq_env_model');

		$args = array(
			'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
            'dt_encaminhamento_ini' => $this->input->post('dt_encaminhamento_ini', TRUE),
            'dt_encaminhamento_fim' => $this->input->post('dt_encaminhamento_fim', TRUE),
            'tp_status'             => $this->input->post('tp_status', TRUE)
		);

        manter_filtros($args);

		$data['collection'] = $this->municipio_arq_env_model->listar($args);
		
		$this->load->view('ecrm/municipio_arq_env/index_result', $data);
	}

	public function cadastro($cd_municipio_arq_env = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('extranet_new/municipio_arq_env_model');

			$data['row'] = $this->municipio_arq_env_model->carrega($cd_municipio_arq_env);

			$this->load->view('ecrm/municipio_arq_env/cadastro', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function aceitar($cd_municipio_arq_env)
	{
		if($this->get_permissao())
		{
			$this->load->model('extranet_new/municipio_arq_env_model');

			$cd_usuario = $this->session->userdata('codigo');

            $this->municipio_arq_env_model->aceitar($cd_municipio_arq_env, $cd_usuario);

            $this->envia_email_aceito($cd_municipio_arq_env);

			redirect('ecrm/municipio_arq_env');
		}
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

	public function rejeitar()
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('extranet_new/municipio_arq_env_model');

			$cd_municipio_arq_env = $this->input->post('cd_municipio_arq_env');

            $cd_usuario = $this->session->userdata('codigo');
            $ds_recusado = $this->input->post('ds_recusado');

        	$args = array(
        		'ds_recusado' => $ds_recusado,
        		'cd_usuario'  => $cd_usuario       
        	);

        	$this->municipio_arq_env_model->rejeitar($cd_municipio_arq_env, $args);

            $this->envia_email_rejeitado($cd_municipio_arq_env);

        	redirect('ecrm/municipio_arq_env');
		}
		else 
		{
			exibir_mensagem('ACESSO NУO PERMITIDO');
		}
	}

    public function envia_email_rejeitado($cd_municipio_arq_env)
    {
        if($this->get_permissao_edicao())
        {
            $this->load->model('extranet_new/municipio_arq_env_model');

            $cd_usuario = $this->session->userdata('codigo');

            $row = $this->municipio_arq_env_model->carrega($cd_municipio_arq_env);

            $usuario = array();

            foreach($this->municipio_arq_env_model->get_email_usuario($row['cd_empresa']) as $item)
            {               
                $usuario[] = $item['ds_email'];
            }

            if(intval($row['cd_municipio_arq_tipo']) == 1)
            {
                $this->load->model('projetos/eventos_email_model');

                if(count($usuario) > 0)
                {
                    $cd_evento = 466;

                    $email = $this->eventos_email_model->carrega($cd_evento);

                    $texto = str_replace(array('[ANO_MES]' ,'[DS_RECUSADO]'), array($row['dt_municipio_arq_env'], trim($row['ds_recusado'])), $email['email']);
                    $assunto = str_replace('[DS_SIGLA]', $row['ds_empresa'], $email['assunto']);

                    $args = array(
                        'de'      => 'Extranet - Confirmaчуo de Recebimento',
                        'assunto' => $assunto,
                        'para'    => implode(';', $usuario),
                        'cc'      => $email['cc'],
                        'cco'     => $email['cco'],
                        'texto'   => $texto
                    );

                    $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
                }
            }
            else if(intval($row['cd_municipio_arq_tipo']) == 3)
            {
                $this->load->model('projetos/eventos_email_model');

                if(count($usuario) > 0)
                {
                    $cd_evento = 467;

                    $email = $this->eventos_email_model->carrega($cd_evento);

                    $texto = str_replace(array('[ANO_MES]' ,'[DS_RECUSADO]'), array($row['dt_municipio_arq_env'], trim($row['ds_recusado'])), $email['email']);
                    $assunto = str_replace('[DS_SIGLA]', $row['ds_empresa'], $email['assunto']);

                    $args = array(
                        'de'      => 'Extranet - Confirmaчуo de Recebimento',
                        'assunto' => $assunto,
                        'para'    => implode(';', $usuario),
                        'cc'      => $email['cc'],
                        'cco'     => $email['cco'],
                        'texto'   => $texto
                    );

                    $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
                }
            }
        }
        else 
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function envia_email_aceito($cd_municipio_arq_env)
    {
        if($this->get_permissao_edicao())
        {
            $this->load->model('extranet_new/municipio_arq_env_model');

            $cd_usuario = $this->session->userdata('codigo');

            $row = $this->municipio_arq_env_model->carrega($cd_municipio_arq_env);

            $usuario = array();

            foreach($this->municipio_arq_env_model->get_email_usuario($row['cd_empresa']) as $item)
            {               
                $usuario[] = $item['ds_email'];
            }

            if(intval($row['cd_municipio_arq_tipo']) == 1)
            {
                $this->load->model('projetos/eventos_email_model');

                if(count($usuario) > 0)
                {
                    $cd_evento = 465;

                    $email = $this->eventos_email_model->carrega($cd_evento);

                    $texto = str_replace('[ANO_MES]', $row['dt_municipio_arq_env'], $email['email']);
                    $assunto = str_replace('[DS_SIGLA]', $row['ds_empresa'], $email['assunto']);

                    $args = array(
                        'de'      => 'Extranet - Confirmaчуo de Recebimento',
                        'assunto' => $assunto,
                        'para'    => implode(';', $usuario),
                        'cc'      => $email['cc'],
                        'cco'     => $email['cco'],
                        'texto'   => $texto
                    );

                    $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
                }
            }
        }
        else 
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }
}
?>