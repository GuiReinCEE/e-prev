<?php
class Investimento_rentabilidade_nominal extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::INVESTIMENTO_RENTABILIDADE_NOMINAL);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->load->model('indicador_plugin/investimento_rentabilidade_nominal_model');
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

	        $this->load->view('indicador_plugin/investimento_rentabilidade_nominal/index',$data);
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

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->investimento_rentabilidade_nominal_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/investimento_rentabilidade_nominal/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}
    }

    function cadastro($cd_investimento_rentabilidade_nominal = 0)
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

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_investimento_rentabilidade_nominal'] = $cd_investimento_rentabilidade_nominal;
			
			if(intval($args['cd_investimento_rentabilidade_nominal']) == 0)
			{
				$this->investimento_rentabilidade_nominal_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_investimento_rentabilidade_nominal'] = $args['cd_investimento_rentabilidade_nominal'];
				$data['row']['nr_valor_1']                    = (isset($arr['nr_valor_1']) ? $arr['nr_valor_1'] : 0);
				$data['row']['nr_valor_2']                    = (isset($arr['nr_valor_2']) ? $arr['nr_valor_2'] : 0);
				$data['row']['nr_valor_3']            		  = 0;
				$data['row']['nr_valor_4']            		  = 0;
				$data['row']['fl_media']              		  = "";
				$data['row']['observacao']            	  	  = "";
				$data['row']['dt_referencia']         		  = (isset($arr['dt_referencia']) ? $arr['dt_referencia'] : "");
				$data['row']['nr_meta']               		  = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);

				$data['row']['nr_valor_1_label'] = (isset($arr['nr_valor_1']) ? $arr['nr_valor_1'] : 0);
				$data['row']['nr_valor_2_label'] = (isset($arr['nr_valor_2']) ? $arr['nr_valor_2'] : 0);
			}			
			else
			{
				$this->investimento_rentabilidade_nominal_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/investimento_rentabilidade_nominal/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
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

			$this->investimento_rentabilidade_nominal_model->listar( $result, $args );
			$collection = $result->result_array();

			$arr_valor_3 = array();
			$arr_valor_4 = array();

			foreach ($collection as $key => $item) 
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S' && intval($item["mes_referencia"]) != intval($this->input->post("mes_referencia", true)))
				{
					$arr_valor_3[] = $item["nr_valor_3"];
					$arr_valor_4[] = $item["nr_valor_4"];
				}
			}

			$args['cd_investimento_rentabilidade_nominal'] = intval($this->input->post('cd_investimento_rentabilidade_nominal', true));
			$args["cd_indicador_tabela"]           = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                 = $this->input->post("dt_referencia", true);
			$args["fl_media"]                      = $this->input->post("fl_media", true);
			$args["nr_valor_1"]                    = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                    = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_valor_3"]                    = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_valor_4"]                    = app_decimal_para_db($this->input->post("nr_valor_4", true));
			$args["nr_valor_5"]                    = calculo_projetado_mensal($args["nr_valor_1"], intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_6"]                    = calculo_projetado_mensal($args["nr_valor_2"], intval($this->input->post("mes_referencia", true)));

			$arr_valor_3[] = $args["nr_valor_3"];
			$arr_valor_4[] = $args["nr_valor_4"];

			$args["nr_valor_7"]                    = calculo_acumulado($arr_valor_3, intval($this->input->post("mes_referencia", true)));
			$args["nr_valor_8"]                    = calculo_acumulado($arr_valor_4, intval($this->input->post("mes_referencia", true)));
			$args["nr_meta"]                       = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]                    = $this->input->post("observacao", true);
			$args["cd_usuario"]                    = $this->session->userdata('codigo');

			$this->investimento_rentabilidade_nominal_model->salvar($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/investimento_rentabilidade_nominal", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}
	
	function excluir($cd_investimento_rentabilidade_nominal)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GIN'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_investimento_rentabilidade_nominal'] = $cd_investimento_rentabilidade_nominal;
			$args["cd_usuario"]                         = $this->session->userdata('codigo');
			
			$this->investimento_rentabilidade_nominal_model->excluir($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/investimento_rentabilidade_nominal", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}

	public function get_valores()
	{
		$this->load->library('integracao_caderno_cci_indicador');

		$this->integracao_caderno_cci_indicador->set_descricao('rentabilidade_nominal');
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

			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->investimento_rentabilidade_nominal_model->listar( $result, $args );
			$collection = $result->result_array();

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
				{
					$data['label_3'] = str_replace("1", number_format($item["nr_valor_1"],2,",","."), $data['label_3']);
					$data['label_5'] = str_replace("1", number_format($item["nr_valor_1"],2,",","."), $data['label_5']);

					$data['label_4'] = str_replace("2", number_format($item["nr_valor_2"],2,",","."), $data['label_4']);
					$data['label_6'] = str_replace("2", number_format($item["nr_valor_2"],2,",","."), $data['label_6']);
					break;
				}
			}

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			//$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_5']), 'background,center');
			//$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
			//$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_4']), 'background,center');
			//$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_9']), 'background,center');

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

						$media_ano[] = $nr_valor_2;
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_3);
					//$indicador[$linha][2] = app_decimal_para_php($nr_valor_4);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_1);
					//$indicador[$linha][3] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][3] = app_decimal_para_php($nr_valor_5);
					//$indicador[$linha][5] = app_decimal_para_php($nr_valor_6);
					$indicador[$linha][4] = app_decimal_para_php($nr_valor_7);
					//$indicador[$linha][8] = app_decimal_para_php($nr_valor_8);
					$indicador[$linha][5] = $observacao;
					
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
				//$indicador[$linha][6] = '';
				//$indicador[$linha][7] = '';
				//$indicador[$linha][8] = '';
				//$indicador[$linha][9] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '';
				//$indicador[$linha][2] = '';
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_1_total);
				//$indicador[$linha][3] = app_decimal_para_php($nr_valor_2_total);
				$indicador[$linha][3] = app_decimal_para_php($nr_valor_5_total);
				//$indicador[$linha][5] = app_decimal_para_php($nr_valor_6_total);
				$indicador[$linha][4] = app_decimal_para_php($nr_valor_7_total);
				//$indicador[$linha][8] = app_decimal_para_php($nr_valor_8_total);
				$indicador[$linha][5] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 4, 'S' );
				//$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 4, 'S' );
				//$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 4, 'S' );
				//$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 4, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 4, 'S' );
				//$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]) );
				
				$linha++;
			}

			// GINrar gr�fico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'2,2,0,0;3,3,0,0',
				"0,0,1,$linha_sem_media",
				"2,2,1,$linha_sem_media;3,3,1,$linha_sem_media-linha",
				usuario_id(),
				$coluna_para_ocultar,
				1,
				2
			);

			$this->db->query($sql);
		}
		else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
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
			
	        $this->investimento_rentabilidade_nominal_model->listar( $result, $args );
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
				}
			}

			// gravar a m�dia do per�odo
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
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->investimento_rentabilidade_nominal_model->atualiza_fechar_periodo($result, $args);
			}

			$this->investimento_rentabilidade_nominal_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO N�O PERMITIDO");
		}

		redirect("indicador_plugin/investimento_rentabilidade_nominal", "refresh");
	}
}
?>