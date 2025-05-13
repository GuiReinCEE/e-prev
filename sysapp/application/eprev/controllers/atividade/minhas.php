<?php
class minhas extends Controller
{
    function __construct()
    {
        parent::Controller();
			
        $this->load->model('projetos/atividades_model');
    }

    function index($fl_gerente_view = "", $fl_juridico_emprestimo = "", $fl_administrativo = "")
    {
    	CheckLogin();

		$args   = array();
		$data   = array();
		$result = null;
	
		$status_aguardando=resgatar_filtro('status_aguardando');
		$status_em_andamento=resgatar_filtro('status_em_andamento');
		$status_encerrado=resgatar_filtro('status_encerrado');
		$status_em_teste=resgatar_filtro('status_em_teste');
		$status_aguardando_definicao=resgatar_filtro('status_aguardando_definicao');
		$status_aguardando_usuario=resgatar_filtro('status_aguardando_usuario');

		$status_aguardando = ($status_aguardando=='S'||$status_aguardando=='');
		$status_em_andamento = ($status_em_andamento=='S'||$status_em_andamento=='');
		$status_encerrado = ($status_encerrado=='S');
		$status_em_teste = ($status_em_teste=='S'||$status_em_teste=='');
		$status_aguardando_definicao = ($status_aguardando_definicao=='S');
		$status_aguardando_usuario = ($status_aguardando_usuario=='S'||$status_aguardando_usuario=='');
		$filtros['status'][] = array( 'id'=>'aguardando', 'value'=>'', 'text'=>'Aguardando', 'checked'=>$status_aguardando );
		$filtros['status'][] = array( 'id'=>'em_andamento', 'value'=>'', 'text'=>'Em Andamento', 'checked'=>$status_em_andamento );
		$filtros['status'][] = array( 'id'=>'encerrado', 'value'=>'Encerrados', 'text'=>'Encerrados', 'checked'=>$status_encerrado );
		$filtros['status'][] = array( 'id'=>'em_teste', 'value'=>'Em testes', 'text'=>'Em testes', 'checked'=>$status_em_teste );
		$filtros['status'][] = array( 'id'=>'aguardando_definicao', 'value'=>'Aguardando definição', 'text'=>'Aguardando definição', 'checked'=>$status_aguardando_definicao );
		$filtros['status'][] = array( 'id'=>'aguardando_usuario', 'value'=>'Aguardando usuário', 'text'=>'Aguardando usuário', 'checked'=>$status_aguardando_usuario );

		if($fl_gerente_view == '')
		{
			$fl_gerente_view=resgatar_filtro('fl_gerente_view');
		}

		$data['filtros'] = $filtros;
		$data['fl_gerente_view'] = $fl_gerente_view;
		$data['fl_juridico_emprestimo'] = $fl_juridico_emprestimo;
		$data['fl_administrativo'] = $fl_administrativo;
		
		$data['divisao_solicitante_dd'] = array();
		$data['projetos_dd'] = array();
		$data['solicitante_dd'] = array();
		$data['atendente_dd'] = array();

		$data['divisao_solicitante_dd'] = $this->atividades_model->get_divisao_solicitante();
		$data['projetos_dd']            = $this->atividades_model->get_projeto();
		$data['ar_tipo_solicitacao']    = $this->atividades_model->get_tipo_solicitacao();			
		$data['solicitante_dd']         = $this->atividades_model->get_solicitante();
		$data['atendente_dd']           = $this->atividades_model->get_atendente();
		$data['classificacao']          = $this->atividades_model->get_classificacao();

		$this->atividades_model->cronograma_grupos($result, $args);
		$data['grupos'] = $result->result_array();
		
		$this->atividades_model->area_atendente($result, $args);
		$data['arr_area_atendente'] = $result->result_array();
		
		$this->load->view('atividade/minhas/index', $data);
		
    }

