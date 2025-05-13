<?php
class Rh_avaliacao_matriz_quadro extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_validacao()
    {
        if($this->session->userdata('indic_05') == 'S')
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
        if($this->get_validacao())
        {
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $data = array(
                'conceito' => $this->matriz_quadro_model->get_conceito(),
                'acao'     => $this->matriz_quadro_model->get_acao()
            );

            $this->load->view('cadastro/rh_avaliacao_matriz_quadro/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if($this->get_validacao())
        {
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $args = array(
                'cd_matriz_conceito' => $this->input->post('cd_matriz_conceito', TRUE),
                'cd_matriz_acao'     => $this->input->post('cd_matriz_acao', TRUE)
            );

            manter_filtros($args);
            
            $data['collection'] = $this->matriz_quadro_model->listar($args);

            $this->load->view('cadastro/rh_avaliacao_matriz_quadro/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_matriz_quadro = 0)
    {
        if($this->get_validacao())
        {
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $data = array(
                'conceito' => $this->matriz_quadro_model->get_conceito(),
                'acao'     => $this->matriz_quadro_model->get_acao()
            );

            if(intval($cd_matriz_quadro) == 0)
            {
                $data['row'] = array(
                    'cd_matriz_quadro'     => '',
                    'cd_matriz_conceito_a' => '',
                    'cd_matriz_conceito_b' => '',
                    'cd_matriz_acao'       => ''
                );
            }
            else
            {
                $data['row'] = $this->matriz_quadro_model->carrega($cd_matriz_quadro);
            }

            $this->load->view('cadastro/rh_avaliacao_matriz_quadro/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if($this->get_validacao())
        {
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $cd_matriz_quadro = $this->input->post('cd_matriz_quadro', TRUE);

            $args = array(
                'cd_matriz_conceito_a' => $this->input->post('cd_matriz_conceito_a', TRUE),
                'cd_matriz_conceito_b' => $this->input->post('cd_matriz_conceito_b', TRUE),
                'cd_matriz_acao'       => $this->input->post('cd_matriz_acao', TRUE),
                'cd_usuario'           => $this->session->userdata('codigo')
            );

            if(intval($cd_matriz_quadro) == 0)
            {
                $this->matriz_quadro_model->salvar($args);
            }
            else
            {
                $this->matriz_quadro_model->atualizar($cd_matriz_quadro, $args);
            }

            redirect('cadastro/rh_avaliacao_matriz_quadro', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}