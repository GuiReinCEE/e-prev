<?php
class Rh_matriz_acao extends Controller
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
            $this->load->view('cadastro/rh_matriz_acao/index');
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
            $this->load->model('rh_avaliacao/matriz_acao_model');

            $args = array();

            manter_filtros($args);

            $data['collection'] = $this->matriz_acao_model->listar($args);

            $this->load->view('cadastro/rh_matriz_acao/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_matriz_acao = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/matriz_acao_model');

            $data['drop'] = array(
                array('value' => 'S', 'text' => 'Sim'),
                array('value' => 'N', 'text' => 'Não')
            );
             
            if(intval($cd_matriz_acao) == 0)
            {
                $data['row'] = array(
                    'cd_matriz_acao' => intval($cd_matriz_acao),
                    'ds_matriz_acao' => '',
                    'fl_progressao'  => '',
                    'fl_promocao'    => '',
                    'cor_fundo'      => '',
                    'cor_texto'      => ''
                );
            }
            else
            {
                $data['row'] = $this->matriz_acao_model->carrega($cd_matriz_acao);
            }

            $this->load->view('cadastro/rh_matriz_acao/cadastro', $data);
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
            $this->load->model('rh_avaliacao/matriz_acao_model');

            $cd_matriz_acao = $this->input->post('cd_matriz_acao', TRUE);

            $args = array(
                'ds_matriz_acao' => $this->input->post('ds_matriz_acao', TRUE),
                'fl_progressao'  => $this->input->post('fl_progressao', TRUE),
                'fl_promocao'    => $this->input->post('fl_promocao', TRUE),
                'cor_fundo'      => $this->input->post('cor_fundo', TRUE),
                'cor_texto'      => $this->input->post('cor_texto', TRUE),
                'cd_usuario'     => $this->session->userdata('codigo')

            );

            if(intval($cd_matriz_acao) == 0)
            {
                $this->matriz_acao_model->salvar($args);
            }
            else
            {
                $this->matriz_acao_model->atualizar($cd_matriz_acao, $args);
            }

            redirect('cadastro/rh_matriz_acao', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}