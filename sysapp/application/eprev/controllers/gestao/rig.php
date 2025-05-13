<?php
class Rig extends Controller
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
            $this->load->view('gestao/rig/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/rig_model');

        $args = array(
            'cd_rig'        => $this->input->post('cd_rig', TRUE),
            'dt_referencia' => $this->input->post('dt_referencia', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->rig_model->listar($args);

        $this->load->view('gestao/rig/index_result', $data);
    }

    public function cadastro($cd_rig = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_rig) == 0)
            {
                $data['row'] = array(
                    'cd_rig'        => intval($cd_rig),
                    'dt_referencia' => '',
                    'arquivo'       => '',
                    'arquivo_nome'  => '',
                    'dt_envio'      => ''
                );
            }
            else
            {
                $this->load->model('gestao/rig_model');

                $data['row'] = $this->rig_model->carrega($cd_rig);
            }

            $this->load->view('gestao/rig/cadastro', $data);
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
            $this->load->model('gestao/rig_model');

            $cd_rig = $this->input->post('cd_rig', TRUE);

            $args = array( 
                'dt_referencia' => $this->input->post('dt_referencia',TRUE),
                'arquivo'       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'  => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'    => $this->session->userdata('codigo')
            );

            if(intval($cd_rig) == 0)
            {
                $this->rig_model->salvar($args);
            }
            else
            {
                $this->rig_model->atualizar($cd_rig, $args);
            }

            redirect('gestao/rig', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get()
    {
        $this->load->model('gestao/rig_model');

        $row = $this->rig_model->get_rig();

        header('Content-Type: application/pdf');
        header("Cache-Control: public, must-revalidate");
        header("Pragma: hack");
        header('Content-Disposition: inline; filename="rig.pdf"');
        header("Content-Transfer-Encoding: binary");        

        readfile('./up/rig/'.$row['arquivo']);  
    }

    public function enviar($cd_rig)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/rig_model'
            ));

            $cd_evento = 439;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/rig/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações RIG',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->rig_model->enviar($cd_rig, $cd_usuario);

            redirect('gestao/rig/cadastro/'.$cd_rig, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    