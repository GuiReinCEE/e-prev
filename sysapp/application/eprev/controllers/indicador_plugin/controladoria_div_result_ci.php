<?php
class Controladoria_div_result_ci extends Controller
{	
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_DIV_RESULTADO_CARTEIRA_INVEST);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/controladoria_div_result_ci_model');
    }
	
	function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/controladoria_div_result_ci/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_4'] = $this->label_4;
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->controladoria_div_result_ci_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/controladoria_div_result_ci/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function cadastro($cd_controladoria_div_result_ci = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_controladoria_div_result_ci'] = $cd_controladoria_div_result_ci;
			
			if(intval($args['cd_controladoria_div_result_ci']) == 0)
			{
				$this->controladoria_div_result_ci_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_controladoria_div_result_ci'] = $args['cd_controladoria_div_result_ci'];
				$data['row']['nr_valor_1']                                  = "";
				$data['row']['fl_media']                                    = "";
				$data['row']['observacao']                                  = "";
				$data['row']['dt_referencia']                               = (isset($arr['dt_referencia']) ? $arr['dt_referencia'] : "");
				$data['row']['nr_meta']                                     = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->controladoria_div_result_ci_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/controladoria_div_result_ci/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_controladoria_div_result_ci'] = intval($this->input->post('cd_controladoria_div_result_ci', true));
			$args["cd_indicador_tabela"]                         = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                               = $this->input->post("dt_referencia", true);
			$args["fl_media"]                                    = $this->input->post("fl_media", true);
			$args["nr_valor_1"]                                  = $this->input->post("nr_valor_1", true);
			$args["nr_meta"]                                     = $this->input->post("nr_meta", true);
            $args["observacao"]                                  = $this->input->post("observacao", true);
			$args["cd_usuario"]                                  = $this->session->userdata('codigo');

			$this->controladoria_div_result_ci_model->salvar($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_div_result_ci/cadastro", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_controladoria_div_result_ci)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_controladoria_div_result_ci'] = $cd_controladoria_div_result_ci;
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->controladoria_div_result_ci_model->excluir($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_div_result_ci", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->controladoria_div_result_ci_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Média de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_valor_1 = $item["nr_valor_1"];
					$nr_meta    = $item["nr_meta"];

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$media_ano[] = $nr_valor_1;
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $nr_valor_1;
					$indicador[$linha][2] = app_decimal_para_php($nr_meta);
					$indicador[$linha][4] = $observacao;

					$ar_tendencia[] = $nr_valor_1;
					
					$linha++;
				}
			}	
				
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );

			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][3] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}
			
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = number_format(($media / sizeof($media_ano)),2);
				$indicador[$linha][2] = app_decimal_para_php($nr_meta);
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2 );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, utf8_encode($indicador[$i][4]) );
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='3';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'1,1,0,0;2,2,0,0;3,3,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media-linha;3,3,1,$linha_sem_media-linha",
				usuario_id(),
				$coluna_para_ocultar,
				1,
				2
			);

			$this->db->query($sql);
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->controladoria_div_result_ci_model->listar( $result, $args );
			$collection = $result->result_array();

			$media_ano            = array();
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media                = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					
					$media_ano[] = $item['nr_valor_1'];
					$nr_meta     += $item['nr_meta'];
				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}
			
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_valor_1']          = number_format(($media / sizeof($media_ano)), 2);
				$args["nr_meta"]             = number_format(($nr_meta / sizeof($media_ano)), 2);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->controladoria_div_result_ci_model->atualiza_fechar_periodo($result, $args);
			}

			$this->controladoria_div_result_ci_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}

		redirect("indicador_plugin/controladoria_div_result_ci", "refresh");
	}

}
?>