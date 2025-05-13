<?php
class Manual extends Controller
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
            //lrodirguez
            else if($this->session->userdata('codigo') == 251)
            {
                return TRUE;
            }
			else
			{
				return FALSE;
			}
        }
        else
        {
            return FALSE;
        }
    }

    public function intranet()
    {
        $this->load->model('gestao/manual_model');

        $args = array(
            'cd_manual_tipo'    => '',
            'dt_referencia'     => '',
            'dt_referencia_fim' => ''
        );

        $data['collection'] = array();

        $manual_tipo = $this->manual_model->lista_manual_tipo($args);

        foreach ($manual_tipo as $key => $item) 
        {
            $row = $this->manual_model->listar($item['cd_manual_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][$key] = $row;

                $data['collection'][$key]['versoes_anteriores'] = $this->manual_model->lista_versoes_anteriores(
                    $row['cd_manual'], 
                    $item['cd_manual_tipo']
                );
            }
        }

        $this->load->view('gestao/manual/intranet', $data);
    }

    public function index()
    {
        $this->load->model('gestao/manual_model');

        $data['manual'] = $this->manual_model->get_manual_tipo();

        $this->load->view('gestao/manual/index', $data);
    }

    public function listar()
    {
        $this->load->model('gestao/manual_model');

        $args = array(
            'cd_manual_tipo'    => $this->input->post('cd_manual_tipo', TRUE),
            'dt_referencia'     => $this->input->post('dt_referencia', TRUE),
            'dt_referencia_fim' => $this->input->post('dt_referencia_fim', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = array();

        $manual_tipo = $this->manual_model->lista_manual_tipo($args);

        foreach ($manual_tipo as $item) 
        {
            $row = $this->manual_model->listar($item['cd_manual_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][] = $row;
            }
        }

        $this->load->view('gestao/manual/index_result', $data);
    }

    public function cadastro($cd_manual = 0)
    {  
        $this->load->model('gestao/manual_model');

        $data['manual'] = $this->manual_model->get_manual_tipo();

        $data['collection'] = array();

        if(intval($cd_manual) == 0)
        {
            $data['row'] = array(
                'cd_manual'        => intval($cd_manual),
                'cd_manual_tipo'   => '',
                'dt_referencia'    => '',
                'arquivo'          => '',
                'arquivo_nome'     => '',
                'nr_versao'        => '',
                'dt_envio'         => ''
            );
        }
        else
        {
            $data['row'] = $this->manual_model->carrega($cd_manual);

            $data['collection'] = $this->manual_model->lista_versoes_anteriores($cd_manual, $data['row']['cd_manual_tipo']);
        }

        $this->load->view('gestao/manual/cadastro', $data);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/manual_model');

            $cd_manual = $this->input->post('cd_manual', TRUE);

            $args = array( 
                'dt_referencia'  => $this->input->post('dt_referencia',TRUE),
                'cd_manual_tipo' => $this->input->post('cd_manual_tipo', TRUE),
                'arquivo'        => $this->input->post('arquivo', TRUE),
                'arquivo_nome'   => $this->input->post('arquivo_nome', TRUE),
                'nr_versao'      => $this->input->post('nr_versao', TRUE),
                'cd_usuario'     => $this->session->userdata('codigo')
            );

            if(intval($cd_manual) == 0)
            {
                $this->manual_model->salvar($args);
            }
            else
            {
                $this->manual_model->atualizar($cd_manual, $args);
            }

            redirect('gestao/manual', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_versao()
    {
        $this->load->model('gestao/manual_model');

        $cd_manual_tipo = $this->input->post('cd_manual_tipo', TRUE);

        $row = $this->manual_model->get_versao($cd_manual_tipo);

        echo (count($row) > 0 ? $row['nr_versao'] : 0);
    }

    public function enviar($cd_manual)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/manual_model'
            ));

            $cd_evento = 464;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $manual = $this->manual_model->carrega($cd_manual);

            $assunto = str_replace('[TIPO_MANUAL]', $manual['ds_manual_tipo'], $email['assunto']);

            $tags = array('[TIPO_MANUAL]', '[LINK]');

            $subs = array(
                $manual['ds_manual_tipo'], 
                site_url('gestao/manual')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações no Manual',
                'assunto' => $assunto,
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->manual_model->enviar($cd_manual, $cd_usuario);

            redirect('gestao/manual/cadastro/'.$cd_manual, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    
