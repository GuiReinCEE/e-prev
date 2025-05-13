<?php
class beneficio_inc_seprorgs extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::BENEFICIO_INCORRECOES_GERACAO_CONTRIBUICOES_SEPRORGS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');
    }

    public function index()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
        {
            if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
            {
                $this->criar_indicador();
            }
        
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $this->load->view('indicador_plugin/beneficio_inc_seprorgs/index.php',$data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->load->model('indicador_plugin/beneficio_inc_seprorgs_model');

            $data['label_0']  = $this->label_0;
            $data['label_1']  = $this->label_1;
            $data['label_2']  = $this->label_2;
            $data['label_3']  = $this->label_3;
            $data['label_4']  = $this->label_4;
            $data['label_5']  = $this->label_5;
            $data['label_6']  = $this->label_6;

            $args = array();

            manter_filtros($args);
            
            $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

            $data['collection'] = $this->beneficio_inc_seprorgs_model->listar($data['tabela'][0]['cd_indicador_tabela'], $args);
            
            $this->load->view('indicador_plugin/beneficio_inc_seprorgs/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_beneficio_inc_seprorgs = 0)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->load->model('indicador_plugin/beneficio_inc_seprorgs_model');

	        $data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        $data['label_6'] = $this->label_6;

            $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

            if(intval($cd_beneficio_inc_seprorgs) == 0)
            {
                $row = $this->beneficio_inc_seprorgs_model->carrega_referencia();
                    
                $data['row'] = array(
                    'cd_beneficio_inc_seprorgs' => 0,
                    'nr_meta' 					=> (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
                    'nr_valor_1' 				=> '',
                    'nr_valor_2' 				=> '',
                    'observacao'            	=> '',
                    'dt_referencia'         	=> (isset($row['dt_referencia']) ? $row['dt_referencia'] : '')
                ); 
            }
            else
            {
                $data['row'] = $this->beneficio_inc_seprorgs_model->carrega($cd_beneficio_inc_seprorgs);
            }

            $this->load->view('indicador_plugin/beneficio_inc_seprorgs/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->load->model('indicador_plugin/beneficio_inc_seprorgs_model');

			$cd_beneficio_inc_seprorgs = $this->input->post('cd_beneficio_inc_seprorgs', TRUE);

			$args = array(
				'dt_referencia' 	   => $this->input->post("dt_referencia", TRUE),
				'cd_indicador_tabela'  => $this->input->post("cd_indicador_tabela", TRUE),
				'fl_media' 			   => 'N',
		        'observacao' 		   => $this->input->post("observacao", TRUE),
				'nr_valor_1' 		   => app_decimal_para_db($this->input->post("nr_valor_1", TRUE)),
				'nr_valor_2' 		   => app_decimal_para_db($this->input->post("nr_valor_2", TRUE)),
				'nr_meta' 			   => app_decimal_para_db($this->input->post("nr_meta", TRUE)),
				'nr_percentual_f' 	   => '',
				'cd_usuario'  		   => $this->session->userdata('codigo')
			);

			if(intval($cd_beneficio_inc_seprorgs) == 0)
			{
				$this->beneficio_inc_seprorgs_model->salvar($args);
			}
			else
			{
				$this->beneficio_inc_seprorgs_model->atualizar($cd_beneficio_inc_seprorgs, $args);
			}

			$this->criar_indicador();

			redirect( "indicador_plugin/beneficio_inc_seprorgs", "refresh" );	
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	function excluir($cd_beneficio_inc_seprorgs)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->load->model('indicador_plugin/beneficio_inc_seprorgs_model');

			$this->beneficio_inc_seprorgs_model->excluir($cd_beneficio_inc_seprorgs, $this->session->userdata('codigo'));

            $this->criar_indicador();

			redirect( "indicador_plugin/beneficio_inc_seprorgs", "refresh" );
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->load->model('indicador_plugin/beneficio_inc_seprorgs_model');

	        $data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        $data['label_6'] = $this->label_6;
		
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

			$collection = $this->beneficio_inc_seprorgs_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador    = array();
			$linha        = 0;
			$ar_tendencia = array();
			$contador     = sizeof($collection);
			$media_ano    = array();

			foreach($collection as $item)
			{
				if(trim($item['fl_media']) == 'S')
				{
					$nr_valor_1 	 = '';
					$nr_valor_2 	 = '';
					$nr_percentual_f = $item['nr_percentual_f'];
					$referencia 	 = " Média de ".$item['ano_referencia'];
				}
				else
				{
					$referencia = $item['mes_referencia'];
					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					
					if(floatval($nr_valor_1) > 0)
					{
						$nr_percentual_f = (floatval($nr_valor_2) / floatval($nr_valor_1)) * 100;
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

				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
				$ar_tendencia[] 	  = $nr_percentual_f;
				$indicador[$linha][3] = app_decimal_para_php($nr_percentual_f);
				$indicador[$linha][4] = app_decimal_para_php($item["nr_meta"]);
                $indicador[$linha][6] = $item["observacao"];

				$linha++;
			}

			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a, $b, $tend) = calcular_tendencia_logaritmica($ar_tendencia);

			for($i = 0; $i < sizeof($ar_tendencia); $i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;

			if(sizeof($media_ano) > 0)
			{
				$media = 0;
				foreach($media_ano as $valor)
				{
					$media += $valor;
				}

				$media = number_format(($media / sizeof($media_ano)), 2);

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
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
                $indicador[$linha][6] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S' );
                $sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'left');
                
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='5';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0;5,5,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media;5,5,1,$linha_sem_media",
				usuario_id(),
				$coluna_para_ocultar,
                1,
                2
			);

			$this->db->query($sql);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$this->load->model('indicador_plugin/beneficio_inc_seprorgs_model');

			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));
			
	        $collection = $this->beneficio_inc_seprorgs_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador  = sizeof($collection);
			$media_ano = array();

			foreach( $collection as $item )
			{
				$nr_meta = $item["nr_meta"];

				if(trim($item['fl_media']) == 'S')
				{
					$nr_valor_1 	 = '';
					$nr_valor_2 	 = '';
					$nr_percentual_f = '';
					$referencia 	 = " Média de " . $item['ano_referencia'];
				}
				else
				{
					$referencia = $item['mes_referencia'];
					$nr_valor_1 = $item["nr_valor_1"];
					$nr_valor_2 = $item["nr_valor_2"];
					
					if(floatval($nr_valor_1) > 0)
					{
						$nr_percentual_f = (floatval($nr_valor_2) / floatval($nr_valor_1)) * 100;
					}
					else
					{
						$nr_percentual_f = '0';
					}
				}

				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$media_ano[] = $nr_percentual_f;
				}
			}

			if(sizeof($media_ano) > 0)
			{
				$media = 0;

				foreach($media_ano as $valor)
				{
					$media += $valor;
				}

				$media = ($media / sizeof($media_ano));

				$args = array(
					'dt_referencia' 	   => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela'  => $tabela[0]['cd_indicador_tabela'],
					'fl_media' 			   => 'S',
			        'observacao' 		   => '',
					'nr_valor_1' 		   => '',
					'nr_valor_2' 		   => '',
					'nr_meta' 			   => $nr_meta,
					'nr_percentual_f' 	   => $media,
					'cd_usuario'           => $this->session->userdata('codigo')
				);

				$this->beneficio_inc_seprorgs_model->salvar($args);
			}

			$this->beneficio_inc_seprorgs_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->session->userdata('codigo'));

			redirect( 'indicador_plugin/beneficio_inc_seprorgs' );
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>