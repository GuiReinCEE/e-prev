<?php
class Avaliacao_treinamento_replica extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index()
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $data = array(
            'tipo' => $this->treinamento_colaborador_item_replica_model->get_tipo()
        );

        $this->load->view('servico/avaliacao_treinamento_replica/index', $data);
    }

    public function listar()
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $args = array(
    		'numero'                          => $this->input->post('numero', TRUE),
    		'ano'                             => $this->input->post('ano', TRUE),
    		'nome'                            => $this->input->post('nome', TRUE),
    		'dt_inicio_ini'                   => $this->input->post('dt_inicio_ini', TRUE),
    		'dt_inicio_fim'                   => $this->input->post('dt_inicio_fim', TRUE),
    		'dt_final_ini'                    => $this->input->post('dt_final_ini', TRUE),
    		'dt_final_fim'                    => $this->input->post('dt_final_fim', TRUE),
            'cd_treinamento_colaborador_tipo' => $this->input->post('cd_treinamento_colaborador_tipo', TRUE)
    	);
        
        manter_filtros($args); 

        $data['collection'] = $this->treinamento_colaborador_item_replica_model->listar($this->session->userdata('cd_registro_empregado'), $args);

        $this->load->view('servico/avaliacao_treinamento_replica/index_result', $data);
    }

    public function cadastro($cd_treinamento_colaborador_item = 0)
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $data = array(
            'cd_treinamento_colaborador_item' => $cd_treinamento_colaborador_item,
            'row'                             => $this->treinamento_colaborador_item_replica_model->carrega($cd_treinamento_colaborador_item)
        );

        $this->load->view('servico/avaliacao_treinamento_replica/cadastro', $data);
    }

    public function salvar()
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $cd_treinamento_colaborador_item = $this->input->post('cd_treinamento_colaborador_item', TRUE);

        $args = array(
            'cd_treinamento_colaborador_item_replica' => $this->input->post('cd_treinamento_colaborador_item_replica', TRUE),
            'fl_aplica_replica'                       => $this->input->post('fl_aplica_replica', TRUE),
            'ds_justificativa'                        => $this->input->post('ds_justificativa', TRUE),
            'dt_limite'                               => $this->input->post('dt_limite', TRUE),
            'cd_usuario'                              => $this->session->userdata('codigo')
        );

        $this->treinamento_colaborador_item_replica_model->salvar($args);

        redirect('servico/avaliacao_treinamento_replica/cadastro/'.$cd_treinamento_colaborador_item, 'refresh');
    }

    public function finalizar($cd_treinamento_colaborador_item_replica)
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $this->treinamento_colaborador_item_replica_model->finalizar($cd_treinamento_colaborador_item_replica, $this->session->userdata('codigo'));

        redirect('servico/avaliacao_treinamento_replica/index', 'refresh');
    }

    public function acompanhamento($cd_treinamento_colaborador_item, $cd_treinamento_colaborador_item_replica, $cd_treinamento_colaborador_item_replica_acompanhamento = 0)
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $data = array(
            'cd_treinamento_colaborador_item'         => $cd_treinamento_colaborador_item,
            'cd_treinamento_colaborador_item_replica' => $cd_treinamento_colaborador_item_replica,
            'row'                                     => $this->treinamento_colaborador_item_replica_model->carrega($cd_treinamento_colaborador_item),
            'collection'                              => $this->treinamento_colaborador_item_replica_model->lista_acompanhamento($cd_treinamento_colaborador_item_replica),
            'verifica_acompanhamento'                 => $this->treinamento_colaborador_item_replica_model->verifica_acompanhamento($cd_treinamento_colaborador_item_replica)
        );

        if(intval($cd_treinamento_colaborador_item_replica_acompanhamento) == 0)
        {
            $data['row_acompanhamento'] = array(
                'cd_treinamento_colaborador_item_replica_acompanhamento' => '',
                'ds_acompanhamento'                                      => ''
            );
        }
        else
        {
            $data['row_acompanhamento'] = $this->treinamento_colaborador_item_replica_model->carrega_acompanhamento($cd_treinamento_colaborador_item_replica_acompanhamento);
        }

        $this->load->view('servico/avaliacao_treinamento_replica/acompanhamento', $data);
    }

    public function salvar_acompanhamento()
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $cd_treinamento_colaborador_item_replica_acompanhamento = $this->input->post('cd_treinamento_colaborador_item_replica_acompanhamento', TRUE);
        $cd_treinamento_colaborador_item                        = $this->input->post('cd_treinamento_colaborador_item', TRUE);
        $cd_treinamento_colaborador_item_replica                = $this->input->post('cd_treinamento_colaborador_item_replica', TRUE);

        $args = array(
            'cd_treinamento_colaborador_item_replica' => $this->input->post('cd_treinamento_colaborador_item_replica', TRUE), 
            'ds_acompanhamento'                       => $this->input->post('ds_acompanhamento', TRUE),
            'cd_usuario'                              => $this->session->userdata('codigo') 
        );

        if(intval($cd_treinamento_colaborador_item_replica_acompanhamento) == 0)
        {
            $this->treinamento_colaborador_item_replica_model->salvar_acompanhamento($args);
        }
        else
        {
            $this->treinamento_colaborador_item_replica_model->atualizar_acompanhamento($cd_treinamento_colaborador_item_replica_acompanhamento, $args);
        }

        redirect('servico/avaliacao_treinamento_replica/acompanhamento/'.$cd_treinamento_colaborador_item.'/'.$cd_treinamento_colaborador_item_replica, 'refresh');
    }

    public function excluir($cd_treinamento_colaborador_item, $cd_treinamento_colaborador_item_replica, $cd_treinamento_colaborador_item_replica_acompanhamento)
    {
        $this->load->model('projetos/treinamento_colaborador_item_replica_model');

        $this->treinamento_colaborador_item_replica_model->excluir($cd_treinamento_colaborador_item_replica_acompanhamento, $this->session->userdata('codigo'));

        redirect('servico/avaliacao_treinamento_replica/acompanhamento/'.$cd_treinamento_colaborador_item.'/'.$cd_treinamento_colaborador_item_replica, 'refresh');
    }
}