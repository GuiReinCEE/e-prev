<?php
class atend_habilit_incorr extends Controller
{
	var	$label_0 = "Mês";
	var	$label_1 = "Número de Benefícios Habilitados";
	var	$label_2 = "Benefícios Habilitados Incorretamente";
	var	$label_3 = "% Habilitações Incorretas";
    var $label_4 = "Meta";
    var $label_5 = "Tendência";
    var $label_6 = "Observação";

	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_HABILITACOES_INCORRETAS);
    }
    function index()
    {
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
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

	        $this->load->view('indicador_plugin/atend_habilit_incorr/index.php',$data);
		}
    }

    function listar()
    {
        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
        $data['label_4'] = $this->label_4;
        $data['label_6'] = $this->label_6;

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
        {
	        $this->load->model( 'indicador_plugin/atend_habilit_incorr_model' );

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

				$this->atend_habilit_incorr_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/atend_habilit_incorr/partial_result', $data);
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
        $data['label_6'] = $this->label_6;

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
		{

			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/atend_habilit_incorr_model');
			$row=$this->atend_habilit_incorr_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_meta, cd_indicador_tabela,
					(SELECT COUNT(*)
				      FROM indicador_plugin.atend_habilit_incorr
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($data['tabela'][0]['nr_ano_referencia'])."
				       AND dt_exclusao IS NULL) AS qt_ano
					FROM indicador_plugin.atend_habilit_incorr 
					WHERE dt_exclusao IS NULL 
					ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
						$row['dt_referencia'] = $row_atual['mes_referencia'];
						$row['nr_meta'] = $row_atual['nr_meta'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
						$row['qt_ano'] = $row_atual['qt_ano'];
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_plugin/atend_habilit_incorr/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
		{
			$this->load->model('indicador_plugin/atend_habilit_incorr_model');
			
			$args['cd_atend_habilit_incorr']=intval($this->input->post('cd_atend_habilit_incorr', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"] = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->atend_habilit_incorr_model->salvar( $args,$msg );
			
			if($retorno)
			{
                if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/atend_habilit_incorr", "refresh" );
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
		{
			$this->load->model('indicador_plugin/atend_habilit_incorr_model');

			$this->atend_habilit_incorr_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/atend_habilit_incorr", "refresh" );
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

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
		{
			$this->load->helper( array('indicador') );

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
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

				$this->load->model('indicador_plugin/atend_habilit_incorr_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->atend_habilit_incorr_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$media_ano=array();
				$nr_valor_1_total = 0;
				$nr_valor_2_total = 0;
				foreach( $collection as $item )
				{
					// histório de 5 anos atrás
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					{
						$nr_meta = $item["nr_meta"];
                        $observacao = $item["observacao"];

						if( $item['fl_media']=='S' )
						{
							$referencia = " Resultado de " . $item['ano_referencia'];

							$nr_valor_1 = '';
							$nr_valor_2 = '';
							$nr_percentual_f = $item['nr_percentual_f'];
						}
						else
						{
							$referencia = $item['mes_referencia'];

							$nr_valor_1 = $item["nr_valor_1"];
							$nr_valor_2 = $item["nr_valor_2"];

							$nr_valor_1_total += $item["nr_valor_1"];
							$nr_valor_2_total += $item["nr_valor_2"];
							
							if( floatval($nr_valor_1)>0 )
							{
								$nr_percentual_f = ( floatval($nr_valor_2)/floatval($nr_valor_1) )*100;
							}
							else
							{
								$nr_percentual_f = '0';
							}
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_percentual_f;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
						$ar_tendencia[] = $nr_percentual_f;
						$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
						$indicador[$linha][4] = app_decimal_para_php($nr_meta);
                        $indicador[$linha][6] = $observacao;

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

				if(sizeof($media_ano)>0)
				{
					$media = 0;
					foreach( $media_ano as $valor )
					{
						$media += $valor;
					}

					$media = number_format(( $media / sizeof($media_ano) ),2 );

					$indicador[$linha][0] = '';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = '';
					$indicador[$linha][4] = '';
					$indicador[$linha][5] = '';
                    $indicador[$linha][6] = '';

					$linha++;

					$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
					$indicador[$linha][1] = $nr_valor_1_total;
					$indicador[$linha][2] = $nr_valor_2_total;
					$indicador[$linha][3] = $media;
					$indicador[$linha][4] = '';
					$indicador[$linha][5] = '';
                    $indicador[$linha][6] = '';
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'left');

					$linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='5';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'3,3,0,0;4,4,0,0;5,5,0,0',
					"0,0,1,$linha_sem_media",
					"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar,
                    1,
                    2
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
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/atend_habilit_incorr_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->atend_habilit_incorr_model->listar( $result, $args );
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

					if( $item['fl_media']=='S' )
					{
						$referencia = " Média de " . $item['ano_referencia'];

						$nr_valor_1 = '';
						$nr_valor_2 = '';
						$nr_percentual_f = '';
					}
					else
					{
						$referencia = $item['mes_referencia'];

						$nr_valor_1 = $item["nr_valor_1"];
						$nr_valor_2 = $item["nr_valor_2"];
						
						if( floatval($nr_valor_1)>0 )
						{
							$nr_percentual_f = ( floatval($nr_valor_2)/floatval($nr_valor_1) )*100;
						}
						else
						{
							$nr_percentual_f = '0';
						}
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

					$sql.=sprintf(" INSERT INTO indicador_plugin.atend_habilit_incorr
					( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_percentual_f, nr_meta, fl_media ) 
					VALUES ( '%s/01/01',current_timestamp,%s, %s, %s, 'S' ); "
					, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($media), floatval( $nr_meta ));
				}

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/atend_habilit_incorr' );
		// echo 'período encerrado com sucesso';
	}
}
?>