<?php
class calculo_taxa_joia extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/atividades_model');
    }
	
	function index($cd_empresa = "", $cd_registro_empregado = "", $seq_dependencia = "")
    {
		$args = Array();
		$data = Array();
		$result = null;
		
		$data['cd_empresa']            = $cd_empresa;
		$data['cd_registro_empregado'] = $cd_registro_empregado;
		$data['seq_dependencia']       = $seq_dependencia;
		
		$data['participante'] = array(
			'cd_empresa'            => $cd_empresa,
			'cd_registro_empregado' => $cd_registro_empregado,
			'seq_dependencia'       => $seq_dependencia
		);
		
		$this->atividades_model->solicitante( $result, $args );
		$data['arr_solicitante'] = $result->result_array();
		
		$this->atividades_model->atendente( $result, $args );
		$data['arr_atendente'] = $result->result_array();
		
		$this->load->view('atividade/calculo_taxa_joia/index', $data);
	}
	
	function listar()
    {
        $args = Array();
		$data = Array();
		$result = null;
		
		$args["status_aguardando"]           = "S";
		$args["status_em_andamento"]         = "S";
		$args["status_encerrado"]            = "S";
		$args["status_em_teste"]             = "S";
		$args["status_aguardando_definicao"] = "S";
		$args['status_aguardando_usuario']   = "";
		$args["feitas"]                      = "S";
		$args["recebidas"]                   = "S";
		$args["tempo"]                       = "";
		$args["dt_solicitacao_inicio"]       = $this->input->post("dt_solicitacao_inicio", TRUE);
		$args["dt_solicitacao_fim"]          = $this->input->post("dt_solicitacao_fim", TRUE);
		$args["dt_envio_inicio"]             = "";
		$args["dt_envio_fim"]                = "";
		$args["dt_conclusao_inicio"]         = $this->input->post("dt_conclusao_inicio", TRUE);
		$args["dt_conclusao_fim"]            = $this->input->post("dt_conclusao_fim", TRUE);
		$args["divisao_solicitante"]         = "";
		$args["projeto"]                     = "";
		$args["cd_tipo_solicitacao"]         = "";
		$args["cd_solicitante"]              = $this->input->post("cd_solicitante", TRUE);
		$args["cd_atendente"]                = $this->input->post("cd_atendente", TRUE);
		$args["descricao"]                   = $this->input->post("descricao", TRUE);

		$args["cd_empresa"]                  = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"]       = $this->input->post("cd_registro_empregado", TRUE);
		$args["seq_dependencia"]             = $this->input->post("seq_dependencia", TRUE);

		$args["cd_usuario_logado"]           = $this->session->userdata('codigo');
		$args["tipo_usuario_logado"]         = $this->session->userdata('tipo');
		$args["gerencia_usuario_logado"]     = $this->session->userdata('divisao');

		$args["numero"]                      = $this->input->post("numero", TRUE);
		$args["tipo_usuario_logado"]         = "";
		$args['calculo_taxa_joia']           = "S";
		$args["fl_gerente_view"] = "";
		$args['cd_atividade_classificacao'] = '';

		manter_filtros($args);
		
        $this->atividades_model->listar( $result, $args );
        $data['collection'] = $result;

        $this->load->view( "atividade/calculo_taxa_joia/index_result", $data );
    }
}
?>