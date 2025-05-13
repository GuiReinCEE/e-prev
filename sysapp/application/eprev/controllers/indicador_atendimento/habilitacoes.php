<?php
class habilitacoes extends Controller
{
	var	$label_0 = "Mês";
	var	$label_1 = "Benefício";
	var	$label_2 = "Pl. Único CEEE";
	var	$label_3 = "Pl. Único RGE";
    var $label_4 = "Pl. Único AES SUL";
    var $label_5 = "Pl. Único CGTEE";
    var $label_6 = "CREEPREV";
    var $label_7 = "CRM Prev";
    var $label_8 = "SENGE";
    var $label_9 = "SINPRO";
    var $label_10 = "Fundação";
    var $label_11 = "Total";

	var $enum_indicador = 0;

    var $fl_permissao = false;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::AREA_HABILITACOES);

        $this->load->helper( array('indicador') );
        CheckLogin();

        if(gerencia_in(array('GP')))
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
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('indicador_atendimento/habilitacoes/index.php',$data);
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
		
		if($this->fl_permissao)
        {
	        $this->load->model( 'indicador_atendimento/habilitacoes_model' );

	        $data['collection'] = array();
	        $result = null;
			$args = array();

			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->habilitacoes_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_atendimento/habilitacoes/partial_result', $data);
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
		
		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_atendimento/habilitacoes_model');
			$row=$this->habilitacoes_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = " 
                        SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia,
                               dt_referencia,
                               nr_meta,
                               cd_indicador_tabela
					      FROM indicador_atendimento.habilitacoes
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

			$this->load->view('indicador_atendimento/habilitacoes/detalhe', $data);
		}
	}

	function salvar()
	{
		if($this->fl_permissao)
		{
			$this->load->model('indicador_atendimento/habilitacoes_model');
			
			$args['cd_habilitacoes']  = intval($this->input->post('cd_habilitacoes', true));
			$args["dt_referencia"]       = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"]            = $this->input->post("fl_media", true);
			$args["nr_valor_1"]          = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]          = app_decimal_para_db($this->input->post("nr_valor_2", true));
            $args["nr_valor_3"]          = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_valor_4"]          = app_decimal_para_db($this->input->post("nr_valor_4", true));
            $args["nr_valor_5"]          = app_decimal_para_db($this->input->post("nr_valor_5", true));
            $args["nr_valor_6"]          = app_decimal_para_db($this->input->post("nr_valor_6", true));
            $args["nr_valor_7"]          = app_decimal_para_db($this->input->post("nr_valor_7", true));
            $args["nr_valor_8"]          = app_decimal_para_db($this->input->post("nr_valor_8", true));
            $args["nr_valor_9"]          = app_decimal_para_db($this->input->post("nr_valor_9", true));
            $args["beneficio"]           = app_decimal_para_db($this->input->post("beneficio", true));
			$args["nr_meta"]             = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["nr_percentual_f"]     = $args["nr_valor_1"] + $args["nr_valor_2"] + $args["nr_valor_3"] + $args["nr_valor_4"] +
                                           $args["nr_valor_5"] + $args["nr_valor_6"] + $args["nr_valor_7"] + $args["nr_valor_8"] +
                                           $args["nr_valor_9"];

			$msg=array();
			$retorno = $this->habilitacoes_model->salvar( $args,$msg );
			
			if($retorno)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_atendimento/habilitacoes/detalhe", "refresh" );
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
			$this->load->model('indicador_atendimento/habilitacoes_model');

			$this->habilitacoes_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_atendimento/habilitacoes', 'refresh' );
			}				
		}
	}

	function criar_indicador()
	{
		$data['label_0'] = $this->label_0;

        $soma_tl_pe = 0;
        $soma_tl_ap = 0;
        $soma_tl_au = 0;
        $soma_tl_pr = 0;

		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			if(sizeof($tabela)<=0)
			{
				return false;
			}
			else
			{
				$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode('Pensão'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode('Aposentadoria'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode('Aux-Doença'), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode('Prorrogações'), 'background,center');

				$this->load->model('indicador_atendimento/habilitacoes_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
				$this->habilitacoes_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;
                $ar_tendencia=array();
				$contador = sizeof($collection);
				$media_ano=array();
                $ar_valores = array();
                $mes = '';
                $contador_arr = 0;

				foreach( $collection as $item )
				{
					if(true)
					{
						if( $item['fl_media']=='S' )
						{
							$referencia = " Total de " . $item['ano_referencia'];
						}
						else
						{
                            $a_data = explode( "/", $item['mes_referencia'] );
                            $referencia = $item['mes_referencia'];
						}

                        $nr_percentual_f = $item['nr_percentual_f'];
                        $beneficio = $item["beneficio"];

                        if($a_data[0] != $mes AND $mes != '' )
                        {
                            $contador_arr++;
                        }
                        
						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_percentual_f;
						}

                        if($a_data[0] != $mes)
                        {
                            $ar_valores[$contador_arr]['mes'] = $referencia;
                            $mes = $a_data[0];
                        }
                        
                        switch ($beneficio)
                        {
                            case 'Pensão' : 
                                $ar_valores[$contador_arr]['pe'] = $item['nr_percentual_f'];
                                break;
                            
                            case 'Aposentadoria' : 
                                $ar_valores[$contador_arr]['ap'] = $item['nr_percentual_f'];
                                break;
                            
                            case 'Aux-Doença' : 
                                $ar_valores[$contador_arr]['au'] = $item['nr_percentual_f'];
                                break;
                            
                            case 'Prorrogações' : 
                                $ar_valores[$contador_arr]['pr'] = $item['nr_percentual_f'];
                                break;
                        }
					}
				}

                foreach( $ar_valores as $item )
                {
                    $indicador[$linha][0] = $item['mes'];
                    $indicador[$linha][1] = (isset($item['pe']) ? $item['pe'] : '');
                    $indicador[$linha][2] = (isset($item['ap']) ? $item['ap'] : '');
                    $indicador[$linha][3] = (isset($item['au']) ? $item['au'] : '');
                    $indicador[$linha][4] = (isset($item['pr']) ? $item['pr'] : '');
                    $ar_tendencia[] = $nr_percentual_f;


                    $soma_tl_pe +=  (isset($item['pe']) ? $item['pe'] : 0);
                    $soma_tl_ap +=  (isset($item['ap']) ? $item['ap'] : 0);
                    $soma_tl_au +=  (isset($item['au']) ? $item['au'] : 0);
                    $soma_tl_pr +=  (isset($item['pr']) ? $item['pr'] : 0);

                    $linha++;
                }

				list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
				for($i=0;$i<sizeof($ar_tendencia);$i++)
				{
					$indicador[$i][5] = $tend[$i];
				}

				$linha_sem_media = $linha;

				if(sizeof($media_ano)>0)
				{
					$indicador[$linha][0] = '';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = '';
					$indicador[$linha][4] = '';
					$indicador[$linha][5] = '';

                    $linha++;

                    $indicador[$linha][0] = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
					$indicador[$linha][1] = $soma_tl_pe;
					$indicador[$linha][2] = $soma_tl_ap;
					$indicador[$linha][3] = $soma_tl_au;
                    $indicador[$linha][4] = $soma_tl_pr;
					$indicador[$linha][5] = '';

					$linha++;
				}

				$linha = 1;

				for( $i=0; $i<sizeof($indicador); $i++ )
				{
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center' );
                    $linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::BARRA_MULTIPLO,
					'1,1,0,0;2,2,0,0;3,3,0,0;4,4,0,0',
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media;3,3,1,$linha_sem_media;4,4,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar
				);

				if(trim($sql)!=''){$this->db->query($sql);}

				return true;
			}
		}
	}

	function fechar_periodo()
	{
		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_atendimento/habilitacoes_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->habilitacoes_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{
				echo "Não foi identificado período aberto para o Indicador";
			}
			else
			{
				$contador = sizeof($collection);
				$media_ano=array();

                $soma_total = 0;
                $soma_vl_1  = 0;
                $soma_vl_2  = 0;
                $soma_vl_3  = 0;
                $soma_vl_4  = 0;
                $soma_vl_5  = 0;
                $soma_vl_6  = 0;
                $soma_vl_7  = 0;
                $soma_vl_8  = 0;
                $soma_vl_9  = 0;
                
				foreach( $collection as $item )
				{
					$nr_meta = $item["nr_meta"];

                    if( $item['fl_media']=='S' )
                    {
                        $referencia = " Total de " . $item['ano_referencia'];
                    }
                    else
                    {
                        $referencia = $item['mes_referencia'];
                    }

                    $nr_valor_1 = $item["nr_valor_1"];
                    $nr_valor_2 = $item["nr_valor_2"];
                    $nr_valor_3 = $item["nr_valor_3"];
                    $nr_valor_4 = $item["nr_valor_4"];
                    $nr_valor_5 = $item["nr_valor_5"];
                    $nr_valor_6 = $item["nr_valor_6"];
                    $nr_valor_7 = $item["nr_valor_7"];
                    $nr_valor_8 = $item["nr_valor_8"];
                    $nr_valor_9 = $item["nr_valor_9"];
                    $nr_percentual_f = $item['nr_percentual_f'];

                    $soma_total += $nr_percentual_f;
                    $soma_vl_1  += $nr_valor_1;
                    $soma_vl_2  += $nr_valor_2;
                    $soma_vl_3  += $nr_valor_3;
                    $soma_vl_4  += $nr_valor_4;
                    $soma_vl_5  += $nr_valor_5;
                    $soma_vl_6  += $nr_valor_6;
                    $soma_vl_7  += $nr_valor_7;
                    $soma_vl_8  += $nr_valor_8;
                    $soma_vl_9  += $nr_valor_9;

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $nr_percentual_f;
					}
				}

				$sql="";
	/*
				if(sizeof($media_ano)>0)
				{
					
					$sql.=sprintf("
                        INSERT INTO indicador_atendimento.habilitacoes
					              (
                                   dt_referencia,
                                   dt_inclusao,
                                   cd_usuario_inclusao,
                                   nr_percentual_f,
                                   nr_valor_1,
                                   nr_valor_2,
                                   nr_valor_3,
                                   nr_valor_4,
                                   nr_valor_5,
                                   nr_valor_6,
                                   nr_valor_7,
                                   nr_valor_8,
                                   nr_valor_9,
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
                                   'S'
                                  ); "
                        , intval($tabela[0]['nr_ano_referencia'])
                        , usuario_id()
                        , floatval($soma_total)
                        , floatval($soma_vl_1)
                        , floatval($soma_vl_2)
                        , floatval($soma_vl_3)
                        , floatval($soma_vl_4)
                        , floatval($soma_vl_5)
                        , floatval($soma_vl_6)
                        , floatval($soma_vl_7)
                        , floatval($soma_vl_8)
                        , floatval($soma_vl_9)
                   );
				}
*/
				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela 
                                    SET dt_fechamento_periodo         = CURRENT_TIMESTAMP,
                                        cd_usuario_fechamento_periodo = %s
                                  WHERE cd_indicador_tabela = %s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

			} #tabela_existe
		}

		redirect( 'indicador_atendimento/habilitacoes' );
		// echo 'período encerrado com sucesso';
	}
}
?>