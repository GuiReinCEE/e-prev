<?php
class Pendencia_query extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index()
    {
        $this->load->model('gestao/pendencia_query_model');

        $data['pendencia_minha'] = $this->pendencia_query_model->get_pendencia_minha();

        $this->load->view('servico/pendencia_query/index', $data);
    }

    public function listar()
    {
        $this->load->model('gestao/pendencia_query_model');

        $args = array(
            'cd_pendencia_minha' => $this->input->post('cd_pendencia_minha', TRUE),
            'fl_superior'        => $this->input->post('fl_superior',TRUE)
         );

        manter_filtros($args);

        $data['collection'] = $this->pendencia_query_model->listar($args);

        $this->load->view('servico/pendencia_query/index_result', $data);
    }

    public function cadastro($cd_pendencia_minha_query = 0)
    {
        $this->load->model('gestao/pendencia_query_model');

        $data['pendencia_minha']  = $this->pendencia_query_model->get_pendencia_minha();
       
        if(intval($cd_pendencia_minha_query) == 0)
        {
            $data['row'] = array(
                'cd_pendencia_minha_query' => intval($cd_pendencia_minha_query),
                'cd_pendencia_minha'       => '',
                'ds_descricao'             => '',
                'ds_pendencia_minha_query' => '',
                'fl_superior'              => ''
            );
        }
        else
        {
            $data['row'] = $this->pendencia_query_model->carrega(intval($cd_pendencia_minha_query));
        }        

        $this->load->view('servico/pendencia_query/cadastro', $data);
    }

    public function salvar()
    {
        $this->load->model('gestao/pendencia_query_model');

        $cd_pendencia_minha_query = $this->input->post('cd_pendencia_minha_query', TRUE);

        $args = array(
            'cd_pendencia_minha_query' => $this->input->post('cd_pendencia_minha_query', TRUE),
            'cd_pendencia_minha'       => $this->input->post('cd_pendencia_minha', TRUE),
            'ds_pendencia_minha_query' => $this->input->post('ds_pendencia_minha_query', TRUE),
            'ds_descricao'             => $this->input->post('ds_descricao', TRUE),
            'fl_superior'              => $this->input->post('fl_superior', TRUE),
            'cd_usuario'               => $this->session->userdata('codigo')
        );

        if(intval($cd_pendencia_minha_query) == 0)
        {
            $cd_pendencia_minha_query = $this->pendencia_query_model->salvar($args);
        }
        else
        {
            $this->pendencia_query_model->atualizar($cd_pendencia_minha_query, $args);
        }

        redirect('servico/pendencia_query', 'refresh');
    }
}
?>