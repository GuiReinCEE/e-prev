<?php
class Organograma extends Controller
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
            $this->load->view('gestao/organograma/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/organograma_model');

        $args = array(
            'cd_organograma' => $this->input->post('cd_organograma', TRUE),
            'dt_referencia'    => $this->input->post('dt_referencia', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->organograma_model->listar($args);

        $this->load->view('gestao/organograma/index_result', $data);
    }

    public function cadastro($cd_organograma = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_organograma) == 0)
            {
                $data['row'] = array(
                    'cd_organograma' => intval($cd_organograma),
                    'dt_referencia'    => '',
                    'arquivo'          => '',
                    'arquivo_nome'     => '',
                    'dt_envio'         => ''
                );
            }
            else
            {
                $this->load->model('gestao/organograma_model');

                $data['row'] = $this->organograma_model->carrega($cd_organograma);
            }

            $this->load->view('gestao/organograma/cadastro', $data);
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
            $this->load->model('gestao/organograma_model');

            $cd_organograma = $this->input->post('cd_organograma', TRUE);

            $args = array( 
                'dt_referencia' => $this->input->post('dt_referencia',TRUE),
                'arquivo'       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'  => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'    => $this->session->userdata('codigo')
            );

            if(intval($cd_organograma) == 0)
            {
                $this->organograma_model->salvar($args);
            }
            else
            {
                $this->organograma_model->atualizar($cd_organograma, $args);
            }

            redirect('gestao/organograma', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get()
    {
        $this->load->model('gestao/organograma_model');

        $row = $this->organograma_model->get_organograma();

        header('Content-Type: application/pdf');
        header("Cache-Control: public, must-revalidate");
        header("Pragma: hack");
        header('Content-Disposition: inline; filename="organograma.pdf"');
        header("Content-Transfer-Encoding: binary");        

        readfile('./up/organograma/'.$row['arquivo']);  
    }

    public function enviar($cd_organograma)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/organograma_model'
            ));

            $cd_evento = 450;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/organograma/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Organograma',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->organograma_model->enviar($cd_organograma, $cd_usuario);

            redirect('gestao/organograma/cadastro/'.$cd_organograma, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    