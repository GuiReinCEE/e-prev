<?php
class Juridico_solicitacao_parecer_gerencia_novo extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_NUMERO_SOLICITACOES_PARECERES_GERENCIA_NOVO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_solicitacao_parecer_gerencia_novo_model' );
    }		
	
	function index()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/juridico_solicitacao_parecer_gerencia_novo/index',$data);
		}
    }	

	function listar()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
        {			
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
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
			$data['label_14'] = $this->label_14;
			$data['label_15'] = $this->label_15;
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);				

			$data['collection'] = $this->juridico_solicitacao_parecer_gerencia_novo_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/juridico_solicitacao_parecer_gerencia_novo/partial_result', $data);
        }
    }	
	
	function detalhe($cd_juridico_solicitacao_parecer_gerencia_novo = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{			
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
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
			$data['label_14'] = $this->label_14;
			$data['label_15'] = $this->label_15;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_juridico_solicitacao_parecer_gerencia_novo) == 0)
			{
				$row = $this->juridico_solicitacao_parecer_gerencia_novo_model->carrega_referencia();

				$data['row'] = array(
					'cd_juridico_solicitacao_parecer_gerencia_novo' => 0,
					'nr_ai' 										=> '',
					'nr_grc' 										=> '',
					'nr_gj' 										=> '',
					'nr_gc' 										=> '',
					'nr_gti' 										=> '',
					'nr_gin' 										=> '',
					'nr_gfc' 										=> '',
					'nr_gcm' 										=> '',
					'nr_gp' 										=> '',
					'nr_de' 										=> '',
					'nr_cf' 										=> '',
					'nr_cd' 										=> '',
					'fl_media' 										=> '',
					'observacao' 									=> '',
					'dt_referencia' 								=> (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ""),
					'nr_meta' 										=> (isset($row['nr_meta']) ? $row['nr_meta'] : 0)

				);
			}			
			else
			{
				$data['row'] = $this->juridico_solicitacao_parecer_gerencia_novo_model->carrega($cd_juridico_solicitacao_parecer_gerencia_novo);
			}

			$this->load->view('indicador_plugin/juridico_solicitacao_parecer_gerencia_novo/detalhe', $data);
		}
	}

	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{					
			$cd_juridico_solicitacao_parecer_gerencia_novo = $this->input->post('cd_juridico_solicitacao_parecer_gerencia_novo', TRUE);

			$nr_ai  = app_decimal_para_db($this->input->post("nr_ai", TRUE));
			$nr_grc = app_decimal_para_db($this->input->post("nr_grc", TRUE));
			$nr_gj  = app_decimal_para_db($this->input->post("nr_gj", TRUE));
			$nr_gc  = app_decimal_para_db($this->input->post("nr_gc", TRUE));
			$nr_gti = app_decimal_para_db($this->input->post("nr_gti", TRUE));
			$nr_gin = app_decimal_para_db($this->input->post("nr_gin", TRUE));
			$nr_gfc = app_decimal_para_db($this->input->post("nr_gfc", TRUE));
			$nr_gcm = app_decimal_para_db($this->input->post("nr_gcm", TRUE));
			$nr_gp  = app_decimal_para_db($this->input->post("nr_gp", TRUE));
			$nr_de  = app_decimal_para_db($this->input->post("nr_de", TRUE));
			$nr_cf  = app_decimal_para_db($this->input->post("nr_cf", TRUE));
			$nr_cd  = app_decimal_para_db($this->input->post("nr_cd", TRUE));

			$nr_total = $nr_ai + $nr_grc + $nr_gj + $nr_gc + $nr_gti + $nr_gin + $nr_gfc + $nr_gcm + $nr_gp + $nr_de + $nr_cf + $nr_cd;

			$args = array(
				'cd_indicador_tabela' => $this->input->post("cd_indicador_tabela", TRUE),
				'dt_referencia' 	  => $this->input->post("dt_referencia", TRUE),
				'fl_media' 			  => $this->input->post("fl_media", TRUE),
				'nr_ai' 			  => $nr_ai,
				'nr_grc' 			  => $nr_grc,
				'nr_gj' 			  => $nr_gj,
				'nr_gc' 			  => $nr_gc,
				'nr_gti' 			  => $nr_gti,
				'nr_gin' 			  => $nr_gin,
				'nr_gfc' 			  => $nr_gfc,
				'nr_gcm' 			  => $nr_gcm,
				'nr_gp' 			  => $nr_gp,
				'nr_de' 			  => $nr_de,
				'nr_cf' 			  => $nr_cf,
				'nr_cd' 			  => $nr_cd,
				'nr_total' 			  => $nr_total,
				'nr_meta' 			  => app_decimal_para_db($this->input->post("nr_meta", TRUE)),
				'observacao' 		  => $this->input->post("observacao", TRUE),
				'cd_usuario' 		  => $this->session->userdata('codigo')
			);

			if(intval($cd_juridico_solicitacao_parecer_gerencia_novo) == 0)
			{
				$this->juridico_solicitacao_parecer_gerencia_novo_model->salvar($args);
			}
			else
			{
				$this->juridico_solicitacao_parecer_gerencia_novo_model->atualizar($cd_juridico_solicitacao_parecer_gerencia_novo, $args);
			}

			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_solicitacao_parecer_gerencia_novo", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	

	function excluir($cd_juridico_solicitacao_parecer_gerencia_novo = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{					
			$this->juridico_solicitacao_parecer_gerencia_novo_model->excluir($cd_juridico_solicitacao_parecer_gerencia_novo, $this->session->userdata('codigo'));

			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_solicitacao_parecer_gerencia_novo", "refresh");
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
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
			$data['label_14'] = $this->label_14;
			$data['label_15'] = $this->label_15;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

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
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_11']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_12']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13,0, utf8_encode($data['label_13']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14,0, utf8_encode($data['label_14']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 15,0, utf8_encode($data['label_15']), 'background,center');
			
			$collection = $this->juridico_solicitacao_parecer_gerencia_novo_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador          = array();
			$linha              = 0;
			$ar_tendencia       = array();
			$contador_ano_atual = 0;
			$nr_meta            = 0;
			$nr_ai_tot_anual  	= 0;
			$nr_grc_tot_anual 	= 0;
			$nr_gj_tot_anual  	= 0;
			$nr_gc_tot_anual  	= 0;
			$nr_gti_tot_anual 	= 0;
			$nr_gin_tot_anual 	= 0;
			$nr_gfc_tot_anual 	= 0;
			$nr_gcm_tot_anual 	= 0;
			$nr_gp_tot_anual  	= 0;
			$nr_de_tot_anual  	= 0;
			$nr_cf_tot_anual  	= 0;
			$nr_cd_tot_anual  	= 0;
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

						$nr_ai_tot_anual  += intval($item['nr_ai']);
				        $nr_grc_tot_anual += intval($item['nr_grc']);
				        $nr_gj_tot_anual  += intval($item['nr_gj']);
				        $nr_gc_tot_anual  += intval($item['nr_gc']);
				        $nr_gti_tot_anual += intval($item['nr_gti']);
				        $nr_gin_tot_anual += intval($item['nr_gin']);
				        $nr_gfc_tot_anual += intval($item['nr_gfc']);
				        $nr_gcm_tot_anual += intval($item['nr_gcm']);
				        $nr_gp_tot_anual  += intval($item['nr_gp']);
				        $nr_de_tot_anual  += intval($item['nr_de']);
				        $nr_cf_tot_anual  += intval($item['nr_cf']);
				        $nr_cd_tot_anual  += intval($item['nr_cd']);

				        $nr_total_tot_anual = $nr_ai_tot_anual  + $nr_grc_tot_anual + $nr_gj_tot_anual  + $nr_gc_tot_anual  + $nr_gti_tot_anual + $nr_gin_tot_anual + $nr_gfc_tot_anual + $nr_gcm_tot_anual + $nr_gp_tot_anual + $nr_de_tot_anual + $nr_cf_tot_anual + $nr_cd_tot_anual;
					}

					$indicador[$linha][0]   = $referencia;
					$indicador[$linha][1]   = $item["nr_ai"];
					$indicador[$linha][2]   = $item["nr_grc"];
					$indicador[$linha][3]   = $item["nr_gj"];
					$indicador[$linha][4]   = $item["nr_gc"];
					$indicador[$linha][5]   = $item["nr_gti"];
					$indicador[$linha][6]   = $item["nr_gin"];
					$indicador[$linha][7]   = $item["nr_gfc"];
					$indicador[$linha][8]   = $item["nr_gcm"];
					$indicador[$linha][9]   = $item["nr_gp"];
					$indicador[$linha][10]  = $item["nr_de"];
					$indicador[$linha][11]  = $item["nr_cf"];
					$indicador[$linha][12]  = $item["nr_cd"];
					$indicador[$linha][13]  = $item["nr_total"];
					$indicador[$linha][14]  = $nr_meta;
					$indicador[$linha][15]  = $item["observacao"];
					
					$linha++;
				}
			}	

			$linha_total = $linha;

			if(intval($contador_ano_atual) > 0)
			{

				$indicador[$linha][0]   = 'Total de '.intval($tabela[0]['nr_ano_referencia']);
				$indicador[$linha][1]   = $nr_ai_tot_anual; 
				$indicador[$linha][2]   = $nr_grc_tot_anual;
				$indicador[$linha][3]   = $nr_gj_tot_anual;
				$indicador[$linha][4]   = $nr_gc_tot_anual;
				$indicador[$linha][5]   = $nr_gti_tot_anual;
				$indicador[$linha][6]   = $nr_gin_tot_anual; 
				$indicador[$linha][7]   = $nr_gfc_tot_anual; 
				$indicador[$linha][8]   = $nr_gcm_tot_anual;
				$indicador[$linha][9]   = $nr_gp_tot_anual;
				$indicador[$linha][10]  = $nr_de_tot_anual;
				$indicador[$linha][11]  = $nr_cf_tot_anual;
				$indicador[$linha][12]  = $nr_cd_tot_anual;
				$indicador[$linha][13]  = $nr_total_tot_anual;
				$indicador[$linha][14]  = $nr_meta;
				$indicador[$linha][15]  = "";

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
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 15, $linha, utf8_encode(nl2br($indicador[$i][15])), 'justify');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = '';
			$campos_grafico		 = array(1,2,3,4,5,6,7,8,9,10,11,12);
			$var_linha_1 		 = '';
			$var_linha_2 		 = '';

			foreach($campos_grafico as $i)
			{
				$var_linha_1.= (trim($var_linha_1) != '' ? ';' : '')."$i,$i,0,0";
				$var_linha_2.= (trim($var_linha_2) != '' ? ';' : '')."$i,$i,$linha_total,$linha_total";
			}
			
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				$var_linha_1,
				"0,0,$linha_total,$linha_total",
				$var_linha_2,
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
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
	        $collection = $this->juridico_solicitacao_parecer_gerencia_novo_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;
			$nr_meta            = 0;
			$nr_ai_tot_anual  	= 0;
			$nr_grc_tot_anual 	= 0;
			$nr_gj_tot_anual  	= 0;
			$nr_gc_tot_anual  	= 0;
			$nr_gti_tot_anual 	= 0;
			$nr_gin_tot_anual 	= 0;
			$nr_gfc_tot_anual 	= 0;
			$nr_gcm_tot_anual 	= 0;
			$nr_gp_tot_anual  	= 0;
			$nr_de_tot_anual  	= 0;
			$nr_cf_tot_anual  	= 0;
			$nr_cd_tot_anual  	= 0;
			$nr_total_tot_anual = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_meta = $item['nr_meta'];

					if(trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;

						$nr_ai_tot_anual  += intval($item['nr_ai']);
				        $nr_grc_tot_anual += intval($item['nr_grc']);
				        $nr_gj_tot_anual  += intval($item['nr_gj']);
				        $nr_gc_tot_anual  += intval($item['nr_gc']);
				        $nr_gti_tot_anual += intval($item['nr_gti']);
				        $nr_gin_tot_anual += intval($item['nr_gin']);
				        $nr_gfc_tot_anual += intval($item['nr_gfc']);
				        $nr_gcm_tot_anual += intval($item['nr_gcm']);
				        $nr_gp_tot_anual  += intval($item['nr_gp']);
				        $nr_de_tot_anual  += intval($item['nr_de']);
				        $nr_cf_tot_anual  += intval($item['nr_cf']);
				        $nr_cd_tot_anual  += intval($item['nr_cd']);

				        $nr_total_tot_anual = $nr_ai_tot_anual  + $nr_grc_tot_anual + $nr_gj_tot_anual  + $nr_gc_tot_anual  + $nr_gti_tot_anual + $nr_gin_tot_anual + $nr_gfc_tot_anual + $nr_gcm_tot_anual + $nr_gp_tot_anual + $nr_de_tot_anual + $nr_cf_tot_anual + $nr_cd_tot_anual;
					}
				}
			}

			// gravar a resultado do período
			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia' 	  => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media' 			  => 'S',
					'nr_ai' 			  => $nr_ai_tot_anual,
					'nr_grc' 			  => $nr_grc_tot_anual,
					'nr_gj' 			  => $nr_gj_tot_anual,
					'nr_gc' 			  => $nr_gc_tot_anual,
					'nr_gti' 			  => $nr_gti_tot_anual,
					'nr_gin' 			  => $nr_gin_tot_anual,
					'nr_gfc' 			  => $nr_gfc_tot_anual,
					'nr_gcm' 			  => $nr_gcm_tot_anual,
					'nr_gp' 			  => $nr_gp_tot_anual,
					'nr_de' 			  => $nr_de_tot_anual,
					'nr_cf' 			  => $nr_cf_tot_anual,
					'nr_cd' 			  => $nr_cd_tot_anual,
					'nr_total' 			  => $nr_total_tot_anual,
					'nr_meta' 			  => $nr_meta,
					'observacao' 		  => '',
					'cd_usuario' 		  => $this->session->userdata('codigo')
				);


				$this->juridico_solicitacao_parecer_gerencia_novo_model->salvar($args);
			}

			$this->juridico_solicitacao_parecer_gerencia_novo_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->session->userdata('codigo'));

			redirect("indicador_plugin/juridico_solicitacao_parecer_gerencia_novo", "refresh");
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
}
?>