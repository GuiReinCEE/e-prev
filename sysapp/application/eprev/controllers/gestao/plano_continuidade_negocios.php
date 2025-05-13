<?php
class Plano_continuidade_negocios extends Controller
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
            $this->load->view('gestao/plano_continuidade_negocios/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/plano_continuidade_negocios_model');

        $args = array(
            'cd_plano_continuidade_negocios' => $this->input->post('cd_plano_continuidade_negocios', TRUE),
            'dt_referencia'                  => $this->input->post('dt_referencia', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->plano_continuidade_negocios_model->listar($args);

        $this->load->view('gestao/plano_continuidade_negocios/index_result', $data);
    }

    public function cadastro($cd_plano_continuidade_negocios = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_plano_continuidade_negocios) == 0)
            {
                $data['row'] = array(
                    'cd_plano_continuidade_negocios' => intval($cd_plano_continuidade_negocios),
                    'dt_referencia'                  => '',
                    'nr_versao'                      => '',
                    'arquivo'                        => '',
                    'arquivo_nome'                   => '',
                    'dt_envio'                       => ''
                );
            }
            else
            {
                $this->load->model('gestao/plano_continuidade_negocios_model');

                $data['row'] = $this->plano_continuidade_negocios_model->carrega($cd_plano_continuidade_negocios);
            }

            $this->load->view('gestao/plano_continuidade_negocios/cadastro', $data);
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
            $this->load->model('gestao/plano_continuidade_negocios_model');

            $cd_plano_continuidade_negocios = $this->input->post('cd_plano_continuidade_negocios', TRUE);

            $args = array( 
                'dt_referencia' => $this->input->post('dt_referencia',TRUE),
                'nr_versao'     => $this->input->post('nr_versao',TRUE),
                'arquivo'       => $this->input->post('arquivo', TRUE),
                'arquivo_nome'  => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'    => $this->session->userdata('codigo')
            );

            if(intval($cd_plano_continuidade_negocios) == 0)
            {
                $this->plano_continuidade_negocios_model->salvar($args);
            }
            else
            {
                $this->plano_continuidade_negocios_model->atualizar($cd_plano_continuidade_negocios, $args);
            }

            redirect('gestao/plano_continuidade_negocios', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get()
    {
        $this->load->model('gestao/plano_continuidade_negocios_model');

        $row = $this->plano_continuidade_negocios_model->get_plano_continuidade_negocios();

        header('Content-Type: application/pdf');
        header("Cache-Control: public, must-revalidate");
        header("Pragma: hack");
        header('Content-Disposition: inline; filename="plano_continuidade_negocios.pdf"');
        header("Content-Transfer-Encoding: binary");        

        readfile('./up/plano_continuidade_negocios/'.$row['arquivo']);  
    }

    public function enviar($cd_plano_continuidade_negocios)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/plano_continuidade_negocios_model'
            ));

            $cd_evento = 457;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/plano_continuidade_negocios/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações no Plano de Continuidade de Negócios (PCN)',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->plano_continuidade_negocios_model->enviar($cd_plano_continuidade_negocios, $cd_usuario);

            redirect('gestao/plano_continuidade_negocios/cadastro/'.$cd_plano_continuidade_negocios, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}    