<?php
class Rh_performance extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if($this->session->userdata('indic_09') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
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
            $this->load->model('rh_avaliacao/performance_model');

            $data['grupo'] = $this->performance_model->get_grupo();

            $this->load->view('cadastro/rh_performance/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/performance_model');

            $args = array(
                'cd_grupo' => $this->input->post('cd_grupo', TRUE)
            );

            manter_filtros($args);

            $data['collection'] = $this->performance_model->listar($args);

            $this->load->view('cadastro/rh_performance/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_performance = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/performance_model');

            $data['grupo'] = $this->performance_model->get_grupo();

            if(intval($cd_performance) == 0)
            {
                $data['row'] = array(
                   'cd_performance'           => intval($cd_performance),
                   'cd_grupo'                 => '',
                   'ds_performance'           => '',
                   'ds_performance_sigla'     => '',
                   'ds_performance_descricao' => '',
                   'nr_ponto'                 => ''
                );
            }
            else
            {
                $data['row'] = $this->performance_model->carrega($cd_performance);
            }

            $this->load->view('cadastro/rh_performance/cadastro', $data);
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
            $this->load->model('rh_avaliacao/performance_model');

            $cd_performance = $this->input->post('cd_performance', TRUE);

            $args = array(
                'cd_grupo'                 => $this->input->post('cd_grupo', TRUE),
                'ds_performance'           => $this->input->post('ds_performance', TRUE),
                'ds_performance_sigla'     => $this->input->post('ds_performance_sigla', TRUE),
                'ds_performance_descricao' => $this->input->post('ds_performance_descricao', TRUE),
                'nr_ponto'                 => $this->input->post('nr_ponto', TRUE),
                'cd_usuario'               => $this->session->userdata('codigo')
            );

            if(intval($cd_performance) == 0)
            {
                $this->performance_model->salvar($args);
            }
            else
            {
                $this->performance_model->atualizar($cd_performance, $args);
            }

            redirect('cadastro/rh_performance', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}