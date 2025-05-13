<?php

class cenario_legal_responsavel extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
        $this->load->model('projetos/cenario_legal_responsavel_model');
    }
	
	public function index()
    {
		if (gerencia_in(array('GC','GFC')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->cenario_legal_responsavel_model->gerencia($result, $args);
			$data['arr_gerencia'] = $result->result_array();

			$this->load->view('gestao/cenario_legal_responsavel/index', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
		if (gerencia_in(array('GC','GFC')))
        {
			$args = Array();
			$data = Array();
			$result = null;
								
			manter_filtros($args);

			$this->cenario_legal_responsavel_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('gestao/cenario_legal_responsavel/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function carrega_usuario()
	{
		$result   = null;
		$data     = array();
        $args     = array();
		
		$args['cd_gerencia'] = $this->input->post("cd_gerencia", TRUE);
		
		$this->cenario_legal_responsavel_model->usuario($result, $args);
        $arr = $result->result_array();

        foreach($arr as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
	
    	echo json_encode($data);
	}
	
	public function salvar()
	{
		if (gerencia_in(array('GC','GFC')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_gerencia'] = $this->input->post("cd_gerencia", TRUE);
			$args['cd_usuario']  = $this->input->post("cd_usuario", TRUE);
			
			$this->cenario_legal_responsavel_model->salvar($result, $args);
			
			redirect("gestao/cenario_legal_responsavel/", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function remover()
	{
		if (gerencia_in(array('GC','GFC')))
        {
			$result   = null;
			$data     = array();
			$args     = array();
			
			$args['cd_usuario']  = $this->input->post("cd_usuario", TRUE);
			
			$this->cenario_legal_responsavel_model->remover($result, $args);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
}
?>