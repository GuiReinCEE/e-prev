<?php
class Administrativo_resul_superior extends Controller
{
	var $enum_indicador = 0;
	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();

		$this->enum_indicador = intval(enum_indicador::RH_ATINGIMENTO_OBJETIVO_TREINAMENTO_SUPERIOR);

		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/administrativo_resul_superior_model');
	}

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$data = array();

			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/administrativo_resul_superior/index',$data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
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

	        $data['tabela'] = indicador_tabela_aberta($this->enum_indicador);
			
			$data['collection'] = $this->administrativo_resul_superior_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/administrativo_resul_superior/index_result', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

	public function cadastro($cd_administrativo_resul_superior = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_3'] = $this->label_3;
			$data['label_5'] = $this->label_5;
	        $data['label_8'] = $this->label_8;
	        $data['label_9'] = $this->label_9;
			 
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			if($cd_administrativo_resul_superior == 0)
			{
			    $row = $this->administrativo_resul_superior_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_administrativo_resul_superior' => intval($cd_administrativo_resul_superior),
					'dt_referencia'                    => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'ano_referencia'                   => (isset($row['ano_referencia']) ? $row['ano_referencia'] : $data['tabela'][0]['nr_ano_referencia']),
					'mes_referencia'                   => (isset($row['mes_referencia']) ? $row['mes_referencia'] : '01'),
					'nr_meta'                          => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'nr_valor_1'                   	   => 0,
			        'nr_valor_2'                   	   => 0,
        	        'nr_valor_3'                       => 0,
        	        'observacao'                       => ''
				);		
			}
			else
			{
				$data['row'] = $this->administrativo_resul_superior_model->carregar($cd_administrativo_resul_superior);
			}

			$this->load->view('indicador_plugin/administrativo_resul_superior/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$cd_administrativo_resul_superior = $this->input->post('cd_administrativo_resul_superior', true);
		
			$nr_valor_1 = app_decimal_para_db($this->input->post('nr_valor_1', true));
			$nr_valor_2 = app_decimal_para_db($this->input->post('nr_valor_2', true));
			$nr_valor_3 = app_decimal_para_db($this->input->post('nr_valor_3', true));

			$nr_percentual_f = floatval($nr_valor_1) + floatval($nr_valor_2) + floatval($nr_valor_3);

		 	$nr_valor_4 = 0;
	        $nr_valor_5 = 0;
	        $nr_valor_6 = 0;

			if($nr_percentual_f > 0)
		    {
		        $nr_valor_4 = (floatval($nr_valor_1) / floatval($nr_percentual_f)) * 100;
		        $nr_valor_5 = (floatval($nr_valor_2) / floatval($nr_percentual_f)) * 100;
		        $nr_valor_6 = (floatval($nr_valor_3) / floatval($nr_percentual_f)) * 100;
		    }

			$args = array(
				'dt_referencia'       => $this->input->post('dt_referencia', true),
				'nr_valor_1'          => $nr_valor_1,
			    'nr_valor_2'          => $nr_valor_2,
        	    'nr_valor_3'          => $nr_valor_3,
        	    'nr_valor_4'          => $nr_valor_4,
        	    'nr_valor_5'          => $nr_valor_5,
        	    'nr_valor_6'          => $nr_valor_6,
        	    'nr_percentual_f'     => $nr_percentual_f,
			    'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
			    'observacao'          => $this->input->post('observacao', true),
			    'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'            => 'N',
                'cd_usuario'          => $this->cd_usuario,
            );

			if(intval($cd_administrativo_resul_superior) == 0)
			{
				$this->administrativo_resul_superior_model->salvar($args);
			}
			else
			{
				$this->administrativo_resul_superior_model->atualizar($cd_administrativo_resul_superior, $args);
			}

			$this->criar_indicador();
				
			redirect('indicador_plugin/administrativo_resul_superior', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function excluir($cd_administrativo_resul_superior)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$this->administrativo_resul_superior_model->excluir($cd_administrativo_resul_superior, $this->cd_usuario);

			$this->criar_indicador();
		
			redirect( "indicador_plugin/administrativo_resul_superior", "refresh" );
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
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

	        $tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_9']), 'background,center');

			$collection = $this->administrativo_resul_superior_model->listar($tabela[0]['cd_indicador_tabela']);
				
			$contador           = sizeof($collection);
			$indicador          = array();
			$a_data             = array(0, 0);
			$contador_ano_atual = 0;
			$linha              = 0;

			$nr_valor_1 = 0;
			$nr_valor_2 = 0;
			$nr_valor_3 = 0; 
			$nr_valor_4 = 0;
			$nr_valor_5 = 0;
			$nr_valor_6 = 0;
			$nr_meta    = 0;

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-5)
				{
					if(trim($item['fl_media']) != 'S')
					{
						$referencia = $item['mes_ano_referencia'];
					}
					else
					{
						$referencia = 'Resultado de '.$item['ano_referencia'];
					}
					
					if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && (trim($item['fl_media']) != 'S'))
					{
						$contador_ano_atual++;
						
						$nr_valor_1 += $item['nr_valor_1'];
				        $nr_valor_2 += $item['nr_valor_2'];
				        $nr_valor_3 += $item['nr_valor_3']; 
				        $nr_meta    = $item['nr_meta']; 
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_valor_1'];
					$indicador[$linha][2] = $item['nr_valor_4'];
					$indicador[$linha][3] = $item['nr_valor_2'];
					$indicador[$linha][4] = $item['nr_valor_5'];
					$indicador[$linha][5] = $item['nr_valor_3'];
					$indicador[$linha][6] = $item['nr_valor_6'];
					$indicador[$linha][7] = $item['nr_percentual_f'];
					$indicador[$linha][8] = $item['nr_meta'];
					$indicador[$linha][9] = $item['observacao'];


					$linha++;
				}
			}		

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
                $nr_percentual_f = $nr_valor_1 + $nr_valor_2 + $nr_valor_3;

			    if($nr_percentual_f > 0)
			    {
			        $nr_valor_4 = ($nr_valor_1 / $nr_percentual_f) * 100;
			        $nr_valor_5 = ($nr_valor_2 / $nr_percentual_f) * 100;
			        $nr_valor_6 = ($nr_valor_3 / $nr_percentual_f) * 100;
			    }

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

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_4);
				$indicador[$linha][3] = app_decimal_para_php($nr_valor_2);
				$indicador[$linha][4] = app_decimal_para_php($nr_valor_5);
				$indicador[$linha][5] = app_decimal_para_php($nr_valor_3);
                $indicador[$linha][6] = app_decimal_para_php($nr_valor_6);
                $indicador[$linha][7] = app_decimal_para_php($nr_percentual_f);
                $indicador[$linha][8] = app_decimal_para_php($nr_meta);
                $indicador[$linha][9] = '';
			}

			$linha = 1;

			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 2, 'S');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode(nl2br($indicador[$i][9])), 'justify');
                $linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_ACUMULADO,
				'2,2,0,0;4,4,0,0;6,6,0,0',
				"0,0,1,$linha_sem_media",
				"2,2,1,$linha_sem_media;4,4,1,$linha_sem_media;6,6,1,$linha_sem_media",
				$this->cd_usuario,
				$coluna_para_ocultar
			);

			$this->db->query($sql);

		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    public function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GC'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
	        $collection = $this->administrativo_resul_superior_model->listar($tabela[0]['cd_indicador_tabela']);

	        $contador_ano_atual = 0;
			
			$nr_valor_1 = 0;
			$nr_valor_2 = 0;
			$nr_valor_3 = 0; 
			$nr_valor_4 = 0;
			$nr_valor_5 = 0;
			$nr_valor_6 = 0;
			$nr_meta    = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;

					$nr_valor_1 += $item['nr_valor_1'];
			        $nr_valor_2 += $item['nr_valor_2'];
			        $nr_valor_3 += $item['nr_valor_3']; 
			        $nr_meta    = $item['nr_meta']; 
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$nr_percentual_f = $nr_valor_1 + $nr_valor_2 + $nr_valor_3;

			    if($nr_percentual_f > 0)
			    {
			        $nr_valor_4 = ($nr_valor_1 / $nr_percentual_f) * 100;
			        $nr_valor_5 = ($nr_valor_2 / $nr_percentual_f) * 100;
			        $nr_valor_6 = ($nr_valor_3 / $nr_percentual_f) * 100;
			    }

				$args = array(
					'dt_referencia'      => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_valor_1'          => $nr_valor_1,
				    'nr_valor_2'          => $nr_valor_2,
	        	    'nr_valor_3'          => $nr_valor_3,
	        	    'nr_valor_4'          => $nr_valor_4,
	        	    'nr_valor_5'          => $nr_valor_5,
	        	    'nr_valor_6'          => $nr_valor_6,
	        	    'nr_percentual_f'     => $nr_percentual_f,
				    'nr_meta'             => $nr_meta,
					'observacao'          => '',
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'            => 'S',
				    'cd_usuario'	      => $this->cd_usuario
				);

				$this->administrativo_resul_superior_model->salvar($args);
			}

			$this->administrativo_resul_superior_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/administrativo_resul_superior', 'refresh');
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }

	}
}
?>