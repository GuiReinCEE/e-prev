<?php
class Codigo_etica extends Controller
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
            //lrodriguez
            else if($this->session->userdata('codigo') == 251)
            {
                return TRUE;
            }
            //Vanessa Silva Alves
            else if($this->session->userdata('codigo') == 424)
            {
                return TRUE;
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
        $this->load->model('gestao/codigo_etica_model');

        $args = array(
            'cd_codigo_etica' => '',
            'dt_referencia'   => '',
        );

        $data['collection'] = $this->codigo_etica_model->listar($args);

        $this->load->view('gestao/codigo_etica/intranet', $data);
    }

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->view('gestao/codigo_etica/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/codigo_etica_model');

        $args = array(
            'cd_codigo_etica' => $this->input->post('cd_codigo_etica', TRUE),
            'dt_referencia'   => $this->input->post('dt_referencia', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->codigo_etica_model->listar($args);

        $this->load->view('gestao/codigo_etica/index_result', $data);
    }

    public function cadastro($cd_codigo_etica = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_codigo_etica) == 0)
            {
                $data['row'] = array(
                    'cd_codigo_etica' => intval($cd_codigo_etica),
                    'dt_referencia'   => '',
                    'arquivo'         => '',
                    'arquivo_nome'    => '',
                    'dt_envio'        => ''
                );
            }
            else
            {
                $this->load->model('gestao/codigo_etica_model');

                $data['row'] = $this->codigo_etica_model->carrega($cd_codigo_etica);
            }

            $this->load->view('gestao/codigo_etica/cadastro', $data);
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
            $this->load->model('gestao/codigo_etica_model');

            $cd_codigo_etica = $this->input->post('cd_codigo_etica', TRUE);

            $args = array( 
                'dt_referencia' => $this->input->post('dt_referencia',TRUE),
                'arquivo'       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'  => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'    => $this->session->userdata('codigo')
            );

            if(intval($cd_codigo_etica) == 0)
            {
                $this->codigo_etica_model->salvar($args);
            }
            else
            {
                $this->codigo_etica_model->atualizar($cd_codigo_etica, $args);
            }

            redirect('gestao/codigo_etica', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get()
    {
        $this->load->model('gestao/codigo_etica_model');

        $row = $this->codigo_etica_model->get_codigo_etica();

        header('Content-Type: application/pdf');
        header("Cache-Control: public, must-revalidate");
        header("Pragma: hack");
        header('Content-Disposition: inline; filename="codigo_etica.pdf"');
        header("Content-Transfer-Encoding: binary");        

        readfile('./up/codigo_etica/'.$row['arquivo']);  
    }

    public function enviar($cd_codigo_etica)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/codigo_etica_model'
            ));

            $cd_evento = 294;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/codigo_etica/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Código de Ética',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->codigo_etica_model->enviar($cd_codigo_etica, $cd_usuario);

            redirect('gestao/codigo_etica/cadastro/'.$cd_codigo_etica, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    