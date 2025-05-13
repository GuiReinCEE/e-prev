<?php
class listas extends Controller 
{
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();
		
		$this->load->model("public/listas_model");
	}

	function index($categoria = 'ORACLE')
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$data['categoria'] = trim($categoria);
	
		$this->load->view('servico/listas/index', $data);
	}
	
	function listar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["categoria"] = $this->input->post("categoria", TRUE);
		
		$this->listas_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('servico/listas/index_result', $data);
	}
	
	function cadastro($categoria = '', $codigo = '')
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args['codigo']    = $codigo;
		$args['categoria'] = $categoria;
		
		$this->listas_model->divisao($result, $args);
		$data['arr_divisao'] = $result->result_array();
		
		if(trim($args['codigo']) == '')
		{
			$data['row'] = array(
				'categoria'   => $args['categoria'],
				'codigo'      => $args['codigo'],
				'descricao'   => '',
				'divisao'     => $this->session->userdata('divisao'),
				'valor'       => '',
				'dt_exclusao' => ''
			);
		}
		else
		{
			$this->listas_model->cadastro($result, $args);
			$data['row'] = $result->row_array();	
		}
		
		$this->load->view('servico/listas/cadastro', $data);
	}
	
	function salvar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["categoria"]  = $this->input->post("categoria", TRUE);
		$args["codigo"]     = $this->input->post("codigo", TRUE);
		$args["codigo_new"] = $this->input->post("codigo_new", TRUE);
		$args["descricao"]  = $this->input->post("descricao", TRUE);
		$args["divisao"]    = $this->input->post("divisao", TRUE);
		$args["valor"]      = $this->input->post("valor", TRUE);
		
		$this->listas_model->salvar( $result, $args );
			
		redirect("servico/listas/index/".$args["categoria"], "refresh");	
	}
	
	function excluir($categoria, $codigo)
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["categoria"]  = $categoria;
		$args["codigo"]     = $codigo;
		
		$this->listas_model->excluir( $result, $args );
		
		redirect("servico/listas/index/".$categoria, "refresh");
	}
}
?>