<?php
class Abaixo_assinado extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_validacao()
    {
        if(gerencia_in(array('GC', 'GCM')) OR $this->session->userdata('indic_12') == '*')
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_validacao_total()
    {
        if(gerencia_in(array('GC')))
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
            $data['fl_permissao'] = $this->get_validacao_total();

            $data['drop'] = array( 
                array('value' => 'S', 'text' => 'Sim'),
                array('value' => 'N', 'text' => 'Não')
            );


            $this->load->view('ecrm/abaixo_assinado/index', $data);
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
            $this->load->model('projetos/abaixo_assinado_model');

            $args = array(
                'nr_ano'                => $this->input->post('nr_ano', TRUE),
                'nr_numero'             => $this->input->post('nr_numero', TRUE),
                'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
                'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
                'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
                'dt_protocolo_ini'      => $this->input->post('dt_protocolo_ini', TRUE),
                'dt_protocolo_fim'      => $this->input->post('dt_protocolo_fim', TRUE),
                'dt_retorno_ini'        => $this->input->post('dt_retorno_ini', TRUE),
                'dt_retorno_fim'        => $this->input->post('dt_retorno_fim', TRUE),
                'dt_limite_retorno_ini' => $this->input->post('dt_limite_retorno_ini', TRUE),
                'dt_limite_retorno_fim' => $this->input->post('dt_limite_retorno_fim', TRUE),
                'fl_retorno'            => $this->input->post('fl_retorno', TRUE)
            );

            manter_filtros($args);

            $data['collection'] = $this->abaixo_assinado_model->listar($args);

            $this->load->view('ecrm/abaixo_assinado/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_abaixo_assinado = 0)
    {
        if($this->get_validacao())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            if(intval($cd_abaixo_assinado) == 0)
            {
                $data['row'] = array(
                    'cd_abaixo_assinado'    => '',
                    'nr_numero_ano'         => '',
                    'dt_protocolo'          => '',
                    'cd_empresa'            => '',
                    'cd_registro_empregado' => '',
                    'seq_dependencia'       => '',
                    'ds_nome'               => '',
                    'ds_descricao'          => '',
                    'ds_email'              => '',
                    'ds_telefone_1'         => '',
                    'ds_telefone_2'         => '',
                    'ds_acao'               => '',
                    'dt_limite_retorno'     => '',
                    'dt_retorno'            => ''
                );
            }
            else
            {
                $data['row'] = $this->abaixo_assinado_model->carrega($cd_abaixo_assinado);
            }

            $data['fl_permissao'] = $this->get_validacao_total();

            $this->load->view('ecrm/abaixo_assinado/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if($this->get_validacao_total())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $cd_abaixo_assinado = $this->input->post('cd_abaixo_assinado', TRUE);

            $numero = $this->abaixo_assinado_model->get_numero();

            $args = array(
                'nr_numero'             => $numero['nr_numero'],
                'nr_ano'                => date('Y'),
                'dt_protocolo'          => $this->input->post('dt_protocolo', TRUE),
                'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
                'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
                'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
                'ds_nome'               => $this->input->post('ds_nome', TRUE),
                'ds_descricao'          => $this->input->post('ds_descricao', TRUE),
                'ds_email'              => $this->input->post('ds_email', TRUE),
                'ds_telefone_1'         => $this->input->post('ds_telefone_1', TRUE),
                'ds_telefone_2'         => $this->input->post('ds_telefone_2', TRUE),
                'ds_acao'               => $this->input->post('ds_acao', TRUE),
                'cd_usuario'            => $this->session->userdata('codigo')
            );

            if(intval($cd_abaixo_assinado) == 0)
            {
                $cd_abaixo_assinado = $this->abaixo_assinado_model->salvar($args);
            }
            else
            {
                $this->abaixo_assinado_model->atualizar($cd_abaixo_assinado, $args);
            }

            $this->email_abaixo_assinado(intval($cd_abaixo_assinado));

            redirect('ecrm/abaixo_assinado/cadastro/'.intval($cd_abaixo_assinado), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function email_abaixo_assinado($cd_abaixo_assinado)
    {
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 362;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = array( '[LINK]' );
        $subs = array( site_url('ecrm/abaixo_assinado/cadastro/'.intval($cd_abaixo_assinado)) );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Abaixo Assinado - Registro',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function acompanhamento($cd_abaixo_assinado, $cd_abaixo_assinado_acompanhamento = 0)
    {
        if($this->get_validacao())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $data = array(
                'cd_abaixo_assinado' => $cd_abaixo_assinado,
                'collection'         => $this->abaixo_assinado_model->listar_acompanhamento($cd_abaixo_assinado),
                'row'                => $this->abaixo_assinado_model->carrega($cd_abaixo_assinado)
            );

            if(intval($cd_abaixo_assinado_acompanhamento) == 0)
            {
                $data['acomp'] = array(
                    'cd_abaixo_assinado_acompanhamento' => '',
                    'ds_acompanhamento'                 => ''
                );
            }
            else
            {
                $data['acomp'] = $this->abaixo_assinado_model->carrega_acompanhamento($cd_abaixo_assinado_acompanhamento);
            }

            $data['fl_permissao'] = $this->get_validacao_total();

            $this->load->view('ecrm/abaixo_assinado/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_acompanhamento()
    {
        if($this->get_validacao_total())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $cd_abaixo_assinado_acompanhamento = $this->input->post('cd_abaixo_assinado_acompanhamento', TRUE);

            $args = array(
                'cd_abaixo_assinado' => $this->input->post('cd_abaixo_assinado', TRUE),
                'ds_acompanhamento'  => $this->input->post('ds_acompanhamento', TRUE),
                'cd_usuario'         => $this->session->userdata('codigo')
            );

            if(intval($cd_abaixo_assinado_acompanhamento) == 0)
            {
                $this->abaixo_assinado_model->salvar_acompanhamento($args);
            }
            else
            {
                $this->abaixo_assinado_model->atualizar_acompanhamento($cd_abaixo_assinado_acompanhamento, $args);
            }

            redirect('ecrm/abaixo_assinado/acompanhamento/'.$args['cd_abaixo_assinado'], 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_acompanhamento($cd_abaixo_assinado, $cd_abaixo_assinado_acompanhamento)
    {
        if($this->get_validacao_total())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $this->abaixo_assinado_model->excluir_acompanhamento(
                $cd_abaixo_assinado_acompanhamento, 
                $this->session->userdata('codigo')
            );

            redirect('ecrm/abaixo_assinado/acompanhamento/'.$cd_abaixo_assinado, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function retorno($cd_abaixo_assinado)
    {
        if($this->get_validacao())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $data = array(
                'cd_abaixo_assinado' => $cd_abaixo_assinado,
                'row'                => $this->abaixo_assinado_model->carrega($cd_abaixo_assinado),
                'tipo'               => $this->abaixo_assinado_model->get_tipo()
            );

            $this->load->view('ecrm/abaixo_assinado/retorno', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_retorno()
    {
        if($this->get_validacao_total())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $cd_abaixo_assinado = $this->input->post('cd_abaixo_assinado', TRUE);

            $args = array(
                'ds_retorno'                      => $this->input->post('ds_retorno', TRUE),
                'dt_retorno'                      => $this->input->post('dt_retorno', TRUE),
                'cd_abaixo_assinado_retorno_tipo' => $this->input->post('cd_abaixo_assinado_retorno_tipo', TRUE),
                'cd_usuario'                      => $this->session->userdata('codigo')
            );

            $this->abaixo_assinado_model->salvar_retorno($cd_abaixo_assinado, $args);

            $this->envia_email_retorno($cd_abaixo_assinado);

            redirect('ecrm/abaixo_assinado/retorno/'.$cd_abaixo_assinado, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function envia_email_retorno($cd_abaixo_assinado)
    {
        $this->load->model('projetos/eventos_email_model');

        $cd_evento = 363;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = array( '[LINK]' );
        $subs = array( site_url('ecrm/abaixo_assinado/cadastro/'.intval($cd_abaixo_assinado)) );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Abaixo Assinado - Registro',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function anexo($cd_abaixo_assinado)
    {
        if($this->get_validacao())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $data = array(
                'cd_abaixo_assinado' => $cd_abaixo_assinado,
                'collection'         => $this->abaixo_assinado_model->listar_anexo($cd_abaixo_assinado),
                'row'                => $this->abaixo_assinado_model->carrega($cd_abaixo_assinado),
                'fl_permissao'       => $this->get_validacao_total()
            );

            $this->load->view('ecrm/abaixo_assinado/anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_anexo()
    {
        if($this->get_validacao_total())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

            $args = array(
                'cd_abaixo_assinado' => $this->input->post('cd_abaixo_assinado', TRUE),
                'cd_usuario'         => $this->session->userdata('codigo')
            );

            if($qt_arquivo > 0)
            {
                $nr_conta = 0;

                while($nr_conta < $qt_arquivo)
                {
                    $args['arquivo_nome']  = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                    $args['arquivo']       = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);

                    $nr_conta ++;

                    $this->abaixo_assinado_model->salvar_anexo($args);
                }
            }

            redirect('ecrm/abaixo_assinado/anexo/'.$args['cd_abaixo_assinado'], 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_anexo($cd_abaixo_assinado, $cd_abaixo_assinado_anexo)
    {
        if($this->get_validacao_total())
        {
            $this->load->model('projetos/abaixo_assinado_model');

            $this->abaixo_assinado_model->excluir_anexo(
                $cd_abaixo_assinado_anexo, 
                $this->session->userdata('codigo')
            );

            redirect('ecrm/abaixo_assinado/anexo/'.$cd_abaixo_assinado, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}