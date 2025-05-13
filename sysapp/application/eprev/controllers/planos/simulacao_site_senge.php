<?php
class Simulacao_site_senge extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
    }

    public function index()
    {
        $this->load->view('planos/simulacao_site_senge/index');
    }

    public function listar()
    {
        $this->load->model('projetos/simulacao_site_model');   

        $args = array(
            'nome'         => $this->input->post('nome', TRUE),
            'fl_simulacao' => $this->input->post('fl_simulacao', TRUE)
        );

        manter_filtros($args);

        $data['collection'] = $this->simulacao_site_model->listar($args);
       
        $this->load->view('planos/simulacao_site_senge/index_result', $data);
    }

    public function simulacao($cd_simulacao_site)
    {
        $this->load->model('projetos/simulacao_site_model');

        $data['row'] = $this->simulacao_site_model->carrega($cd_simulacao_site);

        $view = $this->simulacao_site_model->simulacao($cd_simulacao_site);

        $dados = array();

        foreach ($view as $key => $item) 
        {
            $dados[$item['ds_linha']] = $item['ds_valor'];

            if(trim($item['ds_linha']) == 'EVOLUCAO')
            {
                $dados[$item['ds_linha']] = json_decode($item['ds_valor'], true);

                $data['collection'] = $dados['EVOLUCAO'];
            }
        }

        $data['dados'] = $dados;
       
       $this->load->view('planos/simulacao_site_senge/simulacao', $data);
    }

    public function cadastro($cd_simulacao_site, $cd_simulacao_site_acompanhamento = 0)
    {
        $this->load->model('projetos/simulacao_site_model');

        $data['row'] = $this->simulacao_site_model->carrega($cd_simulacao_site);

        $data['collection'] = $this->simulacao_site_model->listar_acompanhamento($cd_simulacao_site);

        if($cd_simulacao_site_acompanhamento == 0)
        {
           $data['acompanhamento'] = Array(
                'cd_simulacao_site_acompanhamento' => $this->input->post('cd_simulacao_site_acompanhamento', TRUE),
                'cd_simulacao_site'                => $this->input->post('cd_simulacao_site', TRUE),
                'ds_simulacao_site_acompanhamento' => ''
            ); 
        }
        else
        {
            $this->load->model('projetos/simulacao_site_model');

            $data['acompanhamento'] = $this->simulacao_site_model->carrega_acompanhamento($cd_simulacao_site_acompanhamento);
        }

        $this->load->view('planos/simulacao_site_senge/cadastro', $data);
    }

    public function salvar()
    {
        $this->load->model('projetos/simulacao_site_model');

        $cd_simulacao_site_acompanhamento = $this->input->post('cd_simulacao_site_acompanhamento', TRUE);

        $args = Array(
            'cd_simulacao_site'                => $this->input->post('cd_simulacao_site', TRUE),
            'ds_simulacao_site_acompanhamento' => $this->input->post('ds_simulacao_site_acompanhamento', TRUE),
            'cd_usuario'                       => $this->session->userdata('codigo')
        ); 

        if(intval($cd_simulacao_site_acompanhamento) == 0)
        {
            $this->simulacao_site_model->salvar($args);
        }
        else
        {
            $this->simulacao_site_model->atualizar(intval($cd_simulacao_site_acompanhamento), $args);
        }

        redirect('planos/simulacao_site_senge/cadastro/'.$args['cd_simulacao_site'], 'refresh');
    }

    public function excluir($cd_simulacao_site, $cd_simulacao_site_acompanhamento)
    {
        $this->load->model('projetos/simulacao_site_model');

        $data['row'] = $this->simulacao_site_model->carrega($cd_simulacao_site);

        $data['acompanhamendo'] = $this->simulacao_site_model->carrega_acompanhamento($cd_simulacao_site_acompanhamento);

        $this->simulacao_site_model->excluir_acompanhamento( $cd_simulacao_site_acompanhamento,$this->session->userdata('codigo'));
    
        redirect('planos/simulacao_site_senge/cadastro/'.$cd_simulacao_site, 'refresh');
    }
}
?>