<?php
class Secretaria_sumulas_cf extends Controller
{
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::SECRETARIA_DIVUGACAO_SUMULAS_CF);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->load->model('indicador_plugin/secretaria_sumulas_cf_model');		
	}

    function index()
    {
		if(indicador_db::verificar_permissao( usuario_id(), 'SG' ))
		{
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('indicador_plugin/secretaria_sumulas_cf/index.php', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar()
    {
        if(indicador_db::verificar_permissao(usuario_id(),'SG'))
        {
        	$args   = array();
        	$data   = array();
        	$result = null;
	        
	        $data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_6'] = $this->label_6;

	        $tabela = indicador_tabela_aberta( $this->enum_indicador );

			$data['tabela'] = $tabela;

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

			$this->secretaria_sumulas_cf_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('indicador_plugin/secretaria_sumulas_cf/index_result', $data);
		}
		else
		{
			echo "Nenhum período aberto para o indicador.";
		}
    }

	function cadastro($cd_secretaria_sumulas_cf=0)
	{
	    if(indicador_db::verificar_permissao(usuario_id(),'SG'))
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

			$args['cd_secretaria_sumulas_cf'] = $cd_secretaria_sumulas_cf;

			if(intval($args['cd_secretaria_sumulas_cf']) == 0)
			{
				$this->secretaria_sumulas_cf_model->carrega_referencia($result);
				$arr = $result->row_array();

				$data['row']['cd_secretaria_sumulas_cf']         = (isset($arr['cd_secretaria_sumulas_cf']) ? $arr['cd_secretaria_sumulas_cf'] : "");
				$data['row']['nr_valor_1']            = "";
				$data['row']['nr_valor_2']            = "";
				$data['row']['fl_media']              = "";
				$data['row']['observacao']            = "";
				$data['row']['dt_referencia']         = (isset($arr['dt_referencia']) ? $arr['dt_referencia'] : "");
				$data['row']['nr_meta']               = (isset($arr['nr_meta']) ? $arr['nr_meta'] : 0);
				$data['row']['cd_indicador_tabela']   = (isset($arr['cd_indicador_tabela']) ? $arr['cd_indicador_tabela'] : 0);
			}
			else
			{
				$this->secretaria_sumulas_cf_model->carregar($result, $args);
				$data['row'] = $result->row_array(); 
			}

			$this->load->view('indicador_plugin/secretaria_sumulas_cf/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'SG'))
		{	
			$result = null;

			$args   = array(
				   'cd_secretaria_sumulas_cf' => intval($this->input->post('cd_secretaria_sumulas_cf', true)),
				   'dt_referencia'		   => $this->input->post("dt_referencia", true),
				   'cd_usuario'            => $this->session->userdata('codigo'),
				   'cd_indicador_tabela'   => $this->input->post("cd_indicador_tabela", true),
				   'fl_media'              => $this->input->post("fl_media", true),
				   'nr_valor_1'            => app_decimal_para_db($this->input->post("nr_valor_1", true)),
				   'nr_valor_2'            => app_decimal_para_db($this->input->post("nr_valor_2", true)),
				   'nr_meta'               => app_decimal_para_db($this->input->post("nr_meta", true)),
				   'observacao'            => $this->input->post("observacao", true)
			);
			
           $this->secretaria_sumulas_cf_model->salvar($args);
           
            $this->criar_indicador();

            redirect( "indicador_plugin/secretaria_sumulas_cf", "refresh" );
        }
		 else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function excluir($cd_secretaria_sumulas_cf)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'SG'))
		{
			$this->load->model('indicador_plugin/secretaria_sumulas_cf_model');

			$this->secretaria_sumulas_cf_model->excluir($cd_secretaria_sumulas_cf, $this->session->userdata('codigo'));

			$this->criar_indicador();
			
			redirect( "indicador_plugin/secretaria_sumulas_cf", "refresh" );
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
        if(indicador_db::verificar_permissao(usuario_id(),'SG'))
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

			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; 

			$this->secretaria_sumulas_cf_model->listar($result, $args);
			$collection = $result->result_array();	

			$contador  = sizeof($collection);
			$indicador = array();
			$media_ano = array();
			$linha     = 0;
			$soma_v1   = 0;
			$soma_v2   = 0;
				
			foreach($collection as $item)
			{
				if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
				{
					$nr_meta         = $item["nr_meta"];
					$nr_percentual_f = $item['nr_percentual_f'];
                    $observacao      = $item["observacao"];

					if( $item['fl_media']=='S' )
					{
						$referencia = " Resultado de " . $item['ano_referencia'];
						$nr_valor_1 = $item['nr_valor_1'];
						$nr_valor_2 = $item['nr_valor_2'];
						$nr_percentual_f = $item['nr_percentual_f'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
						$nr_valor_1 = $item['nr_valor_1'];
						$nr_valor_2 = $item['nr_valor_2'];
						
						if( floatval($nr_valor_1)>0 )
						{
							$nr_percentual_f = ( floatval($nr_valor_2)/floatval($nr_valor_1) )*100;
						}
						else
						{
							$nr_percentual_f = '0';
						}
					}

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S')
					{
						$media_ano[] = $nr_percentual_f;
						$soma_v1 += $nr_valor_1;
						$soma_v2 += $nr_valor_2;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
					$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
					$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
					$indicador[$linha][4] = app_decimal_para_php($nr_meta);
                    $indicador[$linha][6] = $observacao;

                    $ar_tendencia[]       = $nr_percentual_f;

					$linha++;
				}
			}

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
				$indicador[$linha][1] = $soma_v1;
				$indicador[$linha][2] = $soma_v2;
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

			$this->db->query($sql);
		}
		else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao(usuario_id(),'SG'))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$args   = array();
			$data   = array();
			$result = null;	

			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

	        $this->secretaria_sumulas_cf_model->listar($result, $args);
			$collection = $result->result_array();

			$contador  = sizeof($collection);
			$media_ano = array();
			$soma_v1   = 0;
			$soma_v2   = 0;

			foreach( $collection as $item )
			{
				$nr_meta = $item["nr_meta"];

				if( $item['fl_media']=='S' )
				{
					$referencia = " Resultado de " .$item['ano_referencia'];
					$nr_percentual_f = $item['nr_percentual_f'];
				}
				else
				{
					$referencia = $item['mes_referencia'];

					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					
					if( floatval($nr_valor_1)>0 )
					{
						$nr_percentual_f = (( floatval($nr_valor_2)/floatval($nr_valor_1) )*100);
					}
					else
					{
						$nr_percentual_f = '0';
					}
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$media_ano[] = $nr_percentual_f;
					$soma_v1    += $nr_valor_1;
				    $soma_v2    += $nr_valor_2;
				}
			}

			if(sizeof($media_ano) > 0)
			{
				$media = 0;

				foreach( $media_ano as $valor )
				{
					$media += $valor;
				}

				$media = ( $media / sizeof($media_ano) );

				$args = array(
					'cd_indicador_tabela' => $args['cd_indicador_tabela'],
					'dt_referencia'	      => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_valor_1'          => $soma_v1,
					'nr_valor_2'		  => $soma_v2,
					'nr_percentual_f'     => $media,
				    'nr_meta'             => app_decimal_para_db($nr_meta),
				    'cd_usuario'          => $this->session->userdata('codigo')
				);

				$this->secretaria_sumulas_cf_model->atualiza_fechar_periodo($result, $args);
			}

			$this->secretaria_sumulas_cf_model->fechar_periodo($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
		redirect( 'indicador_plugin/secretaria_sumulas_cf', 'refresh' );
	}
}
?>