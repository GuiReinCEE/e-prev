<?php
class Socio_instituidor extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GCM')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_exclusao()
    {
        #Alexandre Conte
        if($this->session->userdata('codigo') == 28) 
        {
            return TRUE;
        }
        #Vanessa dos Santos Dornelles
        else if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Shaiane de Oliveira Tavares SantAnna
        else if($this->session->userdata('codigo') == 228)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        #Nalu Cristina Ribeiro das Neves
        else if($this->session->userdata('codigo') == 75)
        {
            return TRUE;
        }
        #Kenia Oliveira Barbosa
        else if($this->session->userdata('codigo') == 429)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index($cd_empresa = '')
    { 
        if($this->get_permissao())
        {
            $this->load->model('extranet/socio_instituidor_model');

            $row = $this->socio_instituidor_model->get_ultima_data_validade(intval($cd_empresa));

            $data = array(
                'dt_ult_validacao' => ((count($row) > 0) ? $row['dt_ult_validacao'] : calcular_data(date('d/m/Y'),'3 week', '-')),
                'cd_empresa'       => intval($cd_empresa),
                'empresa'          => $this->socio_instituidor_model->get_empresas(),
                'categoria'        => $this->socio_instituidor_model->get_categoria(),
                'gerencia'         => $this->socio_instituidor_model->get_gerencia()
            );

            $this->load->view('ecrm/socio_instituidor/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('extranet/socio_instituidor_model');

        $args = array(
            'cd_empresa'                     => $this->input->post('cd_empresa', TRUE),
            'cpf'                            => $this->input->post('cpf', TRUE),
            'cpf_participante'               => $this->input->post('cpf_participante', TRUE),
            'dt_inclusao_ini'                => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim'                => $this->input->post('dt_inclusao_fim', TRUE),
            'id_situacao'                    => $this->input->post('id_situacao', TRUE),
            'cd_gerencia_indicacao'          => $this->input->post('cd_gerencia_indicacao', TRUE),
            'cd_socio_instituidor_categoria' => $this->input->post('cd_socio_instituidor_categoria', TRUE),
        );

        manter_filtros($args);
        
        $args['dt_validacao_ini'] = $this->input->post('dt_validacao_ini', TRUE);
        $args['dt_validacao_fim'] = $this->input->post('dt_validacao_fim', TRUE);           
        
        $data['collection'] = $this->socio_instituidor_model->listar($args);

        $this->load->view('ecrm/socio_instituidor/index_result', $data);
    }

    public function cadastro($cd_socio_instituidor_pacote = 0, $cd_empresa = '', $cd_socio_instituidor = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('extranet/socio_instituidor_model');

            $data = array(
                'empresa'  => $this->socio_instituidor_model->get_empresas(),
                'gerencia' => $this->socio_instituidor_model->get_gerencia()
            );

            if(intval($cd_socio_instituidor_pacote) == 0)
            {            
                $data['row'] = array(
                    'cd_socio_instituidor_pacote' => $this->socio_instituidor_model->get_socio_instituidor_pacote($this->session->userdata('codigo')),
                    'cd_socio_instituidor'        => $cd_socio_instituidor,
                    'dt_envio'                    => '',
                    'cd_empresa'                  => trim($cd_empresa),
                    'cpf'                         => '',
                    'nome'                        => '',
                    'cpf_participante'            => '',
                    'cd_gerencia_indicacao'       => '',
                    'cd_usuario_indicacao'        => '' 
                );

                $data['collection'] = array();

                $data['usuarios'] = array();
            }
            else
            {
                if(intval($cd_socio_instituidor) > 0)
                {
                    $data['row'] = $this->socio_instituidor_model->get_socio_instituidor($cd_socio_instituidor); 
                }
                else
                {
                    $row = $this->socio_instituidor_model->get_envio($cd_socio_instituidor_pacote); 

                    $data['row'] = array(
                        'cd_socio_instituidor_pacote' => $cd_socio_instituidor_pacote,
                        'cd_socio_instituidor'        => 0,
                        'dt_envio'                    => $row['dt_envio'],
                        'cd_empresa'                  => '',
                        'cpf'                         => '',
                        'nome'                        => '',
                        'cpf_participante'            => '',
                        'cd_gerencia_indicacao'       => '',
                        'cd_usuario_indicacao'        => '' 
                    );
                }

                $data['usuarios'] = $this->socio_instituidor_model->get_usuarios($data['row']['cd_gerencia_indicacao']);

                $data['collection'] = $this->socio_instituidor_model->cadastro($cd_socio_instituidor_pacote);

                foreach ($data['collection'] as $key => $item) 
                {
                    $data['collection'][$key]['anterior'] = $this->socio_instituidor_model->listar_anterior($item['cpf'], $item['cd_socio_instituidor'], $item['cd_empresa']);
                }
            }

            $data['fl_permissao_exclusao'] = $this->get_permissao_exclusao();

            $this->load->view('ecrm/socio_instituidor/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_usuarios()
    {       
        $this->load->model('extranet/socio_instituidor_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        foreach($this->socio_instituidor_model->get_usuarios($cd_gerencia) as $item)
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
            $this->load->model('extranet/socio_instituidor_model');

            $cd_socio_instituidor = $this->input->post('cd_socio_instituidor', TRUE);

            $args = array(
                'cpf_participante'            => $this->input->post('cpf_participante', TRUE),
                'nome'                        => $this->input->post('nome', TRUE),
                'cpf'                         => $this->input->post('cpf', TRUE),
                'cd_empresa'                  => $this->input->post('cd_empresa', TRUE),
                'cd_gerencia_indicacao'       => $this->input->post('cd_gerencia_indicacao', TRUE),
                'cd_usuario_indicacao'       => $this->input->post('cd_usuario_indicacao', TRUE),
                'cd_socio_instituidor_pacote' => $this->input->post('cd_socio_instituidor_pacote', TRUE),
                'cd_usuario'                  => $this->session->userdata('codigo')
            );

            if(intval($cd_socio_instituidor) > 0)
            {
                $this->socio_instituidor_model->atualizar(intval($cd_socio_instituidor), $args);
            }
            else
            {
                $this->socio_instituidor_model->salvar($args);
            }

            redirect('ecrm/socio_instituidor/cadastro/'.$args['cd_socio_instituidor_pacote'].'/'.$args['cd_empresa'], 'redirect');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function verifica_cpf()
    {
        $this->load->model('extranet/socio_instituidor_model');

        $row = $this->socio_instituidor_model->verifica_cpf($this->input->post('cpf', TRUE), $this->input->post('cd_empresa', TRUE));

        $json = array_map('arrayToUTF8', $row);

        echo json_encode($json);
    }
    
    public function excluir($cd_socio_instituidor_pacote, $cd_socio_instituidor)
    {
        if($this->get_permissao())
        {
            $this->load->model('extranet/socio_instituidor_model');

            $this->socio_instituidor_model->excluir($cd_socio_instituidor);

            redirect('ecrm/socio_instituidor/cadastro/'.$cd_socio_instituidor_pacote, $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir_pacote($cd_socio_instituidor_pacote)
    {
        if($this->get_permissao())
        {
            $this->load->model('extranet/socio_instituidor_model');

            $this->socio_instituidor_model->excluir_pacote($cd_socio_instituidor_pacote);

            redirect('ecrm/socio_instituidor', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
    
    public function enviar($cd_socio_instituidor_pacote)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'extranet/socio_instituidor_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 104;

            $email = $this->eventos_email_model->carrega($cd_evento);
            
            $collection = $this->socio_instituidor_model->get_empresas_pacote($cd_socio_instituidor_pacote);
            
            foreach ($collection as $item)
            {
                if(intval($item['cd_empresa']) != 24 AND intval($item['cd_empresa']) != 29)
                {
                    $email_usuario = array();

                    $patrocinadora = $this->socio_instituidor_model->get_sigla_patrocinadora($item['cd_empresa']);

                    $collection_usuario = $this->socio_instituidor_model->get_usuario_envio_email($item['cd_empresa']);
            
                    foreach ($collection_usuario as $usuario)
                    {
                        if(trim($usuario['email']) != '')
                        {
                            $email_usuario[] = $usuario['email'];
                        }
                    }

                    $args = array( 
                        'de'      => 'Extranet',
                        'assunto' => str_replace('[EMPRESA]', $patrocinadora['sigla'], $email['assunto']),
                        'para'    => implode(';', $email_usuario),
                        'cc'      => $email['cc'],
                        'cco'     => $email['cco'],
                        'texto'   => $email['email'],
                    );

                    $this->eventos_email_model->envia_email(
                        $cd_evento, 
                        $this->session->userdata('codigo'), 
                        $args
                    );
                }
            }

            $this->socio_instituidor_model->enviar($cd_socio_instituidor_pacote, $this->session->userdata('codigo'));

            $collection = $this->socio_instituidor_model->cadastro($cd_socio_instituidor_pacote, 24);
            
            if(count($collection) > 0)
            {
                $args = array(
                    'cd_socio_instituidor_pacote' => $cd_socio_instituidor_pacote,
                    'cd_empresa'                  => 24,
                    'cd_usuario'                  => $this->session->userdata('codigo')
                );

				$this->socio_instituidor_model->valida_socio_interno($args);
            }

            $collection = $this->socio_instituidor_model->cadastro($cd_socio_instituidor_pacote, 29);
            
            if(count($collection) > 0)
            {
                $args = array(
                    'cd_socio_instituidor_pacote' => $cd_socio_instituidor_pacote,
                    'cd_empresa'                  => 29,
                    'cd_usuario'                  => $this->session->userdata('codigo')
                );

                $this->socio_instituidor_model->valida_socio_interno($args);
            }

            redirect('ecrm/socio_instituidor/cadastro/'.$cd_socio_instituidor_pacote, $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function email()
    {
        if ($this->get_permissao())
        {
            $this->load->view('ecrm/socio_instituidor/email');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_email()
    {
        if ($this->get_permissao())
        {
            $this->load->model('extranet/socio_instituidor_model');

            $args = array(
                'dt_email_ini' => $this->input->post("dt_email_ini", TRUE),
                'dt_email_fim' => $this->input->post("dt_email_fim", TRUE)
            );

            manter_filtros($args);

            $data['collection'] = $this->socio_instituidor_model->listar_email($args);

            $this->load->view('ecrm/socio_instituidor/email_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}
