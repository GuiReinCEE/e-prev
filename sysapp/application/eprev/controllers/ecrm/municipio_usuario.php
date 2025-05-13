<?php
class Municipio_usuario extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

    private function get_permissao()
    {
    	#Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
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
        else
        {
            return FALSE;
        }
    }

    public function index()
	{
		if($this->get_permissao())
		{
			$this->load->model('extranet_new/municipio_usuario_model');

			$data = array(
                'empresa' => $this->municipio_usuario_model->get_empresa()
            );

			$this->load->view('ecrm/municipio_usuario/index', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar($data = array())
	{
		$this->load->model('extranet_new/municipio_usuario_model');

		$args = array(
            'cd_empresa' => $this->input->post('cd_empresa', TRUE),
			'fl_interno' => $this->input->post('fl_interno', TRUE)
		);

        manter_filtros($args);

		$data['collection'] = $this->municipio_usuario_model->listar($args);
		
		$this->load->view('ecrm/municipio_usuario/index_result', $data);
	}

	public function cadastro($cd_usuario = 0)
	{
		if($this->get_permissao())
		{
			$this->load->model('extranet_new/municipio_usuario_model');

			$data['empresa'] = $this->municipio_usuario_model->get_empresa();

            if(intval($cd_usuario) == 0)
            {
                $data['row'] = array(
                    'cd_usuario'     => intval($cd_usuario),
                    'cd_empresa'     => '',
                    'ds_nome'        => '',
                    'ds_usuario'     => '',
                    'ds_email'       => '',
                    'fl_troca_senha' => 'S',
                    'fl_interno'     => 'N',
                    'ds_senha'       => ''
                );
            }
            else
            {
                $data['row'] = $this->municipio_usuario_model->carrega($cd_usuario);
            }

			$this->load->view('ecrm/municipio_usuario/cadastro', $data);
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
            $this->load->model('extranet_new/municipio_usuario_model');

            $cd_usuario = $this->input->post('cd_usuario', TRUE);

            $args = array( 
                'cd_empresa'     => $this->input->post('cd_empresa',TRUE),
                'ds_nome'        => $this->input->post('ds_nome', TRUE),
                'ds_usuario'     => $this->input->post('ds_usuario', TRUE),
                'ds_email'       => $this->input->post('ds_email', TRUE),
                'ds_senha'       => MD5(MD5($this->input->post('ds_senha', TRUE))),
                'fl_troca_senha' => $this->input->post('fl_troca_senha', TRUE),
                'fl_interno'     => $this->input->post('fl_interno', TRUE),
                'cd_usuario'     => $this->session->userdata('codigo')
            );

            if(intval($cd_usuario) == 0)
            {
                $this->municipio_usuario_model->salvar($args);
            }
            else
            {
                $ds_senha     = $this->input->post('ds_senha', TRUE);
                $ds_senha_old = $this->input->post('ds_senha_old', TRUE);
                
                if(trim($ds_senha) == trim($ds_senha_old))
                {
                    $args['ds_senha'] = $ds_senha;
                }

                $this->municipio_usuario_model->atualizar($cd_usuario, $args);
            }

            redirect('ecrm/municipio_usuario', 'refresh');
        }
        else 
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}
?>