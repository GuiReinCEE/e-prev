<?php
class indicador extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('indicador/indicador/index.php');
    }

    function listar()
    {
        CheckLogin();

        $this->load->model('projetos/Raiz_indicadores_model');

        $data['collection'] = array();
        $result=null;

        // --------------------------
		// filtros ...

		$args=array();

		// --------------------------
		// listar ...

        $this->Raiz_indicadores_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('indicador/indicador/partial_result', $data);
    }
}
