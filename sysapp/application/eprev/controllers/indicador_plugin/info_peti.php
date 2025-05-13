<?php
class info_peti extends Controller
{
	var	$label_0 = "Semestre";
	var	$label_1 = "Realização";
	var	$label_2 = "% de realização da Meta";
    var	$label_3 = "Meta";
    var	$label_4 = "Tendência";
    var $label_5 = "Observação";
	var $enum_indicador = 0;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::INFO_PETI);
    }

    function index()
    {
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GTI' ) )
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

	        $this->load->view('indicador_plugin/info_peti/index.php',$data);
		}
    }

    function listar()
    {
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
        $data['label_5'] = $this->label_5;

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GTI' ) )
        {
	        $this->load->model( 'indicador_plugin/info_peti_model' );

	        $data['collection'] = array();
	        $result = null;

	        // --------------------------
			// filtros ...

			$args = array();
			$tabela = indicador_tabela_aberta(  $this->enum_indicador  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->info_peti_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/info_peti/partial_result', $data);
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
		$data['label_3'] = $this->label_3;
        $data['label_5'] = $this->label_5;

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GTI' ) )
		{

			$tabela = indicador_tabela_aberta( $this->enum_indicador );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/info_peti_model');
			$row=$this->info_peti_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_meta, cd_indicador_tabela
					FROM indicador_plugin.info_peti
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

			$this->load->view('indicador_plugin/info_peti/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GTI' ) )
		{
			$this->load->model('indicador_plugin/info_peti_model');
			
			$args['cd_info_peti']=intval($this->input->post('cd_info_peti', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->info_peti_model->salvar( $args,$msg );
			
			if($retorno)
			{
				
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/info_peti", "refresh" );
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GTI' ) )
		{
			$this->load->model('indicador_plugin/info_peti_model');

			$this->info_peti_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/info_peti", "refresh" );
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
		
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GTI' ) )
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
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, "", 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');

                $this->load->model('indicador_plugin/info_peti_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->info_peti_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$media_ano=array();
				foreach( $collection as $item )
				{
					// histório de 5 anos atrás
					#if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					if(true)
					{
						$nr_meta    = $item["nr_meta"];
                        $observacao = $item["observacao"];
						$tp_analise = $item['tp_analise'];

						if( $item['fl_media']=='S' )
						{
							$referencia = " Média de " . $item['ano_referencia'];

                            $nr_valor_1 = '';
	
							$nr_percentual_f = floatval($item['nr_percentual_f']);
						}
						else
						{
							$referencia = $item['mes_referencia'];

                            $nr_valor_1 = $item["nr_valor_1"];
							
							$nr_percentual_f = floatval($item['nr_percentual_f']);
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_percentual_f;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
						$indicador[$linha][2] = floatval($nr_valor_1);
						$ar_tendencia[] = $nr_percentual_f;
						$indicador[$linha][3] = floatval($nr_percentual_f);
						$indicador[$linha][4] = floatval($nr_meta);
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
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'left');
                    $linha++;
				}

				// gerar gráfico
                $coluna_para_ocultar='5';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::BARRA_MULTIPLO,
					'2,2,0,0;4,4,0,0;3,3,0,0',
					"0,0,1,$linha_sem_media",
					"2,2,1,$linha_sem_media;4,4,1,$linha_sem_media;3,3,1,$linha_sem_media-linha",
					usuario_id(),
					$coluna_para_ocultar,
					1
				);


				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

				return true;
				/*echo "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresentação' );*/

			} #tabela_existe
		}
	}
}
?>