    function listar()
    {
    	CheckLogin();
    	
		if(($this->input->post("fl_gerente_view", true) == "S") and (!(($this->session->userdata('tipo') == "G") or ($this->session->userdata('indic_13') == "S"))))
		{
			echo br(2).'<span class="label label-important">ACESSO NÃO PERMITIDO, SOMENTE GERENTE OU SUBSTITUTO</span>'.br(5);
		}
		else
		{		
			$this->load->model('projetos/atividade_minhas_coluna_model');

			$args   = array();
			$data   = array();
			$result = null;

			$args["fl_gerente_view"]               = $this->input->post("fl_gerente_view", true);
			$args["status_aguardando"]             = $this->input->post("status_aguardando", true);
			$args["status_em_andamento"]           = $this->input->post("status_em_andamento", true);
			$args["status_encerrado"]              = $this->input->post("status_encerrado", true);
			$args["status_em_teste"]               = $this->input->post("status_em_teste", true);
			$args["status_aguardando_definicao"]   = $this->input->post("status_aguardando_definicao", true);
			$args["status_aguardando_usuario"]     = $this->input->post("status_aguardando_usuario", true);
			$args["nr_prioridade_ini"]             = $this->input->post("nr_prioridade_ini", TRUE);
			$args["nr_prioridade_fim"]             = $this->input->post("nr_prioridade_fim", TRUE);
			$args["fl_cronograma"]                 = $this->input->post("fl_cronograma", TRUE);
			$args["fl_prioridade"]                 = $this->input->post("fl_prioridade", TRUE);
			$args["feitas"]                        = $this->input->post("feitas", TRUE);
			$args["recebidas"]                     = $this->input->post("recebidas", TRUE);
			$args["dt_solicitacao_inicio"]         = $this->input->post("dt_solicitacao_inicio", TRUE);
			$args["dt_solicitacao_fim"]            = $this->input->post("dt_solicitacao_fim", TRUE);
			$args["dt_envio_inicio"]               = $this->input->post("dt_envio_inicio", TRUE);
			$args["dt_envio_fim"]                  = $this->input->post("dt_envio_fim", TRUE);
			$args["dt_conclusao_inicio"]           = $this->input->post("dt_conclusao_inicio", TRUE);
			$args["dt_conclusao_fim"]              = $this->input->post("dt_conclusao_fim", TRUE);
			$args["dt_limite_doc_inicio"]          = $this->input->post("dt_limite_doc_inicio", TRUE);
			$args["dt_limite_doc_fim"]             = $this->input->post("dt_limite_doc_fim", TRUE);
			$args["divisao_solicitante"]           = $this->input->post("divisao_solicitante", TRUE);
			$args["projeto"]                       = $this->input->post("projeto", TRUE);
			$args["cd_tipo_solicitacao"]           = $this->input->post("cd_tipo_solicitacao", TRUE);
			$args["cd_solicitante"]                = $this->input->post("cd_solicitante", TRUE);
			$args["cd_atendente"]                  = $this->input->post("cd_atendente", TRUE);
			$args["descricao"]                     = $this->input->post("descricao", TRUE);
			$args["cd_empresa"]                    = $this->input->post("cd_empresa", TRUE);
			$args["cd_registro_empregado"]         = $this->input->post("cd_registro_empregado", TRUE);
			$args["seq_dependencia"]               = $this->input->post("seq_dependencia", TRUE);
			$args["numero"]                        = $this->input->post("numero", TRUE);
			$args["fl_balanco_gi"]                 = $this->input->post("fl_balanco_gi", TRUE);
			$args["cd_atividade_cronograma_grupo"] = $this->input->post("cd_atividade_cronograma_grupo", TRUE);
			$args["cd_gerencia_atendente"]         = $this->input->post("cd_gerencia_atendente", TRUE);
			$args["cd_atividade_classificacao"]    = $this->input->post("cd_atividade_classificacao", TRUE);
			$args["fl_juridico_emprestimo"]        = $this->input->post("fl_juridico_emprestimo", TRUE);
			$args["fl_administrativo"]             = $this->input->post("fl_administrativo", TRUE);
			
			$args["cd_usuario_logado"]             = $this->session->userdata('codigo');
			$args["tipo_usuario_logado"]           = $this->session->userdata('tipo');
			$args["gerencia_usuario_logado"]       = $this->session->userdata('divisao');

			manter_filtros($args);

			$data['collection_head'] = $this->atividade_minhas_coluna_model->listar();
			$data['collection_ocultar'] = $this->atividade_minhas_coluna_model->listar_ocultar($this->session->userdata('codigo'));

			$this->atividades_model->listar($result, $args);
			$data['collection'] = $result;

			$this->load->view("atividade/minhas/partial_result", $data);
		}
    }

	function detalhe($numero)
	{
		CheckLogin();

		if(intval($numero)==0)
		{
			exibir_mensagem('Para ver o detalhe de uma atividade, deve ser passado o número da mesma.');
		}
		else
		{
			$at=$this->db->query("select area from projetos.atividades where numero=".intval($numero))->row_array();

			if( $at )
			{
				if( $at['area']=='GI' ){ redirect('atividade/informatica/solicitacao/'.intval($numero)); }
			}
			else
			{
				exibir_mensagem('Atividade número '.intval($numero).' não encontrada.');
			}
		}
	}
	
    function buscarAtividade()
    {
    	CheckLogin();

		$args   = array();
		$data   = array();
		$result = null;

		$args["cd_atividade"] = $this->input->post("cd_atividade", true);

		$this->atividades_model->buscarAtividade($result, $args);
        $ar_ret = $result->row_array();
		
		echo json_encode($ar_ret);
    }	
	
    function notificacao()
    {
    	CheckLogin();

		$result = null;
		$data = Array();
		$args = Array();
		
		$args["cd_usuario"] = $this->session->userdata('codigo');
		
		$this->atividades_model->notificacao($result, $args);
		$ar_reg = $result->row_array();

		echo json_encode($ar_reg);
	}	

	function notificacao_socket($cd_usuario)
    {	
    	$result = null;
		$data = Array();
		$args = Array();
		
		$args["cd_usuario"] = intval($cd_usuario);
		
		$this->atividades_model->notificacao($result, $args);
		$ar_reg = $result->row_array();

		echo json_encode($ar_reg);
    }
}
?>