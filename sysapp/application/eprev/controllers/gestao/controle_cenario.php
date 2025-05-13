<?php
class Controle_cenario extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GC', 'GFC')))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function index()
    {
        if($this->get_permissao())
        {
            $this->load->model('projetos/controle_cenario_model');

            $data['anos'] = $this->controle_cenario_model->get_ano();

            $this->load->view('gestao/controle_cenario/index', $data);
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
            ini_set('max_execution_time', 0);
            
            $this->load->model('projetos/controle_cenario_model');

            $data['ar_atividade'] = array();

            $args = array( 
                'mes'             => $this->input->post('mes', TRUE),
                'ano'             => $this->input->post('ano', TRUE),
                'dt_inclusao_ini' => $this->input->post('dt_inclusao_ini', TRUE),
                'dt_inclusao_fim' => $this->input->post('dt_inclusao_fim', TRUE)
            );

            manter_filtros($args);

            $data['registro'] = $this->controle_cenario_model->lista_registro($args);

            foreach($data['registro'] as $key => $item)
            {
                $data['registro'][$key]['atividade'] = $this->controle_cenario_model->lista_atividades($item['cd_cenario']);
            }

            $this->load->view('gestao/controle_cenario/index_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    public function sem_data()
    {
        if($this->get_permissao())
        {
            $this->load->view('gestao/controle_cenario/sem_data');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function atrasada()
    {
        if($this->get_permissao())
        {
            ini_set('max_execution_time', 0);
            
            $this->load->model('projetos/controle_cenario_model');

            $args = array();

            $data['collection'] = $this->controle_cenario_model->lista_atrasada($args);

            $this->load->view('gestao/controle_cenario/atrasada', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
}

?>