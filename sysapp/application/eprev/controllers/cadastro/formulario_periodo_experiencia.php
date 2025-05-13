<?php
class Formulario_periodo_experiencia extends Controller
{
	function __construct()
    {
        parent::Controller();

		CheckLogin();
    }

    public function get_permissao()
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

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/formulario_periodo_experiencia_model');

            $data = array(
                'avaliador' => $this->formulario_periodo_experiencia_model->get_avaliador(),
                'avaliado'  => $this->formulario_periodo_experiencia_model->get_avaliado(),
                'usuario'   => $this->session->userdata('codigo')
            );

            $this->load->view('cadastro/formulario_periodo_experiencia/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
        
    }

    public function listar()
    {
    	$this->load->model('projetos/formulario_periodo_experiencia_model');

        $args = array(
            'cd_usuario_avaliador' => $this->input->post('cd_usuario_avaliador', TRUE),
            'cd_usuario_avaliado'  => $this->input->post('cd_usuario_avaliado', TRUE),
            'dt_inclusao_ini'      => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim'      => $this->input->post('dt_inclusao_fim', TRUE),
            'dt_limite_ini'        => $this->input->post('dt_limite_ini', TRUE),             
            'dt_limite_fim'        => $this->input->post('dt_limite_fim', TRUE),
            'fl_resposta'          => $this->input->post('fl_resposta', TRUE)           
        );

        manter_filtros($args); 

    	$data['collection'] = $this->formulario_periodo_experiencia_model->listar($args);

    	$this->load->view('cadastro/formulario_periodo_experiencia/index_result', $data);
    }

