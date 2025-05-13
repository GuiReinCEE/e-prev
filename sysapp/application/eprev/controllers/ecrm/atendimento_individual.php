<?php

class atendimento_individual extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/atendimento_individual_model');
    }
	
	public function index()
    {
        if(gerencia_in(array('GAP', 'GB')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $this->atendimento_individual_model->usuario_solicitante($result, $args);
            $data['arr_solicitante'] = $result->result_array();

            $this->atendimento_individual_model->usuario_encaminhado($result, $args);
            $data['arr_encaminhado'] = $result->result_array();

            $this->atendimento_individual_model->usuario_encerrado($result, $args);
            $data['arr_encerrado'] = $result->result_array();

            $this->load->view('ecrm/atendimento_individual/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
        if(gerencia_in(array('GAP', 'GB')))
        {
            $args = Array();
            $data = Array();
            $result = null;

            $args['cd_empresa']                = $this->input->post("cd_empresa", TRUE);
            $args['cd_registro_empregado']     = $this->input->post("cd_registro_empregado", TRUE);
            $args['seq_dependencia']           = $this->input->post("seq_dependencia", TRUE);
            $args['nome']                      = $this->input->post("nome", TRUE);
            $args['dt_cadastro_ini']           = $this->input->post("dt_cadastro_ini", TRUE);
            $args['dt_cadastro_fim']           = $this->input->post("dt_cadastro_fim", TRUE);
            $args['dt_encaminhamento_ini']     = $this->input->post("dt_encaminhamento_ini", TRUE);
            $args['dt_encaminhamento_fim']     = $this->input->post("dt_encaminhamento_fim", TRUE);
            $args['dt_encerramento_ini']       = $this->input->post("dt_encerramento_ini", TRUE);
            $args['dt_encerramento_fim']       = $this->input->post("dt_encerramento_fim", TRUE);
            $args['cd_usuario_inclusao']       = $this->input->post("cd_usuario_inclusao", TRUE);
            $args['cd_usuario_encaminhamento'] = $this->input->post("cd_usuario_encaminhamento", TRUE);
            $args['cd_usuario_encerramento']   = $this->input->post("cd_usuario_encerramento", TRUE);
            $args['fl_status']                 = $this->input->post("fl_status", TRUE);

            manter_filtros($args);

            $this->atendimento_individual_model->listar($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('ecrm/atendimento_individual/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function cadastro($cd_atendimento_individual = 0)
	{
        if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_atendimento_individual'] = $cd_atendimento_individual;

            if(intval($args['cd_atendimento_individual']) == 0)
            {
                $data['row'] = array (
                    'cd_atendimento_individual' => intval($args['cd_atendimento_individual']),
                    'cd_empresa'                => '',
                    'cd_registro_empregado'     => '',
                    'seq_dependencia'           => '',
                    'nome'                      => '',
                    'ds_observacao'             => '',
                    'dt_encaminhamento'         => '',
                    'dt_encerramento'           => ''
                );
            }
            else
            {
                $this->atendimento_individual_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('ecrm/atendimento_individual/cadastro', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }

	}
	
	public function salvar()
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_atendimento_individual'] = $this->input->post("cd_atendimento_individual", TRUE);
            $args['cd_empresa']                = $this->input->post("cd_empresa", TRUE);
            $args['cd_registro_empregado']     = $this->input->post("cd_registro_empregado", TRUE);
            $args['seq_dependencia']           = $this->input->post("seq_dependencia", TRUE);
            $args['nome']                      = $this->input->post("nome", TRUE);
            $args['ds_observacao']             = $this->input->post("ds_observacao", TRUE);
            $args['cd_usuario']                = $this->session->userdata("codigo");

            $cd_atendimento_individual = $this->atendimento_individual_model->salvar($result, $args);

            if(intval($args['cd_atendimento_individual']) == 0)
            {	
                redirect("ecrm/atendimento_individual/cadastro/".intval($cd_atendimento_individual), "refresh");
            }
            else
            {
                redirect("ecrm/atendimento_individual/", "refresh");
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	
	function encaminhar($cd_atendimento_individual)
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_atendimento_individual'] = $cd_atendimento_individual;
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $this->atendimento_individual_model->encaminhar($result, $args);

            redirect("ecrm/atendimento_individual/cadastro/".intval($args['cd_atendimento_individual']), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function encerrar()
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result = null;
            $data = Array();
            $args = Array();

            $args['cd_atendimento_individual'] = $this->input->post("cd_atendimento_individual", TRUE);
            $args['cd_usuario']                = $this->session->userdata('codigo');

            $this->atendimento_individual_model->encerrar($result, $args);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function acompanhamento($cd_atendimento_individual)
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_atendimento_individual'] = $cd_atendimento_individual;

            $this->atendimento_individual_model->carrega($result, $args);
            $data['row'] = $result->row_array();

            $this->load->view('ecrm/atendimento_individual/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function listar_acompanhamento()
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_atendimento_individual'] = $this->input->post("cd_atendimento_individual", TRUE);

            $this->atendimento_individual_model->listar_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('ecrm/atendimento_individual/acompanhamento_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function salvar_acompahamento()
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_atendimento_individual']                = $this->input->post("cd_atendimento_individual", TRUE);
            $args['ds_atendimento_individual_acompanhamento'] = utf8_decode($this->input->post("ds_atendimento_individual_acompanhamento", TRUE));
            $args['cd_usuario']                               = $this->session->userdata('codigo');

            $this->atendimento_individual_model->salvar_acompahamento($result, $args);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function excluir_acompahamento()
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_atendimento_individual_acompanhamento'] = $this->input->post("cd_atendimento_individual_acompanhamento", TRUE);
            $args['cd_usuario']                               = $this->session->userdata('codigo');

            $this->atendimento_individual_model->excluir_acompahamento($result, $args);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

}
?>