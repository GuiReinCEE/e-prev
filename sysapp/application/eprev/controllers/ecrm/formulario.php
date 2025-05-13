<?php
class formulario extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('extranet/formulario_model');
    }
    
    function index()
    {        
        $this->load->view('ecrm/formulario/index.php');
    }
    
    function listar()
    {
        $data   = Array();
        $args   = Array();
        $result = null;	
        
        $args["cd_empresa"] = $this->input->post("cd_plano_empresa", TRUE);
        $args["cd_plano"]   = $this->input->post("cd_plano", TRUE);
        
        $this->formulario_model->listar($result, $args);

        $data['collection'] = $result->result_array();
        
        $this->load->view('ecrm/formulario/index_result.php', $data);
    }
    
    function cadastro()
    {        
        $this->load->view('ecrm/formulario/cadastro.php');
    }
    
    function salvar()
    {
        $data   = Array();
        $args   = Array();
        $result = null;	
        
        $args["cd_empresa"]          = $this->input->post("cd_plano_empresa", TRUE);
        $args["cd_plano"]            = $this->input->post("cd_plano", TRUE);
        $args["arquivo"]             = $this->input->post("arquivo", TRUE);
        $args["arquivo_nome"]        = $this->input->post("arquivo_nome", TRUE);
        $args["cd_usuario_inclusao"] = $this->session->userdata('codigo');
        
        $this->formulario_model->salvar($result, $args);
        
        copy("./up/extranet_formulario/".$args["arquivo"], "./../eletroceee/extranet/up/formulario/".$args["arquivo"]);
        unlink("./up/extranet_formulario/".$args["arquivo"]);
        
        redirect("ecrm/formulario/", "refresh");
    }
    
    function excluir($cd_formulario)
    {
        $args = Array();
        $data = Array();
        $result = null;
        
        $args["cd_formulario"]      = $cd_formulario;
        $args["cd_usuario_exclusao"] = $this->session->userdata('codigo');
        
        $this->formulario_model->excluir($result, $args);
        
        redirect("ecrm/formulario/", "refresh");
    }
}

?>