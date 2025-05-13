<?php
class Interesse_municipio extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	private function get_permissao()
    {
        if(gerencia_in(array('GCM')))
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
			$data = array();

			$this->load->view('ecrm/interesse_municipio/index', $data);
		}
		else 
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function listar($data = array())
	{
		$this->load->model('expansao/interesse_municipio_model');

		$args = array();

        manter_filtros($args);

		$data['collection'] = $this->interesse_municipio_model->listar($args);
		
		$this->load->view('ecrm/interesse_municipio/index_result', $data);
	}

	
}
?>