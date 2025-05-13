<?php
class Atendimento_obito extends Controller
{

    function __construct()
    {
        parent::Controller();
    }

    private function get_permissao()
    {
        #Eloisa Helena R. de Rodrigues
        if($this->session->userdata('codigo') == 40)
        {
            return TRUE;
        }
        #Eliane Cristiane Pacheco Alcino
        if($this->session->userdata('codigo') == 39)
        {
            return TRUE;
        }
        #Ygor Roldao Bueno
        if($this->session->userdata('codigo') == 249)
        {
            return TRUE;
        }
        #Cristina Hochmuller da Silva
        if($this->session->userdata('codigo') == 287)
        {
            return TRUE;
        }
        #Nalu Cristina Ribeiro das Neves
        if($this->session->userdata('codigo') == 75)
        {
            return TRUE;
        }
        #Silvia Elisandra Gomes Teixeira
        if($this->session->userdata('codigo') == 354)
        {
            return TRUE;
        }
        #Viviane Schneider de Lara
        else if($this->session->userdata('codigo') == 375)
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

    function index()
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $this->load->model('projetos/Atendimento_obito_model');
            $args = Array();
            $data = Array();
            $this->load->view('ecrm/atendimento_obito/index.php', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Atendimento_obito_model');
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
        $args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
        $args["seq_dependencia"] = $this->input->post("seq_dependencia", TRUE);
        $args["nome"] = $this->input->post("nome", TRUE);
        $args["dt_obito_ini"] = $this->input->post("dt_obito_ini", TRUE);
        $args["dt_obito_fim"] = $this->input->post("dt_obito_fim", TRUE);
        $args["dt_dig_obito_ini"] = $this->input->post("dt_dig_obito_ini", TRUE);
        $args["dt_dig_obito_fim"] = $this->input->post("dt_dig_obito_fim", TRUE);

        $this->Atendimento_obito_model->listar($result, $args);
        $data['collection'] = $result->result_array();
        $this->load->view('ecrm/atendimento_obito/index_result', $data);
    }

    function detalhe($cd_atendimento_obito = 0)
    {
        CheckLogin();

        if($this->get_permissao())
        {
            $this->load->model('projetos/Atendimento_obito_model');
            $args = Array();
            $data = Array();
            $result = null;

            $data['cd_atendimento_obito'] = intval($cd_atendimento_obito);

            if (intval($cd_atendimento_obito) == 0)
            {
                exibir_mensagem("ERRO AO ACESSAR");
            }
            else
            {
                $args['cd_atendimento_obito'] = intval($cd_atendimento_obito);
                $this->Atendimento_obito_model->cadastro($result, $args);
                $data['row'] = $result->row_array();

                $this->Atendimento_obito_model->dependenteListar($result, $args);
                $data['ar_dependente'] = $result->result_array();

                $this->Atendimento_obito_model->acompanhamentoListar($result, $args);
                $data['ar_acompanhamento'] = $result->result_array();
            }
            $this->load->view('ecrm/atendimento_obito/detalhe.php', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function salvar()
    {
        CheckLogin();

        if($this->get_permissao())
        {
            $this->load->model('projetos/Atendimento_obito_model');
            $args = Array();
            $data = Array();
            $result = null;

            $args["cd_atendimento_obito"] = $this->input->post("cd_atendimento_obito", TRUE);
            $args["acompanhamento"] = $this->input->post("acompanhamento", TRUE);
            $args["cd_usuario"] = $this->session->userdata('codigo');

            $this->Atendimento_obito_model->salvar($result, $args);
            redirect("ecrm/atendimento_obito/detalhe/" . $args["cd_atendimento_obito"], "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function encerrar($cd_atendimento_obito = 0)
    {
        CheckLogin();

        if($this->get_permissao())
        {
            $this->load->model('projetos/Atendimento_obito_model');

            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_atendimento_obito"] = intval($cd_atendimento_obito);
            $args["cd_usuario"] = $this->session->userdata('codigo');

            $this->Atendimento_obito_model->encerrar($result, $args);
            redirect("ecrm/atendimento_obito/detalhe/" . $cd_atendimento_obito, "refresh");
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

}
