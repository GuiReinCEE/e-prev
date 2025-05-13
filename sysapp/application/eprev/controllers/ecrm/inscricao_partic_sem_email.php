<?php
class Inscricao_partic_sem_email extends Controller
{
    function __construct()
    {
        parent::Controller();
        CheckLogin();
    }

    private function get_permissao()
    {
        #Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Shaiane de Oliveira Tavares SantAnna
        else if($this->session->userdata('codigo') == 228)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function index($cd_plano = "", $cd_plano_empresa = "")
    {
        if($this->get_permissao())
		{
            $result = null;
            $args = array();
            $data = array();

            $data['cd_plano']         = $cd_plano;
            $data['cd_plano_empresa'] = $cd_plano_empresa;

            $this->load->view('ecrm/inscricao_partic_sem_email/index',$data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    function listar()
    {
        $this->load->model('public/cadastro_sem_email_model');

        $result = null;
        $data = Array();
        $args = Array();

        $args["cd_plano_empresa"]          = $this->input->post("cd_plano_empresa", TRUE); 
        $args["cd_plano"]                  = $this->input->post("cd_plano", TRUE);
        $args["fl_plano"]                  = $this->input->post("fl_plano", TRUE);
        $args["fl_dt_cancela_inscricao"]   = $this->input->post("fl_dt_cancela_inscricao", TRUE);
        $args["fl_dt_desligamento_eletro"] = $this->input->post("fl_dt_desligamento_eletro", TRUE);
        $args["dt_inclusao_inicio"]        = $this->input->post("dt_inclusao_inicio", TRUE);
        $args["dt_inclusao_fim"]           = $this->input->post("dt_inclusao_fim", TRUE);
        $args["dt_ingresso_inicio"]        = $this->input->post("dt_ingresso_inicio", TRUE);
        $args["dt_ingresso_fim"]           = $this->input->post("dt_ingresso_fim", TRUE);

        $this->cadastro_sem_email_model->listar($result, $args);

        $data['collection'] = $result->result_array();

        $this->load->view('ecrm/inscricao_partic_sem_email/partial_result',$data);
    }    
}
?>