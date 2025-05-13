<?php
class Pauta_sg_objetivo extends Controller
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
            $this->load->view('gestao/pauta_sg_objetivo/index');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	$this->load->model('gestao/pauta_sg_objetivo_model');

        $args = array(
            'cd_pauta_sg_objetivo' => $this->input->post('cd_pauta_sg_objetivo', TRUE),
            'ds_pauta_sg_objetivo' => $this->input->post('ds_pauta_sg_objetivo', TRUE),
            'fl_anexo_obrigatorio' => $this->input->post('fl_anexo_obrigatorio', TRUE)
        );

        manter_filtros($args); 

    	$data['collection'] = $this->pauta_sg_objetivo_model->listar($args);

    	$this->load->view('gestao/pauta_sg_objetivo/index_result', $data);
    }

    public function cadastro($cd_pauta_sg_objetivo = 0)
    {
        if($this->get_permissao())
        {
            if(intval($cd_pauta_sg_objetivo) == 0)
            {
                $data['row'] = array(
                    'cd_pauta_sg_objetivo' => intval($cd_pauta_sg_objetivo),
                    'ds_pauta_sg_objetivo' => '',     
                    'fl_anexo_obrigatorio' => ''
                );
            }
            else
            {
                $this->load->model('gestao/pauta_sg_objetivo_model');

                $data['row'] = $this->pauta_sg_objetivo_model->carrega($cd_pauta_sg_objetivo);
            }

            $this->load->view('gestao/pauta_sg_objetivo/cadastro', $data);
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
            $this->load->model('gestao/pauta_sg_objetivo_model');

            $cd_pauta_sg_objetivo = $this->input->post('cd_pauta_sg_objetivo', TRUE);

            $args = array(
                'cd_pauta_sg_objetivo' => $this->input->post('cd_pauta_sg_objetivo', TRUE),
                'ds_pauta_sg_objetivo' => $this->input->post('ds_pauta_sg_objetivo', TRUE),
                'fl_anexo_obrigatorio' => $this->input->post('fl_anexo_obrigatorio', TRUE), 
                'cd_usuario'           => $this->session->userdata('codigo')
            );

            if(intval($cd_pauta_sg_objetivo) == 0)
            {
                $this->pauta_sg_objetivo_model->salvar($args);
            }
            else
            {
                $this->pauta_sg_objetivo_model->atualizar($cd_pauta_sg_objetivo, $args);
            }
            
            redirect('gestao/pauta_sg_objetivo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function excluir($cd_pauta_sg_objetivo)
    {
        if($this->get_permissao())
        {
            $this->load->model('gestao/pauta_sg_objetivo_model');

            $this->pauta_sg_objetivo_model->excluir($cd_pauta_sg_objetivo, $this->session->userdata('codigo'));

            redirect('gestao/pauta_sg_objetivo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}