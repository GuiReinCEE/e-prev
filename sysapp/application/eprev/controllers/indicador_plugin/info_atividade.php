<?php
class info_atividade extends Controller
{	
	var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::INFO_ATIVIDADE);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/info_atividade_model' );
    }
	
	function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'GTI' ))
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

	        $this->load->view('indicador_plugin/info_atividade/index',$data);
		}
    }
	
	function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'GTI' ))
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
			$data['label_9'] = $this->label_9;
	        
			$tabela = indicador_tabela_aberta(  $this->enum_indicador  );
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->info_atividade_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/info_atividade/index_result', $data);
        }
    }
	
	function cadastro($cd_info_atividade = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GTI' ))
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
			$data['label_9'] = $this->label_9;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_info_atividade'] = $cd_info_atividade;
			
			if(intval($args['cd_info_atividade']) == 0)
			{
				$this->info_atividade_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_info_atividade'] = $args['cd_info_atividade'];
				$data['row']['nr_abertas_mes']    = "";
				$data['row']['nr_atendidas_mes']  = "";
				$data['row']['fl_media']          = "";
				$data['row']['observacao']        = "";
				$data['row']['dt_referencia']     = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']           = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->info_atividade_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/info_atividade/cadastro', $data);
		}
	}
	
	function montaMeses($ar_monta = Array())
    {
        $ar_retorno = Array();

        for($i=1; $i<=12; $i++)
        {
            $ar_retorno[$i] = 0;
        }
        foreach($ar_monta as $item)
        {
            $ar_retorno[$item['nr_mes']] = $item['qt_atividade'];
        }

        return $ar_retorno;
    }
	
	function get_valores()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GTI' ))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$this->load->model('projetos/resumo_atividades_model' );
			
			$args["ano"]        = $this->input->post("nr_ano", true);
			$args["mes"]        = $this->input->post("nr_mes", true);
			$args["cd_usuario"] = "";
	
			$this->resumo_atividades_model->abertasSuporte( $result, $args );
            $data = $result->row_array();

			$abertas_sup = (count($data) > 0 ? intval($data['qt_atividade']) : 0);

			$this->resumo_atividades_model->abertasSistema( $result, $args );
			$data = $result->row_array();

			$abertas_sis = (count($data) > 0 ? intval($data['qt_atividade']) : 0);
			
			$row['nr_abertas_mes'] = intval($abertas_sup) + intval($abertas_sis);
			
            $this->resumo_atividades_model->concluidasSuporte( $result, $args );
			$data = $result->row_array();
			$concluidas_sup = (count($data) > 0 ? intval($data['qt_atividade']) : 0);

            $this->resumo_atividades_model->canceladasSuporte( $result, $args );
			$data = $result->row_array();
			$canceladas_sup = (count($data) > 0 ? intval($data['qt_atividade']) : 0);

            $this->resumo_atividades_model->concluidasSistema( $result, $args );
			$data = $result->row_array();
			$concluidas_sis = (count($data) > 0 ? intval($data['qt_atividade']) : 0);

            $this->resumo_atividades_model->canceladasSistema( $result, $args );
			$data = $result->row_array();
			$canceladas_sis = (count($data) > 0 ? intval($data['qt_atividade']) : 0);
			
			$row['nr_atendidas_mes'] = intval($concluidas_sup) + intval($canceladas_sup) + intval($concluidas_sis) + intval($canceladas_sis);
			
			echo json_encode($row);
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GTI' ))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_info_atividade']      = intval($this->input->post('cd_info_atividade', true));
			$args["cd_indicador_tabela"]    = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]          = $this->input->post("dt_referencia", true);
			$args["fl_media"]               = $this->input->post("fl_media", true);
			$args["nr_abertas_mes"]         = app_decimal_para_db($this->input->post("nr_abertas_mes", true));
			$args["nr_atendidas_mes"]       = app_decimal_para_db($this->input->post("nr_atendidas_mes", true));
			$args["nr_meta"]                = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]             = $this->input->post("observacao", true);
			$args["cd_usuario"]             = $this->session->userdata('codigo');

			$this->info_atividade_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/info_atividade", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_info_atividade)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GTI' ))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_info_atividade'] = $cd_info_atividade;
			$args["cd_usuario"]        = $this->session->userdata('codigo');
			
			$this->info_atividade_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/info_atividade", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GTI' ))
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
			$data['label_9'] = $this->label_9;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, "", 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_9']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_8']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->info_atividade_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador            = array();
			$linha                = 0;
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$nr_media             = 0;
			$nr_abertas_acu_f     = 0;
			$nr_atendidas_acu_f   = 0;
			$nr_percentual_acu_f  = 0;
			$media                = 0;
			$nr_percentual_ano    = 0;
			$nr_aberta_ano        = 0;
			$nr_atendida_ano      = 0;
			$tp_analise           = "";
			
			$fl_periodo = false;
			
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
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Média de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_abertas_mes      = $item['nr_abertas_mes'];
					$nr_atendidas_mes    = $item['nr_atendidas_mes'];
					$nr_meta             = $item['nr_meta'];
					$nr_observacao       = $item["observacao"];
					$nr_percentual_mes_f = $item['nr_percentual_mes_f'];
					$tp_analise          = $item['tp_analise'];
					
					if(trim($item['nr_abertas_acu_f']) == '')
					{
						$nr_abertas_acu_f += $nr_abertas_mes;
					}
					else
					{
						$nr_abertas_acu_f = floatval($item['nr_abertas_acu_f']);
					}
					
					if(trim($item['nr_atendidas_acu_f']) == '')
					{
						$nr_atendidas_acu_f += $nr_atendidas_mes;
					}
					else
					{
						$nr_atendidas_acu_f = floatval( $item['nr_atendidas_acu_f'] );
					}
					
					if(trim($item['nr_percentual_acu_f']) == '')
					{
						if(floatval($nr_abertas_acu_f) > 0)
						{
							$nr_percentual_acu_f = (floatval($nr_atendidas_acu_f) / floatval($nr_abertas_acu_f) )*100;
						}
						else
						{
							$nr_percentual_acu_f = 0;
						}
					}
					else
					{
						$nr_percentual_acu_f = floatval($item['nr_percentual_acu_f']);
					}

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$nr_percentual_ano  += $item['nr_percentual_mes_f'];
						$nr_aberta_ano      += $item['nr_abertas_mes'];
						$nr_atendida_ano    += $item['nr_atendidas_mes'];
						
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
					$indicador[$linha][2] = $nr_abertas_mes;
					$indicador[$linha][3] = $nr_atendidas_mes;
					$indicador[$linha][4] = $nr_percentual_mes_f;
					$indicador[$linha][5] = $nr_abertas_acu_f;
					$indicador[$linha][6] = $nr_atendidas_acu_f;
					$indicador[$linha][7] = $nr_abertas_acu_f - $nr_atendidas_acu_f;
					$indicador[$linha][8] = $nr_percentual_acu_f;
					$indicador[$linha][9] = $nr_meta;
					$indicador[$linha][10] = $observacao;
					
					$linha++;
				}
			}	

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';
				$indicador[$linha][8] = '';
				$indicador[$linha][9] = '';
				$indicador[$linha][10] = '';

				$linha++;

				$ar_status = indicador_status_check($nr_percentual_acu_f, 0, $nr_meta, $tp_analise);
				
				$indicador[$linha][0] = '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($ar_status["fl_meta"], $ar_status["fl_direcao"], "S").'" border="0">';
				$indicador[$linha][2] = $nr_aberta_ano / (intval($contador_ano_atual) == 0 ? 1 : intval($contador_ano_atual));
				$indicador[$linha][3] = $nr_atendida_ano / (intval($contador_ano_atual) == 0 ? 1 : intval($contador_ano_atual));
				$indicador[$linha][4] = $nr_percentual_ano / (intval($contador_ano_atual) == 0 ? 1 : intval($contador_ano_atual));
				$indicador[$linha][5] = $nr_abertas_acu_f;
				$indicador[$linha][6] = $nr_atendidas_acu_f;
				$indicador[$linha][7] = $nr_abertas_acu_f - $nr_atendidas_acu_f;
				$indicador[$linha][8] = $nr_percentual_acu_f;
				$indicador[$linha][9] = $nr_meta;
				$indicador[$linha][10] = '';

				$linha++;
				$indicador[$linha][0]  = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1]  = "";
				$indicador[$linha][2]  = $nr_aberta_ano;
				$indicador[$linha][3]  = $nr_atendida_ano;
				$indicador[$linha][4]  = ($nr_atendida_ano / $nr_aberta_ano) * 100;
				$indicador[$linha][5]  = "";
				$indicador[$linha][6]  = "";
				$indicador[$linha][7]  = "";
				$indicador[$linha][8]  = "";
				$indicador[$linha][9]  = "";
				$indicador[$linha][10] = "";				
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, utf8_encode(nl2br($indicador[$i][10])), 'left');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'4,4,0,0;8,8,0,0;9,9,0,0',
				"0,0,1,$linha_sem_media",
				"4,4,1,$linha_sem_media;8,8,1,$linha_sem_media;9,9,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar,
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
		if(indicador_db::verificar_permissao(usuario_id(),'GTI' ))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->info_atividade_model->listar( $result, $args );
			$collection = $result->result_array();

			$indicador            = array();
			$linha                = 0;
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media_ano            = array();
			$media                = 0;
			$nr_media             = 0;
			$nr_abertas_acu_f     = 0;
			$nr_atendidas_acu_f   = 0;
			$nr_percentual_acu_f  = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
					
				}
				
				$nr_abertas_mes      = $item['nr_abertas_mes'];
				$nr_atendidas_mes    = $item['nr_atendidas_mes'];
				$nr_percentual_mes_f = $item['nr_percentual_mes_f'];
				$nr_meta             = $item['nr_meta'];
				
				if(trim($item['nr_abertas_acu_f']) == '')
				{
					$nr_abertas_acu_f += $nr_abertas_mes;
				}
				else
				{
					$nr_abertas_acu_f = floatval($item['nr_abertas_acu_f']);
				}
				
				if(trim($item['nr_atendidas_acu_f']) == '')
				{
					$nr_atendidas_acu_f += $nr_atendidas_mes;
				}
				else
				{
					$nr_atendidas_acu_f = floatval( $item['nr_atendidas_acu_f'] );
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_abertas_acu_f']    = floatval($nr_abertas_acu_f);
				$args['nr_atendidas_acu_f']  = floatval($nr_atendidas_acu_f);
				$args["nr_meta"]             = ($nr_meta);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->info_atividade_model->atualiza_fechar_periodo($result, $args);
			}

			$this->info_atividade_model->fechar_periodo($result, $args);

		}

		redirect("indicador_plugin/info_atividade", "refresh");
	}
}
?>