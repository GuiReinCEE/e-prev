<?php
class Rh_avaliacao extends Controller
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

    public function index($nr_ano = '')
    {
        $data['nr_ano'] = (intval($nr_ano) > 0 ? $nr_ano : date('Y'));

        $this->load->view('cadastro/rh_avaliacao/index', $data);
    }

    public function listar()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $args = array(
            'nr_ano_avaliacao' => $this->input->post('nr_ano_avaliacao', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->avaliacao_model->listar_minhas_avalicoes($this->session->userdata('codigo'), $args);

        foreach ($data['collection'] as $key => $item) 
        {
            $row = $this->avaliacao_model->listar_minhas_avalicoes_status($item['cd_avaliacao_usuario'], $this->session->userdata('codigo'));

            $data['collection'][$key]['ds_status']       = $row['ds_status'];
            $data['collection'][$key]['ds_class_status'] = $row['ds_class_status'];
        }

        $this->load->view('cadastro/rh_avaliacao/index_result', $data);
    }

    public function formulario($cd_avaliacao_usuario)
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $valida = $this->avaliacao_model->valida_permissao_usuario($cd_avaliacao_usuario, $this->session->userdata('codigo'));

        if($this->get_permissao() OR intval($valida['fl_permissao']) > 0)
        {
            $bloco          = array();
            $bloco_pergunta = array();
            $performance    = array();  

            $row = $this->avaliacao_model->get_formulario_usuario($cd_avaliacao_usuario);

            $avaliacao = $this->avaliacao_model->carrega($row['cd_avaliacao']);

            if(trim($avaliacao['fl_inicio']) == 'S' OR $this->get_permissao())
            {
                $row_status = $this->avaliacao_model->formulario_status($cd_avaliacao_usuario, $this->session->userdata('codigo'));

                $matriz = array();

                $cd_matriz              = '';
                $ds_promocao_progressao = '';   
                $view_pdi               = '';     

                if(trim($row['dt_encerramento']) == '')
                {
                    if(count($row_status) > 0)
                    {
                        $row['cd_avaliacao_usuario_avaliacao'] = $row_status['cd_avaliacao_usuario_avaliacao'];
                        $row['ds_avaliacao']                   = $row_status['ds_status'];
                        $row['tp_avaliacao']                   = $row_status['tp_avaliacao'];
                    }
                    else
                    {
                        $row['cd_avaliacao_usuario_avaliacao'] = 0;
                        $row['ds_avaliacao']                   = 'Autoavaliação';
                        $row['tp_avaliacao']                   = 'PRI';
                    }
                    
                }
                else
                {
                    $row['cd_avaliacao_usuario_avaliacao'] = $row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao'];
                    $row['ds_avaliacao']                   = 'Finalizado';
                    $row['tp_avaliacao']                   = 'QUA';

                    $matriz = $this->monta_matriz($row['cd_avaliacao']);

                    $resultado = $this->avaliacao_model->resultado($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao']);

                    if(count($resultado) > 0)
                    {
                        $cd_matriz = $resultado[0]['cd_matriz'].$resultado[1]['cd_matriz'];
                    }

                    $resultado_tipo = $this->avaliacao_model->resultado_tipo($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao']);

                    if(count($resultado_tipo) > 0)
                    {
                        if(trim($resultado_tipo['fl_progressao']) == 'S' AND trim($resultado_tipo['fl_promocao']) == 'S')
                        {
                            $ds_promocao_progressao = 'Progressão e Promoção';
                        }
                        else if(trim($resultado_tipo['fl_progressao']) == 'S' AND trim($resultado_tipo['fl_promocao']) == 'N')
                        {
                            $ds_promocao_progressao = 'Progressão';
                        }
                    }

                    $data_result = array(
                        'fl_adicionar' => false,
                        'collection'   => $this->avaliacao_model->listar_plano_desenvolvimento($cd_avaliacao_usuario)
                    );

                    $data_result['row']['dt_encerramento'] = $row['dt_encerramento'];

                    $view_pdi = $this->load->view('cadastro/rh_avaliacao/plano_desenvolvimeto_individual_result', $data_result, TRUE);
                }

                $this->monta_conhecimento($row);
                $collection = $this->monta_formulario($row, $bloco, $bloco_pergunta, $performance);

                $data['row']        = $row;
                $data['collection'] = $collection;

                $fl_permissao_reuniao_avaliacao = FALSE;

                if(trim($row['tp_avaliacao']) == 'QUA' AND trim($row['dt_encerramento_autoavaliacao']) == '')
                {
                    $fl_permissao_reuniao_avaliacao = TRUE;
                }

                $data = array(
                    'row'                            => $row,
                    'collection'                     => $collection,
                    'performance'                    => $performance,
                    'bloco'                          => $bloco,
                    'bloco_pergunta'                 => $bloco_pergunta,
                    'matriz'                         => $matriz,
                    'view_pdi'                       => $view_pdi,
                    'cd_matriz'                      => $cd_matriz,
                    'ds_promocao_progressao'         => $ds_promocao_progressao,
                    'fl_permissao'                   => (intval($valida['fl_permissao']) > 0 ? TRUE : FALSE),
                    'fl_permissao_reuniao_avaliacao' => $fl_permissao_reuniao_avaliacao,
                    'ocorrencia_ponto'               => $this->avaliacao_model->listar_ocorrencia_ponto($row['cd_usuario'], ($row['nr_ano_avaliacao'] - 1))
                );

                $this->load->view('cadastro/rh_avaliacao/formulario', $data);
            }
            else 
            {
                exibir_mensagem('Avaliação inicia em '.$row['dt_inicio']);
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function monta_matriz($cd_avaliacao)
    {
        $matriz = array();

        $collection = $this->avaliacao_model->matriz($cd_avaliacao);

        $conceito_a = '';

        $i = 0;

        foreach ($collection as $key => $item) 
        {
            if(trim($conceito_a) != trim($item['ds_matriz_conceito_a']))
            {
                $i ++;
                $conceito_a = trim($item['ds_matriz_conceito_a']);
                $matriz[$i] = array();
            }

            $matriz[$i][] = array(
                'cd_matriz' => trim($item['ds_matriz_conceito_a']).trim($item['ds_matriz_conceito_b']),
                'ds_matriz' => trim($item['ds_matriz_acao']),
                'cor_fundo' => $item['cor_fundo'],
                'cor_texto' => $item['cor_texto']
            );
        }

        return $matriz;
    }

    private function monta_formulario($row, &$bloco_formulario, &$bloco_pergunta, &$performance)
    {
        $collection = $this->avaliacao_model->get_formulario_grupo($row['cd_avaliacao']);

        $tp_avaliacao_anterior = '';

        switch ($row['tp_avaliacao']) 
        {
            case 'SEG':
                $tp_avaliacao_anterior = 'PRI';
                break;
            case 'TER':
                $tp_avaliacao_anterior = 'SEG';
                break;
            case 'QUA':
                $tp_avaliacao_anterior = 'TER';
                break;
        }

        foreach ($collection as $key => $item)
        {
            $collection[$key]['peformance'] = $this->avaliacao_model->get_formulario_peformance($row['cd_avaliacao'], $item['tp_grupo']);
            $collection[$key]['bloco']      = $this->avaliacao_model->get_formulario_bloco_usuario($row['cd_avaliacao_usuario'], $item['tp_grupo']);

            $performance[$item['tp_grupo']] = $collection[$key]['peformance'];

            foreach ($collection[$key]['bloco'] as $key2 => $bloco)
            {
                $collection[$key]['bloco'][$key2]['ds_justificativa_avaliado'] = '';
                $collection[$key]['bloco'][$key2]['ds_justificativa']          = '';

                $collection[$key]['bloco'][$key2]['pergunta'] = $this->avaliacao_model->get_formulario_pergunta_usuario($row['cd_avaliacao_usuario'], $bloco['cd_avaliacao_bloco']);

                $bloco_formulario[$item['tp_grupo']][$bloco['cd_avaliacao_bloco']] = count($collection[$key]['bloco'][$key2]['pergunta']);

                $justificativa = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_justificativa($bloco['cd_avaliacao_bloco'], $row['cd_avaliacao_usuario_avaliacao']);

                if(count($justificativa) > 0)
                {
                    $collection[$key]['bloco'][$key2]['ds_justificativa'] = $justificativa['ds_justificativa'];
                }
                else if(in_array($row['tp_avaliacao'], array('TER', 'QUA')) AND $this->session->userdata('codigo') == intval($row['cd_usuario_avaliador']))
                {
                    $justificativa = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_justificativa_anterior(
                        $bloco['cd_avaliacao_bloco'], 
                        $row['cd_avaliacao_usuario'], 
                        $tp_avaliacao_anterior
                    );

                    if(count($justificativa) > 0)
                    {
                        $collection[$key]['bloco'][$key2]['ds_justificativa'] = $justificativa['ds_justificativa'];
                    }
                }

                if(trim($row['tp_avaliacao']) == 'QUA' AND trim($row['dt_encerramento_reuniao_avaliacao']) == '')
                {
                    $justificativa = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_justificativa_anterior(
                        $bloco['cd_avaliacao_bloco'], 
                        $row['cd_avaliacao_usuario'], 
                        'PRI'
                    );

                    $collection[$key]['bloco'][$key2]['ds_justificativa_avaliado'] = $justificativa['ds_justificativa'];
                }

                foreach ($collection[$key]['bloco'][$key2]['pergunta'] as $key3 => $pergunta)
                {
                    $bloco_pergunta[] = $pergunta['cd_avaliacao_bloco_pergunta'];

                    $collection[$key]['bloco'][$key2]['pergunta'][$key3]['ds_resposta_avaliado'] = '';
                    $collection[$key]['bloco'][$key2]['pergunta'][$key3]['tp_resposta']          = '';

                    $resposta = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_resposta($pergunta['cd_avaliacao_bloco_pergunta'], $row['cd_avaliacao_usuario_avaliacao']);

                    if(count($resposta) > 0)
                    {
                        $collection[$key]['bloco'][$key2]['pergunta'][$key3]['tp_resposta']  = $resposta['tp_resposta'];
                    }
                    else if(in_array($row['tp_avaliacao'], array('TER', 'QUA')) AND $this->session->userdata('codigo') == intval($row['cd_usuario_avaliador']))
                    {
                        $resposta = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_resposta_anterior(
                            $pergunta['cd_avaliacao_bloco_pergunta'], 
                            $row['cd_avaliacao_usuario'], 
                            $tp_avaliacao_anterior
                        );

                        if(count($resposta) > 0)
                        {
                            $collection[$key]['bloco'][$key2]['pergunta'][$key3]['tp_resposta']  = $resposta['tp_resposta'];
                        }
                    }

                    if(trim($row['tp_avaliacao']) == 'QUA' AND trim($row['dt_encerramento_reuniao_avaliacao']) == '')
                    {
                        $resposta = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_resposta_anterior(
                            $pergunta['cd_avaliacao_bloco_pergunta'], 
                            $row['cd_avaliacao_usuario'], 
                            'PRI'
                        );

                        $collection[$key]['bloco'][$key2]['pergunta'][$key3]['ds_resposta_avaliado'] = $resposta['ds_performance'];
                    }
                }    
            }
        }

        return $collection;
    }

    private function monta_conhecimento(&$row)
    {
        $conhecimento = '';

        if(trim($row['ds_conhecimento_generico']) != '')
        {
            $ds_conhecimento_generico = $row['ds_conhecimento_generico'];

            $ds_conhecimento_generico = str_replace("\r", '', $ds_conhecimento_generico);
            $ds_conhecimento_generico = str_replace("\n", br(), $ds_conhecimento_generico);

            $conhecimento .= '<h3>Conhecimentos Genéricos</h3>'.br().$ds_conhecimento_generico;
        }

        if(trim($row['ds_conhecimento_especifico']) != '')
        {
            if(trim($conhecimento) != '')
            {
                $conhecimento .= br(2);
            }

            $ds_conhecimento_especifico = $row['ds_conhecimento_especifico'];

            $ds_conhecimento_especifico = str_replace("\r", '', $ds_conhecimento_especifico);
            $ds_conhecimento_especifico = str_replace("\n", br(), $ds_conhecimento_especifico);

            $conhecimento .= '<h3>Conhecimentos Específicos</h3>'.br().$ds_conhecimento_especifico;
        }

        $row['ds_conhecimento'] = '<div style="font-size:60%;">'.$conhecimento.'</div>';
    }

    public function salvar()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $args = array(
            'cd_avaliacao'                   => $this->input->post('cd_avaliacao', TRUE),
            'cd_avaliacao_usuario'           => $this->input->post('cd_avaliacao_usuario', TRUE),
            'cd_avaliacao_usuario_avaliacao' => $this->input->post('cd_avaliacao_usuario_avaliacao', TRUE),
            'cd_usuario_inclusao'            => $this->session->userdata('codigo')
        );

        $fl_encerramento = $this->input->post('fl_encerramento', TRUE);

        $pergunta = $this->input->post('pergunta', TRUE);

        if(count($pergunta) > 0)
        {
            foreach ($pergunta as $key => $item) 
            {
                $row = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_resposta($key, $args['cd_avaliacao_usuario_avaliacao']);

                $args['cd_avaliacao_bloco_pergunta'] = $key;
                $args['tp_resposta']                 = $item;

                if(count($row) == 0)
                {
                    $this->avaliacao_model->salvar_avaliacao_usuario_avaliacao_resposta($args);
                }
                else
                {
                    $this->avaliacao_model->atualizar_avaliacao_usuario_avaliacao_resposta($row['cd_avaliacao_usuario_avaliacao_resposta'], $args);
                }
            }
        }

        $justificativa = $this->input->post('justificativa', TRUE);

        if(count($justificativa) > 0)
        {
            foreach ($justificativa as $key => $item) 
            {
                $row = $this->avaliacao_model->get_avaliacao_usuario_avaliacao_justificativa($key, $args['cd_avaliacao_usuario_avaliacao']);

                $args['cd_avaliacao_bloco'] = $key;
                $args['ds_justificativa']   = $item;

                if(count($row) == 0)
                {
                    $this->avaliacao_model->salvar_avaliacao_usuario_avaliacao_justificativa($args);
                }
                else
                {
                    $this->avaliacao_model->atualizar_avaliacao_usuario_avaliacao_justificativa($row['cd_avaliacao_usuario_avaliacao_justificativa'], $args);
                }
            }
        }

        if(trim($fl_encerramento) == 'N')
        {
            redirect('cadastro/rh_avaliacao/formulario/'.intval($args['cd_avaliacao_usuario']), 'refresh');
        }
        else
        {
            $this->avaliacao_model->encerrar_avaliacao($args['cd_avaliacao_usuario_avaliacao'], $this->session->userdata('codigo'));

            $tp_avaliacao = $this->input->post('tp_avaliacao', TRUE);

            if((in_array($tp_avaliacao, array('SEG', 'TER'))))
            {
                switch ($tp_avaliacao) 
                {
                    case 'SEG':
                        $args['tp_avaliacao'] = 'TER';
                        break;
                    case 'TER':
                        $args['tp_avaliacao'] = 'QUA';
                        break;
                }

                $args['cd_usuario'] = $this->input->post('cd_usuario_avaliador', TRUE);

                $this->avaliacao_model->salvar_avaliacao_usuario_avaliacao($args);
            }

            if($tp_avaliacao == 'QUA')
            {
                $bloco = $this->input->post('bloco', TRUE);

                if(trim($bloco) != '')
                {
                    $bloco = explode(',', $bloco);

                    foreach ($bloco as $key => $item) 
                    {
                        $row = $this->avaliacao_model->get_bloco($item);

                        $args = array(
                            'cd_avaliacao'                                => $this->input->post('cd_avaliacao', TRUE),
                            'cd_avaliacao_usuario'                        => $this->input->post('cd_avaliacao_usuario', TRUE),
                            'ds_avaliacao_usuario_plando_desenvolvimento' => $row['ds_bloco'],
                            'ds_plano_melhoria'                           => '',
                            'ds_resultado'                                => '',
                            'ds_responsavel'                              => '',
                            'ds_quando'                                   => '',
                            'fl_formulario'                               => 'S',
                            'cd_usuario'                                  => $this->session->userdata('codigo')
                        );

                        $this->avaliacao_model->salvar_plano_desenvolvimeto_individual($args);
                    }
                }

                redirect('cadastro/rh_avaliacao/plano_desenvolvimento_individual/'.$this->input->post('cd_avaliacao_usuario', TRUE), 'refresh');
            }
            else
            {
                redirect('cadastro/rh_avaliacao', 'refresh');
            }
        }
    }

    public function plano_desenvolvimento_individual($cd_avaliacao_usuario)
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $valida = $this->avaliacao_model->valida_permissao_usuario($cd_avaliacao_usuario, $this->session->userdata('codigo'));

        if($this->get_permissao() OR intval($valida['fl_permissao']) > 0)
        {
            $row = $this->avaliacao_model->get_formulario_usuario($cd_avaliacao_usuario);

            if(trim($row['dt_encerramento']) == '' AND trim($row['dt_encerramento_reuniao_avaliacao']) != '')
            {
                $data = array(
                    'row'        => $row,
                    'collection' => $this->avaliacao_model->get_treinamento_avaliado($row['nr_ano_avaliacao'], $row['cd_usuario'])
                );

                $this->load->view('cadastro/rh_avaliacao/plano_desenvolvimento_individual', $data);
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_plano_desenvolvimeto_individual()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $cd_avaliacao_usuario = $this->input->post('cd_avaliacao_usuario', TRUE);

        $data = array(
            'fl_adicionar' => true,
            'row'          => $this->avaliacao_model->get_formulario_usuario($cd_avaliacao_usuario),
            'collection'   => $this->avaliacao_model->listar_plano_desenvolvimento($cd_avaliacao_usuario)
        );

        echo $this->load->view('cadastro/rh_avaliacao/plano_desenvolvimeto_individual_result', $data);
    }

    public function cadastro_plano_desenvolvimeto_individual($cd_avaliacao, $cd_avaliacao_usuario, $cd_avaliacao_usuario_plando_desenvolvimento = 0)
    {
        if(intval($cd_avaliacao_usuario_plando_desenvolvimento) == 0)
        {
            $data['row'] = array(
                'cd_avaliacao_usuario_plando_desenvolvimento' => $cd_avaliacao_usuario_plando_desenvolvimento,
                'cd_avaliacao_usuario'                        => $cd_avaliacao_usuario,
                'cd_avaliacao'                                => $cd_avaliacao,
                'ds_avaliacao_usuario_plando_desenvolvimento' => '',
                'ds_plano_melhoria'                           => '',
                'ds_resultado'                                => '',
                'ds_responsavel'                              => '',
                'ds_quando'                                   => '',
                'fl_formulario'                               => ''
            );
        }
        else
        {
            $this->load->model('rh_avaliacao/avaliacao_model');

            $data['row'] = $this->avaliacao_model->carrega_plano_desenvolvimeto_individual($cd_avaliacao_usuario_plando_desenvolvimento);
        }

        echo $this->load->view('cadastro/rh_avaliacao/plano_desenvolvimeto_individual_cadastro', $data);
    }

    public function salvar_plano_desenvolvimeto_individual()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $cd_avaliacao_usuario_plando_desenvolvimento = $this->input->post('cd_avaliacao_usuario_plando_desenvolvimento', TRUE);        
        $cd_avaliacao_usuario                        = $this->input->post('cd_avaliacao_usuario', TRUE);

        $args = array(
            'cd_avaliacao'                                => $this->input->post('cd_avaliacao', TRUE),
            'cd_avaliacao_usuario'                        => $cd_avaliacao_usuario,
            'ds_avaliacao_usuario_plando_desenvolvimento' => $this->input->post('ds_avaliacao_usuario_plando_desenvolvimento', TRUE),
            'ds_plano_melhoria'                           => $this->input->post('ds_plano_melhoria', TRUE),
            'ds_resultado'                                => $this->input->post('ds_resultado', TRUE),
            'ds_responsavel'                              => $this->input->post('ds_responsavel', TRUE),
            'ds_quando'                                   => $this->input->post('ds_quando', TRUE),
            'fl_formulario'                               => 'N',
            'cd_usuario'                                  => $this->session->userdata('codigo')
        );

        if(intval($cd_avaliacao_usuario_plando_desenvolvimento) == 0)
        {
            $this->avaliacao_model->salvar_plano_desenvolvimeto_individual($args);
        }
        else
        {
            $this->avaliacao_model->atualizar_plano_desenvolvimeto_individual($cd_avaliacao_usuario_plando_desenvolvimento, $args);
        }

        redirect('cadastro/rh_avaliacao/plano_desenvolvimento_individual/'.$cd_avaliacao_usuario, 'refresh');
    }

    public function excluir_plano_desenvolvimeto_individual($cd_avaliacao_usuario, $cd_avaliacao_usuario_plando_desenvolvimento)
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $this->avaliacao_model->excluir_plano_desenvolvimeto_individual($cd_avaliacao_usuario_plando_desenvolvimento, $this->session->userdata('codigo'));

        redirect('cadastro/rh_avaliacao/plano_desenvolvimento_individual/'.$cd_avaliacao_usuario, 'refresh');
    }

    public function salvar_pontos_fortes()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $this->avaliacao_model->salvar_pontos_fortes(
            $this->input->post('cd_avaliacao_usuario', TRUE), 
            utf8_decode($this->input->post('ds_pontos_fortes', TRUE)), 
            $this->session->userdata('codigo')
        );
    }

    public function salvar_pontos_melhorias()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $this->avaliacao_model->salvar_pontos_melhorias(
            $this->input->post('cd_avaliacao_usuario', TRUE), 
            utf8_decode($this->input->post('ds_pontos_melhorias', TRUE)), 
            $this->session->userdata('codigo')
        );
    }

    public function salvar_observacao()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $this->avaliacao_model->salvar_observacao(
            $this->input->post('cd_avaliacao_usuario', TRUE), 
            utf8_decode($this->input->post('ds_observacao', TRUE)), 
            $this->session->userdata('codigo')
        );
    }

    public function encerrar_avaliacao_usuario()
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $cd_avaliacao_usuario = $this->input->post('cd_avaliacao_usuario', TRUE);

        $args = array(
            'ds_pontos_fortes'    => $this->input->post('ds_pontos_fortes', TRUE),
            'ds_pontos_melhorias' => $this->input->post('ds_pontos_melhorias', TRUE),
            'ds_observacao'       => $this->input->post('ds_observacao', TRUE),
            'cd_usuario'          => $this->session->userdata('codigo')
        );

        $this->avaliacao_model->encerrar_avaliacao_usuario($cd_avaliacao_usuario, $args);

        $this->envia_email_rh($cd_avaliacao_usuario);

        redirect('cadastro/rh_avaliacao/formulario/'.intval($cd_avaliacao_usuario), 'refresh');
    }

    private function envia_email_rh($cd_avaliacao_usuario)
    {
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 375;

        $email = $this->eventos_email_model->carrega($cd_evento);
        $row   = $this->avaliacao_model->get_formulario_usuario($cd_avaliacao_usuario);

        $tags = array('[NR_ANO]', '[DS_NOME]', '[LINK]');

        $subs = array(
            $row['nr_ano_avaliacao'],
            $row['ds_avaliado'],
            site_url('cadastro/rh_avaliacao/formulario/'.intval($cd_avaliacao_usuario))
        );

        $texto   = str_replace($tags, $subs, $email['email']);
        $assunto = str_replace(array('[NR_ANO]', '[DS_NOME]'), array($row['nr_ano_avaliacao'], $row['ds_avaliado']), $email['assunto']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Sistema de Avaliação - Encerramento',
            'assunto' => $assunto,
            'para'    => $email['para'],  
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function formulario_pdf($cd_avaliacao_usuario)
    {
        $this->load->model('rh_avaliacao/avaliacao_model');

        $valida = $this->avaliacao_model->valida_permissao_usuario($cd_avaliacao_usuario, $this->session->userdata('codigo'));

        if($this->get_permissao() OR intval($valida['fl_permissao']) > 0)
        {
            $row = $this->avaliacao_model->get_formulario_usuario($cd_avaliacao_usuario);

            if(trim($row['dt_encerramento']) != '')
            {
                $resultado = $this->avaliacao_model->resultado($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao']);

                $cd_matriz = $resultado[0]['cd_matriz'].$resultado[1]['cd_matriz'];
                
                $resultado_tipo = $this->avaliacao_model->resultado_tipo($row['cd_avaliacao_usuario_avaliacao_reuniao_avaliacao']);

                $ds_promocao_progressao = '';

                if(trim($resultado_tipo['fl_progressao']) == 'S' AND trim($resultado_tipo['fl_promocao']) == 'S')
                {
                    $ds_promocao_progressao = 'Progressão e Promoção';
                }
                else if(trim($resultado_tipo['fl_progressao']) == 'S' AND trim($resultado_tipo['fl_promocao']) == 'N')
                {
                    $ds_promocao_progressao = 'Progressão';
                }

                $matriz = $this->monta_matriz($row['cd_avaliacao']);

                $collection = $this->avaliacao_model->listar_plano_desenvolvimento($cd_avaliacao_usuario);

                $this->load->plugin('fpdf');

                $fpdf = new PDF('P', 'mm', 'A4');
                $fpdf->AddFont('segoeuil');
                $fpdf->AddFont('segoeuib');       
                $fpdf->SetNrPag(true);
                $fpdf->SetMargins(10, 14, 5);
                $fpdf->header_exibe = true;
                $fpdf->header_logo = true;
                $fpdf->header_titulo = true;
                $fpdf->header_titulo_texto = 'Processo de Avaliação';
                $fpdf->AddPage();

                $altura_linha = 5;

                $fpdf->SetY($fpdf->GetY() + 1);
                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->MultiCell(190, $altura_linha, 'Período:');
                $fpdf->SetFont('segoeuil', '', 12);
                $fpdf->MultiCell(190, $altura_linha, $row['nr_ano_avaliacao']);

                $fpdf->SetY($fpdf->GetY() + 1);
                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->MultiCell(190, $altura_linha, 'Avaliado:');
                $fpdf->SetFont('segoeuil', '', 12);
                $fpdf->MultiCell(190, $altura_linha, $row['ds_avaliado']);  

                $fpdf->SetY($fpdf->GetY() + 1);
                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->MultiCell(190, $altura_linha, 'Avaliador:');
                $fpdf->SetFont('segoeuil', '', 12);
                $fpdf->MultiCell(190, $altura_linha, $row['ds_avaliador']);  

                if(trim($ds_promocao_progressao) != '')
                {
                    $fpdf->SetY($fpdf->GetY() + 1);
                    $fpdf->SetFont('segoeuib', '', 12);
                    $fpdf->MultiCell(190, $altura_linha, 'Tipo:');
                    $fpdf->SetFont('segoeuil', '', 12);
                    $fpdf->MultiCell(190, $altura_linha, $ds_promocao_progressao);  

                    $fpdf->SetY($fpdf->GetY() + 1);
                }

                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->MultiCell(190, $altura_linha, 'Resultado:');
                $fpdf->SetFont('segoeuib', '', 14);

                $fpdf->SetTextColor(31, 104, 176);
                $fpdf->MultiCell(190, $altura_linha, $cd_matriz);  
                $fpdf->SetTextColor(0, 0, 0);
                $fpdf->SetY($fpdf->GetY() + 4);

                $fpdf->SetFont('segoeuib', '', 14);
                
                $fpdf->MultiCell(190, $altura_linha, 'MATRIZ DE COMPETÊNCIAS E DESEMPENHO', 0, 'C');  

                $fpdf->SetY($fpdf->GetY() + 4);
                
                $fpdf->SetWidths(array(35, 35, 35, 35));
                $fpdf->SetAligns(array('C', 'C', 'C', 'C'));
                $fpdf->SetFont('segoeuib', '', 10);

                foreach ($matriz as $key => $item) 
                {
                    $quadro           = array();
                    $quadro_cor_fundo = array();
                    $quadro_cor_texto = array();

                    $i = 0;
                    $marcar = -1;

                    foreach ($item as $key2 => $item2) 
                    {
                        list($r, $g, $b) = sscanf($item2['cor_fundo'], "#%02x%02x%02x");

                        $quadro_cor_fundo[$i] = array($r, $g, $b);

                        list($r, $g, $b) = sscanf($item2['cor_texto'], "#%02x%02x%02x");

                        $quadro_cor_texto[$i] = array($r, $g, $b);
  
                        $quadro[$i] = $item2['cd_matriz'].chr(10).$item2['ds_matriz'];

                        if(trim($item2['cd_matriz']) == trim($cd_matriz))
                        {
                            $marcar = $i;
                        }

                        $i ++;
                    }
                    $fpdf->SetX($fpdf->GetX() + 25);
                    $fpdf->RowCollor($quadro, $quadro_cor_fundo, $quadro_cor_texto, 35,$marcar);
                }
                
                $fpdf->SetY($fpdf->GetY() + 8);

                $fpdf->SetTextColor(0, 0, 0);

                $fpdf->SetWidths(array(190));
                $fpdf->SetAligns(array('C'));

                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->Row(array('Pontos Fortes'));
                $fpdf->SetFont('segoeuil', '', 12);
                $fpdf->SetAligns(array('L'));
                $fpdf->Row(array($row['ds_pontos_fortes']));

                $fpdf->SetY($fpdf->GetY() + 8);

                $fpdf->SetWidths(array(190));
                $fpdf->SetAligns(array('C'));
                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->Row(array('Pontos de Melhorias'));
                $fpdf->SetFont('segoeuil', '', 12);
                $fpdf->SetAligns(array('L'));
                $fpdf->Row(array($row['ds_pontos_melhorias']));

                $fpdf->SetY($fpdf->GetY() + 8);

                $fpdf->SetWidths(array(190));
                $fpdf->SetAligns(array('C'));
                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->Row(array('Observações'));
                $fpdf->SetFont('segoeuil', '', 12);
                $fpdf->SetAligns(array('L'));
                $fpdf->Row(array($row['ds_observacao']));

                $fpdf->SetY($fpdf->GetY() + 8);

                $fpdf->SetWidths(array(42, 42, 40, 36, 30));
                $fpdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
                $fpdf->SetFont('segoeuib', '', 10);
                $fpdf->Row(array('Competência/Fator de Desempenho', 'Plano para Melhoria do Desempenho', 'Resultado Esperado', 'Responsável (Quem)', 'Quando (Prazo)'));

                $fpdf->SetAligns(array('J', 'J', 'J', 'J', 'J'));
                $fpdf->SetFont('segoeuil', '', 10);

                foreach($collection as $item)
                {
                    $fpdf->Row(array(
                        $item['ds_avaliacao_usuario_plando_desenvolvimento'],
                        $item['ds_plano_melhoria'],
                        $item['ds_resultado'],
                        $item['ds_responsavel'],
                        $item['ds_quando']
                    ));
                }

                $fpdf->SetY($fpdf->GetY() + 8);

                $fpdf->SetFont('segoeuib', '', 12);
                $fpdf->MultiCell(190, $altura_linha, 'Data: '.date('d/m/Y'));

                $fpdf->SetY($fpdf->GetY() + 4);

                $fpdf->SetFont('segoeuil', '', 12);
                $fpdf->MultiCell(190, $altura_linha, '(     ) Concordo com o resultado da avaliação (houve consenso).');
                $fpdf->MultiCell(190, $altura_linha, '(     ) Estou ciente do resultado da avaliação (não houve consenso).');

                $fpdf->SetY($fpdf->GetY() + 30);

                $fpdf->SetFont('segoeuib', '', 14);
                $fpdf->Text($fpdf->GetX(),$fpdf->GetY()-5, '______________________________________');
                $fpdf->Text($fpdf->GetX()+30,$fpdf->GetY(), 'Avaliado');

                $fpdf->Text($fpdf->GetX()+110,$fpdf->GetY()-5, '______________________________________');
                $fpdf->Text($fpdf->GetX()+140,$fpdf->GetY(), 'Avaliador');

                $fpdf->Output();
                exit;
            }
            else
            {
                exibir_mensagem('ACESSO NÃO PERMITIDO');
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}