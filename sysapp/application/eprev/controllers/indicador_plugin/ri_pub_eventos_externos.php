<?php
class ri_pub_eventos_externos extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::RI_PUBLICO_PRESENTE_EM_EVENTOS_EXTERNOS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model( 'indicador_plugin/ri_pub_eventos_externos_model' );
    }
		
	function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			#### FECHA PERIODO ENCERRADO PARA ABRIR NOVO ####
			$ar_periodo = indicador_periodo_aberto();
			$ar_tabela  = indicador_tabela_aberta(intval($this->enum_indicador));
			if(intval($ar_periodo[0]["cd_indicador_periodo"]) != intval($ar_tabela[0]["cd_indicador_periodo"]))
			{
				$qr_sql = indicador_db::fechar_periodo_para_indicador(intval($ar_tabela[0]["cd_indicador_tabela"]), $this->session->userdata('codigo'));
				$this->db->query($qr_sql);
			}		
		
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/ri_pub_eventos_externos/index',$data);
		}
    }
	
	function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_7'] = $this->label_7;
	        
			$tabela = indicador_tabela_aberta(  $this->enum_indicador  );
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->ri_pub_eventos_externos_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/ri_pub_eventos_externos/index_result', $data);
        }
    }
	
	function cadastro($cd_ri_pub_eventos_externos = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_7'] = $this->label_7;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_ri_pub_eventos_externos'] = $cd_ri_pub_eventos_externos;
			
			if(intval($args['cd_ri_pub_eventos_externos']) == 0)
			{
				$this->ri_pub_eventos_externos_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_ri_pub_eventos_externos'] = $args['cd_ri_pub_eventos_externos'];
				$data['row']['nr_valor_1']                 = "";
				$data['row']['nr_valor_2']                 = "";
				$data['row']['nr_valor_3']                 = "";
				$data['row']['fl_media']                   = "";
				$data['row']['observacao']                 = "";
				$data['row']['mes_referencia']             = (isset($arr['mes_referencia']) ? $arr['mes_referencia'] : "");
				$data['row']['ano_referencia']             = (isset($arr['ano_referencia']) ? $arr['ano_referencia'] : "");
				$data['row']['nr_meta']                    = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->ri_pub_eventos_externos_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/ri_pub_eventos_externos/cadastro', $data);
		}
	}
	
	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'AC') || indicador_db::verificar_permissao(usuario_id(), 'GE'))
		{
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_ri_pub_eventos_externos'] = $this->input->post('cd_ri_pub_eventos_externos', true);
			$args["dt_referencia"]              = $this->input->post("dt_referencia", true);
			$args["cd_indicador_tabela"]        = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"]                   = $this->input->post("fl_media", true);
			$args["nr_valor_1"]                 = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                 = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_valor_3"]                 = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_meta"]                    = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                 = $this->input->post("observacao", true);
			$args["cd_usuario"]                 = $this->session->userdata('codigo');
			
			$this->ri_pub_eventos_externos_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/ri_pub_eventos_externos", "refresh");
		}
	}
	
	function excluir($cd_ri_pub_eventos_externos)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_ri_pub_eventos_externos'] = $cd_ri_pub_eventos_externos;
			$args["cd_usuario"]                 = $this->session->userdata('codigo');
			
			$this->ri_pub_eventos_externos_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/ri_pub_eventos_externos", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'AC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;

			$tabela = indicador_tabela_aberta( $this->enum_indicador );
			
			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; 
			
			$this->ri_pub_eventos_externos_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador = array();
			$linha = 0;
			
			$media_ano = array();
			
			foreach( $collection as $item )
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
				
				$nr_valor_1      = $item["nr_valor_1"];
				$nr_valor_2      = $item["nr_valor_2"];
				$nr_valor_3      = $item["nr_valor_3"];
				$nr_meta         = $item['nr_meta'];
				$nr_percentual_f = $item['nr_percentual_f'];
				
				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
				{
					$media_ano[] = $nr_percentual_f;
				}
				
				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
				$indicador[$linha][3] = app_decimal_para_php($nr_valor_3);
				$ar_tendencia[] = $nr_percentual_f;
				$indicador[$linha][4] = app_decimal_para_php($nr_percentual_f);
				$indicador[$linha][5] = app_decimal_para_php($nr_meta);
				$indicador[$linha][7] = $observacao;

				$linha++;
			}
			
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][6] = $tend[$i];
			}
			
			$linha_sem_media = $linha;

				if(sizeof($media_ano)>0)
				{
					$media = 0;
					
					foreach( $media_ano as $valor )
					{
						$media += $valor;
					}

					$media = number_format(( $media / sizeof($media_ano) ),2 );
				}
				
				$linha = 1;
				
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 0 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 0 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]), 'left');
                    
					$linha++;
				}
				
				// gerar gráfico
                $coluna_para_ocultar='5,6';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::BARRA_MULTIPLO,
					'4,4,0,0;5,5,0,0;6,6,0,0',
					"0,0,1,$linha_sem_media",
					"4,4,1,$linha_sem_media;5,5,1,$linha_sem_media;6,6,1,$linha_sem_media-linha",
					usuario_id(),
					$coluna_para_ocultar,
                    1,
                    2
				);

			$this->db->query($sql);
		}
	}
}
?>