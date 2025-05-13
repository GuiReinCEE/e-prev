<?php
class juridico_num_acoes_jud extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_NUMERO_DE_ACOES_JUDICIAIS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_num_acoes_jud_model' );
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

	        $this->load->view('indicador_plugin/juridico_num_acoes_jud/index',$data);
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
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_num_acoes_jud_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_num_acoes_jud/partial_result', $data);
        }
    }

	function detalhe($cd_juridico_num_acoes_jud = 0)
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

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_num_acoes_jud'] = intval($cd_juridico_num_acoes_jud);
			
			if(intval($args['cd_juridico_num_acoes_jud']) == 0)
			{
				$this->juridico_num_acoes_jud_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_juridico_num_acoes_jud'] = $args['cd_juridico_num_acoes_jud'];
				$data['row']['nr_nova']       = "";
				$data['row']['qt_acoes']      = "";
				$data['row']['nr_encerrada']  = "";
				$data['row']['observacao']    = "";
				$data['row']['dt_referencia'] = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']       = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->juridico_num_acoes_jud_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/juridico_num_acoes_jud/detalhe', $data);
		}
	}	
		
	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_num_acoes_jud'] = intval($this->input->post('cd_juridico_num_acoes_jud', true));
			$args["cd_indicador_tabela"]    = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]          = $this->input->post("dt_referencia", true);
			$args["fl_media"]               = $this->input->post("fl_media", true);
			$args["nr_nova"]                = app_decimal_para_db($this->input->post("nr_nova", true));
			$args["nr_encerrada"]           = app_decimal_para_db($this->input->post("nr_encerrada", true));
			$args["nr_meta"]                = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]             = $this->input->post("observacao", true);
			$args["cd_usuario"]             = $this->session->userdata('codigo');

			$this->juridico_num_acoes_jud_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_num_acoes_jud", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}	
	
	function excluir($cd_juridico_num_acoes_jud = 0)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_num_acoes_jud'] = intval($cd_juridico_num_acoes_jud);
			$args["cd_usuario"]             = $this->session->userdata('codigo');
			
			$this->juridico_num_acoes_jud_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_num_acoes_jud", "refresh");
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
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->juridico_num_acoes_jud_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador          = array();
			$linha              = 0;
			$ar_tendencia       = array();
			$contador_ano_atual = 0;
			$nr_nova_total      = 0;
			$nr_encerrada_total = 0;
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
						$referencia = "Média de ".$item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$nr_nova_total      += $item["nr_nova"];
						$nr_encerrada_total += $item["nr_encerrada"];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item["nr_nova"];
					$indicador[$linha][2] = $item["nr_encerrada"];
					$indicador[$linha][3] = $nr_meta;
					$indicador[$linha][4] = $item["observacao"];
					
					$linha++;
				}
			}	

			$linha_sem_media = $linha;
			
			$nr_nova_media     = ($nr_nova_total / ($contador_ano_atual == 0 ? 1 : $contador_ano_atual));
			$nr_encerrada_media = ($nr_encerrada_total / ($contador_ano_atual == 0 ? 1 : $contador_ano_atual));			

			$indicador[$linha][0] = "Média de ".intval($tabela[0]['nr_ano_referencia']);
			$indicador[$linha][1] = $nr_nova_media;
			$indicador[$linha][2] = $nr_encerrada_media;
			$indicador[$linha][3] = $nr_meta;
			$indicador[$linha][4] = "";
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, utf8_encode(nl2br($indicador[$i][4])), 'left');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar = 3;
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'1,1,0,0;2,2,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media",
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
			
	        $this->juridico_num_acoes_jud_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador_ano_atual = 0;
			$nr_nova_total      = 0;
			$nr_encerrada_total = 0;
			$nr_meta            = 0;			
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$nr_meta = $item['nr_meta'];

					if(trim($item['fl_media']) != 'S')
					{
						$contador_ano_atual++;
						$nr_nova_total      += $item["nr_nova"];
						$nr_encerrada_total += $item["nr_encerrada"];
					}
				}
			}

			// gravar a resultado do período
			if(intval($contador_ano_atual) > 0)
			{
				$args["cd_indicador_tabela"] = $args['cd_indicador_tabela'];
				$args["dt_referencia"]       = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['nr_nova']             = floatval(($nr_nova_total / ($contador_ano_atual == 0 ? 1 : $contador_ano_atual)));
				$args['nr_encerrada']        = floatval(($nr_encerrada_total / ($contador_ano_atual == 0 ? 1 : $contador_ano_atual)));
				$args["nr_meta"]             = floatval($nr_meta);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->juridico_num_acoes_jud_model->atualiza_fechar_periodo($result, $args);
			}

			$this->juridico_num_acoes_jud_model->fechar_periodo($result, $args);

		}

		redirect("indicador_plugin/juridico_num_acoes_jud", "refresh");
	}	
}
?>