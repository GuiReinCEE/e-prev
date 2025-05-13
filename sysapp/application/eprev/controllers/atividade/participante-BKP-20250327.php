<?php
class Participante extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index($cd_empresa = '', $cd_registro_empregado = '', $seq_dependencia = '')
    {
    	$this->load->model('projetos/relatorio_atividades_participante_model');

    	$data = array(
    		'filtros' => array(
	    		array('id' => 'aguardando', 'value' => '', 'text' => 'Aguardando', 'checked' => true),
	    		array('id' => 'em_andamento', 'value' => '', 'text' => 'Em Andamento', 'checked' => true),
	    		array('id' => 'encerrado', 'value' => 'Encerrados', 'text' => 'Encerrados', 'checked' => false),
	    		array('id' => 'em_teste', 'value' => 'Em testes', 'text' => 'Em testes', 'checked' => true),
	    		array('id' => 'aguardando_definicao', 'value' => 'Aguardando definiчуo', 'text' => 'Aguardando definiчуo', 'checked' => false),
	    		array('id' => 'aguardando_usuario', 'value' => 'Aguardando usuсrio', 'text' => 'Aguardando usuсrio', 'checked' => true)
	    	),
	    	'gerencia_solicitante'  => $this->relatorio_atividades_participante_model->get_gerencia_solicitante(),
	    	'projetos'              => $this->relatorio_atividades_participante_model->get_projetos(),
	    	'tipo_solicitacao'      => $this->relatorio_atividades_participante_model->get_tipo_solicitacao(),
	    	'solicitante'           => $this->relatorio_atividades_participante_model->get_solicitante(),
	    	'atendente'             => $this->relatorio_atividades_participante_model->get_atendente(),
	    	'cd_empresa'            => $cd_empresa,
	    	'cd_registro_empregado' => $cd_registro_empregado,
	    	'seq_dependencia'       => $seq_dependencia
    	);	
		
        $this->load->view('atividade/participante/index', $data);
    }

    public function listar()
    {
        $this->load->model('projetos/atividades_model');

		$result = null;

		$args = array(
			'status_aguardando'           => $this->input->post('status_aguardando', TRUE),
			'status_em_andamento'         => $this->input->post('status_em_andamento', TRUE),
			'status_encerrado'            => $this->input->post('status_encerrado', TRUE),
			'status_em_teste'             => $this->input->post('status_em_teste', TRUE),
			'status_aguardando_definicao' => $this->input->post('status_aguardando_definicao', TRUE),
			'status_aguardando_usuario'   => $this->input->post('status_aguardando_usuario', TRUE),
			'feitas'                      => $this->input->post('feitas', TRUE),
			'recebidas'                   => $this->input->post('recebidas', TRUE),
			'tempo'                       => $this->input->post('tempo', TRUE),
			'dt_solicitacao_inicio'       => $this->input->post('dt_solicitacao_inicio', TRUE),
			'dt_solicitacao_fim'          => $this->input->post('dt_solicitacao_fim', TRUE),
			'dt_envio_inicio'             => $this->input->post('dt_envio_inicio', TRUE),
			'dt_envio_fim'                => $this->input->post('dt_envio_fim', TRUE),
			'dt_conclusao_inicio'         => $this->input->post('dt_conclusao_inicio', TRUE),
			'dt_conclusao_fim'            => $this->input->post('dt_conclusao_fim', TRUE),
			'dt_limite_doc_inicio'        => $this->input->post('dt_limite_doc_inicio', TRUE),
			'dt_limite_doc_fim'           => $this->input->post('dt_limite_doc_fim', TRUE),
			'divisao_solicitante'         => $this->input->post('divisao_solicitante', TRUE),
			'projeto'                     => $this->input->post('projeto', TRUE),
			'cd_tipo_solicitacao'         => $this->input->post('cd_tipo_solicitacao', TRUE),
			'cd_solicitante'              => $this->input->post('cd_solicitante', TRUE),
			'cd_atendente'                => $this->input->post('cd_atendente', TRUE),
			'descricao'                   => $this->input->post('descricao', TRUE),
			'cd_empresa'                  => $this->input->post('cd_empresa', TRUE),
			'cd_registro_empregado'       => $this->input->post('cd_registro_empregado', TRUE),
			'seq_dependencia'             => $this->input->post('seq_dependencia', TRUE),
			'numero'                      => $this->input->post('numero', TRUE),
			'participante'                => TRUE,
			'fl_gerente_view'             => '',
			'cd_atividade_classificacao'  => '',
			'cd_usuario_logado'           => $this->session->userdata('codigo'),
			'tipo_usuario_logado'         => $this->session->userdata('tipo'),
			'gerencia_usuario_logado'     => $this->session->userdata('divisao'),
		);

		if($this->session->userdata('divisao') == 'GRSC')
		{
			$args['fl_gerente_view'] = 'S';
		}	

		manter_filtros($args);
		
        $this->atividades_model->listar($result, $args);
        $data['collection'] = $result;

        $this->load->view('atividade/participante/index_result', $data);
    }
}
?>