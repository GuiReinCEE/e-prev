<?php
class Atividade_atendimento_cenario_legal extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index($numero, $cd_gerencia_destino)
    {
        $result = null;

        $this->load->model(array(
            'projetos/atividade_atendimento_cenario_legal_model',
            'projetos/atividade_solicitacao_model'
        ));

        $args = array(
            'cd_gerencia' => trim(strtoupper($cd_gerencia_destino)),
            'numero'      => intval($numero)
        );

        $this->atividade_solicitacao_model->carrega($result, $args);
        $row = $result->row_array();

        $data = array(
            'gerencia' => $this->atividade_atendimento_cenario_legal_model->get_gerencia($row['cd_cenario'], $cd_gerencia_destino),
            'row'      => $row
        );

        $this->load->view('atividade/atividade_atendimento_cenario_legal/index', $data);
    }

    public function salvar()
    {
        $result = null;

        $this->load->model(array(
            'projetos/atividade_atendimento_cenario_legal_model',
            'projetos/atividade_solicitacao_model'
        ));

        $args = array(
            'numero'                   => $this->input->post('numero', TRUE),
            'cd_cenario'               => $this->input->post('cd_cenario', TRUE),
            'cd_gerencia_destino'      => $this->input->post('cd_gerencia_destino', TRUE),
            'pertinencia'              => $this->input->post('pertinencia', TRUE),
            //115 = Cenário Legal
            'sistema'                  => 115,
            'ds_justificativa_cenario' => $this->input->post('ds_justificativa_cenario', TRUE),
            'cd_usuario'               => $this->session->userdata('codigo')
        );

        $this->atividade_solicitacao_model->carrega($result, $args);
        $row = $result->row_array();     

        if(trim($args['cd_gerencia_destino']) != '' AND trim($args['cd_gerencia_destino']) != trim($row['cd_gerencia_destino']))
        {
            $args['cod_solicitante'] = 98;
            $args['para']            = 'anunes@eletroceee.com.br';
            $args['area_antiga']     = $row['cd_gerencia_destino'];

            $this->atividade_atendimento_cenario_legal_model->encerra_atividade($args);

            $usuario_gerencia = $this->atividade_atendimento_cenario_legal_model->get_usuario_gerencia_destino($args['cd_gerencia_destino']);

            $numero_atividade = array();
           
            foreach ($usuario_gerencia as $key => $item) 
            {
                $args['descricao']     = 'Prezado(a): '.$item['nome'].chr(20).'Verificar procedência do seguinte conteúdo do Cenário Legal: '.$row['titulo_cenario'];
                $args['cod_atendente'] = $item['codigo'];

                $numero_atividade[] = $this->atividade_atendimento_cenario_legal_model->nova_atividade($args);

                //$this->atividade_atendimento_cenario_legal_model->email_nova_atividade($result, $args);
            }

            $args['atividades'] = $numero_atividade;

            $this->atividade_atendimento_cenario_legal_model->historico_encerra_atividade($args);
        }
        else
        {

            $this->atividade_atendimento_cenario_legal_model->conclui_atividade($args);

            $this->atividade_atendimento_cenario_legal_model->atualiza_cenario($args);

            if(intval($args['pertinencia']) == 1 OR intval($args['pertinencia']) == 4)
            {
                $this->load->plugin('encoding_pi');
                
                $this->load->model('projetos/cenario_model');
                
                $cenario = $this->cenario_model->carrega_conteudo(
                    intval($args['cd_cenario'])
                );

                $caminho_cenario = '../eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CENARIO-LEGAL/'.$cenario['ds_ano_edicao'];

                if(!is_dir($caminho_cenario))
                {
                    mkdir($caminho_cenario, 0777);
                }

                $caminho_cenario .= '/'.$cenario['ds_mes_edicao'].' - '.fixUTF8(mes_extenso($cenario['ds_mes_edicao']));

                if(!is_dir($caminho_cenario))
                {
                    mkdir($caminho_cenario, 0777);
                }

                copy('../cieprev/up/cenario/'.$cenario['arquivo'], $caminho_cenario.'/'.fixUTF8($cenario['arquivo_nome']));

                if(trim($cenario['dt_envio_email_colegiado']) == '')
                {
                    $this->load->model('projetos/eventos_email_model');

                    $cd_evento = 434;

                    $email = $this->eventos_email_model->carrega($cd_evento);

                    $tags = array('[TITULO]', '[DT_ENVIO]', '[LINK]');

                    $subs = array(
                        $cenario['titulo'], 
                        $cenario['dt_envio_email'],
                        'https://www.e-prev.com.br/cieprev/up/cenario/'.$cenario['arquivo']
                    );

                    $texto = str_replace($tags, $subs, $email['email']);

                    $cd_usuario = $this->session->userdata('codigo');

                    $envio_email = array(
                        'de'      => 'Cenário Legal',
                        'assunto' => $email['assunto'],
                        'para'    => $email['para'],
                        'cc'      => $email['cc'],
                        'cco'     => $email['cco'],
                        'texto'   => $texto
                    );

                    $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $envio_email);

                    $this->cenario_model->envia_email_colegiado(
                        intval($cenario['cd_edicao']),
                        $this->session->userdata('codigo')
                    );
                }
            }

            if(intval($args['pertinencia']) == 2)
            {
                $this->load->model('projetos/cenario_model');

                $cenario = $this->cenario_model->carrega_conteudo($args['cd_cenario']);

                $pendencia_gestao = array(
                    'cd_reuniao_sistema_gestao_tipo' => 24,
                    'cd_gerencia_destino'            => $args['cd_gerencia_destino'],
                    'ds_item'                        => 'Pertinente, altera processo : '.$cenario['titulo'],
                    'dt_prazo'                       => $cenario['dt_legal'],
                    'cd_cenario'                     => $cenario['cd_cenario'],
                    'cd_atividade'                   => $this->input->post('numero', TRUE),
                    'cd_usuario'                     => $this->session->userdata('codigo')
                );

                $this->atividade_atendimento_cenario_legal_model->salvar_pendencia_gestao($pendencia_gestao);

                /*
                $this->load->model('projetos/eventos_email_model');

                $cd_evento = 329;

                $email = $this->eventos_email_model->carrega($cd_evento);

                $cd_cenario_plano_acao = $this->atividade_atendimento_cenario_legal_model->cenario_plano_acao($args);

                $tags = array('[DS_CENARIO]', '[LINK]');

                $subs = array(
                    $row['cd_cenario'].' - '.$row['titulo_cenario'], 
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
                */
            }

            $this->load->model('projetos/eventos_email_model');

            $cd_evento = 468;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $this->atividade_solicitacao_model->carrega($result, $args);
            $row = $result->row_array();  

            if(isset($row['pertinencia_status'])) 
            {
                $gerente_supervisor = $this->atividade_solicitacao_model->get_gerente_supervisor($row['cd_gerencia_destino']);

                $para = '';

                foreach ($gerente_supervisor as $key => $item)
                {
                    $para .= $item['ds_email'].(isset($gerente_supervisor[($key+1)]) ? ';' : '');
                }

                $tags = array('[AVALIADOR]', '[TITULO]', '[RESULTADO]', '[GERENCIA]');

                $subs = array(
                    $row['ds_atendente'],
                    $row['cd_cenario'].' - '.$row['titulo_cenario'],
                    $row['pertinencia_status'],
                    $row['cd_gerencia_destino']
                );

                $texto = str_replace($tags, $subs, $email['email']);

                $cd_usuario = $this->session->userdata('codigo');

                $envio_email = array(
                    'de'      => 'Cenário Legal - Resultado Avaliação',
                    'assunto' => $email['assunto'],
                    'para'    => $para,
                    'cc'      => $email['cc'],
                    'cco'     => $email['cco'],
                    'texto'   => $texto
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $envio_email);
            }
        }

        redirect('atividade/atividade_atendimento_cenario_legal/index/'.intval($args['numero']).'/'.trim($row['cd_gerencia_destino']), 'refresh');
    }
}