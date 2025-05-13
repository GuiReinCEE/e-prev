<?php
class Portabilidade extends Controller
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
            $this->load->model('projetos/portabilidade_model');

            $data['status'] = $this->portabilidade_model->get_status();

            $this->load->view('ecrm/portabilidade/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('projetos/portabilidade_model');

        $args = array(
            'cd_portabilidade_status' => $this->input->post('cd_portabilidade_status', TRUE),
            'cd_empresa'              => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado'   => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'         => $this->input->post('seq_dependencia', TRUE),
            'dt_inclusao_ini'         => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim'         => $this->input->post('dt_inclusao_fim', TRUE),
            'dt_acompanhamento_ini'   => $this->input->post('dt_acompanhamento_ini', TRUE),
            'dt_acompanhamento_fim'   => $this->input->post('dt_acompanhamento_fim', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->portabilidade_model->listar($args);

        $this->load->view('ecrm/portabilidade/index_result', $data);
    }

    public function cadastro($cd_portabilidade = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/portabilidade_model');

            $data['status'] = $this->portabilidade_model->get_status();

            if(intval($cd_portabilidade) == 0)
            {
                $data['row'] = array(
                    'cd_portabilidade'                => intval($cd_portabilidade),
                    'cd_empresa'                      => '',
                    'cd_registro_empregado'           => '',
                    'seq_dependencia'                 => '',
                    'ds_portabilidade_acompanhamento' => '',
                    'cd_portabilidade_status'         => 1,
                    'dt_agendamento_alerta'           => ''
                );
            }
            else
            {
                $data['row'] = $this->portabilidade_model->carrega($cd_portabilidade);
                $data['acompanhamento'] = $this->portabilidade_model->lista_acompahamento($cd_portabilidade);
            }

            $this->load->view('ecrm/portabilidade/cadastro', $data);
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
            $this->load->model('projetos/portabilidade_model');

            $cd_portabilidade = $this->input->post('cd_portabilidade', TRUE);

            $args = array( 
                'cd_empresa'                      => $this->input->post('cd_empresa',TRUE),
                'cd_registro_empregado'           => $this->input->post('cd_registro_empregado', TRUE),
                'seq_dependencia'                 => $this->input->post('seq_dependencia', TRUE),
                'ds_portabilidade_acompanhamento' => $this->input->post('ds_portabilidade_acompanhamento', TRUE),
                'cd_portabilidade_status'         => $this->input->post('cd_portabilidade_status', TRUE),
                'dt_agendamento_alerta'           => $this->input->post('dt_agendamento_alerta', TRUE),
                'cd_usuario'                      => $this->session->userdata('codigo')
            );

            if(intval($cd_portabilidade) == 0)
            {
                $cd_portabilidade = $this->portabilidade_model->salvar($args);
            }

            if(trim($args['dt_agendamento_alerta']) != '')
            {
                $assunto = 'Portabilidade RE '.$args['cd_empresa'].'/'.$args['cd_registro_empregado'].'/'.$args['seq_dependencia'];
                $mensagem = 'Acompanhar Portabilidade do RE: '.$args['cd_empresa'].'/'.$args['cd_registro_empregado'].'/'.$args['seq_dependencia'].'
----------------------------------------------------------------------------------------------------
Link: '.site_url('ecrm/portabilidade/cadastro/'.$cd_portabilidade).'
----------------------------------------------------------------------------------------------------
Agendamento realizado por: '.$this->session->userdata('nome').'
';
                $agenda = $this->portabilidade_model->agendar($args['dt_agendamento_alerta'], $assunto, $mensagem, $args['cd_usuario']);

                $args['cd_agenda'] = $agenda['agendar'];
            }
            
            $this->portabilidade_model->salvar_acompanhamento($cd_portabilidade, $args);

            redirect('ecrm/portabilidade/cadastro/'.intval($cd_portabilidade), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    