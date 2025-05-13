<?php
class equilibrio extends Controller
{
	var	$label_0 = "Mês";
	var	$label_1 = "Técnica (TEM)";
	var	$label_2 = "Matemática (PRECISA)";
	var	$label_3 = "Tec/Mat";
	var	$label_4 = "Meta";
    var	$label_5 = "Peso";
    var	$label_6 = "Acum tec";
    var	$label_7 = "Acum mat";
    var	$label_8 = "Acum tec / mat";
    var	$label_9 = "";
    var	$label_10 = "Peso obtido no mês";
    var	$label_11 = "Peso obtido 12 meses";
    var	$label_12 = "Média móvel";

    var $enum_indicador = 0;

    function __construct()
    {
        parent::Controller();
        $this->enum_indicador = intval(enum_indicador::EQUILIBRIO);
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

	        $this->load->view('igp/equilibrio/index.php', $data);
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

        CheckLogin();
        
		$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
		$data['tabela'] = $tabela;

		$this->load->model('igp/Equilibrio_model');

		$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

		$this->Equilibrio_model->listar( $result, $args );
		$data['collection'] = $result->result_array();

		$this->load->view('igp/equilibrio/partial_result', $data);
    }

	function detalhe($cd = 0)
	{
		$args   = array();
		$data   = array();
		$result = null;
		
        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_4'] = $this->label_4;
        $data['label_5'] = $this->label_5;

        CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC'))  or (gerencia_in(array('GC'))))
		{
			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

			if(intval($cd) == 0)
			{
				$qr_sql = "
							SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
								   dt_referencia, 
								   nr_meta, 
								   nr_peso 
							  FROM igp.equilibrio 
							 WHERE dt_exclusao IS NULL 
							 ORDER BY dt_referencia DESC 
							 LIMIT 1
						  ";
				$ob_res = $this->db->query($qr_sql);
				$ar_ant = $ob_res->row_array();

				$data['row']['cd_equilibrio'] = 0;
				$data['row']['dt_referencia'] = $ar_ant['mes_referencia'];
				$data['row']['nr_tecnica']    = "";
				$data['row']['nr_matematica'] = "";
				$data['row']['nr_meta']       = $ar_ant['nr_meta'];
				$data['row']['nr_peso']       = $ar_ant['nr_peso'];
			}
			else
			{
				$this->load->model('igp/Equilibrio_model');
				$data['row'] = $this->Equilibrio_model->carregar(intval($cd));
			}			

			$this->load->view('igp/equilibrio/detalhe', $data);			
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function salvar()
	{
		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')))
		{
			$args   = array();
			$data   = array();
			$result = null;			
			
			$this->load->model('igp/Equilibrio_model');
			
			$args['cd_equilibrio']       = intval($this->input->post('cd_equilibrio', TRUE));
			$args["cd_indicador_tabela"] = intval($this->input->post('cd_indicador_tabela', TRUE));
			$args["dt_referencia"]       = $this->input->post("dt_referencia",TRUE);
			$args["nr_tecnica"]          = app_decimal_para_db( $this->input->post("nr_tecnica",TRUE) );
			$args["nr_matematica"]       = app_decimal_para_db( $this->input->post("nr_matematica",TRUE) );
			$args["nr_meta"]             = app_decimal_para_db( $this->input->post("nr_meta",TRUE) );
			$args["nr_peso"]             = app_decimal_para_db( $this->input->post("nr_peso",TRUE) );
			$args["cd_usuario"]          = usuario_id();
			
			$msg=array();
			$retorno = $this->Equilibrio_model->salvar( $args,$msg );
			
			if($retorno)
			{
				$this->criar_indicador();
				redirect( "igp/equilibrio", "refresh" );			
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

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')))
		{
			$this->load->model('igp/Equilibrio_model');

			$this->Equilibrio_model->excluir( $id );

			redirect( 'igp/equilibrio', 'refresh' );
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
        $data['label_12'] = $this->label_12;

		CheckLogin();

		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')))
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );
			$INDICADOR_TABELA_CODIGO = $tabela[0]['cd_indicador_tabela'];

			$this->load->helper(array('igp'));

			$this->load->model('igp/equilibrio_model', 'dbmodel');

            $args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			igp_limpar_historico($this->enum_indicador);			
			
	        $this->dbmodel->listar($result, $args);
			$collection = $result->result_array();
			$indicador = array();
			foreach( $collection as $item )
			{
				$tecnica = floatval($item["nr_tecnica"]);
				$matematica = floatval($item["nr_matematica"]);
				$meta = floatval($item["nr_meta"]);
				$peso = floatval($item["nr_peso"]);


				$row['mes_ano'] =		$item['mes_referencia'];
				$row['VALOR_1'] =		number_format($item['nr_tecnica'], 2,',', '.');
				$row['VALOR_2'] =		number_format($item['nr_matematica'], 2,',', '.');
				$row['PERCENTUAL'] =	number_format($item['nr_tec_mat_percentual']/100, 4, ',', '.');
				$row['ACUMULADO'] =		number_format($item['nr_tec_mat_percentual_acumulado']/100, 4, ',', '.');
				$row['META'] =			number_format($item['nr_meta']/100, 4, ',', '.');
				$row['MEDIA_MOVEL'] =	number_format($item['nr_media_movel_percentual']/100,4,',','.');

				$indicador[] = $row;

				// coleta de dados para histórico dos anos anteriores
				if( strpos($item['mes_referencia'],"12/")>-1 )
				{
					$hst_meta = floatval($item["nr_meta"]);
					$hst_resultado = floatval($item["nr_tec_mat_percentual"]);	
					$mr = explode( '/', $item['mes_referencia'] );
					$desvio_meta = floatval($hst_resultado)-floatval($hst_meta);

					igp_gravar_historico( intval($mr[1]), floatval($hst_meta), floatval($hst_resultado), floatval($desvio_meta), $this->enum_indicador );
				}
			}

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = ".intval($INDICADOR_TABELA_CODIGO).";";
			
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4,0, utf8_encode('Acumulado'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 6,0, utf8_encode($data['label_12']), 'background,center');

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
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, $indicador[$i]['PERCENTUAL'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, $indicador[$i]['ACUMULADO'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, $indicador[$i]['META'], 'right');
				$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 6, $linha, $indicador[$i]['MEDIA_MOVEL'], 'right');

				$media += floatval(app_decimal_para_db($indicador[$i]['PERCENTUAL']));
				$contador_media++;
				
				if( strpos($indicador[$i]['mes_ano'],"/".date('Y'))>-1 )
				{
					$media_ano += floatval(app_decimal_para_db($indicador[$i]['PERCENTUAL']));
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
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, number_format($media,4,',','.'), 'right');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');
			$linha++;
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 0, $linha, 'Media '.date('Y'), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 1, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 2, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 3, $linha, number_format($media_ano,4,',','.'), 'right');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 4, $linha, '', '');
			$sql.=indicador_db::sql_inserir_celula( $INDICADOR_TABELA_CODIGO, 5, $linha, '', '');

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				"3,3,0,0;6,6,0,0;5,5,0,0",
				"0,0,1,12",
				"3,3,1,12-barra;6,6,1,12-linha;5,5,1,12-linha",
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
		CheckLogin();
		
		if(($this->session->userdata('indic_12') == "*") or (indicador_db::verificar_permissao(usuario_id(),'GC')))
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
			redirect('igp/equilibrio');
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }		
	}	
}
?>