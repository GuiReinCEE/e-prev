<?php
class Recadastramento_dependente extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        #Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        #Cristiano
        else if($this->session->userdata('codigo') == 170)
        {
            return TRUE;
        }
        #GRSC
        else if($this->session->userdata('divisao') == 'GRSC')
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_acao()
    {
        #Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        #Kenia Oliveira Barbosa
        else if($this->session->userdata('codigo') == 429)
        {
            return TRUE;
        }
        #Gabriel Eliseu Lima da Luz
        else if($this->session->userdata('codigo') == 312)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
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
			#### PENDENTE PARTICIPANTE OU FUNDAÇÃO ####
			$data['ar_pendente'][] = array('value' => 'S', 'text' => 'Sim');
			$data['ar_pendente'][] = array('value' => 'N', 'text' => 'Não');  

			#### PENDENTE PARTICIPANTE ####
			$data['ar_pendente_participante'][] = array('value' => 'S', 'text' => 'Sim');
			$data['ar_pendente_participante'][] = array('value' => 'N', 'text' => 'Não');				
			
			$this->load->view('ecrm/recadastramento_dependente/index', $data);
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
            $this->load->model('projetos/recadastramento_dependente_model');




            $args = array(
                'dt_envio_participante_ini' => $this->input->post('dt_envio_participante_ini', TRUE),
                'dt_envio_participante_fim' => $this->input->post('dt_envio_participante_fim', TRUE),
                'dt_confirmacao_ini'        => $this->input->post('dt_confirmacao_ini', TRUE),
                'dt_confirmacao_fim'        => $this->input->post('dt_confirmacao_fim', TRUE),
                'fl_confirmado'             => $this->input->post('fl_confirmado', TRUE),
                'fl_cancelado'              => $this->input->post('fl_cancelado', TRUE),
                'cd_empresa'                => $this->input->post('cd_empresa', TRUE),
                'seq_dependencia'           => $this->input->post('seq_dependencia', TRUE),
                'cd_registro_empregado'     => $this->input->post('cd_registro_empregado', TRUE),
                'fl_pendente'               => $this->input->post('fl_pendente', TRUE),
                'fl_pendente_participante'  => $this->input->post('fl_pendente_participante', TRUE)
            );
                
            manter_filtros($args);

            $data['collection'] = $this->recadastramento_dependente_model->listar($args);

            $this->load->view('ecrm/recadastramento_dependente/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_recadastramento_dependente)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/recadastramento_dependente_model');

            $data = array(
                'row'        => $this->recadastramento_dependente_model->carrega($cd_recadastramento_dependente),
                'collection' => $this->recadastramento_dependente_model->listar_dependente($cd_recadastramento_dependente),
                'dependente' => $this->recadastramento_dependente_model->listar_dependente_participante($cd_recadastramento_dependente)
            );

            $data['participante'] = $this->recadastramento_dependente_model->get_participante($data['row']['cd_empresa'], $data['row']['cd_registro_empregado'], $data['row']['seq_dependencia']);

            $args = array(
                'token'        => '9f815795413e11f45cf36720bd73e00f',
                'cd_documento' => $data['row']['id_doc']
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://www.e-prev.com.br/cieprev/index.php/clicksign/clicksign/documento_situacao');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $retorno_json = curl_exec($ch);

            $json = json_decode($retorno_json, true);

            $data['row']['fl_confirmar'] = 'N';

            if(trim($json['fl_status']) == 'CLOSED')
            {
                $data['row']['fl_confirmar'] = 'S';
            }

            $data['row']['ds_status_assinatura'] = '';

            if($json['fl_erro'] == 'N' AND isset($json['ds_status']) AND trim($json['ds_status']) != '')
            {
                $data['row']['ds_status_assinatura'] = $json['ds_status'];
            }

            $data['fl_permissao'] = $this->get_permissao_acao();

            $this->load->view('ecrm/recadastramento_dependente/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cancelar()
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/recadastramento_dependente_model',
                'projetos/eventos_email_model'
            ));

            $cd_evento = 248;

            $cd_recadastramento_dependente = $this->input->post('cd_recadastramento_dependente', TRUE);
            $ds_justificativa              = $this->input->post('ds_justificativa', TRUE);
            $cd_usuario                    = $this->session->userdata('codigo');

            $row = $this->recadastramento_dependente_model->carrega($cd_recadastramento_dependente);

            $args = array(
                'token'        => '9f815795413e11f45cf36720bd73e00f',
                'cd_documento' => $row['id_doc']
            );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://www.e-prev.com.br/cieprev/index.php/clicksign/clicksign/documento_cancelar');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $retorno_json = curl_exec($ch);

            $json = json_decode($retorno_json, true);

            $args = array(
                'ds_justificativa' => $ds_justificativa,
                'cd_usuario'       => $cd_usuario  
            );
            
            $this->recadastramento_dependente_model->cancelar(
                $cd_recadastramento_dependente,
                $args
            );
            
            $participante = $this->recadastramento_dependente_model->participante_email($cd_recadastramento_dependente);
            
            if(count($participante) > 0)
            {
                $email = $this->eventos_email_model->carrega($cd_evento);

                $tags = array('[OBS]', '[RE_CRIPTO]');
                
                $subs = array(nl2br(trim($ds_justificativa)), $participante['re_cripto']);

                $texto = str_replace($tags, $subs, $email['email']);

                $args = array(
                    'de'                    => 'Dependentes-beneficiários',
                    'assunto'               => $email['assunto'],
                    'para'                  => $participante['email'],
                    'cc'                    => $participante['email_profissional'],
                    'cco'                   => $email['cco'],
                    'texto'                 => $texto,
                    'cd_empresa'            => $participante['cd_empresa'],
                    'cd_registro_empregado' => $participante['cd_registro_empregado'],
                    'seq_dependencia'       => $participante['seq_dependencia'],
                    'tp_email'              => 'A',
                    'cd_divulgacao'         => ''
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
            }

            redirect('ecrm/recadastramento_dependente', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function confirmar_endereco($cd_recadastramento_dependente)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/recadastramento_dependente_model');

            $row = $this->recadastramento_dependente_model->carrega($cd_recadastramento_dependente);

            $args = array(
                'id_app'               => '5385fa5e2ae966dfb007d75000ec8ed5',
                're_cripto'            => $row['re_cripto'],
                'cep'                  => $row['cep'].'-'.$row['complemento_cep'],
                'endereco'             => $row['endereco'],
                'nr_endereco'          => $row['nr_endereco'],
                'bairro'               => $row['bairro'],
                'cidade'               => $row['cidade'],
                'unidade_federativa'   => $row['unidade_federativa'],
                'email'                => $row['email'],
                'email_profissional'   => $row['email_profissional'],
                'telefone'             => (trim($row['telefone']) != '' ? '('.$row['ddd'].') '.$row['telefone'] : ''),
                'celular'              => (trim($row['celular']) != '' ? '('.$row['ddd_celular'].') '.$row['celular'] : ''),
                'complemento_endereco' => $row['complemento_endereco'],
                'ramal'                => $row['ramal'],
            );


            if (($_SERVER['SERVER_ADDR'] == '10.63.255.5') or ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
            {
                $url = 'http://app.eletroceee.com.br/srvautoatendimento/index.php/set_dados_pessoais';
            }
            else
            {
                $url = 'http://appdv.eletroceee.com.br/srvautoatendimento/index.php/set_dados_pessoais';
            }

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $retorno_json = curl_exec($ch);

            $json = json_decode($retorno_json, true);

            if($json['error']['status'] == 0)
            {
                $this->recadastramento_dependente_model->confirmar_endereco($cd_recadastramento_dependente, $this->session->userdata('codigo'));
                
                redirect('ecrm/recadastramento_dependente/cadastro/'.$cd_recadastramento_dependente);
            }
            else
            {
                exibir_mensagem('<h1 style="color:red; font-size:180%;">ERRO!!!!!</h1><br/> <h3>Erro ao ataulizar os dados.</h3>');
            }   

        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function confirmar($cd_recadastramento_dependente, $tipo = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/recadastramento_dependente_model',
                'projetos/eventos_email_model'
            ));

            $cd_usuario = $this->session->userdata('codigo');

            if(intval($tipo) == 0)
            {
                $this->recadastramento_dependente_model->confirmar(
                    $cd_recadastramento_dependente,
                    $cd_usuario
                );
            }
            else
            {
                $this->recadastramento_dependente_model->confirmar_sem_oracle(
                    $cd_recadastramento_dependente,
                    $cd_usuario
                );
            }

            $participante = $this->recadastramento_dependente_model->participante_email($cd_recadastramento_dependente);

            if(count($participante) > 0)
            {
                /*
                $participante_recadastramento = $this->recadastramento_dependente_model->carrega($cd_recadastramento_dependente);

                if(trim($participante_recadastramento['fl_dependente']) == 'S')
                {
                    //COM DEPENDENTE
                    $cd_evento = 246;

                    $email = $this->eventos_email_model->carrega($cd_evento);

                    $dependente = $this->recadastramento_dependente_model->get_dependentes_cadastro($cd_recadastramento_dependente);

                    $collection = array();

                    foreach ($dependente as $item) 
                    {
                        $collection[] = $item['ds_nome'];
                    }

                    $texto = str_replace('[DEPENDENTES]',  implode(br(), $collection), $email['email']);

                    $args = array(
                        'de'                    => 'Dependentes-beneficiários',
                        'assunto'               => $email['assunto'],
                        'para'                  => $participante['email'],
                        'cc'                    => $participante['email_profissional'],
                        'cco'                   => $email['cco'],
                        'texto'                 => $texto,
                        'cd_empresa'            => $participante['cd_empresa'],
                        'cd_registro_empregado' => $participante['cd_registro_empregado'],
                        'seq_dependencia'       => $participante['seq_dependencia'],
                        'tp_email'              => 'A',
                        'cd_divulgacao'         => ''
                    );

                    $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
                }
                else
                {
                    //SEM DEPENDENTE
                    $cd_evento = 247;

                    $email = $this->eventos_email_model->carrega($cd_evento);

                    $args = array(
                        'de'                    => 'Dependentes-beneficiários',
                        'assunto'               => $email['assunto'],
                        'para'                  => $participante['email'],
                        'cc'                    => $participante['email_profissional'],
                        'cco'                   => $email['cco'],
                        'texto'                 => $email['email'],
                        'cd_empresa'            => $participante['cd_empresa'],
                        'cd_registro_empregado' => $participante['cd_registro_empregado'],
                        'seq_dependencia'       => $participante['seq_dependencia'],
                        'tp_email'              => 'A',
                        'cd_divulgacao'         => ''
                    );

                    $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
                }
                */
                
                $cd_evento = 247;

                $email = $this->eventos_email_model->carrega($cd_evento);

                $args = array(
                    'de'                    => 'Dependentes-beneficiários',
                    'assunto'               => $email['assunto'],
                    'para'                  => $participante['email'],
                    'cc'                    => $participante['email_profissional'],
                    'cco'                   => $email['cco'],
                    'texto'                 => $email['email'],
                    'cd_empresa'            => $participante['cd_empresa'],
                    'cd_registro_empregado' => $participante['cd_registro_empregado'],
                    'seq_dependencia'       => $participante['seq_dependencia'],
                    'tp_email'              => 'A',
                    'cd_divulgacao'         => ''
                );

                $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
            }

            $this->confirmar_endereco($cd_recadastramento_dependente);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}
?>