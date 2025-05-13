<?php
class Cenario_plano_acao extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $data = array(
            'titulo'   => $this->cenario_plano_acao_model->get_titulo(),
            'gerencia' => $this->cenario_plano_acao_model->get_gerencia()
        );

        $this->load->view('gestao/cenario_plano_acao/index', $data);
    }

    public function listar()
    {
    	$this->load->model('projetos/cenario_plano_acao_model');

        $args = array(
            'cd_cenario'                  => $this->input->post('cd_cenario', TRUE),
            'cd_gerencia_responsavel'     => $this->input->post('cd_gerencia_responsavel', TRUE),      
            'dt_verificacao_eficacia_ini' => $this->input->post('dt_verificacao_eficacia_ini', TRUE),
            'dt_verificacao_eficacia_fim' => $this->input->post('dt_verificacao_eficacia_fim', TRUE),
            'dt_validacao_eficacia_ini'   => $this->input->post('dt_validacao_eficacia_ini', TRUE),
            'dt_validacao_eficacia_fim'   => $this->input->post('dt_validacao_eficacia_fim', TRUE),
            'dt_prazo_previsto_ini'       => $this->input->post('dt_prazo_previsto_ini', TRUE),
            'dt_prazo_previsto_fim'       => $this->input->post('dt_prazo_previsto_fim', TRUE)
        );

        manter_filtros($args);

    	$data['collection'] = $this->cenario_plano_acao_model->listar($this->session->userdata('divisao'), $args);

    	$this->load->view('gestao/cenario_plano_acao/index_result', $data);
    }

    public function cadastro($cd_cenario_plano_acao)
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $this->load->model('projetos/cenario_plano_acao_model');

            $data['row'] = $row;

            $this->load->view('gestao/cenario_plano_acao/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $cd_cenario_plano_acao = $this->input->post('cd_cenario_plano_acao', TRUE); 

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $args = array(
                'cd_cenario_plano_acao'   => $this->input->post('cd_cenario_plano_acao', TRUE),
                'ds_cenario_plano_acao'   => $this->input->post('ds_cenario_plano_acao', TRUE),
                'dt_prazo_previsto'       => $this->input->post('dt_prazo_previsto', TRUE),
                'dt_verificacao_eficacia' => $this->input->post('dt_verificacao_eficacia', TRUE),
                'dt_validacao_eficacia'   => $this->input->post('dt_validacao_eficacia', TRUE), 
                'cd_usuario'              => $this->session->userdata('codigo')
            );

            $this->cenario_plano_acao_model->atualiza($cd_cenario_plano_acao, $args);

            if(trim($args['dt_validacao_eficacia']) != '')
            {
                $this->load->model('projetos/eventos_email_model');
                
                $cd_evento = 332;

                $email = $this->eventos_email_model->carrega($cd_evento);

                $collection = $this->cenario_plano_acao_model->get_usuario_responsavel($row['cd_gerencia_responsavel']);

                $para = '';

                foreach ($collection as $key => $item) 
                {
                    $para .= $item['ds_email'];

                    if(isset($collection[($key+1)]))
                    {
                        $para .= ';';
                    }
                }

                $this->cenario_plano_acao_model->atualiza_implementacao($row['cd_atividade'], $row['cd_cenario'], $args);

                $tags = array('[DS_CENARIO]', '[LINK]');

                $subs = array(
                    $row['cd_cenario'].' - '.$row['titulo'], 
                    site_url('gestao/cenario_plano_acao/cadastro/'.intval($cd_cenario_plano_acao))
                );

                $texto = str_replace($tags, $subs, $email['email']);

                $cd_usuario = $this->session->userdata('codigo');

                $envio_email = array(
                    'de'      => 'Cenário Legal - Plano de Ação',
                    'assunto' => $email['assunto'],
                    'para'    => $para,
                    'cc'      => $email['cc'],
                    'cco'     => $email['cco'],
                    'texto'   => $texto
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $envio_email);

            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }

        redirect('gestao/cenario_plano_acao/cadastro/'.$cd_cenario_plano_acao, 'refresh');
    }

    public function envio_responsavel($cd_cenario_plano_acao)
    {
        if(gerencia_in(array('GC')))
        {
            $this->load->model(array(
                'projetos/cenario_plano_acao_model',
                'projetos/eventos_email_model'
            ));

            $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

            $cd_evento = 330;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $collection = $this->cenario_plano_acao_model->get_usuario_responsavel($row['cd_gerencia_responsavel']);

            $para = '';

            foreach ($collection as $key => $item) 
            {
                $para .= $item['ds_email'];

                if(isset($collection[($key+1)]))
                {
                    $para .= ';';
                }
            }

            $this->cenario_plano_acao_model->envio_responsavel($cd_cenario_plano_acao, $this->session->userdata('codigo'));

            $implementacao['dt_prazo_previsto'] = $row['dt_prazo_previsto'];

            $this->cenario_plano_acao_model->atualiza_prazo_previsto($row['cd_atividade'], $row['cd_cenario'], $implementacao);

            $tags = array('[DS_CENARIO]', '[LINK]');

            $subs = array(
                $row['cd_cenario'].' - '.$row['titulo'], 
                site_url('gestao/cenario_plano_acao/cadastro/'.intval($cd_cenario_plano_acao))
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $envio_email = array(
                'de'      => 'Cenário Legal - Plano de Ação',
                'assunto' => $email['assunto'],
                'para'    => $para,
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $envio_email);

            redirect('gestao/cenario_plano_acao/cadastro/'.$cd_cenario_plano_acao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function envio_auditoria($cd_cenario_plano_acao)
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array($row['cd_gerencia_responsavel'])) AND $this->session->userdata('indic_03') == '*')
        {
            $this->load->model('projetos/eventos_email_model');

            $cd_evento = 331;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $this->cenario_plano_acao_model->envio_auditoria($cd_cenario_plano_acao, $this->session->userdata('codigo'));

            $tags = array('[DS_CENARIO]', '[DATA_VERIFICACAO]', '[LINK]');

            $subs = array(
                $row['cd_cenario'].' - '.$row['titulo'], 
                $row['dt_verificacao_eficacia'],
                site_url('gestao/cenario_plano_acao/cadastro/'.intval($cd_cenario_plano_acao))
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $envio_email = array(
                'de'      => 'Cenário Legal - Plano de Ação',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $envio_email);

            redirect('gestao/cenario_plano_acao/cadastro/'.$cd_cenario_plano_acao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function acompanhamento($cd_cenario_plano_acao)
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $data = array(
                'row'        => $row,
                'cd_usuario' => $this->session->userdata('codigo'),                
                'collection' => $this->cenario_plano_acao_model->listar_acompanhamento($cd_cenario_plano_acao)
            );

            $this->load->view('gestao/cenario_plano_acao/acompanhamento', $data);

        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_acompanhamento()
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $cd_cenario_plano_acao = $this->input->post('cd_cenario_plano_acao', TRUE);

            $cd_cenario_plano_acao_acompanhamento = $this->input->post('cd_cenario_plano_acao_acompanhamento', TRUE);
            
            $args = array(
                'cd_cenario_plano_acao_acompanhamento' => $cd_cenario_plano_acao_acompanhamento ,
                'cd_cenario_plano_acao'                => $this->input->post('cd_cenario_plano_acao', TRUE),
                'ds_cenario_plano_acao_acompanhamento' => $this->input->post('ds_cenario_plano_acao_acompanhamento', TRUE),
                'cd_usuario'                           => $this->session->userdata('codigo')
            );                                           

            $this->cenario_plano_acao_model->salvar_acompanhamento($args); 
             
            redirect('gestao/cenario_plano_acao/acompanhamento/'.$cd_cenario_plano_acao , 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_acompanhamento($cd_cenario_plano_acao, $cd_cenario_plano_acao_acompanhamento)
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $this->cenario_plano_acao_model->excluir_acompanhamento(
                $cd_cenario_plano_acao_acompanhamento, 
                $this->session->userdata('codigo')
            );

            redirect('gestao/cenario_plano_acao/acompanhamento/'.$cd_cenario_plano_acao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function anexo($cd_cenario_plano_acao)
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $data = array(
                'row'        => $row,
                'cd_usuario' => $this->session->userdata('codigo'),
                'collection' => $this->cenario_plano_acao_model->anexo_listar($cd_cenario_plano_acao)
            );

            $this->load->view('gestao/cenario_plano_acao/anexo', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function anexo_salvar()
    {
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $this->load->model('projetos/cenario_plano_acao_model');
            
            $qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));
            
            $cd_cenario_plano_acao = $this->input->post('cd_cenario_plano_acao', TRUE);

            if($qt_arquivo > 0)
            {
                $nr_conta = 0;

                while($nr_conta < $qt_arquivo)
                {
                    $args = array();        
                    
                    $args['arquivo_nome'] = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
                    $args['arquivo']      = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
                    $args['cd_usuario']   = $this->session->userdata('codigo');
                    
                    $this->cenario_plano_acao_model->anexo_salvar(intval($cd_cenario_plano_acao), $args);
                    
                    $nr_conta++;
                }
            }

            redirect('gestao/cenario_plano_acao/anexo/'.intval($cd_cenario_plano_acao), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function anexo_excluir($cd_cenario_plano_acao, $cd_cenario_plano_acao_anexo)
    {   
        $this->load->model('projetos/cenario_plano_acao_model');

        $row = $this->cenario_plano_acao_model->carrega($cd_cenario_plano_acao);

        if(gerencia_in(array('AI', 'GC', $row['cd_gerencia_responsavel'])))
        {
            $cd_usuario = $this->input->post('cd_usuario', TRUE); 

            $this->cenario_plano_acao_model->anexo_excluir(
                intval($cd_cenario_plano_acao_anexo), 
                $this->session->userdata('codigo')
            );
            
            redirect('gestao/cenario_plano_acao/anexo/'.intval($cd_cenario_plano_acao), 'refresh');   
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }     
    }
}