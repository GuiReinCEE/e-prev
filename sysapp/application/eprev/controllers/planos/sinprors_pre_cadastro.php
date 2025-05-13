<?php

class sinprors_pre_cadastro extends Controller
{

    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('expansao/pre_cadastro_model');
    }

    function index()
    {
        $this->load->view('planos/sinprors_pre_cadastro/index.php');
    }

    function listar()
    {
        $data['collection'] = array();
        $result = null;
        $args = array();

        $args["tp_pre_cadastro"]           = 'P';
        $args["dt_inclusao_inicial"]       = $this->input->post("dt_inclusao_inicial", true);
        $args["dt_inclusao_final"]         = $this->input->post("dt_inclusao_final", true);
        $args["ds_nome"]                   = $this->input->post("ds_nome", true);
        $args["nr_cpf"]                    = $this->input->post("nr_cpf", true);
        $args["cd_enviado"]                = $this->input->post("cd_enviado", true);
        $args["dt_acompanhamento_inicial"] = $this->input->post("dt_acompanhamento_inicial", true);
        $args["dt_acompanhamento_final"]   = $this->input->post("dt_acompanhamento_final", true);

        manter_filtros($args);

        $this->pre_cadastro_model->listar($result, $args);

        $data['collection'] = $result->result_array();

        $this->load->view('planos/sinprors_pre_cadastro/partial_result', $data);
    }

    function simulador()
    {
        $this->load->view('planos/sinprors_pre_cadastro/simulador.php');
    }

    function cadastro($cd_pre_cadastro = 0)
    {
        if(gerencia_in(Array('GRI')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_pre_cadastro'] = intval($cd_pre_cadastro);
            $args['cd_pre_cadastro'] = intval($cd_pre_cadastro);

            if ($data['cd_pre_cadastro'] == 0)
            {
                $data['row'] = Array(
                  'cd_pre_cadastro' => 0,
                  'ds_nome' => '',
                  'ds_email' => '',
                  'nr_telefone' => '',
                  'nr_matricula' => '',
                  'nr_cpf' => '',
                  'dt_nascimento' => '',
                  'ds_duvida' => ''
                );
            }
            else
            {
                $this->pre_cadastro_model->carrega($result, $args);

                $data['row'] = $result->row_array();
            }
            
            $this->load->view('planos/sinprors_pre_cadastro/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }

        
    }
    
    function simulador_listar()
    {
        $data['collection'] = array();
        $result = null;
        $args = array();

        $args["tp_pre_cadastro"] = 'S';
        $args["dt_inclusao_inicial"]       = $this->input->post("dt_inclusao_inicial", true);
        $args["dt_inclusao_final"]         = $this->input->post("dt_inclusao_final", true);
        $args["ds_nome"]                   = $this->input->post("ds_nome", true);
        $args["nr_cpf"]                    = $this->input->post("nr_cpf", true);
        $args["cd_enviado"]                = $this->input->post("cd_enviado", true);
        $args["dt_acompanhamento_inicial"] = $this->input->post("dt_acompanhamento_inicial", true);
        $args["dt_acompanhamento_final"]   = $this->input->post("dt_acompanhamento_final", true);

        $this->pre_cadastro_model->listar($result, $args);

        $data['collection'] = $result->result_array();

        $this->load->view('planos/sinprors_pre_cadastro/simulador_partial_result', $data);
    }
    
    function salvar()
    {
        if(gerencia_in(Array('GRI')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_pre_cadastro"] = $this->input->post("cd_pre_cadastro", TRUE);
            $args["ds_nome"]         = $this->input->post("ds_nome", TRUE);
            $args["ds_email"]        = $this->input->post("ds_email", TRUE);
            $args["nr_telefone"]     = $this->input->post("nr_telefone", TRUE);
            $args["nr_matricula"]    = $this->input->post("nr_matricula", TRUE);
            $args["nr_cpf"]          = $this->input->post("nr_cpf", TRUE);
            $args["dt_nascimento"]   = $this->input->post("dt_nascimento", TRUE);
            $args["ds_duvida"]       = $this->input->post("ds_duvida", TRUE);
            $args["cd_usuario"]     = $this->session->userdata('codigo');

            $cd_pre_cadastro = $this->pre_cadastro_model->salvar($result, $args);
            redirect("planos/sinprors_pre_cadastro/acompanhamento/".$cd_pre_cadastro, "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function acompanhamento($cd_pre_cadastro)
    {
        if(gerencia_in(Array('GRI')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_pre_cadastro'] = intval($cd_pre_cadastro);
            $args['cd_pre_cadastro'] = intval($cd_pre_cadastro);
            
            $this->pre_cadastro_model->acompanhamento($result, $args);

            $data['collection'] = $result->result_array();
            
            
            $this->load->view('planos/sinprors_pre_cadastro/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function salvar_acompanhamento()
    {
        if(gerencia_in(Array('GRI')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_pre_cadastro"] = $this->input->post("cd_pre_cadastro", TRUE);
            $args["cd_enviado"]      = $this->input->post("cd_enviado", TRUE);
            $args["observacao"]      = $this->input->post("observacao", TRUE);
            $args["cd_usuario"]      = $this->session->userdata('codigo');
 
            $this->pre_cadastro_model->salvar_acompanhamento($result, $args);
            redirect("planos/sinprors_pre_cadastro/acompanhamento/".$args["cd_pre_cadastro"], "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    
}

?>