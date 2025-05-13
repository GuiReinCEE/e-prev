<?php
class Rh_pergunta extends Controller
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
            $this->load->model('rh_avaliacao/pergunta_model');

            $data['bloco'] = $this->pergunta_model->get_bloco();

            $this->load->view('cadastro/rh_pergunta/index', $data);
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
            $this->load->model('rh_avaliacao/pergunta_model');

            $args = array(
                'cd_bloco' => $this->input->post('cd_bloco', TRUE)
            );
    
            manter_filtros($args);
    
            $data['collection'] = $this->pergunta_model->listar($args);

            foreach ($data['collection'] as $key => $item) 
            {
                $data['collection'][$key]['classe'] = array();

                foreach ($this->pergunta_model->listar_classes($item['cd_pergunta']) as $key2 => $item2)
                {
                    $data['collection'][$key]['classe'][] = $item2['ds_classe'];
                }       
            }
    
            $this->load->view('cadastro/rh_pergunta/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_pergunta = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('rh_avaliacao/pergunta_model');

            $data = array(
                'bloco'  => $this->pergunta_model->get_bloco(),
                'classe' => $this->pergunta_model->get_classe()
            );

            if(intval($cd_pergunta) == 0)
            {
                $data['row'] = array(
                    'cd_pergunta' => intval($cd_pergunta),
                    'cd_bloco'    => '',
                    'ds_pergunta' => ''
                );

                $data['pergunta_classe'] = array();
            }
            else
            {
                $data['row']     = $this->pergunta_model->carrega($cd_pergunta);

                $data['pergunta_classe'] = array();

                foreach ($this->pergunta_model->listar_classes($cd_pergunta) as $key => $item)
                {
                    $data['pergunta_classe'][] = $item['cd_classe'];
                }
            }
    
            $this->load->view('cadastro/rh_pergunta/cadastro', $data);
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
            $this->load->model('rh_avaliacao/pergunta_model');

            $cd_pergunta = $this->input->post('cd_pergunta', TRUE);
    
            $args = array(
                'cd_bloco'    => $this->input->post('cd_bloco', TRUE),
                'ds_pergunta' => $this->input->post('ds_pergunta', TRUE),
                'classe'      => (is_array($this->input->post('classe', TRUE)) ? $this->input->post('classe', TRUE) : array()),
                'cd_usuario'  => $this->session->userdata('codigo')
            );
    
            if(intval($cd_pergunta) == 0)
            {
                $cd_pergunta = $this->pergunta_model->salvar($args);
            }
            else
            {
                $this->pergunta_model->atualizar($cd_pergunta, $args);
            }
    
            redirect('cadastro/rh_pergunta', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}