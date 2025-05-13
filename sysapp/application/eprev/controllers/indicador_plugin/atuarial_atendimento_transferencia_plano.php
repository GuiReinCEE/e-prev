<?php
class Atuarial_atendimento_transferencia_plano extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::ATUARIAL_ATENDIMENTO_PRAZO_TRANSFERENCIA_PLANO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		$this->cd_usuario = $this->session->userdata('codigo');	

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}		
		
		$this->load->model('indicador_plugin/atuarial_atendimento_transferencia_plano_model');
    }

    function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id()))
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/atuarial_atendimento_transferencia_plano/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
        {
		 	$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_6'] = $this->label_6;

			$data['tabela']     = indicador_tabela_aberta($this->enum_indicador);	
			
			$data['collection'] = $this->atuarial_atendimento_transferencia_plano_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/atuarial_atendimento_transferencia_plano/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function cadastro($cd_atuarial_atendimento_transferencia_plano = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->load->model('indicador_plugin/atuarial_atendimento_transferencia_plano_model');
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			$data['drop']   = array(
                array('value' => '01', 'text' => '01'), 
                array('value' => '02', 'text' => '02'), 
                array('value' => '03', 'text' => '03'), 
                array('value' => '04', 'text' => '04')
            );
			
			if(intval($cd_atuarial_atendimento_transferencia_plano) == 0)
			{
				$row = $this->atuarial_atendimento_transferencia_plano_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));

				$data['row'] = array(
					'cd_atuarial_atendimento_transferencia_plano' => 0,
					'fl_media' 								  => '',
					'ds_observacao' 						  => '',
					'nr_tarefas' 							  => '',
					'nr_realizadas' 						  => '',
					'ano_referencia'         		          => (isset($row['ds_ano_referencia_n']) ? trim($row['ds_ano_referencia_n']) : ''),
                    'mes_referencia'         		          => (isset($row['ds_mes_referencia_n']) ? trim($row['ds_mes_referencia_n']) : ''),
					'dt_referencia' 						  => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ""),
					'nr_meta' 								  => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->atuarial_atendimento_transferencia_plano_model->carrega($cd_atuarial_atendimento_transferencia_plano);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/atuarial_atendimento_transferencia_plano/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->load->model('indicador_plugin/atuarial_atendimento_transferencia_plano_model');

			$cd_atuarial_atendimento_transferencia_plano = $this->input->post('cd_atuarial_atendimento_transferencia_plano', true);
			
			$nr_tarefas    = app_decimal_para_db($this->input->post('nr_tarefas', true));
			$nr_realizadas = app_decimal_para_db($this->input->post('nr_realizadas', true));
								
			$nr_resultado  = 100;
			
			if(floatval($nr_tarefas) > 0)
			{
				$nr_resultado  = ($nr_realizadas / $nr_tarefas) * 100;
			}
			
			$args = array(
				'cd_indicador_tabela' => $this->input->post("cd_indicador_tabela", TRUE),
				'dt_referencia'       => $this->input->post("dt_referencia", TRUE),
				'fl_media' 			  => $this->input->post("fl_media", TRUE),
				'nr_tarefas' 		  => $nr_tarefas,
				'nr_realizadas' 	  => $nr_realizadas,
				'nr_resultado' 	      => $nr_resultado,
				'nr_meta' 			  => app_decimal_para_db($this->input->post("nr_meta", TRUE)),
				'ds_observacao' 	  => $this->input->post("ds_observacao", TRUE),
				'cd_usuario' 		  => $this->session->userdata('codigo')
			);

			if(intval($cd_atuarial_atendimento_transferencia_plano) == 0)
			{
				$this->atuarial_atendimento_transferencia_plano_model->salvar($args);
			}
			else
			{
				$this->atuarial_atendimento_transferencia_plano_model->atualizar($cd_atuarial_atendimento_transferencia_plano, $args);
			}
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/atuarial_atendimento_transferencia_plano", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function excluir($cd_atuarial_atendimento_transferencia_plano)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->load->model('indicador_plugin/atuarial_atendimento_transferencia_plano_model');
			
			$this->atuarial_atendimento_transferencia_plano_model->excluir($cd_atuarial_atendimento_transferencia_plano, $this->session->userdata('codigo'));
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/atuarial_atendimento_transferencia_plano", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->load->model('indicador_plugin/atuarial_atendimento_transferencia_plano_model');
		
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela']).";";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			
			$collection = $this->atuarial_atendimento_transferencia_plano_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;
			
			$nr_tarefas_result     = 0;
			$nr_realizadas_result  = 0;
			$nr_meta_result        = 0;
			$nr_resultado_result   = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$ds_observacao = $item["ds_observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = " Média de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					$nr_tarefas    = $item["nr_tarefas"];
					$nr_realizadas = $item["nr_realizadas"];
					$nr_resultado  = $item['nr_resultado'];
					$nr_meta       = $item["nr_meta"];

					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$media_ano[] = $nr_resultado;
						$contador_ano_atual++;
						
						$nr_tarefas_result     += $item["nr_tarefas"];
						$nr_realizadas_result  += $item["nr_realizadas"];
						$nr_meta_result        = $item["nr_meta"];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($nr_tarefas);
					$indicador[$linha][2] = app_decimal_para_php($nr_realizadas);
					$indicador[$linha][3] = app_decimal_para_php($nr_resultado);
					$indicador[$linha][4] = app_decimal_para_php($nr_meta);
					$indicador[$linha][6] = $ds_observacao;

					$ar_tendencia[] = $nr_resultado;
					
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

			if(intval($contador_ano_atual) > 0)
			{
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}
			
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';

				$linha++;
				
				$nr_resultado_result  = 100;
			
				if(floatval($nr_tarefas_result) > 0)
				{
					$nr_resultado_result  = ($nr_realizadas_result / $nr_tarefas_result) * 100;
				}

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_tarefas_result;
				$indicador[$linha][2] = $nr_realizadas_result;
				$indicador[$linha][3] = $nr_resultado_result;
				$indicador[$linha][4] = $nr_meta_result;
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]) );
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='5';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media-linha",
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->load->model('indicador_plugin/atuarial_atendimento_transferencia_plano_model');
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
	        $collection = $this->atuarial_atendimento_transferencia_plano_model->listar($tabela[0]['cd_indicador_tabela']);

			$media_ano            = array();
			$ar_tendencia         = array();
			$nr_meta              = 0;
			$contador_ano_atual   = 0;
			$media                = 0;
			
			$nr_tarefas_result     = 0;
			$nr_realizadas_result  = 0;
			$nr_meta_result        = 0;
			$nr_resultado_result   = 0;
			
			foreach( $collection as $item )
			{			 
				if($item['fl_media'] != 'S' )
				{
					$referencia = $item['mes_referencia'];
				}

				if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
				{
					$contador_ano_atual++;
					
					$nr_tarefas_result     += $item["nr_tarefas"];
					$nr_realizadas_result  += $item["nr_realizadas"];
					$nr_meta_result        = $item["nr_meta"];
				}
			}

			// gravar a média do período
			if(intval($contador_ano_atual) > 0)
			{
				$nr_resultado_result  = 100;
			
				if(floatval($nr_tarefas_result) > 0)
				{
					$nr_resultado_result  = ($nr_realizadas_result / $nr_tarefas_result) * 100;
				}
				
				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia' 	  => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media' 			  => 'S',
					'nr_tarefas' 		  => $nr_tarefas_result,
					'nr_realizadas' 	  => $nr_realizadas_result,
					'nr_resultado_result' => $nr_resultado_result,
					'nr_meta' 			  => $nr_meta_result,
					'ds_observacao' 	  => '',
					'cd_usuario' 		  => $this->session->userdata('codigo')
				);

				$this->atuarial_atendimento_transferencia_plano_model->salvar($args);
			}

			$this->atuarial_atendimento_transferencia_plano_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->session->userdata('codigo'));

			redirect("indicador_plugin/atuarial_atendimento_transferencia_plano", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}
}
?>