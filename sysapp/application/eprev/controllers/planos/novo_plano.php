<?php
class Novo_plano extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }
    
    private function get_permissao()
    {
        if(gerencia_in(array('GAP.')))
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
            $this->load->model('projetos/novo_plano_model');

            $data['subprocesso'] = $this->novo_plano_model->get_subprocesso();

            $this->load->view('planos/novo_plano/index', $data);
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
            $this->load->model('projetos/novo_plano_model');

            $args = array(
                'cd_novo_plano_subprocesso' => $this->input->post('cd_novo_plano_subprocesso', TRUE),
                'fl_encerramento'           => $this->input->post('fl_encerramento', TRUE)
            );

            manter_filtros($args);

            $data['collection'] = $this->novo_plano_model->listar($args);

            $this->load->view('planos/novo_plano/index_result', $data);	
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function set_ordem()
    {
        $this->load->model('projetos/novo_plano_model');

        $cd_novo_plano_subprocesso = $this->input->post('cd_novo_plano_subprocesso', TRUE);

        $args = array(
            'cd_novo_plano_estrutura' => $this->input->post('cd_novo_plano_estrutura', TRUE),
            'nr_ordem'                => $this->input->post('nr_ordem', TRUE),
            'cd_usuario'              => $this->session->userdata('codigo')
        );
        
        $this->novo_plano_model->set_ordem($cd_novo_plano_subprocesso, $args);
    }

    public function cadastro($cd_novo_plano_estrutura = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $data['subprocesso'] = $this->novo_plano_model->get_subprocesso();

            if(intval($cd_novo_plano_estrutura) == 0)
            {
                $data['row'] = array(
                    'cd_novo_plano_estrutura'   => '',
                    'cd_novo_plano_subprocesso' => '',
                    'nr_ordem'                  => '',
                    'ds_novo_plano_estrutura'   => '',
                    'dt_encerramento'           => ''
                );
            }
            else
            {
                $data['row'] = $this->novo_plano_model->carrega($cd_novo_plano_estrutura);
            }

            $this->load->view('planos/novo_plano/cadastro', $data);
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
            $this->load->model('projetos/novo_plano_model');

            $cd_novo_plano_estrutura = $this->input->post('cd_novo_plano_estrutura', TRUE);

            $args = array(
                'cd_novo_plano_subprocesso' => $this->input->post('cd_novo_plano_subprocesso', TRUE),
                'nr_ordem'                  => $this->input->post('nr_ordem', TRUE),
                'ds_novo_plano_estrutura'   => $this->input->post('ds_novo_plano_estrutura', TRUE),
                'cd_usuario'                => $this->session->userdata('codigo')
            );

            if(intval($cd_novo_plano_estrutura) == 0)
            {
                $cd_novo_plano_estrutura = $this->novo_plano_model->salvar($args);
            }
            else
            {
                $this->novo_plano_model->atualizar($cd_novo_plano_estrutura, $args);
            }
            
            redirect('planos/novo_plano/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function set_ordem_subprocesso()
    {
        $this->load->model('projetos/novo_plano_model');

        $cd_novo_plano_subprocesso = $this->input->post('cd_novo_plano_subprocesso', TRUE);

        $row = $this->novo_plano_model->set_ordem_subprocesso($cd_novo_plano_subprocesso);

        echo json_encode($row);
    }

    public function desativar($cd_novo_plano_estrutura)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $this->novo_plano_model->desativar($cd_novo_plano_estrutura, $this->session->userdata('codigo'));

            redirect('planos/novo_plano/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function ativar($cd_novo_plano_estrutura)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $this->novo_plano_model->ativar($cd_novo_plano_estrutura, $this->session->userdata('codigo'));

            redirect('planos/novo_plano/cadastro/'.$cd_novo_plano_estrutura, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function plano()
    {
        if($this->get_permissao())
        {
            $this->load->view('planos/novo_plano/plano');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function plano_listar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $data['collection'] = $this->novo_plano_model->listar_plano();

            $this->load->view('planos/novo_plano/plano_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function plano_cadastro($cd_novo_plano = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            if(intval($cd_novo_plano) == 0)
            {
                $data['row'] = array(
                    'cd_novo_plano'       => '',
                    'ds_nome_plano'       => '',
                    'dt_limite_aprovacao' => '',
                    'dt_inicio'           => ''
                );
            }
            else
            {
                $data['row'] = $this->novo_plano_model->carrega_plano($cd_novo_plano);
            }

            $this->load->view('planos/novo_plano/plano_cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function plano_salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $cd_novo_plano = $this->input->post('cd_novo_plano', TRUE);

            $args = array(
                'ds_nome_plano'       => $this->input->post('ds_nome_plano', TRUE),
                'dt_limite_aprovacao' => $this->input->post('dt_limite_aprovacao', TRUE),
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_novo_plano) == 0)
            {
                $cd_novo_plano = $this->novo_plano_model->salvar_plano($args);
            }
            else
            {
                $this->novo_plano_model->atualizar_plano($cd_novo_plano, $args);
            }

            redirect('planos/novo_plano/plano_cadastro/'.intval($cd_novo_plano), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        } 
    }

    public function iniciar_atividade($cd_novo_plano)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $this->novo_plano_model->cria_atividade_plano(intval($cd_novo_plano), $this->session->userdata('codigo'));

            redirect('planos/novo_plano/atividade/'.$cd_novo_plano, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function atividade($cd_novo_plano)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $data['row'] = $this->novo_plano_model->carrega_plano($cd_novo_plano);

            $collection = $this->novo_plano_model->get_subprocesso();

            foreach ($collection as $key => $item) 
            {
                $collection[$key]['atividade'] = array();

                $collection[$key]['atividade'] = $this->novo_plano_model->listar_atividade(
                    $cd_novo_plano,
                    $item['value']
                );
            }

            $data['collection'] = $collection;
            
            $this->load->view('planos/novo_plano/atividade',$data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }   
    }

    public function acompanhamento($cd_novo_plano, $cd_novo_plano_atividade, $cd_novo_plano_atividade_acompanhamento = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $data = array(
                'collection' => $this->novo_plano_model->listar_acompanhamento($cd_novo_plano_atividade),
                'row'        => $this->novo_plano_model->carrega_plano($cd_novo_plano),
                'atividade'  => $this->novo_plano_model->carrega_atividade($cd_novo_plano_atividade)
            );

            if(intval($cd_novo_plano_atividade_acompanhamento) == 0)
            {
                $data['acompanhamento'] = array(
                    'cd_novo_plano_atividade_acompanhamento' => '',
                    'ds_acompanhamento'                      => ''
                );
            }
            else
            {
                $data['acompanhamento'] = $this->novo_plano_model->carrega_acompanhamento($cd_novo_plano_atividade_acompanhamento);
            }

            $this->load->view('planos/novo_plano/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }   
    }

    public function salvar_acompanhamento()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $cd_novo_plano_atividade_acompanhamento = $this->input->post('cd_novo_plano_atividade_acompanhamento', TRUE);
            $cd_novo_plano_atividade                = $this->input->post('cd_novo_plano_atividade', TRUE);
            $cd_novo_plano                          = $this->input->post('cd_novo_plano', TRUE);

            $args = array(
                'ds_acompanhamento'       => $this->input->post('ds_acompanhamento', TRUE),
                'cd_usuario'              => $this->session->userdata('codigo')
            );

            if(intval($cd_novo_plano_atividade_acompanhamento) == 0)
            {
                $this->novo_plano_model->salvar_acompanhamento($cd_novo_plano_atividade, $args);
            }
            else
            {
                $this->novo_plano_model->atualizar_acompanhamento($cd_novo_plano_atividade_acompanhamento, $args);
            }

            redirect('planos/novo_plano/acompanhamento/'.$cd_novo_plano.'/'.$cd_novo_plano_atividade, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }  
    } 

    public function concluir_atividade($cd_novo_plano, $cd_novo_plano_atividade)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_plano_model');

            $this->novo_plano_model->concluir_atividade($cd_novo_plano_atividade, $this->session->userdata('codigo'));

            redirect('planos/novo_plano/atividade/'.$cd_novo_plano, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }  
    }
}