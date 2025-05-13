<?php
class Alteracao_tabela_oracle extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GTI')))
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
            $this->load->view('servico/alteracao_tabela_oracle/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('informatica/alteracao_tabela_oracle_model');

        $data['collection'] = $this->alteracao_tabela_oracle_model->listar();

        $this->load->view('servico/alteracao_tabela_oracle/index_result', $data);
    }

    public function salvar($cd_alteracao)
    {
        if($this->get_permissao())
        {
            $this->load->model('informatica/alteracao_tabela_oracle_model');
             
            $this->alteracao_tabela_oracle_model->salvar($cd_alteracao, $this->session->userdata('codigo'));

            redirect('servico/alteracao_tabela_oracle', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function confirmadas()
    {
        if($this->get_permissao())
        {
            $this->load->view('servico/alteracao_tabela_oracle/confirmadas');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar_confirmadas()
    {
        $this->load->model('informatica/alteracao_tabela_oracle_model');

        $data = array();

        $args = array(
            'dt_alteracao_ini' => $this->input->post('dt_alteracao_ini', TRUE),
            'dt_alteracao_fim' => $this->input->post('dt_alteracao_fim', TRUE),
            'dt_inclusao_ini'  => $this->input->post('dt_inclusao_ini', TRUE),
            'dt_inclusao_fim'  => $this->input->post('dt_inclusao_fim', TRUE),
        );

        manter_filtros($args);

        $data['collection'] = $this->alteracao_tabela_oracle_model->listar_confirmadas($args);

        $this->load->view('servico/alteracao_tabela_oracle/confirmadas_result', $data);
    }

    public function set_descricao($cd_alteracao_tabela_oracle)
    {
        if($this->get_permissao())
        {
            $this->load->model('informatica/alteracao_tabela_oracle_model'); 

            $args = array(
                'ds_descricao' => $this->input->post('ds_descricao', TRUE),
                'cd_usuario'   => $this->session->userdata('codigo')
            );

            $this->alteracao_tabela_oracle_model->set_descricao($cd_alteracao_tabela_oracle, $args);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

}
?>