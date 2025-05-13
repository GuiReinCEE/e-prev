<?php
class desligamento_inadi extends Controller
{
	var	$label_0 = "Mês";
	var	$label_1 = "CEEEPREV";
	var	$label_2 = "Único CEEE";
	var	$label_3 = "FAMÍLIA";
    var $label_4 = "AES";
    var $label_5 = "CRM";
    var $label_6 = "SENGE";
    var $label_7 = "SINPRO";
    var $label_8 = "CGTEE";
    var $label_9 = "SINTAE";
    var $label_10 = "RGE";
    var $label_11 = "FUNDAÇÃO";
    var $label_12 = "Total";
    var $label_13 = "Tendência";

	var $enum_indicador = 0;

    var $fl_permissao = false;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::AREA_DESLIGAMENTO_INADI);

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

	        $this->load->view('indicador_atendimento/desligamento_inadi/index.php',$data);
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
		
		if($this->fl_permissao)
        {
	        $this->load->model( 'indicador_atendimento/desligamento_inadi_model' );

	        $data['collection'] = array();
	        $result = null;
			$args = array();

			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->desligamento_inadi_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_atendimento/desligamento_inadi/partial_result', $data);
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
		
		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_atendimento/desligamento_inadi_model');
			$row=$this->desligamento_inadi_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = " 
                        SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia,
                               dt_referencia,
                               nr_meta,
                               cd_indicador_tabela
					      FROM indicador_atendimento.desligamento_inadi
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

			$this->load->view('indicador_atendimento/desligamento_inadi/detalhe', $data);
		}
	}

	function salvar()
	{
		if($this->fl_permissao)
		{
			$this->load->model('indicador_atendimento/desligamento_inadi_model');
			
			$args['cd_desligamento_inadi']  = intval($this->input->post('cd_desligamento_inadi', true));
			$args["dt_referencia"]       = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"]            = $this->input->post("fl_media", true);
			$args["nr_valor_1"]          = intval($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]          = intval($this->input->post("nr_valor_2", true));
            $args["nr_valor_3"]          = intval($this->input->post("nr_valor_3", true));
			$args["nr_valor_4"]          = intval($this->input->post("nr_valor_4", true));
            $args["nr_valor_5"]          = intval($this->input->post("nr_valor_5", true));
            $args["nr_valor_6"]          = intval($this->input->post("nr_valor_6", true));
            $args["nr_valor_7"]          = intval($this->input->post("nr_valor_7", true));
            $args["nr_valor_8"]          = intval($this->input->post("nr_valor_8", true));
            $args["nr_valor_9"]          = intval($this->input->post("nr_valor_9", true));
            $args["nr_valor_10"]         = intval($this->input->post("nr_valor_10", true));
            $args["nr_valor_11"]         = intval($this->input->post("nr_valor_11", true));
			$args["nr_meta"]             = intval($this->input->post("nr_meta", true));
            $args["nr_percentual_f"]     = $args["nr_valor_1"] + $args["nr_valor_2"] + $args["nr_valor_3"] + $args["nr_valor_4"] +
                                           $args["nr_valor_5"] + $args["nr_valor_6"] + $args["nr_valor_7"] + $args["nr_valor_8"] +
                                           $args["nr_valor_9"] + $args["nr_valor_10"] + $args["nr_valor_11"];

			$msg=array();
			$retorno = $this->desligamento_inadi_model->salvar( $args,$msg );
			
			if($retorno)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_atendimento/desligamento_inadi", "refresh" );
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
			$this->load->model('indicador_atendimento/desligamento_inadi_model');

			$this->desligamento_inadi_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_atendimento/desligamento_inadi', 'refresh' );
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
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_12']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 13,0, utf8_encode($data['label_13']), 'background,center');

				$this->load->model('indicador_atendimento/desligamento_inadi_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
				$this->desligamento_inadi_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;
                $ar_tendencia=array();
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
                $soma_vl_10 = 0;
                $soma_vl_11 = 0;

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
							$referencia = $item['mes_referencia'];
							
							
							$nr_valor_1  = $item["nr_valor_1"];
							$nr_valor_2  = $item["nr_valor_2"];
							$nr_valor_3  = $item["nr_valor_3"];
							$nr_valor_4  = $item["nr_valor_4"];
							$nr_valor_5  = $item["nr_valor_5"];
							$nr_valor_6  = $item["nr_valor_6"];
							$nr_valor_7  = $item["nr_valor_7"];
							$nr_valor_8  = $item["nr_valor_8"];
							$nr_valor_9  = $item["nr_valor_9"];
							$nr_valor_10 = $item["nr_valor_10"];
							$nr_valor_11 = $item["nr_valor_11"];
							$nr_percentual_f = $item['nr_percentual_f'];

							if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
							{
								$media_ano[] = $nr_percentual_f;
							}

							$indicador[$linha][0] = $referencia;
							$indicador[$linha][1] = intval($nr_valor_1);
							$indicador[$linha][2] = intval($nr_valor_2);
							$indicador[$linha][3] = intval($nr_valor_3);
							$indicador[$linha][4] = intval($nr_valor_4);
							$indicador[$linha][5] = intval($nr_valor_5);
							$indicador[$linha][6] = intval($nr_valor_6);
							$indicador[$linha][7] = intval($nr_valor_7);
							$indicador[$linha][8] = intval($nr_valor_8);
							$indicador[$linha][9] = intval($nr_valor_9);
							$indicador[$linha][10] = intval($nr_valor_10);
							$indicador[$linha][11] = intval($nr_valor_11);
							$ar_tendencia[] = $nr_percentual_f;
							$indicador[$linha][12] = intval($nr_percentual_f);

							$linha++;							
							
							
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
							$soma_vl_10 += $nr_valor_10;
							$soma_vl_11 += $nr_valor_11;							
						}
					}
				}

				list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
				for($i=0;$i<sizeof($ar_tendencia);$i++)
				{
					$indicador[$i][13] = $tend[$i];
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
                    $indicador[$linha][6] = '';
                    $indicador[$linha][7] = '';
                    $indicador[$linha][8] = '';
                    $indicador[$linha][9] = '';
                    $indicador[$linha][10] = '';
                    $indicador[$linha][11] = '';
                    $indicador[$linha][12] = '';
                    $indicador[$linha][13] = '';

					$linha++;

					$indicador[$linha][0] = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
					$indicador[$linha][1] = $soma_vl_1;
					$indicador[$linha][2] = $soma_vl_2;
					$indicador[$linha][3] = $soma_vl_3;
					$indicador[$linha][4] = $soma_vl_4;
					$indicador[$linha][5] = $soma_vl_5;
                    $indicador[$linha][6] = $soma_vl_6;
                    $indicador[$linha][7] = $soma_vl_7;
                    $indicador[$linha][8] = $soma_vl_8;
                    $indicador[$linha][9] = $soma_vl_9;
                    $indicador[$linha][10] = $soma_vl_10;
                    $indicador[$linha][11] = $soma_vl_11;
                    $indicador[$linha][12] = $soma_total;
                    $indicador[$linha][13] = '';
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][10]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][11]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 12, $linha, app_decimal_para_php($indicador[$i][12]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 13, $linha, app_decimal_para_php($indicador[$i][13]), 'center' );
                    $linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='13';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'1,1,0,0;2,2,0,0;3,3,0,0;4,4,0,0;5,5,0,0;6,6,0,0;7,7,0,0;8,8,0,0;9,9,0,0;10,10,0,0;11,11,0,0',
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media;3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;
                     5,5,1,$linha_sem_media;6,6,1,$linha_sem_media;7,7,1,$linha_sem_media;8,8,1,$linha_sem_media;
                     9,9,1,$linha_sem_media;10,10,1,$linha_sem_media;11,11,1,$linha_sem_media",
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
			
			$this->load->model('indicador_atendimento/desligamento_inadi_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->desligamento_inadi_model->listar( $result, $args );
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
                $soma_vl_10  = 0;
                $soma_vl_11  = 0;
                
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
                    $nr_valor_10 = $item["nr_valor_10"];
                    $nr_valor_11 = $item["nr_valor_11"];
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
                    $soma_vl_10  += $nr_valor_10;
                    $soma_vl_11  += $nr_valor_11;

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $nr_percentual_f;
					}
				}

				$sql="";

				if(sizeof($media_ano)>0)
				{
					
					$sql.=sprintf("
                        INSERT INTO indicador_atendimento.desligamento_inadi
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
                                   nr_valor_10,
                                   nr_valor_11,
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
                        , floatval($soma_vl_10)
                        , floatval($soma_vl_11)
                   );
				}

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

		redirect( 'indicador_atendimento/desligamento_inadi' );
		// echo 'período encerrado com sucesso';
	}
}
?>