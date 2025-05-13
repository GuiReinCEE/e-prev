<?php
class Municipio_arq_ret extends Controller
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
			$this->load->model('extranet_new/municipio_arq_ret_model');

			$data = array(
                'cd_empresa' => $cd_empresa,
                'empresa'    => $this->municipio_arq_ret_model->get_empresa(),
                'status'     => $this->get_status()
            );

			$this->load->view('ecrm/municipio_arq_ret/index', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar($data = array())
	{
		$this->load->model('extranet_new/municipio_arq_ret_model');

		$args = array(
			'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
            'dt_encaminhamento_ini' => $this->input->post('dt_encaminhamento_ini', TRUE),
            'dt_encaminhamento_fim' => $this->input->post('dt_encaminhamento_fim', TRUE),
            'tp_status'             => $this->input->post('tp_status', TRUE)
		);

        manter_filtros($args);

		$data['collection'] = $this->municipio_arq_ret_model->listar($args);
		
		$this->load->view('ecrm/municipio_arq_ret/index_result', $data);
	}

	public function excluir($cd_municipio_arq_ret)
	{
		if($this->get_permissao_edicao())
		{
			$this->load->model('extranet_new/municipio_arq_ret_model');

        	$cd_usuario = $this->session->userdata('codigo');       
 
        	$this->municipio_arq_ret_model->excluir($cd_municipio_arq_ret, $cd_usuario);

        	redirect('ecrm/municipio_arq_ret');
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>