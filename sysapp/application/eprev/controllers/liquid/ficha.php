<?php
class Ficha extends Controller
{
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
            $this->load->model('liquid/ficha_model');
            
            $data['gerencia'] = $this->ficha_model->get_gerencia();

            $this->load->view('liquid/ficha/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        $this->load->model('liquid/ficha_model');

        $args['cd_gerencia'] = $this->input->post('cd_gerencia', TRUE);
                
        manter_filtros($args);

        $data['collection'] = $this->ficha_model->listar($args);

        foreach($data['collection'] as $key => $item)
        {
            $gerencia = $this->ficha_model->get_ficha_gerencia($item['cd_ficha']);
                
            $data['collection'][$key]['gerencia'] = array();

            foreach($gerencia as $item2)
            {               
                $data['collection'][$key]['gerencia'][] = $item2['cd_gerencia'];
            }       
        }

        $this->load->view('liquid/ficha/index_result', $data);
    }

    public function cadastro($cd_ficha = 0)
    {
        if($this->get_permissao())
        {
            $this->load->model('liquid/ficha_model');

            $data['gerencia'] = $this->ficha_model->get_gerencia();

            if(intval($cd_ficha) == 0)
            {
                $data['row'] = array(
                    'cd_ficha'    => intval($cd_ficha),
                    'nr_ficha'    => '',
                    'ds_ficha'    => '',
                    'ds_caminho'  => ''
                );

                $data['ficha_gerencia'] = array();
            }
            else
            {
                $this->load->model('liquid/ficha_model');

                $data['row'] = $this->ficha_model->carrega($cd_ficha);

                $ficha_gerencia = $this->ficha_model->get_ficha_gerencia($cd_ficha);

                foreach($ficha_gerencia as $item)
                {
                    $data['ficha_gerencia'][] = $item['cd_gerencia'];
                }
            }          
            
            $this->load->view('liquid/ficha/cadastro', $data);
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
            $this->load->model('liquid/ficha_model');

            $cd_ficha = $this->input->post('cd_ficha', TRUE);

            $args = array(
                'cd_ficha'   => intval($cd_ficha),
                'nr_ficha'   => $this->input->post('nr_ficha', TRUE),
                'ds_ficha'   => $this->input->post('ds_ficha', TRUE),
                'ds_caminho' => $this->input->post('ds_caminho', TRUE),
                'cd_usuario' => $this->session->userdata('codigo')
            );

            $ficha_gerencia = $this->input->post('ficha_gerencia', TRUE);

            if(!is_array($ficha_gerencia))
            {
                $args['ficha_gerencia'] = array();
            }
            else
            {
                $args['ficha_gerencia'] = $ficha_gerencia;
            }

            if(intval($cd_ficha) == 0)
            {
                $this->ficha_model->salvar($args);
            } 
            else
            {
                $this->ficha_model->atualizar($cd_ficha, $args);
            }
            
            redirect('liquid/ficha', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
}