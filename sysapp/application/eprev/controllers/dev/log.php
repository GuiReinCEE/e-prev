<?php
class log extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('dev/log/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Log_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$count = 0;
		$args=array();

		$args["tipo"] = $this->input->post("tipo", TRUE);
		$args["limite"] = $this->input->post("limite", TRUE);
		$args["local"] = $this->input->post("local", TRUE);
		$args["descricao"] = $this->input->post("descricao", TRUE);
		$args["data_inicio"] = $this->input->post("data_inicio", TRUE);
		$args["data_fim"] = $this->input->post("data_fim", TRUE);

		// --------------------------
		// listar ...

        $this->Log_model->listar( $result, $count, $args );

		$data['collection'] = $result->result_array();

        $data['quantos'] = sizeof($data['collection']);
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('dev/log/partial_result', $data);
    }
}
