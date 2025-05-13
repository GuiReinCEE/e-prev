<?php
class Cenario_legal_pesquisa extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
	}

	public function index()
	{
		$this->load->view('gestao/cenario_legal_pesquisa/index');
	}

	public function listar()
	{
		$this->load->model('projetos/cenario_model');

		$args = array(
			'dt_legal_ini'         => $this->input->post('dt_legal_ini', TRUE),
			'dt_legal_fim'         => $this->input->post('dt_legal_fim', TRUE),
			'dt_implementacao_ini' => $this->input->post('dt_implementacao_ini', TRUE),
			'dt_implementacao_fim' => $this->input->post('dt_implementacao_fim', TRUE),
			'tit_capa'             => $this->input->post('tit_capa', TRUE),
			'conteudo'             => $this->input->post('conteudo', TRUE),
			'titulo'               => $this->input->post('titulo', TRUE),
			'referencia'           => $this->input->post('referencia', TRUE)
		);

    	manter_filtros($args);
    	
    	$data = array(
    		'collection' => $this->cenario_model->listar_pesquisa_edicao($args),
    	);

    	$this->load->view('gestao/cenario_legal_pesquisa/index_result', $data);
	}
}
?>