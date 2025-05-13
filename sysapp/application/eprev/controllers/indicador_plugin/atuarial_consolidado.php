<?php
class atuarial_consolidado extends Controller
{
	var $enum_indicador = 0;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::ATUARIAL_EAP_PLANO);
    }

    function index()
    {
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GA' ) )
		{
			
			// VERIFICA SE EXISTE TABELA NO PERÍODO ABERTO, SE NÃO EXISTIR, CRIAR TABELA NO PERÍODO QUE ESTIVER ABERTO
			indicador_db::abrir_periodo_para_indicador( intval($this->enum_indicador), usuario_id() );

	        $this->load->view('indicador_plugin/atuarial_consolidado/index.php');
		}
    }

    function listar()
    {
        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GA' ) )
        {
	        $this->load->model( 'indicador_plugin/Atuarial_consolidado_model' );

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args = array();
			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->Atuarial_consolidado_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/atuarial_consolidado/partial_result', $data);
			}
			else
			{
				echo "Nenhum período aberto para o indicador.";
			}
        }
    }

	function detalhe($cd=0)
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GA' ) )
		{
			$data['CD_INDICADOR'] = $this->enum_indicador;

			$this->load->model('indicador_plugin/Atuarial_consolidado_model');
			$row=$this->Atuarial_consolidado_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_meta, cd_indicador_tabela
					FROM indicador_plugin.atuarial_consolidado 
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
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_plugin/atuarial_consolidado/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GA' ) )
		{
			$this->load->model('indicador_plugin/Atuarial_consolidado_model');
			
			$args['cd_atuarial_consolidado']=intval($this->input->post('cd_atuarial_consolidado', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();

			$args["nr_reserva_tecnica"] = app_decimal_para_db($this->input->post("nr_reserva_tecnica", true));
			$args["nr_provisao_matematica"] = app_decimal_para_db($this->input->post("nr_provisao_matematica", true));
			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$msg=array();
			$retorno = $this->Atuarial_consolidado_model->salvar( $args,$msg );
			
			if($retorno)
			{
				redirect( "indicador_plugin/atuarial_consolidado", "refresh" );			
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GA' ) )
		{
			$this->load->model('indicador_plugin/Atuarial_consolidado_model');

			$this->Atuarial_consolidado_model->excluir($id);

			redirect( 'indicador_plugin/atuarial_consolidado', 'refresh' );
		}
	}

	function criar_indicador()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GA' ) )
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode('Mês'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode('Reservas Técnicas'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode('Provisões Matemáticas'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode('Variação Mensal'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode('Meta (%)'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode('Log. (variação mensal)'), 'background,center');

				$this->load->model('indicador_plugin/Atuarial_consolidado_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->Atuarial_consolidado_model->listar( $result, $args );
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
							$referencia = " Média de " . $item['ano_referencia'];

							$nr_reserva_tecnica = '';
							$nr_provisao_matematica = '';
							$variacao = '';
						}
						else
						{
							$referencia = $item['mes_referencia'];

							$nr_reserva_tecnica = $item["nr_reserva_tecnica"];
							$nr_provisao_matematica = $item["nr_provisao_matematica"];
							
							if( floatval($nr_reserva_tecnica)>0 )
							{
								$variacao = ( ( floatval($nr_reserva_tecnica)/floatval($nr_provisao_matematica) )-1 ) * 100;
							}
							else
							{
								$variacao='';
							}
						}

						if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])  )
						{
							$media_ano[] = $variacao;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_reserva_tecnica);
						$indicador[$linha][2] = app_decimal_para_php($nr_provisao_matematica);
						$ar_variacao[] = $variacao;
						$indicador[$linha][3] = app_decimal_para_php($variacao);
						$indicador[$linha][4] = app_decimal_para_php($nr_meta);

						$linha++;
					}
				}

				// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
				list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_variacao );
				for($i=0;$i<sizeof($ar_variacao);$i++)
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

					$linha++;

					$indicador[$linha][0] = '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = $media;
					$indicador[$linha][4] = '';
					$indicador[$linha][5] = '';
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2 );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center' );

					$linha++;
				}

				$coluna_para_ocultar='4,5';
				// gerar gráfico
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'3,3,0,0;4,4,0,0;5,5,0,0',
					"0,0,1,$linha_sem_media",
					"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

				echo "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresentação' );

			} #tabela_existe
		}
	}

	function fechar_periodo()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GA' ) )
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/Atuarial_consolidado_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->Atuarial_consolidado_model->listar( $result, $args );
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

						$nr_reserva_tecnica = '';
						$nr_provisao_matematica = '';
						$variacao = '';
					}
					else
					{
						$referencia = $item['mes_referencia'];

						$nr_reserva_tecnica = $item["nr_reserva_tecnica"];
						$nr_provisao_matematica = $item["nr_provisao_matematica"];
						
						if( floatval($nr_reserva_tecnica)>0 )
						{
							$variacao = ( ( floatval($nr_reserva_tecnica)/floatval($nr_provisao_matematica) )-1 ) * 100;
						}
						else
						{
							$variacao='';
						}
					}

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $variacao;
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

					$sql.=sprintf(" INSERT INTO indicador_plugin.atuarial_consolidado
					( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_variacao_f, nr_meta, fl_media ) 
					VALUES ( '%s/01/01',current_timestamp,%s, %s, %s, 'S' ); "
					, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($media), floatval( app_decimal_para_db($nr_meta) ));
				}

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/atuarial_consolidado' );
		// echo 'período encerrado com sucesso';
	}
}
?>