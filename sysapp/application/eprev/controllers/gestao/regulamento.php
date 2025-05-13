<?php
class Regulamento extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC')))
        {
            //asilva
            if($this->session->userdata('codigo') == 3)
            {
                return TRUE;
            }
            //Renata Opitz
            else if($this->session->userdata('codigo') == 468)
            {
                return TRUE;
            }
			#Vanessa Silva Alves
			else if($this->session->userdata('codigo') == 424)
			{
				return true;
			}
            #Vitoria Vidal Medeiros da Silva
            else if($this->session->userdata('codigo') == 431)
            {
                return true;
            }
			#Regis Rodrigues da Silveira
            else if($this->session->userdata('codigo') == 411)
            {
                return true;
            }
            #Bruna Gomes
            else if($this->session->userdata('codigo') == 497)
            {
                return true;
            }
			#Julia Gabrieli Freitas de Oliveira
            else if($this->session->userdata('codigo') == 489)
            {
                return true;
            }
            else if($this->session->userdata('codigo') == 251)
            {
                return TRUE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    public function intranet()
    {
        $this->load->model('gestao/regulamento_model');

        $args = array(
            'cd_regulamento_tipo'     => '',
            'nr_ata_cd'               => '',
            'dt_aprovacao_cd_ini'     => '',
            'dt_aprovacao_cd_fim'     => '',
            'dt_aprovacao_previc_ini' => '',
            'dt_aprovacao_previc_fim' => '',
            'dt_envio_previc_ini'     => '',
            'dt_envio_previc_fim'     => ''
        );

        $data['collection'] = array();

        $nr_aba = $this->input->post('nr_aba', TRUE);

        $fl_desligado = (intval($nr_aba) == 0 ? 'N' : 'S');

        $regulamento_tipo = $this->regulamento_model->lista_regulamento_tipo($args, $fl_desligado);

        $data['fl_desligado'] = $fl_desligado;

        foreach ($regulamento_tipo as $key => $item) 
        {
            $row = $this->regulamento_model->listar($item['cd_regulamento_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][$key] = $row;

                $data['collection'][$key]['versoes_anteriores'] = $this->regulamento_model->lista_versoes_anteriores(
                    $row['cd_regulamento'], 
                    $item['cd_regulamento_tipo']
                );
            }
        }

        $data['nr_aba'] = $nr_aba;

        $this->load->view('gestao/regulamento/intranet', $data);
    }

    public function index()
    {
        $this->load->model('gestao/regulamento_model');

        $data['regulamento'] = $this->regulamento_model->get_regulamento_tipo();

        $this->load->view('gestao/regulamento/index', $data);
    }

    public function listar()
    {
        $this->load->model('gestao/regulamento_model');

        $args = array(
            'cd_regulamento_tipo'     => $this->input->post('cd_regulamento_tipo', TRUE),
            'nr_ata_cd'               => $this->input->post('nr_ata_cd', TRUE),
            'dt_aprovacao_cd_ini'     => $this->input->post('dt_aprovacao_cd_ini', TRUE),
            'dt_aprovacao_cd_fim'     => $this->input->post('dt_aprovacao_cd_fim', TRUE),
            'dt_aprovacao_previc_ini' => $this->input->post('dt_aprovacao_previc_ini', TRUE),
            'dt_aprovacao_previc_fim' => $this->input->post('dt_aprovacao_previc_fim', TRUE),
            'dt_envio_previc_ini'     => $this->input->post('dt_envio_previc_ini', TRUE),
            'dt_envio_previc_fim'     => $this->input->post('dt_envio_previc_fim', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = array();

        $regulamento_tipo = $this->regulamento_model->lista_regulamento_tipo($args); 

        foreach ($regulamento_tipo as $item) 
        {
             $row = $this->regulamento_model->listar($item['cd_regulamento_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][] = $row;
            }
        }

        $this->load->view('gestao/regulamento/index_result', $data);
    }

    public function cadastro($cd_regulamento = 0)
    {  
        $this->load->model('gestao/regulamento_model');

        $data['regulamento'] = $this->regulamento_model->get_regulamento_tipo(); 

        $data['collection'] = array(); 
 
        if(intval($cd_regulamento) == 0)
        {
            $data['row'] = array(
                'cd_regulamento'                => intval($cd_regulamento),
                'cd_gerencia_responsavel'       => '',
                'fl_publicado_site'             => '',
                'cd_regulamento_tipo'           => '',
                'dt_aprovacao_cd'               => '',
                'dt_aprovacao_previc'           => '',
                'dt_envio_previc'               => '',
                'ds_aprovacao_previc'           => '',
                'arquivo_aprovacao_previc'      => '',
                'arquivo_aprovacao_previc_nome' => '',
                'arquivo'                       => '',
                'arquivo_nome'                  => '',
                'arquivo_comparativo'           => '',
                'arquivo_comparativo_nome'      => '',
                'nr_ata_cd'                     => '',
                'dt_envio'                      => '',
                'fl_envio_email'                => ''
            );    

            $data['fl_editar'] = TRUE;
        }
        else        
        {
            $data['row'] = $this->regulamento_model->carrega(intval($cd_regulamento));

            $data['collection'] = $this->regulamento_model->lista_versoes_anteriores($cd_regulamento, $data['row']['cd_regulamento_tipo']);

            $data['fl_editar'] = FALSE;

            if(gerencia_in(array('GC'))) 
            {
                $data['fl_editar'] = TRUE;
            }
        }

        $this->load->view('gestao/regulamento/cadastro', $data);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/regulamento_model');

            $cd_regulamento = $this->input->post('cd_regulamento', TRUE);

            $args = array( 
                'cd_gerencia_responsavel'       => $this->input->post('cd_gerencia_responsavel',TRUE),
                'fl_publicado_site'             => $this->input->post('fl_publicado_site',TRUE),
                'dt_aprovacao_cd'               => $this->input->post('dt_aprovacao_cd',TRUE),
                'ds_aprovacao_previc'           => $this->input->post('ds_aprovacao_previc',TRUE),
                'dt_aprovacao_previc'           => $this->input->post('dt_aprovacao_previc',TRUE),
                'dt_envio_previc'               => $this->input->post('dt_envio_previc',TRUE),
                'cd_regulamento_tipo'           => $this->input->post('cd_regulamento_tipo', TRUE),
                'arquivo_aprovacao_previc'      => $this->input->post('arquivo_aprovacao_previc', TRUE),
                'arquivo_aprovacao_previc_nome' => $this->input->post('arquivo_aprovacao_previc_nome', TRUE),
                'arquivo'                       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'                  => $this->input->post('arquivo_nome', TRUE),
                'arquivo_comparativo'           => $this->input->post('arquivo_comparativo', TRUE),
                'arquivo_comparativo_nome'      => $this->input->post('arquivo_comparativo_nome', TRUE),
                'nr_ata_cd'                     => $this->input->post('nr_ata_cd', TRUE),
                'fl_envio_email'                => $this->input->post('fl_envio_email', TRUE),
                'cd_usuario'                    => $this->session->userdata('codigo')
            );

            if(intval($cd_regulamento) == 0)
            {
                $cd_regulamento = $this->regulamento_model->salvar($args);
            }
            else
            {
                $this->regulamento_model->atualizar($cd_regulamento, $args);
            }

            redirect('gestao/regulamento/cadastro/'.intval($cd_regulamento), 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar($cd_regulamento)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/regulamento_model'
            ));

            $regulamento = $this->regulamento_model->carrega($cd_regulamento);

            if(trim($regulamento['fl_envio_email']) == 'A')
            {
                $cd_evento = 298;
            }
            else
            {
                $cd_evento = 293;
            }
            
            $email = $this->eventos_email_model->carrega($cd_evento);

            $assunto = str_replace('[TIPO_REGULAMENTO]', $regulamento['ds_regulamento_tipo'], $email['assunto']);

            $tags = array('[TIPO_REGULAMENTO]', '[CNPB]', '[LINK]');

            $ds_cnpb = '';

            if(trim($regulamento['ds_cnpb']) != '')
            {
                $ds_cnpb = ' (CNPB nº '.$regulamento['ds_cnpb'].')';
            }

            $subs = array(
                $regulamento['ds_regulamento_tipo'], 
                $ds_cnpb, 
                site_url('gestao/regulamento')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Propostas ao Regulamento',
                'assunto' => $assunto,
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->regulamento_model->enviar($cd_regulamento, $cd_usuario);

            redirect('gestao/regulamento/cadastro/'.$cd_regulamento, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
} 