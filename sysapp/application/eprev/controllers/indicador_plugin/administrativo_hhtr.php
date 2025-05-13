<?php
class administrativo_hhtr extends Controller
{
    var	$label_0 = "Mês";
	var	$label_1 = "Total de Horas de Treinamento";
	var	$label_2 = "Efetivo (Colaboradores)";
	var	$label_3 = "Hora/Homem de Treinamento (acumulado)";
    var	$label_4 = "Meta";
    var	$label_5 = "Referencial";
    var	$label_6 = "Tendência";
    var $label_7 = "Observação";

	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::RH_HORA_HOMEM_TREINAMENTO);
    }

    function index()
    {
        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
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

	        $this->load->view('indicador_plugin/administrativo_hhtr/index.php');
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
        $data['label_7'] = $this->label_7;

		$this->load->helper( array('indicador') );

		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
        {
			$this->load->helper( array('indicador') );
	        $this->load->model( 'indicador_plugin/Administrativo_hhtr_model' );

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args = array();
			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->Administrativo_hhtr_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/administrativo_hhtr/partial_result', $data);
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
        $data['label_7'] = $this->label_7;

		$this->load->helper( array('indicador') );

		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->helper( array('indicador') );

			$data['CD_INDICADOR'] = $this->enum_indicador;

            $tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/Administrativo_hhtr_model');
			$row=$this->Administrativo_hhtr_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_referencial, nr_meta, cd_indicador_tabela
					FROM indicador_plugin.administrativo_hhtr 
					WHERE dt_exclusao IS NULL 
					ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
						$row['dt_referencia'] = $row_atual['mes_referencia'];
						$row['nr_referencial'] = $row_atual['nr_referencial'];
						$row['nr_meta'] = $row_atual['nr_meta'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
						$row['nr_total_hora'] = 0;
						$row['nr_efetivo'] = 0;
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_plugin/administrativo_hhtr/detalhe', $data);
		}
	}

	function salvar()
	{
        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->model('indicador_plugin/Administrativo_hhtr_model');
			
			$args['cd_administrativo_hhtr']=intval($this->input->post('cd_administrativo_hhtr', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_total_hora"] = app_decimal_para_db($this->input->post("nr_total_hora", true));
			$args["nr_efetivo"] = app_decimal_para_db($this->input->post("nr_efetivo", true));
			$args["nr_referencial"] = app_decimal_para_db($this->input->post("nr_referencial", true));
			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->Administrativo_hhtr_model->salvar( $args,$msg );
			
			if($retorno)
			{
                if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/administrativo_hhtr", "refresh" );
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->model('indicador_plugin/Administrativo_hhtr_model');

			$this->Administrativo_hhtr_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/administrativo_hhtr", "refresh" );
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

		$this->load->helper( array('indicador') );

		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
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
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');

				$this->load->model('indicador_plugin/Administrativo_hhtr_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->Administrativo_hhtr_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;
				$linha_sem_media = 0;
				
				$contador_ano_atual = 0;
				$contador = sizeof($collection);
				$media_ano=array();
				$a_data=array(0, 0);
				$nr_acumulado_anterior = 0;
				$nr_total_hora_total = 0;
				$nr_efetivo_total = 0;
				$nr_meta_total = 0;
				$nr_referencial_total = 0;
				
				foreach( $collection as $item )
				{
					// exibir apenas 5 anos de histórico
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					{
						$nr_meta = $item["nr_meta"];
						$nr_referencial = $item["nr_referencial"];

						if( $item['fl_media']=='S' )
						{
							$referencia = " Acum. de " . $item['ano_referencia'];

							$nr_total_hora = '';
							$nr_efetivo = '';
                            $observacao = '';
							$nr_acumulado_f = $item['nr_acumulado_f']; // valor da média dos anos anteriores é gravada nessa coluna quando o período é fechado
						}
						else
						{
							$referencia = $item['mes_referencia'];

							$nr_total_hora = $item["nr_total_hora"];
							$nr_efetivo = $item["nr_efetivo"];
                            $observacao = $item["observacao"];


							if($nr_total_hora != 0)
                            {
                                $nr_acumulado_f = (floatval($nr_total_hora)/floatval($nr_efetivo)) + floatval($nr_acumulado_anterior);
                            }
                            else
                            {
                              $nr_acumulado_f = floatval($nr_acumulado_anterior);
                            }
							$nr_acumulado_anterior = $nr_acumulado_f;
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
						{
							$nr_total_hora_total += $nr_total_hora;
							$nr_efetivo_total += $nr_efetivo;
							$nr_meta_total = $nr_meta;
							$nr_referencial_total = $nr_referencial;
							$contador_ano_atual++;
							$media_ano[] = $nr_acumulado_f;
						}
						
						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_total_hora);
						$indicador[$linha][2] = app_decimal_para_php($nr_efetivo);
						$tendencia[] = $nr_acumulado_f;
						$indicador[$linha][3] = app_decimal_para_php($nr_acumulado_f);
						$indicador[$linha][4] = app_decimal_para_php($nr_meta);
						$indicador[$linha][5] = app_decimal_para_php($nr_referencial);
                        $indicador[$linha][7] = $observacao;

						$linha++;
					}
				}
				$linha_sem_media = $linha;
				if($contador_ano_atual > 0)
				{
					$indicador[$linha][0] = '';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = '';
					$indicador[$linha][4] = '';
					$indicador[$linha][5] = '';
					$indicador[$linha][6] = '';
                    $indicador[$linha][7] = '';
					$tendencia[] = 0;
					
					$linha++;
					
					$indicador[$linha][0] = 'Acum. de '.intval($tabela[0]['nr_ano_referencia']);
					$indicador[$linha][1] = "";#app_decimal_para_php($nr_total_hora_total);
					$indicador[$linha][2] = "";#app_decimal_para_php($nr_efetivo_total);
					$tendencia[] = $nr_acumulado_f;
					$indicador[$linha][3] = app_decimal_para_php($nr_acumulado_f);
					$indicador[$linha][4] = app_decimal_para_php($nr_meta_total);
					$indicador[$linha][5] = app_decimal_para_php($nr_referencial_total);
                    $indicador[$linha][7] = '';
					
					$linha++;
				}

				// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
				list($a,$b,$tend) = calcular_tendencia_logaritmica( $tendencia );
				for($i=0;$i<sizeof($tendencia);$i++)
				{
					$indicador[$i][6] = $tend[$i];
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode(nl2br($indicador[$i][7])), 'left');

					$linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='6';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'3,3,0,0;4,4,0,0;5,5,0,0',
					"0,0,1,$linha_sem_media",
					"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar,
                    1,
                    2,
                    3
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->helper( array('indicador') );
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/Administrativo_hhtr_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->Administrativo_hhtr_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$contador = sizeof( $collection );
				$nr_acumulado_anterior=0;
				foreach( $collection as $item )
				{
					$referencia = $item['mes_referencia'];

					$nr_total_hora = $item["nr_total_hora"];
					$nr_efetivo = $item["nr_efetivo"];

					if($nr_total_hora != 0)
                    {
                        $nr_acumulado_f = (floatval($nr_total_hora)/floatval($nr_efetivo)) + floatval($nr_acumulado_anterior);
                    }
                    else
                    {
                      $nr_acumulado_f = floatval($nr_acumulado_anterior);
                    }
					$nr_acumulado_anterior = $nr_acumulado_f;
				}

				$sql="";

				// gravar o acumulado do ano
				if( floatval($nr_acumulado_f)>0 )
				{
					$sql .= sprintf(" INSERT INTO indicador_plugin.administrativo_hhtr
					( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_acumulado_f, fl_media )
					VALUES ( '%s/01/01',current_timestamp,%s, %s, 'S' ); "
					, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($nr_acumulado_f) );
				}

				// indicar que o período foi fechado para o 'indicador_tabela'
				$sql.=sprintf( "	UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s
									WHERE cd_indicador_tabela=%s; "
					, intval(usuario_id())
					, intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){ $this->db->query($sql); }

			} #tabela_existe
		}

		redirect( 'indicador_plugin/administrativo_hhtr' );
	}
}
?>
