<?php
class investimento_pi extends Controller
{
    var	$label_0 = "Mês";
	var	$label_1 = "% de Enquadramento";
    var $label_2 = "Meta";
    var $label_3 = "Observação";

	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::INVESTIMENTO_ENQUADRAMENTO_POLITICA_INVESTIMENTOS);
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

            $data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador) );

	        $this->load->view('indicador_plugin/investimento_pi/index.php');
		}
    }

    function listar()
    {
        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
        $data['label_2'] = $this->label_2;
        $data['label_3'] = $this->label_3;

        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
        {
	        $this->load->model( 'indicador_plugin/Investimento_pi_model' );

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

				$this->Investimento_pi_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view( 'indicador_plugin/investimento_pi/partial_result', $data );
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

        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{

			$data['CD_INDICADOR'] = $this->enum_indicador;
            $tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/Investimento_pi_model');
			$row=$this->Investimento_pi_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_meta, cd_indicador_tabela
					FROM indicador_plugin.investimento_pi 
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

			$this->load->view('indicador_plugin/investimento_pi/detalhe', $data);
		}
	}

	function salvar()
	{
        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
			$this->load->model('indicador_plugin/Investimento_pi_model');
			
			$args['cd_investimento_pi']=intval($this->input->post('cd_investimento_pi', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));

			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->Investimento_pi_model->salvar( $args,$msg );
			
			if($retorno)
			{
                if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/investimento_pi", "refresh" );
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
			$this->load->model('indicador_plugin/Investimento_pi_model');

			$this->Investimento_pi_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/investimento_pi", "refresh" );
			}	
		}
	}

	function criar_indicador()
	{
        $data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
        $data['label_2'] = $this->label_2;
        $data['label_3'] = $this->label_3;

        $this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GC' ) )
		{
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

				$this->load->model('indicador_plugin/Investimento_pi_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->Investimento_pi_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$media_ano = array();
				$a_data = array(0, 0);
				$nr_acumulado_anterior = 0;
				foreach( $collection as $item )
				{
					// exibir apenas 5 anos de histórico
					if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					{
						$nr_meta = floatval($item["nr_meta"]);
						$nr_valor_1 = floatval($item["nr_valor_1"]);
                        $observacao = $item["observacao"];

						if( $item['fl_media']=='S' )
						{
							$referencia = $item['ano_referencia'];
						}
						else
						{
							$referencia = $item['mes_referencia'];
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = $nr_valor_1;
						$indicador[$linha][2] = $nr_meta;
                        $indicador[$linha][3] = $observacao;

						$linha++;
					}
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode($indicador[$i][3]), 'left');

					$linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'1,1,0,0;2,2,0,0',
					"0,0,1,$linha",
					"1,1,1,".$linha.";2,2,1,".$linha."",
					usuario_id(),
					$coluna_para_ocultar,
                    1
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
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));

			$this->load->model('indicador_plugin/Investimento_pi_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->Investimento_pi_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$contador = sizeof( $collection );
				$nr_acumulado_anterior = 0;
				$item = $collection[sizeof($collection)-1]; // USAR VALOR DO ULTIMO REGISTRO PRA GRAVAR O TOTAL PARA O ANO

				$referencia = $item['mes_referencia'];

				$nr_valor_1 = floatval($item["nr_valor_1"]);
				$nr_meta = floatval($item["nr_meta"]);

				$sql="";

				// gravar o acumulado do ano
				$sql .= sprintf(" INSERT INTO indicador_plugin.investimento_pi
				( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_valor_1, nr_meta, fl_media )
				VALUES ( '%s/01/01',current_timestamp, %s, %s, %s, 'S' ); "
				, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($nr_valor_1), floatval($nr_meta) );

				// indicar que o período foi fechado para o 'indicador_tabela'
				$sql.=sprintf( "	UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s
									WHERE cd_indicador_tabela=%s;  "
					, intval(usuario_id())
					, intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){ $this->db->query($sql); }

				//echo $sql;

			} #tabela_existe
		}

		redirect( 'indicador_plugin/investimento_pi' );
	}
}
?>