<?php
class Solic_entrega_documento extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissoa_adm()
    {
        #Gerente GFC
        if($this->session->userdata('tipo') == 'G' AND $this->session->userdata('divisao') == 'GFC')
        {
            return TRUE;
        }
        #Substituto GFC
        else if($this->session->userdata('indic_01') == 'S' AND $this->session->userdata('divisao') == 'GFC')
        {
            return TRUE;
        }
        #Itapua Molina Berchon
        elseif($this->session->userdata('codigo') == 51)
        {
            return TRUE;
        }
        #Guilherme Froes Madure
        else if($this->session->userdata('codigo') == 359)
        {
            return TRUE;
        }
        #Mauricio Soares Soares
        elseif($this->session->userdata('codigo') == 409)
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

    private function get_status()
    {
        return array(
            array('value' => 'A', 'text' => 'Acompanhamento'),
            array('value' => 'E', 'text' => 'Em Atendimento'),
            array('value' => 'O', 'text' => 'Concluído'),
            array('value' => 'C', 'text' => 'Cancelado')
        );
    }

    public function index()
    {
        $this->load->model('projetos/solic_entrega_documento_model');

        $data = array(
            'solic_entrega_documento' => $this->solic_entrega_documento_model->get_documento_tipo(),
            'status'                  => array(
                array('value' => 'E', 'text' => 'Em Atendimento'),
                array('value' => 'O', 'text' => 'Concluído'),
                array('value' => 'C', 'text' => 'Cancelado')
            )
        ); 

        $this->load->view('ecrm/solic_entrega_documento/index', $data); 
    }

    public function listar()
    {
    	$this->load->model('projetos/solic_entrega_documento_model');

        $args = array(
            'cd_solic_entrega_documento_tipo' => $this->input->post('cd_solic_entrega_documento_tipo', TRUE),
            'fl_prioridade'                   => $this->input->post('fl_prioridade', TRUE),
            'fl_recebido'                     => $this->input->post('fl_recebido', TRUE),
            'dt_recebido_ini'                 => $this->input->post('dt_recebido_ini', TRUE),
            'dt_recebido_fim'                 => $this->input->post('dt_recebido_fim', TRUE),
            'dt_ini'                          => $this->input->post('dt_ini', TRUE),
            'dt_fim'                          => $this->input->post('dt_fim', TRUE),
            'fl_status'                       => $this->input->post('fl_status', TRUE)
        );

        manter_filtros($args); 

        $data['collection'] = $this->solic_entrega_documento_model->listar(
            $this->session->userdata('codigo'), 
            $this->get_permissoa_adm(),
            $args
        );

        $this->load->view('ecrm/solic_entrega_documento/index_result', $data);
    }

    public function cadastro($cd_solic_entrega_documento = 0)
    {
        $this->load->model('projetos/solic_entrega_documento_model');

        $data['solic_entrega_documento'] = $this->solic_entrega_documento_model->get_documento_tipo();

        if(intval($cd_solic_entrega_documento) == 0)
        {
            $data['row'] = array(
                'cd_solic_entrega_documento'      => intval($cd_solic_entrega_documento),
                'cd_solic_entrega_documento_tipo' => '',
                'fl_prioridade'                   => '',
                'fl_destinatario'                 => '',
                'ds_destinatario'                 => '',
                'ds_observacao'                   => '',
                'data_ini'                        => '',
                'hr_ini'                          => '',
                'hr_limite'                       => '',
                'ds_endereco'                     => '',
                'ds_contato'                      => ''
            );

            $data['fl_cadastro'] = true;
        }
        else            
        {
            $data['row'] = $this->solic_entrega_documento_model->carrega($cd_solic_entrega_documento);

            $data['fl_cadastro'] = false;

            if(intval($data['row']['cd_usuario_inclusao']) == $this->session->userdata('codigo'))
            {
                $data['fl_cadastro'] = true;
            }
        }
        
        $this->load->view('ecrm/solic_entrega_documento/cadastro', $data);
    }

    public function salvar()
    {
        $this->load->model('projetos/solic_entrega_documento_model');

        $cd_solic_entrega_documento = $this->input->post('cd_solic_entrega_documento', TRUE);

        $args = array(
            'cd_solic_entrega_documento'      => $this->input->post('cd_solic_entrega_documento', TRUE),
            'cd_solic_entrega_documento_tipo' => $this->input->post('cd_solic_entrega_documento_tipo', TRUE),
            'data_ini'                        => $this->input->post('data_ini',TRUE).' '.$this->input->post('hr_ini', TRUE),
            'data_fim'                        => $this->input->post('data_ini' , TRUE).' '.$this->input->post('hr_limite', TRUE),
            'dt_recebido'                     => $this->input->post('dt_recebido', TRUE),
            'fl_prioridade'                   => $this->input->post('fl_prioridade', TRUE), 
            'fl_destinatario'                 => $this->input->post('fl_destinatario', TRUE), 
            'ds_destinatario'                 => $this->input->post('ds_destinatario', TRUE),
            'ds_observacao'                   => $this->input->post('ds_observacao', TRUE),
            'ds_endereco'                     => $this->input->post('ds_endereco', TRUE),
            'ds_contato'                      => $this->input->post('ds_contato', TRUE),
            'cd_usuario'                      => $this->session->userdata('codigo')
        );

        if(intval($cd_solic_entrega_documento) == 0)
        {
            $cd_solic_entrega_documento = $this->solic_entrega_documento_model->salvar($args);

            $this->enviar_email(intval($cd_solic_entrega_documento));
        }
        else
        {
            $this->solic_entrega_documento_model->atualizar($cd_solic_entrega_documento, $args);
        }

        redirect('ecrm/solic_entrega_documento', 'refresh');
    } 

    private function enviar_email($cd_solic_entrega_documento)
    {
        $this->load->model('projetos/eventos_email_model');

        $row = $this->solic_entrega_documento_model->carrega($cd_solic_entrega_documento);

        $cd_evento = 301;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = array('[DS_DOCUMENTO]', '[DS_PROPRIEDADE]', '[DS_SOLICITANTE]', '[DT_INI]', '[HR_INICIAL]', '[HR_LIMITE]', '[LINK]');

        $subs = array(
            $row['ds_solic_entrega_documento_tipo'],
            $row['ds_prioridade'],
            $row['ds_usuario_inclusao'],
            $row['data_ini'],
            $row['hr_ini'],
            $row['hr_limite'],
            site_url('ecrm/solic_entrega_documento/acompanhamento/'.intval($row['cd_solic_entrega_documento']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Solicitação Entrega Documento',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function acompanhamento($cd_solic_entrega_documento)
    {
        $this->load->model('projetos/solic_entrega_documento_model');

        $data = array(
            'row'            => $this->solic_entrega_documento_model->carrega($cd_solic_entrega_documento),
            'fl_usuario_adm' => $this->get_permissoa_adm(),
            'status'         => $this->get_status(),
            'collection'     => $this->solic_entrega_documento_model->listar_acompanhamento($cd_solic_entrega_documento),
            'acompanhamento' => array(
                'cd_solic_entrega_documento_acompanhamento' => 0,
                'ds_descricao'                              => '',
                'fl_status'                                 => ''
            )
        );

        $this->load->view('ecrm/solic_entrega_documento/acompanhamento', $data);
    }    

    public function salvar_acompanhamento()
    {
        $this->load->model('projetos/solic_entrega_documento_model');

        $cd_solic_entrega_documento_acompanhamento = $this->input->post('cd_solic_entrega_documento_acompanhamento', TRUE);
        $cd_solic_entrega_documento                = $this->input->post('cd_solic_entrega_documento', TRUE);

        $args = array(
            'cd_solic_entrega_documento' => $cd_solic_entrega_documento,
            'fl_status'                  => $this->input->post('fl_status', TRUE),
            'ds_descricao'               => $this->input->post('ds_descricao', TRUE),
            'cd_usuario'                 => $this->session->userdata('codigo')
        );

        if(intval($cd_solic_entrega_documento_acompanhamento) == 0)
        {
            $this->solic_entrega_documento_model->salvar_acompanhamento($args);
        }
        else
        {
            $this->solic_entrega_documento_model->atualizar_atualizar($cd_solic_entrega_documento_acompanhamento, $args);
        }

        if(trim($args['fl_status']) == 'A')
        {
            $this->enviar_email_acompanhamento(intval($cd_solic_entrega_documento), trim($args['ds_descricao']));
        }
        else
        {
            $this->enviar_email_status(intval($cd_solic_entrega_documento), trim($args['fl_status']));
        }

        redirect('ecrm/solic_entrega_documento/acompanhamento/'.intval($cd_solic_entrega_documento), 'refresh');
    } 

    private function enviar_email_status($cd_solic_entrega_documento, $fl_status)
    {
        $this->load->model('projetos/eventos_email_model');

        $row = $this->solic_entrega_documento_model->carrega($cd_solic_entrega_documento);

        $cd_evento = 302;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $ds_status = '';

        if(trim($fl_status) == 'E')
        {
            $ds_status = 'Em Atendimento';
        }
        else if(trim($fl_status) == 'O')
        {
            $ds_status = 'Concluído';
        }
        else
        {
            $ds_status = 'Cancelado';
        }

        $tags = array('[DS_DOCUMENTO]', '[DS_STATUS]', '[DS_SOLICITANTE]', '[DT_INI]', '[HR_INICIAL]', '[HR_LIMITE]', '[LINK]');

        $subs = array(
            $row['ds_solic_entrega_documento_tipo'],
            $ds_status,
            $row['ds_usuario_inclusao'],
            $row['data_ini'],
            $row['hr_ini'],
            $row['hr_limite'],
            site_url('ecrm/solic_entrega_documento/acompanhamento/'.intval($row['cd_solic_entrega_documento']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Solicitação Entrega Documento',
            'assunto' => str_replace('[DS_STATUS]', $ds_status, $email['assunto']),
            'para'    => $row['ds_email_solicitante'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    private function enviar_email_acompanhamento($cd_solic_entrega_documento, $ds_acompanhamento)
    {
        $this->load->model('projetos/eventos_email_model');

        $row = $this->solic_entrega_documento_model->carrega($cd_solic_entrega_documento);

        $cd_evento = 303;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $tags = array('[DS_DOCUMENTO]',  '[DS_SOLICITANTE]', '[DT_INI]', '[HR_INICIAL]', '[HR_LIMITE]', '[DS_ACOMPANHAMENTO]', '[LINK]');

        $subs = array(
            $row['ds_solic_entrega_documento_tipo'],
            $row['ds_usuario_inclusao'],
            $row['data_ini'],
            $row['hr_ini'],
            $row['hr_limite'],
            $ds_acompanhamento,
            site_url('ecrm/solic_entrega_documento/acompanhamento/'.intval($row['cd_solic_entrega_documento']))
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array(
            'de'      => 'Solicitação Entrega Documento',
            'assunto' => $email['assunto'],
            'para'    => $email['para'].';'.$row['ds_email_solicitante'],
            'cc'      => $this->session->userdata('usuario').'@eletroceee.com.br',
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function receber($cd_solic_entrega_documento)
    {
        $this->load->model('projetos/solic_entrega_documento_model');

        $this->solic_entrega_documento_model->receber(intval($cd_solic_entrega_documento), $this->session->userdata('codigo'));

        redirect('ecrm/solic_entrega_documento', 'refresh');
    }

    public function pdf($cd_solic_entrega_documento)
    {
        $this->load->model('projetos/solic_entrega_documento_model');

        $this->load->plugin('fpdf');

        $row = $this->solic_entrega_documento_model->carrega($cd_solic_entrega_documento);
                
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');               
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        
        $ob_pdf->AddPage();

        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/solic_entrega_documento.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->SetFont('segoeuil', '', 12);

        $ob_pdf->Text(15.5, 36, trim($row['ds_solic_entrega_documento_tipo']));
        $ob_pdf->Text(137, 36, trim($row['cd_gerencia']));

        $ob_pdf->Text(25, 46, trim($row['data_ini']));
        $ob_pdf->Text(94, 46, trim($row['hr_ini']));
        $ob_pdf->Text(162, 46, trim($row['hr_limite']));

        if($row['fl_prioridade'] == 'U')
        {
            $ob_pdf->Text(42.7, 60, 'X');
        }
        else if($row['fl_prioridade'] == 'M')
        {
            $ob_pdf->Text(93.2, 60, 'X');
        }
        else    
        {
            $ob_pdf->Text(143.6, 60, 'X');
        }

        $ob_pdf->Text(32, 73.5, trim($row['ds_endereco']));
        $ob_pdf->Text(15.5, 105, trim($row['ds_contato']));

        if($row['fl_destinatario'] == 'E')
        {
            $ob_pdf->Text(41.7, 125,'X');
            $ob_pdf->Text(85, 124, $row['ds_destinatario']);
        } 
        else if($row['fl_destinatario'] == 'O') 
        {
            $ob_pdf->Text(16.6, 133, 'X');
            $ob_pdf->Text(32, 133, $row['ds_destinatario']);
        }
        else 
        {
            $ob_pdf->Text(16.6, 125, 'X');
            $ob_pdf->Text(85, 124, $row['ds_destinatario']);
        }
       
        $ob_pdf->Text(23, 144.5, trim($row['ds_observacao']));
        $ob_pdf->Text(15.5, 178, trim($row['ds_solicitante']));
        $ob_pdf->Text(94, 178, trim($row['ds_re']));

        $ob_pdf->Output();
    }
}