    public function cadastro($cd_formulario_periodo_experiencia_solic = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/formulario_periodo_experiencia_model');

            $data = array(
                'usuario'    => $this->formulario_periodo_experiencia_model->get_usuario(),
                'formulario' => $this->formulario_periodo_experiencia_model->get_formulario()     
            );

            if(intval($cd_formulario_periodo_experiencia_solic) == 0)
            {
                $data['row'] = array(
            		'cd_formulario_periodo_experiencia_solic' => intval($cd_formulario_periodo_experiencia_solic),
                    'cd_formulario_periodo_experiencia'       => '',
                    'cd_usuario_avaliador'                    => '',
                    'cd_usuario_avaliado'                     => '',
                    'dt_limite'                               => '',
                    'dt_resposta'                             => '',
                    'arquivo'                                 => '',
                    'arquivo_nome'                            => ''
            	);
            }
            else            
            {
            	$data['row'] = $this->formulario_periodo_experiencia_model->carrega($cd_formulario_periodo_experiencia_solic);
            }
            
            $this->load->view('cadastro/formulario_periodo_experiencia/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/formulario_periodo_experiencia_model');

            $cd_formulario_periodo_experiencia_solic = $this->input->post('cd_formulario_periodo_experiencia_solic', TRUE);

            $cd_formulario_periodo_experiencia = $this->input->post('cd_formulario_periodo_experiencia', TRUE);

            $args = array(
                'cd_formulario_periodo_experiencia_solic' => $cd_formulario_periodo_experiencia_solic,
                'cd_formulario_periodo_experiencia'       => $cd_formulario_periodo_experiencia,
                'cd_usuario_avaliador'                    => $this->input->post('cd_usuario_avaliador', TRUE), 
                'cd_usuario_avaliado'                     => $this->input->post('cd_usuario_avaliado' , TRUE), 
                'ds_formulario'                           => $this->input->post('ds_formulario', TRUE),
                'dt_limite'                               => $this->input->post('dt_limite', TRUE),
                'arquivo'                                 => $this->input->post('arquivo', TRUE),
                'arquivo_nome'                            => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'                              => $this->session->userdata('codigo')
            ); 

            $formulario = array();

            foreach ($this->formulario_periodo_experiencia_model->listar_grupos(intval($cd_formulario_periodo_experiencia))  
     as $key => $item)
            {   
                $formulario[$item['cd_formulario_periodo_experiencia_grupo']]['ds_grupo'] = utf8_encode($item['ds_formulario_periodo_experiencia_grupo']);

                $formulario[$item['cd_formulario_periodo_experiencia_grupo']]['pergunta'] = array();

                foreach($this->formulario_periodo_experiencia_model->listar_perguntas($item['cd_formulario_periodo_experiencia_grupo']) as $key1 => $pergunta)
                {
                    $formulario[$item['cd_formulario_periodo_experiencia_grupo']]['pergunta'][$pergunta['cd_formulario_periodo_experiencia_pergunta']] = utf8_encode($pergunta['ds_formulario_periodo_experiencia_pergunta']);
                }
            }

            $args['ds_formulario'] = json_encode($formulario);

            if(intval($cd_formulario_periodo_experiencia_solic) == 0)
            {
                $cd_formulario_periodo_experiencia_solic = $this->formulario_periodo_experiencia_model->salvar($args);

                $this->enviar_email($cd_formulario_periodo_experiencia_solic);
            }
            else
            {
                $this->formulario_periodo_experiencia_model->atualizar($cd_formulario_periodo_experiencia_solic, $args);
            }

            redirect('cadastro/formulario_periodo_experiencia', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function minhas() 
    {
        $this->load->model('projetos/formulario_periodo_experiencia_model');

        $data['avaliado'] = $this->formulario_periodo_experiencia_model->get_avaliado_minhas($this->session->userdata('codigo'));

        $this->load->view('cadastro/formulario_periodo_experiencia/minhas', $data);
    }

    public function minhas_listar()
    {
        $this->load->model('projetos/formulario_periodo_experiencia_model');

        $args = array(
            'cd_usuario_avaliado' => $this->input->post('cd_usuario_avaliado', TRUE),
            'dt_inclusao_ini'     => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim'     => $this->input->post('dt_inclusao_fim', TRUE),
            'dt_limite_ini'       => $this->input->post('dt_limite_ini', TRUE),         
            'dt_limite_fim'       => $this->input->post('dt_limite_fim', TRUE),
            'fl_resposta'         => $this->input->post('fl_resposta', TRUE) 
        );

        manter_filtros($args);

        $data['collection'] = $this->formulario_periodo_experiencia_model->listar_minhas($this->session->userdata('codigo'), $args);

        $this->load->view('cadastro/formulario_periodo_experiencia/minhas_result', $data); 
    }

    public function responder($cd_formulario_periodo_experiencia_solic)
    {
        $this->load->model('projetos/formulario_periodo_experiencia_model');

        $data['row'] = $this->formulario_periodo_experiencia_model->carrega(intval($cd_formulario_periodo_experiencia_solic));

        if(
            intval($data['row']['cd_usuario_avaliador']) == $this->session->userdata('codigo') 
            OR 
            (trim($data['row']['ds_gerencia']) == $this->session->userdata('divisao') AND $this->session->userdata('indic_13') == 'S')
            OR 
            $this->get_permissao())
        {
            if(intval($data['row']['dt_resposta']) == '')
            {
                //$data['formulario'] = json_decode($data['row']['ds_formulario'], TRUE);
                //$data['resposta']   = json_decode($data['row']['ds_resposta'], TRUE);

                $this->load->view('cadastro/formulario_periodo_experiencia/responder_arquivo', $data);
            }
            else
            {
                exibir_mensagem('SOLICITAÇÃO JÁ RESPONDIDA');
            }
        }   
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_resposta()
    {
        $this->load->model('projetos/formulario_periodo_experiencia_model');

        $cd_formulario_periodo_experiencia_solic = $this->input->post('cd_formulario_periodo_experiencia_solic', TRUE);

        $data['row'] = $this->formulario_periodo_experiencia_model->carrega(intval($cd_formulario_periodo_experiencia_solic));

        if(intval($data['row']['cd_usuario_avaliador']) == $this->session->userdata('codigo')  OR $this->get_permissao())
        {
            $formulario = json_decode($data['row']['ds_formulario'], TRUE);

            $resposta = array();

            foreach ($formulario as $key => $value) 
            {
                $resposta[$key] = $this->input->post('cd_grupo_'.$key, TRUE);
            }

            $fl_encerrar = $this->input->post('fl_encerrar', TRUE);

            if(trim($fl_encerrar) == 'S')
            {
                $this->formulario_periodo_experiencia_model->responder_arquivo_encerrar(
                    $cd_formulario_periodo_experiencia_solic, 
                    $this->input->post('arquivo', TRUE),
                    $this->input->post('arquivo_nome', TRUE),
                    $this->session->userdata('codigo')
                );

                $this->enviar_email_final($cd_formulario_periodo_experiencia_solic);

                redirect('cadastro/formulario_periodo_experiencia/minhas', 'refresh');
            }
            else
            {
                $this->formulario_periodo_experiencia_model->responder_arquivo(
                    $cd_formulario_periodo_experiencia_solic, 
                    $this->input->post('arquivo', TRUE),
                    $this->input->post('arquivo_nome', TRUE),
                    $this->session->userdata('codigo')
                );

                redirect('cadastro/formulario_periodo_experiencia/responder/'.intval($cd_formulario_periodo_experiencia_solic), 'refresh');
            }
        }   
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar_email($cd_formulario_periodo_experiencia_solic)
    {
        $this->load->model(array(
            'projetos/formulario_periodo_experiencia_model',
            'projetos/eventos_email_model'
        ));
        
        $cd_evento = 338;
        
        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->formulario_periodo_experiencia_model->carrega(intval($cd_formulario_periodo_experiencia_solic)); 

        $cd_usuario = $this->session->userdata('codigo');  

        $email_para = $this->formulario_periodo_experiencia_model->get_emails($row['cd_usuario_avaliador']);

        $tags = array('[DS_AVALIADO]', '[DT_LIMITE]', '[LINK]');

        $subs = array($row['ds_usuario_avaliado'] , $row['dt_limite'], site_url('cadastro/formulario_periodo_experiencia/responder/'.intval($row['cd_formulario_periodo_experiencia_solic'])));

        $texto = str_replace($tags, $subs, $email['email']);

        $args = array(
            'de'      => 'Formulario período experiência',
            'assunto' => str_replace('[DS_AVALIADO]', $row['ds_usuario_avaliado'], $email['assunto']),
            'para'    => implode(';', $email_para),
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
    
    public function enviar_email_final($cd_formulario_periodo_experiencia_solic)
    {
        $this->load->model(array(
            'projetos/formulario_periodo_experiencia_model',
            'projetos/eventos_email_model'
        ));
        
        $cd_evento = 339;
        
        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->formulario_periodo_experiencia_model->carrega(intval($cd_formulario_periodo_experiencia_solic));   

        $cd_usuario = $this->session->userdata('codigo');

        $tags = array('[DS_AVALIADO]', '[DS_AVALIADOR]', '[LINK]');

        $subs = array($row['ds_usuario_avaliado'] , $row['ds_avaliador'], site_url('cadastro/formulario_periodo_experiencia'));

        $texto = str_replace($tags, $subs, $email['email']);
        
        $args = array(
            'de'      => 'Formulario período experiência respondido',
            'assunto' => str_replace('[DS_AVALIADO]', $row['ds_usuario_avaliado'], $email['assunto']),
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }  

    public function pdf($cd_formulario_periodo_experiencia_solic)
    {
        $this->load->model('projetos/formulario_periodo_experiencia_model');
        
        $row = $this->formulario_periodo_experiencia_model->carrega(intval($cd_formulario_periodo_experiencia_solic));

        $formulario = json_decode($row['ds_formulario'], TRUE);

        $resposta = json_decode($row['ds_resposta'], TRUE);

        $this->load->plugin('fpdf');

        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetNrPagDe(true);
        $ob_pdf->SetMargins(10, 15, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = $row['ds_formulario_periodo_experiencia'];

        $ob_pdf->AddPage();    

        $ob_pdf->SetFont('segoeuib','',10);
        $ob_pdf->MultiCell(190, 1, 'Colaborador avaliado:', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->MultiCell(190, 8, $row['ds_avaliado'],'0', 'L');

        $ob_pdf->SetFont('segoeuib','',10);
        $ob_pdf->MultiCell(190, 1, 'Cargo:', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->MultiCell(190, 8, $row['ds_cargo'],'0', 'L');

        $ob_pdf->SetFont('segoeuib','',10);
        $ob_pdf->MultiCell(190, 1, 'Área', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->MultiCell(190, 8, $row['divisao'],'0', 'L');

        $ob_pdf->SetFont('segoeuib','',10);
        $ob_pdf->MultiCell(190, 1, 'Admissão', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->MultiCell(190, 8, $row['dt_admissao'],'0', 'L');

        $ob_pdf->SetFont('segoeuib','',10);
        $ob_pdf->MultiCell(190, 1, 'Devolução deste formulário até:', '0', 'L');
        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->MultiCell(190, 8, $row['dt_limite'],'0', 'L');

        $ob_pdf->SetFont('segoeuil','',10);
        $ob_pdf->MultiCell(190, 4, $row['ds_descricao'],'0', 'L');

        foreach($formulario as $key => $item)
        {
            $ob_pdf->SetFont('segoeuib', '', 12);
            $ob_pdf->MultiCell(0, 7, utf8_decode($item['ds_grupo']), '0', 'L');   

            foreach ($item['pergunta'] as $key2 => $pergunta)
            {
                    $ob_pdf->SetWidths(array(190));
                    $ob_pdf->SetAligns(array('L'));
                    $ob_pdf->SetFont('segoeuib', '', 8);
                    $ob_pdf->Row(array('( '.(trim($resposta[$key] == $key2) ? 'X' : '  ').' )  ' .utf8_decode($pergunta)));
                    $ob_pdf->SetAligns(array('L'));
            }

            $ob_pdf->SetY($ob_pdf->GetY() + 4); 
        }

        $ob_pdf->SetY($ob_pdf->GetY() + 5);
        $ob_pdf->SetFont('segoeuil','', 12);
        $ob_pdf->MultiCell(0, 7, 'Avaliador(a): '.$row['ds_avaliador'], '0', 'L'); 
        $ob_pdf->MultiCell(0, 18, 'Assinatura: ______________________________________________', '0', 'L');   
        $ob_pdf->MultiCell(0, 7, 'Data: '.$row['dt_resposta'], '0', 'L');
        $ob_pdf->Output();
        exit;
    }
}