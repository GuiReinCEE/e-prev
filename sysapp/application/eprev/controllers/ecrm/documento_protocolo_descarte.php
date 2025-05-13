<?php

class documento_protocolo_descarte extends Controller
{

	function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('projetos/documento_protocolo_descarte_model');
    }
	
	public function index()
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$this->documento_protocolo_descarte_model->gerencias($result, $args);
		$data['arr_gerencias'] = $result->result_array();
		
		$this->load->view('ecrm/documento_protocolo_descarte/index', $data);
	}
	
	public function listar()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_gerencia"] = $this->input->post("cd_gerencia", TRUE);
		$args["fl_descarte"] = $this->input->post("fl_descarte", TRUE);
		
		manter_filtros($args);
		
		$this->documento_protocolo_descarte_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('ecrm/documento_protocolo_descarte/partial_result', $data);
	}
	
	public function cadastro($cd_documento = 0, $cd_divisao = '')
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$args['cd_documento'] = intval($cd_documento);
		$args['cd_divisao']   = trim($cd_divisao);
		
		if($args['cd_documento'] == 0)
		{
			$data['row'] = Array(
                  'cd_documento'  => 0,
                  'fl_descarte'   => '',
				  'cd_divisao'    => '',
				  'documento'     => '',
				  'gerencia'      => ''
                );
		}
		else
		{
			$this->documento_protocolo_descarte_model->carrega($result, $args);
			$data['row'] = $result->row_array();
		}
						
        $this->load->view('ecrm/documento_protocolo_descarte/cadastro', $data);
    }
	
	public function salvar()
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_documento"] = $this->input->post("cd_tipo_doc", TRUE);
		$args["fl_descarte"]  = $this->input->post("fl_descarte", TRUE);
		$args["acao"]         = $this->input->post("acao", TRUE);
		$args["cd_divisao"]  = $this->session->userdata('divisao');
		$args["cd_usuario"]   = $this->session->userdata('codigo');
		
		$this->documento_protocolo_descarte_model->salvar($result, $args);
		
		redirect("ecrm/documento_protocolo_descarte", "refresh");
	}
	
	public function excluir($cd_documento)
	{
		$args = Array();
		$data = Array();
		$result = null;
		
		$args["cd_documento"] = $cd_documento;
		$args["cd_usuario"]   = $this->session->userdata('codigo');
		
		$this->documento_protocolo_descarte_model->excluir($result, $args);
		
		redirect("ecrm/documento_protocolo_descarte", "refresh");
	}
	
	public function verifica_documento()
	{
		$args["cd_documento"] = $this->input->post("cd_documento", TRUE);
		$args["cd_gerencia"]  = $this->session->userdata('divisao');
	
		$this->documento_protocolo_descarte_model->verifica_documento($result, $args);
		$row = $result->row_array();
		
		if(intval($row['tl']) > 0)
		{
			echo 'erro';
		}
		else
		{
			echo 'ok';
		}
	}
}	
?>