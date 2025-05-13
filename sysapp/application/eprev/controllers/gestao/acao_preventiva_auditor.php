<?php

class acao_preventiva_auditor extends Controller
{

    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('gestao/acao_preventiva_auditor_model');
    }
    
    public function index()
    {
        if(($this->session->userdata('indic_12') == '*') OR ($this->session->userdata('indic_05') == 'S')) 
        {
            $args = Array();
            $data = Array();
            $result = null;
			
            $this->acao_preventiva_auditor_model->usuario_auditor($result, $args);
            $data['ar_auditor'] = $result->result_array();		

            $this->acao_preventiva_auditor_model->combo_processo($result, $args);
            $data['ar_processo'] = $result->result_array();				

            $this->load->view('gestao/acao_preventiva_auditor/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function listar()
    {
        if(($this->session->userdata('indic_12') == '*') OR ($this->session->userdata('indic_05') == 'S')) 
        {
            $data = array();
            $result = null;
            $args = array();
			
			$args["cd_auditor"]  = $this->input->post("cd_auditor", TRUE);
			$args["cd_processo"] = $this->input->post("cd_processo", TRUE);
			$args["fl_vigente"]  = $this->input->post("fl_vigente", TRUE);
			
			manter_filtros($args);			

            $this->acao_preventiva_auditor_model->listar($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('gestao/acao_preventiva_auditor/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function cadastro($cd_processo)
    {
        if(($this->session->userdata('indic_12') == '*') OR ($this->session->userdata('indic_05') == 'S')) 
        {
            $data = array();
            $result = null;
            $args = array();

            $args['cd_processo'] = $cd_processo;
            $data['cd_processo'] = $cd_processo;

            $this->acao_preventiva_auditor_model->carrega($result, $args);
            $row = $result->row_array();
			$data['ds_processo'] = (isset($row['procedimento']) ? $row['procedimento'] : "");

            $args['cd_usuario_titular']    = (isset($row['cd_usuario_titular']) ? $row['cd_usuario_titular'] : 0);
            $args['cd_usuario_substituto'] = (isset($row['cd_usuario_substituto']) ? $row['cd_usuario_substituto'] : 0);

            $data['cd_usuario_titular'] = $args['cd_usuario_titular'];
            $data['cd_usuario_substituto'] = $args['cd_usuario_substituto'];

            $data['fl_status'] = ((($args['cd_usuario_titular'] == 0) AND ($args['cd_usuario_substituto'] == 0)) ? 0 : 1);

            $this->acao_preventiva_auditor_model->usuario_titular($result, $args);
            $data['arr_usuario_titular'] = $result->result_array();

            $this->acao_preventiva_auditor_model->usuario_substituto($result, $args);
            $data['arr_usuario_substituto'] = $result->result_array();

            $this->load->view('gestao/acao_preventiva_auditor/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if(($this->session->userdata('indic_12') == '*') OR ($this->session->userdata('indic_05') == 'S')) 
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_processo"]           = $this->input->post("cd_processo", TRUE);
            $args["fl_status"]             = $this->input->post("fl_status", TRUE);
            $args["cd_usuario_titular"]    = $this->input->post("cd_usuario_titular", TRUE);
            $args["cd_usuario_substituto"] = $this->input->post("cd_usuario_substituto", TRUE);
            $args["cd_usuario"]            = $this->session->userdata('codigo');

            $this->acao_preventiva_auditor_model->salvar($result, $args);
            redirect("gestao/acao_preventiva_auditor/index", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}
?>