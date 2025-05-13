<?php
class adocao_entidade extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
		
        $this->load->model('projetos/adocao_entidade_model');
    }
	
	public function index()
    {
		if(gerencia_in(array('GAP', 'GB')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$this->adocao_entidade_model->adocao_entidade_periodo($result, $args);
			$data['arr_periodo'] = $result->result_array();
			
			$this->load->view('ecrm/adocao_entidade/index', $data);
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
			
			$args['ds_adocao_entidade']                    = $this->input->post("ds_adocao_entidade", TRUE);
			$args['cd_adocao_entidade_periodo']            = $this->input->post("cd_adocao_entidade_periodo", TRUE);
			$args['fl_adocao_entidade_tipo']               = $this->input->post("fl_adocao_entidade_tipo", TRUE);
			$args['dt_adocao_entidade_acompanhamento_ini'] = $this->input->post("dt_adocao_entidade_acompanhamento_ini", TRUE);
			$args['dt_adocao_entidade_acompanhamento_fim'] = $this->input->post("dt_adocao_entidade_acompanhamento_fim", TRUE);
			
			manter_filtros($args);

            $this->adocao_entidade_model->listar($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('ecrm/adocao_entidade/index_result', $data);
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function cadastro($cd_adocao_entidade = 0)
	{
        if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_adocao_entidade'] = $cd_adocao_entidade;

            if(intval($args['cd_adocao_entidade']) == 0)
            {
                $data['row'] = array (
                    'cd_adocao_entidade'         => intval($args['cd_adocao_entidade']),
                    'ds_adocao_entidade'         => '',
                    'fl_adocao_entidade_tipo'    => '',
                    'cd_adocao_entidade_periodo' => ''
                );
            }
            else
            {
                $this->adocao_entidade_model->carrega($result, $args);
                $data['row'] = $result->row_array();
            }

            $this->load->view('ecrm/adocao_entidade/cadastro', $data);
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

            $args['cd_adocao_entidade']         = $this->input->post("cd_adocao_entidade", TRUE);
            $args['ds_adocao_entidade']         = $this->input->post("ds_adocao_entidade", TRUE);
            $args['fl_adocao_entidade_tipo']    = $this->input->post("fl_adocao_entidade_tipo", TRUE);
            $args['cd_adocao_entidade_periodo'] = $this->input->post("cd_adocao_entidade_periodo", TRUE);
            $args['nome']                       = $this->input->post("nome", TRUE);
            $args['cd_usuario']                 = $this->session->userdata("codigo");

            $cd_adocao_entidade = $this->adocao_entidade_model->salvar($result, $args);

            redirect("ecrm/adocao_entidade/cadastro/".intval($cd_adocao_entidade), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function acompanhamento($cd_adocao_entidade)
    {
		if(gerencia_in(array('GAP', 'GB')))
        {
			$args = Array();
			$data = Array();
			$result = null;
			
			$args['cd_adocao_entidade'] = $cd_adocao_entidade;
			
			$this->adocao_entidade_model->carrega($result, $args);
			$data['row'] = $result->row_array();
			
			$this->load->view('ecrm/adocao_entidade/acompanhamento', $data);
		}
	}
	
	public function listar_acompanhamento()
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_adocao_entidade'] = $this->input->post("cd_adocao_entidade", TRUE);

            $this->adocao_entidade_model->listar_acompanhamento($result, $args);
            $data['collection'] = $result->result_array();

            $this->load->view('ecrm/adocao_entidade/acompanhamento_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function salvar_acompanhamento()
	{
		if(gerencia_in(array('GAP', 'GB')))
        {
            $result   = null;
            $data     = array();
            $args     = array();

            $args['cd_adocao_entidade']                = $this->input->post("cd_adocao_entidade", TRUE);
            $args['ds_adocao_entidade_acompanhamento'] = $this->input->post("ds_adocao_entidade_acompanhamento", TRUE);
            $args['dt_adocao_entidade_acompanhamento'] = $this->input->post("dt_adocao_entidade_acompanhamento", TRUE);
            $args['cd_usuario']                        = $this->session->userdata('codigo');

            $this->adocao_entidade_model->salvar_acompahamento($result, $args);
			
			redirect("ecrm/adocao_entidade/acompanhamento/".intval($args['cd_adocao_entidade']), "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
}