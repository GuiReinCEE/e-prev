<?php
class administrativo_escolari_atual extends Controller
{
    var	$label_0 = "Ano";
    var	$label_1 = "Mestrado";
    var	$label_2 = "Pós-Grad Comp";
    var	$label_3 = "Pós-Grad Incom";
    var	$label_4 = "Sup Completo";
    var	$label_5 = "Sup Incompleto";
    var	$label_6 = "2º Grau Comp";
    var	$label_7 = "2º Grau Incomp";
    var	$label_8 = "1º Grau Comp";
    var	$label_9 = "1º Grau Incomp";
    var	$label_10 = "Total";
    var $label_11 = "Observação";

    var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::RH_ESCOLARIDADE_ATUAL);
    }

    function index()
    {
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GGS' ) )
		{
			#### FECHA PERIODO ENCERRADO PARA ABRIR NOVO ####
			$ar_periodo = indicador_periodo_aberto();
			$ar_tabela  = indicador_tabela_aberta(intval($this->enum_indicador));
			if(intval($ar_periodo[0]["cd_indicador_periodo"]) != intval($ar_tabela[0]["cd_indicador_periodo"]))
			{
				$qr_sql = indicador_db::fechar_periodo_para_indicador(intval($ar_tabela[0]["cd_indicador_tabela"]), $this->session->userdata('codigo'));
				$this->db->query($qr_sql);
			}			
			
			
			// VERIFICA SE EXISTE TABELA NO PERÍODO ABERTO, SE NÃO EXISTIR, CRIAR TABELA NO PERÍODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( $this->enum_indicador );

	        $this->load->view('indicador_plugin/administrativo_escolari_atual/index.php',$data);
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

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GGS' ) )
        {
	        $this->load->model( 'indicador_plugin/administrativo_escolari_atual_model' );

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

				$this->administrativo_escolari_atual_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/administrativo_escolari_atual/partial_result', $data);
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

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GGS' ) )
		{

			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/administrativo_escolari_atual_model');
			$row=$this->administrativo_escolari_atual_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_meta,  cd_indicador_tabela
					FROM indicador_plugin.administrativo_escolari_atual
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

			$this->load->view('indicador_plugin/administrativo_escolari_atual/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GGS' ) )
		{
			$this->load->model('indicador_plugin/administrativo_escolari_atual_model');
			
			$args['cd_administrativo_escolari_atual']=intval($this->input->post('cd_administrativo_escolari_atual', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"] = app_decimal_para_db($this->input->post("nr_valor_2", true));
            $args["nr_valor_3"] = app_decimal_para_db($this->input->post("nr_valor_3", true));
            $args["nr_valor_4"] = app_decimal_para_db($this->input->post("nr_valor_4", true));
            $args["nr_valor_5"] = app_decimal_para_db($this->input->post("nr_valor_5", true));
            $args["nr_valor_6"] = app_decimal_para_db($this->input->post("nr_valor_6", true));
            $args["nr_valor_7"] = app_decimal_para_db($this->input->post("nr_valor_7", true));
            $args["nr_valor_8"] = app_decimal_para_db($this->input->post("nr_valor_8", true));
            $args["nr_valor_9"] = app_decimal_para_db($this->input->post("nr_valor_9", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->administrativo_escolari_atual_model->salvar( $args,$msg );
			
			if($retorno)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/administrativo_escolari_atual", "refresh" );
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
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GGS' ) )
		{
			$this->load->model('indicador_plugin/administrativo_escolari_atual_model');

			$this->administrativo_escolari_atual_model->excluir($id);

            if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_plugin/administrativo_escolari_atual', 'refresh' );
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

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GGS' ) )
		{
			$this->load->helper( array('indicador') );

			$tabela = indicador_tabela_aberta( $this->enum_indicador );

			if(sizeof($tabela)<=0)
			{#tabela_existe

				return false;
				#echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

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
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_10']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_11']), 'background,center');
				
				$this->load->model('indicador_plugin/administrativo_escolari_atual_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->administrativo_escolari_atual_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$media_ano=array();
				foreach( $collection as $item )
				{
					// histório de 5 anos atrás
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					{
						$nr_meta = $item["nr_meta"];

						if( $item['fl_media']=='S' )
						{
							$nr_valor_1 = '';
							$nr_valor_2 = '';
                            $nr_valor_3 = '';
                            $nr_valor_4 = '';
                            $nr_valor_5 = '';
                            $nr_valor_6 = '';
                            $nr_valor_7 = '';
                            $nr_valor_8 = '';
                            $nr_valor_9 = '';
                            $observacao = '';

							$nr_percentual_f = $item['nr_percentual_f'];
						}
						else
						{
							$referencia = $item['ano_referencia'];

							$nr_valor_1 = $item["nr_valor_1"];
							$nr_valor_2 = $item["nr_valor_2"];
                            $nr_valor_3 = $item["nr_valor_3"];
                            $nr_valor_4 = $item["nr_valor_4"];
                            $nr_valor_5 = $item["nr_valor_5"];
                            $nr_valor_6 = $item["nr_valor_6"];
                            $nr_valor_7 = $item["nr_valor_7"];
                            $nr_valor_8 = $item["nr_valor_8"];
                            $nr_valor_9 = $item["nr_valor_9"];
                            $observacao = $item["observacao"];

                            $soma = $nr_valor_1 + $nr_valor_2 + $nr_valor_3 + $nr_valor_4 + $nr_valor_5 + $nr_valor_6 + $nr_valor_7
                                + $nr_valor_8 + $nr_valor_9;
							
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_valor_1;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
                        $indicador[$linha][3] = app_decimal_para_php($nr_valor_3);
                        $indicador[$linha][4] = app_decimal_para_php($nr_valor_4);
                        $indicador[$linha][5] = app_decimal_para_php($nr_valor_5);
                        $indicador[$linha][6] = app_decimal_para_php($nr_valor_6);
                        $indicador[$linha][7] = app_decimal_para_php($nr_valor_7);
                        $indicador[$linha][8] = app_decimal_para_php($nr_valor_8);
                        $indicador[$linha][9] = app_decimal_para_php($nr_valor_9);
                        $indicador[$linha][10] = app_decimal_para_php($soma);
                        $indicador[$linha][11] = $observacao;

						$linha++;
					}
				}

				$linha_sem_media = $linha;

				if(sizeof($media_ano)>0)
				{
					$media = 0;
					foreach( $media_ano as $valor )
					{
						$media += $valor;
					}
/*
					$media = number_format(( $media / sizeof($media_ano) ),2 );

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

					$linha++;

					$indicador[$linha][0] = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = '';
					$indicador[$linha][4] = '';
					$indicador[$linha][5] = '';
					$indicador[$linha][6] = '';
                    $indicador[$linha][7] = '';
                    $indicador[$linha][8] = '';
                    $indicador[$linha][9] = '';
                    $indicador[$linha][10] = $soma;
 * */
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][10]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, utf8_encode($indicador[$i][11]), 'left');
                    $linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::PIZZA,
					'1,1,0,0;2,2,0,0;3,3,0,0;4,4,0,0;5,5,0,0;6,6,0,0;7,7,0,0;8,8,0,0;9,9,0,0',
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media;3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media;6,6,1,$linha_sem_media;7,7,1,$linha_sem_media;8,8,1,$linha_sem_media;9,9,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

				return true;
				/* "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresentação' );*/

			} #tabela_existe
		}
	}

	function fechar_periodo()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GGS' ) )
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/administrativo_escolari_atual_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->administrativo_escolari_atual_model->listar( $result, $args );
			$collection = $result->result_array();

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
					$nr_meta = $item["nr_meta"];
					$nr_referencial = $item["nr_referencial"];

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
						$nr_percentual_f = '';
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

                        $soma = $nr_valor_1 + $nr_valor_2 + $nr_valor_3 + $nr_valor_4 + $nr_valor_5 + $nr_valor_6 + $nr_valor_7
                            + $nr_valor_8 + $nr_valor_9;
					}

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $nr_percentual_f;
					}
				}

				$sql="";

				// gravar a média do período
				if(sizeof($media_ano)>0)
				{
					$media = 0;

					foreach( $media_ano as $valor )
					{
						$media += $valor;
					}

					$media = ( $media / sizeof($media_ano) );

					$sql.=sprintf(" INSERT INTO indicador_plugin.administrativo_escolari_atual
					( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_percentual_f,  fl_media, nr_referencial ) 
					VALUES ( '%s/01/01',current_timestamp,%s, %s, %s, 'S', %s ); "
					, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($soma),   floatval( app_decimal_para_db($nr_referencial) ));
				}

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/administrativo_escolari_atual' );
		// echo 'período encerrado com sucesso';
	}
}
?>