<?php
class Rentabilidade_ci extends Controller
{
    var	$label_0 = "Meses";
	var	$label_1 = "A Rentab CI";
	var	$label_2 = "B Benchmark CI";
	var	$label_3 = "A-B";
	var	$label_4 = "Fator Rentab CI";
    var	$label_5 = "Fator Benchmark";
    var	$label_6 = "Nr Índice Rentab CI";
    var	$label_7 = "Nr Índice Benchmark";
    var	$label_8 = "Var Acu 12 meses Rentab CI";
    var	$label_9 = "Var Acu 12 meses Benchmark";
    var	$label_10 = "Mín";
    var	$label_11 = "Máx";
    var	$label_12 = "PODER";
    var	$label_13 = "Peso IGP";
    var	$label_14 = "IGP Mês";
    var	$label_15 = "IGP Acum 12";
    var	$label_16 = "IGP Média 12";
    var	$label_17 = "J-K";
    var	$label_18 = "Peso";
    var	$label_19 = "Peso Acum";
    var	$label_20 = "Peso Média";

	var $enum_indicador = 0;
	
    function __construct()
    {
        parent::Controller();
        $this->enum_indicador = intval(enum_indicador::RENTABILIDADE_CI);
		$this->load->helper( array('indicador') );
    }

    function index()
    {
		if(CheckLogin())
		{
			// VERIFICA SE EXISTE TABELA NO PERÍODO ABERTO, SE NÃO EXISTIR, CRIAR TABELA NO PERÍODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

			$this->load->view('igp/rentabilidade_ci/index.php', $data);
		}
    }

    function listar()
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
        $data['label_20'] = $this->label_20;
		
		CheckLogin();
		
        $tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
		$data['tabela'] = $tabela;

	    $this->load->model('igp/Rentabilidade_ci_model');
	
