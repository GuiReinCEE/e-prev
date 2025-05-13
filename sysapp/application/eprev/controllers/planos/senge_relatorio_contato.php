<?php
class senge_relatorio_contato extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('senge_previdencia/relatorio_model');
    }

    function index()
    {
        $this->load->view('planos/senge_relatorio_contato/index.php');
    }

    function listar()
    {
        $data   = array();
        $result = null;
        $args   = array();

        $args["nr_ano"] = $this->input->post("nr_ano", true);

        manter_filtros($args);

        $this->relatorio_model->relatorio_contato($result, $args);
        $data['ar_reg'] = $result->result_array();

        $this->load->view('planos/senge_relatorio_contato/index_result', $data);
    }
}
?>