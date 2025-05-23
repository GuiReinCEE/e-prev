<?php
class Regimento_interno extends Controller
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
			#GUI
            else if($this->session->userdata('codigo') == 251)
            {
                return true;
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
        $this->load->model('gestao/regimento_interno_model');

        $args = array(
            'cd_regimento_interno_tipo' => '',
            'dt_referencia'             => '',
            'dt_referencia_fim'         => ''
        );

        $data['collection'] = array();

        $regimento_interno_tipo = $this->regimento_interno_model->lista_regimento_interno_tipo($args);

        foreach ($regimento_interno_tipo as $key => $item) 
        {
            $row = $this->regimento_interno_model->listar($item['cd_regimento_interno_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][$key] = $row;

                $data['collection'][$key]['versoes_anteriores'] = $this->regimento_interno_model->lista_versoes_anteriores(
                    $row['cd_regimento_interno'], 
                    $item['cd_regimento_interno_tipo']
                );
            }
        }

        $this->load->view('gestao/regimento_interno/intranet', $data);
    }

    public function index()
    {
        $this->load->model('gestao/regimento_interno_model');

        $data['regimento_interno'] = $this->regimento_interno_model->get_regimento_interno_tipo();

        $this->load->view('gestao/regimento_interno/index', $data);
    }

    public function listar()
    {
        $this->load->model('gestao/regimento_interno_model');

        $args = array(
            'cd_regimento_interno_tipo' => $this->input->post('cd_regimento_interno_tipo', TRUE),
            'dt_referencia'             => $this->input->post('dt_referencia', TRUE),
            'dt_referencia_fim'         => $this->input->post('dt_referencia_fim', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = array();

        $regimento_interno_tipo = $this->regimento_interno_model->lista_regimento_interno_tipo($args);

        foreach ($regimento_interno_tipo as $item) 
        {
            $row = $this->regimento_interno_model->listar($item['cd_regimento_interno_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][] = $row;
            }
        }

        $this->load->view('gestao/regimento_interno/index_result', $data);
    }

    public function cadastro($cd_regimento_interno = 0)
    {  
        $this->load->model('gestao/regimento_interno_model');

        $data['regimento_interno'] = $this->regimento_interno_model->get_regimento_interno_tipo();

        $data['collection'] = array();

        if(intval($cd_regimento_interno) == 0)
        {
            $data['row'] = array(
                'cd_regimento_interno'      => intval($cd_regimento_interno),
                'cd_regimento_interno_tipo' => '',
                'dt_referencia'             => '',
                'arquivo'                   => '',
                'arquivo_nome'              => '',
                'nr_versao'                 => '',
                'dt_envio'                  => '',
                'tempo_vencimento'          => ''
            );
        }
        else
        {
            $data['row'] = $this->regimento_interno_model->carrega($cd_regimento_interno);

            $data['collection'] = $this->regimento_interno_model->lista_versoes_anteriores($cd_regimento_interno, $data['row']['cd_regimento_interno_tipo']);
        }

        $this->load->view('gestao/regimento_interno/cadastro', $data);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/regimento_interno_model');

            $cd_regimento_interno = $this->input->post('cd_regimento_interno', TRUE);

            $args = array( 
                'dt_referencia'             => $this->input->post('dt_referencia',TRUE),
                'cd_regimento_interno_tipo' => $this->input->post('cd_regimento_interno_tipo', TRUE),
                'arquivo'                   => $this->input->post('arquivo', TRUE),
                'arquivo_nome'              => $this->input->post('arquivo_nome', TRUE),
                'nr_versao'                 => $this->input->post('nr_versao', TRUE),
                'tempo_vencimento'          => $this->input->post('tempo_vencimento', TRUE),
                'cd_usuario'                => $this->session->userdata('codigo')
            );

            if(intval($cd_regimento_interno) == 0)
            {
                $this->regimento_interno_model->salvar($args);
            }
            else
            {
                $this->regimento_interno_model->atualizar($cd_regimento_interno, $args);
            }

            redirect('gestao/regimento_interno', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_versao()
    {
        $this->load->model('gestao/regimento_interno_model');

        $cd_regimento_interno_tipo = $this->input->post('cd_regimento_interno_tipo', TRUE);

        $row = $this->regimento_interno_model->get_versao($cd_regimento_interno_tipo);

        echo (count($row) > 0 ? $row['nr_versao'] : 0);
    }

    public function enviar($cd_regimento_interno)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/regimento_interno_model'
            ));

            $cd_evento = 295;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $regimento_interno = $this->regimento_interno_model->carrega($cd_regimento_interno);

            $assunto = str_replace('[TIPO_REGIMENTO]', $regimento_interno['ds_regimento_interno_tipo'], $email['assunto']);

            $tags = array('[TIPO_REGIMENTO]', '[LINK]');

            $subs = array(
                $regimento_interno['ds_regimento_interno_tipo'], 
                site_url('gestao/regimento_interno')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Regimento Interno',
                'assunto' => $assunto,
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->regimento_interno_model->enviar($cd_regimento_interno, $cd_usuario);

            redirect('gestao/regimento_interno/cadastro/'.$cd_regimento_interno, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    
