<?php
class Manual_investimento extends Controller
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
            //lrodriguez
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

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->view('gestao/manual_investimento/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/manual_investimento_model');

        $args = array(
            'cd_manual_investimento' => $this->input->post('cd_manual_investimento', TRUE),
            'dt_referencia'          => $this->input->post('dt_referencia', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->manual_investimento_model->listar($args);

        $this->load->view('gestao/manual_investimento/index_result', $data);
    }

    public function cadastro($cd_manual_investimento = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_manual_investimento) == 0)
            {
                $data['row'] = array(
                    'cd_manual_investimento' => intval($cd_manual_investimento),
                    'dt_referencia'          => '',
                    'arquivo'                => '',
                    'arquivo_nome'           => '',
                    'dt_envio'               => ''
                );
            }
            else
            {
                $this->load->model('gestao/manual_investimento_model');

                $data['row'] = $this->manual_investimento_model->carrega($cd_manual_investimento);
            }

            $this->load->view('gestao/manual_investimento/cadastro', $data);
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
            $this->load->model('gestao/manual_investimento_model');

            $cd_manual_investimento = $this->input->post('cd_manual_investimento', TRUE);

            $args = array( 
                'dt_referencia' => $this->input->post('dt_referencia',TRUE),
                'arquivo'       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'  => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'    => $this->session->userdata('codigo')
            );

            if(intval($cd_manual_investimento) == 0)
            {
                $this->manual_investimento_model->salvar($args);
            }
            else
            {
                $this->manual_investimento_model->atualizar($cd_manual_investimento, $args);
            }

            redirect('gestao/manual_investimento', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get()
    {
        $this->load->model('gestao/manual_investimento_model');

        $row = $this->manual_investimento_model->get_manual_investimento();

        header('Content-Type: application/pdf');
        header("Cache-Control: public, must-revalidate");
        header("Pragma: hack");
        header('Content-Disposition: inline; filename="manual_investimento.pdf"');
        header("Content-Transfer-Encoding: binary");        

        readfile('./up/manual_investimento/'.$row['arquivo']);  
    }

    public function enviar($cd_manual_investimento)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/manual_investimento_model'
            ));

            $cd_evento = 463;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/manual_investimento/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Manual de Investimentos',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->manual_investimento_model->enviar($cd_manual_investimento, $cd_usuario);

            redirect('gestao/manual_investimento/cadastro/'.$cd_manual_investimento, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    