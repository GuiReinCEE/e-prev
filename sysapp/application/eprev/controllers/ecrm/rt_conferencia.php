<?php
class rt_conferencia extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
        $this->load->model('rt/rt_conferencia_model');		
    }

    function index()
    {
		$result = null;
		$data   = array();
		$args   = array();

        $this->load->view('ecrm/rt_conferencia/index.php', $data);
    }

    function listar()
    {
		$result = null;
		$data   = array();
		$args   = array();

		$args["dt_rt_inicio"]          = $this->input->post("dt_rt_inicio", TRUE);
		$args["dt_rt_fim"]             = $this->input->post("dt_rt_fim", TRUE);
		$args["cpf"]                   = $this->input->post("cpf", TRUE);
		$args["nome"]                  = $this->input->post("nome", TRUE);
		$args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
		
		manter_filtros($args);

        $this->rt_conferencia_model->listar($result, $args);
		$data['collection'] = $result->result_array();
		
        $this->load->view('ecrm/rt_conferencia/index_result', $data);
    }
}
?>