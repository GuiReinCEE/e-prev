<?php
class Contrib_programada extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GCM')))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    private function get_permissao_confirma()
    {
        #Kenia Oliveira Barbosa
        if($this->session->userdata('codigo') == 429) 
        {
            return TRUE;
        }
        #Vanessa dos Santos Dornelles
        else if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Gabriel Eliseu Lima da Luz
        else if($this->session->userdata('codigo') == 312)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        #Nalu Cristina Ribeiro das Neves
        else if($this->session->userdata('codigo') == 75)
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
        if($this->get_permissao())
        {
            $this->load->view('ecrm/contrib_programada/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('autoatendimento/contribuicao_programada_model');

        $args = array(
            'dt_solicitacao_ini'    => $this->input->post('dt_solicitacao_ini', TRUE),
            'dt_solicitacao_fim'    => $this->input->post('dt_solicitacao_fim', TRUE),
            'dt_inicio_ini'         => $this->input->post('dt_inicio_ini', TRUE),
            'dt_inicio_fim'         => $this->input->post('dt_inicio_fim', TRUE),
            'nome'                  => $this->input->post('nome', TRUE),
            'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
            'dt_cancelado'          => $this->input->post('dt_cancelado', TRUE),
            'fl_confirmado'         => $this->input->post('fl_confirmado', TRUE),
            'dt_confirmacao_ini'    => $this->input->post('dt_confirmacao_ini', TRUE),
            'dt_confirmacao_fim'    => $this->input->post('dt_confirmacao_fim', TRUE)
        );
        
        manter_filtros($args);

        $data['collection'] = $this->contribuicao_programada_model->listar($args);

        $data['fl_permissao_receber'] = $this->get_permissao_confirma();
        
        $this->load->view('ecrm/contrib_programada/index_result', $data);
    }

    public function confirmar($cd_contribuicao_programada)
    {
        if($this->get_permissao_confirma())
        {
            $this->load->model('autoatendimento/contribuicao_programada_model');

            $this->contribuicao_programada_model->confirma($cd_contribuicao_programada, $this->session->userdata('codigo'));

            redirect('ecrm/contrib_programada/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function confirmar_itens()
    {
        if($this->get_permissao_confirma())
        {
            $contribuicao_programada = $this->input->post('contribuicao_programada', TRUE);

            if(is_array($contribuicao_programada))
            {
                $this->load->model('autoatendimento/contribuicao_programada_model');

                foreach ($contribuicao_programada as $key => $item) 
                {
                    $this->contribuicao_programada_model->confirma(
                        $item, 
                        $this->session->userdata('codigo')
                    );
                }
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }

    }

    public function cancelar()
    {
        if($this->get_permissao_confirma())
        {
            $contribuicao_programada = $this->input->post('contribuicao_programada', TRUE);

            if(is_array($contribuicao_programada))
            {
                $this->load->model('autoatendimento/contribuicao_programada_model');

                foreach ($contribuicao_programada as $key => $item)     
                {
                    $this->contribuicao_programada_model->cancelar(
                        $item, 
                        $_SERVER['REMOTE_ADDR'] , 
                        $this->session->userdata('codigo')
                    );
                }
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}
?>