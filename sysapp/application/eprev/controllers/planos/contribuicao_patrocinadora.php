<?php
class Contribuicao_patrocinadora extends Controller 
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
            $this->load->view('planos/contribuicao_patrocinadora/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('autoatendimento/contribuicao_patroc_model');

        $args = array(
            'cd_empresa'            => $this->input->post('cd_empresa', TRUE),
            'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
            'seq_dependencia'       => $this->input->post('seq_dependencia', TRUE),
            'dt_solicitacao_ini'    => $this->input->post('dt_solicitacao_ini', TRUE),
            'dt_solicitacao_fim'    => $this->input->post('dt_solicitacao_fim', TRUE)
        );
                
        manter_filtros($args);
        
        $data['collection'] = $this->contribuicao_patroc_model->listar($args);

        $this->load->view('planos/contribuicao_patrocinadora/index_result', $data);
    }

    public function acompanhamento($cd_contribuicao_patroc)
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/contribuicao_patroc_model');

            $data = array(
                'row'        => $this->contribuicao_patroc_model->carrega($cd_contribuicao_patroc),
                'collection' => $this->contribuicao_patroc_model->listar_acompanhamento($cd_contribuicao_patroc)
            );

            $this->load->view('planos/contribuicao_patrocinadora/acompanhamento', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function salvar_acompanhamento()
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/contribuicao_patroc_model');

            $cd_contribuicao_patroc = $this->input->post('cd_contribuicao_patroc', TRUE);
            
            $args = array(
                'cd_contribuicao_patroc' => $cd_contribuicao_patroc,
                'ds_descricao'           => $this->input->post('ds_descricao', TRUE),
                'cd_usuario'             => $this->session->userdata('codigo')
            ); 

            $this->contribuicao_patroc_model->salvar_acompanhamento($args); 
             
            redirect('planos/contribuicao_patrocinadora/acompanhamento/'.$cd_contribuicao_patroc, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    } 

    public function excluir_acompanhamento($cd_contribuicao_patroc, $cd_contribuicao_patroc_acompanhamento)
    {
        $this->load->model('autoatendimento/contribuicao_patroc_model');

        $this->contribuicao_patroc_model->excluir_acompanhamento($cd_contribuicao_patroc_acompanhamento, $this->session->userdata('codigo'));

        redirect('planos/contribuicao_patrocinadora/acompanhamento/'.$cd_contribuicao_patroc, 'refresh');
    }
}
?>
