<?php
class participantes_inst extends Controller
{
	var	$label_0 = "Mês";
	var	$label_1 = "Participantes";#"Participantes (Somente Instituidores)";
	var	$label_2 = "A";
	var	$label_3 = "B";
    var $label_4 = "Meta Mínima";
    var $label_5 = "Tendência";
    var $label_6 = "Faixa";
	var $label_7 = "Meta";	

	var $enum_indicador = 0;
	var $fl_permissao = FALSE;
	
	function __construct()
    {
        parent::Controller();
		$this->enum_indicador = intval(enum_indicador::PODER_PARTICIPANTES_INST);
		
		CheckLogin();
		if((gerencia_in(array('GGS'))) and ($this->session->userdata('tipo') == "G")) # Gerente da GGS
		{
			$this->fl_permissao = TRUE;
		}
		elseif (gerencia_in(array('GP')))
		{
			$this->fl_permissao = TRUE;
		}		
		elseif (usuario_id() == 103)
		{
			$this->fl_permissao = TRUE;
		}
		elseif (usuario_id() == 251)
		{
			$this->fl_permissao = TRUE;
		}
    }

    function index()
    {
		CheckLogin();
		$this->load->helper(array('indicador'));
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

	        $this->load->view('indicador_poder/participantes_inst/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

    function listar()
    {
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
        $data['label_4'] = $this->label_4;
        $data['label_6'] = $this->label_6;
		
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
        {
	        $this->load->model( 'indicador_poder/participantes_inst_model' );

	        $data['collection'] = array();
	        $result = null;
            $args = array();
			
			$tabela = indicador_tabela_aberta(  intval( $this->enum_indicador )  );
			$data['tabela'] = $tabela;

			if( sizeof($tabela)>0 )
			{
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->participantes_inst_model->listar( $result, $args );

				$data['collection'] = $result->result_array();

				$this->load->view('indicador_poder/participantes_inst/partial_result', $data);
			}
			else
			{
				echo "Nenhum período aberto para o indicador.";
			}
        }
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

	function detalhe($cd=0)
	{
		$data['label_0'] = $this->label_0;
		$data['label_1'] = $this->label_1;
        $data['label_4'] = $this->label_4;
		
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
		{

			$tabela = indicador_tabela_aberta( intval( $this->enum_indicador  ) );
			$data['tabela'] = $tabela;

			$this->load->model('indicador_poder/participantes_inst_model');
			$row=$this->participantes_inst_model->carregar( $cd );

            if($row)
			{
				if($cd==0)
				{
					$sql = "
                        SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia,
                               dt_referencia,
                               nr_meta,
                               cd_indicador_tabela
                          FROM indicador_poder.participantes_inst
                         WHERE dt_exclusao IS NULL
                         ORDER BY dt_referencia DESC LIMIT 1
					";
					$query = $this->db->query($sql);
					$row_atual = $query->row_array();

					if($row_atual)
					{
                        $row['dt_referencia']       = $row_atual['mes_referencia'];
						$row['cd_indicador_tabela'] = $row_atual['cd_indicador_tabela'];
					}
				}

				$data['row'] = $row; 
			}

			$this->load->view('indicador_poder/participantes_inst/detalhe', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function salvar()
	{
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
		{
			$this->load->model('indicador_poder/participantes_inst_model');
			
			$args['cd_participantes_inst'] = intval($this->input->post('cd_participantes_inst', true));
			$args["dt_referencia"]           = $this->input->post("dt_referencia", true);
			$args["cd_usuario_inclusao"]     = usuario_id();
			$args["cd_indicador_tabela"]     = $this->input->post("cd_indicador_tabela", true);
			$args["fl_media"]                = $this->input->post("fl_media", true);
			$args["nr_valor_1"]              = app_decimal_para_db($this->input->post("nr_valor_1", true));

			$msg=array();
			$retorno = $this->participantes_inst_model->salvar( $args,$msg );
			
			if($retorno)
			{
				if(!$this->criar_indicador())
				{
					exibir_mensagem("Erro ao criar indicador.");
				}
				else
				{
					redirect( "indicador_poder/participantes_inst", "refresh" );
				}				
			}
			else
			{
				$mensagens = implode('<br>',$msg);
				exibir_mensagem($msg[0]);
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function excluir($id)
	{
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
		{
			$this->load->model('indicador_poder/participantes_inst_model');

			$this->participantes_inst_model->excluir($id);

			if(!$this->criar_indicador())
			{
				exibir_mensagem("Erro ao criar indicador.");
			}
			else
			{
				redirect( 'indicador_poder/participantes_inst', 'refresh' );
			}				
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
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
		
		$this->load->helper(array('indicador'));
		$this->load->model('indicador_poder/parametro_meta_model');	
		$this->load->model('indicador_poder/participantes_inst_model');
		
		CheckLogin();
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
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, "", 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
				$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
                $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
				
				$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];

				$this->participantes_inst_model->listar( $result, $args );
				$collection = $result->result_array();

				$indicador=array();
                $ar_tendencia=array();
                $media_ano=array();

				$linha=0;
                
				$contador = sizeof($collection);
				

				foreach( $collection as $item )
				{
					if(true)
					{
						$nr_meta         = $item["nr_meta"];
                        $referencia      = $item['mes_referencia'];
                        $nr_valor_1      = $item["nr_valor_1"];
                        $nr_valor_2      = $item["nr_valor_2"];
                        $nr_percentual_f = $item['nr_percentual_f'];
                        $nr_faixa        = $item['nr_faixa'];
						$tp_analise      = $item['tp_analise'];

						if( intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) )
						{
							$media_ano[] = $nr_valor_1;
						}

						$indicador[$linha][0] = $referencia;
						$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
						$indicador[$linha][2] = ($nr_valor_1);
						$indicador[$linha][3] = ($nr_valor_2);
						$indicador[$linha][4] = ($nr_percentual_f);
						$indicador[$linha][5] = ($nr_meta);
                        $indicador[$linha][6] = $nr_faixa;

						$param['cd_indicado_tabela'] = $tabela[0]['cd_indicador_tabela'];
						$param['dt_referencia'] = $item['dt_referencia'];
						$indicador[$linha][7] = $this->parametro_meta_model->getMeta($param);

						$linha++;
					}
				}

				$linha_sem_media = $linha;

				$indicador = array_reverse($indicador);
				$linha = 1;
				for( $i=0; $i<sizeof($indicador); $i++ )
				{
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center');
					$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center');
                    $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]), 'center' );

					$linha++;
				}

				$coluna_para_ocultar='3,4';
				$sql.=indicador_db::sql_inserir_grafico(
					$tabela[0]['cd_indicador_tabela'],
					enum_indicador_grafico_tipo::LINHA,
					'2,2,0,0;5,5,0,0',
					"0,0,1,$linha_sem_media",
					"2,2,1,$linha_sem_media;5,5,1,$linha_sem_media-linha",
					usuario_id(),
					$coluna_para_ocultar,
                    1,
					-1,
					-1,
					"S"	
				);

				if(trim($sql)!=''){$this->db->query($sql);}

                $this->load->model('indicador_poder/resultado_poder_semestre_1_model');
                $this->resultado_poder_semestre_1_model->criar_indicador( );

                $this->load->model('indicador_poder/resultado_poder_semestre_2_model');
                $this->resultado_poder_semestre_2_model->criar_indicador( );

				return true;
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

	function fechar_periodo()
	{
		CheckLogin();
		$this->load->helper(array('indicador'));
		if($this->fl_permissao)
		{
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
			$this->load->model('indicador_poder/participantes_inst_model');
			$args['cd_indicador_tabela'] = $tabela[0]['cd_indicador_tabela'];
	        $this->participantes_inst_model->listar( $result, $args );
			$collection = $result->result_array();

			if(sizeof($tabela)<=0)
			{
				echo "Não foi identificado período aberto para o Indicador";
			}
			else
			{
				$sql=" 
                    UPDATE indicador.indicador_tabela 
                       SET dt_fechamento_periodo         = CURRENT_TIMESTAMP,
                           cd_usuario_fechamento_periodo = ".intval(usuario_id())." 
                     WHERE cd_indicador_tabela=". intval($tabela[0]['cd_indicador_tabela']);

				if(trim($sql)!=''){$this->db->query($sql);}

			}
			redirect( 'indicador_poder/participantes_inst' );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
		
	}
}
?>