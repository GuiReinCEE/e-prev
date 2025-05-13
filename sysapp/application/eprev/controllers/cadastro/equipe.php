<?php
class Equipe extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
    }

    public function index($cd_divisao = '')
    {	
		$data['cd_divisao'] = (trim($cd_divisao) != '' ? trim($cd_divisao) : $this->session->userdata('divisao'));

		$this->load->view('cadastro/equipe/index', $data);
    }

	public function listar()
	{
		$this->load->model(array(
			'projetos/equipe_model',
			'projetos/processos_model'
		));

		$args['cd_divisao'] = $this->input->post("cd_divisao", TRUE);
		$data['cd_divisao'] = $this->input->post("cd_divisao", TRUE);
		
		manter_filtros($args);

		$data['collection'] = $this->equipe_model->listar(trim($args['cd_divisao']));

		$args = array(
			'fl_vigente'              => 'S',
			'fl_versao_it'            => '',
			'cd_gerencia_responsavel' => trim($args['cd_divisao'])
		);

		$data['processos'] = $this->processos_model->listar($args);

		foreach ($data['processos'] as $key => $item) 
		{
			$data['processos'][$key]['usuario_responsavel'] = array();

			foreach($this->processos_model->get_usuario_responsavel($item['cd_processo']) as $usuario)
			{
				$data['processos'][$key]['usuario_responsavel'][] = $usuario['ds_nome_usuario'];
			}
		}

		$this->load->view('cadastro/equipe/index_result', $data);
	}
}