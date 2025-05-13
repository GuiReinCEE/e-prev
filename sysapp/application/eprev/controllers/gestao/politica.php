<?php
class Politica extends Controller
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
        $this->load->model('gestao/politica_model');

        $args = array(
            'cd_politica_tipo'  => '',
            'dt_referencia'     => '',
            'dt_referencia_fim' => ''
        );

        $data['collection'] = array();

        $politica_tipo = $this->politica_model->lista_politica_tipo($args);

        foreach ($politica_tipo as $key => $item) 
        {
            $row = $this->politica_model->listar($item['cd_politica_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][$key] = $row;

                $data['collection'][$key]['versoes_anteriores'] = $this->politica_model->lista_versoes_anteriores(
                    $row['cd_politica'], 
                    $item['cd_politica_tipo']
                );
            }
        }

        $this->load->view('gestao/politica/intranet', $data);
    }

    public function index()
    {
        $this->load->model('gestao/politica_model');

        $data['politica'] = $this->politica_model->get_politica_tipo();

        $this->load->view('gestao/politica/index', $data);
    }

    public function listar()
    {
        $this->load->model('gestao/politica_model');

        $args = array(
            'cd_politica_tipo'  => $this->input->post('cd_politica_tipo', TRUE),
            'dt_referencia'     => $this->input->post('dt_referencia', TRUE),
            'dt_referencia_fim' => $this->input->post('dt_referencia_fim', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = array();

        $politica_tipo = $this->politica_model->lista_politica_tipo($args);

        foreach ($politica_tipo as $item) 
        {
            $row = $this->politica_model->listar($item['cd_politica_tipo'] , $args);

            if(count($row) > 0)
            {
                $data['collection'][] = $row;
            }
        }

        $this->load->view('gestao/politica/index_result', $data);
    }

    public function cadastro($cd_politica = 0)
    {  
        $this->load->model('gestao/politica_model');

        $data['politica'] = $this->politica_model->get_politica_tipo();

        $data['collection'] = array();

        if(intval($cd_politica) == 0)
        {
            $data['row'] = array(
                'cd_politica'      => intval($cd_politica),
                'cd_politica_tipo' => '',
                'dt_referencia'    => '',
                'arquivo'          => '',
                'arquivo_nome'     => '',
                'nr_versao'        => '',
                'dt_envio'         => ''
            );
        }
        else
        {
            $data['row'] = $this->politica_model->carrega($cd_politica);

            $data['collection'] = $this->politica_model->lista_versoes_anteriores($cd_politica, $data['row']['cd_politica_tipo']);
        }

        $this->load->view('gestao/politica/cadastro', $data);
    }

    public function salvar()
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/politica_model');

            $cd_politica = $this->input->post('cd_politica', TRUE);

            $args = array( 
                'dt_referencia'    => $this->input->post('dt_referencia',TRUE),
                'cd_politica_tipo' => $this->input->post('cd_politica_tipo', TRUE),
                'arquivo'          => $this->input->post('arquivo', TRUE),
                'arquivo_nome'     => $this->input->post('arquivo_nome', TRUE),
                'nr_versao'        => $this->input->post('nr_versao', TRUE),
                'cd_usuario'       => $this->session->userdata('codigo')
            );

            if(intval($cd_politica) == 0)
            {
                $this->politica_model->salvar($args);
            }
            else
            {
                $this->politica_model->atualizar($cd_politica, $args);
            }

            redirect('gestao/politica', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get_versao()
    {
        $this->load->model('gestao/politica_model');

        $cd_politica_tipo = $this->input->post('cd_politica_tipo', TRUE);

        $row = $this->politica_model->get_versao($cd_politica_tipo);

        echo (count($row) > 0 ? $row['nr_versao'] : 0);
    }

    public function enviar($cd_politica)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/politica_model'
            ));

            $cd_evento = 328;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $politica = $this->politica_model->carrega($cd_politica);

            $assunto = str_replace('[TIPO_POLITICA]', $politica['ds_politica_tipo'], $email['assunto']);

            $tags = array('[TIPO_POLITICA]', '[LINK]');

            $subs = array(
                $politica['ds_politica_tipo'], 
                site_url('gestao/politica')
            );

            $texto = str_replace($tags, $subs, $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Política',
                'assunto' => $assunto,
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->politica_model->enviar($cd_politica, $cd_usuario);

            redirect('gestao/politica/cadastro/'.$cd_politica, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    
