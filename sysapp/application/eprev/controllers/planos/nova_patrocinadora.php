<?php
class Nova_patrocinadora extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GP', 'GC')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_estrutura($row)
    {
        $cd_divisao = $this->session->userdata('divisao');
        $cd_usuario = $this->session->userdata('codigo');

        if($cd_divisao == 'GP' OR $row['cd_usuario_responsavel'] == $cd_usuario OR $row['cd_usuario_substituto'] == $cd_usuario)
        {
            return TRUE;
        }
        else if($cd_usuario == 251)
        {
            return TRUE;
        }
        else if($cd_usuario = 170)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_status()
    {
        return array(
            array('value' => 'N', 'text' => 'Não iniciada'),
            array('value' => 'A', 'text' => 'Em andamento'), 
            array('value' => 'E', 'text' => 'Encerrada')
        );
    }

    public function get_usuarios()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        foreach($this->nova_patrocinadora_model->get_usuarios($cd_gerencia) as $item)
        {
            $data[] = array(
                'value' => $item['value'],
                'text'  => utf8_encode($item['text'])
            );
        }
        
        echo json_encode($data);
    }

    public function set_ordem()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $cd_nova_patrocinadora_estrutura = $this->input->post('cd_nova_patrocinadora_estrutura', TRUE);

        $args = array(
            'nr_nova_patrocinadora_estrutura' => $this->input->post('nr_nova_patrocinadora_estrutura', TRUE),
            'cd_usuario'                      => $this->session->userdata('codigo')
        );
        
        $this->nova_patrocinadora_model->set_ordem($cd_nova_patrocinadora_estrutura, $args);
    }

    public function index()
    {
        $this->load->view('planos/nova_patrocinadora/index');
    }  

    public function listar()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $args = array(
        	'fl_desativado' => $this->input->post('fl_desativado', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->nova_patrocinadora_model->listar($args);
        $data['cd_usuario'] = $this->session->userdata('codigo');
        $data['cd_divisao'] = $this->session->userdata('divisao');

        foreach($data['collection'] as $key => $item)
        {               
            $data['collection'][$key]['ds_atividade_dependente'] = array();

            $atividades = $this->nova_patrocinadora_model->get_atividade_estrutura_dependencia($item['cd_nova_patrocinadora_estrutura']);

            foreach($atividades as $key1 => $atividade) 
            {
                $data['collection'][$key]['ds_atividade_dependente'][] = $atividade['ds_atividade_dependente'] ;
            }
        }   

        $this->load->view('planos/nova_patrocinadora/index_result', $data);		
    }

    public function cadastro($cd_nova_patrocinadora_estrutura = 0)
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $data['atividade'] = $this->nova_patrocinadora_model->get_atividades($cd_nova_patrocinadora_estrutura); 

        if(intval($cd_nova_patrocinadora_estrutura) == 0)
        {
            $row = $this->nova_patrocinadora_model->get_proximo_numero();

            $data['row'] = array(
                'cd_nova_patrocinadora_estrutura' => intval($cd_nova_patrocinadora_estrutura),
                'nr_nova_patrocinadora_estrutura' => (count($row) > 0 ? $row['nr_nova_patrocinadora_estrutura'] : 1),
                'ds_nova_patrocinadora_estrutura' => '',
                'ds_atividade'                  => '',
                'cd_gerencia'                   => '',
                'cd_usuario_responsavel'        => '',
                'cd_usuario_substituto'         => '',
                'nr_prazo'                      => '',
                'ds_observacao'                 => '',
                'dt_desativado'                 => ''
            );

            $data['responsavel'] = array();
            $data['substituto']  = array();          
        }
        else 
        {
            $data['row'] = $this->nova_patrocinadora_model->carrega($cd_nova_patrocinadora_estrutura);       

            if($this->get_permissao_estrutura($data['row']))
            {
                $usuario = $this->nova_patrocinadora_model->get_usuarios($data['row']['cd_gerencia']);

                $data['responsavel'] = $usuario;
                $data['substituto']  = $usuario;

                $data['atividade_estrutura_dependencia'] = array();     

                $atividade = $this->nova_patrocinadora_model->get_atividade_estrutura_dependencia($cd_nova_patrocinadora_estrutura);
                    
                foreach($atividade as $item)
                {               
                    $data['atividade_estrutura_dependencia'][] = $item['cd_nova_patrocinadora_estrutura_dep'];
                }                         
                
                $this->load->view('planos/nova_patrocinadora/cadastro', $data); 
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
        }
    }

    public function salvar()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $cd_nova_patrocinadora_estrutura = $this->input->post('cd_nova_patrocinadora_estrutura', TRUE);

        $args = array(
            'nr_nova_patrocinadora_estrutura' => $this->input->post('nr_nova_patrocinadora_estrutura', TRUE),
            'ds_nova_patrocinadora_estrutura' => $this->input->post('ds_nova_patrocinadora_estrutura', TRUE),
            'ds_atividade'                  => $this->input->post('ds_atividade', TRUE),
            'cd_gerencia'                   => $this->input->post('cd_gerencia', TRUE),
            'cd_usuario_responsavel'        => $this->input->post('cd_usuario_responsavel', TRUE),
            'cd_usuario_substituto'         => $this->input->post('cd_usuario_substituto', TRUE),
            'nr_prazo'                      => $this->input->post('nr_prazo', TRUE),
            'ds_observacao'                 => $this->input->post('ds_observacao', TRUE),
            'cd_usuario'                    => $this->session->userdata('codigo')
        );

        $atividade_estrutura_dependencia = $this->input->post('atividade_estrutura_dependencia', TRUE);

        if(!is_array($atividade_estrutura_dependencia))
        {
            $args['atividade_estrutura_dependencia'] = array();
        }
        else
        {
            $args['atividade_estrutura_dependencia'] = $atividade_estrutura_dependencia;
        }
        
        if(intval($cd_nova_patrocinadora_estrutura) == 0)
        {
            $cd_nova_patrocinadora_estrutura = $this->nova_patrocinadora_model->salvar($args);
        }
        else
        {
            $this->nova_patrocinadora_model->atualizar($cd_nova_patrocinadora_estrutura, $args);
        }

        redirect('planos/nova_patrocinadora/cadastro/'.$cd_nova_patrocinadora_estrutura, 'refresh');
    }

    public function desativar($cd_nova_patrocinadora_estrutura)
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $this->nova_patrocinadora_model->desativar($cd_nova_patrocinadora_estrutura, $this->session->userdata('codigo'));

        redirect('planos/nova_patrocinadora/index', 'refresh');
    }

    public function ativar($cd_nova_patrocinadora_estrutura)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/nova_patrocinadora_model');

            $this->nova_patrocinadora_model->ativar($cd_nova_patrocinadora_estrutura, $this->session->userdata('codigo'));

            redirect('planos/nova_patrocinadora/cadastro/'.$cd_nova_patrocinadora_estrutura, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function patrocinadora()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $data['planos'] = $this->nova_patrocinadora_model->get_planos();

        $this->load->view('planos/nova_patrocinadora/patrocinadora', $data);  
    }

    public function patrocinadora_listar()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $args = array(
            'cd_plano'      => $this->input->post('cd_plano', TRUE),
            'dt_inicio_ini' => $this->input->post('dt_inicio_ini', TRUE),
            'dt_inicio_fim' => $this->input->post('dt_inicio_fim', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->nova_patrocinadora_model->patrocinadora_listar($args);

        $this->load->view('planos/nova_patrocinadora/patrocinadora_result', $data);       
    }

    public function patrocinadora_cadastro($cd_nova_patrocinadora = 0)
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $data['planos'] = $this->nova_patrocinadora_model->get_planos();

        if(intval($cd_nova_patrocinadora) == 0)
        {
            $data['row'] = array(
                'cd_nova_patrocinadora' => intval($cd_nova_patrocinadora),
                'ds_nome_patrocinadora' => '',
                'dt_limite_aprovacao' => '',
                'cd_plano'            => '',
                'dt_inicio'           => '',
                'cd_empresa'          => ''
            );
        }
        else
        {
            $data['row'] = $this->nova_patrocinadora_model->patrocinadora_carrega($cd_nova_patrocinadora);                
        } 

        $this->load->view('planos/nova_patrocinadora/patrocinadora_cadastro', $data);   
    }

    public function patrocinadora_salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/nova_patrocinadora_model');

            $cd_nova_patrocinadora = $this->input->post('cd_nova_patrocinadora', TRUE);

            $args = array(
                'ds_nome_patrocinadora' => $this->input->post('ds_nome_patrocinadora', TRUE),
                'dt_limite_aprovacao' => $this->input->post('dt_limite_aprovacao', TRUE),
                'cd_plano'            => $this->input->post('cd_plano', TRUE),
                'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_nova_patrocinadora) == 0)
            {
                $cd_nova_patrocinadora = $this->nova_patrocinadora_model->patrocinadora_salvar($args);
            }
            else
            {
                $this->nova_patrocinadora_model->patrocinadora_atualizar($cd_nova_patrocinadora, $args);
            }

            redirect('planos/nova_patrocinadora/patrocinadora_cadastro/'.$cd_nova_patrocinadora, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function iniciar_atividade($cd_nova_patrocinadora)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/nova_patrocinadora_model');

            $this->nova_patrocinadora_model->cria_atividade_patrocinadora(intval($cd_nova_patrocinadora), $this->session->userdata('codigo'));

            $atividades = $this->nova_patrocinadora_model->get_atividade_inicio($cd_nova_patrocinadora);

            $this->email_iniciar_atividade_atuarial($cd_nova_patrocinadora);

            foreach ($atividades as $key => $atividade) 
            {
                $this->email_iniciar_atividade(intval($cd_nova_patrocinadora), $atividade);

                $this->nova_patrocinadora_model->iniciar_atividade(
                    intval($atividade['cd_nova_patrocinadora_atividade']),
                    $this->session->userdata('codigo')
                );
            }

            redirect('planos/nova_patrocinadora/atividade/'.$cd_nova_patrocinadora, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function email_iniciar_atividade_atuarial($cd_nova_patrocinadora)
    {
        $this->load->model(array(
            'projetos/nova_patrocinadora_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 447;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->nova_patrocinadora_model->patrocinadora_carrega(intval($cd_nova_patrocinadora));

        $cd_usuario = $this->session->userdata('codigo');

        $tags = array('[NOME_PATROCINADORA]', '[LINK]');
        $subs = array(
            $row['ds_nome_patrocinadora'], 
            site_url('planos/nova_patrocinadora/patrocinadora_cadastro/'.intval($cd_nova_patrocinadora))
        );
        
        $texto = str_replace($tags, $subs, $email['email']);

        $args = array(
            'de'      => 'Operacionalização de Nova Patrocinadora',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    private function email_iniciar_atividade($cd_nova_patrocinadora, $atividade)
    {
        $this->load->model(array(
            'projetos/nova_patrocinadora_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 348;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->nova_patrocinadora_model->patrocinadora_carrega(intval($cd_nova_patrocinadora));

        $cd_usuario = $this->session->userdata('codigo');

        $tags = array('[NOME_ATIVIDADE]', '[NOME_PATROCINADORA]', '[DT_PRAZO]', '[LINK]');
        $subs = array(
            $atividade['ds_nova_patrocinadora_atividade'], 
            $row['ds_nome_patrocinadora'], 
            $atividade['dt_prazo'],
            site_url('planos/nova_patrocinadora/minha_atividade/'.intval($cd_nova_patrocinadora).'/'.intval($atividade['cd_nova_patrocinadora_atividade']))
        );
        
        $texto = str_replace($tags, $subs, $email['email']);

        $args = array(
            'de'      => 'Operacionalização de Nova Patrocinadora',
            'assunto' => $email['assunto'],
            'para'    => $atividade['ds_email_responsavel'].';'.$atividade['ds_email_substituto'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function atividade($cd_nova_patrocinadora)
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $data['row'] = $this->nova_patrocinadora_model->patrocinadora_carrega($cd_nova_patrocinadora); 

        $data['collection'] = $this->nova_patrocinadora_model->listar_atividade($cd_nova_patrocinadora);

        foreach($data['collection'] as $key => $item)
        {               
            $data['collection'][$key]['atividades_dependentes'] = array();
            $data['collection'][$key]['acompanhamento']         = array();

            $atividades      = $this->nova_patrocinadora_model->get_atividade_dependente($cd_nova_patrocinadora, $item['cd_nova_patrocinadora_atividade']);
            $acompanhamentos = $this->nova_patrocinadora_model->listar_acompanhamento($item['cd_nova_patrocinadora_atividade']);

            foreach($atividades as $key1 => $atividade) 
            {
                $data['collection'][$key]['atividades_dependentes'][] = $atividade['ds_atividades_dependentes'];
            }

            foreach($acompanhamentos as $key1 => $acompanhamento) 
            {
                $data['collection'][$key]['acompanhamento'][] = $acompanhamento['dt_inclusao'].' : '.$acompanhamento['ds_acompanhamento'];  
            }
        }

        $this->load->view('planos/nova_patrocinadora/atividade',$data);  
    } 

    public function minhas()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $data['patrocinadora'] = $this->nova_patrocinadora_model->get_patrocinadora();

        $this->load->view('planos/nova_patrocinadora/minhas', $data); 
    }

    public function minhas_listar()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $args = array(
            'dt_prazo_ini'          => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'          => $this->input->post('dt_prazo_fim', TRUE),
            'fl_encerramento'       => $this->input->post('fl_encerramento', TRUE),
            'cd_nova_patrocinadora' => $this->input->post('cd_nova_patrocinadora', TRUE)
        );
                
        manter_filtros($args);

        $data['collection'] = $this->nova_patrocinadora_model->listar_minhas(
            $this->session->userdata('codigo'), 
            $args
        );

        $this->load->view('planos/nova_patrocinadora/minhas_result', $data);
    }

    public function minha_atividade($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade, $cd_nova_patrocinadora_atividade_acompanhamento = 0)
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $data = array(
            'atividade'  => $this->nova_patrocinadora_model->carrega_atividade($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade),
            'collection' => $this->nova_patrocinadora_model->listar_acompanhamento($cd_nova_patrocinadora_atividade),
            'status'     => $this->get_status()
        );

        if(intval($cd_nova_patrocinadora_atividade_acompanhamento) == 0)
        {
            $data['row'] = array(
                'cd_nova_patrocinadora_atividade_acompanhamento' => intval($cd_nova_patrocinadora_atividade_acompanhamento),
                'fl_status'                                    => '',
                'ds_acompanhamento'                            => '',
                'cd_atividade'                                 => ''
            );
        }
        else
        {
            $data['row'] = $this->nova_patrocinadora_model->carrega_acompanhamento($cd_nova_patrocinadora_atividade_acompanhamento);
        }

        $this->load->view('planos/nova_patrocinadora/minha_atividade', $data); 
    }

    public function valida_atividade()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $data = $this->nova_patrocinadora_model->valida_atividade($this->input->post('cd_atividade', TRUE));

        echo json_encode($data);
    }

    public function salvar_acompanhamento()
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $cd_nova_patrocinadora_atividade_acompanhamento = $this->input->post('cd_nova_patrocinadora_atividade_acompanhamento', TRUE);
        $cd_nova_patrocinadora_atividade                = $this->input->post('cd_nova_patrocinadora_atividade', TRUE);
        $cd_nova_patrocinadora                          = $this->input->post('cd_nova_patrocinadora', TRUE);
        
        $args = array(
            'cd_nova_patrocinadora_atividade' => intval($cd_nova_patrocinadora_atividade),
            'ds_acompanhamento'             => $this->input->post('ds_acompanhamento', TRUE),
            'cd_atividade'                  => $this->input->post('cd_atividade', TRUE),
            'cd_usuario'                    => $this->session->userdata('codigo')
        );

        if(intval($cd_nova_patrocinadora_atividade_acompanhamento) == 0)
        {
            $this->nova_patrocinadora_model->salvar_acompanhamento($args);
        }
        else
        {
            $this->nova_patrocinadora_model->atualizar_acompanhamento($cd_nova_patrocinadora_atividade_acompanhamento, $args);
        }

        redirect('planos/nova_patrocinadora/minha_atividade/'.$cd_nova_patrocinadora.'/'.$cd_nova_patrocinadora_atividade, 'refresh');
    }

    public function excluir_acompanhamento($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade, $cd_nova_patrocinadora_atividade_acompanhamento)
    {
        $this->load->model('projetos/nova_patrocinadora_model');

        $this->nova_patrocinadora_model->excluir_acompanhamento($cd_nova_patrocinadora_atividade_acompanhamento, $this->session->userdata('codigo'));

        redirect('planos/nova_patrocinadora/minha_atividade/'.$cd_nova_patrocinadora.'/'.$cd_nova_patrocinadora_atividade, 'refresh');
    }

    public function encerrar_acompanhamento($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade)
    {
        $this->load->model(array(
            'projetos/nova_patrocinadora_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 349;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->nova_patrocinadora_model->patrocinadora_carrega(intval($cd_nova_patrocinadora));

        $atividade = $this->nova_patrocinadora_model->carrega_atividade($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade);

        $cd_usuario = $this->session->userdata('codigo');

        $tags = array('[NOME_ATIVIDADE]', '[NOME_PATROCINADORA]', '[LINK]');
        $subs = array(
            $atividade['ds_nova_patrocinadora_atividade'], 
            $row['ds_nome_patrocinadora'], 
            site_url('planos/nova_patrocinadora/atividade/'.intval($cd_nova_patrocinadora))
        );
        
        $texto = str_replace($tags, $subs, $email['email']);

        $args = array(
            'de'      => 'Operacionalização de Nova Patrocinadora',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

        $this->nova_patrocinadora_model->encerrar_atividade($cd_nova_patrocinadora_atividade, $cd_usuario);

        $atividades = $this->nova_patrocinadora_model->get_atividade_dependente_inicio($cd_nova_patrocinadora, $cd_nova_patrocinadora_atividade);

        foreach ($atividades as $key => $atividade) 
        {
            $this->email_iniciar_atividade(intval($cd_nova_patrocinadora), $atividade);

            $this->nova_patrocinadora_model->iniciar_atividade(
                intval($atividade['cd_nova_patrocinadora_atividade']),
                $cd_usuario
            );
        }

        $row = $this->nova_patrocinadora_model->patrocinadora_carrega($cd_nova_patrocinadora);

        if(intval($row['qt_atividade']) == intval($row['qt_atividades_encerradas']))
        {
            $cd_evento = 350;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $tags = array('[NOME_PATROCINADORA]', '[LINK]');
            $subs = array(
                $row['ds_nome_patrocinadora'], 
                site_url('planos/nova_patrocinadora/atividade/'.intval($cd_nova_patrocinadora))
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $this->nova_patrocinadora_model->encerrar_patrocinadora($cd_nova_patrocinadora);

            $args = array(
                'de'      => 'Operacionalização de Nova Patrocinadora',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
        }   

        redirect('planos/nova_patrocinadora/minhas', 'refresh');
    }
}