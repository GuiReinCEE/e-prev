<?php
class Investimento_rentabilidade_plano_unico extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::INVESTIMENTO_RENTABILIDADE_PLANO_UNICO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->load->model('indicador_plugin/investimento_rentabilidade_plano_unico_model');
    }

    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
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

	        $this->load->view('indicador_plugin/investimento_rentabilidade_plano_unico/index',$data);
		}
    }

    function listar()
    {
    	if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
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
	        $data['label_10'] = $this->label_10;
	        $data['label_11'] = $this->label_11;

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->investimento_rentabilidade_plano_unico_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/investimento_rentabilidade_plano_unico/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function cadastro($cd_investimento_rentabilidade_plano_unico = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
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
	        $data['label_10'] = $this->label_10;
	        $data['label_11'] = $this->label_11;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_investimento_rentabilidade_plano_unico'] = $cd_investimento_rentabilidade_plano_unico;
			
			if(intval($args['cd_investimento_rentabilidade_plano_unico']) == 0)
			{
				$this->investimento_rentabilidade_plano_unico_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_investimento_rentabilidade_plano_unico'] = $args['cd_investimento_rentabilidade_plano_unico'];
				$data['row']['nr_valor_1']                    = 0;
				$data['row']['nr_valor_2']                    = 0;
				$data['row']['nr_valor_3']            		  = 0;
				$data['row']['nr_valor_4']            		  = (isset($arr['nr_valor_4']) ? $arr['nr_valor_4'] : 0);
				$data['row']['nr_valor_5']            		  = 0;
				$data['row']['fl_media']              		  = "";
				$data['row']['observacao']            	  	  = "";
				$data['row']['dt_referencia']         		  = (isset($arr['dt_referencia']) ? $arr['dt_referencia'] : "");
				$data['row']['nr_meta']               		  = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->investimento_rentabilidade_plano_unico_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/investimento_rentabilidade_plano_unico/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->investimento_rentabilidade_plano_unico_model->listar( $result, $args );

			$collection = $result->result_array();

			$arr_valor_1 = array();
			$arr_valor_2 = array();
			$arr_valor_3 = array();
			$arr_valor_5 = array();

			foreach ($collection as $key => $item) 
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S' && intval($item["mes_referencia"]) != intval($this->input->post("mes_referencia", true)))
				{
					$arr_valor_1[] = $item["nr_valor_1"];
					$arr_valor_2[] = $item["nr_valor_2"];
					$arr_valor_3[] = $item["nr_valor_3"];
					$arr_valor_5[] = $item["nr_valor_5"];
				}
			}

			$args['cd_investimento_rentabilidade_plano_unico'] = intval($this->input->post('cd_investimento_rentabilidade_plano_unico', true));
			$args["cd_indicador_tabela"]           = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                 = $this->input->post("dt_referencia", true);
			$args["fl_media"]                      = $this->input->post("fl_media", true);
			$args["nr_valor_1"]                    = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                    = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_valor_3"]                    = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_valor_4"]                    = app_decimal_para_db($this->input->post("nr_valor_4", true));
			$args["nr_valor_5"]                    = app_decimal_para_db($this->input->post("nr_valor_5", true));

			$args["nr_valor_9"]                   = calculo_projetado_mensal($args["nr_valor_4"], intval($this->input->post("mes_referencia", true)));

			$arr_valor_1[] = $args["nr_valor_1"];
			$arr_valor_2[] = $args["nr_valor_2"];
			$arr_valor_3[] = $args["nr_valor_3"];
			$arr_valor_5[] = $args["nr_valor_5"];


			$args["nr_valor_6"]                    = calculo_acumulado($arr_valor_1, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_7"]                    = calculo_acumulado($arr_valor_2, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_8"]                    = calculo_acumulado($arr_valor_3, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_10"]                    = calculo_acumulado($arr_valor_5, intval($this->input->post("mes_referencia", true)));

			$args["nr_meta"]                       = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                    = $this->input->post("observacao", true);
			$args["cd_usuario"]                    = $this->session->userdata('codigo');

			$this->investimento_rentabilidade_plano_unico_model->salvar($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/investimento_rentabilidade_plano_unico", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_investimento_rentabilidade_plano_unico)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_investimento_rentabilidade_plano_unico'] = $cd_investimento_rentabilidade_plano_unico;
			$args["cd_usuario"]                         = $this->session->userdata('codigo');
			
			$this->investimento_rentabilidade_plano_unico_model->excluir($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/investimento_rentabilidade_plano_unico", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
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
	        $data['label_10'] = $this->label_10;
	        $data['label_11'] = $this->label_11;

			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->investimento_rentabilidade_plano_unico_model->listar( $result, $args );
			$collection = $result->result_array();

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_10']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_9']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_11']), 'background,center');

			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;

			$nr_valor_1_total = 0;
			$nr_valor_2_total = 0;
			$nr_valor_3_total = 0;
			$nr_valor_4_total = 0;
			$nr_valor_5_total = 0;
			$nr_valor_6_total = 0;
			$nr_valor_7_total = 0;
			$nr_valor_8_total = 0;
			$nr_valor_9_total = 0;
			$nr_valor_10_total = 0;

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Resultado de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					$nr_valor_3 = $item["nr_valor_3"];
					$nr_valor_4 = $item['nr_valor_4'];
					$nr_valor_5 = $item['nr_valor_5'];
					$nr_valor_6 = $item['nr_valor_6'];
					$nr_valor_7 = $item['nr_valor_7'];
					$nr_valor_8 = $item['nr_valor_8'];
					$nr_valor_9 = $item['nr_valor_9'];
					$nr_valor_10 = $item['nr_valor_10'];

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$nr_valor_1_total = $item["nr_valor_1"];
						$nr_valor_2_total = $item["nr_valor_2"];
						$nr_valor_3_total = $item["nr_valor_3"];
						$nr_valor_4_total = $item["nr_valor_4"];
						$nr_valor_5_total = $item["nr_valor_5"];
						$nr_valor_6_total = $item["nr_valor_6"];
						$nr_valor_7_total = $item["nr_valor_7"];
						$nr_valor_8_total = $item["nr_valor_8"];
						$nr_valor_9_total = $item["nr_valor_9"];
						$nr_valor_10_total = $item["nr_valor_10"];

						$media_ano[] = $nr_valor_2;
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][3] = app_decimal_para_php($nr_valor_3);
					$indicador[$linha][4] = app_decimal_para_php($nr_valor_4);
					$indicador[$linha][5] = app_decimal_para_php($nr_valor_5);
					$indicador[$linha][6] = app_decimal_para_php($nr_valor_6);
					$indicador[$linha][7] = app_decimal_para_php($nr_valor_7);
					$indicador[$linha][8] = app_decimal_para_php($nr_valor_8);
					$indicador[$linha][9] = app_decimal_para_php($nr_valor_9);
					$indicador[$linha][10] = app_decimal_para_php($nr_valor_10);
					$indicador[$linha][11] = $observacao;
					
					$linha++;
				}
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
				$indicador[$linha][7] = '';
				$indicador[$linha][8] = '';
				$indicador[$linha][9] = '';
				$indicador[$linha][10] = '';
				$indicador[$linha][11] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1_total);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_2_total);
				$indicador[$linha][3] = app_decimal_para_php($nr_valor_3_total);
				$indicador[$linha][4] = app_decimal_para_php($nr_valor_4_total);
				$indicador[$linha][5] = app_decimal_para_php($nr_valor_5_total);
				$indicador[$linha][6] = app_decimal_para_php($nr_valor_6_total);
				$indicador[$linha][7] = app_decimal_para_php($nr_valor_7_total);
				$indicador[$linha][8] = app_decimal_para_php($nr_valor_8_total);
				$indicador[$linha][9] = app_decimal_para_php($nr_valor_9_total);
				$indicador[$linha][10] = app_decimal_para_php($nr_valor_10_total);
				$indicador[$linha][11] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][10]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, utf8_encode($indicador[$i][11]) );
				
				$linha++;
			}

			// GINrar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'4,4,0,0;5,5,0,0;6,6,0,0;9,9,0,0;8,8,0,0',
				"0,0,1,$linha_sem_media",
				"4,4,1,$linha_sem_media;5,5,1,$linha_sem_media;6,6,1,$linha_sem_media;9,9,1,$linha_sem_media;8,8,1,$linha_sem_media",
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
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->investimento_rentabilidade_plano_unico_model->listar( $result, $args );
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
					
					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					$nr_valor_3 = $item["nr_valor_3"];
					$nr_valor_4 = $item["nr_valor_4"];
					$nr_valor_5 = $item["nr_valor_5"];
					$nr_valor_6 = $item["nr_valor_6"];
					$nr_valor_7 = $item["nr_valor_7"];
					$nr_valor_8 = $item["nr_valor_8"];
					$nr_valor_9 = $item["nr_valor_9"];
					$nr_valor_10 = $item["nr_valor_10"];
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
				$args['nr_valor_1']          = ($nr_valor_1);
				$args['nr_valor_2']          = ($nr_valor_2);
				$args['nr_valor_3']          = ($nr_valor_3);
				$args['nr_valor_4']          = ($nr_valor_4);
				$args['nr_valor_5']          = ($nr_valor_5);
				$args['nr_valor_6']          = ($nr_valor_6);
				$args['nr_valor_7']          = ($nr_valor_7);
				$args['nr_valor_8']          = ($nr_valor_8);
				$args['nr_valor_9']          = ($nr_valor_9);
				$args['nr_valor_10']          = ($nr_valor_10);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->investimento_rentabilidade_plano_unico_model->atualiza_fechar_periodo($result, $args);
			}

			$this->investimento_rentabilidade_plano_unico_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}

		redirect("indicador_plugin/investimento_rentabilidade_plano_unico", "refresh");
	}
}
?>