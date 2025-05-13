<?php
class Solic_fiscalizacao_audit_origem extends Controller
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
            $this->load->view('atividade/solic_fiscalizacao_audit_origem/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	$this->load->model('projetos/solic_fiscalizacao_audit_origem_model');

        $args = array(
            'ds_solic_fiscalizacao_audit_origem' => $this->input->post('ds_solic_fiscalizacao_audit_origem', TRUE),
            'fl_especificar'                     => $this->input->post('fl_especificar', TRUE)
        );

        manter_filtros($args); 

    	$data['collection'] = $this->solic_fiscalizacao_audit_origem_model->listar($args);

    	$this->load->view('atividade/solic_fiscalizacao_audit_origem/index_result', $data);
    }

    public function cadastro($cd_solic_fiscalizacao_audit_origem = 0)
    {
        if($this->get_permissao())
        {
            if(intval($cd_solic_fiscalizacao_audit_origem) == 0)
            {
                $data['row'] = array(
                    'cd_solic_fiscalizacao_audit_origem' => intval($cd_solic_fiscalizacao_audit_origem),
                    'ds_solic_fiscalizacao_audit_origem' => '',     
                    'fl_especificar'                     => 'N'
                );
            }
            else
            {
                $this->load->model('projetos/solic_fiscalizacao_audit_origem_model');

                $data['row'] = $this->solic_fiscalizacao_audit_origem_model->carrega($cd_solic_fiscalizacao_audit_origem);
            }

            $this->load->view('atividade/solic_fiscalizacao_audit_origem/cadastro', $data);
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
            $this->load->model('projetos/solic_fiscalizacao_audit_origem_model');

            $cd_solic_fiscalizacao_audit_origem = $this->input->post('cd_solic_fiscalizacao_audit_origem', TRUE);

            $args = array(
                'cd_solic_fiscalizacao_audit_origem' => $this->input->post('cd_solic_fiscalizacao_audit_origem', TRUE),
                'ds_solic_fiscalizacao_audit_origem' => $this->input->post('ds_solic_fiscalizacao_audit_origem', TRUE),
                'fl_especificar'                     => $this->input->post('fl_especificar', TRUE), 
                'cd_usuario'                         => $this->session->userdata('codigo')
            );

            if(intval($cd_solic_fiscalizacao_audit_origem) == 0)
            {
                $this->solic_fiscalizacao_audit_origem_model->salvar($args);
            }
            else
            {
                $this->solic_fiscalizacao_audit_origem_model->atualizar($cd_solic_fiscalizacao_audit_origem, $args);
            }

            redirect('atividade/solic_fiscalizacao_audit_origem', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_solic_fiscalizacao_audit_origem)
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/solic_fiscalizacao_audit_origem_model');

            $this->solic_fiscalizacao_audit_origem_model->excluir(
                $cd_solic_fiscalizacao_audit_origem, 
                $this->session->userdata('codigo')
            );

            redirect('atividade/solic_fiscalizacao_audit_origem', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}