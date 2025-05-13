<?php
class Protocolo_sg extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('SG')))
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
	        $this->load->view('cadastro/protocolo_sg/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('projetos/protocolo_sg_model');

        $args = array(
            'nr_numero'               => $this->input->post('nr_numero', TRUE),
            'nr_ano'                  => $this->input->post('nr_ano', TRUE),
            'cd_usuario_responsavel'  => '',
            'cd_gerencia_responsavel' => '',
            'cd_usuario_substituto'   => '',
            'cd_gerencia_substituto'  => '',
            'dt_prazo_ini'            => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'            => $this->input->post('dt_prazo_fim', TRUE),
            'fl_respondido'           => $this->input->post('fl_respondido', TRUE),
            'cd_usuario'              => ''
        );

        manter_filtros($args);

        $data['collection'] = $this->protocolo_sg_model->listar($args);

        $this->load->view('cadastro/protocolo_sg/index_result', $data);	
    }

    public function cadastro($cd_protocolo_sg = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_sg_model');

            $data['gerencia'] = $this->protocolo_sg_model->get_gerencia();

            if (intval($cd_protocolo_sg) == 0)
            {
                $data['row'] = array(
                    'cd_protocolo_sg'         => intval($cd_protocolo_sg),
                    'ds_protocolo_sg'         => '',
                    'cd_gerencia_responsavel' => '',
                    'cd_usuario_responsavel'  => '',
                    'cd_usuario_substituto'   => '', 
                    'cd_gerencia_substituto'  => '',
                    'dt_prazo'                => '',
                    'dt_envio'                => '',
                    'arquivo'                 => '',
                    'arquivo_nome'            => '',
                    'fl_conhecimento'         => ''
                );

                $data['usuario_responsavel'] = array();
                $data['usuario_substituto']  = array();
            }
            else
            {
                $data['row'] = $this->protocolo_sg_model->carrega($cd_protocolo_sg);

                $data['usuario_responsavel'] = $this->protocolo_sg_model->get_usuarios($data['row']['cd_gerencia_responsavel']);
                $data['usuario_substituto']  = $this->protocolo_sg_model->get_usuarios($data['row']['cd_gerencia_substituto']);
            }

            $this->load->view('cadastro/protocolo_sg/cadastro', $data); 
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_usuarios()
    {
        $this->load->model('projetos/protocolo_sg_model');

        $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

        echo json_encode($this->protocolo_sg_model->get_usuarios($cd_gerencia));
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_sg_model');

            $cd_protocolo_sg = $this->input->post('cd_protocolo_sg', TRUE);

            $args = array(
                'ds_protocolo_sg'         => $this->input->post('ds_protocolo_sg', TRUE),
                'cd_gerencia_responsavel' => $this->input->post('cd_gerencia_responsavel', TRUE),
                'cd_usuario_responsavel'  => $this->input->post('cd_usuario_responsavel', TRUE),
                'cd_usuario_substituto'   => $this->input->post('cd_usuario_substituto', TRUE),
                'cd_gerencia_substituto'  => $this->input->post('cd_gerencia_substituto', TRUE),
                'dt_prazo'                => $this->input->post('dt_prazo', TRUE),
                'arquivo'                 => $this->input->post('arquivo', TRUE),
                'arquivo_nome'            => $this->input->post('arquivo_nome', TRUE),
                'fl_conhecimento'         => $this->input->post('fl_conhecimento', TRUE),
                'cd_usuario'              => $this->session->userdata('codigo')
            );

            if(intval($cd_protocolo_sg) == 0)
            {
                $cd_protocolo_sg = $this->protocolo_sg_model->salvar($args);
            }
            else
            {
                $this->protocolo_sg_model->atualizar(intval($cd_protocolo_sg), $args);
            }
            
            redirect('cadastro/protocolo_sg/cadastro/'.$cd_protocolo_sg, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar($cd_protocolo_sg)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_sg_model');

            $this->protocolo_sg_model->enviar(intval($cd_protocolo_sg), $this->session->userdata('codigo'));

            $this->enviar_email($cd_protocolo_sg);

            redirect('cadastro/protocolo_sg', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    private function enviar_email($cd_protocolo_sg)
    {
        $this->load->model(array(
            'projetos/protocolo_sg_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 179;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->protocolo_sg_model->carrega($cd_protocolo_sg);

        $tags = array('[ANO_NUMERO]', '[LINK]');

        $subs = array(
            $row['ano_numero'],
            site_url('cadastro/protocolo_sg/minhas')
        );

        $texto = str_replace($tags, $subs, $email['email']);

        if(trim($row['dt_prazo']) != '')
        {
            $texto = str_replace('[PRAZO]', 'Prazo de atendimento até '.$row['dt_prazo'], $texto);
        }

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Protocolo Secretária',
            'assunto' => $email['assunto'],
            'para'    => $row['ds_email_responsavel'].(trim($row['ds_email_substituto']) != '' ? ';'.trim($row['ds_email_substituto']) : ''),
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }

    public function excluir($cd_protocolo_sg)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/protocolo_sg_model');

            $this->protocolo_sg_model->excluir(intval($cd_protocolo_sg), $this->session->userdata('codigo'));

            redirect('cadastro/protocolo_sg', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function minhas()
    {
        $this->load->view('cadastro/protocolo_sg/minhas');
    }

    public function minhas_listar()
    {
        $this->load->model('projetos/protocolo_sg_model');

        $args = array(
            'nr_numero'               => $this->input->post('nr_numero', TRUE),
            'nr_ano'                  => $this->input->post('nr_ano', TRUE),
            'cd_usuario_responsavel'  => '',
            'cd_gerencia_responsavel' => '',
            'cd_usuario_substituto'   => '',
            'cd_gerencia_substituto'  => '',
            'dt_prazo_ini'            => $this->input->post('dt_prazo_ini', TRUE),
            'dt_prazo_fim'            => $this->input->post('dt_prazo_fim', TRUE),
            'fl_respondido'           => $this->input->post('fl_respondido', TRUE),
            'cd_usuario'              => $this->session->userdata('codigo')
        );
        
        manter_filtros($args);

        $data['collection'] = $this->protocolo_sg_model->listar($args);

        $this->load->view('cadastro/protocolo_sg/minhas_result', $data); 
    }

    public function receber($cd_protocolo_sg)
    {
        $this->load->model('projetos/protocolo_sg_model');

        $this->protocolo_sg_model->receber(intval($cd_protocolo_sg), $this->session->userdata('codigo'));

        $this->receber_enviar_email($cd_protocolo_sg);

        redirect('cadastro/protocolo_sg/minhas', 'refresh');
    }

    private function receber_enviar_email($cd_protocolo_sg)
    {
        $this->load->model(array(
            'projetos/protocolo_sg_model',
            'projetos/eventos_email_model'
        ));

        $cd_evento = 257;

        $email = $this->eventos_email_model->carrega($cd_evento);

        $row = $this->protocolo_sg_model->carrega($cd_protocolo_sg);

        $tags = array('[ANO_NUMERO]', '[LINK]');

        $subs = array(
            $row['ano_numero'],
            site_url('cadastro/protocolo_sg')
        );

        $texto = str_replace($tags, $subs, $email['email']);

        $cd_usuario = $this->session->userdata('codigo');

        $args = array( 
            'de'      => 'Protocolo Secretária',
            'assunto' => $email['assunto'],
            'para'    => $email['para'],
            'cc'      => $email['cc'],
            'cco'     => $email['cco'],
            'texto'   => $texto
        );

        $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
    }
}