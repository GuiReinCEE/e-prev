<?php
class senhas_liberadas extends Controller
{
	var	$label_0 = "M�s";
	var	$label_1 = "Consulta";
	var	$label_2 = "Completa";
    var $label_3 = "Total";
    var $label_4 = "Tend�ncia";

	var $enum_indicador = 0;

    var $fl_permissao = false;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::AREA_SENHAS_LIBERADAS);

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

	        $this->load->view('indicador_atendimento/senhas_liberadas/index.php',$data);
		}
    }

    function listar()
    {
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		
		if($this->fl_permissao)
        {
	        $this->load->model( 'indicador_atendimento/senhas_liberadas_model' );

	        $data['collection'] = array();
	        $result = null;
			$args = array();

			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->senhas_liberadas_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_atendimento/senhas_liberadas/partial_result', $data);
			}
			else
			{
				echo "Nenhum per�odo aberto para o indicador.";
			}
        }
    }

	function detalhe($cd=0)
	{
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
		$data['label_2'] = $this->label_2;
		$data['label_3'] = $this->label_3;
		
		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_atendimento/senhas_liberadas_model');
			$row=$this->senhas_liberadas_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = " 
                        SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia,
                               dt_referencia,
                               nr_meta,
                               cd_indicador_tabela
					      FROM indicador_atendimento.senhas_liberadas
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

			$this->load->view('indicador_atendimento/senhas_liberadas/detalhe', $data);
		}
	}

	function salvar()
	{
		if($this->fl_permissao)
		{
			$this->load->model('indicador_atendimento/senhas_liberadas_model');
			
			$args['cd_senhas_liberadas'] = intval($this->input->post('cd_senhas_liberadas', true));
			$args["dt_referencia"]       = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"]            = $this->input->post("fl_media", true);
			$args["nr_valor_1"]          = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"]          = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_meta"]             = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["nr_percentual_f"]     = $args["nr_valor_1"] + $args["nr_valor_2"];

			$msg=array();
			$retorno = $this->senhas_liberadas_model->salvar( $args,$msg );
			
			if($retorno)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_atendimento/senhas_liberadas", "refresh" );
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
			$this->load->model('indicador_atendimento/senhas_liberadas_model');

			$this->senhas_liberadas_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_atendimento/senhas_liberadas', 'refresh' );
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

				$this->load->model('indicador_atendimento/senhas_liberadas_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
				$this->senhas_liberadas_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;
                $ar_tendencia=array();
				$contador = sizeof($collection);
				$media_ano=array();

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
						}

                        $nr_valor_1 = $item["nr_valor_1"];
                        $nr_valor_2 = $item["nr_valor_2"];
                        $nr_percentual_f = $item['nr_percentual_f'];

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_percentual_f;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
						$ar_tendencia[] = $nr_percentual_f;
						$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);

						$linha++;
					}
				}

				list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
				for($i=0;$i<sizeof($ar_tendencia);$i++)
				{
					$indicador[$i][4] = $tend[$i];
				}

				$linha_sem_media = $linha;

				if(sizeof($media_ano)>0)
				{
					$indicador[$linha][0] = '';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = '';
					$indicador[$linha][4] = '';

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

				// gerar gr�fico
				$coluna_para_ocultar='4';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'1,1,0,0;2,2,0,0',
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media",
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
			
			$this->load->model('indicador_atendimento/senhas_liberadas_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->senhas_liberadas_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{
				echo "N�o foi identificado per�odo aberto para o Indicador";
			}
			else
			{
				$contador = sizeof($collection);
				$media_ano=array();

                $soma_total = 0;
                
				foreach( $collection as $item )
				{
					$nr_meta = $item["nr_meta"];

                    $nr_valor_1 = $item["nr_valor_1"];
                    $nr_valor_2 = $item["nr_valor_2"];

                    $nr_percentual_f = $item['nr_percentual_f'];

					if( $item['fl_media']=='S' )
					{
						$referencia = " Total de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];

					}

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
					{
						$media_ano[] = $nr_percentual_f;
					}
				}

				$sql="";

				if(sizeof($media_ano)>0)
				{
					
					$sql.=sprintf("
                        INSERT INTO indicador_atendimento.senhas_liberadas
					              (
                                   dt_referencia,
                                   dt_inclusao,
                                   cd_usuario_inclusao,
                                   nr_valor_1,
                                   nr_valor_2,
                                   nr_percentual_f,
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
                                   'S'
                                  ); "
                        , intval($tabela[0]['nr_ano_referencia'])
                        , usuario_id()
                        , floatval($nr_valor_1)
                        , floatval($nr_valor_2)
                        , floatval($nr_percentual_f)
                   );
				}

				// indicar que o per�odo foi fechado para o indicador_tabela
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

		redirect( 'indicador_atendimento/senhas_liberadas' );
		// echo 'per�odo encerrado com sucesso';
	}
}
?>