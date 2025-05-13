<?php
class Estatuto extends Controller
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

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->view('gestao/estatuto/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('gestao/estatuto_model');

        $args = array(
            'cd_estatuto'          => $this->input->post('cd_estatuto', TRUE),
            'dt_aprovacao_cd_ini'  => $this->input->post('dt_aprovacao_cd_ini', TRUE),
            'dt_aprovacao_cd_fim'  => $this->input->post('dt_aprovacao_cd_fim', TRUE),
            'dt_aprovacao_spc_ini' => $this->input->post('dt_aprovacao_spc_ini', TRUE),
            'dt_aprovacao_spc_fim' => $this->input->post('dt_aprovacao_spc_fim', TRUE),
         );

        manter_filtros($args);

        $data['collection'] = $this->estatuto_model->listar($args);

        $this->load->view('gestao/estatuto/index_result', $data);
    }

    public function cadastro($cd_estatuto = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_estatuto) == 0)
            {
                $data['row'] = array(
                    'cd_estatuto'      => intval($cd_estatuto),
                    'dt_aprovacao_cd'  => '',
                    'dt_aprovacao_spc' => '',
                    'ds_aprovacao_spc' => '',
                    'dt_envio_spc'     => '',
                    'nr_ata_cd'        => '',
                    'arquivo'          => '',
                    'arquivo_nome'     => '',
                    'dt_envio'         => ''
                );
            }
            else
            {
                $this->load->model('gestao/estatuto_model');

                $data['row'] = $this->estatuto_model->carrega($cd_estatuto);
            }

            $this->load->view('gestao/estatuto/cadastro', $data);
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
            $this->load->model('gestao/estatuto_model');

            $cd_estatuto = $this->input->post('cd_estatuto', TRUE);

            $args = array( 
                'dt_aprovacao_cd'  => $this->input->post('dt_aprovacao_cd',TRUE),
                'dt_aprovacao_spc' => $this->input->post('dt_aprovacao_spc',TRUE),
                'ds_aprovacao_spc' => $this->input->post('ds_aprovacao_spc',TRUE),
                'dt_envio_spc'     => $this->input->post('dt_envio_spc',TRUE),
                'nr_ata_cd'        => $this->input->post('nr_ata_cd', TRUE),
                'arquivo'          => $this->input->post('arquivo', TRUE),
                'arquivo_nome'     => $this->input->post('arquivo_nome', TRUE),
                'cd_usuario'       => $this->session->userdata('codigo')
            );

            if(intval($cd_estatuto) == 0)
            {
                $this->estatuto_model->salvar($args);
            }
            else
            {
                $this->estatuto_model->atualizar($cd_estatuto, $args);
            }

            redirect('gestao/estatuto', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function get()
    {
        $this->load->model('gestao/estatuto_model');

        $row = $this->estatuto_model->get_estatuto();

        header('Content-Type: application/pdf');
        header("Cache-Control: public, must-revalidate");
        header("Pragma: hack");
        header('Content-Disposition: inline; filename="estatuto.pdf"');
        header("Content-Transfer-Encoding: binary");        

        readfile('./up/estatuto/'.$row['arquivo']);  
    }

    public function enviar($cd_estatuto)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/estatuto_model'
            ));

            $cd_evento = 296;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $texto = str_replace('[LINK]', site_url('gestao/estatuto/get'), $email['email']);

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Alterações Propostas ao estatuto',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->estatuto_model->enviar($cd_estatuto, $cd_usuario);

            redirect('gestao/estatuto/cadastro/'.$cd_estatuto, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function intranet()
    {
        $this->load->model('gestao/Estatuto_model');

        $args = array(
            'cd_estatuto'          => '',
            'dt_aprovacao_cd_ini'  => '',
            'dt_aprovacao_cd_fim'  => '',
            'dt_aprovacao_spc_ini' => '',
            'dt_aprovacao_spc_fim' => ''
         );

        $data['collection'] = $this->Estatuto_model->listar($args);

         $this->load->view('gestao/estatuto/intranet', $data);
    }
}    