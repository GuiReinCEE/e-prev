<?php
class administrativo_total_digitalizado extends Controller
{
    var	$label_0 = "Mês";
	var	$label_1 = 'SG';
	var	$label_2 = "GRI";
	var	$label_3 = 'GJ';
    var $label_4 = 'GIN';
    var $label_5 = 'GI';
    var $label_6 = 'GF';
    var $label_7 = 'GC';
    var $label_8 = 'GB';
    var $label_9 = 'GAP';
    var $label_10 = 'GAD';
    var $label_11 = 'GA';
    var $label_12 = 'DE';
    var $label_13 = 'OUTROS';
    var $label_14 = "Observação";

	var $enum_indicador = 0;

    var $fl_permissao = false;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::RH_TOTAL_DIGITALIZADO);

        $this->load->helper( array('indicador') );
        CheckLogin();

        if(gerencia_in(array('GGS' )))
        {
            $this->fl_permissao = true;
        }
        else
        {
            $this->fl_permissao = false;
        }
    }

    function index()
    {
        if($this->fl_permissao)
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

	        $this->load->view('indicador_plugin/administrativo_total_digitalizado/index.php', $data);
		}
    }

    function listar()
    {
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

		if($this->fl_permissao)
		{
	        $this->load->model( 'indicador_plugin/administrativo_total_digitalizado_model');

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args = array();
			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->administrativo_total_digitalizado_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/administrativo_total_digitalizado/partial_result', $data);
			}
			else
			{
				echo "Nenhum período aberto para o indicador.";
			}
        }
    }

	function detalhe($cd=0)
	{
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

		if($this->fl_permissao)
		{
			$data['CD_INDICADOR'] = $this->enum_indicador;
            $tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/administrativo_total_digitalizado_model');
			$row=$this->administrativo_total_digitalizado_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
                        SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia,
                               dt_referencia,
                               nr_meta,
                               cd_indicador_tabela
					      FROM indicador_plugin.administrativo_total_digitalizado
					     WHERE dt_exclusao IS NULL
					     ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
						$row['dt_referencia'] = $row_atual['mes_referencia'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
					}
				}

				$data['row'] = $row;
			}

			$this->load->view('indicador_plugin/administrativo_total_digitalizado/detalhe', $data);
		}
	}

	function salvar()
	{
        if($this->fl_permissao)
		{
			$this->load->model('indicador_plugin/administrativo_total_digitalizado_model');

			$args['cd_administrativo_total_digitalizado']=intval($this->input->post('cd_administrativo_total_digitalizado', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db( $this->input->post("nr_valor_1", true) );
			$args["nr_valor_2"] = app_decimal_para_db( $this->input->post("nr_valor_2", true) );
            $args["nr_valor_3"] = app_decimal_para_db( $this->input->post("nr_valor_3", true) );
            $args["nr_valor_4"] = app_decimal_para_db( $this->input->post("nr_valor_4", true) );
            $args["nr_valor_5"] = app_decimal_para_db( $this->input->post("nr_valor_5", true) );
            $args["nr_valor_6"] = app_decimal_para_db( $this->input->post("nr_valor_6", true) );
            $args["nr_valor_7"] = app_decimal_para_db( $this->input->post("nr_valor_7", true) );
            $args["nr_valor_8"] = app_decimal_para_db( $this->input->post("nr_valor_8", true) );
            $args["nr_valor_9"] = app_decimal_para_db( $this->input->post("nr_valor_9", true) );
            $args["nr_valor_10"] = app_decimal_para_db( $this->input->post("nr_valor_10", true) );
            $args["nr_valor_11"] = app_decimal_para_db( $this->input->post("nr_valor_11", true) );
            $args["nr_valor_12"] = app_decimal_para_db( $this->input->post("nr_valor_12", true) );
            $args["nr_valor_13"] = app_decimal_para_db( $this->input->post("nr_valor_13", true) );
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->administrativo_total_digitalizado_model->salvar( $args, $msg );

			if($retorno)
			{
                if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/administrativo_total_digitalizado", "refresh" );
				}
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
	}

	function excluir($id)
	{
        if($this->fl_permissao)
		{
			$this->load->model('indicador_plugin/administrativo_total_digitalizado_model');

			$this->administrativo_total_digitalizado_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/administrativo_total_digitalizado", "refresh" );
			}
		}
	}

	function criar_indicador()
	{
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

		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			if(sizeof($tabela)<=0)
			{#tabela_existe
                return false;
				#echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";




				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_4']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_5']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_6']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_7']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_8']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_9']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_10']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_11']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_12']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 13,0, utf8_encode($data['label_13']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 14,0, utf8_encode($data['label_14']), 'background,center');

				$this->load->model('indicador_plugin/administrativo_total_digitalizado_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->administrativo_total_digitalizado_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;
  
                $linha_total = 1;
				$contador = sizeof($collection);
				$media_ano=array();

                $sm_valor_1 = 0;
                $sm_valor_2 = 0;
                $sm_valor_3 = 0;
                $sm_valor_4 = 0;
                $sm_valor_5 = 0;
                $sm_valor_6 = 0;
                $sm_valor_7 = 0;
                $sm_valor_8 = 0;
                $sm_valor_9 = 0;
                $sm_valor_10 = 0;
                $sm_valor_11 = 0;
                $sm_valor_12 = 0;
                $sm_valor_13 = 0;
				foreach( $collection as $item )
				{
					// histório de 5 anos atrás
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					{

                        $nr_valor_1 = $item["nr_valor_1"];
                        $nr_valor_2 = $item["nr_valor_2"];
                        $nr_valor_3 = $item["nr_valor_3"];
                        $nr_valor_4 = $item["nr_valor_4"];
                        $nr_valor_5 = $item["nr_valor_5"];
                        $nr_valor_6 = $item["nr_valor_6"];
                        $nr_valor_7 = $item["nr_valor_7"];
                        $nr_valor_8 = $item["nr_valor_8"];
                        $nr_valor_9 = $item["nr_valor_9"];
                        $nr_valor_10 = $item["nr_valor_10"];
                        $nr_valor_11 = $item["nr_valor_11"];
                        $nr_valor_12 = $item["nr_valor_12"];
                        $nr_valor_13 = $item["nr_valor_13"];
                        $observacao = $item["observacao"];
                       
						if( $item['fl_media']=='S' )
						{
							$referencia = $item['ano_referencia'];

						}
						else
						{
							$referencia = $item['mes_referencia'];
                           
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_valor_1;
						}

						$indicador[$linha][0] = $item['mes_referencia'];
						$indicador[$linha][12] = app_decimal_para_php($nr_valor_12);
						$indicador[$linha][11] = app_decimal_para_php($nr_valor_11);
                        $indicador[$linha][10] = app_decimal_para_php($nr_valor_10);
                        $indicador[$linha][9] = app_decimal_para_php($nr_valor_9);
                        $indicador[$linha][8] = app_decimal_para_php($nr_valor_8);
                        $indicador[$linha][7] = app_decimal_para_php($nr_valor_7);
                        $indicador[$linha][6] = app_decimal_para_php($nr_valor_6);
                        $indicador[$linha][5] = app_decimal_para_php($nr_valor_5);
                        $indicador[$linha][4] = app_decimal_para_php($nr_valor_4);
                        $indicador[$linha][3] = app_decimal_para_php($nr_valor_3);
                        $indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
                        $indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
                        $indicador[$linha][13] = app_decimal_para_php($nr_valor_13);
                        $indicador[$linha][14] = $observacao;

						$linha++;

                        $sm_valor_1 += $nr_valor_1;
                        $sm_valor_2 += $nr_valor_2;
                        $sm_valor_3 += $nr_valor_3;
                        $sm_valor_4 += $nr_valor_4;
                        $sm_valor_5 += $nr_valor_5;
                        $sm_valor_6 += $nr_valor_6;
                        $sm_valor_7 += $nr_valor_7;
                        $sm_valor_8 += $nr_valor_8;
                        $sm_valor_9 += $nr_valor_9;
                        $sm_valor_10 += $nr_valor_10;
                        $sm_valor_11 += $nr_valor_11;
                        $sm_valor_12 += $nr_valor_12;
                        $sm_valor_13 += $nr_valor_13;

					}
				}

				// LINHA DE TENDÊNCIA - CURVA LOGARITMICA

				$linha_sem_media = $linha;

				if(sizeof($media_ano)>0)
				{
                    $indicador[$linha][0] = '';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = '';
					$indicador[$linha][4] = '';
                    $indicador[$linha][5] = '';
                    $indicador[$linha][6] = '';
                    $indicador[$linha][7] = '';
                    $indicador[$linha][8] = '';
                    $indicador[$linha][9] = '';
                    $indicador[$linha][10] = '';
                    $indicador[$linha][11] = '';
                    $indicador[$linha][12] = '';
                    $indicador[$linha][13] = '';
                    $indicador[$linha][14] = '';

                    $linha++;

					$indicador[$linha][0] = $item['ano_referencia'];
					$indicador[$linha][12] = $sm_valor_12;
					$indicador[$linha][11] = $sm_valor_11;
					$indicador[$linha][10] = $sm_valor_10;
					$indicador[$linha][9] = $sm_valor_9;
                    $indicador[$linha][8] = $sm_valor_8;
                    $indicador[$linha][7] = $sm_valor_7;
                    $indicador[$linha][6] = $sm_valor_6;
                    $indicador[$linha][5] = $sm_valor_5;
                    $indicador[$linha][4] = $sm_valor_4;
                    $indicador[$linha][3] = $sm_valor_3;
                    $indicador[$linha][2] = $sm_valor_2;
                    $indicador[$linha][1] = $sm_valor_1;
                    $indicador[$linha][13] = $sm_valor_13;
                    $indicador[$linha][14] = '';

                    $linha++;

                    $linha_total = $linha;

				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][3]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][4]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][5]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][6]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][7]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][8]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][9]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][10]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][11]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][12]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 13, $linha, app_decimal_para_php($indicador[$i][13]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 14, $linha, utf8_encode($indicador[$i][14]), 'left');
					$linha++;
				}

                                                                                /*
 $label_12, $label_11, $label_10, $label_9, $label_8, $label_7, $label_6,
         $label_5, $label_4, $label_3, $label_2, $label_1, $label_13, $label_14,''
 */

				// gerar gráfico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::PIZZA,
					'12,12,0,0;11,11,0,0;10,10,0,0;9,9,0,0;8,8,0,0;7,7,0,0;6,6,0,0;5,5,0,0;4,4,0,0;3,3,0,0;2,2,0,0;1,1,0,0;13,13,0,0',
					"0,0,$linha_total,$linha_total",
					"12,12,$linha_total,$linha_total;11,11,$linha_total,$linha_total;10,10,$linha_total,$linha_total;9,9,$linha_total,$linha_total;
                     8,8,$linha_total,$linha_total;7,7,$linha_total,$linha_total;6,6,$linha_total,$linha_total;5,5,$linha_total,$linha_total;
                     4,4,$linha_total,$linha_total;3,3,$linha_total,$linha_total;2,2,$linha_total,$linha_total;1,1,$linha_total,$linha_total;13,13,$linha_total,$linha_total",
					usuario_id(),
					$coluna_para_ocultar
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}
                return true;
				#echo "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresentação' );

			} #tabela_existe
		}
	}

	function fechar_periodo()
	{
        if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));

			$this->load->model('indicador_plugin/administrativo_total_digitalizado_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->administrativo_total_digitalizado_model->listar( $result, $args );
			$collection = $result->result_array();
            $sm_valor_1 = 0;
            $sm_valor_2 = 0;
            $sm_valor_3 = 0;
            $sm_valor_4 = 0;
            $sm_valor_5 = 0;
            $sm_valor_6 = 0;
            $sm_valor_7 = 0;
            $sm_valor_8 = 0;
            $sm_valor_9 = 0;
            $sm_valor_10 = 0;
            $sm_valor_11 = 0;
            $sm_valor_12 = 0;
            $sm_valor_13 = 0;

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$contador = sizeof($collection);
				$media_ano=array();

				foreach( $collection as $item )
				{

					if( $item['fl_media']=='S' )
					{
						$referencia = " Média de " . $item['ano_referencia'];

						$nr_valor_1 = '';
                        $nr_valor_2 = '';
                        $nr_valor_3 = '';
                        $nr_valor_4 = '';
                        $nr_valor_5 = '';
                        $nr_valor_6 = '';
                        $nr_valor_7 = '';
                        $nr_valor_8 = '';
                        $nr_valor_9 = '';
                        $nr_valor_10 = '';
                        $nr_valor_11 = '';
                        $nr_valor_12 = '';
                        $nr_valor_13 = '';
					}
					else
					{
						$referencia = $item['mes_referencia'];

						$nr_valor_1 = $item["nr_valor_1"];
                        $nr_valor_2 = $item["nr_valor_2"];
                        $nr_valor_3 = $item["nr_valor_3"];
                        $nr_valor_4 = $item["nr_valor_4"];
                        $nr_valor_5 = $item["nr_valor_5"];
                        $nr_valor_6 = $item["nr_valor_6"];
                        $nr_valor_7 = $item["nr_valor_7"];
                        $nr_valor_8 = $item["nr_valor_8"];
                        $nr_valor_9 = $item["nr_valor_9"];
                        $nr_valor_10 = $item["nr_valor_10"];
                        $nr_valor_11 = $item["nr_valor_11"];
                        $nr_valor_12 = $item["nr_valor_12"];
                        $nr_valor_13 = $item["nr_valor_13"];

					}

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $nr_valor_1;
					}

                    $sm_valor_1 += $nr_valor_1;
                    $sm_valor_2 += $nr_valor_2;
                    $sm_valor_3 += $nr_valor_3;
                    $sm_valor_4 += $nr_valor_4;
                    $sm_valor_5 += $nr_valor_5;
                    $sm_valor_6 += $nr_valor_6;
                    $sm_valor_7 += $nr_valor_7;
                    $sm_valor_8 += $nr_valor_8;
                    $sm_valor_9 += $nr_valor_9;
                    $sm_valor_10 += $nr_valor_10;
                    $sm_valor_11 += $nr_valor_11;
                    $sm_valor_12 += $nr_valor_12;
                    $sm_valor_13 += $nr_valor_13;
				}

				$sql="";

				// gravar a média do período
				if(sizeof($media_ano)>0)
				{
					$sql.=sprintf(" INSERT INTO indicador_plugin.administrativo_total_digitalizado
                                              (
                                                dt_referencia,
                                                dt_inclusao,
                                                cd_usuario_inclusao,
                                                nr_valor_1,
                                                nr_valor_2,
                                                nr_valor_3,
                                                nr_valor_4,
                                                nr_valor_5,
                                                nr_valor_6,
                                                nr_valor_7,
                                                nr_valor_8,
                                                nr_valor_9,
                                                nr_valor_10,
                                                nr_valor_11,
                                                nr_valor_12,
                                                nr_valor_13,
                                                fl_media
                                              )
					                     VALUES
                                              (
                                               '%s/01/01',
                                               CURRENT_TIMESTAMP,
                                               %s,
                                               %s,
                                               %s,
                                               %s,
                                               %s, 
                                               %s, 
                                               %s,
                                               %s, 
                                               %s, 
                                               %s,
                                               %s, 
                                               %s, 
                                               %s,
                                               %s,
                                               'S' ); ",
                            intval($tabela[0]['nr_ano_referencia']) ,
                            usuario_id(),
                            floatval($sm_valor_1),
                            floatval($sm_valor_2),
                            floatval($sm_valor_3),
                            floatval($sm_valor_4),
                            floatval($sm_valor_5),
                            floatval($sm_valor_6),
                            floatval($sm_valor_7),
                            floatval($sm_valor_8),
                            floatval($sm_valor_9),
                            floatval($sm_valor_10),
                            floatval($sm_valor_11),
                            floatval($sm_valor_12),
                            floatval($sm_valor_13)

                            );
				}

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}
		redirect( 'indicador_plugin/administrativo_total_digitalizado' );
		// echo 'período encerrado com sucesso';

	}
}
?>