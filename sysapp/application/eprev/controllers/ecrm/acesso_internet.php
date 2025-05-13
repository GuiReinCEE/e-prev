<?php
class Acesso_internet extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

         $this->load->model('public/conta_acessos_model');
    }

    function index()
    {
        $this->load->view('ecrm/acesso_internet/index');
    }

    function listar()
    {
        $args = array();
        $data = array();
        $result = null;

		$args["dt_ini"] = $this->input->post("dt_ini", TRUE);
		$args["dt_fim"] = $this->input->post("dt_fim", TRUE);

		manter_filtros($args);

        $this->conta_acessos_model->listar($result, $args);
		$data['collection'] = $result->result_array();

        $this->load->view('ecrm/acesso_internet/partial_result', $data);
    }

    function pagina()
    {
        $this->load->view('ecrm/acesso_internet/pagina');
    }

    function pagina_listar()
    {
        $args = array();
        $data = array();
        $result = null;

        $args["dt_ini"] = $this->input->post("dt_ini", TRUE);
        $args["dt_fim"] = $this->input->post("dt_fim", TRUE);

        manter_filtros($args);

        $this->conta_acessos_model->listar_acesso_auto_atendimento($result, $args);
        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/acesso_internet/pagina_result', $data);
    }
}
?>