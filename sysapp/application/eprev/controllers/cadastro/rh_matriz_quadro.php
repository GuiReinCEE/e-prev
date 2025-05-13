<?php
class Rh_matriz_quadro extends Controller
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
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $data = array(
                'conceito' => $this->matriz_quadro_model->get_conceito(),
                'acao'     => $this->matriz_quadro_model->get_acao()
            );

            $this->load->view('cadastro/rh_matriz_quadro/index', $data);
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
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $args = array(
                'cd_matriz_conceito' => $this->input->post('cd_matriz_conceito', TRUE),
                'cd_matriz_acao'     => $this->input->post('cd_matriz_acao', TRUE)
            );

            manter_filtros($args);
            
            $data['collection'] = $this->matriz_quadro_model->listar($args);

            $this->load->view('cadastro/rh_matriz_quadro/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_matriz_quadro = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $data = array(
                'conceito' => $this->matriz_quadro_model->get_conceito(),
                'acao'     => $this->matriz_quadro_model->get_acao()
            );

            if(intval($cd_matriz_quadro) == 0)
            {
                $data['row'] = array(
                    'cd_matriz_quadro'     => intval($cd_matriz_quadro),
                    'cd_matriz_conceito_a' => '',
                    'cd_matriz_conceito_b' => '',
                    'cd_matriz_acao'       => ''
                );
            }
            else
            {
                $data['row'] = $this->matriz_quadro_model->carrega($cd_matriz_quadro);
            }

            $this->load->view('cadastro/rh_matriz_quadro/cadastro', $data);
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

            redirect('cadastro/rh_matriz_quadro', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function matriz()
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/matriz_quadro_model');

            $args = array(
                'cd_matriz_conceito' => '',
                'cd_matriz_acao'     => ''
            );
            
            $collection = $this->matriz_quadro_model->listar($args);

            $data['collection'] = array();

            $conceito_a = '';

            $i = 0;

            foreach ($collection as $key => $item) 
            {
                if(trim($conceito_a) != trim($item['ds_matriz_conceito_a']))
                {
                    $i ++;
                    $conceito_a = trim($item['ds_matriz_conceito_a']);
                    $data['collection'][$i] = array();
                }

                $data['collection'][$i][] = array(
                    'cd_matriz' => trim($item['ds_matriz_conceito_a']).trim($item['ds_matriz_conceito_b']),
                    'ds_matriz' => trim($item['ds_matriz_acao']),
                    'cor_fundo' => $item['cor_fundo'],
                    'cor_texto' => $item['cor_texto']
                );
            }

            $this->load->view('cadastro/rh_matriz_quadro/matriz', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}