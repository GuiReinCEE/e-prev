<?php
class Controladoria_informativo_gerencial extends Controller
{	
	var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_SATISFACAO_INFORMATIVO_GERENCIAL_NOVO_2014);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/controladoria_informativo_gerencial_model' );
    }
	
	function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'GC'))
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

	        $this->load->view('indicador_plugin/controladoria_informativo_gerencial/index',$data);
		}
    }
	
	function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'GC'))
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
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->controladoria_informativo_gerencial_model->listar($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('indicador_plugin/controladoria_informativo_gerencial/index_result', $data);
        }
    }
	
	function cadastro($cd_controladoria_informativo_gerencial = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GC'))
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

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_controladoria_informativo_gerencial'] = $cd_controladoria_informativo_gerencial;
			
			if(intval($args['cd_controladoria_informativo_gerencial']) == 0)
			{
				$this->controladoria_informativo_gerencial_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_controladoria_informativo_gerencial'] = $args['cd_controladoria_informativo_gerencial'];
				$data['row']['ano_referencia']                         = "";
				$data['row']['mes_referencia']                         = "";
				$data['row']['nr_repondente']                          = "";
				$data['row']['nr_clareza_meta']                        = "";
				$data['row']['nr_clareza_1']                           = "";
				$data['row']['nr_clareza_2']                           = "";
				$data['row']['nr_clareza_3']                           = "";
				$data['row']['nr_clareza_4']                           = "";
				$data['row']['nr_clareza_5']                           = "";
				$data['row']['nr_exatidao_meta']                       = "";
				$data['row']['nr_exatidao_1']                          = "";
				$data['row']['nr_exatidao_2']                          = "";
				$data['row']['nr_exatidao_3']                          = "";
				$data['row']['nr_exatidao_4']                          = "";
				$data['row']['nr_exatidao_5']                          = "";
				$data['row']['nr_tempestividade_meta']                 = "";
				$data['row']['nr_tempestividade_1']                    = "";
				$data['row']['nr_tempestividade_2']                    = "";
				$data['row']['nr_tempestividade_3']                    = "";
				$data['row']['nr_tempestividade_4']                    = "";
				$data['row']['nr_tempestividade_5']                    = "";
				$data['row']['nr_relevancia_meta']                     = "";
				$data['row']['nr_relevancia_1']                        = "";
				$data['row']['nr_relevancia_2']                        = "";
				$data['row']['nr_relevancia_3']                        = "";
				$data['row']['nr_relevancia_4']                        = "";
				$data['row']['nr_relevancia_5']                        = "";
				$data['row']['nr_tempestividade']                      = "";
				$data['row']['nr_relevancia']                          = "";
				$data['row']['observacao']                             = "";				
			}			
			else
			{
				$this->controladoria_informativo_gerencial_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/controladoria_informativo_gerencial/cadastro', $data);
		}
	}
	

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GC'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_controladoria_informativo_gerencial'] = intval($this->input->post('cd_controladoria_informativo_gerencial', true));
			$args["cd_indicador_tabela"]                    = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                          = $this->input->post("dt_referencia", true);
			$args["fl_media"]                               = $this->input->post("fl_media", true);
			$args["nr_repondente"]                          = app_decimal_para_db($this->input->post("nr_repondente", true));
			$args["nr_clareza_meta"]                        = app_decimal_para_db($this->input->post("nr_clareza_meta", true));
			$args["nr_clareza_1"]                           = app_decimal_para_db($this->input->post("nr_clareza_1", true));
			$args["nr_clareza_2"]                           = app_decimal_para_db($this->input->post("nr_clareza_2", true));
			$args["nr_clareza_3"]                           = app_decimal_para_db($this->input->post("nr_clareza_3", true));
			$args["nr_clareza_4"]                           = app_decimal_para_db($this->input->post("nr_clareza_4", true));
			$args["nr_clareza_5"]                           = app_decimal_para_db($this->input->post("nr_clareza_5", true));
			$args["nr_exatidao_meta"]                       = app_decimal_para_db($this->input->post("nr_exatidao_meta", true));
			$args["nr_exatidao_1"]                          = app_decimal_para_db($this->input->post("nr_exatidao_1", true));
			$args["nr_exatidao_2"]                          = app_decimal_para_db($this->input->post("nr_exatidao_2", true));
			$args["nr_exatidao_3"]                          = app_decimal_para_db($this->input->post("nr_exatidao_3", true));
			$args["nr_exatidao_4"]                          = app_decimal_para_db($this->input->post("nr_exatidao_4", true));
			$args["nr_exatidao_5"]                          = app_decimal_para_db($this->input->post("nr_exatidao_5", true));
			$args["nr_tempestividade_meta"]                 = app_decimal_para_db($this->input->post("nr_tempestividade_meta", true));
			$args["nr_tempestividade_1"]                    = app_decimal_para_db($this->input->post("nr_tempestividade_1", true));
			$args["nr_tempestividade_2"]                    = app_decimal_para_db($this->input->post("nr_tempestividade_2", true));
			$args["nr_tempestividade_3"]                    = app_decimal_para_db($this->input->post("nr_tempestividade_3", true));
			$args["nr_tempestividade_4"]                    = app_decimal_para_db($this->input->post("nr_tempestividade_4", true));
			$args["nr_tempestividade_5"]                    = app_decimal_para_db($this->input->post("nr_tempestividade_5", true));
			$args["nr_relevancia_meta"]                     = app_decimal_para_db($this->input->post("nr_relevancia_meta", true));
			$args["nr_relevancia_1"]                        = app_decimal_para_db($this->input->post("nr_relevancia_1", true));
			$args["nr_relevancia_2"]                        = app_decimal_para_db($this->input->post("nr_relevancia_2", true));
			$args["nr_relevancia_3"]                        = app_decimal_para_db($this->input->post("nr_relevancia_3", true));
			$args["nr_relevancia_4"]                        = app_decimal_para_db($this->input->post("nr_relevancia_4", true));
			$args["nr_relevancia_5"]                        = app_decimal_para_db($this->input->post("nr_relevancia_5", true));
            $args["observacao"]                             = $this->input->post("observacao", true);
			$args["cd_usuario"]                             = $this->session->userdata('codigo');

			$this->controladoria_informativo_gerencial_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_informativo_gerencial", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_controladoria_informativo_gerencial)
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_controladoria_informativo_gerencial'] = $cd_controladoria_informativo_gerencial;
			$args["cd_usuario"]                             = $this->session->userdata('codigo');
			
			$this->controladoria_informativo_gerencial_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_informativo_gerencial", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GC'))
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
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']." Meta"), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_3']." Meta"), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_4']." Meta"), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_5']." Meta"), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_7']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->controladoria_informativo_gerencial_model->listar( $result, $args );
			$collection = $result->result_array();
			
			$indicador            = array();
			$linha                = 0;
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media_ano            = array();
			$nr_media             = 0;
			$media                = 0;
			
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
					
					$nr_repondente          = $item['nr_repondente'];
					$nr_clareza             = $item['nr_clareza'];
					$nr_clareza_meta        = $item['nr_clareza_meta'];
					$nr_exatidao            = $item['nr_exatidao'];
					$nr_exatidao_meta       = $item['nr_exatidao_meta'];
					$nr_tempestividade      = $item['nr_tempestividade'];
					$nr_tempestividade_meta = $item['nr_tempestividade_meta'];
					$nr_relevancia          = $item['nr_relevancia'];
					$nr_relevancia_meta     = $item['nr_relevancia_meta'];
					$nr_satisfacao          = $item['nr_satisfacao'];
					$nr_meta                = $item['nr_meta'];
					$nr_observacao          = $item["observacao"];
					
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$media_ano[] = $nr_satisfacao;
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $nr_repondente;
					$indicador[$linha][2] = $nr_clareza;
					$indicador[$linha][3] = $nr_clareza_meta;
					$indicador[$linha][4] = $nr_exatidao;
					$indicador[$linha][5] = $nr_exatidao_meta;
					$indicador[$linha][6] = $nr_tempestividade;
					$indicador[$linha][7] = $nr_tempestividade_meta;
					$indicador[$linha][8] = $nr_relevancia;
					$indicador[$linha][9] = $nr_relevancia_meta;
					$indicador[$linha][10] = $nr_satisfacao;
					$indicador[$linha][11] = $nr_meta;
					$indicador[$linha][12] = $observacao;
					
					$linha++;
				}
			}	
				
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				foreach( $media_ano as $valor )
				{
					$media += $valor;
				}

				$media = number_format(( $media / sizeof($media_ano) ),2 );

				$indicador[$linha][0]  = '';
				$indicador[$linha][1]  = '';
				$indicador[$linha][2]  = '';
				$indicador[$linha][3]  = '';
				$indicador[$linha][4]  = '';
				$indicador[$linha][5]  = '';
				$indicador[$linha][6]  = '';
				$indicador[$linha][7]  = '';
				$indicador[$linha][8]  = '';
				$indicador[$linha][9]  = '';
				$indicador[$linha][10] = '';
				$indicador[$linha][11] = '';
				$indicador[$linha][12] = '';

				$linha++;

				$indicador[$linha][0]  = '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1]  = '';
				$indicador[$linha][2]  = '';
				$indicador[$linha][3]  = '';
				$indicador[$linha][4]  = '';
				$indicador[$linha][5]  = '';
				$indicador[$linha][6]  = '';
				$indicador[$linha][7]  = '';
				$indicador[$linha][8]  = '';		
				$indicador[$linha][9]  = '';		
				$indicador[$linha][10] = $media;		
				$indicador[$linha][11] = $nr_meta;	
				$indicador[$linha][12] = '';		
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][10]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][11]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12, $linha, utf8_encode(nl2br($indicador[$i][12])), 'left');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'10,10,0,0;11,11,0,0',
				"0,0,1,$linha_sem_media",
				"10,10,1,$linha_sem_media;11,11,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar,
				1
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
		if(indicador_db::verificar_permissao(usuario_id(), 'GC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->controladoria_informativo_gerencial_model->listar( $result, $args );
			$collection = $result->result_array();

			$indicador               = array();
			$linha                   = 0;
			$ar_tendencia            = array();
			$nr_meta                 = 0;
			$contador_ano_atual      = 0;
			$media_ano               = array();
			$media                   = 0;
			$total_nr_clareza        = 0;
		    $total_nr_exatidao       = 0;
		    $total_nr_tempestividade = 0;
		    $total_nr_relevancia     = 0; 
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}
				
				$total_nr_clareza        += $item['nr_clareza'];
				$total_nr_exatidao       += $item['nr_exatidao'];
				$total_nr_tempestividade += $item['nr_tempestividade'];
				$total_nr_relevancia     += $item['nr_relevancia'];
				$nr_meta                 = $item['nr_meta'];
				

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
				$args['nr_satisfacao']       = (floatval($total_nr_clareza/4) + floatval($total_nr_exatidao/4) + floatval($total_nr_tempestividade/4) + floatval($total_nr_relevancia/4)) /4;
				$args["nr_meta"]             = ($nr_meta);
				$args["cd_usuario"]          = $this->session->userdata('codigo');

				$this->controladoria_informativo_gerencial_model->atualiza_fechar_periodo($result, $args);
			}

			$this->controladoria_informativo_gerencial_model->fechar_periodo($result, $args);

			$this->criar_indicador();

		}

		redirect("indicador_plugin/controladoria_informativo_gerencial", "refresh");
	}
}
?>