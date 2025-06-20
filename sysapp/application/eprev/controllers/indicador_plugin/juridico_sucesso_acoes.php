<?php
class juridico_sucesso_acoes extends Controller
{
	var $enum_indicador = 0;
	var $nr_meta_improcede = "20,29% a 69,13%";
	var $nr_meta_parcial   = "6,52% a 25,18%";
	var $nr_meta_procede   = "22,40% a 54,53%";	

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_SUCESSO_DAS_ACOES);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_sucesso_acoes_model' );   		
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

	        $this->load->view('indicador_plugin/juridico_sucesso_acoes/index',$data);
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
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			
			$data['nr_meta_improcede'] = $this->nr_meta_improcede;
			$data['nr_meta_parcial']   = $this->nr_meta_parcial;
			$data['nr_meta_procede']   = $this->nr_meta_procede;			
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->juridico_sucesso_acoes_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/juridico_sucesso_acoes/partial_result', $data);
        }    	
    }

	function detalhe($cd_juridico_sucesso_acoes = 0)
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
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_juridico_sucesso_acoes'] = $cd_juridico_sucesso_acoes;
			
			if(intval($args['cd_juridico_sucesso_acoes']) == 0)
			{
				$data['row']['cd_juridico_sucesso_acoes'] = $args['cd_juridico_sucesso_acoes'];
				$data['row']['cd_etapa']       = "";
				$data['row']['nr_valor_1']     = "";
				$data['row']['nr_valor_2']     = "";
				$data['row']['nr_valor_3']     = "";
				$data['row']['nr_valor_4']     = "";
				$data['row']['fl_media']       = "";
				$data['row']['observacao']     = "";
				$data['row']['ano_referencia'] = "";
				$data['row']['ano_referencia'] = "";
				$data['row']['dt_referencia']  = "";
				$data['row']['nr_meta']        = "";
				$data['row']['nr_meta_min']    = "";
				$data['row']['nr_meta_max']    = "";
			}			
			else
			{
				$this->juridico_sucesso_acoes_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			$this->juridico_sucesso_acoes_model->etapa($result, $args);
			$data['ar_fase'] = $result->result_array();			
			
			$this->load->view('indicador_plugin/juridico_sucesso_acoes/detalhe', $data);
		}        
	}	
	
	function salvar()
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_juridico_sucesso_acoes'] = intval($this->input->post('cd_juridico_sucesso_acoes', true));
			$args["cd_indicador_tabela"]       = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]             = (trim($this->input->post("dt_referencia", true)) == "" ? "01/".(intval($this->input->post("cd_etapa", true)) + 1)."/".$this->input->post("ano_referencia", true) : $this->input->post("dt_referencia", true));
			$args["fl_media"]                  = $this->input->post("fl_media", true);
			$args["cd_etapa"]                  = $this->input->post("cd_etapa", true);

			$args["nr_valor_1"]                = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_valor_3"]                = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_valor_4"]                = app_decimal_para_db($this->input->post("nr_valor_4", true));
			
			$args["nr_meta"]                   = app_decimal_para_db($this->input->post("nr_meta", true));
			$args["nr_meta_min"]               = app_decimal_para_db($this->input->post("nr_meta_min", true));
			$args["nr_meta_max"]               = app_decimal_para_db($this->input->post("nr_meta_max", true));
            $args["observacao"]                = $this->input->post("observacao", true);
			$args["cd_usuario"]                = $this->session->userdata('codigo');

			$this->juridico_sucesso_acoes_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_sucesso_acoes", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO N�O PERMITIDO");
        }        
	}	

	function excluir($cd_juridico_sucesso_acoes)
	{
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_juridico_sucesso_acoes'] = $cd_juridico_sucesso_acoes;
			$args["cd_usuario"]                = $this->session->userdata('codigo');
			
			$this->juridico_sucesso_acoes_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/juridico_sucesso_acoes", "refresh");
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
			$data['label_10'] = $this->label_10;
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

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

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_11']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_12']), 'background,center');

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_9']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_10']), 'background,center');


			$this->load->model('indicador_plugin/juridico_sucesso_acoes_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			$this->juridico_sucesso_acoes_model->listar( $result, $args );
			$collection = $result->result_array();

			$indicador=array();
			$linha=0;

			$contador = sizeof($collection);
			$media_ano=array();
			$nr_soma = 0;
			$cent_valor1 = 0;
			$cent_valor2 = 0;
			$cent_valor3 = 0;
			$cent_valor4 = 0;
			
			$nr_tot_cent_valor_1 = 0;
			$nr_tot_cent_valor_2 = 0;
			$nr_tot_cent_valor_3 = 0;
			$nr_tot_cent_valor_4 = 0;				
			
			$nr_tot_valor_1 = 0;
			$nr_tot_valor_2 = 0;
			$nr_tot_valor_3 = 0;
			$nr_tot_valor_4 = 0;			
			
			$soma = 0;
			foreach( $collection as $item )
			{
				$nr_meta = $item["nr_meta"];
				$observacao = $item["observacao"];

				if($item['fl_media'] != 'S')
				{
					$referencia = substr($item['mes_referencia'], 0, 2);

					switch ($referencia)
					{
						case '01':
							$referencia = 'Fase Inicial';
							break;
						case '02':
							$referencia = '1� Inst�ncia';
							break;
						case '03':
							$referencia = '2� Inst�ncia';
							break;
						case '04':
							$referencia = '3� Inst�ncia';
							break;
					}

					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					$nr_valor_3 = $item["nr_valor_3"];
					$nr_valor_4 = $item["nr_valor_4"];
					
					$nr_tot_valor_1 += floatval($nr_valor_1);
					$nr_tot_valor_2 += floatval($nr_valor_2);
					$nr_tot_valor_3 += floatval($nr_valor_3);
					$nr_tot_valor_4 += floatval($nr_valor_4);						
				
					
					$nr_meta_min = $item["nr_meta_min"];
					$nr_meta_max = $item["nr_meta_max"];
					$nr_percentual_f = '';

					$nr_soma = $nr_valor_1 + $nr_valor_2 + $nr_valor_3 + $nr_valor_4;

					$soma += $nr_soma;

					if($nr_soma > 0){
						$cent_valor1 = (floatval($nr_valor_1)/floatval($nr_soma) * 100);
						$cent_valor2 = (floatval($nr_valor_2)/floatval($nr_soma) * 100);
						$cent_valor3 = (floatval($nr_valor_3)/floatval($nr_soma) * 100);
						$cent_valor4 = (floatval($nr_valor_4)/floatval($nr_soma) * 100);
					}

					$nr_tot_cent_valor_1 += floatval($cent_valor1);
					$nr_tot_cent_valor_2 += floatval($cent_valor2);
					$nr_tot_cent_valor_3 += floatval($cent_valor3);
					$nr_tot_cent_valor_4 += floatval($cent_valor4);	
					
					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($cent_valor1);
					$indicador[$linha][3] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][4] = ($cent_valor2);
					$indicador[$linha][5] = app_decimal_para_php($nr_valor_3);
					$indicador[$linha][6] = ($cent_valor3);
					$indicador[$linha][7] = app_decimal_para_php($nr_valor_4);
					$indicador[$linha][8] = ($cent_valor4);
					$indicador[$linha][9] = app_decimal_para_php($nr_soma);
					$indicador[$linha][10] = $observacao;
					$indicador[$linha][11] = app_decimal_para_php($nr_meta_min);
					$indicador[$linha][12] = app_decimal_para_php($nr_meta_max);

					$linha++;						
				}
			}

			// LINHA DE TEND�NCIA - CURVA LOGARITMICA

			$linha_sem_media = $linha;

			$indicador[$linha][0] = 'Resultado';
			$indicador[$linha][1] = $nr_tot_valor_1;
			$indicador[$linha][2] = "";
			$indicador[$linha][3] = $nr_tot_valor_2;
			$indicador[$linha][4] = (floatval($nr_tot_cent_valor_2)/3);
			$indicador[$linha][5] = $nr_tot_valor_3;
			$indicador[$linha][6] = (floatval($nr_tot_cent_valor_3)/3);
			$indicador[$linha][7] = $nr_tot_valor_4;
			$indicador[$linha][8] = (floatval($nr_tot_cent_valor_4)/3);
			$indicador[$linha][9] = intval($soma);
			$indicador[$linha][10] = '';
			$indicador[$linha][11] = '';
			$indicador[$linha][12] = '';
			$linha++;
			
			#### META ####
			$linha_meta = $linha;
			$indicador[$linha][0]  = "Meta";
			$indicador[$linha][1]  = "";
			$indicador[$linha][2]  = "";
			$indicador[$linha][3]  = "";
			$indicador[$linha][4]  = $this->nr_meta_improcede;
			$indicador[$linha][5]  = "";
			$indicador[$linha][6]  = $this->nr_meta_parcial;
			$indicador[$linha][7]  = "";
			$indicador[$linha][8]  = $this->nr_meta_procede;
			$indicador[$linha][9]  = "";
			$indicador[$linha][10] = '';
			$indicador[$linha][11] = '';
			$indicador[$linha][12] = '';
			$linha++;			
			

			$linha = 1;
			for( $i=0; $i<count($indicador); $i++ )
			{
				if($linha_meta == $i)
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center');
				}
				else
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S' );
				}				
				
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][11]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][12]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12, $linha, utf8_encode($indicador[$i][10]), 'left');	
				
				$linha++;
			}

			// gerar gr�fico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_ACUMULADO,
				'2,2,0,0;4,4,0,0;6,6,0,0;8,8,0,0',
				"0,0,1,$linha_sem_media",
				"2,2,1,$linha_sem_media;4,4,1,$linha_sem_media;6,6,1,$linha_sem_media;8,8,1,$linha_sem_media",
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
			$args["cd_usuario"]          = $this->session->userdata('codigo');
	
			$this->juridico_sucesso_acoes_model->fechar_periodo($result, $args);
		}
		redirect("indicador_plugin/juridico_sucesso_acoes", "refresh");
	}	
}
?>