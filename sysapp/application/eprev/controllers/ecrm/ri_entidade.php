<?php
class ri_entidade extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		$this->load->model('temporario/Entidade_model');

		$args = Array();	
		$data = Array();	
		
		$this->Entidade_model->comboEntidade($result, $args);
		$data['ar_entidade'] = $result->result_array();		
		
		$this->load->view('ecrm/ri_entidade/index.php',$data);
    }	
	
    function listar()
    {
        CheckLogin();
		$this->load->model('temporario/Entidade_model');

		$result = null;
		$data = Array();
		$args = Array();
		
		$args["cd_entidade"] = $this->input->post('cd_entidade', TRUE);
		$args["cd_usuario"]  = $this->session->userdata('codigo');
		
		manter_filtros($args);
		
		$this->Entidade_model->listar($result, $args);
		$data['ar_entidade_item'] = $result->result_array();
		
		$data['ar_item_usuario'] = Array();
		
		foreach($data['ar_entidade_item'] as $ar_item )
		{
			$this->Entidade_model->itemUsuario($result, $ar_item);
			$data['ar_item_usuario'][$ar_item['cd_entidade_item']] = $result->result_array();			
		}
		
		$this->Entidade_model->entidade($result, $args);
		$data['ar_entidade'] = $result->row_array();		
		
		$this->load->view('ecrm/ri_entidade/index_result', $data);
    }		
		
		
    function incluiItemUsuario()
    {
		CheckLogin();

		$this->load->model('temporario/Entidade_model');

		$result = null;
		$args = Array();
		$data = Array();

		$args["cd_entidade_item"] = $this->input->post("cd_entidade_item", TRUE);
		$args["cd_usuario"]       = $this->session->userdata('codigo');
		
		echo $this->Entidade_model->incluiItemUsuario($result, $args);
    }		

    function excluirItemUsuario()
    {
		CheckLogin();

		$this->load->model('temporario/Entidade_model');

		$result = null;
		$args = Array();
		$data = Array();

		$args["cd_entidade_item_usuario"] = $this->input->post("cd_entidade_item_usuario", TRUE);
		
		echo $this->Entidade_model->excluirItemUsuario($result, $args);
    }	
}
