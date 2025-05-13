<?php
class cadastro extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin() ;
		
		$this->load->model('projetos/indicador_model');
    }
	
    function index()
    {
		if(gerencia_in(array('GTI','GC')))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$this->indicador_model->grupos($result, $args);
			$data['ar_grupos'] = $result->result_array();

			$this->indicador_model->controles($result, $args);
			$data['ar_controle'] = $result->result_array();		

			$this->indicador_model->tipos($result, $args);
			$data['ar_tipo'] = $result->result_array();				

	        $this->load->view('indicador/cadastro/index', $data);
		}
		else
		{
			exibir_mensagem('Sem permissão para exibir essa página.');
		}
    }

    function listar()
    {
        if(usuario_responsavel_indicador(usuario_id()))
        {
			$result = null;
			$args = array();
			$data = array();
	
			$args['cd_indicador_grupo']    = $this->input->post('cd_indicador_grupo', TRUE);
			$args['fl_igp'] 			   = $this->input->post('fl_igp', TRUE);
			$args['fl_poder'] 			   = $this->input->post('fl_poder', TRUE);
			$args['cd_processo']           = $this->input->post('cd_processo', TRUE);
			$args['cd_indicador_controle'] = $this->input->post('cd_indicador_controle', TRUE);
			$args['cd_tipo']               = $this->input->post('cd_tipo', TRUE);

			manter_filtros($args);

	        $this->indicador_model->listar( $result, $args );
			$data['collection'] = $result->result_array();

	        $this->load->view('indicador/cadastro/index_result', $data);
        }
    }

	function detalhe($cd_indicador = 0)
	{ 
		if(usuario_responsavel_indicador(usuario_id()))
		{
			if( intval($cd_indicador)==0 || gerencia_in(array('GTI','GC')) )
			{
				$result = null;
				$args = array();
				$data = array();
				
				$this->indicador_model->gerencia( $result,  $args );
				$data['divisao'] = $result->result_array();
				
				$args['cd_indicador'] = $cd_indicador;
				
				if(intval($args['cd_indicador']) == 0)
				{
					$data['row'] = array(
						'cd_indicador'                => '',
						'cd_indicador_grupo'          => '',
						'cd_processo'                 => '',
						'cd_usuario_responsavel'      => '',
						'ds_indicador'                => '',
						'cd_responsavel'              => '',
						'cd_substituto'               => '',
						'ds_dimensao_qualidade'       => '',
						'nr_ordem'                    => '',
						'ds_formula'                  => '',
						'dt_pronto'                   => '',
						'dt_exclusao'                 => '',
						'cd_usuario_exclusao'         => '',
						'cd_indicador_controle'       => '',
						'dt_limite_atualizar'         => '',
						'cd_indicador_unidade_medida' => '',
						'cd_tipo'                     => '',
						'ds_missao'                   => '',
						'ds_meta'                     => '',
						'plugin_nome'                 => '',
						'plugin_tabela'               => '',
						'tp_analise'                  => '', 
						'cd_gerencia'                 => '',
						'fl_periodo'                  => '',
						'fl_igp'                      => '',
						'fl_poder'                    => '',
						'qt_periodo_anterior'         => ''
					);
				}
				else
				{
					$this->indicador_model->carregar($result, $args);
					$data['row'] = $result->row_array();
				}

				$args['cd_usuario'] = $data['row']["cd_responsavel"];
				$this->indicador_model->usuarioCombo($result, $args);
				$data['ar_usuario_responsavel'] = $result->result_array();						
				
				$args['cd_usuario'] = $data['row']["cd_substituto"];
				$this->indicador_model->usuarioCombo($result, $args);
				$data['ar_usuario_substituto'] = $result->result_array();				
				
				$this->load->view('indicador/cadastro/cadastro', $data);
			}
			else
			{
				exibir_mensagem('O Indicador de Desempenho escolhido não é de sua Responsabilidade, você não pode acessá-lo nesse módulo de cadastro e manutenção.');
			}
		}
	}

	function salvar()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			  
			$args['cd_indicador']                = $this->input->post('cd_indicador', true);
			$args['cd_indicador_grupo']          = $this->input->post('cd_indicador_grupo',TRUE);
			$args['cd_processo']                 = $this->input->post('cd_processo',TRUE);
			$args['ds_indicador']                = $this->input->post('ds_indicador',TRUE);
			$args['cd_responsavel']              = $this->input->post('cd_responsavel',TRUE);
			$args['cd_substituto']               = $this->input->post('cd_substituto',TRUE);
			$args['ds_dimensao_qualidade']       = $this->input->post('ds_dimensao_qualidade',true);
			$args['nr_ordem']                    = $this->input->post('nr_ordem',TRUE);
			$args['cd_indicador_controle']       = $this->input->post('cd_indicador_controle',TRUE);
			$args['dt_limite_atualizar']         = $this->input->post('dt_limite_atualizar',TRUE);
			$args['ds_formula']                  = $this->input->post('ds_formula',TRUE);
			$args['cd_indicador_unidade_medida'] = $this->input->post('cd_indicador_unidade_medida',true);
			$args['ds_meta']                     = $this->input->post('ds_meta', true);
			$args['cd_indicador']                = $this->input->post('cd_indicador',true);
            $args['cd_tipo']                     = $this->input->post('cd_tipo',true);
            $args['ds_missao']                   = $this->input->post('ds_missao',true);
            $args['plugin_nome']                 = $this->input->post('plugin_nome',true);
            $args['plugin_tabela']               = $this->input->post('plugin_tabela',true);
            $args['tp_analise']                  = $this->input->post('tp_analise',true);
            $args['cd_gerencia']                 = $this->input->post('cd_gerencia',true);
            $args['fl_periodo']                  = $this->input->post('fl_periodo',true);
            $args['fl_igp']                  	 = $this->input->post('fl_igp',true);
            $args['fl_poder']                  	 = $this->input->post('fl_poder',true);
            $args['qt_periodo_anterior']         = $this->input->post('qt_periodo_anterior',true);
			
			$args['cd_usuario_responsavel']      = $this->session->userdata('codigo');

			$cd_indicador = $this->indicador_model->salvar($result, $args);
			
			redirect( 'indicador/cadastro/detalhe/'.$cd_indicador, 'refresh' );	
		}
	}

	function excluir($cd_indicador)
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador'] = $cd_indicador;
			$args['cd_usuario']   = $this->session->userdata('codigo');
			
			$this->indicador_model->excluir($result, $args);
		
			redirect( 'indicador/cadastro', 'refresh' );
		}
	}

	function carregar_tabelas_ajax()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador'] = $this->input->post('cd_indicador');

			$count = 0;
			
			$this->indicador_model->periodos_abertos($result, $args);
			$rows = $result->result_array();

			echo '<b>Período Aberto:</b>'.br(2);
			
			if($rows)
			{
				foreach( $rows as $row )
				{
					echo "<div style='margin:0 0 3 0;'>";
					echo anchor( "indicador/tabela/index/".$row['cd_indicador_tabela'], $row['ds_periodo'], array('style'=>'text-decoration:underline;') );
					echo " - ";
					echo "<span style='color:red;'>" .  anchor("indicador/cadastro/fechar_periodo_tabela/".$row['cd_indicador_tabela'], 'Fechar período', array('style'=>'color:red;text-decoration:underline;', 'onclick'=>"return confirm('Fechar o período dessa tabela?')"))  . "</span>";
					echo "</div>";

					$count++;
				}
			}
			else
			{
				$this->indicador_model->novo_periodo($result, $args);
				$rows = $result->result_array();
				
				echo '<i>Nenhuma tabela criada em período aberto.</i>'.br();
				
				if($rows)
				{
					echo br();
					
					foreach( $rows as $row )
					{
						echo comando('criar_tabela_btn', 'Criar uma tabela para: '.$row['ds_periodo'], ' location.href="'.site_url('indicador/cadastro/criar_tabela/'.$args['cd_indicador'].'/'.$row['cd_indicador_periodo']).'" ' ).br();
					}
				}
				echo br(2);
			}

			$this->indicador_model->periodos_fechados($result, $args);
			$rows = $result->result_array();

			echo br().'<b>Período Fechado:</b>'.br(2);
			
			if($rows)
			{
				foreach( $rows as $row )
				{
					echo "<div style='margin:0 0 3 0;'>";
					echo anchor( "indicador/apresentacao/detalhe/".$row['cd_indicador_tabela'], $row['ds_periodo'], array('style'=>'text-decoration:underline;') );
					echo "</div>";

					$count++;
				}
			}
			else
			{
				echo '<i>Nenhuma tabela em período fechado.</i>'.br(2);
			}
		}
	}
	
	function fechar_periodo_tabela($cd_indicador_tabela)
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
		
			$args['cd_indicador_tabela'] = $cd_indicador_tabela;
			$args['cd_usuario']          = $this->session->userdata('codigo');

			$this->indicador_model->fechar_periodo_tabela($result, $args);

			redirect( "indicador/cadastro" );
		}
	}

	function criar_tabela($cd_indicador, $cd_indicador_periodo)
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			if( intval($cd_indicador) > 0 and intval($cd_indicador_periodo) > 0 )
			{
				$result = null;
				$args = array();
				$data = array();
				
				$args['cd_indicador']         = $cd_indicador;
				$args['cd_indicador_periodo'] = $cd_indicador_periodo;
				$args['cd_usuario']           = $this->session->userdata('codigo');
			
				$cd_indicador_tabela = $this->indicador_model->criar_tabela($result, $args);
			
				redirect( "indicador/tabela/index/".$cd_indicador_tabela );
			}
		}
	}
	
	function rotulos($cd_indicador)
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador'] = $cd_indicador;
			$data['cd_indicador'] = $cd_indicador;
			
			$this->indicador_model->carregar($result, $args);
			$data['ar_indicador'] = $result->row_array();			
			
			$this->indicador_model->getColunasTabela( $result, $args );
			$data['ar_coluna_tabela'] = $result->result_array();
			
			$this->load->view('indicador/cadastro/rotulos', $data);
		}
	}
	
	function listar_rotulos()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador']            = $this->input->post('cd_indicador', true);
			$args['indicador_plugin_tabela'] = $this->input->post('indicador_plugin_tabela', true);
			
			
			$this->indicador_model->listar_rotulos( $result, $args );
			$ar_reg = $result->result_array();
			#$data['collection'] = $result->result_array();
			
			$ar_ret = Array();
			foreach($ar_reg as $item)
			{
				$valor = "";
				if(trim($item['ds_coluna_tabela']) != "")
				{
					$args['ds_coluna_tabela'] = $item['ds_coluna_tabela'];
		
					$this->indicador_model->rotuloValor( $result, $args );
					$row = $result->row_array();
				
					$valor = $row["valor"];
				}
				
				$item["ultimo_valor"] = $valor;
				
				$ar_ret[] = $item;
			}
			$data['collection'] = $ar_ret;

	        $this->load->view('indicador/cadastro/rotulos_result', $data);
		}
	}
	
	function salvar_rotulo()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador']       = $this->input->post('cd_indicador', true);
			$args['cd_indicador_label'] = $this->input->post('cd_indicador_label', true);
			$args['id_label']           = $this->input->post('id_label', true);
			$args['ds_label']           = $this->input->post('ds_label', true);
			
			$args['ds_coluna_tabela']   = $this->input->post('ds_coluna_tabela', true);
			$args['ds_integracao_sa']   = $this->input->post('ds_integracao_sa', true);
			$args['ds_modelo_sa']       = $this->input->post('ds_modelo_sa', true);
			$args['ds_tipo_sa']         = $this->input->post('ds_tipo_sa', true);
			
			$args['cd_usuario']         = $this->session->userdata('codigo', true);
			
			$this->indicador_model->salvar_rotulo( $result, $args );
			
			redirect( "indicador/cadastro/rotulos/".$args['cd_indicador'] );
		}
	}
	
	function carrega_rotulos()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador_label'] = $this->input->post('cd_indicador_label', true);
			
			$this->indicador_model->carrega_rotulo( $result, $args );
			$row = $result->row_array();
			
			$row = array_map("arrayToUTF8", $row);	
			
			echo json_encode($row);
		}
	}
	
	function verifica_nr_label()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador']       = $this->input->post('cd_indicador', true);
			$args['cd_indicador_label'] = $this->input->post('cd_indicador_label', true);
			$args['id_label']           = $this->input->post('id_label', true);
			
			$this->indicador_model->verifica_nr_label( $result, $args );
			$row = $result->row_array();
			
			echo json_encode($row);
		}
	}
	
	function excluir_rotulo($cd_indicador, $cd_indicador_label)
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador']       = $cd_indicador;
			$args['cd_indicador_label'] = $cd_indicador_label;
			$args['cd_usuario']         = $this->session->userdata('codigo', true);
			
			$this->indicador_model->excluir_rotulo( $result, $args );
			
			redirect( "indicador/cadastro/rotulos/".$cd_indicador );
		}
	}
	
	function rotuloValor()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['indicador_plugin_tabela'] = $this->input->post('indicador_plugin_tabela', true);
			$args['ds_coluna_tabela']        = $this->input->post('ds_coluna_tabela', true);
	
			$this->indicador_model->rotuloValor( $result, $args );
			$row = $result->row_array();
			
			echo json_encode(array('valor' => $row["valor"]));
		}
	}	
	
	function saveConfigSA()
	{
		if(usuario_responsavel_indicador(usuario_id()))
		{
			$result = null;
			$args = array();
			$data = array();
			
			$args['cd_indicador'] = $this->input->post('cd_indicador', true);
			$args['fl_config_sa'] = $this->input->post('fl_config_sa', true);
			$args['cd_usuario']   = $this->session->userdata('codigo', true);
			
			$this->indicador_model->saveConfigSA( $result, $args );
		}
	}	
	
}
?>