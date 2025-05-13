<?php
class Campanha_aumento_contrib_inst extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    public function index()
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $data['instituidor'] = $this->campanha_aumento_contrib_inst_model->get_instituidor();

            $this->load->view('planos/campanha_aumento_contrib_inst/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }   
    }

    public function listar()
    {
        $this->load->model('expansao/campanha_aumento_contrib_inst_model');   

        $args = array(
            'cd_empresa'      => $this->input->post('cd_empresa', TRUE),
            'dt_envio'        => $this->input->post('dt_envio', TRUE),
            'dt_envio_ini'    => $this->input->post('dt_envio_ini', TRUE),
            'dt_envio_fim'    => $this->input->post('dt_envio_fim', TRUE),
            'dt_inclusao_ini' => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim' => $this->input->post('dt_inclusao_fim', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->campanha_aumento_contrib_inst_model->listar($args);
       
        $this->load->view('planos/campanha_aumento_contrib_inst/index_result', $data); 
    }

    public function cadastro($cd_campanha_aumento_contrib_inst = 0)
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {    
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $data['instituidor'] = $this->campanha_aumento_contrib_inst_model->get_instituidor();

            if(intval($cd_campanha_aumento_contrib_inst) == 0)
            {
               $data['row'] = array(
                    'cd_campanha_aumento_contrib_inst' => $this->input->post('cd_campanha_aumento_contrib_inst', TRUE),
                    'cd_empresa'                       => '',
                    'ds_assunto'                       => '',
                    'ds_tpl'                           => '',
                    'dt_envio'                         => '',
                    'dt_base_extrato'                  => ''
                );
            }
            else
            {
                $data['row'] = $this->campanha_aumento_contrib_inst_model->carrega(
                    $cd_campanha_aumento_contrib_inst
                );
            }

            $this->load->view('planos/campanha_aumento_contrib_inst/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function get_campanha_anterior()
    {
        $this->load->model('expansao/campanha_aumento_contrib_inst_model'); 
       
        $campanha = $this->campanha_aumento_contrib_inst_model->get_campanha_anterior(
            $this->input->post("cd_empresa", true)
        );

        $campanha = array_map("arrayToUTF8", $campanha);            

        echo json_encode($campanha);
    }       

    public function salvar()
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {     
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $cd_campanha_aumento_contrib_inst = $this->input->post('cd_campanha_aumento_contrib_inst', TRUE);

            $args = array(
                'cd_empresa' => $this->input->post('cd_empresa', TRUE),
                'ds_assunto' => $this->input->post('ds_assunto', TRUE),
                'ds_tpl'     => $this->input->post('ds_tpl', TRUE),
                'cd_usuario' => $this->session->userdata('codigo')
            ); 

            if(intval($cd_campanha_aumento_contrib_inst) == 0)
            {
                $cd_campanha_aumento_contrib_inst =  $this->campanha_aumento_contrib_inst_model->salvar($args);
            }
            else
            {
                $this->campanha_aumento_contrib_inst_model->atualizar(
                    intval($cd_campanha_aumento_contrib_inst), $args
                );
            }

            redirect('planos/campanha_aumento_contrib_inst/cadastro/'.$cd_campanha_aumento_contrib_inst, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function participante($cd_campanha_aumento_contrib_inst)
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        { 
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $data['row'] = $this->campanha_aumento_contrib_inst_model->carrega(
                $cd_campanha_aumento_contrib_inst
            );

            $this->load->view('planos/campanha_aumento_contrib_inst/participante', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function participante_listar()
    {
        CheckLogin();
        
        $this->load->model('expansao/campanha_aumento_contrib_inst_model');

        $cd_campanha_aumento_contrib_inst = $this->input->post('cd_campanha_aumento_contrib_inst', TRUE);

        $row = $this->campanha_aumento_contrib_inst_model->carrega(
            $cd_campanha_aumento_contrib_inst
        );
  
        $data['fl_email'] = (trim($row['ds_tpl']) != '' ? 'S' : 'N');
   
        $args = array(
            'fl_exclusao' => $this->input->post('fl_exclusao', TRUE),
            'fl_email'    => $this->input->post('fl_email', TRUE),
            'fl_app'      => $this->input->post('fl_app', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->campanha_aumento_contrib_inst_model->participante_listar($cd_campanha_aumento_contrib_inst, $args);
        $data['row']        = $row;
       
        $this->load->view('planos/campanha_aumento_contrib_inst/participante_result', $data);
    }

    public function excluir_participante($cd_campanha_aumento_contrib_inst, $cd_campanha_aumento_contrib_inst_participante)
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {     
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $this->campanha_aumento_contrib_inst_model->remover(
                $cd_campanha_aumento_contrib_inst_participante, 
                $this->session->userdata('codigo')
            );
           
            redirect('planos/campanha_aumento_contrib_inst/participante/'.$cd_campanha_aumento_contrib_inst, 'refresh');
        }
        else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
    }

    public function adicionar_participante($cd_campanha_aumento_contrib_inst, $cd_campanha_aumento_contrib_inst_participante)
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {     
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $this->campanha_aumento_contrib_inst_model->adicionar(
                $cd_campanha_aumento_contrib_inst_participante,
                $this->session->userdata('codigo')
            );
           
            redirect('planos/campanha_aumento_contrib_inst/participante/'.$cd_campanha_aumento_contrib_inst, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    private function get_info_email($cd_edicao, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $collection = $this->campanha_aumento_contrib_inst_model->get_dados_meu_retrato(
            $cd_edicao, 
            $cd_empresa, 
            $cd_registro_empregado, 
            $seq_dependencia
        );

        $meu_retrato = array();

        foreach ($collection as $key => $item) 
        {
            $meu_retrato[$item['cd_linha']] = array(
                'ds_linha' => $item['ds_linha'],
                'vl_valor' => $item['vl_valor']
            );
        }

        $row_cripto_re = $this->campanha_aumento_contrib_inst_model->cripto_re(
            $cd_empresa, 
            $cd_registro_empregado, 
            $seq_dependencia
        );

        $data = array(
            'row'        => $meu_retrato,
            'cripto_re'  => $row_cripto_re['cripto_re']
        );

        return $data;
    }

    public function ver_email($cd_campanha_aumento_contrib_inst, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {    
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $row = $this->campanha_aumento_contrib_inst_model->carrega($cd_campanha_aumento_contrib_inst);

            $data = $this->get_info_email($row['cd_edicao'], $cd_empresa, $cd_registro_empregado, $seq_dependencia);

            $this->load->view('planos/campanha_aumento_contrib_inst/'.$row['ds_tpl'], $data);
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function enviar_email_teste($cd_campanha_aumento_contrib_inst, $cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {   
            $this->load->model(array(
                'expansao/campanha_aumento_contrib_inst_model',
                'projetos/eventos_email_model'
            ));

            $row = $this->campanha_aumento_contrib_inst_model->carrega($cd_campanha_aumento_contrib_inst);

            $data = $this->get_info_email($row['cd_edicao'], $cd_empresa, $cd_registro_empregado, $seq_dependencia);

            $cd_evento = 237;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $args = array(
                'de'      => 'Campanha Aumento de Contribuiчуo - Instituidor',
                'assunto' => $row['ds_assunto'],
                'para'    => '',
                'cc'      => '',
                'cco'     => $email['cco'],
                'texto'   => $this->load->view('planos/campanha_aumento_contrib_inst/'.$row['ds_tpl'], $data, TRUE)
            );

            $this->eventos_email_model->envia_email($cd_evento, $this->session->userdata('codigo'), $args);

            redirect('planos/campanha_aumento_contrib_inst/participante/'.$cd_campanha_aumento_contrib_inst, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function agendar_envio($cd_campanha_aumento_contrib_inst)
    {
        CheckLogin();

        if(gerencia_in(array('GCM')))
        {  
            $this->load->model('expansao/campanha_aumento_contrib_inst_model');

            $this->campanha_aumento_contrib_inst_model->agendar_envio($cd_campanha_aumento_contrib_inst, $this->session->userdata('codigo'));

            redirect('planos/campanha_aumento_contrib_inst/cadastro/'.$cd_campanha_aumento_contrib_inst, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    private function enviar_email($cd_campanha_aumento_contrib_inst)
    {
        $this->load->model(array(
            'expansao/campanha_aumento_contrib_inst_model',
            'projetos/eventos_email_model'
        ));

        $row = $this->campanha_aumento_contrib_inst_model->carrega($cd_campanha_aumento_contrib_inst);

        if(trim($row['dt_agenda_envio']) != '' AND trim($row['dt_envio']) == '')
        {
            $divulgacao = array(
                'ds_assunto' => 'Campanha Aumento de Contribuiчуo - '.trim($row['ds_instituidor']).' - '.date('m/Y'),
                'cd_usuario' => $row['cd_usuario_agenda_envio']
            );

            $cd_divulgacao = $this->campanha_aumento_contrib_inst_model->cadastra_email_mkt($divulgacao);

            $this->campanha_aumento_contrib_inst_model->enviar($cd_campanha_aumento_contrib_inst, $cd_divulgacao, $row['cd_usuario_agenda_envio']);

            $cd_evento = 237;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $args = array(
                'fl_exclusao' => 'N',
                'fl_email'    => 'S',
                'fl_app'      => ''
            );

            manter_filtros($args);

            $collection = $this->campanha_aumento_contrib_inst_model->participante_listar($cd_campanha_aumento_contrib_inst, $args);

            foreach ($collection as $key => $item) 
            {
                $data = $this->get_info_email($row['cd_edicao'], $item['cd_empresa'], $item['cd_registro_empregado'], $item['seq_dependencia']);

                $args = array(
                    'de'                    => 'Campanha Aumento de Contribuiчуo - Instituidor',
                    'assunto'               => $row['ds_assunto'],
                    'para'                  => $item['email'],
                    'cc'                    => $item['email_profissional'],
                    'cco'                   => '',
                    'cd_empresa'            => $item['cd_empresa'],
                    'cd_registro_empregado' => $item['cd_registro_empregado'],
                    'seq_dependencia'       => $item['seq_dependencia'],
                    'cd_divulgacao'         => $cd_divulgacao,
                    'tp_email'              => 'F',
                    'texto'                 => $this->load->view('planos/campanha_aumento_contrib_inst/'.$row['ds_tpl'], $data, TRUE)
                );

                $this->eventos_email_model->envia_email($cd_evento, $row['cd_usuario_agenda_envio'], $args);
            }
    		
    		$this->campanha_aumento_contrib_inst_model->enviarPush($cd_campanha_aumento_contrib_inst, $row['cd_usuario_agenda_envio']);
        }
    }

    public function rotina_envia_email()
    {
        $this->load->model('expansao/campanha_aumento_contrib_inst_model');

        set_time_limit(0);

        echo "ARQUIVO => ".(__FILE__).chr(10);
        echo "INI => ".date("Y-m-d H:i:s").chr(10);
        flush();

        $row = $this->campanha_aumento_contrib_inst_model->get_envio_email_agendado();

        $qt = 0;

        if(isset($row['cd_campanha_aumento_contrib_inst']) AND intval($row['cd_campanha_aumento_contrib_inst']) > 0)
        {
            $qt = intval($row['qt_envio']);

            $this->enviar_email($row['cd_campanha_aumento_contrib_inst']);
        }

        echo "QT ENVIO => ".$qt.chr(10);

        echo "FIM => ".date("Y-m-d H:i:s").chr(10);
        flush();

    }
}
?>