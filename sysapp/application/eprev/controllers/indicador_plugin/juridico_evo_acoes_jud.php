<?php
class juridico_evo_acoes_jud extends Controller
{
	var $enum_indicador = 0;
	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::JURIDICO_EVOLUCAO_DAS_ACOES_JUDICIAIS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/juridico_evo_acoes_jud_model' );
    }	
	
	function index()
    {
		if((indicador_db::verificar_permissao(usuario_id(), 'AJ')) OR (indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$args   = array();
			$data   = array();
			$result = null;
		
			#### FECHA PERIODO ENCERRADO PARA ABRIR NOVO ####
			$ar_periodo = indicador_periodo_aberto();
			$ar_tabela  = indicador_tabela_aberta(intval($this->enum_indicador));
			if(intval($ar_periodo[0]["cd_indicador_periodo"]) != intval($ar_tabela[0]["cd_indicador_periodo"]))
			{
				$qr_sql = indicador_db::fechar_periodo_para_indicador(intval($ar_tabela[0]["cd_indicador_tabela"]), $this->session->userdata('codigo'));
				$this->db->query($qr_sql);
			}
		
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/juridico_evo_acoes_jud/index',$data);
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
		if(CheckLogin() AND (indicador_db::verificar_permissao(usuario_id(), 'AJ') OR indicador_db::verificar_permissao(usuario_id(), 'GP')))
        {
	        $this->load->model( 'indicador_plugin/juridico_evo_acoes_jud_model' );

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

				$this->juridico_evo_acoes_jud_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/juridico_evo_acoes_jud/partial_result', $data);
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
        $data['label_5'] = $this->label_5;
		
		$this->load->helper( array('indicador') );
		if(CheckLogin() AND (indicador_db::verificar_permissao(usuario_id(), 'AJ') OR indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
            $data['CD_INDICADOR'] = $this->enum_indicador;
			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_plugin/juridico_evo_acoes_jud_model');
			$row=$this->juridico_evo_acoes_jud_model->carregar( $cd );
			if($row)
			{
				if($cd==0)
				{
					$sql = "
					SELECT to_char(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, dt_referencia, nr_meta, cd_indicador_tabela
					FROM indicador_plugin.juridico_evo_acoes_jud
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

			$this->load->view('indicador_plugin/juridico_evo_acoes_jud/detalhe', $data);
		}
	}

	function salvar()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() AND (indicador_db::verificar_permissao(usuario_id(), 'AJ') OR indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$this->load->model('indicador_plugin/juridico_evo_acoes_jud_model');
			
			$args['cd_juridico_evo_acoes_jud']=intval($this->input->post('cd_juridico_evo_acoes_jud', true));
			$args["dt_referencia"] = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"] = usuario_id();
			$args["cd_indicador_tabela"] = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"] = $this->input->post("fl_media", true);

			$args["nr_valor_1"] = app_decimal_para_db($this->input->post("nr_valor_1", true));
			$args["nr_valor_2"] = app_decimal_para_db($this->input->post("nr_valor_2", true));
            $args["nr_valor_3"] = app_decimal_para_db($this->input->post("nr_valor_3", true));
			$args["nr_meta"] = app_decimal_para_db($this->input->post("nr_meta", true));
            $args["observacao"] = $this->input->post("observacao", true);

			$msg=array();
			$retorno = $this->juridico_evo_acoes_jud_model->salvar( $args,$msg );
			
			if($retorno)
			{
				
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_plugin/juridico_evo_acoes_jud", "refresh" );
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
		if(CheckLogin() AND (indicador_db::verificar_permissao(usuario_id(), 'AJ') OR indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$this->load->model('indicador_plugin/juridico_evo_acoes_jud_model');

			$this->juridico_evo_acoes_jud_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_plugin/juridico_evo_acoes_jud', 'refresh' );
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
		if(CheckLogin() AND (indicador_db::verificar_permissao(usuario_id(), 'AJ') OR indicador_db::verificar_permissao(usuario_id(), 'GP')))
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

				$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');

				$this->load->model('indicador_plugin/juridico_evo_acoes_jud_model');
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela']; // usar a tabela aberta do periodo mais antigo
				$this->juridico_evo_acoes_jud_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
				$linha=0;

				$contador = sizeof($collection);
				$media_ano=array();
                $nr_valor_ant = 0;
                $nr_valor_f1 = 0;
                $nr_percentual_f = 0;
				foreach( $collection as $item )
				{
					// hist�rio de 5 anos atr�s
					#if( intval($item['ano_referencia'])>=intval($tabela[0]['nr_ano_referencia'])-5 )
					if(true)
					{
						$nr_meta = $item["nr_meta"];
                        $observacao = $item["observacao"];

						if( $item['fl_media']=='S' )
						{
							$referencia = " M�dia de " . $item['ano_referencia'];

							$nr_valor_1 = '';
							$nr_valor_f1 = '';
							$nr_percentual_f = $item['nr_percentual_f'];
						}
						else
						{
							$referencia = $item['ano_referencia'];

							$nr_valor_1 = $item["nr_valor_1"];
							$nr_valor_ant = $nr_valor_f1;
                            $nr_valor_f1 += $nr_valor_1;

                            if($nr_valor_1 != $nr_valor_f1){
                                $nr_percentual_f =((floatval($nr_valor_f1)/floatval($nr_valor_ant)-1)*100);
                            }
							
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_percentual_f;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						$indicador[$linha][2] = app_decimal_para_php($nr_valor_f1);
						$ar_tendencia[] = $nr_valor_1;
						$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
                        $indicador[$linha][5] = $observacao;
    
						$linha++;
					}
				}

				// LINHA DE TEND�NCIA - CURVA LOGARITMICA
				list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
				for($i=0;$i<sizeof($ar_tendencia);$i++)
				{
					$indicador[$i][4] = ($i == 0 ? 0 : $tend[$i]);
				}

				$linha_sem_media = $linha;


				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'left');

					$linha++;
				}

				// gerar gr�fico
				$coluna_para_ocultar='4';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::BARRA_MULTIPLO,
					'1,1,0,0;4,4,0,0',
					"0,0,1,$linha",
					"1,1,1,$linha;4,4,1,$linha-linha",
					usuario_id(),
					$coluna_para_ocultar,
                    -1,
                    1
				);

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}

				return true;
				/*echo "Indicador atualizado com sucesso;".br(2);

				echo anchor( "indicador/apresentacao/detalhe/".intval( $tabela[0]['cd_indicador_tabela'] ), 'Exibir apresenta��o' );*/

			} #tabela_existe
		}
	}

	function fechar_periodo()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() AND (indicador_db::verificar_permissao(usuario_id(), 'AJ') OR indicador_db::verificar_permissao(usuario_id(), 'GP')))
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_plugin/juridico_evo_acoes_jud_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->juridico_evo_acoes_jud_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "N�o foi identificado per�odo aberto para o Indicador";
				exit;

			}#tabela_existe

			else
			{#tabela_existe

				/*
				$contador = sizeof($collection);
				$media_ano=array();
				foreach( $collection as $item )
				{
					$nr_meta = $item["nr_meta"];

					if( $item['fl_media']=='S' )
					{
						$referencia = " M�dia de " . $item['ano_referencia'];

						$nr_valor_1 = '';
						$nr_valor_2 = '';
						$nr_percentual_f = $item['nr_percentual_f'];
					}
					else
					{
						$referencia = $item['mes_referencia'];

						$nr_valor_1 = $item["nr_valor_1"];
						$nr_valor_2 = $item["nr_valor_2"];
						
						if( floatval($nr_valor_1)>0 )
						{
							$nr_percentual_f = ( (floatval($nr_valor_1)/floatval($nr_valor_2)) -1 )*100;
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

				$sql="";

				// gravar a m�dia do per�odo
				if(sizeof($media_ano)>0)
				{
					$media = 0;

					foreach( $media_ano as $valor )
					{
						$media += $valor;
					}

					$media = ( $media / sizeof($media_ano) );

					$sql.=sprintf(" INSERT INTO indicador_plugin.juridico_evo_acoes_jud
					( dt_referencia,dt_inclusao,cd_usuario_inclusao, nr_percentual_f, nr_meta, fl_media ) 
					VALUES ( '%s/01/01',current_timestamp,%s, %s, %s, 'S' ); "
					, intval($tabela[0]['nr_ano_referencia']) , usuario_id(), floatval($media), floatval( app_decimal_para_db($nr_meta) ));
				}

				// indicar que o per�odo foi fechado para o indicador_tabela
				$sql.=sprintf( " UPDATE indicador.indicador_tabela SET dt_fechamento_periodo = current_timestamp, cd_usuario_fechamento_periodo=%s WHERE cd_indicador_tabela=%s; "
					,intval(usuario_id())
					,intval($tabela[0]['cd_indicador_tabela']) );

				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}
				*/
				
			
				
				/*
				// indicar que o per�odo foi fechado para o indicador_tabela
				$sql=" 
						INSERT INTO indicador_plugin.juridico_evo_acoes_jud
							 ( 
							   dt_referencia,
							   dt_inclusao,
							   cd_usuario_inclusao, 
							   nr_valor_1, 
							   nr_valor_2, 
							   nr_percentual_f, 
							   nr_meta, 
							   fl_media 
							 ) 	
							 (
								SELECT aep.dt_referencia,
									   CURRENT_TIMESTAMP,
									   ".usuario_id().", 
									   aep.nr_valor_1,
									   aep.nr_valor_2,
									   (((nr_valor_1/nr_valor_2)-1) * 100) AS nr_percentual_f, 
									   aep.nr_meta,
									   'S'
								  FROM indicador_plugin.juridico_evo_acoes_jud aep
								 WHERE aep.dt_exclusao IS NULL
                                   AND aep.cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."													  
								   AND aep.dt_referencia = (SELECT MAX(eap1.dt_referencia) 
															  FROM indicador_plugin.juridico_evo_acoes_jud eap1
															 WHERE eap1.cd_indicador_tabela = aep.cd_indicador_tabela 
															   AND eap1.dt_exclusao IS NULL)
							 );
				
						UPDATE indicador.indicador_tabela 
						   SET dt_fechamento_periodo = CURRENT_TIMESTAMP, 
						 	   cd_usuario_fechamento_periodo=".intval(usuario_id())."
						 WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; 
					  ";
				*/
				// executar comandos
				if(trim($sql)!=''){$this->db->query($sql);}				
				
			} #tabela_existe
		}

		redirect( 'indicador_plugin/juridico_evo_acoes_jud' );
		// echo 'per�odo encerrado com sucesso';
	}
}
?>