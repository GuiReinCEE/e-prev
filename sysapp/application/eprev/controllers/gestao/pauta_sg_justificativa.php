<?php
class Pauta_sg_justificativa extends Controller
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
            $this->load->view('gestao/pauta_sg_justificativa/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	$this->load->model('gestao/pauta_sg_justificativa_model');

        $args = array(
            'ds_pauta_sg_justificativa' => $this->input->post('ds_pauta_sg_justificativa', TRUE)
        );

        manter_filtros($args); 

    	$data['collection'] = $this->pauta_sg_justificativa_model->listar($args);

    	$this->load->view('gestao/pauta_sg_justificativa/index_result', $data);
    }

    public function cadastro($cd_pauta_sg_justificativa = 0)
    {
        if($this->get_permissao())
        {
            if(intval($cd_pauta_sg_justificativa) == 0)
            {
                $data['row'] = array(
                    'cd_pauta_sg_justificativa' => intval($cd_pauta_sg_justificativa),
                    'ds_pauta_sg_justificativa' => ''     
                );
            }
            else
            {
                $this->load->model('gestao/pauta_sg_justificativa_model');

                $data['row'] = $this->pauta_sg_justificativa_model->carrega($cd_pauta_sg_justificativa);
            }

            $this->load->view('gestao/pauta_sg_justificativa/cadastro', $data);
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
            $this->load->model('gestao/pauta_sg_justificativa_model');

            $cd_pauta_sg_justificativa = $this->input->post('cd_pauta_sg_justificativa', TRUE);

            $args = array(
                'cd_pauta_sg_justificativa' => $this->input->post('cd_pauta_sg_justificativa', TRUE),
                'ds_pauta_sg_justificativa' => $this->input->post('ds_pauta_sg_justificativa', TRUE),
                'cd_usuario'                   => $this->session->userdata('codigo')
            );

            if(intval($cd_pauta_sg_justificativa) == 0)
            {
                $this->pauta_sg_justificativa_model->salvar($args);
            }
            else
            {
                $this->pauta_sg_justificativa_model->atualizar($cd_pauta_sg_justificativa, $args);
            }

            redirect('gestao/pauta_sg_justificativa', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_pauta_sg_justificativa)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/pauta_sg_justificativa_model');

            $this->pauta_sg_justificativa_model->excluir($cd_pauta_sg_justificativa, $this->session->userdata('codigo'));

            redirect('gestao/pauta_sg_justificativa', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}