		$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	
	    $this->Rentabilidade_ci_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view('igp/rentabilidade_ci/partial_result', $data);		
    }

	function detalhe($cd = 0)
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
        $data['label_20'] = $this->label_20;

		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GIN')) or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC'))  or (gerencia_in(array('GC'))))
		{
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd) == 0)
			{
				$qr_sql = "
							SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
								   dt_referencia, 
								   nr_peso_igp
							  FROM igp.rentabilidade_ci 
							 WHERE dt_exclusao IS NULL 
							 ORDER BY dt_referencia DESC 
							 LIMIT 1
						  ";
				$ob_res = $this->db->query($qr_sql);
				$ar_ant = $ob_res->row_array();

				$data['row']['cd_rentabilidade_ci'] = 0;
				$data['row']['dt_referencia']       = $ar_ant['mes_referencia'];
				$data['row']['nr_rentabilidade']    = "";
				$data['row']['nr_benchmark']        = "";
				$data['row']['nr_peso_igp']         = $ar_ant['nr_peso_igp'];
			}
			else
			{
				$this->load->model('igp/Rentabilidade_ci_model');
				$data['row'] = $this->Rentabilidade_ci_model->carregar(intval($cd));
			}			

			$this->load->view('igp/rentabilidade_ci/detalhe', $data);			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function salvar()
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GIN')) or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$args   = array();
			$data   = array();
			$result = null;				

			$this->load->model('igp/Rentabilidade_ci_model');

			$args['cd_rentabilidade_ci'] = intval($this->input->post('cd_rentabilidade_ci', TRUE));
			$args['cd_indicador_tabela'] = intval($this->input->post('cd_indicador_tabela', TRUE));
			$args["dt_referencia"]       = $this->input->post("dt_referencia",TRUE);
			$args["nr_rentabilidade"]    = app_decimal_para_db($this->input->post("nr_rentabilidade",TRUE));
			$args["nr_benchmark"]        = app_decimal_para_db($this->input->post("nr_benchmark",TRUE));
			$args["nr_peso_igp"]         = app_decimal_para_db($this->input->post("nr_peso_igp",TRUE));
			$args["cd_usuario"]          = usuario_id();

			$msg=array();
			$retorno = $this->Rentabilidade_ci_model->salvar($args, $msg);

			if($retorno)
			{
				$this->criar_indicador();
				redirect( "igp/rentabilidade_ci", "refresh" );			
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function excluir($id)
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GIN')) or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$this->load->model('igp/Rentabilidade_ci_model');

			$this->Rentabilidade_ci_model->excluir( $id );

			redirect( 'igp/rentabilidade_ci', 'refresh' );
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
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
        $data['label_20'] = $this->label_20;

		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GIN')) or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$INDICADOR_TABELA_CODIGO = $tabela[0]['cd_indicador_tabela'];
			
			$this->load->helper(array('igp'));
	
			$this->load->model('igp/Rentabilidade_ci_model', 'dbmodel');

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			igp_limpar_historico($this->enum_indicador);			
	        
			$this->dbmodel->listar($result, $args);
			$collection = $result->result_array();
			$indicador = array();
			foreach($collection as $item)
			{
				$row = array();
				$row['mes_ano'] = $item['mes_referencia'];
				$row['VALOR_1'] = number_format($item['nr_rentabilidade'],4,',','');
				$row['VALOR_2'] = number_format($item['nr_igp_acumulado'],4,',','');
				$row['VALOR_3'] = number_format($item['nr_benchmark_variacao'],4,',','');
				$row['VALOR_4'] = number_format($item['nr_igp_media'],4,',','');
				$row['VALOR_5'] = number_format($item['nr_benchmark'],4,',','');

				$indicador[] = $row;

				// coleta de dados para histórico dos anos anteriores
				if( strpos($item['mes_referencia'],"12/")>-1 )
				{
					/* $hst_meta = floatval($meta);
					$hst_resultado = floatval($tecnica_matematica);
					$mr = explode( '/', $item['mes_referencia'] );
					$desvio_meta = floatval($hst_resultado)-floatval($hst_meta);

					igp_gravar_historico( intval($mr[1]), floatval($hst_meta), floatval($hst_resultado), floatval($desvio_meta), $this->enum_indicador ); */
				}
			}

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($INDICADOR_TABELA_CODIGO).";";

			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1,0, utf8_encode('Rentab no mês'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2,0, utf8_encode('Rentab acum 12 meses'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3,0, utf8_encode('Meta Acum 12 meses'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4,0, utf8_encode('Média Móvel 12 meses'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5,0, utf8_encode('Meta 12 meses'), 'background,center');

			$linha              = 1;
			$contador_media     = 0;
			$contador_media_ano = 0;
			$media              = 0;
			$media_ano          = 0;
			$nr_ini = (count($indicador) - 12); #quantidade de meses para acumular
			$nr_fim = count($indicador);
			for($i = $nr_ini; $i < $nr_fim; $i++)
			{
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, $indicador[$i]['mes_ano'], 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, $indicador[$i]['VALOR_1'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, $indicador[$i]['VALOR_2'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, $indicador[$i]['VALOR_3'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, $indicador[$i]['VALOR_4'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, $indicador[$i]['VALOR_5'], 'right');

				$media += floatval(app_decimal_para_db($indicador[$i]['VALOR_1']));
				$contador_media++;
				
				if( strpos($indicador[$i]['mes_ano'],"/".date('Y'))>-1 )
				{
					$media_ano += floatval(app_decimal_para_db($indicador[$i]['VALOR_1']));
					$contador_media_ano++;
				}

				$linha++;
			}

            if($contador_media > 0)
            {
                $media = floatval($media) / $contador_media;
            }
			
			$media_ano = floatval($media_ano) / ($contador_media_ano == 0 ? 1 : $contador_media_ano);

			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, '', 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');
			$linha++;
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, 'Media', 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, number_format($media,2,',','.'), 'right');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');
			$linha++;
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, 'Media '.date('Y'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, number_format($media_ano,2,',','.'), 'right');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				"1,1,0,0;2,2,0,0;3,3,0,0;4,4,0,0",
				"0,0,1,12",
				"1,1,1,12-barra;2,2,1,12-barra;3,3,1,12-linha;4,4,1,12-linha",
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
		CheckLogin();
		
		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GIN')) or (indicador_db::verificar_permissao(usuario_id(),'GC')) OR (indicador_db::verificar_permissao(usuario_id(),'GFC')))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			if(count($tabela) == 0)
			{
				echo "Não foi identificado período aberto para o Indicador";
			}
			else
			{
				// indicar que o período foi fechado para o indicador_tabela
				$qr_sql = " 
							UPDATE indicador.indicador_tabela 
							   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
							       cd_usuario_fechamento_periodo = ".intval(usuario_id())."
							 WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; 
						  ";
				$this->db->query($qr_sql);
			}
			redirect('igp/rentabilidade_ci');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}		
}
?>