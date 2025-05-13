<?php
class tarefa extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
        $this->load->model('projetos/tarefas_model');
    }

    function index()
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$this->tarefas_model->listar_solicitante($result, $args);
		$data['solicitante_dd'] = $result->result_array();
		
		$this->tarefas_model->listar_atendente($result, $args);
		$data['atendente_dd'] = $result->result_array();

        $this->load->view('atividade/tarefa/index.php', $data);
    }

    function listar()
    {
        $result = null;
		$data = Array();
		$args = Array();

		$args['status_atual'] = array();

		if($this->input->post('status_aman')!='') $args['status_atual'][]=$this->input->post('status_aman', TRUE);
		if($this->input->post('status_eman')!='') $args['status_atual'][]=$this->input->post('status_eman', TRUE);
		if($this->input->post('status_susp')!='') $args['status_atual'][]=$this->input->post('status_susp', TRUE);
		if($this->input->post('status_libe')!='') $args['status_atual'][]=$this->input->post('status_libe', TRUE);
		if($this->input->post('status_conc')!='') $args['status_atual'][]=$this->input->post('status_conc', TRUE);

		if( sizeof($args['status_atual'])==0 )
		{
			$args['status_atual'] = array('AMAN','EMAN','SUSP','LIBE');
		}

		$args['dt_encaminhamento_inicio'] = $this->input->post('dt_encaminhamento_inicio', TRUE);
		$args['dt_encaminhamento_fim']    = $this->input->post('dt_encaminhamento_fim', TRUE);
		$args['dt_ok_anal_inicio']        = $this->input->post('dt_concluido_inicio', TRUE);
		$args['dt_ok_anal_fim']           = $this->input->post('dt_concluido_fim', TRUE);
		$args['cd_mandante']              = $this->input->post('cd_solicitante', TRUE);
		$args['cd_recurso']               = $this->input->post('cd_atendente', TRUE);
		$args['prioridade']               = $this->input->post('prioridade', TRUE);
		$args['cd_atividade']             = $this->input->post('cd_atividade', TRUE);
		$args['cd_tarefa']                = $this->input->post('cd_tarefa', TRUE);

		manter_filtros($args);
		
        $this->tarefas_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

        $this->load->view('atividade/tarefa/partial_result', $data);
    }
	
	function cadastro($cd_atividade, $cd_tarefa = 0, $fl_tarefa_tipo = '')
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']   = $cd_atividade;
		$args['cd_tarefa']      = $cd_tarefa;
		$args['fl_tarefa_tipo'] = $fl_tarefa_tipo;

		$this->tarefas_model->programas( $result, $args );
		$data['arr_programa'] = $result->result_array();
		
		$this->tarefas_model->tipo_tarefas( $result, $args );
		$data['arr_tipo_tarefa'] = $result->result_array();
		
		$this->tarefas_model->analistas( $result, $args );
		$data['arr_analista'] = $result->result_array();
		
		$this->tarefas_model->programador( $result, $args );
		$data['arr_programador'] = $result->result_array();
		
		if(intval($args['cd_tarefa']) == 0)
		{
			if(trim(strtoupper($fl_tarefa_tipo)) == '' || trim(strtoupper($fl_tarefa_tipo)) == 'A')
			{
				$programa       = '';
				$cd_tipo_tarefa = '';
			} 
			elseif(trim(strtoupper($fl_tarefa_tipo)) == 'F')
			{
				$programa       = 'Oracle Forms';
				$cd_tipo_tarefa = '3';
			}
			elseif(trim(strtoupper($fl_tarefa_tipo)) == 'R')
			{
				$programa       = 'Oracle Reports';
				$cd_tipo_tarefa = '14';
			}
					
			$data['row'] = array(
				'cd_atividade'   	  => $cd_atividade,
				'cd_tarefa'      	  => $cd_tarefa,
				'programa'       	  => $programa,
				'cd_tipo_tarefa'      => $cd_tipo_tarefa,
				'cd_mandante'    	  => $this->session->userdata('codigo'),
				'cd_recurso'     	  => '',
				'prioridade'     	  => 'N',
				'fl_checklist'   	  => 'N',
				'dt_inicio_prev' 	  => '',
				'dt_fim_prev'    	  => '',
				'dt_inicio_prog' 	  => '',
				'dt_fim_prog'    	  => '',
				'dt_ok_anal'     	  => '',
				'resumo'         	  => '',
				'descricao'           => '',
				'casos_testes'        => '',
				'tabs_envolv'         => '',
				'fl_tarefa_tipo'      => $fl_tarefa_tipo,
				'nr_nivel_prioridade' => 0,
				'dt_encaminhamento'   => '',
				'ds_nome_tela'        => '',
				'ds_menu'             => '',
				'fl_orientacao'       => 'R',
				'ds_dir'              => '',
				'ds_nome_arq'         => '',
				'ds_delimitador'      => '',
				'fl_largura'          => 'N',
				'ds_ordem'            => '',
				'status_atual'        => '',
				'status_cor'          => ''
			);
		}
		else
		{
			$this->tarefas_model->tarefa( $result, $args );
		    $data['row'] = $result->row_array();
			
			$fl_tarefa_tipo = $data['row']['fl_tarefa_tipo'];
		}
		
		if(trim(strtoupper($fl_tarefa_tipo)) == '')
		{
			$this->load->view('atividade/tarefa/cadastro', $data);
		} 
		elseif(trim(strtoupper($fl_tarefa_tipo)) == 'F')
		{
			$this->load->view('atividade/tarefa/cadastro_forms', $data);
		}
		elseif(trim(strtoupper($fl_tarefa_tipo)) == 'R')
		{
			$this->load->view('atividade/tarefa/cadastro_reports', $data);
		}
		elseif(trim(strtoupper($fl_tarefa_tipo)) == 'A')
		{
			$this->load->view('atividade/tarefa/cadastro_anexo', $data);
		}
	}
	
	function salvar()
    {
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']   	 = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']   	     = $this->input->post("cd_tarefa", TRUE);
		$args['programa']       	 = $this->input->post("programa", TRUE);
		$args['cd_tipo_tarefa']      = $this->input->post("cd_tipo_tarefa", TRUE);
		$args['cd_mandante']    	 = $this->input->post("cd_mandante", TRUE);
		$args['cd_recurso']     	 = $this->input->post("cd_recurso", TRUE);
		$args['prioridade']     	 = $this->input->post("prioridade", TRUE);
		$args['fl_checklist']   	 = $this->input->post("fl_checklist", TRUE);
		$args['dt_inicio_prev'] 	 = $this->input->post("dt_inicio_prev", TRUE);
		$args['dt_fim_prev']    	 = $this->input->post("dt_fim_prev", TRUE);
		$args['resumo']         	 = $this->input->post("resumo", TRUE);
		$args['descricao']      	 = $this->input->post("descricao", TRUE);
		$args['casos_testes']   	 = $this->input->post("casos_testes", TRUE);
		$args['tabs_envolv']    	 = $this->input->post("tabs_envolv", TRUE);
		$args['fl_tarefa_tipo'] 	 = $this->input->post("fl_tarefa_tipo", TRUE);
		$args['nr_nivel_prioridade'] = $this->input->post("nr_nivel_prioridade", TRUE);
		$args['ds_nome_tela']        = $this->input->post("ds_nome_tela", TRUE);
		$args['ds_menu']             = $this->input->post("ds_menu", TRUE);
		$args['fl_orientacao']       = $this->input->post("fl_orientacao", TRUE);
		$args['ds_nome_arq']         = $this->input->post("ds_nome_arq", TRUE);
		$args['ds_dir']              = $this->input->post("ds_dir", TRUE);
		$args['ds_delimitador']      = $this->input->post("ds_delimitador", TRUE);
		$args['ds_ordem']            = $this->input->post("ds_ordem", TRUE);
		$args['fl_largura']          = $this->input->post("fl_largura", TRUE);
		$arsg['cd_usuario']     	 = $this->session->userdata('codigo');
		$fl_encaminhamento           = $this->input->post('fl_encaminhamento');

		$cd_tarefa = $this->tarefas_model->salvar($result, $args);
		
		if(trim($fl_encaminhamento) == 'S')
		{
			$args['cd_tarefa'] = $cd_tarefa;
			$this->tarefas_model->encaminhar( $result, $args );
		}
		
		redirect("atividade/tarefa/cadastro/".intval($args["cd_atividade"])."/".$cd_tarefa, "refresh");	
	}
	
	function listar_lovs()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$cd_mandante          = $this->input->post("cd_mandante", TRUE);
		
		$data['fl_analista'] = false;
		if(intval($cd_mandante) == $this->session->userdata('codigo'))
		{
			$data['fl_analista'] = true;
		}
		
		$this->tarefas_model->lovs( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/tarefa/lovs_result', $data);
	}
	
	function salvar_lovs()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$args['ds_seq']   	  = $this->input->post("ds_seq", TRUE);
		$args['ds_tabela']    = $this->input->post("ds_tabela", TRUE);
		$args['ds_campo_ori'] = $this->input->post("ds_campo_ori", TRUE);
		$args['ds_campo_des'] = $this->input->post("ds_campo_des", TRUE);
		$arsg['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->tarefas_model->salvar_lovs($result, $args);
	}
	
	function excluir_lovs()
	{
		$args['cd_atividade']    = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']       = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_lovs'] = $this->input->post("cd_tarefas_lovs", TRUE);
		
		$this->tarefas_model->excluir_lovs($result, $args);
	}
	
	function listar_parametros()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']  = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']     = $this->input->post("cd_tarefa", TRUE);
		$cd_mandante           = $this->input->post("cd_mandante", TRUE);
		
		$data['fl_analista'] = false;
		if(intval($cd_mandante) == $this->session->userdata('codigo'))
		{
			$data['fl_analista'] = true;
		}
		
		$this->tarefas_model->paremetros( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/tarefa/parametros_result', $data);
	}
	
	function salvar_parametros()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$args['ds_campo']     = $this->input->post("ds_campo", TRUE);
		$args['ds_tipo']      = $this->input->post("ds_tipo", TRUE);
		$args['nr_ordem']     = $this->input->post("nr_ordem", TRUE);
		$arsg['cd_usuario']   = $this->session->userdata('codigo');
		
		$this->tarefas_model->salvar_parametros($result, $args);
	}
	
	function excluir_parametros()
	{
		$args['cd_atividade']          = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']             = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_parametros'] = $this->input->post("cd_tarefas_parametros", TRUE);
		
		$this->tarefas_model->excluir_parametros($result, $args);
	}
	
	function listar_relatorios()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']  = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']     = $this->input->post("cd_tarefa", TRUE);
		$cd_mandante           = $this->input->post("cd_mandante", TRUE);
		
		$data['fl_analista'] = false;
		if(intval($cd_mandante) == $this->session->userdata('codigo'))
		{
			$data['fl_analista'] = true;
		}
		
		$this->tarefas_model->tarefas_reports( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/tarefa/relatorios_result', $data);
	}
	
	function salvar_relatorios()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']  = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']     = $this->input->post("cd_tarefa", TRUE);
		$args['ds_banco']      = $this->input->post("cd_db", TRUE);
		$args['ds_tabela']     = $this->input->post("cd_tabela", TRUE);
		$args['ds_campo']      = $this->input->post("cd_campo", TRUE);
		$args['ds_label']      = $this->input->post("ds_label", TRUE);
		
		$this->tarefas_model->salvar_relatorios($result, $args); 
	}
		
	function excluir_relatorios()
	{
		$result = null;
		$data = Array();
		$args = Array();
	
		$args['cd_atividade']       = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']          = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_tabelas'] = $this->input->post("cd_tarefas_tabelas", TRUE);
		
		$this->tarefas_model->excluir_relatorios($result, $args);		
	}

	function encaminhar($cd_atividade, $cd_tarefa)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		
		$this->tarefas_model->encaminhar( $result, $args );
		
		redirect("atividade/tarefa/cadastro/".intval($args["cd_atividade"])."/".$cd_tarefa, "refresh");	
	}
	
	function tabelas()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_db'] = $this->input->post("cd_db", TRUE);
		
		if(trim($args['cd_db']) == 'ORACLE')
		{
			$this->tarefas_model->tabelas_oracle( $result, $args );
			$collection = $result->result_array();
		}
		elseif(trim($args['cd_db']) == 'POSTGRESQL')
		{
			$this->tarefas_model->tabelas_postgresql( $result, $args );
			$collection = $result->result_array();
		}
				
	    echo json_encode($collection);
	}
	
	function campos()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$arr = explode(".",$this->input->post("cd_tabela", TRUE));
		$args['cd_db'] = $this->input->post("cd_db", TRUE);
		
		$args['nspname'] = $arr[0];
		$args['relname'] = $arr[1];
		
		if(trim($args['cd_db']) == 'ORACLE')
		{
			$this->tarefas_model->campos_oracle( $result, $args );
			$collection = $result->result_array();
		}
		elseif(trim($args['cd_db']) == 'POSTGRESQL')
		{
			$this->tarefas_model->campos_postgresql( $result, $args );
			$collection = $result->result_array();
		}
				
	    echo json_encode($collection);
	}
	
	function listar_tipos()
	{
		$args = Array();
        $data = Array();
        $result = null;
		$data['collection'] = array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$cd_mandante          = $this->input->post("cd_mandante", TRUE);
		
		$data['fl_analista'] = false;
		if(intval($cd_mandante) == $this->session->userdata('codigo'))
		{
			$data['fl_analista'] = true;
		}
		
		$this->tarefas_model->tipos( $result, $args );
		$arr = $result->result_array();
		
		$i = 0;
		$j = 0;
		
		foreach($arr as $item)
		{
			$data['collection'][$i]['cd_tarefas_layout'] = $item['cd_tarefas_layout'];
			$data['collection'][$i]['ds_tipo']           = $item['ds_tipo'];
			$data['collection'][$i]['campo'] = array();
			
			$args['cd_tarefas_layout'] = $item['cd_tarefas_layout'];
			
			$this->tarefas_model->tipo_campos( $result, $args );
			$arr2 = $result->result_array();
			
			foreach($arr2 as $item2)
			{
				$data['collection'][$i]['campo'][$j]['cd_tarefas_layout_campo'] = $item2['cd_tarefas_layout_campo'];
				$data['collection'][$i]['campo'][$j]['ds_nome']                 = $item2['ds_nome'];
				$data['collection'][$i]['campo'][$j]['ds_tamanho']              = $item2['ds_tamanho'];
				$data['collection'][$i]['campo'][$j]['ds_caracteristica']       = $item2['ds_caracteristica'];
				$data['collection'][$i]['campo'][$j]['ds_formato']              = $item2['ds_formato'];
				$data['collection'][$i]['campo'][$j]['ds_definicao']            = $item2['ds_definicao'];
				
				$j++;
			}
			
			$i++;
		}
				
		$this->load->view('atividade/tarefa/tipos_result', $data);
	}
	
	function salvar_tipo()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$args['ds_tipo']      = $this->input->post("ds_tipo", TRUE);
		
		$this->tarefas_model->salvar_tipo($result, $args);
	}
	
	function excluir_tipo()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_atividade']      = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']         = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_layout'] = $this->input->post("cd_tarefas_layout", TRUE);
		
		$this->tarefas_model->excluir_tipo($result, $args);
	}
	
	function excluir_campo()
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_atividade']            = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']               = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_layout_campo'] = $this->input->post("cd_tarefas_layout_campo", TRUE);
		
		$this->tarefas_model->excluir_campo($result, $args);
	}
	
	function salvar_campo()
	{
		$args = Array();
        $data = Array();
        $result = null;
			
		$args['cd_atividade']      = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']         = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_layout'] = $this->input->post("cd_tarefas_layout", TRUE);
		$args['ds_nome']           = $this->input->post("campo_nome", TRUE);
		$args['ds_tamanho']        = $this->input->post("campo_tamanho", TRUE);
		$args['ds_caracteristica'] = $this->input->post("campo_caracteristica", TRUE);
		$args['ds_formato']        = $this->input->post("campo_formato", TRUE);
		$args['ds_definicao']      = $this->input->post("campo_definicao", TRUE);
		
		$this->tarefas_model->salvar_campo($result, $args);
	}
	
	function listar_tabelas()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']  = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']     = $this->input->post("cd_tarefa", TRUE);
		$cd_mandante           = $this->input->post("cd_mandante", TRUE);
		
		$data['fl_analista'] = false;
		if(intval($cd_mandante) == $this->session->userdata('codigo'))
		{
			$data['fl_analista'] = true;
		}
		
		$this->tarefas_model->tabelas( $result, $args );
		$data['collection_tabelas'] = $result->result_array();
		
		$this->tarefas_model->ordenacao( $result, $args );
		$data['collection_ordenacao'] = $result->result_array();
		
		$this->load->view('atividade/tarefa/tabelas_result', $data);
	}
	
	function salvar_tabela()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade']  = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']     = $this->input->post("cd_tarefa", TRUE);
		$args['ds_banco']      = $this->input->post("cd_db", TRUE);
		$args['ds_tabela']     = $this->input->post("cd_tabela", TRUE);
		$args['ds_campo']      = $this->input->post("cd_campo", TRUE);
		$args['nr_ordem']      = $this->input->post("nr_ordem", TRUE);
		
		$this->tarefas_model->salvar_tabela($result, $args);
		$this->tarefas_model->salvar_ordenacao($result, $args);
	}
	
	function atualiza_tabela()
	{
		$result = null;
		$data = Array();
		$args = Array();

		$args['cd_atividade']       = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']          = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_tabelas'] = $this->input->post("cd_tarefas_tabelas", TRUE);
		$args['ds_campo']           = $this->input->post("ds_campo", TRUE);
		$args['fl_campo']           = $this->input->post("fl_campo", TRUE);
		$args['ds_vl_dominio']      = $this->input->post("ds_vl_dominio", TRUE);
		$args['fl_campo_de']        = $this->input->post("fl_campo_de", TRUE);
		$args['ds_label']           = $this->input->post("ds_label", TRUE);
		$args['fl_visivel']         = $this->input->post("fl_visivel", TRUE);
	
		$this->tarefas_model->atualiza_tabela($result, $args);
	}
	
	function atualiza_ordenacao()
	{
		$result = null;
		$data = Array();
		$args = Array();

		$args['cd_atividade']       = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']          = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_tabelas'] = $this->input->post("cd_tarefas_tabelas", TRUE);
		$args['nr_ordem']           = $this->input->post("nr_ordem", TRUE);
	
		$this->tarefas_model->atualiza_ordenacao($result, $args);
	}
	
	function atualiza_relatorio()
	{
		$result = null;
		$data = Array();
		$args = Array();

		$args['cd_atividade']       = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']          = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_tabelas'] = $this->input->post("cd_tarefas_tabelas", TRUE);
		$args['ds_label']           = $this->input->post("ds_label", TRUE);
		$args['ds_campo']           = $this->input->post("ds_campo", TRUE);
	
		$this->tarefas_model->atualiza_relatorio($result, $args);
	}
	
	function excluir_tabela()
	{
		$result = null;
		$data = Array();
		$args = Array();

		$args['cd_atividade']       = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']          = $this->input->post("cd_tarefa", TRUE);
		$args['cd_tarefas_tabelas'] = $this->input->post("cd_tarefas_tabelas", TRUE);
		
		$this->tarefas_model->excluir_tabela($result, $args);
	}
	
	function conforme($cd_atividade, $cd_tarefa)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		
		$this->tarefas_model->conforme($result, $args);
		
		redirect("atividade/atividade_atendimento/index/".intval($args["cd_atividade"]), "refresh");	
	}
	
	function nao_conforme($cd_atividade, $cd_tarefa)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		
		$this->tarefas_model->tarefa( $result, $args );
		$data['row'] = $result->row_array();
		
		$this->load->view('atividade/tarefa/nao_conforme', $data);
	}
	
	function salvar_nao_conforme()
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['cd_tarefa']    = $this->input->post("cd_tarefa", TRUE);
		$args['ds_obs']       = $this->input->post("ds_obs", TRUE);
		
		$this->tarefas_model->salvar_nao_conforme($result, $args);
		
		redirect("atividade/tarefa/cadastro/".intval($args["cd_atividade"])."/".$args['cd_tarefa'], "refresh");	
	}
	
	function excluir_tarefa($cd_atividade, $cd_tarefa)
	{
		$result = null;
		$data = Array();
		$args = Array();
		
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		
		$this->tarefas_model->excluir_tarefa($result, $args);

		redirect("atividade/atividade_atendimento/index/".intval($args["cd_atividade"]), "refresh");
	}
	
	function imprimir($cd_atividade, $cd_tarefa)
	{
		$args = Array();
        $data = Array();
        $result = null;
		
		$args['cd_atividade'] = $cd_atividade;
		$args['cd_tarefa']    = $cd_tarefa;
		
		$this->tarefas_model->tarefa( $result, $args );
		$row = $result->row_array();
		
		$args['codigo'] = $row['codigo'];
		
		$this->tarefas_model->anexos( $result, $args );
		$arr_anexos = $result->result_array();
		
		$this->load->plugin('fpdf');

        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = true;
        $ob_pdf->header_logo = true;
        $ob_pdf->header_titulo = true;
        $ob_pdf->header_titulo_texto = "Tarefa";
		
		$ob_pdf->AddPage();
		
		$x = 64;
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Atividade / Tarefa: ");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['cd_atividade'].'/'.$row['cd_tarefa'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Nome do programa:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['programa'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Tipo da tarefa:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['nome_tarefa'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Analista:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['analista'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Programador:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['programador'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Prioridade:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['ds_prioridade'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Data de Início Prevista:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['dt_inicio_prev'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Data de Término Prevista:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['dt_fim_prev'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Data de início da tarefa:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['dt_inicio_prog'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Data de fim da tarefa:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['dt_fim_prog'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Data de Acordo:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
		$ob_pdf->MultiCell(0, 5, $row['dt_ok_anal'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Resumo:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY(9, $ob_pdf->getY()+3);
		$ob_pdf->MultiCell(0, 5, $row['resumo'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Objetivo:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY(9, $ob_pdf->getY()+3);
		$ob_pdf->MultiCell(0, 5, $row['descricao'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Funcionalidades/restrições da seleção (regras):");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY(9, $ob_pdf->getY()+3);
		$ob_pdf->MultiCell(0, 5, $row['casos_testes'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Funções ou procedimentos a serem utilizados:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY(9, $ob_pdf->getY()+3);
		$ob_pdf->MultiCell(0, 5, $row['tabs_envolv'], '0', 'L');
				
		if(strtoupper(trim($row['fl_tarefa_tipo'])) == 'F')
		{
			$this->tarefas_model->lovs( $result, $args );
		    $arr_lovs = $result->result_array();
			
			$this->tarefas_model->tabelas( $result, $args );
		    $arr_tabelas = $result->result_array();
			
			$this->tarefas_model->ordenacao( $result, $args );
		    $arr_ordenacao = $result->result_array();
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Nome da Tela (Forms):");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_nome_tela'], '0', 'L');
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Menu:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_menu'], '0', 'L');
								
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY());
			$ob_pdf->Text(10, $ob_pdf->getY(), "Lovs:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setY($ob_pdf->getY()+2);
			
			$ob_pdf->SetWidths(array(35,51,52,52));
			$ob_pdf->SetAligns(array('C', 'C', 'C', 'C'));
			$ob_pdf->Row(array("Seq", "Tabela", "Campo Origem", "Campo Destino"));
			$ob_pdf->SetAligns(array('L', 'L', 'L', 'L'));
			
			foreach($arr_lovs as $item)
			{			
				$ob_pdf->Row(array( $item['ds_seq'], 
				                    $item['ds_tabela'], 
									$item['ds_campo_ori'],
									$item['ds_campo_des']
				));
			}
			
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY());
			$ob_pdf->Text(10, $ob_pdf->getY(), "Detalhes da tela:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setY($ob_pdf->getY()+2);
									
			$ob_pdf->SetWidths(array(52,31,26,34,17,30));
			$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
			$ob_pdf->Row(array("Banco.Tabela", "Campo", "Tipo Campo", "Val. de Domínio", "Ds/En", "Prompt"));
			$ob_pdf->SetAligns(array('L', 'L', 'C', 'L', 'C', 'L'));
			$ob_pdf->SetFont('Courier','',10);
			
			foreach($arr_tabelas as $item)
			{			
				$ob_pdf->Row(array( $item['ds_banco'].'.'.$item['ds_tabela'], 
				                    $item['ds_campo'], 
									$item['fl_campo'],
									$item['ds_vl_dominio'],
									$item['fl_campo_de'],
									$item['ds_label']
				));
			}
			
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY());
			$ob_pdf->Text(10, $ob_pdf->getY(), "Ordenado por:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setY($ob_pdf->getY()+2);
			
			$ob_pdf->SetWidths(array(64,63,63));
			$ob_pdf->SetAligns(array('C', 'C', 'C'));
			$ob_pdf->Row(array("Banco", "Tabela", "Campo"));
			
			foreach($arr_ordenacao as $item)
			{			
				$ob_pdf->Row(array( $item['ds_banco'],
				                    $item['ds_tabela'], 
				                    $item['ds_campo']
				));
			}
			
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
		}
		else if(strtoupper(trim($row['fl_tarefa_tipo'])) == 'R')
		{
			$this->tarefas_model->paremetros( $result, $args );
		    $arr_paremetros = $result->result_array();
			
			$this->tarefas_model->tarefas_reports( $result, $args );
		    $arr_tarefas = $result->result_array();
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Menu:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_menu'], '0', 'L');
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Orientação:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_orientacao'], '0', 'L');
					
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY());
			$ob_pdf->Text(10, $ob_pdf->getY(), "Parâmetros:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setY($ob_pdf->getY()+2);
						
			$ob_pdf->SetWidths(array(64,63,63));
			$ob_pdf->SetAligns(array('C', 'C', 'C'));
			$ob_pdf->Row(array("Nome Campo", "Tipo Campo", "Ordem"));
			$ob_pdf->SetAligns(array('L', 'L', 'L'));
			
			foreach($arr_paremetros as $item)
			{			
				$ob_pdf->Row(array( $item['ds_campo'], 
				                    $item['ds_tipo'], 
									$item['nr_ordem']
				));
			}
			
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY());
			$ob_pdf->Text(10, $ob_pdf->getY(), "Detalhes do relatório:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setY($ob_pdf->getY()+2);
			
			$ob_pdf->SetWidths(array(48,48,47,47));
			$ob_pdf->SetAligns(array('C', 'C', 'C', 'C'));
			$ob_pdf->Row(array("Banco", "Tabela", "Campo", "Label"));
			$ob_pdf->SetAligns(array('L', 'L', 'L', 'L'));
			
			foreach($arr_tarefas as $item)
			{			
				$ob_pdf->Row(array( $item['ds_banco'], 
				                    $item['ds_tabela'], 
									$item['ds_campo'],
									$item['ds_label']
				));
			}
			
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
		}
		else if(strtoupper(trim($row['fl_tarefa_tipo'])) == 'A')
		{
			$this->tarefas_model->tipos( $result, $args );
		    $arr_tipos = $result->result_array();
		
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Nome do Processo:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_nome_tela'], '0', 'L');
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Diretório:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_dir'], '0', 'L');
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Nome:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_nome_arq'], '0', 'L');
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Delimitador:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_delimitador'], '0', 'L');
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Largura Fixa:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_largura'], '0', 'L');
			
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY());
			$ob_pdf->Text(10, $ob_pdf->getY(), "Tipos:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setY($ob_pdf->getY()+2);
			
			foreach($arr_tipos as $item)
			{			
				$args['cd_tarefas_layout'] = $item['cd_tarefas_layout'];
				
				$ob_pdf->SetWidths(array(190));
				$ob_pdf->SetAligns(array('C'));
				
				$ob_pdf->Row(array("Tipo: ".$item['ds_tipo']));
				
				$ob_pdf->SetWidths(array(38,38,38,38,38));
				$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
				$ob_pdf->Row(array("Nome Campo", "Tamanho Campo", "Característica", "Formato Campo", "Definição"));
				$ob_pdf->SetAligns(array('L', 'L', 'L', 'L'));
				
				$this->tarefas_model->tipo_campos( $result, $args );
				$arr_tipo_campos = $result->result_array();
				
				foreach($arr_tipo_campos as $item2)
				{
					$ob_pdf->Row(array( $item2['ds_nome'], 
				                        $item2['ds_tamanho'], 
					  			        $item2['ds_caracteristica'],
									    $item2['ds_formato'],
										$item2['ds_definicao']
				    ));
				}
			}
			
			$ob_pdf->setXY(10,$ob_pdf->getY()+5);
			
			$ob_pdf->SetFont('Courier', 'B', 10);
			$ob_pdf->setY($ob_pdf->getY()+5);
			$ob_pdf->Text(10, $ob_pdf->getY(), "Ordenado por:");
			$ob_pdf->SetFont('Courier', '', 10);
			$ob_pdf->setXY($x, $ob_pdf->getY()-3.5);
			$ob_pdf->MultiCell(0, 5, $row['ds_ordem'], '0', 'L');
		}
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY()+5);
		$ob_pdf->Text(10, $ob_pdf->getY(), "Considerações gerais e complementos:");
		$ob_pdf->SetFont('Courier', '', 10);
		$ob_pdf->setXY(9, $ob_pdf->getY()+3);
		$ob_pdf->MultiCell(0, 5, $row['observacoes'], '0', 'L');
		
		$ob_pdf->SetFont('Courier', 'B', 10);
		$ob_pdf->setY($ob_pdf->getY());
		$ob_pdf->Text(10, $ob_pdf->getY(), "Anexos:");
		$ob_pdf->SetFont('Courier', '', 10);
		
		$ob_pdf->setY($ob_pdf->getY()+2);
		
		$ob_pdf->SetWidths(array(140,50));
		$ob_pdf->SetAligns(array('C', 'C'));
		$ob_pdf->Row(array("Nome", "Dt Inclusão"));
		$ob_pdf->SetAligns(array('L', 'C'));
		
		foreach($arr_anexos as $item)
		{			
			$ob_pdf->Row(array( $item['arquivo_nome'], 
								$item['dt_inclusao']
			));
		}
		
		$ob_pdf->Output();
	}
}
?>