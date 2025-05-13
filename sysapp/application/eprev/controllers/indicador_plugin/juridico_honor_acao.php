<?php
class juridico_honor_acao extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_CUSTO_MEDIO_DE_HONORARIOS_POR_ACAO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_honor_acao_model' );
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

	        $this->load->view('indicador_plugin/juridico_honor_acao/index',$data);
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
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_honor_acao_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_honor_acao/partial_result', $data);
        }
    }	
	
	function detalhe($cd_juridico_honor_acao = 0)
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


			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_honor_acao'] = intval($cd_juridico_honor_acao);
			
			if(intval($args['cd_juridico_honor_acao']) == 0)
			{
				$this->juridico_honor_acao_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_juridico_honor_acao'] = $args['cd_juridico_honor_acao'];
				$data['row']['vl_honorario']  = 0;
				$data['row']['qt_acoes']      = '';
				$data['row']['fl_media']      = '';
				$data['row']['observacao']    = '';
				$data['row']['dt_referencia'] = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']       = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->juridico_honor_acao_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/juridico_honor_acao/detalhe', $data);
		}
	}	
	
	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_honor_acao'] = intval($this->input->post('cd_juridico_honor_acao', true));
			$args["cd_indicador_tabela"]    = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]          = $this->input->post("dt_referencia", true);
			$args["fl_media"]               = $this->input->post("fl_media", true);
			$args["vl_honorario"]           = app_decimal_para_db($this->input->post("vl_honorario", true));
			$args["qt_acoes"]               = app_decimal_para_db($this->input->post("qt_acoes", true));
			$args["nr_meta"]                = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]             = $this->input->post("observacao", true);
			$args["cd_usuario"]             = $this->session->userdata('codigo');

			$this->juridico_honor_acao_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_honor_acao", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function excluir($cd_juridico_honor_acao = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_honor_acao'] = intval($cd_juridico_honor_acao);
			$args["cd_usuario"]             = $this->session->userdata('codigo');
			
			$this->juridico_honor_acao_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_honor_acao", "refresh");
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
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->juridico_honor_acao_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador                = array();
			$linha                    = 0;
			$ar_tendencia             = array();
			$contador_ano_atual       = 0;
			$vl_honorario_total       = 0;
			$vl_honorario_medio_total = 0;
			$nr_meta                  = 0;			
			$fl_periodo               = false;
			
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
						$vl_honorario_total       += $item["vl_honorario"];
						$vl_honorario_medio_total += $item["vl_honorario_medio"];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item["vl_honorario"];
					$indicador[$linha][2] = $item["qt_acoes"];
					$indicador[$linha][3] = $item["vl_honorario_medio"];
					$indicador[$linha][4] = $nr_meta;
					$indicador[$linha][5] = 0; #tendencia
					$indicador[$linha][6] = $item["observacao"];
					
					$ar_tendencia[] = $item["vl_honorario_medio"]; 
					
					$linha++;
				}
			}	
				
			#### LINHA DE TENDÊNCIA - CURVA LOGARITMICA ####
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0] = 'Resultado de '.intval($tabela[0]['nr_ano_referencia']);
				$indicador[$linha][1] = $vl_honorario_total;
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = ($vl_honorario_medio_total / $contador_ano_atual);
				$indicador[$linha][4] = $nr_meta;
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'left');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = 5;
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar,
				1,2,3
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
			
	        $this->juridico_honor_acao_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador_ano_atual       = 0;
			$vl_honorario_total       = 0;
			$vl_honorario_medio_total = 0;
			$nr_meta                  = 0;			
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_meta = $item['nr_meta'];

					if(trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$vl_honorario_total       += $item["vl_honorario"];
						$vl_honorario_medio_total += $item["vl_honorario_medio"];
					}
				}
			}

			// gravar a resultado do período
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['vl_honorario']        = floatval($vl_honorario_total);
				$args['vl_honorario_medio']  = floatval(($vl_honorario_medio_total / $contador_ano_atual));
				$args["nr_meta"]             = floatval($nr_meta);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->juridico_honor_acao_model->atualiza_fechar_periodo($result, $args);
			}

			$this->juridico_honor_acao_model->fechar_periodo($result, $args);

		}

		redirect("indicador_plugin/juridico_honor_acao", "refresh");
	}	
}
?>