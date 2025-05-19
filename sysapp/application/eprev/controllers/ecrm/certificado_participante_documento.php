<?php

class Certificado_participante_documento extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        $this->load->model('projetos/certificado_participante_documento_model');
    }

    private function get_permissao()
    {
        #Mauro Oliveira Pyhus
        if($this->session->userdata('codigo') == 73)
        {
            return TRUE;
        }
        #Vanessa dos Santos Dornelles
        else if($this->session->userdata('codigo') == 146)
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
        #GUILHERME REINHEIMER
        else if($this->session->userdata('codigo') == 561)
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
    
    public function index()
    {
        if($this->get_permissao())
        {
            $args = Array();
            $data = Array();
            $result = null;
            
            $this->certificado_participante_documento_model->get_patrocinadoras($result, $args);
            $data['arr_patrocinadoras'] = $result->result_array();

            $this->load->view('ecrm/certificado_participante_documento/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    public function listar()
    {
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);

        manter_filtros($args);  

        $this->certificado_participante_documento_model->listar($result, $args);

        $data['collection'] = $result->result_array();

        $this->load->view('ecrm/certificado_participante_documento/partial_result', $data);
    }
    
    function cadastro($cd_certificado_participante_documento = 0)
    {
        if($this->get_permissao())
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_certificado_participante_documento'] = intval($cd_certificado_participante_documento);
            $args['cd_certificado_participante_documento'] = intval($cd_certificado_participante_documento);

            $this->certificado_participante_documento_model->get_patrocinadoras($result, $args);
            $data['arr_patrocinadoras'] = $result->result_array();
            
            if ($cd_certificado_participante_documento == 0)
            {
                $data['row'] = Array(
                  'cd_certificado_participante_documento' => 0,
                  'cd_documento' => '',
                  'fl_verificar' => '',
                  'cd_empresa' => ''
                );
            }
            else
            {
                $this->certificado_participante_documento_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('ecrm/certificado_participante_documento/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar()
    {
        if($this->get_permissao())
        { 
            $args = Array();
            $data = Array();
            $result = null;
            
            $args['cd_certificado_participante_documento'] = $this->input->post("cd_certificado_participante_documento", TRUE);
            $args['cd_documento'] = $this->input->post("cd_tipo_doc", TRUE);
            $args['fl_verificar'] = $this->input->post("fl_verificar", TRUE);
            $args['cd_empresa'] = $this->input->post("cd_empresa", TRUE);
            $args['cd_usuario']                            = $this->session->userdata('codigo');
            
            $this->certificado_participante_documento_model->salvar($result, $args);
            
            redirect("ecrm/certificado_participante_documento/", "refresh");
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }  
    }
    
    function excluir($cd_certificado_participante_documento)
    {
        if($this->get_permissao())
        { 
            $args['cd_certificado_participante_documento'] = intval($cd_certificado_participante_documento);
            $args['cd_usuario']                            = $this->session->userdata('codigo');
            
            $this->certificado_participante_documento_model->excluir($result, $args);
            
            redirect("ecrm/certificado_participante_documento/", "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        } 
    }
    
}

?>