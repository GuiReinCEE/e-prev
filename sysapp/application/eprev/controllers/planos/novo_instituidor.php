<?php
class Novo_instituidor extends Controller
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

        if($cd_divisao == 'GAP.' OR $row['cd_usuario_responsavel'] == $cd_usuario OR $row['cd_usuario_substituto'] == $cd_usuario OR $cd_usuario =251)
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
        $this->load->model('projetos/novo_instituidor_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        foreach($this->novo_instituidor_model->get_usuarios($cd_gerencia) as $item)
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
        $this->load->model('projetos/novo_instituidor_model');

        $cd_novo_instituidor_estrutura = $this->input->post('cd_novo_instituidor_estrutura', TRUE);

        $args = array(
            'nr_novo_instituidor_estrutura' => $this->input->post('nr_novo_instituidor_estrutura', TRUE),
            'cd_usuario'                      => $this->session->userdata('codigo')
        );
        
        $this->novo_instituidor_model->set_ordem($cd_novo_instituidor_estrutura, $args);
    }

    public function index()
    {

        $this->load->view('planos/novo_instituidor/index');
    }  

    public function listar()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $args = array(
        	'fl_desativado' => $this->input->post('fl_desativado', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->novo_instituidor_model->listar($args);
        $data['cd_usuario'] = $this->session->userdata('codigo');
        $data['cd_divisao'] = $this->session->userdata('divisao');

        foreach($data['collection'] as $key => $item)
        {               
            $data['collection'][$key]['ds_atividade_dependente'] = array();

            $atividades = $this->novo_instituidor_model->get_atividade_estrutura_dependencia($item['cd_novo_instituidor_estrutura']);

            foreach($atividades as $key1 => $atividade) 
            {
                $data['collection'][$key]['ds_atividade_dependente'][] = $atividade['ds_atividade_dependente'] ;
            }
        } 

        $this->load->view('planos/novo_instituidor/index_result', $data);		
    }

    public function cadastro($cd_novo_instituidor_estrutura = 0)
    {
        $this->load->model('projetos/novo_instituidor_model');

        $data['atividade'] = $this->novo_instituidor_model->get_atividades($cd_novo_instituidor_estrutura); 

        $data['atividade_estrutura_dependencia'] = array();

        $atividade = $this->novo_instituidor_model->get_atividade_estrutura_dependencia($cd_novo_instituidor_estrutura);
                    
        foreach($atividade as $item)
        {               
            $data['atividade_estrutura_dependencia'][] = $item['cd_novo_instituidor_estrutura_dep'];
        } 

        if(intval($cd_novo_instituidor_estrutura) == 0)
        {
            if($this->get_permissao())
            {
                $row = $this->novo_instituidor_model->get_proximo_numero();

                $data['row'] = array(
                    'cd_novo_instituidor_estrutura' => intval($cd_novo_instituidor_estrutura),
                    'nr_novo_instituidor_estrutura' => (count($row) > 0 ? $row['nr_novo_instituidor_estrutura'] : 1),
                    'ds_novo_instituidor_estrutura' => '',
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

                $this->load->view('planos/novo_instituidor/cadastro', $data);
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
                     
        }
        else 
        {
            $data['row'] = $this->novo_instituidor_model->carrega($cd_novo_instituidor_estrutura);

            if($this->get_permissao_estrutura($data['row']))
            {
                $usuario = $this->novo_instituidor_model->get_usuarios($data['row']['cd_gerencia']);

                $data['responsavel'] = $usuario;
                $data['substituto']  = $usuario;
                
                $this->load->view('planos/novo_instituidor/cadastro', $data);   
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
        }
    }

    public function salvar()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $cd_novo_instituidor_estrutura = $this->input->post('cd_novo_instituidor_estrutura', TRUE);

        $args = array(
            'nr_novo_instituidor_estrutura' => $this->input->post('nr_novo_instituidor_estrutura', TRUE),
            'ds_novo_instituidor_estrutura' => $this->input->post('ds_novo_instituidor_estrutura', TRUE),
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
        
        if(intval($cd_novo_instituidor_estrutura) == 0)
        {
            $cd_novo_instituidor_estrutura = $this->novo_instituidor_model->salvar($args);
        }
        else
        {
            $this->novo_instituidor_model->atualizar($cd_novo_instituidor_estrutura, $args);
        }

        redirect('planos/novo_instituidor/cadastro/'.$cd_novo_instituidor_estrutura, 'refresh');
    }

    public function desativar($cd_novo_instituidor_estrutura)
    {
        $this->load->model('projetos/novo_instituidor_model');

        $this->novo_instituidor_model->desativar($cd_novo_instituidor_estrutura, $this->session->userdata('codigo'));

        redirect('planos/novo_instituidor/index', 'refresh');
    }

    public function ativar($cd_novo_instituidor_estrutura)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_instituidor_model');

            $this->novo_instituidor_model->ativar($cd_novo_instituidor_estrutura, $this->session->userdata('codigo'));

            redirect('planos/novo_instituidor/cadastro/'.$cd_novo_instituidor_estrutura, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function instituidor()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $data['planos'] = $this->novo_instituidor_model->get_planos();

        $this->load->view('planos/novo_instituidor/instituidor', $data); 
    }

    public function instituidor_listar()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $args = array(
            'cd_plano'      => $this->input->post('cd_plano', TRUE),
            'dt_inicio_ini' => $this->input->post('dt_inicio_ini', TRUE),
            'dt_inicio_fim' => $this->input->post('dt_inicio_fim', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->novo_instituidor_model->instituidor_listar($args);

        $this->load->view('planos/novo_instituidor/instituidor_result', $data);       
    }

    public function instituidor_cadastro($cd_novo_instituidor = 0)
    {
        $this->load->model('projetos/novo_instituidor_model');

        $data['planos'] = $this->novo_instituidor_model->get_planos();

        if(intval($cd_novo_instituidor) == 0)
        {
            $data['row'] = array(
                'cd_novo_instituidor' => intval($cd_novo_instituidor),
                'ds_nome_instituidor' => '',
                'dt_limite_aprovacao' => '',
                'cd_plano'            => '',
                'cd_empresa'          => '',
                'dt_inicio'           => ''
            );
        }
        else
        {
            $data['row'] = $this->novo_instituidor_model->instituidor_carrega($cd_novo_instituidor);                
        } 

        $this->load->view('planos/novo_instituidor/instituidor_cadastro', $data);
    }

    public function instituidor_salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_instituidor_model');

            $cd_novo_instituidor = $this->input->post('cd_novo_instituidor', TRUE);

            $args = array(
                'ds_nome_instituidor' => $this->input->post('ds_nome_instituidor', TRUE),
                'dt_limite_aprovacao' => $this->input->post('dt_limite_aprovacao', TRUE),
                'cd_plano'            => $this->input->post('cd_plano', TRUE),
                'cd_empresa'          => $this->input->post('cd_empresa', TRUE),
                'cd_usuario'          => $this->session->userdata('codigo')
            );

            if(intval($cd_novo_instituidor) == 0)
            {
                $cd_novo_instituidor = $this->novo_instituidor_model->instituidor_salvar($args);
            }
            else
            {
                $this->novo_instituidor_model->instituidor_atualizar($cd_novo_instituidor, $args);
            }

            redirect('planos/novo_instituidor/instituidor_cadastro/'.$cd_novo_instituidor, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function iniciar_atividade($cd_novo_instituidor)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/novo_instituidor_model');

            $this->novo_instituidor_model->cria_atividade_instituidor(intval($cd_novo_instituidor), $this->session->userdata('codigo'));

            $atividades = $this->novo_instituidor_model->get_atividade_inicio($cd_novo_instituidor);

            foreach ($atividades as $key => $atividade) 
            {
                $this->email_iniciar_atividade(intval($cd_novo_instituidor), $atividade);

                $this->novo_instituidor_model->iniciar_atividade(
                    intval($atividade['cd_novo_instituidor_atividade']),
                    $this->session->userdata('codigo')
                );
            }

            redirect('planos/novo_instituidor/atividade/'.$cd_novo_instituidor, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function email_iniciar_atividade($cd_novo_instituidor, $atividade)
    {
        $this->load->model(array(
            'projetos/novo_instituidor_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 277;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->novo_instituidor_model->instituidor_carrega(intval($cd_novo_instituidor));

        $cd_usuario = $this->session->userdata('codigo');

        $tags = array('[NOME_ATIVIDADE]', '[NOME_INSTITUIDOR]', '[DT_PRAZO]', '[LINK]');
        $subs = array(
            $atividade['ds_novo_instituidor_atividade'], 
            $row['ds_nome_instituidor'], 
            $atividade['dt_prazo'],
            site_url('planos/novo_instituidor/minha_atividade/'.intval($cd_novo_instituidor).'/'.intval($atividade['cd_novo_instituidor_atividade']))
        );
        
        $texto = str_replace($tags, $subs, $email['email']);

        $args = array(
            'de'      => 'Operacionalização de Novo Instituidor',
            'assunto' => $email['assunto'],
            'para'    => $atividade['ds_email_responsavel'].';'.$atividade['ds_email_substituto'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function atividade($cd_novo_instituidor)
    {
        $this->load->model('projetos/novo_instituidor_model');

        $data['row'] = $this->novo_instituidor_model->instituidor_carrega($cd_novo_instituidor); 

        $data['collection'] = $this->novo_instituidor_model->listar_atividade($cd_novo_instituidor);

        foreach($data['collection'] as $key => $item)
        {               
            $data['collection'][$key]['atividades_dependentes'] = array();
            $data['collection'][$key]['acompanhamento']         = array();

            $atividades      = $this->novo_instituidor_model->get_atividade_dependente($cd_novo_instituidor, $item['cd_novo_instituidor_atividade']);
            $acompanhamentos = $this->novo_instituidor_model->listar_acompanhamento($item['cd_novo_instituidor_atividade']);

            foreach($atividades as $key1 => $atividade) 
            {
                $data['collection'][$key]['atividades_dependentes'][] = $atividade['ds_atividades_dependentes'];
            }

            foreach($acompanhamentos as $key1 => $acompanhamento) 
            {
                $data['collection'][$key]['acompanhamento'][] = $acompanhamento['dt_inclusao'].' : '.$acompanhamento['ds_acompanhamento'];  
            }
        }

        $this->load->view('planos/novo_instituidor/atividade',$data);  
    } 

    public function minhas()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $data['instituidor'] = $this->novo_instituidor_model->get_instituidor();

        $this->load->view('planos/novo_instituidor/minhas', $data); 
    }

    public function minhas_listar()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $args = array(
            'dt_prazo_ini'        => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'        => $this->input->post('dt_prazo_fim', TRUE),
            'fl_encerramento'     => $this->input->post('fl_encerramento', TRUE),
            'cd_novo_instituidor' => $this->input->post('cd_novo_instituidor', TRUE)
        );
                
        manter_filtros($args);

        $data['collection'] = $this->novo_instituidor_model->listar_minhas(
            $this->session->userdata('codigo'), 
            $args
        );

        $this->load->view('planos/novo_instituidor/minhas_result', $data);
    }

    public function minha_atividade($cd_novo_instituidor, $cd_novo_instituidor_atividade, $cd_novo_instituidor_atividade_acompanhamento = 0)
    {
        $this->load->model('projetos/novo_instituidor_model');

        $data = array(
            'atividade'  => $this->novo_instituidor_model->carrega_atividade($cd_novo_instituidor, $cd_novo_instituidor_atividade),
            'collection' => $this->novo_instituidor_model->listar_acompanhamento($cd_novo_instituidor_atividade),
            'status'     => $this->get_status()
        );

        if(intval($cd_novo_instituidor_atividade_acompanhamento) == 0)
        {
            $data['row'] = array(
                'cd_novo_instituidor_atividade_acompanhamento' => intval($cd_novo_instituidor_atividade_acompanhamento),
                'fl_status'                                    => '',
                'ds_acompanhamento'                            => '',
                'cd_atividade'                                 => ''
            );
        }
        else
        {
            $data['row'] = $this->novo_instituidor_model->carrega_acompanhamento($cd_novo_instituidor_atividade_acompanhamento);
        }

        $this->load->view('planos/novo_instituidor/minha_atividade', $data); 
    }

    public function valida_atividade()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $data = $this->novo_instituidor_model->valida_atividade($this->input->post('cd_atividade', TRUE));

        echo json_encode($data);
    }

    public function salvar_acompanhamento()
    {
        $this->load->model('projetos/novo_instituidor_model');

        $cd_novo_instituidor_atividade_acompanhamento = $this->input->post('cd_novo_instituidor_atividade_acompanhamento', TRUE);
        $cd_novo_instituidor_atividade                = $this->input->post('cd_novo_instituidor_atividade', TRUE);
        $cd_novo_instituidor                          = $this->input->post('cd_novo_instituidor', TRUE);
        
        $args = array(
            'cd_novo_instituidor_atividade' => intval($cd_novo_instituidor_atividade),
            'ds_acompanhamento'             => $this->input->post('ds_acompanhamento', TRUE),
            'cd_atividade'                  => $this->input->post('cd_atividade', TRUE),
            'cd_usuario'                    => $this->session->userdata('codigo')
        );

        if(intval($cd_novo_instituidor_atividade_acompanhamento) == 0)
        {
            $this->novo_instituidor_model->salvar_acompanhamento($args);
        }
        else
        {
            $this->novo_instituidor_model->atualizar_acompanhamento($cd_novo_instituidor_atividade_acompanhamento, $args);
        }

        redirect('planos/novo_instituidor/minha_atividade/'.$cd_novo_instituidor.'/'.$cd_novo_instituidor_atividade, 'refresh');
    }

    public function excluir_acompanhamento($cd_novo_instituidor, $cd_novo_instituidor_atividade, $cd_novo_instituidor_atividade_acompanhamento)
    {
        $this->load->model('projetos/novo_instituidor_model');

        $this->novo_instituidor_model->excluir_acompanhamento($cd_novo_instituidor_atividade_acompanhamento, $this->session->userdata('codigo'));

        redirect('planos/novo_instituidor/minha_atividade/'.$cd_novo_instituidor.'/'.$cd_novo_instituidor_atividade, 'refresh');
    }

    public function encerrar_acompanhamento($cd_novo_instituidor, $cd_novo_instituidor_atividade)
    {
        $this->load->model(array(
            'projetos/novo_instituidor_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 291;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->novo_instituidor_model->instituidor_carrega(intval($cd_novo_instituidor));

        $atividade = $this->novo_instituidor_model->carrega_atividade($cd_novo_instituidor, $cd_novo_instituidor_atividade);

        $cd_usuario = $this->session->userdata('codigo');

        $tags = array('[NOME_ATIVIDADE]', '[NOME_INSTITUIDOR]', '[LINK]');
        $subs = array(
            $atividade['ds_novo_instituidor_atividade'], 
            $row['ds_nome_instituidor'], 
            site_url('planos/novo_instituidor/atividade/'.intval($cd_novo_instituidor))
        );
        
        $texto = str_replace($tags, $subs, $email['email']);

        $args = array(
            'de'      => 'Operacionalização de Novo Instituidor',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

        $this->novo_instituidor_model->encerrar_atividade($cd_novo_instituidor_atividade, $cd_usuario);

        $atividades = $this->novo_instituidor_model->get_atividade_dependente_inicio($cd_novo_instituidor, $cd_novo_instituidor_atividade);

        foreach ($atividades as $key => $atividade) 
        {
            $this->email_iniciar_atividade(intval($cd_novo_instituidor), $atividade);

            $this->novo_instituidor_model->iniciar_atividade(
                intval($atividade['cd_novo_instituidor_atividade']),
                $cd_usuario
            );
        }

        $row = $this->novo_instituidor_model->instituidor_carrega($cd_novo_instituidor);

        if(intval($row['qt_atividade']) == intval($row['qt_atividades_encerradas']))
        {
            $cd_evento = 292;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $tags = array('[NOME_INSTITUIDOR]', '[LINK]');
            $subs = array(
                $row['ds_nome_instituidor'], 
                site_url('planos/novo_instituidor/atividade/'.intval($cd_novo_instituidor))
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $this->novo_instituidor_model->encerrar_instituidor($cd_novo_instituidor);

            $args = array(
                'de'      => 'Operacionalização de Novo Instituidor',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
        }   

        redirect('planos/novo_instituidor/minhas', 'refresh');
    }
}