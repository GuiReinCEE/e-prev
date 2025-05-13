<?php
class Controladoria_variacao_orcamentaria extends Controller
{
	var $enum_indicador     = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_VARIACAO_ORCAMENTARIA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}			
		
		$this->load->model('indicador_plugin/controladoria_variacao_orcamentaria_model');
    }

    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
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

	        $this->load->view('indicador_plugin/controladoria_variacao_orcamentaria/index',$data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
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
		
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			$data['tabela'] = $tabela;	

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			$this->controladoria_variacao_orcamentaria_model->listar( $result, $args );
			$data['collection'] = $result->result_array();

			$this->load->view('indicador_plugin/controladoria_variacao_orcamentaria/index_result', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

	function cadastro($cd_controladoria_variacao_orcamentaria = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
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
		
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['tabela'] = $tabela;
			
			$args['cd_controladoria_variacao_orcamentaria'] = $cd_controladoria_variacao_orcamentaria;

			if(intval($args['cd_controladoria_variacao_orcamentaria']) == 0)
			{
				$args['nr_ano_referencia'] = intval($data['tabela'][0]['nr_ano_referencia']);
				$this->controladoria_variacao_orcamentaria_model->carrega_referencia($result, $args);
				$arr = $result->row_array();
				
				$data['row']['cd_controladoria_variacao_orcamentaria'] = $args['cd_controladoria_variacao_orcamentaria'];
				$data['row']['nr_valor_1']                           = 0;
				$data['row']['nr_valor_2']                           = 0;
				$data['row']['fl_media']                             = "";
				$data['row']['observacao']                           = "";
				$data['row']['dt_referencia']                        = (isset($arr['dt_referencia_n']) ? $arr['dt_referencia_n'] : "");
				$data['row']['nr_meta']                              = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
				$data['row']['nr_meta_2']                            = (isset($arr['nr_meta_2']) ? $arr['nr_meta_2'] : 0);
				$data['row']['qt_ano']                            = (isset($arr['qt_ano']) ? $arr['qt_ano'] : 0);

			}			
			else
			{
				$this->controladoria_variacao_orcamentaria_model->carrega($result, $args);
				$data['row'] = $result->row_array();
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/controladoria_variacao_orcamentaria/cadastro', $data);
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
		{		
			$args   = array();
			$data   = array();
			$result = null;		
			
			$args['cd_controladoria_variacao_orcamentaria'] = intval($this->input->post('cd_controladoria_variacao_orcamentaria', true));
			$args["cd_indicador_tabela"]                  = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                        = $this->input->post("dt_referencia", true);
			$args["fl_media"]                             = $this->input->post("fl_media", true);
			$args["nr_valor_1"]                           = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]                           = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_meta"]                              = app_decimal_para_db($this->input->post("nr_meta", true));
			$args["nr_meta_2"]                            = app_decimal_para_db($this->input->post("nr_meta_2", true));
            $args["observacao"]                           = $this->input->post("observacao", true);
			$args["cd_usuario"]                           = $this->session->userdata('codigo');

			$this->controladoria_variacao_orcamentaria_model->salvar($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_variacao_orcamentaria", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function excluir($cd_controladoria_variacao_orcamentaria)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_controladoria_variacao_orcamentaria'] = $cd_controladoria_variacao_orcamentaria;
			$args["cd_usuario"]                           = $this->session->userdata('codigo');
			
			$this->controladoria_variacao_orcamentaria_model->excluir($result, $args);
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_variacao_orcamentaria", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function get_valores()
	{
		$args = array();
		
		$this->load->model('indicador_pga/avaliacao_fornecedores_model' );
		
		$args["ano"]        = $this->input->post("ano_referencia", true);
		$args["mes"]        = $this->input->post("mes_referencia", true);
		
		$row = $this->controladoria_variacao_orcamentaria_model->get_valores($args);
	
		if(isset($row['nr_valor_1']))
		{
			$row['nr_valor_1'] = number_format($row['nr_valor_1'], 2, ',', '.');
		}
		else
		{
			$row['nr_valor_1'] = number_format(0, 2, ',', '.');	
		}

		if(isset($row['nr_valor_2']))
		{
			$row['nr_valor_2'] = number_format($row['nr_valor_2'], 2, ',', '.');
		}
		else
		{
			$row['nr_valor_2'] = number_format(0, 2, ',', '.');	
		}
		
		echo json_encode($row);
	}
	
	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
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
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));
		
			$this->load->helper(array('indicador'));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula(
			 $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula(
			 $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula(
			 $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula(
			 $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula(
			 $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula(
			 $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
			
			$this->controladoria_variacao_orcamentaria_model->listar( $result, $args );
			$collection = $result->result_array();

			$indicador = array();
			$linha = 0;
			
			$contador_ano_atual = 0;
			$contador = sizeof($collection);
			$ultimo_mes_1 = 0;
			$ultimo_mes_2 = 0;
			$ultima_meta = 0;	
			$ultima_meta_2 = 0;					
			
			$a_data = array(0, 0);
			
			foreach( $collection as $item )
			{
				// histório de 5 anos atrás
				if(intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-10)
				//if(true)
				{
					$a_data = explode( "/", $item['mes_referencia'] );
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Total de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_valor_1   = $item['nr_valor_1'];
					$nr_valor_2   = $item['nr_valor_2'];
					$nr_resultado = $item['nr_resultado'];
					$nr_meta      = $item['nr_meta'];

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media'] != 'S' )
					{
						$contador_ano_atual++;
						$ultimo_mes_1 += $nr_valor_1;
						$ultimo_mes_2 += $nr_valor_2;
						$ultima_meta = $item['nr_meta'];
						$ultima_meta_2 = $item['nr_meta_2'];
					}

					$col=0;

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $nr_valor_1;
					$indicador[$linha][2] = $nr_valor_2;
					$indicador[$linha][3] = $nr_resultado;
					$indicador[$linha][4] = $item['nr_meta_2'];
					$indicador[$linha][5] = $nr_meta;
					$indicador[$linha][6] = $observacao;

					$linha++;
				}
			}
			
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			/*
			list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][6] = $tend[$i];
			}				
			*/
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Total do '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = app_decimal_para_php($ultimo_mes_1);
				$indicador[$linha][2] = app_decimal_para_php($ultimo_mes_2);
				$indicador[$linha][3] = app_decimal_para_php(($ultimo_mes_2 / $ultimo_mes_1) * 100);
				$indicador[$linha][4] = app_decimal_para_php($ultima_meta_2);
				$indicador[$linha][5] = app_decimal_para_php($ultima_meta);
				$indicador[$linha][6] = '';
			}

			$linha = 1;
			for( $i=0; $i<sizeof($indicador); $i++ )
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'justify');

				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media-barra;4,4,1,$linha_sem_media-linha;5,5,1,$linha_sem_media-linha",
				usuario_id(),
				$coluna_para_ocultar,
				1,
				2
			);

			$this->db->query($sql);
		}
	}

	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GC') OR indicador_db::verificar_permissao(usuario_id(),'GFC'))
		{
			$args   = array();
			$data   = array();
			$result = null;	
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
			
	        $this->controladoria_variacao_orcamentaria_model->listar( $result, $args );
			$collection = $result->result_array();

			$contador = sizeof($collection);
			$ar_media_ano_percentual_a = array();
			
			$contador_ano_atual = 0;
			
			$nr_valor_1 = 0;
			$nr_valor_2 = 0;	
			$ultima_meta = 0;
			$ultima_meta_2 = 0;
			
			foreach( $collection as $item )
			{
				$nr_meta  = $item['nr_meta'];		
			
				if($item['fl_media'] !='S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					$nr_valor_1 += $item['nr_valor_1'];
					$nr_valor_2 += $item['nr_valor_2'];
					$ultima_meta = $item['nr_meta'];
					$ultima_meta_2 = $item['nr_meta_2'];
				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				$args['cd_controladoria_variacao_orcamentaria'] = 0;
				$args["cd_indicador_tabela"]                  = $args['cd_indicador_tabela'];
				$args["dt_referencia"]                        = '01/01/'.intval($tabela[0]['nr_ano_referencia']);
				$args["fl_media"]                             = 'S';
				$args["nr_valor_1"]                           = $nr_valor_1;
				$args["nr_valor_2"]                           = $nr_valor_2;
				$args["nr_meta"]                              = $ultima_meta;
				$args["nr_meta_2"]                            = $ultima_meta_2;
				$args["observacao"]                           = "";
				$args["cd_usuario"]                           = $this->session->userdata('codigo');

				$this->controladoria_variacao_orcamentaria_model->salvar($result, $args);
			}

			$this->controladoria_variacao_orcamentaria_model->fechar_periodo($result, $args);

		}

		redirect("indicador_plugin/controladoria_variacao_orcamentaria", "refresh");
	}
}
?>