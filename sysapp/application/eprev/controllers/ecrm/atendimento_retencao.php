<?php
class Atendimento_retencao extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GCM')))
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
    		$this->load->model('projetos/atendimento_retencao_model');

    		$data['usuario'] = $this->atendimento_retencao_model->get_usuario_inclusao();

    		$this->load->view('ecrm/atendimento_retencao/index', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function listar()
    {
    	$this->load->model('projetos/atendimento_retencao_model');

    	$args = array(
			'cd_empresa' 			=> $this->input->post('cd_empresa'),
			'cd_registro_empregado' => $this->input->post('cd_registro_empregado'),
			'seq_dependencia' 		=> $this->input->post('seq_dependencia'),
			'dt_ini' 				=> $this->input->post('dt_ini'),
			'dt_fim' 				=> $this->input->post('dt_fim'),
            'cd_usuario'            => $this->input->post('cd_usuario'),
            'fl_retido'             => $this->input->post('fl_retido'),
			'cd_atendimento'        => $this->input->post('cd_atendimento')
		);

		manter_filtros($args);
		
        $data['qt_anterior'] = 0;

		$data['collection'] = $this->atendimento_retencao_model->listar($args);
		
		$this->load->view('ecrm/atendimento_retencao/index_result', $data);
    }

    public function cadastro($cd_atendimento_retencao = 0, $cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '', $cd_atendimento = 0)
    {
    	if($this->get_permissao())
    	{
    		if(intval($cd_atendimento_retencao) == 0)
        	{
        		$data['row'] = array(
	                'cd_atendimento_retencao' => intval($cd_atendimento_retencao),
	                'cd_empresa'              => $cd_empresa,
                    'cd_atendimento'          => $cd_atendimento,
	                'cd_registro_empregado'   => $cd_registro_empregado,
	                'seq_dependencia'         => $seq_dependencia,
	                'fl_retido'               => '',
	                'ds_descricao'            => ''
	            );

                $data['collection'] = array();
        	}
        	else
        	{
        		$this->load->model('projetos/atendimento_retencao_model');

        		$data['row']        = $this->atendimento_retencao_model->carrega($cd_atendimento_retencao);
                $data['collection'] = $this->atendimento_retencao_model->acompanhamento($cd_atendimento_retencao);
        	}

        	$this->load->view('ecrm/atendimento_retencao/cadastro', $data);
    	}
    	else
    	{
    		exibir_mensagem('ACESSO NÃO PERMITIDO');
    	}
    }

    public function salvar()
    {
        $this->load->model('projetos/atendimento_retencao_model');

        $cd_atendimento_retencao = $this->input->post('cd_atendimento_retencao', TRUE);

        $args = array(
            'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
            'ds_descricao'          => $this->input->post('ds_descricao', TRUE), 
            'cd_atendimento'        => $this->input->post('cd_atendimento', TRUE), 
            'fl_retido'             => $this->input->post('fl_retido', TRUE), 
            'cd_usuario'            => $this->session->userdata('codigo')
        );

        if(intval($cd_atendimento_retencao) == 0)
        {
            $this->atendimento_retencao_model->salvar($args);
        }
        else
        {
            $this->atendimento_retencao_model->atualizar($cd_atendimento_retencao, $args);

            $args = array(
                'ds_descricao' => $this->input->post('ds_atendimento_retencao_acompanhamento', TRUE),
                'cd_usuario'   => $this->session->userdata('codigo')
            );

            $this->atendimento_retencao_model->salvar_acompanhamento($cd_atendimento_retencao, $args);
        }

        redirect('ecrm/atendimento_retencao', 'refresh');
    }

    public function listar_retencao_anterior()
    {
        $this->load->model('projetos/atendimento_retencao_model');

        $cd_atendimento_retencao = $this->input->post('cd_atendimento_retencao', TRUE);

        $args = array(
            'cd_empresa'            => $this->input->post('cd_empresa'),
            'cd_registro_empregado' => $this->input->post('cd_registro_empregado'),
            'seq_dependencia'       => $this->input->post('seq_dependencia'),
            'dt_ini'                => '',
            'dt_fim'                => '',
            'cd_usuario'            => '',
            'cd_atendimento'        => '',
            'fl_retido'             => ''
        );

        $data = $this->atendimento_retencao_model->get_retencao_mes($args['cd_empresa'], $args['cd_registro_empregado'], $args['seq_dependencia']);

        $data['collection'] = $this->atendimento_retencao_model->listar($args, $cd_atendimento_retencao);

        $this->load->view('ecrm/atendimento_retencao/index_result', $data);
    }
}