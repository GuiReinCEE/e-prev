<?php
class sinprors_relatorio_agenda extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('sinprors_previdencia/relatorio_model');
    }

    function index()
    {
        $this->load->view('planos/sinprors_relatorio_agenda/index.php');
    }

    function listar()
    {
        $data['collection'] = array();
        $result = null;
        $args = array();

        $args["nr_ano"] = $this->input->post("nr_ano", true);

        manter_filtros($args);

        $this->relatorio_model->relatorio_agenda($result, $args);

        $data['ar_reg'] = $result->result_array();

        $this->load->view('planos/sinprors_relatorio_agenda/index_result', $data);
    }
}
?>