<?php
class atendimento extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index($id=0)
	{
		if( ! CheckLogin() ) exit;

		$col = array();
		$err = array();

		// carregando atividade
		$this->load->model('projetos/Atividades_model');
		$this->Atividades->carregar_pk($id, $col, $err); // 20352
		$data['atividade'] = $col;

		// valida atividade
		if(intval($id)>0 && $col['area']!='GAP')
		{
			echo "Atividade de outra área!"; return false; exit;
		}

		// carregando dropdown tipo da manutenção
		$q = $this->db->query("SELECT codigo as value, descricao as text FROM listas WHERE categoria='TPMN' AND divisao = 'GAP' AND dt_exclusao IS NULL ORDER BY descricao;");
		$data['tipo_manutencao_dd'] = $q->result_array();

		// carregando dropdown tipo da atividade
		$q = $this->db->query("SELECT codigo as value, descricao as text FROM listas WHERE categoria = 'TPAT' AND divisao = 'GAP' ORDER BY descricao;");
		$data['tipo_atividade_dd'] = $q->result_array();

		// carregando dropdown atendentes
		$q = $this->db->query("SELECT codigo as value, descricao as text FROM listas WHERE categoria = 'TPAT' AND divisao = 'GAP' ORDER BY descricao;");
		$data['tipo_atividade_dd'] = $q->result_array();

		// carregando dropdown planos
		$q = $this->db->query("SELECT cd_plano as value, descricao as text FROM planos WHERE cd_plano <> 0 ORDER BY descricao;");
		$data['plano_dd'] = $q->result_array();

		// carregando dropdown perfil de solicitante
		$q = $this->db->query("SELECT codigo as value, descricao as text FROM listas WHERE categoria='SDAP' ORDER BY descricao;");
		$data['perfil_solicitante_dd'] = $q->result_array();

		// carregando dropdown forma de solicitação
		$q = $this->db->query("SELECT codigo as value, descricao as text FROM listas WHERE categoria='FDAP' ORDER BY descricao;");
		$data['forma_solicitacao_dd'] = $q->result_array();

		$idx=0;$fe[$idx]['value'] = '';$fe[$idx]['text'] = '';
		$idx+=1;$fe[$idx]['value'] = '1';$fe[$idx]['text'] = 'Correio';
		$idx+=1;$fe[$idx]['value'] = '2';$fe[$idx]['text'] = 'Central de atendimento';
		$idx+=1;$fe[$idx]['value'] = '3';$fe[$idx]['text'] = 'Email';
		$data['forma_envio_dd'] = $fe;

		$this->load->view('atividade/atendimento/index',$data);
	}

	function aba_atendimento_index($id)
	{
		if( ! CheckLogin() ) exit;

		$col = array();
		$err = array();

		// carregando atividade
		$this->load->model('projetos/Atividades_model');
		$this->Atividades->carregar_pk($id, $col, $err);
		$data['atividade'] = $col;

		// valida atividade
		if(intval($id)>0 && $col['area']!='GAP')
		{
			echo "Atividade de outra área!"; return false; exit;
		}

		// carregando dropdown tipo da manutenção
		$q = $this->db->query("SELECT codigo as value, nome as text 
		FROM projetos.projetos WHERE area=? and dt_exclusao is null ORDER BY nome;", array('GAP'));
		$data['projeto_dd'] = $q->result_array();

		// carregando dropdown de status atual
		$q = $this->db->query("SELECT codigo as value, descricao as text FROM listas WHERE categoria = 'STAT' AND divisao = ? ORDER BY descricao", array('GAP'));
		$data['status_dd'] = $q->result_array();

		// carregando dropdown de complexidade
		$q = $this->db->query("SELECT codigo as value, descricao as text FROM listas WHERE categoria='CPLX' ORDER BY codigo");
		$data['complexidade_dd'] = $q->result_array();

		$this->load->view('atividade/atendimento/atendimento_index.php', $data);
	}

	function aba_anexo_index()
	{
		$this->load->view('atividade/atendimento/anexo');
	}

	function aba_historico_index()
	{
		$this->load->view('atividade/atendimento/historico');
	}

	function salvar_solicitacao()
	{
		$f=array( 
			'numero'
			, 'area'
			, 'tipo_solicitacao'
			, 'tipo'
			, 'titulo'
			, 'descricao'
			, 'problema'
			, 'cod_atendente'
			, 'dt_limite'
			, 'cd_empresa'
			, 'cd_registro_empregado'
			, 'cd_sequencia'
			, 'cd_plano'
			, 'solicitante'
			, 'forma'
			, 'tp_envio'
			, 'cd_atendimento'
			, 'cod_solicitante'
			, 'divisao'
		);

		foreach($f as $item)
		{
			$dados[$item]=$this->input->post($item, TRUE);
		}

		$this->load->model('projetos/Atividades_model');
		if( intval($dados['numero'])==0 )
		{
			$err=array();
			$newId = $this->Atividades_model->atendimento_solicitacao_inserir($dados, $err);

			if($newId)
			{
				redirect( 'atividade/atendimento/index/'.$newId, 'refresh' );
			}
			else
			{
				show_error(implode(";",$err));
			}
		}
		else
		{
			$err=array();
			$ret = $this->Atividades_model->atendimento_solicitacao_salvar($dados, $err);

			if($ret)
			{
				redirect( 'atividade/atendimento/index/'.$dados['numero'], 'refresh' );
			}
			else
			{
				show_error(implode(";",$err));
			}
		}
	}

	function salvar_atendimento()
	{
		$f=array(
			'numero'
			, 'sistema'
			, 'status_atual'
			, 'dt_envio_teste'
			, 'dt_limite_testes'
			, 'cod_testador'
			, 'dt_inicio_real'
			, 'dt_fim_real'
			, 'solucao'
			, 'complexidade'
			, 'numero_dias'
			, 'periodicidade'
		);

		foreach($f as $item)
		{
			$dados[$item]=$this->input->post($item, TRUE);
		}

		$this->load->model('projetos/Atividades_model');
		$ret = $this->Atividades_model->atendimento_atendimento_salvar($dados, $err);

		if($ret)
		{
			redirect( 'atividade/atendimento/aba_atendimento_index/'.intval($dados['numero']), 'refresh' );
		}
		else
		{
			show_error(implode(";",$err));
		}
	}
}
?>