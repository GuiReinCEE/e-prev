<?php
class Treinamentos_documento extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
		$this->load->model('projetos/treinamentos_documento_model');
		
        $data = array(
            'tipo' => $this->treinamentos_documento_model->get_tipo()
        );
		
		$this->load->view('servico/treinamentos_documento/index', $data);
    }

    public function listar()
    {
    	$this->load->model('projetos/treinamentos_documento_model');

    	$args = array(
    		'numero'                          => $this->input->post('numero', TRUE),
    		'ano'                             => $this->input->post('ano', TRUE),
    		'nome'                            => $this->input->post('nome', TRUE),
    		'dt_inicio_ini'                   => $this->input->post('dt_inicio_ini', TRUE),
    		'dt_inicio_fim'                   => $this->input->post('dt_inicio_fim', TRUE),
    		'dt_final_ini'                    => $this->input->post('dt_final_ini', TRUE),
    		'dt_final_fim'                    => $this->input->post('dt_final_fim', TRUE),
            'cd_treinamento_colaborador_tipo' => $this->input->post('cd_treinamento_colaborador_tipo', TRUE)
    	);

		manter_filtros($args);

        $data['collection'] = $this->treinamentos_documento_model->listar($this->session->userdata('cd_treinamento_colaborador'), $args);
		
		$this->load->view('servico/treinamentos_documento/index_result', $data);
    }

    public function documento($cd_treinamento_colaborador)
    {
        $this->load->model('projetos/treinamentos_documento_model');

        $data = array(
            'row'        => $this->treinamentos_documento_model->carrega(intval($cd_treinamento_colaborador)),
            'collection' => $this->treinamentos_documento_model->listar_documento(intval($cd_treinamento_colaborador))
        );

        $this->load->view('servico/treinamentos_documento/documento',$data);
    }


    public function diretoria()
    {
        $this->load->model('projetos/treinamentos_documento_model');
        
        $data = array(
            'tipo' => $this->treinamentos_documento_model->get_tipo()
        );
        
        $this->load->view('servico/treinamentos_documento/diretoria', $data);
    }

    public function diretoria_listar()
    {
        $this->load->model('projetos/treinamentos_documento_model');

        $args = array(
            'numero'                          => $this->input->post('numero', TRUE),
            'ano'                             => $this->input->post('ano', TRUE),
            'nome'                            => $this->input->post('nome', TRUE),
            'dt_inicio_ini'                   => $this->input->post('dt_inicio_ini', TRUE),
            'dt_inicio_fim'                   => $this->input->post('dt_inicio_fim', TRUE),
            'dt_final_ini'                    => $this->input->post('dt_final_ini', TRUE),
            'dt_final_fim'                    => $this->input->post('dt_final_fim', TRUE),
            'cd_treinamento_colaborador_tipo' => $this->input->post('cd_treinamento_colaborador_tipo', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->treinamentos_documento_model->listar($this->session->userdata('cd_treinamento_colaborador'), $args);
        
        $this->load->view('servico/treinamentos_documento/index_result', $data);
    }

    public function documento_diretoria($cd_treinamento_diretoria_conselhos)
    {
        $this->load->model('projetos/treinamentos_documento_model');

        $data = array(
            'row'        => $this->treinamentos_documento_model->carrega_diretoria(intval($cd_treinamento_diretoria_conselhos)),
            'collection' => $this->treinamentos_documento_model->listar_documento_diretoria(intval($cd_treinamento_diretoria_conselhos))
        );

        $this->load->view('servico/treinamentos_documento/documento_diretoria',$data);
    }
}