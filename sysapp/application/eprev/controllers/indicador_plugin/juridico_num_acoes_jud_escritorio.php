<?php
class juridico_num_acoes_jud_escritorio extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_NUMERO_DE_ACOES_JUDICIAIS_ESCRITORIOS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_num_acoes_jud_escritorio_model' );
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

	        $this->load->view('indicador_plugin/juridico_num_acoes_jud_escritorio/index',$data);
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
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_num_acoes_jud_escritorio_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_num_acoes_jud_escritorio/partial_result', $data);
        }
    }

	function detalhe($cd_juridico_num_acoes_jud_escritorio = 0)
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

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_num_acoes_jud_escritorio'] = intval($cd_juridico_num_acoes_jud_escritorio);
			
			if(intval($args['cd_juridico_num_acoes_jud_escritorio']) == 0)
			{
				$this->juridico_num_acoes_jud_escritorio_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_juridico_num_acoes_jud_escritorio'] = $args['cd_juridico_num_acoes_jud_escritorio'];
				$data['row']['nr_juchem']       = "";
				$data['row']['nr_ribeiro']      = "";
				$data['row']['nr_cenco']        = "";
				$data['row']['observacao']      = "";
				$data['row']['dt_referencia']   = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']         = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->juridico_num_acoes_jud_escritorio_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/juridico_num_acoes_jud_escritorio/detalhe', $data);
		}
	} 	
	
	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_num_acoes_jud_escritorio'] = intval($this->input->post('cd_juridico_num_acoes_jud_escritorio', true));
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]       = $this->input->post("dt_referencia", true);
			$args["fl_media"]            = $this->input->post("fl_media", true);
			$args["nr_juchem"]           = app_decimal_para_db($this->input->post("nr_juchem", true));
			$args["nr_ribeiro"]          = app_decimal_para_db($this->input->post("nr_ribeiro", true));
			$args["nr_cenco"]            = app_decimal_para_db($this->input->post("nr_cenco", true));
			$args["nr_meta"]             = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]          = $this->input->post("observacao", true);
			$args["cd_usuario"]          = $this->session->userdata('codigo');

			$this->juridico_num_acoes_jud_escritorio_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_num_acoes_jud_escritorio", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }
	}	
	
	function excluir($cd_juridico_num_acoes_jud_escritorio = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_num_acoes_jud_escritorio'] = intval($cd_juridico_num_acoes_jud_escritorio);
			$args["cd_usuario"]                   = $this->session->userdata('codigo');
			
			$this->juridico_num_acoes_jud_escritorio_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_num_acoes_jud_escritorio", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
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

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->juridico_num_acoes_jud_escritorio_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador          = array();
			$linha              = 0;
			$ar_tendencia       = array();
			$contador_ano_atual = 0;
			$nr_juchem_total    = 0;
			$pr_juchem_total    = 0;
			$nr_ribeiro_total   = 0;
			$pr_ribeiro_total   = 0;
			$nr_cenco_total     = 0;
			$pr_cenco_total     = 0;
			$nr_total_total     = 0;
			$nr_meta            = 0;
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
						$referencia = "Total de ".$item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$nr_juchem_total  += $item["nr_juchem"];
						$pr_juchem_total  += $item["pr_juchem"];
						$nr_ribeiro_total += $item["nr_ribeiro"];
						$pr_ribeiro_total += $item["pr_ribeiro"];
						$nr_cenco_total   += $item["nr_cenco"];
						$pr_cenco_total   += $item["pr_cenco"];
						$nr_total_total   += $item["nr_total"];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item["nr_juchem"];
					$indicador[$linha][2] = $item["pr_juchem"];
					$indicador[$linha][3] = $item["nr_ribeiro"];
					$indicador[$linha][4] = $item["pr_ribeiro"];
					$indicador[$linha][5] = $item["nr_cenco"];
					$indicador[$linha][6] = $item["pr_cenco"];
					$indicador[$linha][7] = $item["nr_total"];
					$indicador[$linha][8] = $nr_meta;
					$indicador[$linha][9] = $item["observacao"];
					$linha++;
				}
			}	

			$linha_sem_resultado = $linha;
			
			
			$indicador[$linha][0] = "M�dia de ".intval($tabela[0]['nr_ano_referencia']);
			$indicador[$linha][1] = $nr_juchem_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1);
			$indicador[$linha][2] = $pr_juchem_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1);
			$indicador[$linha][3] = $nr_ribeiro_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1);
			$indicador[$linha][4] = $pr_ribeiro_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1);
			$indicador[$linha][5] = $nr_cenco_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1);
			$indicador[$linha][6] = $pr_cenco_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1);
			$indicador[$linha][7] = $nr_total_total / (intval($contador_ano_atual) > 0 ? intval($contador_ano_atual) : 1);
			$indicador[$linha][8] = "";
			$indicador[$linha][9] = "";
			$linha++;	
			
			
			$indicador[$linha][0] = "Total de ".intval($tabela[0]['nr_ano_referencia']);
			$indicador[$linha][1] = $nr_juchem_total;
			$indicador[$linha][2] = ($nr_juchem_total / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100;
			$indicador[$linha][3] = $nr_ribeiro_total;
			$indicador[$linha][4] = ($nr_ribeiro_total / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100;
			$indicador[$linha][5] = $nr_cenco_total;
			$indicador[$linha][6] = ($nr_cenco_total / (intval($nr_total_total) > 0 ? intval($nr_total_total) : 1)) * 100;
			$indicador[$linha][7] = $nr_total_total;
			$indicador[$linha][8] = $nr_meta;
			$indicador[$linha][9] = "";
			$linha++;
			
			$linha_total = $linha;
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S',2,'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S',2,'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S',2,'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode(nl2br($indicador[$i][9])), 'left');
				
				$linha++;
			}

			// gerar gr�fico
			$coluna_para_ocultar = '8';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::PIZZA,
				'1,1,0,0;3,3,0,0;5,5,0,0',
				"0,0,$linha_total,$linha_total",
				"1,1,$linha_total,$linha_total;3,3,$linha_total,$linha_total;5,5,$linha_total,$linha_total",
				usuario_id(),
				$coluna_para_ocultar
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
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->juridico_num_acoes_jud_escritorio_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador_ano_atual    = 0;
			$nr_juchem_total    = 0;
			$pr_juchem_total    = 0;
			$nr_ribeiro_total   = 0;
			$pr_ribeiro_total   = 0;
			$nr_cenco_total     = 0;
			$pr_cenco_total     = 0;
			$nr_total_total     = 0;
			$nr_meta            = 0;		
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_meta = $item['nr_meta'];

					if(trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$nr_juchem_total  += $item["nr_juchem"];
						$pr_juchem_total  += $item["pr_juchem"];
						$nr_ribeiro_total += $item["nr_ribeiro"];
						$pr_ribeiro_total += $item["pr_ribeiro"];
						$nr_cenco_total   += $item["nr_cenco"];
						$pr_cenco_total   += $item["pr_cenco"];
						$nr_total_total   += $item["nr_total"];
					}
				}
			}

			// gravar a resultado do per�odo
			if(intval($contador_ano_atual) > 0)
			{	
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_juchem']           = floatval($nr_juchem_total);
				$args['nr_ribeiro']          = floatval($nr_ribeiro_total);
				$args['nr_cenco']            = floatval($nr_cenco_total);
				$args["nr_meta"]             = floatval($nr_meta);
				$args["observacao"]          = "";
				$args["cd_usuario"]          = $this->session->userdata('codigo');
	
				$this->juridico_num_acoes_jud_escritorio_model->atualiza_fechar_periodo($result, $args);
			}

			$this->juridico_num_acoes_jud_escritorio_model->fechar_periodo($result, $args);

		}

		redirect("indicador_plugin/juridico_num_acoes_jud_escritorio", "refresh");
	}	
}
?>