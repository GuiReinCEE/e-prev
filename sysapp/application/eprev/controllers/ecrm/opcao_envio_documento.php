<?php

class Opcao_envio_documento extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/opcao_envio_documento_model');
    }

    private function get_permissao()
    {
        #Vanessa dos Santos Dornelles
         if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Shaiane de Oliveira Tavares SantAnna
        else if($this->session->userdata('codigo') == 228)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
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
            $args = Array();
            $data = Array();
            $result = null;

            $this->load->view('ecrm/opcao_envio_documento/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args['cd_empresa']            = $this->input->post("cd_empresa", TRUE);   
        $args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado", TRUE);   
        $args['seq_dependencia']       = $this->input->post("seq_dependencia", TRUE);  
		$args['dt_solicitao_ini']      = $this->input->post("dt_solicitao_ini", TRUE);  
		$args['dt_solicitao_fim']      = $this->input->post("dt_solicitao_fim", TRUE);  
		$args['nome']                  = $this->input->post("nome", TRUE);  
	            
        manter_filtros($args);

        $this->opcao_envio_documento_model->listar($result, $args);
        $collection = $result->result_array();
		
		$data['collection'] = array();
		
		foreach($collection as $key => $item)
		{
			$args['cd_aa_opcao_envio'] = $item['cd_aa_opcao_envio'];
		
			$data['collection'][$key] = $item;
			
			$this->opcao_envio_documento_model->opcao($result, $args);
            $data['collection'][$key]['opcao'] = $result->result_array();
		}
	
        $this->load->view('ecrm/opcao_envio_documento/index_result', $data);
    }
}

?>