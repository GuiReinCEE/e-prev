<?php
class Juridico_solicitacao_parecer_gerencia extends Controller
{
	var $enum_indicador = 0;
	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_NUMERO_SOLICITACOES_PARECERES_GERENCIA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_solicitacao_parecer_gerencia_model' );
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

	        $this->load->view('indicador_plugin/juridico_solicitacao_parecer_gerencia/index',$data);
		}
    }	

	function listar()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
        {
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;
			$data['label_10'] = $this->label_10;
			//$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
			$data['label_14'] = $this->label_14;
			$data['label_15'] = $this->label_15;
			$data['label_16'] = $this->label_16;
			$data['label_17'] = $this->label_17;
			$data['label_18'] = $this->label_18;
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_solicitacao_parecer_gerencia_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_solicitacao_parecer_gerencia/partial_result', $data);
        }
    }	
	
	function detalhe($cd_juridico_solicitacao_parecer_gerencia = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;
			$data['label_10'] = $this->label_10;
			//$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
			$data['label_14'] = $this->label_14;
			$data['label_15'] = $this->label_15;
			$data['label_16'] = $this->label_16;
			$data['label_17'] = $this->label_17;
			$data['label_18'] = $this->label_18;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_solicitacao_parecer_gerencia'] = intval($cd_juridico_solicitacao_parecer_gerencia);
			
			if(intval($args['cd_juridico_solicitacao_parecer_gerencia']) == 0)
			{
				$this->juridico_solicitacao_parecer_gerencia_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_juridico_solicitacao_parecer_gerencia'] = $args['cd_juridico_solicitacao_parecer_gerencia'];
				$data['row']['nr_ac']         = "";
				$data['row']['nr_ai']         = "";
				$data['row']['nr_aj']         = "";
				$data['row']['nr_gc']         = "";
				$data['row']['nr_ge']         = "";
				$data['row']['nr_gfc']        = "";
				$data['row']['nr_ggs']        = "";
				$data['row']['nr_gin']        = "";
				$data['row']['nr_gp']         = "";
				$data['row']['nr_sg']         = "";
				$data['row']['fl_media']      = "";
				$data['row']['observacao']    = "";
				$data['row']['dt_referencia'] = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']       = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->juridico_solicitacao_parecer_gerencia_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/juridico_solicitacao_parecer_gerencia/detalhe', $data);
		}
	}

	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_solicitacao_parecer_gerencia'] = intval($this->input->post('cd_juridico_solicitacao_parecer_gerencia', true));
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]       = $this->input->post("dt_referencia", true);
			$args["fl_media"]            = $this->input->post("fl_media", true);
			$args["nr_ac"]               = app_decimal_para_db($this->input->post("nr_ac", true));
			$args["nr_ai"]              = app_decimal_para_db($this->input->post("nr_ai", true));
			$args["nr_aj"]              = app_decimal_para_db($this->input->post("nr_aj", true));
			$args["nr_gc"]               = app_decimal_para_db($this->input->post("nr_gc", true));
			$args["nr_ge"]               = app_decimal_para_db($this->input->post("nr_ge", true));
			$args["nr_gfc"]               = app_decimal_para_db($this->input->post("nr_gfc", true));
			$args["nr_ggs"]               = app_decimal_para_db($this->input->post("nr_ggs", true));
			$args["nr_gin"]              = app_decimal_para_db($this->input->post("nr_gin", true));
			$args["nr_gp"]               = app_decimal_para_db($this->input->post("nr_gp", true));
			$args["nr_sg"]              = app_decimal_para_db($this->input->post("nr_sg", true));
			$args["nr_meta"]             = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]          = $this->input->post("observacao", true);
			$args["cd_usuario"]          = $this->session->userdata('codigo');

			$this->juridico_solicitacao_parecer_gerencia_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_solicitacao_parecer_gerencia", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	

	function excluir($cd_juridico_solicitacao_parecer_gerencia = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_solicitacao_parecer_gerencia'] = intval($cd_juridico_solicitacao_parecer_gerencia);
			$args["cd_usuario"]             = $this->session->userdata('codigo');
			
			$this->juridico_solicitacao_parecer_gerencia_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_solicitacao_parecer_gerencia", "refresh");
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
		
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;
			$data['label_10'] = $this->label_10;
			//$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
			$data['label_14'] = $this->label_14;
			$data['label_15'] = $this->label_15;
			$data['label_16'] = $this->label_16;
			$data['label_17'] = $this->label_17;
			$data['label_18'] = $this->label_18;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  7,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  8,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'],  9,0, utf8_encode($data['label_9']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_10']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode(""), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_12']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13,0, utf8_encode($data['label_13']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14,0, utf8_encode($data['label_14']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 15,0, utf8_encode($data['label_15']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 16,0, utf8_encode($data['label_16']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 17,0, utf8_encode($data['label_17']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 18,0, utf8_encode($data['label_18']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->juridico_solicitacao_parecer_gerencia_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador          = array();
			$linha              = 0;
			$ar_tendencia       = array();
			$contador_ano_atual = 0;
			$nr_meta            = 0;
			$nr_ac_tot_anual = 0;
			$nr_ai_tot_anual = 0;
			$nr_aj_tot_anual = 0;
			$nr_gc_tot_anual = 0;
			$nr_ge_tot_anual = 0;
			$nr_gfc_tot_anual = 0;
			$nr_ggs_tot_anual = 0;
			$nr_gin_tot_anual = 0;
			$nr_gp_tot_anual = 0;
			$nr_sg_tot_anual = 0;

			$nr_pre_tot_anual = 0;
			$nr_prev_tot_anual = 0;
			$nr_fin_tot_anual = 0;
			$nr_infr_tot_anual = 0;

			$nr_total_tot_anual = 0;	
			$fl_periodo         = false;
			
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
						$referencia = "Total de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$nr_ac_tot_anual += intval($item['nr_ac']); 
				        $nr_ai_tot_anual += intval($item['nr_ai']); 
				        $nr_aj_tot_anual += intval($item['nr_aj']); 
				        $nr_gc_tot_anual += intval($item['nr_gc']); 
				        $nr_ge_tot_anual += intval($item['nr_ge']); 
				        $nr_gfc_tot_anual += intval($item['nr_gfc']); 
				        $nr_ggs_tot_anual += intval($item['nr_ggs']); 
				        $nr_gin_tot_anual += intval($item['nr_gin']); 
				        $nr_gp_tot_anual += intval($item['nr_gp']); 
				        $nr_sg_tot_anual+= intval($item['nr_sg']); 
						$nr_pre_tot_anual   += intval($item['nr_pre']);
						$nr_prev_tot_anual   += intval($item['nr_prev']);
						$nr_fin_tot_anual   += intval($item['nr_fin']);
						$nr_infr_tot_anual   += intval($item['nr_infr']);
						$nr_total_tot_anual += intval($item['nr_total']);
					}

					$indicador[$linha][0]  = $referencia;
					$indicador[$linha][1]  = $item["nr_ac"];
					$indicador[$linha][2]  = $item["nr_ai"];
					$indicador[$linha][3]  = $item["nr_aj"];
					$indicador[$linha][4]  = $item["nr_gc"];
					$indicador[$linha][5]  = $item["nr_ge"];
					$indicador[$linha][6]  = $item["nr_gfc"];
					$indicador[$linha][7]  = $item["nr_ggs"];
					$indicador[$linha][8]  = $item["nr_gin"];
					$indicador[$linha][9]  = $item["nr_gp"];
					$indicador[$linha][10] = $item["nr_sg"];
					$indicador[$linha][11] = "";//$item["nr_gi"];
					$indicador[$linha][12] = $item["nr_total"];
					$indicador[$linha][13] = $item["nr_pre"];
					$indicador[$linha][14] = $item["nr_prev"];
					$indicador[$linha][15] = $item["nr_fin"];
					$indicador[$linha][16] = $item["nr_infr"];
					$indicador[$linha][17] = $nr_meta;
					$indicador[$linha][18] = $item["observacao"];
					
					$linha++;
				}
			}	

			$linha_total = $linha;

			if(intval($contador_ano_atual) > 0)
			{

				$indicador[$linha][0] = 'Total de '.intval($tabela[0]['nr_ano_referencia']);
				$indicador[$linha][1]  = $nr_ac_tot_anual; 
				$indicador[$linha][2]  = $nr_ai_tot_anual;
				$indicador[$linha][3]  = $nr_aj_tot_anual;
				$indicador[$linha][4]  = $nr_gc_tot_anual;
				$indicador[$linha][5]  = $nr_ge_tot_anual;
				$indicador[$linha][6]  = $nr_gfc_tot_anual; 
				$indicador[$linha][7]  = $nr_ggs_tot_anual; 
				$indicador[$linha][8]  = $nr_gin_tot_anual;
				$indicador[$linha][9]  = $nr_gp_tot_anual; 
				$indicador[$linha][10] = $nr_sg_tot_anual;
				$indicador[$linha][11] = "";//$nr_gi_tot_anual; 
				$indicador[$linha][12] = $nr_total_tot_anual;
				$indicador[$linha][13] = $nr_pre_tot_anual;
				$indicador[$linha][14] = $nr_prev_tot_anual;
				$indicador[$linha][15] = $nr_fin_tot_anual;
				$indicador[$linha][16] = $nr_infr_tot_anual;
				$indicador[$linha][17] = $nr_meta;
				$indicador[$linha][18] = "";
				$linha_total++;
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{                                                                                 
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'],  9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][10]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][11]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12, $linha, app_decimal_para_php($indicador[$i][12]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 13, $linha, app_decimal_para_php($indicador[$i][13]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 14, $linha, app_decimal_para_php($indicador[$i][14]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 15, $linha, app_decimal_para_php($indicador[$i][15]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 16, $linha, app_decimal_para_php($indicador[$i][16]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 17, $linha, app_decimal_para_php($indicador[$i][17]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 18, $linha, utf8_encode(nl2br($indicador[$i][18])), 'left');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = '11;17';
			$ar_g = array(1,2,3,4,5,6,7,8,9,10);
			$var1 = "";
			$var2 = "";
			foreach($ar_g as $i)
			{
				$var1.= (trim($var1) != "" ? ";" : "")."$i,$i,0,0";
				$var2.= (trim($var2) != "" ? ";" : "")."$i,$i,$linha_total,$linha_total";
			}
			
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				$var1,
				"0,0,$linha_total,$linha_total",
				$var2,
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
			
	        $this->juridico_solicitacao_parecer_gerencia_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador_ano_atual = 0;
			$nr_meta            = 0;
			$nr_ac_tot_anual = 0;
			$nr_ai_tot_anual = 0;
			$nr_aj_tot_anual = 0;
			$nr_gc_tot_anual = 0;
			$nr_ge_tot_anual = 0;
			$nr_gfc_tot_anual = 0;
			$nr_ggs_tot_anual = 0;
			$nr_gin_tot_anual = 0;
			$nr_gp_tot_anual = 0;
			$nr_sg_tot_anual = 0;

			$nr_pre_tot_anual = 0;
			$nr_prev_tot_anual = 0;
			$nr_fin_tot_anual = 0;
			$nr_infr_tot_anual = 0;

			$nr_total_tot_anual = 0;	
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_meta = $item['nr_meta'];

					if(trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$nr_ac_tot_anual += intval($item['nr_ac']); 
				        $nr_ai_tot_anual += intval($item['nr_ai']); 
				        $nr_aj_tot_anual += intval($item['nr_aj']); 
				        $nr_gc_tot_anual += intval($item['nr_gc']); 
				        $nr_ge_tot_anual += intval($item['nr_ge']); 
				        $nr_gfc_tot_anual += intval($item['nr_gfc']); 
				        $nr_ggs_tot_anual += intval($item['nr_ggs']); 
				        $nr_gin_tot_anual += intval($item['nr_gin']); 
				        $nr_gp_tot_anual += intval($item['nr_gp']); 
				        $nr_sg_tot_anual+= intval($item['nr_sg']); 
						$nr_pre_tot_anual   += intval($item['nr_pre']);
					}
				}
			}

			// gravar a resultado do período
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"] = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_ac']         = floatval($nr_ac_tot_anual);
				$args['nr_ai']        = floatval($nr_ai_tot_anual);
				$args['nr_aj']        = floatval($nr_aj_tot_anual);
				$args['nr_gc']         = floatval($nr_gc_tot_anual);
				$args['nr_ge']         = floatval($nr_ge_tot_anual);
				$args['nr_gfc']         = floatval($nr_gfc_tot_anual);
				$args['nr_ggs']         = floatval($nr_ggs_tot_anual);
				$args['nr_gin']        = floatval($nr_gin_tot_anual);
				$args['nr_gp']         = floatval($nr_gp_tot_anual);
				$args['nr_sg']        = floatval($nr_sg_tot_anual);
				$args["nr_meta"]       = floatval($nr_meta);
				$args["cd_usuario"]    = $this->session->userdata('codigo');

				$this->juridico_solicitacao_parecer_gerencia_model->atualiza_fechar_periodo($result, $args);
			}

			$this->juridico_solicitacao_parecer_gerencia_model->fechar_periodo($result, $args);
		}

		redirect("indicador_plugin/juridico_solicitacao_parecer_gerencia", "refresh");
	}	
}
?>