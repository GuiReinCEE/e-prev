<?php
class Atendimento_rodizio extends Controller
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
            $this->load->view('ecrm/atendimento_rodizio/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	$this->load->model('projetos/atendimento_rodizio_model');

        $args = array(
            'dt_atendimento_rodizio_ini' => $this->input->post('dt_atendimento_rodizio_ini', TRUE),
            'dt_atendimento_rodizio_fim' => $this->input->post('dt_atendimento_rodizio_fim', TRUE),
            'tp_turno'                   => $this->input->post('tp_turno', TRUE)
        );

        manter_filtros($args); 

    	$data['collection'] = $this->atendimento_rodizio_model->listar($args);

        foreach ($data['collection'] as $key => $value) 
        {
            $data['collection'][$key]['atendente'] = $this->atendimento_rodizio_model->listar_atendente(
                $data['collection'][$key]['cd_atendimento_rodizio']
            );
        }

    	$this->load->view('ecrm/atendimento_rodizio/index_result', $data);
    }

    public function cadastro($cd_atendimento_rodizio = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/atendimento_rodizio_model');

            $data['atendente'] = $this->atendimento_rodizio_model->get_atendente();

            if(intval($cd_atendimento_rodizio) == 0)
            {
                $data['row'] = array(
                    'cd_atendimento_rodizio' => intval($cd_atendimento_rodizio),
                    'dt_atendimento_rodizio' => '',
                    'tp_turno'               => ''
                );
            }
            else
            {
                $data['row'] = $this->atendimento_rodizio_model->carrega($cd_atendimento_rodizio);
            }

            foreach ($data['atendente'] as $key => $item) 
            {
                $data['atendente'][$key]['tp_posicao'] = '';

                $atendimento_rodizio = $this->atendimento_rodizio_model->atendimento_rodizio($cd_atendimento_rodizio, $item['cd_usuario']);

                if(count($atendimento_rodizio) > 0)
                {
                    $data['atendente'][$key]['tp_posicao'] = $atendimento_rodizio['tp_posicao'];
                }
            }

            $this->load->view('ecrm/atendimento_rodizio/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/atendimento_rodizio_model');

            $cd_atendimento_rodizio = $this->input->post('cd_atendimento_rodizio', TRUE);

            $args = array(   
                'cd_atendimento_rodizio' => $cd_atendimento_rodizio,
                'dt_atendimento_rodizio' => $this->input->post('dt_atendimento_rodizio', TRUE),
                'tp_turno'               => $this->input->post('tp_turno', TRUE),
                'atendente'              => array(),
                'cd_usuario'             => $this->session->userdata('codigo')
            );

            if(is_array($this->input->post('atendente', TRUE)))
            {
                $args['atendente'] = $this->input->post('atendente', TRUE);
            }

            if(intval($cd_atendimento_rodizio) == 0)
            {
                $this->atendimento_rodizio_model->salvar($args);
            }
            else
            {
                $this->atendimento_rodizio_model->atualizar($cd_atendimento_rodizio, $args);
            }

            redirect('ecrm/atendimento_rodizio', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }                  
    }
}
