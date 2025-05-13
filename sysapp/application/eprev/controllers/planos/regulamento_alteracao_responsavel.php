<?php
class Regulamento_alteracao_responsavel extends Controller
{
	function __construct()
	{
		parent::Controller();

		CheckLogin();
	}

	public function index()
	{
		$this->load->model('gestao/regulamento_alteracao_responsavel_model');

		foreach ($this->regulamento_alteracao_responsavel_model->carrega_divisao() as $key => $item)
        {
            $usuario = array();

            foreach ($this->regulamento_alteracao_responsavel_model->carrega_usuario($item['cd_gerencia']) as $key2 => $item2) 
            {
                $usuario[] = $item2['ds_usuario'];
            }

            $data['collection'][] = array(
                'cd_gerencia' => $item['cd_gerencia'],
                'ds_usuario'  =>  $usuario
            );       
        }

		$this->load->view('planos/regulamento_alteracao_responsavel/index', $data);
	}

    public function cadastro($cd_gerencia = '')
    {
        $this->load->model('gestao/regulamento_alteracao_responsavel_model');

        $data = array(
            'usuario'     => $this->regulamento_alteracao_responsavel_model->get_usuario_divisao($cd_gerencia),
            'cd_gerencia' => $this->regulamento_alteracao_responsavel_model->carrega_nome_area($cd_gerencia)
        );

        $usuario_responsavel = $this->regulamento_alteracao_responsavel_model->carrega_usuario($cd_gerencia);

        if(count($usuario_responsavel) > 0)
        {
            foreach ($usuario_responsavel as $key => $item)
            {
                $data['usuario_responsavel'][] = $item['cd_usuario'];
            }
        }
        else
        {
            $data['usuario_responsavel'][] = array();
        }

        $this->load->view('planos/regulamento_alteracao_responsavel/cadastro', $data);
    }

    public function salvar()
    {
        $this->load->model('gestao/regulamento_alteracao_responsavel_model');

        $args = array(
            'usuario'     => (is_array($this->input->post('usuario', TRUE)) ? $this->input->post('usuario', TRUE): array()),
            'cd_gerencia' => $this->input->post('cd_gerencia', TRUE),
            'cd_usuario'  => $this->session->userdata('codigo')
        );

        $this->regulamento_alteracao_responsavel_model->salvar_resp_regulamento_alteracao($args);

        redirect('planos/regulamento_alteracao_responsavel/index');
    }
}