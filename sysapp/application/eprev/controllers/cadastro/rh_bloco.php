<?php
class Rh_bloco extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    private function get_permissao()
    {
        if($this->session->userdata('indic_09') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
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
            $this->load->model('rh_avaliacao/bloco_model');

            $data['grupo'] = $this->bloco_model->get_grupo();

            $this->load->view('cadastro/rh_bloco/index', $data);
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
            $this->load->model('rh_avaliacao/bloco_model');

            $args = array(
                'cd_grupo' => $this->input->post('cd_grupo', TRUE)
            );
    
            manter_filtros($args);
    
            $data['collection'] = $this->bloco_model->listar($args);
    
            $this->load->view('cadastro/rh_bloco/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_bloco = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/bloco_model');

            $data['grupo'] = $this->bloco_model->get_grupo(); 

            if(intval($cd_bloco) == 0)
            {
                $data['row'] = array(
                    'cd_bloco'           => intval($cd_bloco),
                    'cd_grupo'           => '',
                    'ds_bloco'           => '',
                    'ds_bloco_descricao' => '',
                    'fl_conhecimento'    => ''
                );
            }
            else
            {
                $data['row'] = $this->bloco_model->carrega($cd_bloco);
            }
    
            $this->load->view('cadastro/rh_bloco/cadastro', $data);
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
            $this->load->model('rh_avaliacao/bloco_model');

            $cd_bloco = $this->input->post('cd_bloco', TRUE);        
    
            $args = array(
                'cd_grupo'           => $this->input->post('cd_grupo', TRUE),
                'ds_bloco'           => $this->input->post('ds_bloco', TRUE),
                'ds_bloco_descricao' => $this->input->post('ds_bloco_descricao', TRUE),
                'fl_conhecimento'    => $this->input->post('fl_conhecimento', TRUE),
                'cd_usuario'         => $this->session->userdata('codigo')
            );
    
            if(intval($cd_bloco) == 0)
            {
                $this->bloco_model->salvar($args);
            }
            else
            {
                $this->bloco_model->atualizar($cd_bloco, $args);
            }
    
            redirect('cadastro/rh_bloco', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}