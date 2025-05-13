<?php
class Reuniao_sistema_gestao_tipo extends Controller {

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
		$this->load->view('gestao/reuniao_sistema_gestao_tipo/index');
    }

    public function listar()
    {
		$this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

    	$args = array(
            'ds_reuniao_sistema_gestao_tipo' => $this->input->post('ds_reuniao_sistema_gestao_tipo', TRUE)
        );
				
		manter_filtros($args);

		$data['collection'] = $this->reuniao_sistema_gestao_tipo_model->listar($args);

        foreach($data['collection'] as $key => $item)
        {
            $processo = $this->reuniao_sistema_gestao_tipo_model->get_processo_checked(
                $item['cd_reuniao_sistema_gestao_tipo']
            );
                
            $data['collection'][$key]['processo'] = array();

            foreach($processo as $item2)
            {               
                $data['collection'][$key]['processo'][] = $item2['processo'];
            }       
        }

        $this->load->view('gestao/reuniao_sistema_gestao_tipo/index_result', $data);
    }

    public function cadastro($cd_reuniao_sistema_gestao_tipo = 0)
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        $data['processo'] = $this->reuniao_sistema_gestao_tipo_model->get_processo();

        $data['processo_checked'] = array();

        if(intval($cd_reuniao_sistema_gestao_tipo) == 0)
        {
            $data['row'] = array(
                'cd_reuniao_sistema_gestao_tipo' => intval($cd_reuniao_sistema_gestao_tipo),
                'ds_reuniao_sistema_gestao_tipo' => ''
            );
        }
        else
        {
            $data['row'] = $this->reuniao_sistema_gestao_tipo_model->carrega($cd_reuniao_sistema_gestao_tipo);

            $processo = $this->reuniao_sistema_gestao_tipo_model->get_processo_checked($cd_reuniao_sistema_gestao_tipo);
                
            foreach($processo as $item)
            {               
                $data['processo_checked'][] = $item['cd_processo'];
            }       
        }

        $this->load->view('gestao/reuniao_sistema_gestao_tipo/cadastro', $data);
    }

    public function salvar()
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        $cd_reuniao_sistema_gestao_tipo = $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE);

        $args = array(
            'cd_reuniao_sistema_gestao_tipo' => $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE),
            'ds_reuniao_sistema_gestao_tipo' => $this->input->post('ds_reuniao_sistema_gestao_tipo', TRUE),
            'cd_usuario'                     => $this->session->userdata('codigo')
        );

        $args['processo_checked'] = $this->input->post('processo_checked', TRUE);

        if(!is_array($args['processo_checked']))
        {
            $args['processo_checked'] = array();
        }

        if(intval($cd_reuniao_sistema_gestao_tipo) == 0)
        {
            $cd_reuniao_sistema_gestao_tipo = $this->reuniao_sistema_gestao_tipo_model->salvar($args);

            redirect('gestao/reuniao_sistema_gestao_tipo/indicador/'.$cd_reuniao_sistema_gestao_tipo, 'refresh');
        } 
        else
        {
            $this->reuniao_sistema_gestao_tipo_model->atualizar(intval($cd_reuniao_sistema_gestao_tipo), $args);

            redirect('gestao/reuniao_sistema_gestao_tipo/cadastro/'.$cd_reuniao_sistema_gestao_tipo, 'refresh');
        }
    }

    public function cadastro_ordem($cd_reuniao_sistema_gestao_tipo)
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        $data['processo'] = $this->reuniao_sistema_gestao_tipo_model->get_processo_checked(
            $cd_reuniao_sistema_gestao_tipo
        );

        $data['row'] = $this->reuniao_sistema_gestao_tipo_model->carrega($cd_reuniao_sistema_gestao_tipo);
                    
        $this->load->view('gestao/reuniao_sistema_gestao_tipo/cadastro_ordem', $data);
    }

    public function salvar_cadastro_ordem()
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        $cd_usuario = $this->session->userdata('codigo');
            
        $cd_reuniao_sistema_gestao_tipo = $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE);

        $data['processo'] = $this->reuniao_sistema_gestao_tipo_model->get_processo_checked(
            $cd_reuniao_sistema_gestao_tipo
        );
  
        foreach($data['processo'] as $item)
        {
            $args = array(
                'nr_ordem'    => $this->input->post($item['cd_processo'], TRUE),
                'cd_processo' => $this->input->post('processo_'.$item['cd_processo'], TRUE)
            );
             
            $this->reuniao_sistema_gestao_tipo_model->salvar_cadastro_ordem(
                $cd_reuniao_sistema_gestao_tipo, 
                $cd_usuario, 
                $args
            );
        }
       
        redirect('gestao/reuniao_sistema_gestao_tipo/cadastro_ordem/'.intval($cd_reuniao_sistema_gestao_tipo), 'refresh');
    }

    public function indicador($cd_reuniao_sistema_gestao_tipo)
    {
        
            $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

            $data['row'] = $this->reuniao_sistema_gestao_tipo_model->carrega(intval($cd_reuniao_sistema_gestao_tipo));

            $processo = $this->reuniao_sistema_gestao_tipo_model->get_processo_checked($cd_reuniao_sistema_gestao_tipo);

            $data['indicador']       = array();
            $data['indicador_check'] = array();

            foreach ($processo as $key => $processo_item) 
            {
                $indicador = $this->reuniao_sistema_gestao_tipo_model->get_indicador($processo_item['cd_processo']);

                $indicador_check = $this->reuniao_sistema_gestao_tipo_model->get_indicador_checked(intval($processo_item['cd_processo']), $cd_reuniao_sistema_gestao_tipo);

                foreach ($indicador as $key => $indicador_item) 
                {
                    $data['indicador'][] = $indicador_item;
                }

                foreach ($indicador_check as $key2 => $indicador_check_item) 
                {
                    $data['indicador_check'][] = $indicador_check_item['cd_indicador'];
                }
            }

            $this->load->view('gestao/reuniao_sistema_gestao_tipo/indicador', $data);
    }

    public function indicador_salvar()
    {
        $this->load->model('gestao/reuniao_sistema_gestao_tipo_model');

        $cd_reuniao_sistema_gestao_tipo = $this->input->post('cd_reuniao_sistema_gestao_tipo', TRUE);

        $args['cd_usuario'] = $this->session->userdata('codigo');
        
        $indicador_checked = $this->input->post('indicador_checked', TRUE);

        if(!is_array($indicador_checked))
        {
            $args['indicador_checked'] = array();
        }
        else
        {
            $args['indicador_checked'] = $indicador_checked;
        }

        $this->reuniao_sistema_gestao_tipo_model->atualizar_indicador($cd_reuniao_sistema_gestao_tipo, $args);

        redirect('gestao/reuniao_sistema_gestao_tipo/indicador/'.$cd_reuniao_sistema_gestao_tipo);
    }
}   