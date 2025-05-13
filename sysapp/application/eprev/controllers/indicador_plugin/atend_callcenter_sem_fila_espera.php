<?php
class atend_callcenter_sem_fila_espera extends Controller
{
	/*
    var	$label_0 = "Mês/Ano";
	var	$label_1 = "Ligações Atendidas S/ Fila";
	var	$label_2 = "Ligaçoes Atendidas";
	var	$label_3 = "% Lig. Atendidas S/ Fila";
	var	$label_4 = "Observação";
	var	$label_5 = "Tendência";
	*/
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_CALL_CENTER_ATENDIMENTO_SEM_FILA);
		
		$this->load->helper(array('indicador'));
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}
		
		$this->load->model( 'indicador_plugin/atend_callcenter_sem_fila_espera_model' );
    }

    function index()
    {
		if(indicador_db::verificar_permissao(usuario_id(), 'GCM' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$fl_novo_periodo = indicador_db::abrir_periodo_para_indicador($this->enum_indicador, usuario_id());

			if($fl_novo_periodo)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
			}

			$data['tabela'] = indicador_tabela_aberta( intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/atend_callcenter_sem_fila_espera/index',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function listar()
    {
		if(indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
        {
	        $args = Array();
			$data = Array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(sizeof($data['tabela'])>0)
			{
				$args['cd_indicador_tabela'] = $data['tabela'][0]['cd_indicador_tabela'];

				$this->atend_callcenter_sem_fila_espera_model->listar( $result, $args );
				$data['collection'] = $result->result_array();

				$this->load->view('indicador_plugin/atend_callcenter_sem_fila_espera/partial_result', $data);
			}
			else
			{
				exibir_mensagem("Nenhum período aberto para o indicador.");
			}        
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function cadastro($cd_atend_callcenter_sem_fila_espera = 0)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GCM' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			$args['cd_atend_callcenter_sem_fila_espera'] = $cd_atend_callcenter_sem_fila_espera;
			
			$this->atend_callcenter_sem_fila_espera_model->carregar($result, $args);
			$data['row'] = $result->row_array();

			if(intval($args['cd_atend_callcenter_sem_fila_espera']) == 0)
			{
				$this->atend_callcenter_sem_fila_espera_model->referencia( $result, $args );
				$row_atual = $result->row_array();					
				
				if($row_atual)
				{
					$data['row']['dt_referencia']       = $row_atual['mes_referencia'];
					$data['row']['nr_meta']             = $row_atual['nr_meta'];
					$data['row']['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
				}
				
				$data['row']['cd_atend_callcenter_sem_fila_espera'] = 0;
				$data['row']['nr_ligacao_sem_fila'] = "";
				$data['row']['nr_ligacao_atendida'] = "";
				$data['row']['observacao'] = "";
			}

			$this->load->view('indicador_plugin/atend_callcenter_sem_fila_espera/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GCM' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$args["cd_atend_callcenter_sem_fila_espera"] = $this->input->post("cd_atend_callcenter_sem_fila_espera", true);
			$args["cd_indicador_tabela"]                 = $this->input->post("cd_indicador_tabela", true);
			$args["dt_referencia"]                       = '01/'.$this->input->post("mes_referencia", true).'/'.$this->input->post("ano_referencia", true);
			$args["nr_ligacao_sem_fila"]                 = $this->input->post("nr_ligacao_sem_fila", true);
			$args["nr_ligacao_atendida"]                 = $this->input->post("nr_ligacao_atendida", true);
			$args["nr_meta"]                             = app_decimal_para_db($this->input->post("nr_meta", true));
			$args["observacao"]                          = $this->input->post("observacao", true);
			$args["cd_usuario"]                          = $this->session->userdata('codigo');
            
			$this->atend_callcenter_sem_fila_espera_model->salvar( $result, $args );
			
			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect("indicador_plugin/atend_callcenter_sem_fila_espera", "refresh");
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function excluir($cd_atend_callcenter_sem_fila_espera)
	{
		if(indicador_db::verificar_permissao(usuario_id(),'GCM' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
		
			$args['cd_atend_callcenter_sem_fila_espera'] = $cd_atend_callcenter_sem_fila_espera;
			$args['cd_usuario']                          = $this->session->userdata('codigo');
			
			$this->atend_callcenter_sem_fila_espera_model->excluir( $result, $args );

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect("indicador_plugin/atend_callcenter_sem_fila_espera", "refresh");
			}	
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao(usuario_id(), 'GCM' ))
		{
			$args = Array();
			$data = Array();
			$result = null;
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
		
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			if(sizeof($tabela)<=0)
			{
                return false;
			}
			else
			{

				$sql = "
					DELETE 
					  FROM indicador.indicador_parametro 
					 WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; ";

				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_6']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');

				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
				
				$this->atend_callcenter_sem_fila_espera_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador = array();
				$linha     = 0;
				$contador  = sizeof($collection);
				$media_ano = array();
				
				foreach( $collection as $item )
				{
					if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - 5)
					{
						if( $item['fl_media']=='S' )
						{
							$referencia = " Média de " . $item['ano_referencia'];

							$nr_valor_1 = '';
							$nr_valor_2 = '';
							$nr_meta    = $item["nr_meta"];
						}
						else
						{
							$referencia = $item['ano_mes_referencia'];

							$nr_valor_1 = $item["nr_ligacao_sem_fila"];
							$nr_valor_2 = $item["nr_ligacao_atendida"];
							$nr_meta    = $item["nr_meta"];
						}

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $item['nr_ligacao_atendida_percentual'];
						}
						
						$ar_tendencia[] = $item['nr_ligacao_atendida_percentual'];
						
						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
						$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
						$indicador[$linha][3] = app_decimal_para_php($item['nr_ligacao_atendida_percentual']);
						$indicador[$linha][4] = $item["observacao"];
						$indicador[$linha][6] = app_decimal_para_php($nr_meta);

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

					$indicador[$linha][0] = '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
					$indicador[$linha][1] = '';
					$indicador[$linha][2] = '';
					$indicador[$linha][3] = $media;
					$indicador[$linha][4] = "";
					$indicador[$linha][5] = '';
                    $indicador[$linha][6] = number_format($nr_meta,2);
				}

				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][4]), 'left');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
                    

					$linha++;
				}

				$coluna_para_ocultar='6';
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

				if(trim($sql) != '')
				{
					$this->db->query($sql);
				}
				
                return true;
			}
		}
		else
		{
			return false;
		}
	}

	function fechar_periodo()
	{
		$this->load->helper( array('indicador') );
		if(CheckLogin() && indicador_db::verificar_permissao( usuario_id(), 'GCM' ) )
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$this->load->model('indicador_plugin/atend_callcenter_sem_fila_espera_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->atend_callcenter_sem_fila_espera_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{#tabela_existe

				echo "Não foi identificado período aberto para o Indicador";

			}#tabela_existe

			else
			{#tabela_existe
				$contador_ano_atual = 0;
				$contador           = sizeof($collection);
				$media_ano          = array();
				$a_data             = array(0, 0);			
			
				foreach( $collection as $item )
				{
					$a_data = explode( "/", $item['ano_mes_referencia'] );
				
					if($item['fl_media'] =='S')
					{
						$link = '';

						$referencia = " Média de " . $item['ano_referencia'];

						$nr_valor_1 = '';
						$nr_valor_2 = '';
					}
					else
					{
						$link = anchor("indicador_plugin/atend_callcenter_sem_fila_espera/cadastro/" . $item["cd_atend_callcenter_sem_fila_espera"], "editar");

						$referencia = $item['ano_mes_referencia'];
						$nr_meta = $item['nr_meta'];

						$nr_valor_1 = $item["nr_ligacao_sem_fila"];
						$nr_valor_2 = $item["nr_ligacao_atendida"];
					}

					if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && $item['fl_media'] != 'S')
					{
						$contador_ano_atual ++;
						$media_ano[] = $item['nr_ligacao_atendida_percentual'];
					}					
				}

				$media = 0;
				
				foreach( $media_ano as $valor )
				{
					$media += $valor;
				}

				$media = number_format( ($media / sizeof($media_ano)), 2 );				
				

				// gravar média e fechar o período para o indicador
				$sql = "
						INSERT INTO indicador_plugin.atend_callcenter_sem_fila_espera 
							 (
								dt_referencia,
								dt_inclusao,
								dt_alteracao,
								cd_usuario_inclusao, 
								cd_usuario_alteracao, 
								nr_ligacao_atendida_percentual,
							    nr_meta,
								fl_media
							 ) 
						VALUES 
							 ( 
								TO_DATE('".intval($tabela[0]['nr_ano_referencia'])."-01-01','YYYY-MM-DD'), 
								CURRENT_TIMESTAMP, 
								CURRENT_TIMESTAMP, 
								".usuario_id().", 
								".usuario_id().", 
								".floatval($media).",
								".floatval($nr_meta).",
								'S' 
							 );
						
						UPDATE indicador.indicador_tabela
						   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
						       cd_usuario_fechamento_periodo = ".usuario_id()."
				         WHERE cd_indicador_tabela = ".intval($tabela[0]['cd_indicador_tabela'])."; 						
					   ";

				// executar comandos
				if(trim($sql) != '')
				{ 
					$this->db->query($sql); 
				}

			} #tabela_existe
		}

		redirect( 'indicador_plugin/atend_callcenter_sem_fila_espera' );
		// echo 'período encerrado com sucesso';
	}
}
?>