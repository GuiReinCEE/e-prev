<?php
class Rh_matriz_conceito extends Controller
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
            $this->load->model('rh_avaliacao/matriz_conceito_model');

            $data['grupo'] = $this->matriz_conceito_model->get_grupo();

            $this->load->view('cadastro/rh_matriz_conceito/index', $data);
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
            $this->load->model('rh_avaliacao/matriz_conceito_model');

            $args = array(
                'cd_grupo' => $this->input->post('cd_grupo', TRUE)
            );
            
            manter_filtros($args);

            $data['collection'] = $this->matriz_conceito_model->listar($args);

            $this->load->view('cadastro/rh_matriz_conceito/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_matriz_conceito = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/matriz_conceito_model');

            $data['grupo'] = $this->matriz_conceito_model->get_grupo();

            if(intval($cd_matriz_conceito) == 0)
            {
                $data['row'] = array(
                    'cd_matriz_conceito' => intval($cd_matriz_conceito),
                    'cd_grupo'           => '',
                    'nr_matriz_conceito' => '',
                    'nr_nota_min'        => 0,
                    'nr_nota_max'        => 0
                );
            }
            else
            {
                $data['row'] = $this->matriz_conceito_model->carrega($cd_matriz_conceito);
            }

            $this->load->view('cadastro/rh_matriz_conceito/cadastro', $data);
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
            $this->load->model('rh_avaliacao/matriz_conceito_model');

            $cd_matriz_conceito = $this->input->post('cd_matriz_conceito', TRUE);

            $args = array(
                'cd_grupo'           => $this->input->post('cd_grupo', TRUE),
                'nr_matriz_conceito' => $this->input->post('nr_matriz_conceito', TRUE),
                'nr_nota_min'        => app_decimal_para_db($this->input->post('nr_nota_min', TRUE)),
                'nr_nota_max'        => app_decimal_para_db($this->input->post('nr_nota_max', TRUE)),
                'cd_usuario'         => $this->session->userdata('codigo')
            );

            if(intval($cd_matriz_conceito) == 0)
            {
                $this->matriz_conceito_model->salvar($args);
            }
            else
            {
                $this->matriz_conceito_model->atualizar($cd_matriz_conceito, $args);
            }

            redirect('cadastro/rh_matriz_conceito', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}