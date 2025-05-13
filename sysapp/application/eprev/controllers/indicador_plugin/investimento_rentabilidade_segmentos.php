<?php
class Investimento_rentabilidade_segmentos extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::INVESTIMENTO_RENTABILIDADE_SEGMENTOS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->load->model('indicador_plugin/investimento_rentabilidade_segmentos_model');
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

	        $this->load->view('indicador_plugin/investimento_rentabilidade_segmentos/index',$data);
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
	        $data['label_12'] = $this->label_12;
	        $data['label_13'] = $this->label_13;
	        $data['label_14'] = $this->label_14;
	        $data['label_15'] = $this->label_15;
	        $data['label_16'] = $this->label_16;
	        $data['label_17'] = $this->label_17;
	        $data['label_18'] = $this->label_18;
	        $data['label_19'] = $this->label_19;

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->investimento_rentabilidade_segmentos_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/investimento_rentabilidade_segmentos/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function cadastro($cd_investimento_rentabilidade_segmentos = 0)
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
	        $data['label_12'] = $this->label_12;
	        $data['label_13'] = $this->label_13;
	        $data['label_14'] = $this->label_14;
	        $data['label_15'] = $this->label_15;
	        $data['label_16'] = $this->label_16;
	        $data['label_17'] = $this->label_17;
	        $data['label_18'] = $this->label_18;
	        $data['label_19'] = $this->label_19;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_investimento_rentabilidade_segmentos'] = $cd_investimento_rentabilidade_segmentos;
			
			if(intval($args['cd_investimento_rentabilidade_segmentos']) == 0)
			{
				$this->investimento_rentabilidade_segmentos_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_investimento_rentabilidade_segmentos'] = $args['cd_investimento_rentabilidade_segmentos'];
				$data['row']['nr_valor_1']                    = 0;
				$data['row']['nr_valor_2']            		  = 0;
				$data['row']['nr_valor_3']            		  = 0;
				$data['row']['nr_valor_4']            		  = 0;
				$data['row']['nr_valor_5']            		  = 0;
				$data['row']['nr_valor_6']            		  = 0;
				$data['row']['nr_valor_7']            		  = 0;
				$data['row']['nr_valor_8']            		  = 0;
				$data['row']['nr_valor_9']            		  = 0;
				$data['row']['fl_media']              		  = "";
				$data['row']['observacao']            	  	  = "";
				$data['row']['dt_referencia']         		  = (isset($arr['dt_referencia']) ? $arr['dt_referencia'] : "");
			}			
			else
			{
				$this->investimento_rentabilidade_segmentos_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/investimento_rentabilidade_segmentos/cadastro', $data);
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

			$this->investimento_rentabilidade_segmentos_model->listar( $result, $args );
			$collection = $result->result_array();

			$arr_valor_1 = array();
			$arr_valor_2 = array();
			$arr_valor_3 = array();
			$arr_valor_4 = array();
			$arr_valor_5 = array();
			$arr_valor_6 = array();
			$arr_valor_7 = array();
			$arr_valor_8 = array();
			$arr_valor_9 = array();

			foreach ($collection as $key => $item) 
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S' && intval($this->input->post("mes_referencia", true)))
				{
					$arr_valor_1[] = $item["nr_valor_1"];
					$arr_valor_2[] = $item["nr_valor_2"];
					$arr_valor_3[] = $item["nr_valor_3"];
					$arr_valor_4[] = $item["nr_valor_4"];
					$arr_valor_5[] = $item["nr_valor_5"];
					$arr_valor_6[] = $item["nr_valor_6"];
					$arr_valor_7[] = $item["nr_valor_7"];
					$arr_valor_8[] = $item["nr_valor_8"];
					$arr_valor_9[] = $item["nr_valor_9"];
				}
			}

			$args['cd_investimento_rentabilidade_segmentos'] = intval($this->input->post('cd_investimento_rentabilidade_segmentos', true));
			$args["cd_indicador_tabela"]           = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                 = $this->input->post("dt_referencia", true);
			$args["fl_media"]                      = $this->input->post("fl_media", true);
			
			$args["nr_valor_1"]                    = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                    = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_valor_3"]                    = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_valor_4"]                    = app_decimal_para_db($this->input->post("nr_valor_4", true));
			$args["nr_valor_5"]                    = app_decimal_para_db($this->input->post("nr_valor_5", true));
			$args["nr_valor_6"]                    = app_decimal_para_db($this->input->post("nr_valor_6", true));
			$args["nr_valor_7"]                    = app_decimal_para_db($this->input->post("nr_valor_7", true));
			$args["nr_valor_8"]                    = app_decimal_para_db($this->input->post("nr_valor_8", true));
			$args["nr_valor_9"]                    = app_decimal_para_db($this->input->post("nr_valor_9", true));
			
			$arr_valor_1[] = $args["nr_valor_1"];
			$arr_valor_2[] = $args["nr_valor_2"];
			$arr_valor_3[] = $args["nr_valor_3"];
			$arr_valor_4[] = $args["nr_valor_4"];
			$arr_valor_5[] = $args["nr_valor_5"];
			$arr_valor_6[] = $args["nr_valor_6"];
			$arr_valor_7[] = $args["nr_valor_7"];
			$arr_valor_8[] = $args["nr_valor_8"];
			$arr_valor_9[] = $args["nr_valor_9"];

			$nr_valor_3 = calculo_acumulado($arr_valor_3, intval($this->input->post("mes_referencia", true)));
			$nr_valor_9 = calculo_acumulado($arr_valor_9, intval($this->input->post("mes_referencia", true)));

			$args["nr_valor_10"]                   = (((($nr_valor_9/100)+1)/(($nr_valor_3/100)+1))-1)*100;

			$args["nr_valor_11"]                   = calculo_acumulado($arr_valor_1, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_12"]                   = calculo_acumulado($arr_valor_2, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_13"]                   = calculo_acumulado($arr_valor_4, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_14"]                   = calculo_acumulado($arr_valor_5, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_15"]                   = calculo_acumulado($arr_valor_6, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_16"]                   = calculo_acumulado($arr_valor_7, intval($this->input->post("mes_referencia", true))); 
			$args["nr_valor_17"]                   = calculo_acumulado($arr_valor_8, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_18"]                   = calculo_acumulado($arr_valor_9, intval($this->input->post("mes_referencia", true)));

            $args["observacao"]                    = $this->input->post("observacao", true);
			$args["cd_usuario"]                    = $this->session->userdata('codigo');

			$this->investimento_rentabilidade_segmentos_model->salvar($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/investimento_rentabilidade_segmentos", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_investimento_rentabilidade_segmentos)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_investimento_rentabilidade_segmentos'] = $cd_investimento_rentabilidade_segmentos;
			$args["cd_usuario"]                    = $this->session->userdata('codigo');
			
			$this->investimento_rentabilidade_segmentos_model->excluir($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/investimento_rentabilidade_segmentos", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
		
	public function get_valores()
	{
		$this->load->library('integracao_caderno_cci_indicador');

		$this->integracao_caderno_cci_indicador->set_descricao('rentabilidade_segmentos');
		$this->integracao_caderno_cci_indicador->set_ano($this->input->post('nr_ano', true));
		$this->integracao_caderno_cci_indicador->set_mes($this->input->post('nr_mes', true));
		
		$data = $this->integracao_caderno_cci_indicador->get_valores();

		echo json_encode($data);
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
	        $data['label_12'] = $this->label_12;
	        $data['label_13'] = $this->label_13;
	        $data['label_14'] = $this->label_14;
	        $data['label_15'] = $this->label_15;
	        $data['label_16'] = $this->label_16;
	        $data['label_17'] = $this->label_17;
	        $data['label_18'] = $this->label_18;
	        $data['label_19'] = $this->label_19;

			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_9']), 'background,center');

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_13']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_14']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_15']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 13,0, utf8_encode($data['label_16']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 14,0, utf8_encode($data['label_17']), 'background,center');
			//$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 15,0, utf8_encode($data['label_12']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 15,0, utf8_encode($data['label_11']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 16,0, utf8_encode($data['label_18']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 17,0, utf8_encode($data['label_10']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 18,0, utf8_encode($data['label_19']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->investimento_rentabilidade_segmentos_model->listar( $result, $args );
			$collection = $result->result_array();
			
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
			$nr_valor_11_total = 0;
			$nr_valor_12_total = 0;
			$nr_valor_13_total = 0;
			$nr_valor_14_total = 0;
			$nr_valor_15_total = 0;
			$nr_valor_16_total = 0;
			$nr_valor_17_total = 0;
			$nr_valor_18_total = 0;
			$nr_valor_19_total = 0;

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
					
					$nr_valor_1 = $item['nr_valor_1'];
					$nr_valor_2 = $item['nr_valor_2'];
					$nr_valor_3 = $item['nr_valor_3'];
					$nr_valor_4 = $item['nr_valor_4'];
					$nr_valor_5 = $item['nr_valor_5'];
					$nr_valor_6 = $item['nr_valor_6'];
					$nr_valor_7 = $item['nr_valor_7'];
					$nr_valor_8 = $item['nr_valor_8'];
					$nr_valor_9 = $item['nr_valor_9'];
					$nr_valor_10 = $item['nr_valor_10'];
					$nr_valor_11 = $item['nr_valor_11'];
					$nr_valor_12 = $item['nr_valor_12'];
					$nr_valor_13 = $item['nr_valor_13'];
					$nr_valor_14 = $item['nr_valor_14'];
					$nr_valor_15 = $item['nr_valor_15'];
					$nr_valor_16 = $item['nr_valor_16'];
					$nr_valor_17 = $item['nr_valor_17'];
					$nr_valor_18 = $item['nr_valor_18'];

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$nr_valor_1_total = $item["nr_valor_1"];
						$nr_valor_2_total = $item['nr_valor_2'];
						$nr_valor_3_total = $item['nr_valor_3'];
						$nr_valor_4_total = $item['nr_valor_4'];
						$nr_valor_5_total = $item['nr_valor_5'];
						$nr_valor_6_total = $item['nr_valor_6'];
						$nr_valor_7_total = $item['nr_valor_7'];
						$nr_valor_8_total = $item['nr_valor_8'];
						$nr_valor_9_total = $item['nr_valor_9'];
						$nr_valor_10_total = $item['nr_valor_10'];
						$nr_valor_11_total = $item['nr_valor_11'];
						$nr_valor_12_total = $item['nr_valor_12'];
						$nr_valor_13_total = $item['nr_valor_13'];
						$nr_valor_14_total = $item['nr_valor_14'];
						$nr_valor_15_total = $item['nr_valor_15'];
						$nr_valor_16_total = $item['nr_valor_16'];
						$nr_valor_17_total = $item['nr_valor_17'];
						$nr_valor_18_total = $item['nr_valor_18'];

						$media_ano[] = $nr_valor_2;
						$contador_ano_atual++;
					}
					/*
					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][3] = app_decimal_para_php($nr_valor_3);
					$indicador[$linha][4] = app_decimal_para_php($nr_valor_4);
					$indicador[$linha][5] = app_decimal_para_php($nr_valor_5);
					$indicador[$linha][6] = app_decimal_para_php($nr_valor_6);
					$indicador[$linha][7] = app_decimal_para_php($nr_valor_7);
					$indicador[$linha][8] = app_decimal_para_php($nr_valor_8);
					$indicador[$linha][9] = app_decimal_para_php($nr_valor_9);
					$indicador[$linha][10] = app_decimal_para_php($nr_valor_10);
					$indicador[$linha][11] = app_decimal_para_php($nr_valor_11);
					$indicador[$linha][12] = app_decimal_para_php($nr_valor_12);
					$indicador[$linha][13] = app_decimal_para_php($nr_valor_13);
					$indicador[$linha][14] = app_decimal_para_php($nr_valor_14);
					$indicador[$linha][15] = app_decimal_para_php($nr_valor_15);
					$indicador[$linha][16] = app_decimal_para_php($nr_valor_16);
					$indicador[$linha][17] = app_decimal_para_php($nr_valor_17);
					$indicador[$linha][18] = app_decimal_para_php($nr_valor_18);
					$indicador[$linha][19] = $observacao;
					
					$linha++;
					*/
				}
			}	
				
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}
				/*
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
				$indicador[$linha][12] = '';
				$indicador[$linha][13] = '';
				$indicador[$linha][14] = '';
				$indicador[$linha][15] = '';
				$indicador[$linha][16] = '';
				$indicador[$linha][17] = '';
				$indicador[$linha][18] = '';
				$indicador[$linha][19] = '';

				$linha++;
				*/
				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1_total);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_2_total);
				$indicador[$linha][3] = app_decimal_para_php($nr_valor_3_total);
				$indicador[$linha][4] = app_decimal_para_php($nr_valor_4_total);
				$indicador[$linha][5] = app_decimal_para_php($nr_valor_5_total);
				$indicador[$linha][6] = app_decimal_para_php($nr_valor_6_total);
				$indicador[$linha][7] = app_decimal_para_php($nr_valor_7_total);
				$indicador[$linha][8] = app_decimal_para_php($nr_valor_8_total);
				$indicador[$linha][9] = app_decimal_para_php($nr_valor_9_total);
				$indicador[$linha][10] = app_decimal_para_php($nr_valor_13_total);
				$indicador[$linha][11] = app_decimal_para_php($nr_valor_14_total);
				$indicador[$linha][12] = app_decimal_para_php($nr_valor_15_total);
				$indicador[$linha][13] = app_decimal_para_php($nr_valor_16_total);
				$indicador[$linha][14] = app_decimal_para_php($nr_valor_17_total);
				//$indicador[$linha][15] = app_decimal_para_php($nr_valor_12_total);
				$indicador[$linha][15] = app_decimal_para_php($nr_valor_11_total);
				$indicador[$linha][16] = app_decimal_para_php($nr_valor_18_total);
				$indicador[$linha][17] = app_decimal_para_php($nr_valor_10_total);
				$indicador[$linha][18] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][10]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][11]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12, $linha, app_decimal_para_php($indicador[$i][12]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 13, $linha, app_decimal_para_php($indicador[$i][13]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 14, $linha, app_decimal_para_php($indicador[$i][14]), 'center', 'S', 2, 'S' );
				//$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 15, $linha, app_decimal_para_php($indicador[$i][15]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 15, $linha, app_decimal_para_php($indicador[$i][15]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 16, $linha, app_decimal_para_php($indicador[$i][16]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 17, $linha, app_decimal_para_php($indicador[$i][17]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 18, $linha, utf8_encode($indicador[$i][18]) );
				
				$linha++;
			}

			// GINrar gráfico
			$coluna_para_ocultar='1,2,3,4,5,6,7,8,9';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'10,10,0,0;11,11,0,0;12,12,0,0;13,13,0,0;14,14,0,0;15,15,0,0;16,16,0,0;17,17,0,0;18,18,0,0',
				"0,0,1,$linha",
				"10,10,1,$linha;11,11,1,$linha;12,12,1,$linha;13,13,1,$linha;14,14,1,$linha;15,15,1,$linha;16,16,1,$linha;17,17,1,$linha;18,18,1,$linha",
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
			
	        $this->investimento_rentabilidade_segmentos_model->listar( $result, $args );
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
					
					$nr_valor_1 = $item['nr_valor_1'];
					$nr_valor_2 = $item['nr_valor_2'];
					$nr_valor_3 = $item['nr_valor_3'];
					$nr_valor_4 = $item['nr_valor_4'];
					$nr_valor_5 = $item['nr_valor_5'];
					$nr_valor_6 = $item['nr_valor_6'];
					$nr_valor_7 = $item['nr_valor_7'];
					$nr_valor_8 = $item['nr_valor_8'];
					$nr_valor_9 = $item['nr_valor_9'];
					$nr_valor_10 = $item['nr_valor_10'];
					$nr_valor_11 = $item['nr_valor_11'];
					$nr_valor_12 = $item['nr_valor_12'];
					$nr_valor_13 = $item['nr_valor_13'];
					$nr_valor_14 = $item['nr_valor_14'];
					$nr_valor_15 = $item['nr_valor_15'];
					$nr_valor_16 = $item['nr_valor_16'];
					$nr_valor_17 = $item['nr_valor_17'];
					$nr_valor_18 = $item['nr_valor_18'];
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
				$args['nr_valor_11']          = ($nr_valor_11);
				$args['nr_valor_12']          = ($nr_valor_12);
				$args['nr_valor_13']          = ($nr_valor_13);
				$args['nr_valor_14']          = ($nr_valor_14);
				$args['nr_valor_15']          = ($nr_valor_15);
				$args['nr_valor_16']          = ($nr_valor_16);
				$args['nr_valor_17']          = ($nr_valor_17);
				$args['nr_valor_18']          = ($nr_valor_18);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->investimento_rentabilidade_segmentos_model->atualiza_fechar_periodo($result, $args);
			}

			$this->investimento_rentabilidade_segmentos_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}

		redirect("indicador_plugin/investimento_rentabilidade_segmentos", "refresh");
	}
}
?>