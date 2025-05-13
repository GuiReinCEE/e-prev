<?php
class demonstrativo_estatistico extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GAP.')))
        {
            return true;
            
        }
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index()
    {
        $this->load->view('gestao/demonstrativo_estatistico/index');
    }

    public function listar()
    {
        $this->load->model('gestao/demonstrativo_estatistico_model');

        $fl_envio = 'S';

        if($this->get_permissao())
        {
            $fl_envio = 'N';
        }

        $args = array(
            'fl_envio' => $fl_envio
        );

        manter_filtros($args);

        $data['collection'] = $this->demonstrativo_estatistico_model->listar($args);

        $this->load->view('gestao/demonstrativo_estatistico/index_result', $data);
    }

    public function cadastro($cd_demonstrativo_estatistico = 0)
    {
        if($this->get_permissao())
        {
            $data = array();

            if(intval($cd_demonstrativo_estatistico) == 0)
            {
                $data['row'] = array(
                    'cd_demonstrativo_estatistico'   => intval($cd_demonstrativo_estatistico),
                    'dt_referencia'                  => '',
                    'mes_ano'                        => '',
                    'nr_ano'                         => '',
                    'nr_mes'                         => '',
                    'arquivo'                        => '',
                    'arquivo_nome'                   => '',
                    'arquivo_planilha'               => '',
                    'arquivo_planilha_nome'          => '',
                    'arquivo_ceeeprev'               => '',
                    'arquivo_ceeeprev_nome'          => '',
                    'arquivo_ceeeprev_planilha'      => '',
                    'arquivo_ceeeprev_planilha_nome' => '',
                    'dt_envio'                       => ''
                );
            }
            else
            {
                $this->load->model('gestao/demonstrativo_estatistico_model');

                $data['row'] = $this->demonstrativo_estatistico_model->carrega($cd_demonstrativo_estatistico);
            }

            $this->load->view('gestao/demonstrativo_estatistico/cadastro', $data);
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
            $this->load->model('gestao/demonstrativo_estatistico_model');

            $cd_demonstrativo_estatistico = $this->input->post('cd_demonstrativo_estatistico', TRUE);

            $args = array( 
                'dt_referencia'                  => '01/'.$this->input->post('nr_mes',TRUE).'/'.$this->input->post('nr_ano',TRUE),
                'arquivo'                        => $this->input->post('arquivo', TRUE),
                'arquivo_nome'                   => $this->input->post('arquivo_nome', TRUE),
                'arquivo_planilha'               => $this->input->post('arquivo_planilha', TRUE),
                'arquivo_planilha_nome'          => $this->input->post('arquivo_planilha_nome', TRUE),
                'arquivo_ceeeprev'               => $this->input->post('arquivo_ceeeprev', TRUE),
                'arquivo_ceeeprev_nome'          => $this->input->post('arquivo_ceeeprev_nome', TRUE),
                'arquivo_ceeeprev_planilha'      => $this->input->post('arquivo_ceeeprev_planilha', TRUE),
                'arquivo_ceeeprev_planilha_nome' => $this->input->post('arquivo_ceeeprev_planilha_nome', TRUE),
                'cd_usuario'                     => $this->session->userdata('codigo')
            );

            if(intval($cd_demonstrativo_estatistico) == 0)
            {
                $this->demonstrativo_estatistico_model->salvar($args);
            }
            else
            {
                $this->demonstrativo_estatistico_model->atualizar($cd_demonstrativo_estatistico, $args);
            }

            redirect('gestao/demonstrativo_estatistico', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function enviar($cd_demonstrativo_estatistico)
    {
        if($this->get_permissao())
        {
            $this->load->model(array(
                'projetos/eventos_email_model',
                'gestao/demonstrativo_estatistico_model'
            ));

            $cd_evento = 459;

            $email = $this->eventos_email_model->carrega($cd_evento);

            $row = $this->demonstrativo_estatistico_model->carrega($cd_demonstrativo_estatistico);

            $tags = array('[MES]', '[ANO]', '[LINK]');
            $subs = array($row['nr_mes'], $row['nr_ano'], site_url('gestao/demonstrativo_estatistico/index'));

            $texto = str_replace($tags, $subs, $email['email']);            

            $cd_usuario = $this->session->userdata('codigo');

            $args = array(
                'de'      => 'Demonstrativo Estatístico',
                'assunto' => $email['assunto'],
                'para'    => $email['para'],
                'cc'      => $email['cc'],
                'cco'     => $email['cco'],
                'texto'   => $texto
            );

            $this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

            $this->demonstrativo_estatistico_model->enviar($cd_demonstrativo_estatistico, $cd_usuario);

            redirect('gestao/demonstrativo_estatistico/cadastro/'.$cd_demonstrativo_estatistico, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function excluir($cd_demonstrativo_estatistico)
    {
        if($this->get_permissao())
        {
            $cd_usuario = $this->session->userdata('codigo');

            $this->load->model(array(
                'gestao/demonstrativo_estatistico_model'
            ));
            
            $this->demonstrativo_estatistico_model->excluir($cd_demonstrativo_estatistico, $cd_usuario);

            redirect('gestao/demonstrativo_estatistico', 'refresh');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}    