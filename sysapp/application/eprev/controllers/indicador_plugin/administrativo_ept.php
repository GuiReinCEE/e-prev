<?php
class administrativo_ept extends Controller
{
    var	$label_0 = "M�s";
	var	$label_1 = "Eventos efetivados (acumulado)";
	var	$label_2 = "Eventos previstos (acumulado)";
	var	$label_3 = "% execu��o do Plano de Treinamento";
	var	$label_4 = "Meta";
    var $label_5 = "Observa��o";

    var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::RH_EXECUCAO_PLANO_TREINAMENTO);
    }

    function index()
    {
        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			// VERIFICA SE EXISTE TABELA NO PER�ODO ABERTO, SE N�O EXISTIR, CRIAR TABELA NO PER�ODO QUE ESTIVER ABERTO
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

	        $this->load->view('indicador_plugin/administrativo_ept/index.php');
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

        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
        {
	        $this->load->model( 'indicador_plugin/Administrativo_ept_model' );

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

				$this->Administrativo_ept_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/administrativo_ept/partial_result', $data);
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
		$data['label_4'] = $this->label_4;
        $data['label_5'] = $this->label_5;

        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->helper( array('indicador') );

            $tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$data['CD_INDICADOR'] = $this->enum_indicador;

			$this->load->model('indicador_plugin/Administrativo_ept_model');
			$row=$this->Administrativo_ept_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_valor_2, nr_meta, cd_indicador_tabela
					FROM indicador_plugin.administrativo_ept 
					WHERE dt_exclusao IS NULL 
					ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
						$row['dt_referencia'] = $row_atual['mes_referencia'];
						$row['nr_valor_2'] = $row_atual['nr_valor_2'];
						$row['nr_meta'] = $row_atual['nr_meta'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_plugin/administrativo_ept/detalhe', $data);
		}
	}

	function salvar()
	{
        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->model('indicador_plugin/Administrativo_ept_model');
			
			$args['cd_administrativo_ept']=intval($this->input->post('cd_administrativo_ept', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"] = app_decimal_para_db($this->input->post("nr_valor_2", true));
			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->Administrativo_ept_model->salvar( $args,$msg );
			
			if($retorno)
			{
                if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/administrativo_ept", "refresh" );
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
			$this->load->model('indicador_plugin/Administrativo_ept_model');

			$this->Administrativo_ept_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/administrativo_ept", "refresh" );
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->helper( array('indicador') );

			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			if(sizeof($tabela)<=0)
			{#tabela_existe

                return false;
				#echo "N�o foi identificado per�odo aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$sql=" DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');

				$this->load->model('indicador_plugin/Administrativo_ept_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->Administrativo_ept_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$media_ano=array();
				$a_data=array(0, 0);
				foreach( $collection as $item )
				{
					// exibir apenas 5 anos de hist�rico
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					{
						$nr_meta = $item["nr_meta"];

						if( $item['fl_media']=='S' )
						{
							$referencia = " Acum. " . $item['ano_referencia'];

							$nr_valor_1= '';
							$nr_valor_2= '';
                            $observacao = '';
							$nr_percentual_f = $item['nr_percentual_f']; // valor da m�dia dos anos anteriores � gravada nessa coluna quando o per�odo � fechado
						}
						else
						{
							$referencia = $item['mes_referencia'];

							$nr_valor_1 = $item["nr_valor_1"];
							$nr_valor_2 = $item["nr_valor_2"];
                            $observacao = $item["observacao"];

							$nr_percentual_f = ( floatval($nr_valor_1)/floatval($nr_valor_2) )*100;
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media']!='S' )
						{
							$media_ano[] = $nr_percentual_f;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
						$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
						$indicador[$linha][4] = app_decimal_para_php($nr_meta);
                        $indicador[$linha][5] = $observacao;

						$linha++;
					}
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'left');

					$linha++;
				}

				// gerar gr�fico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::BARRA_MULTIPLO,
					'3,3,0,0;4,4,0,0',
					"0,0,1,$linha",
					"3,3,1,$linha;4,4,1,$linha",
					usuario_id(),
					$coluna_para_ocultar,
                    1
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

                return true;
				#echo "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresenta��o' );

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
			
			$this->load->model('indicador_plugin/Administrativo_ept_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->Administrativo_ept_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "N�o foi identificado per�odo aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$item = $collection[sizeof($collection)-1];
				if($item)
				{
					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					$nr_meta = $item["nr_meta"];
					$nr_percentual_f = (floatval($nr_valor_1)/floatval($nr_valor_2))*100;

					$sql="";

					// gravar o acumulado do ano
					if( floatval($nr_percentual_f)>0 )
					{
						$sql .= sprintf(" INSERT INTO indicador_plugin.administrativo_ept
						( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_percentual_f, nr_meta, fl_media )
						VALUES ( '%s/01/01',current_timestamp,%s, %s, %s, 'S' ); "
						, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($nr_percentual_f), floatval($nr_meta) );
					}

					// indicar que o per�odo foi fechado para o 'indicador_tabela'
					$sql.=sprintf( "	UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s
										WHERE cd_indicador_tabela=%s; "
						, intval(usuario_id())
						, intval($tabela[0]['cd_indicador_tabela']) );

					// executar comandos
					if(trim($sql)!=''){ $this->db->query($sql); }
				}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/administrativo_ept' );
	}
}
?>