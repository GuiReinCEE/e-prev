<?php
class resumo_atividades_gri extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
        if(gerencia_in(array('GI','GRI')))
        {
            $this->load->view('atividade/resumo_atividades_gri/index.php');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
        CheckLogin();
        if(gerencia_in(array('GI','GRI')))
        {
            $this->load->model('projetos/resumo_atividades_gri_model');
            $data['collection'] = array();
            $result = null;
            $args = array();

            $args['ano'] = $this->input->post('ano', TRUE);

            $this->resumo_atividades_gri_model->listar( $result, $args );

            $data['atividade'] = $result->result_array();

            $this->resumo_atividades_gri_model->listaAnoAnterior( $result, $args );

            $data['ano_anterior'] = $result->row_array();

            $this->resumo_atividades_gri_model->listaAtendimentos( $result, $args );

            $data['atendimento'] = $result->result_array();

            $this->resumo_atividades_gri_model->listaProgramas( $result, $args );

            $data['programa'] = $result->result_array();

            $data['ano'] = $args['ano'];

            $this->load->view('atividade/resumo_atividades_gri/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function carregaAtividades()
    {
        CheckLogin();
        if(gerencia_in(array('GI','GRI')))
        {
            $this->load->model('projetos/resumo_atividades_gri_model');
            $data['collection'] = array();
            $result = null;
            $args = array();

            $args['mes'] = $this->input->post('mes', TRUE);
            $args['ano'] = $this->input->post('ano', TRUE);

            $this->resumo_atividades_gri_model->carregaAtividades( $result, $args );

            $data['collection'] = $result->result_array();

        	$this->load->view('atividade/resumo_atividades_gri/lista_result', $data);
            
        }
    }
}