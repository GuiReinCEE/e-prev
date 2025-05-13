<?php
class Acao_vendas extends Controller
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

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/acao_vendas_model');

            $data['usuario_responsavel'] = $this->acao_vendas_model->get_usuarios();

            $this->load->view('planos/acao_vendas/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('projetos/acao_vendas_model');

        $args = array(
            'cd_acao_vendas'         => $this->input->post('cd_acao_vendas', TRUE),
            'cd_usuario_responsavel' => $this->input->post('cd_usuario_responsavel', TRUE),
            'dt_acao_vendas_ini'     => $this->input->post('dt_acao_vendas_ini', TRUE),
            'dt_acao_vendas_fim'     => $this->input->post('dt_acao_vendas_fim', TRUE)
         );

        manter_filtros($args);

        $data['collection'] = $this->acao_vendas_model->listar($args);

        $this->load->view('planos/acao_vendas/index_result', $data);
    }

    public function cadastro($cd_acao_vendas = 0)
    { 
        $this->load->model('projetos/acao_vendas_model');

        $data['usuario_responsavel'] = $this->acao_vendas_model->get_usuarios();
       
        if(intval($cd_acao_vendas) == 0)
        {
            $data['row'] = array(
                'cd_acao_vendas'         => intval($cd_acao_vendas),
                'cd_usuario_responsavel' => $this->session->userdata('codigo'),
                'ds_acao_vendas'         => '',
                'dt_acao_vendas'         => '',
                'hr_acao_vendas'         => '',
                'nr_contatos'            => '', 
                'nr_fechamento'          => ''
             );
        }
        else
        {
            $data['row'] = $this->acao_vendas_model->carrega(intval($cd_acao_vendas));
        }        

        $this->load->view('planos/acao_vendas/cadastro', $data);    
    }

    public function salvar()
    {
        $this->load->model('projetos/acao_vendas_model');

        $cd_acao_vendas = $this->input->post('cd_acao_vendas', TRUE);
 
        $args = array( 
            'cd_usuario_responsavel' => $this->input->post('cd_usuario_responsavel', TRUE),
            'ds_acao_vendas'         => $this->input->post('ds_acao_vendas', TRUE),
            'dt_acao_vendas'         => $this->input->post('dt_acao_vendas',TRUE).' '.$this->input->post('hr_acao_vendas', TRUE),
            'nr_contatos'            => $this->input->post('nr_contatos', TRUE),
            'nr_fechamento'          => $this->input->post('nr_fechamento', TRUE),
            'cd_usuario'             => $this->session->userdata('codigo'),
        );

        if(intval($cd_acao_vendas) == 0)
        {
            $cd_acao_vendas = $this->acao_vendas_model->salvar($args);
        }
        else
        {
            $this->acao_vendas_model->atualizar($cd_acao_vendas, $args);
        }

        redirect('planos/acao_vendas', 'refresh');
    }
}    