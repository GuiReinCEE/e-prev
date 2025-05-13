<?php
class Controladoria_crescimento_patr_segmento extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_CRESCIMENTO_PATRIMONIAL_EM_RELACAO_AO_SEGMENTO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->load->model('indicador_plugin/controladoria_crescimento_patr_segmento_model');
    }

    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'CQ'))
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

	        $this->load->view('indicador_plugin/controladoria_crescimento_patr_segmento/index',$data);
		}
    }

    function listar()
    {
    	if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'CQ'))
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
	        
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$data['tabela'] = $tabela;		

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];			

			$this->controladoria_crescimento_patr_segmento_model->listar( $result, $args );
			$data['collection'] = $result->result_array();

			$this->controladoria_crescimento_patr_segmento_model->ultimo_fechamento($result, $args);
			$data['row'] = $result->row_array();
			
			$this->load->view('indicador_plugin/controladoria_crescimento_patr_segmento/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function cadastro($cd_controladoria_crescimento_patr_segmento = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'CQ'))
		{
			$args   = array();
			$data   = array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_controladoria_crescimento_patr_segmento'] = $cd_controladoria_crescimento_patr_segmento;
			
			if(intval($args['cd_controladoria_crescimento_patr_segmento']) == 0)
			{
				$this->controladoria_crescimento_patr_segmento_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_controladoria_crescimento_patr_segmento'] = $args['cd_controladoria_crescimento_patr_segmento'];
				$data['row']['nr_valor_1']            = 0;
				$data['row']['nr_valor_2']            = 0;
				$data['row']['fl_media']              = "";
				$data['row']['observacao']            = "";
				$data['row']['dt_referencia']         = '';
				$data['row']['ano_referencia']        = '';
				$data['row']['mes_referencia']        = '';
				$data['row']['nr_meta']               = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
			}			
			else
			{
				$this->controladoria_crescimento_patr_segmento_model->carrega($result, $args);
				$data['row'] = $result->row_array();
			}

			$this->load->view('indicador_plugin/controladoria_crescimento_patr_segmento/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'CQ'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_controladoria_crescimento_patr_segmento'] = intval($this->input->post('cd_controladoria_crescimento_patr_segmento', true));
			$args["cd_indicador_tabela"]   = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]         = $this->input->post("dt_referencia", true);
			$args["fl_media"]              = "N";
			$args["nr_valor_1"]            = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]            = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_meta"]               = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"]            = $this->input->post("observacao", true);
			$args["cd_usuario"]            = $this->session->userdata('codigo');

			$this->controladoria_crescimento_patr_segmento_model->salvar($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_crescimento_patr_segmento", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_controladoria_crescimento_patr_segmento)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'CQ'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_controladoria_crescimento_patr_segmento'] = $cd_controladoria_crescimento_patr_segmento;
			$args["cd_usuario"]            = $this->session->userdata('codigo');
			
			$this->controladoria_crescimento_patr_segmento_model->excluir($result, $args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_crescimento_patr_segmento", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'CQ'))
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
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$this->load->helper(array('indicador'));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->controladoria_crescimento_patr_segmento_model->listar( $result, $args );
			$collection = $result->result_array();

			$this->controladoria_crescimento_patr_segmento_model->ultimo_fechamento($result, $args);
			$row = $result->row_array();
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;

			$nr_valor_1_total        = 0;
			$nr_valor_2_total        = 0;
			$nr_segmento_total       = 0;
			$nr_fceee_total          = 0;
			$nr_fceee_segmento_total = 0;
			$nr_meta_total           = 0;
			
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

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$contador_ano_atual++;

						$nr_valor_1_total = $item["nr_valor_1"];
						$nr_valor_2_total = $item["nr_valor_2"];
						$nr_meta_total    = $item["nr_meta"];

						$nr_segmento_total       = $item["nr_segmento"];
						$nr_fceee_total          = $item["nr_fceee"];
						$nr_fceee_segmento_total = $item["nr_fceee_segmento"];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item["nr_valor_1"]);
					$indicador[$linha][2] = app_decimal_para_php($item["nr_valor_2"]);
					$indicador[$linha][3] = app_decimal_para_php($item["nr_segmento"]);
					$indicador[$linha][4] = app_decimal_para_php($item["nr_fceee"]);
					$indicador[$linha][5] = app_decimal_para_php($item["nr_fceee_segmento"]);
					$indicador[$linha][6] = app_decimal_para_php($item["nr_meta"]);
					$indicador[$linha][7] = $observacao;
					
					$linha++;
				}
			}	

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				/*
				$nr_segmento_total       = (($nr_valor_1_total / $row["nr_valor_1"]) - 1) * 100;
				$nr_fceee_total          = (($nr_valor_2_total / $row["nr_valor_2"]) - 1) * 100;
				$nr_fceee_segmento_total = (((1+$nr_fceee_total)/(1+$nr_segmento_total))-1);
				*/
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';

				$linha++;
				
				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_valor_1_total;
				$indicador[$linha][2] = $nr_valor_2_total;
				$indicador[$linha][3] = app_decimal_para_php(number_format($nr_segmento_total, 2, ',', '.'));
				$indicador[$linha][4] = app_decimal_para_php(number_format($nr_fceee_total, 2, ',', '.'));
				$indicador[$linha][5] = app_decimal_para_php(number_format($nr_fceee_segmento_total, 2, ',', '.'));
				$indicador[$linha][6] = app_decimal_para_php($nr_meta_total);
				$indicador[$linha][7] = '';
		
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]), 'left' );
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0;6,6,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;6,6,1,$linha_sem_media",
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
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'CQ'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->controladoria_crescimento_patr_segmento_model->listar( $result, $args );
			$collection = $result->result_array();

			$this->controladoria_crescimento_patr_segmento_model->ultimo_fechamento($result, $args);
			$row = $result->row_array();

			$media_ano            = array();
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media                = 0;

			$nr_valor_1_total        = 0;
			$nr_valor_2_total        = 0;
			$nr_segmento_total       = 0;
			$nr_fceee_total          = 0;
			$nr_fceee_segmento_total = 0;
			$nr_meta_total           = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					
					$nr_valor_1_total = $item["nr_valor_1"];
					$nr_valor_2_total = $item["nr_valor_2"];
					$nr_meta_total    = $item["nr_meta"];

					$nr_segmento_total       = $item["nr_segmento"];
					$nr_fceee_total          = $item["nr_fceee"];
					$nr_fceee_segmento_total = $item["nr_fceee_segmento"];
				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}
				/*
				$nr_segmento_total       = (($nr_valor_1_total / $row["nr_valor_1"]) - 1) * 100;
				$nr_fceee_total          = (($nr_valor_2_total / $row["nr_valor_2"]) - 1) * 100;
				$nr_fceee_segmento_total = (((1+$nr_fceee_total)/(1+$nr_segmento_total))-1);
				*/
				$args['cd_controladoria_crescimento_patr_segmento'] = 0;
				$args["cd_indicador_tabela"]                        = $args['cd_indicador_tabela'];
				$args["dt_referencia"]                              = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args['fl_media']                                   = 'S';
				$args['nr_valor_1']                                 = $nr_valor_1_total;
				$args['nr_valor_2']                                 = $nr_valor_2_total;
				$args["nr_meta"]                                    = $nr_meta_total;
				$args['observacao']                                 = '';
				$args["cd_usuario"]                                 = $this->session->userdata('codigo');

				$this->controladoria_crescimento_patr_segmento_model->salvar($result, $args);
			}

			$this->controladoria_crescimento_patr_segmento_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}

		redirect("indicador_plugin/controladoria_crescimento_patr_segmento", "refresh");
	}
}
?>