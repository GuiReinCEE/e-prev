<?php
class juridico_inden_deman extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_CUSTO_MEDIO_DE_INDENIZACAO_POR_DEMANDANTE);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_inden_deman_model' );
    }	

	function index()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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

	        $this->load->view('indicador_plugin/juridico_inden_deman/index',$data);
		}
    }

	function listar()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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
			$data['label_8'] = $this->label_8;
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_inden_deman_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_inden_deman/partial_result', $data);
        }
    }

	function detalhe($cd_juridico_inden_deman = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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
			$data['label_8'] = $this->label_8;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_inden_deman'] = intval($cd_juridico_inden_deman);
			
			if(intval($args['cd_juridico_inden_deman']) == 0)
			{
				$this->juridico_inden_deman_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_juridico_inden_deman'] = $args['cd_juridico_inden_deman'];
				$data['row']['vl_indenizacao'] = "";
				$data['row']['nr_liquidada']   = "";
				$data['row']['nr_demandante']  = "";
				$data['row']['fl_media']       = "";
				$data['row']['observacao']     = "";
				$data['row']['dt_referencia']  = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']        = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->juridico_inden_deman_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/juridico_inden_deman/detalhe', $data);
		}
	}	
	
	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_inden_deman'] = intval($this->input->post('cd_juridico_inden_deman', true));
			$args["cd_indicador_tabela"]    = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]          = $this->input->post("dt_referencia", true);
			$args["fl_media"]               = $this->input->post("fl_media", true);
			$args["vl_indenizacao"]         = app_decimal_para_db($this->input->post("vl_indenizacao", true));
			$args["nr_liquidada"]           = app_decimal_para_db($this->input->post("nr_liquidada", true));
			$args["nr_demandante"]          = app_decimal_para_db($this->input->post("nr_demandante", true));
			$args["nr_meta"]                = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]             = $this->input->post("observacao", true);
			$args["cd_usuario"]             = $this->session->userdata('codigo');

			$this->juridico_inden_deman_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_inden_deman", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function excluir($cd_juridico_inden_deman = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_inden_deman'] = intval($cd_juridico_inden_deman);
			$args["cd_usuario"]              = $this->session->userdata('codigo');
			
			$this->juridico_inden_deman_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_inden_deman", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
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
			$data['label_8'] = $this->label_8;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->juridico_inden_deman_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador            = array();
			$linha                = 0;
			$ar_tendencia         = array();
			$contador_ano_atual   = 0;
			$vl_indenizacao_total = 0;
			$nr_liquidada_total   = 0;
			$nr_demandante_total  = 0;
			$nr_meta              = 0;			
			$fl_periodo           = false;
			
			if(intval($tabela[0]['qt_periodo_anterior']) == -1)
			{
				$tabela[0]['qt_periodo_anterior'] = 0;
			}
			else if(intval($tabela[0]['qt_periodo_anterior']) == 0)
			{
				$fl_periodo = true;
			}
			
			foreach($collection as $item)
			{
				if((intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - intval($tabela[0]['qt_periodo_anterior'])) OR ($fl_periodo))
				{
					$nr_meta = $item['nr_meta'];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Resultado de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$vl_indenizacao_total += $item["vl_indenizacao"];
						$nr_liquidada_total   += $item["nr_liquidada"];
						$nr_demandante_total  += $item["nr_demandante"];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item["vl_indenizacao"];
					$indicador[$linha][2] = $item["nr_liquidada"];
					$indicador[$linha][3] = $item["nr_demandante"];
					$indicador[$linha][4] = $item["vl_indenizacao_liquidada"];
					$indicador[$linha][5] = $item["vl_indenizacao_demandante"];
					$indicador[$linha][6] = $nr_meta;
					$indicador[$linha][7] = 0; #tendencia
					$indicador[$linha][8] = $item["observacao"];
					
					$ar_tendencia[] = $item["vl_indenizacao_liquidada"]; 
					
					$linha++;
				}
			}	
				
			#### LINHA DE TENDÊNCIA - CURVA LOGARITMICA ####
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][7] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0] = 'Resultado de '.intval($tabela[0]['nr_ano_referencia']);
				$indicador[$linha][1] = $vl_indenizacao_total;
				$indicador[$linha][2] = $nr_liquidada_total;
				$indicador[$linha][3] = $nr_demandante_total;
				$indicador[$linha][4] = ($vl_indenizacao_total / $nr_liquidada_total);
				$indicador[$linha][5] = ($vl_indenizacao_total / $nr_demandante_total);
				$indicador[$linha][6] = $nr_meta;
				$indicador[$linha][7] = '';
				$indicador[$linha][8] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode(nl2br($indicador[$i][8])), 'left');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = "6,7";
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar
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
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->juridico_inden_deman_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador_ano_atual   = 0;
			$vl_indenizacao_total = 0;
			$nr_liquidada_total   = 0;
			$nr_demandante_total  = 0;
			$nr_meta              = 0;			
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_meta = $item['nr_meta'];

					if(trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$vl_indenizacao_total += $item["vl_indenizacao"];
						$nr_liquidada_total   += $item["nr_liquidada"];
						$nr_demandante_total  += $item["nr_demandante"];
					}
				}
			}

			// gravar a resultado do período
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['vl_indenizacao']      = floatval($vl_indenizacao_total);
				$args['nr_liquidada']        = floatval($nr_liquidada_total);
				$args['nr_demandante']       = floatval($nr_demandante_total);
				$args["nr_meta"]             = floatval($nr_meta);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->juridico_inden_deman_model->atualiza_fechar_periodo($result, $args);
			}

			$this->juridico_inden_deman_model->fechar_periodo($result, $args);

		}

		redirect("indicador_plugin/juridico_inden_deman", "refresh");
	}		

	function fechar_periodoxxxx()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() AND (indicador_db::verificar_permissao(usuario_id(), 'AJ') OR indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/juridico_inden_deman_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->juridico_inden_deman_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$contador = sizeof($collection);
				$media_ano=array();
                $nr_valor_f1 = 0;
                $nr_valor_f2 = 0;
                $nr_valor_f3 = 0;
				foreach( $collection as $item )
				{
					$nr_meta = $item["nr_meta"];

					if( $item['fl_media']=='S' )
					{
						$referencia = " Média de " . $item['ano_referencia'];

						$nr_valor_1 = '';
						$nr_valor_2 = '';
                        $nr_valor_3 = '';
						$nr_percentual_f = $item['nr_percentual_f'];
					}
					else
					{
						$referencia = $item['mes_referencia'];

						$nr_valor_1 = $item["nr_valor_1"];
						$nr_valor_2 = $item["nr_valor_2"];
                        $nr_valor_3 = $item["nr_valor_3"];
						$nr_percentual_f = '';

                        $nr_valor_f1 += $nr_valor_1;
                        $nr_valor_f2 += $nr_valor_2;
                        $nr_valor_f3 += $nr_valor_3;
                        
						if($nr_percentual_f=='')
                        {
                            if($nr_valor_2 > 0){
                                $nr_percentual_f = (floatval($nr_valor_1)/floatval($nr_valor_2));
                            } else {
                                $nr_percentual_f = '0';
                            }
                        }
					}

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $nr_percentual_f;
					}
				}

				$sql="";

				// gravar a média do período
				if(sizeof($media_ano)>0)
				{
					
					$media = $nr_valor_f1/$nr_valor_f3;

					$sql.=sprintf(" INSERT INTO indicador_plugin.juridico_inden_deman
					( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_percentual_f, nr_meta, fl_media ) 
					VALUES ( '%s/01/01',current_timestamp,%s, %s, %s, 'S' ); "
					, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($media), floatval( app_decimal_para_db($nr_meta) ));
				}

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/juridico_inden_deman' );
		// echo 'período encerrado com sucesso';
	}
}
?>