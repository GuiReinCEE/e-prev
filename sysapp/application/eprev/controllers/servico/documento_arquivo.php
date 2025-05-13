<?php
class Documento_arquivo extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GTI', 'GCM')))
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
            $this->load->view('servico/documento_arquivo/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/documento_arquivo_model');

            $args = array();

            manter_filtros($args);

            $data['collection'] = $this->documento_arquivo_model->listar($args);

            $this->load->view('servico/documento_arquivo/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_documento_arquivo = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/documento_arquivo_model');

            if(intval($cd_documento_arquivo) == 0)
            {
                $data['row'] = array(
                    'cd_documento_arquivo' => '',
                    'arquivo'              => '',
                    'arquivo_nome'         => '',
                    'ds_documento_arquivo' => '',
                    'ds_usuario_inclusao'  => '',
                    'dt_inclusao'          => ''
                );
            }
            else
            {
                $data['row'] = $this->documento_arquivo_model->carrega($cd_documento_arquivo);
            }

            $this->load->view('servico/documento_arquivo/cadastro', $data);
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
            $this->load->model('autoatendimento/documento_arquivo_model');

            $cd_documento_arquivo = $this->input->post('cd_documento_arquivo', TRUE);

            $args = array(
                'arquivo'              => $this->input->post('arquivo', TRUE),
                'arquivo_nome'         => $this->input->post('arquivo_nome', TRUE),
                'ds_documento_arquivo' => $this->input->post('ds_documento_arquivo', TRUE),
                'cd_usuario'           => $this->session->userdata('codigo')
            );

            if(intval($cd_documento_arquivo) == 0)
            {
                $this->documento_arquivo_model->salvar($args);
            }
            else
            {
                $this->documento_arquivo_model->atualizar($cd_documento_arquivo, $args);
            }

            redirect('servico/documento_arquivo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_documento_arquivo)
    {
        if($this->get_permissao())
        {
            $this->load->model('autoatendimento/documento_arquivo_model');

            $this->documento_arquivo_model->excluir($cd_documento_arquivo, $this->session->userdata('codigo'));

            redirect('servico/documento_arquivo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}