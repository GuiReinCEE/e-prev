<?php
class Solic_entrega_documento_resp_area_consolidadora extends Controller
{
	function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index()
    {
        $this->load->model('projetos/solic_entrega_documento_resp_area_consolidadora_model');

        foreach ($this->solic_entrega_documento_resp_area_consolidadora_model->carrega_divisao() as $key => $item)
        {
            $usuario = array();

            foreach ($this->solic_entrega_documento_resp_area_consolidadora_model->carrega_usuario($item['cd_gerencia']) as $key2 => $item2) 
            {
                $usuario[] = $item2['ds_usuario'];
            }

            $data['collection'][] = array(
                'cd_gerencia' => $item['cd_gerencia'],
                'ds_usuario'  =>  $usuario
            );       
        }

        $this->load->view('atividade/solic_entrega_documento_resp_area_consolidadora/index', $data);
    }

    public function salvar()
    {
        $this->load->model('projetos/solic_entrega_documento_resp_area_consolidadora_model');

        $args = array(
            'usuario'     => (is_array($this->input->post('usuario', TRUE)) ? $this->input->post('usuario', TRUE): array()),
            'cd_gerencia' => $this->input->post('cd_gerencia', TRUE),
            'cd_usuario'  => $this->session->userdata('codigo')
        );

        $this->solic_entrega_documento_resp_area_consolidadora_model->salvar_resp_area_concolidadora($args);

        redirect('atividade/solic_entrega_documento_resp_area_consolidadora/index');
    }

    public function cadastro($cd_gerencia = '')
    {
        $this->load->model('projetos/solic_entrega_documento_resp_area_consolidadora_model');

        $data = array(
            'usuario'     => $this->solic_entrega_documento_resp_area_consolidadora_model->get_usuario_area_consolidadora($cd_gerencia),
            'cd_gerencia' => $this->solic_entrega_documento_resp_area_consolidadora_model->carrega_nome_area($cd_gerencia)
        );

        $usuarios_area_concolidadora = $this->solic_entrega_documento_resp_area_consolidadora_model->carrega_usuario($cd_gerencia);

        if(count($usuarios_area_concolidadora) > 0)
        {
            foreach ($usuarios_area_concolidadora as $key => $item)
            {
                $data['usuario_responsavel'][] = $item['cd_usuario'];
            }
        }
        else
        {
            $data['usuario_responsavel'][] = array();
        }

        $this->load->view('atividade/solic_entrega_documento_resp_area_consolidadora/cadastro', $data);
    }
}