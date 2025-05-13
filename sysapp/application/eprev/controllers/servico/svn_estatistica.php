<?php
class svn_estatistica extends Controller 
{
	function __construct()
	{
		parent::Controller();
		
		CheckLogin();
		
		$this->load->model("svn/revisoes_model");
	}

	private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

	function index($tipo = 'ORACLE')
	{
		if($this->get_permissao())
        {
			$result = null;
			$args   = Array();
			$data   = Array();
			
			$data['tipo'] = trim($tipo);
		
			$this->load->view('servico/svn_estatistica/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	function listar()
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args['ds_repositorio'] = $this->input->post("ds_repositorio");
		
		$this->revisoes_model->inicio($result, $args);
		$row = $result->row_array();
		
		$data['row']['nr_tamanho_inicio'] = $row['nr_tamanho'];
		
		$this->revisoes_model->total($result, $args);
		$row = $result->row_array();
		
		$data['row']['nr_tamanho_atual'] = $row['nr_tamanho'];
		
		$this->revisoes_model->media($result, $args);
		$row = $result->row_array();
		
		$data['row']['nr_tamanho_media'] = $row['nr_tamanho'];
		$data['row']['nr_tamanho_cresc'] = $row['pr_crescimento'];
		
		$this->revisoes_model->mes($result, $args);
		$data['collection'] = $result->result_array();
		
		$this->load->view('servico/svn_estatistica/index_result', $data);
	}
}
?>