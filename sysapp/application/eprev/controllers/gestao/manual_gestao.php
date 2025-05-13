<?php
class Manual_gestao extends Controller
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
            $this->load->view('gestao/manual_gestao/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/manual_gestao_model');

        $args = array(
            'cd_manual_gestao' => $this->input->post('cd_manual_gestao', TRUE),
            'dt_referencia'    => $this->input->post('dt_referencia', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->manual_gestao_model->listar($args);

        $this->load->view('gestao/manual_gestao/index_result', $data);
    }

    public function cadastro($cd_manual_gestao = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_manual_gestao) == 0)
            {
                $data['row'] = array(
                    'cd_manual_gestao' => intval($cd_manual_gestao),
                    'dt_referencia'    => '',
                    'arquivo'          => '',
                    'arquivo_nome'     => '',
                    'dt_envio'         => ''
                );
            }
            else
            {
                $this->load->model('gestao/manual_gestao_model');

                $data['row'] = $this->manual_gestao_model->carrega($cd_manual_gestao);
            }

            $this->load->view('gestao/manual_gestao/cadastro', $data);
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
            $this->load->model('gestao/manual_gestao_model');

            $cd_manual_gestao = $this->input->post('cd_manual_gestao', TRUE);

            $args = array( 
                'dt_referencia' => $this->input->post('dt_referencia',TRUE),
                'arquivo'       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'  => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'    => $this->session->userdata('codigo')
            );

            if(intval($cd_manual_gestao) == 0)
            {
                $this->manual_gestao_model->salvar($args);
            }
            else
            {
                $this->manual_gestao_model->atualizar($cd_manual_gestao, $args);
            }

            redirect('gestao/manual_gestao', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get()
    {
        $this->load->model('gestao/manual_gestao_model');

        $row = $this->manual_gestao_model->get_manual_gestao();

        header('Content-Type: application/pdf');
        header("Cache-Control: public, must-revalidate");
        header("Pragma: hack");
        header('Content-Disposition: inline; filename="manual_gestao.pdf"');
        header("Content-Transfer-Encoding: binary");        

        readfile('./up/manual_gestao/'.$row['arquivo']);  
    }

    public function enviar($cd_manual_gestao)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/manual_gestao_model'
            ));

            $cd_evento = 334;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/manual_gestao/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Manual de Gestão',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->manual_gestao_model->enviar($cd_manual_gestao, $cd_usuario);

            redirect('gestao/manual_gestao/cadastro/'.$cd_manual_gestao, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    