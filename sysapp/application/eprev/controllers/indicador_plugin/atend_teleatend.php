<?php
class atend_teleatend extends Controller
{
	var	$label_0 = "Mês";
	var	$label_1 = "Ligações recebidas 0800";
    var $label_2 = "Observação";

	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_TOTAL_DE_LIGACOES_RECEBIDAS_NO_0800);
    }

    function index()
    {
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
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

	        $this->load->view('indicador_plugin/atend_teleatend/index.php',$data);
		}
    }

    function listar()
    {
		$data['label_0'] = $this->label_0;
        $data['label_1'] = $this->label_1;
        $data['label_2'] = $this->label_2;

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
        {
	        $this->load->model( 'indicador_plugin/Atend_teleatend_model' );

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

				$this->Atend_teleatend_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/atend_teleatend/partial_result', $data);
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
		//$data['label_2'] = "Nº de ligações transferidas";
		//$data['label_3'] = "Percentual de ligações";

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{

			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/Atend_teleatend_model');
			$row=$this->Atend_teleatend_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, cd_indicador_tabela
					FROM indicador_plugin.atend_teleatend 
					WHERE dt_exclusao IS NULL 
					ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
						$row['dt_referencia'] = $row_atual['mes_referencia'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_plugin/atend_teleatend/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{
			$this->load->model('indicador_plugin/Atend_teleatend_model');
			
			$args['cd_atend_teleatend']=intval($this->input->post('cd_atend_teleatend', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);
            $args["observacao"] = $this->input->post("observacao", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));

			$msg=array();
			$retorno = $this->Atend_teleatend_model->salvar( $args,$msg );
			
			if($retorno)
			{
                if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/atend_teleatend", "refresh" );
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{
			$this->load->model('indicador_plugin/Atend_teleatend_model');

			$this->Atend_teleatend_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( "indicador_plugin/atend_teleatend", "refresh" );
			}
		}
	}

	function criar_indicador()
	{
		$data['label_0'] = $this->label_0;
        $data['label_1'] = $this->label_1;
        $data['label_2'] = $this->label_2;

		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
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
                
				$this->load->model('indicador_plugin/Atend_teleatend_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->Atend_teleatend_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$total=0;
				foreach( $collection as $item )
				{
                    $observacao = $item["observacao"];
					// histório de 5 anos atrás
					if( intval($item['ano_referencia']) ==intval($tabela[0]['nr_ano_referencia']) )
					{
						$nr_valor_1 = $item["nr_valor_1"];
						
						$total+=floatval($nr_valor_1);

						$referencia = $item['mes_referencia'];

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
                        $indicador[$linha][2] = $observacao;

						$linha++;
					}
				}

				$linha_sem_media = $linha;

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
                $indicador[$linha][2] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Total de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $total;
                $indicador[$linha][2] = '';

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode($indicador[$i][2]), 'left');

					$linha++;
				}

				// gerar gráfico
				$coluna_para_ocultar='';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					"1,1,0,0",
					"0,0,1,$linha_sem_media",
					"1,1,1,$linha_sem_media",
					usuario_id(),
					$coluna_para_ocultar
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
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GP' ) )
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/Atend_teleatend_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->Atend_teleatend_model->listar( $result, $args );
			$ar_atend_teleatend = $result->result_array();

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe

				$total=0;
				foreach( $ar_atend_teleatend as $item )
				{
					$total+=floatval($item['nr_valor_1']);
				}
	
				/*
				$contador = sizeof($collection);
				$media_ano=array();
				foreach( $collection as $item )
				{
					$referencia = $item['mes_referencia'];

					$nr_valor_1 = $item["nr_valor_1"];
				}

				$sql="";

				// indicar que o período foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}
				*/
				
				
				if(count($ar_atend_teleatend) >= 12)
				{
					$sql="
							INSERT INTO indicador_plugin.atend_teleatend
								 (
									dt_referencia, 
									cd_usuario_inclusao, 
									fl_media, 
									nr_valor_1
								 )
							VALUES 
								 (
									TO_DATE('".intval($tabela[0]['nr_ano_referencia'])."-12-01','YYYY-MM-DD'), 
									".usuario_id().", 
									'S',
									".floatval($total)."
								 );
						";

					// indicar que o período foi fechado para o indicador_tabela
					$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
						,intval(usuario_id())
						,intval($tabela[0]['cd_indicador_tabela']) );

					// executar comandos
					if(trim($sql)!=''){$this->db->query($sql);}
				}				

			} #tabela_existe
		}

		redirect( 'indicador_plugin/atend_teleatend' );
		// echo 'período encerrado com sucesso';
	}
}
?>