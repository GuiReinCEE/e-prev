<?php
class Rh_progressao_promocao extends Controller
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
            $this->load->model('rh_avaliacao/progressao_promocao_model');

            $data['gerencia'] = $this->progressao_promocao_model->get_gerencia_usuario();

            $this->load->view('cadastro/rh_progressao_promocao/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('rh_avaliacao/progressao_promocao_model');

        $args = array(
            'cd_gerencia' => $this->input->post('cd_gerencia', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->progressao_promocao_model->listar($args);

        foreach ($data['collection'] as $key => $item) 
        {
            if(intval($item['cd_progressao_promocao']) > 0)
            {
                $row = $this->progressao_promocao_model->carrega_progressao_promocao($item['cd_progressao_promocao']);

                $data['collection'][$key]['ds_cargo_area_atuacao']  = $row['ds_cargo_area_atuacao'];
                $data['collection'][$key]['ds_classe']              = $row['ds_classe'];
                $data['collection'][$key]['dt_progressao_promocao'] = $row['dt_progressao_promocao'];
            }
        }

        $this->load->view('cadastro/rh_progressao_promocao/index_result', $data);           
    }

    public function cadastro($cd_usuario, $cd_progressao_promocao = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/progressao_promocao_model');

            if(intval($cd_progressao_promocao) == 0)
            {
                $data['row'] = $this->progressao_promocao_model->carrega($cd_usuario);
            }
            else
            {
                $data['row'] = $this->progressao_promocao_model->carrega_progressao_promocao($cd_progressao_promocao);
            }

            $data['classe']        = $this->progressao_promocao_model->get_classe($data['row']['cd_cargo_area_atuacao']);
            $data['area_atuacao']  = $this->progressao_promocao_model->get_area_atuacao($data['row']['cd_gerencia']);
            $data['classe_padrao'] = $this->progressao_promocao_model->get_classe_padrao($data['row']['cd_classe']);
            $data['collection']    = $this->progressao_promocao_model->listar_progressao_promocao($cd_usuario);

            $this->load->view('cadastro/rh_progressao_promocao/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function classe()
    {
        $this->load->model('rh_avaliacao/progressao_promocao_model');

        $cd_cargo_area_atuacao = $this->input->post('cd_cargo_area_atuacao', TRUE);

        $data = array();

        $classe = $this->progressao_promocao_model->get_classe($cd_cargo_area_atuacao);

        if(count($classe) == 1)
        {
            $data['cd_classe'] = $classe[0]['value'];
        }

        foreach($classe as $item)
        {
            $data['classe'][] = array(
                'value' => $item['value'],
                'text'  => utf8_encode($item['text'])
            );
        }

        echo json_encode($data);
    }

    public function classe_padrao()
    {
        $this->load->model('rh_avaliacao/progressao_promocao_model');

        $cd_classe = $this->input->post('cd_classe', TRUE);

        $data = array();

        $classe_padrao = $this->progressao_promocao_model->get_classe_padrao($cd_classe);

        foreach($classe_padrao as $item)
        {
            $data[] = array(
                'value' => $item['value'],
                'text'  => utf8_encode($item['text'])
            );
        }

        echo json_encode($data);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/progressao_promocao_model');

            $cd_usuario             = $this->input->post('cd_usuario', TRUE);
            $cd_progressao_promocao = $this->input->post('cd_progressao_promocao', TRUE);

            $args = array(
                'cd_cargo_area_atuacao'  => $this->input->post('cd_cargo_area_atuacao', TRUE),
                'cd_classe'              => $this->input->post('cd_classe', TRUE),
                'cd_classe_padrao'       => $this->input->post('cd_classe_padrao', TRUE),
                'dt_progressao_promocao' => $this->input->post('dt_progressao_promocao', TRUE),
                'cd_usuario'             => $this->session->userdata('codigo')
            );

            if(intval($cd_progressao_promocao) == 0)
            {
                $this->progressao_promocao_model->salvar($cd_usuario, $args);
            }
            else
            {
                $this->progressao_promocao_model->atualizar($cd_progressao_promocao, $args);
            }

            redirect('cadastro/rh_progressao_promocao/cadastro/'.intval($cd_usuario), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}