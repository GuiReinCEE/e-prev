<?php
class email extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('senge/email/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Envia_emails');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$count = 0;
		$args=array();

		$args["devolvido"] = $this->input->post("devolvido", TRUE);
		$args["dt_envio_inicio"] = $this->input->post("dt_envio_inicio", TRUE);
		$args["dt_envio_fim"] = $this->input->post("dt_envio_fim", TRUE);

		// --------------------------
		// listar ...

        $this->Envia_emails->listar_senge( $result, $count, $args );

		$data['collection'] = $result->result_array();

        $data['quantos'] = sizeof($data['collection']);
        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('senge/email/partial_result', $data);
    }
}
