<?php
class encaminhada extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

		// filtros
		$filtros['status'][] = array( 'id'=>'aguardando', 'value'=>'', 'text'=>'Aguardando', 'checked'=>true );
		$filtros['status'][] = array( 'id'=>'em_andamento', 'value'=>'', 'text'=>'Em Andamento', 'checked'=>true );
		$filtros['status'][] = array( 'id'=>'encerrado', 'value'=>'Encerrados', 'text'=>'Encerrados', 'checked'=>false );
		$filtros['status'][] = array( 'id'=>'em_teste', 'value'=>'Em testes', 'text'=>'Em testes', 'checked'=>true );
		$filtros['status'][] = array( 'id'=>'aguardando_definicao', 'value'=>'Aguardando definição', 'text'=>'Aguardando definição', 'checked'=>false );

		$data['filtros'] = $filtros;

		// Dropdown de divisão solicitante
		$data['divisao_solicitante_dd'] = $this->db->query("
			SELECT distinct codigo as value, nome as text 
			FROM projetos.divisoes a 
			JOIN projetos.atividades b ON a.codigo=b.divisao 
			ORDER BY nome ASC
		")->result_array();

		$data['projetos_dd'] = $this->db->query("
			SELECT codigo as value, nome as text 
			FROM projetos.projetos 
			WHERE codigo IN
			(
				SELECT DISTINCT(a.sistema) 
				FROM projetos.atividades a, listas l1, listas l2 
				WHERE l1.codigo = a.status_atual AND l1.categoria = 'STAT' AND l2.categoria = 'TPAT' AND l2.codigo = a.tipo
			) 
			ORDER BY nome
		")->result_array();
		
		#### TIPO DE MANUTENÇÃO ####
		$data['ar_tipo_solicitacao'] = $this->db->query("
			SELECT tm.codigo AS value,
				   tm.divisao || ' - ' || tm.descricao AS text
			  FROM public.listas tm
			  JOIN projetos.divisoes d
				ON d.codigo = tm.divisao
			 WHERE tm.categoria   = 'TPMN' 
			   AND tm.dt_exclusao IS NULL
			   AND tm.divisao     <> '*'
			 ORDER BY tm.divisao ASC,
					  tm.descricao ASC
		")->result_array();		

		$data['solicitante_dd'] = $this->db->query("
			SELECT distinct a.codigo as value, a.nome as text
			FROM projetos.usuarios_controledi a
			JOIN projetos.atividades b on a.codigo=b.cod_solicitante
			/*WHERE a.tipo IN ('D','G','N','U')*/
			ORDER BY a.nome
		")->result_array();

		$data['atendente_dd'] = $this->db->query("
			SELECT distinct a.codigo as value, a.nome as text
			FROM projetos.usuarios_controledi a
			JOIN projetos.atividades b on a.codigo=b.cod_atendente
			/*WHERE a.tipo IN ('D','G','N','U')*/
			ORDER BY a.nome
		")->result_array();

        $this->load->view('atividade/encaminhada/index.php', $data);
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Atividades_model');

		$args   = array();
		$data   = array();
		$result = null;

		
		$args["status_aguardando"]           = $this->input->post("status_aguardando", TRUE);
		$args["status_em_andamento"]         = $this->input->post("status_em_andamento", TRUE);
		$args["status_encerrado"]            = $this->input->post("status_encerrado", TRUE);
		$args["status_em_teste"]             = $this->input->post("status_em_teste", TRUE);
		$args["status_aguardando_definicao"] = $this->input->post("status_aguardando_definicao", TRUE);

		$args["feitas"]                      = $this->input->post("feitas", TRUE);
		$args["recebidas"]                   = $this->input->post("recebidas", TRUE);
		$args["tempo"]                       = $this->input->post("tempo", TRUE);
		$args["dt_solicitacao_inicio"]       = $this->input->post("dt_solicitacao_inicio", TRUE);
		$args["dt_solicitacao_fim"]          = $this->input->post("dt_solicitacao_fim", TRUE);
		$args["dt_envio_inicio"]             = $this->input->post("dt_envio_inicio", TRUE);
		$args["dt_envio_fim"]                = $this->input->post("dt_envio_fim", TRUE);
		$args["dt_conclusao_inicio"]         = $this->input->post("dt_conclusao_inicio", TRUE);
		$args["dt_conclusao_fim"]            = $this->input->post("dt_conclusao_fim", TRUE);

		$args["divisao_solicitante"]         = $this->input->post("divisao_solicitante", TRUE);
		$args["projeto"]                     = $this->input->post("projeto", TRUE);
		$args["cd_tipo_solicitacao"]         = $this->input->post("cd_tipo_solicitacao", TRUE);
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
		$args["encaminhada"]                 = TRUE;

		$args["status_aguardando_usuario"]   = '';
		$args["dt_limite_doc_inicio"]        = '';
		$args["cd_atividade_classificacao"]  = 0;
		
		$args["fl_gerente_view"] = "";
		if($this->session->userdata('divisao') == "GP")
		{
			$args["fl_gerente_view"] = "S";
		}

		manter_filtros($args);
		
        $this->Atividades_model->listar($result, $args);
        $data['collection'] = $result;

        $this->load->view("atividade/encaminhada/partial_result", $data);
    }
}
?>