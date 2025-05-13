<?php
class Administrativo_absenteismo extends Controller
{
	function __construct()
    {
        parent::Controller();

		$this->enum_indicador = intval(enum_indicador::RH_ABSENTEISMO);

        CheckLogin();
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/administrativo_absenteismo_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS' ) )
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta( $this->enum_indicador );

	        $this->load->view('indicador_plugin/administrativo_absenteismo/index.php',$data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
        {
	        $data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        $data['label_6'] = $this->label_6;
	        $data['label_7'] = $this->label_7;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			$data['collection'] = $this->administrativo_absenteismo_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/administrativo_absenteismo/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function detalhe($cd_administrativo_absenteismo = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
	        $data['label_4'] = $this->label_4;
	        $data['label_6'] = $this->label_6;
	        $data['label_7'] = $this->label_7;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_administrativo_absenteismo) == 0)
			{
				$row = $this->administrativo_absenteismo_model->carrega_referencia();

				$data['row'] = array(
					'cd_administrativo_absenteismo' => 0,
					'cd_indicador_tabela' 			=> $row['cd_indicador_tabela'],
					'dt_referencia' 	  			=> $row['mes_referencia'],
					'observacao' 					=> '',
					'nr_meta' 	  					=> $row['nr_meta'],
					'nr_referencial' 	  			=> $row['nr_referencial'],
					'nr_valor_1' 		  			=> 0,
					'nr_valor_2' 		  			=> 0
				);
			}
			else
			{
				$data['row'] = $this->administrativo_absenteismo_model->carregar($cd_administrativo_absenteismo);
			}

			$this->load->view('indicador_plugin/administrativo_absenteismo/detalhe', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{			
			$cd_administrativo_absenteismo = $this->input->post('cd_administrativo_absenteismo', TRUE);

            $args = array(
				'dt_referencia' 	  => $this->input->post("dt_referencia", TRUE),
				'cd_indicador_tabela' => $this->input->post("cd_indicador_tabela", TRUE),
				'fl_media' 			  => 'N',
				'nr_valor_1' 		  => app_decimal_para_db($this->input->post("nr_valor_1", TRUE)),
				'nr_valor_2' 		  => app_decimal_para_db($this->input->post("nr_valor_2", TRUE)),
				'nr_meta' 			  => app_decimal_para_db($this->input->post("nr_meta", TRUE)),
				'nr_referencial' 	  => app_decimal_para_db($this->input->post("nr_referencial", TRUE)),
				'nr_percentual_f' 	  => '',
				'observacao' 		  => $this->input->post("observacao", TRUE),
				'cd_usuario' 		  => $this->cd_usuario
            );

            if(intval($cd_administrativo_absenteismo) == 0)
            {
				$this->administrativo_absenteismo_model->salvar($args);
            }
            else
            {
				$this->administrativo_absenteismo_model->atualizar($cd_administrativo_absenteismo, $args);
            }
			
			$this->criar_indicador();

			redirect('indicador_plugin/administrativo_absenteismo');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_administrativo_absenteismo)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$this->administrativo_absenteismo_model->excluir($cd_administrativo_absenteismo, $this->cd_usuario);

            $this->criar_indicador();

            redirect('indicador_plugin/administrativo_absenteismo');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela']).";";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			
			$collection = $this->administrativo_absenteismo_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador = array();
			$linha 	   = 0;
			$contador  = sizeof($collection);
			$media_ano = array();

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - 5)
				{
					$nr_meta 	    = $item["nr_meta"];
					$nr_referencial = $item["nr_referencial"];

					if($item['fl_media'] == 'S')
					{
						$referencia = " Média de " . $item['ano_referencia'];

						$nr_valor_1      = '';
						$nr_valor_2      = '';
                        $observacao      = '';
						$nr_percentual_f = $item['nr_percentual_f'];
					}
					else
					{
						$referencia = $item['mes_referencia'];

						$nr_valor_1 = $item["nr_valor_1"];
						$nr_valor_2 = $item["nr_valor_2"];
                        $observacao = $item["observacao"];
						
						if(floatval($nr_valor_1) > 0)
						{
							$nr_percentual_f = (floatval($nr_valor_2) / floatval($nr_valor_1)) * 100;
						}
						else
						{
							$nr_percentual_f = '0';
						}
					}

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$media_ano[] = $nr_percentual_f;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
					$ar_tendencia[]       = $nr_percentual_f;
					$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
					$indicador[$linha][4] = app_decimal_para_php($nr_meta);
					$indicador[$linha][6] = app_decimal_para_php($nr_referencial);
                    $indicador[$linha][7] = $observacao;

					$linha++;
				}
			}

			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);

			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(sizeof($media_ano) > 0)
			{
				$media = 0;

				foreach($media_ano as $valor)
				{
					$media += $valor;
				}

				$media = number_format(($media / sizeof($media_ano)), 2);

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
                $indicador[$linha][7] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = $media;
				$indicador[$linha][4] = $nr_meta;
				$indicador[$linha][5] = "";
				$indicador[$linha][6] = $nr_referencial;
                $indicador[$linha][7] = '';
			}

			$linha = 1;

			for( $i=0; $i<sizeof($indicador); $i++ )
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2 );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2 );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S' );
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode(nl2br($indicador[$i][7])), 'left');

				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='5';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0;6,6,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;6,6,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar,
                1,
                2
			);

			$this->db->query($sql);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GS'))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));

	        $collection = $this->administrativo_absenteismo_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador  = sizeof($collection);
			$media_ano = array();

			foreach($collection as $item)
			{
				$nr_meta 		= $item["nr_meta"];
				$nr_referencial = $item["nr_referencial"];

				if( $item['fl_media']=='S' )
				{
					$referencia = " Média de " . $item['ano_referencia'];

					$nr_valor_1      = '';
					$nr_valor_2      = '';
					$nr_percentual_f = '';
				}
				else
				{
					$referencia = $item['mes_referencia'];

					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					
					if(floatval($nr_valor_1) > 0)
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

			if(sizeof($media_ano)>0)
			{
				$media = 0;

				foreach( $media_ano as $valor )
				{
					$media += $valor;
				}

				$media = ($media / sizeof($media_ano));

				$args = array(
					'dt_referencia' 	  => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'fl_media' 			  => 'S',
					'observacao' 		  => '',
					'nr_meta' 			  => $nr_meta,
					'nr_percentual_f'     => $media,
					'nr_referencial' 	  => app_decimal_para_db($nr_referencial),
					'cd_usuario' 		  => $this->cd_usuario
	            );

	            $this->administrativo_absenteismo_model->salvar($args);

	            $this->administrativo_absenteismo_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);
			}

			redirect('indicador_plugin/administrativo_absenteismo');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}
?>