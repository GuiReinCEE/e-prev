<?php
class Rh_grupo extends Controller
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
            $this->load->view('cadastro/rh_grupo/index');
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
            $this->load->model('rh_avaliacao/grupo_model');

            $args = array();
    
            manter_filtros($args);
    
            $data['collection'] = $this->grupo_model->listar($args);
    
            $this->load->view('cadastro/rh_grupo/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_grupo = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/grupo_model');

            if(intval($cd_grupo) == 0)
            {
                $data['row'] = array(
                    'cd_grupo'       => intval($cd_grupo),
                    'ds_grupo'       => '',
                    'ds_grupo_sigla' => ''
                );
            }
            else
            {
                $data['row'] = $this->grupo_model->carrega($cd_grupo);
            }
    
            $this->load->view('cadastro/rh_grupo/cadastro', $data);
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
            $this->load->model('rh_avaliacao/grupo_model');

            $cd_grupo = $this->input->post('cd_grupo', TRUE);        
    
            $args = array(
                'ds_grupo'       => $this->input->post('ds_grupo', TRUE),
                'ds_grupo_sigla' => $this->input->post('ds_grupo_sigla', TRUE),
                'cd_usuario'     => $this->session->userdata('codigo')
            );
    
            if(intval($cd_grupo) == 0)
            {
                $this->grupo_model->salvar($args);
            }
            else
            {
                $this->grupo_model->atualizar($cd_grupo, $args);
            }
    
            redirect('cadastro/rh_grupo', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}