<?php
class Administrativo_retorno_pessoa extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::RH_RETORNO_POR_PESSOA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}			
		
		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/administrativo_retorno_pessoa_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GGS'))
		{
			$data = array();
		
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/administrativo_retorno_pessoa/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
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
			
		$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

		$data['collection'] = $this->administrativo_retorno_pessoa_model->listar($data['tabela'][0]['cd_indicador_tabela']);

		$this->load->view('indicador_plugin/administrativo_retorno_pessoa/index_result', $data);
    }

    public function get_valores()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GGS'))
		{
			$args = array(
				'nr_ano' => $this->input->post('nr_ano', true),
		     	'nr_mes' => $this->input->post('nr_mes', true)
			);

			$result = $this->administrativo_retorno_pessoa_model->get_valores($args);

			$row = array(
				'nr_receita' => (count($result) > 0 ? number_format($result['nr_receita'], 2, ',', '.') : 0),
				'nr_despesa' => (count($result) > 0 ? number_format($result['nr_despesa'], 2, ',', '.') : 0)
			);
			
			echo json_encode($row);
		}
	}

	public function cadastro($cd_administrativo_retorno_pessoa = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GGS'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_6'] = $this->label_6;
			$data['label_8'] = $this->label_8;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_administrativo_retorno_pessoa) == 0)
			{
				$row = $this->administrativo_retorno_pessoa_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_administrativo_retorno_pessoa' => intval($cd_administrativo_retorno_pessoa),
					'dt_referencia'                    => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'ano_referencia'                   => (isset($row['ano_referencia']) ? $row['ano_referencia'] : ''),
					'mes_referencia'                   => (isset($row['mes_referencia']) ? $row['mes_referencia'] : ''),
					'nr_pessoa'                        => (isset($row['nr_pessoa']) ? intval($row['nr_pessoa']) : 0),
					'nr_receita'                       => 0,
					'nr_despesa'                       => 0,
					'nr_meta'                          => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'ds_observacao'                    => ''
				);
			}			
			else
			{
				$data['row'] = $this->administrativo_retorno_pessoa_model->carrega(intval($cd_administrativo_retorno_pessoa));
			}

			$this->load->view('indicador_plugin/administrativo_retorno_pessoa/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GGS'))
		{		
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$nr_pessoa               = $this->input->post('nr_pessoa', true);
			$nr_receita              = $this->input->post('nr_receita', true);
			$nr_despesa              = $this->input->post('nr_despesa', true);
			$nr_meta                 = $this->input->post('nr_meta', true);
			$nr_diferenca            = app_decimal_para_db($nr_receita) - app_decimal_para_db($nr_despesa);
			$nr_resultado            = 0;
			$nr_resultado_percentual = 0;

			if(intval($nr_pessoa) > 0)
			{
				$nr_resultado = floatval($nr_diferenca) / intval($nr_pessoa);
			}

			if(app_decimal_para_db($nr_meta) > 0)
			{
				$nr_resultado_percentual = (floatval($nr_resultado) / app_decimal_para_db($nr_meta)) * 100;
			}

			$args = array(
				'cd_administrativo_retorno_pessoa' => intval($this->input->post('cd_administrativo_retorno_pessoa', true)),
				'dt_referencia'                    => $this->input->post('dt_referencia', true),
				'nr_pessoa'                        => app_decimal_para_db($nr_pessoa),
				'nr_receita'                       => app_decimal_para_db($nr_receita),
				'nr_despesa'                       => app_decimal_para_db($nr_despesa),
				'nr_meta'                          => app_decimal_para_db($nr_meta),
				'nr_diferenca'                     => $nr_diferenca,
				'nr_resultado'                     => $nr_resultado,
				'nr_resultado_percentual'          => $nr_resultado_percentual,
				'ds_observacao'                    => $this->input->post('ds_observacao', true),
				'cd_indicador_tabela'              => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'                         => 'N',
			    'cd_usuario'	           		   => $this->cd_usuario
			);

			$this->administrativo_retorno_pessoa_model->salvar($args);

			$this->criar_indicador();
			
			redirect('indicador_plugin/administrativo_retorno_pessoa', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_administrativo_retorno_pessoa)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GGS'))
		{
			$this->administrativo_retorno_pessoa_model->excluir($cd_administrativo_retorno_pessoa, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/administrativo_retorno_pessoa', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{	
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GGS'))
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
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_8']), 'background,center');
			
			$collection = $this->administrativo_retorno_pessoa_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador           = sizeof($collection);
			$indicador          = array();
			$a_data             = array(0, 0);
			$contador_ano_atual = 0;
			$linha              = 0;

			$nr_pessoa_total    = 0;
			$nr_receita_total   = 0;
			$nr_despesa_total   = 0;
			$nr_diferenca_total = 0;
			$nr_resultado_total = 0;
			$nr_meta_total      = 0;
			
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

						$nr_pessoa_total    += $item['nr_pessoa'];
						$nr_receita_total   += $item['nr_receita'];
						$nr_despesa_total   += $item['nr_despesa'];
						$nr_diferenca_total += $item['nr_diferenca'];
						$nr_resultado_total += $item['nr_resultado'];
						$nr_meta_total      += $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_pessoa'];
					$indicador[$linha][2] = $item['nr_receita'];
					$indicador[$linha][3] = $item['nr_despesa'];
					$indicador[$linha][4] = $item['nr_diferenca'];
					$indicador[$linha][5] = $item['nr_resultado'];
					$indicador[$linha][6] = $item['nr_meta'];
					$indicador[$linha][7] = $item['nr_resultado_percentual'];
					$indicador[$linha][8] = $item['ds_observacao'];

					$linha++;
				}
			}
			
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$nr_resultado_percentual = 0;

				if($nr_resultado_total > 0)
				{
					$nr_resultado_percentual = ($nr_resultado_total / $nr_meta_total) * 100;
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

				$linha++;

				$indicador[$linha][0] = '<b>Acumulado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = number_format($nr_pessoa_total/$contador_ano_atual, 2, ',', '.');
				$indicador[$linha][2] = app_decimal_para_php($nr_receita_total);
				$indicador[$linha][3] = app_decimal_para_php($nr_despesa_total);
				$indicador[$linha][4] = app_decimal_para_php($nr_diferenca_total);
				$indicador[$linha][5] = app_decimal_para_php($nr_resultado_total);
				$indicador[$linha][6] = app_decimal_para_php($nr_meta_total);
				$indicador[$linha][7] = app_decimal_para_php($nr_resultado_percentual);
				$indicador[$linha][8] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Média de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = number_format($nr_pessoa_total/$contador_ano_atual, 2, ',', '.');
				$indicador[$linha][2] = app_decimal_para_php($nr_receita_total/$contador_ano_atual);
				$indicador[$linha][3] = app_decimal_para_php($nr_despesa_total/$contador_ano_atual);
				$indicador[$linha][4] = app_decimal_para_php($nr_diferenca_total/$contador_ano_atual);
				$indicador[$linha][5] = app_decimal_para_php($nr_resultado_total/$contador_ano_atual);
				$indicador[$linha][6] = app_decimal_para_php($nr_meta_total/$contador_ano_atual);
				$indicador[$linha][7] = app_decimal_para_php($nr_resultado_percentual);
				$indicador[$linha][8] = '';
			}

			$linha = 1;

			for($i=0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode(nl2br($indicador[$i][8])), 'left');

				$linha++;
			}

			$coluna_para_ocultar = '';

			$sql .= indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'5,5,0,0;6,6,0,0',
				"0,0,1,$linha_sem_media",
				"5,5,1,$linha_sem_media-barra;6,6,1,$linha_sem_media",
				$this->cd_usuario,
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

	public function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GGS'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
	        $collection = $this->administrativo_retorno_pessoa_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;
			$nr_pessoa_total    = 0;
			$nr_receita_total   = 0;
			$nr_despesa_total   = 0;
			$nr_diferenca_total = 0;
			$nr_resultado_total = 0;
			$nr_meta_total      = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;

					$nr_pessoa_total    += $item['nr_pessoa'];
					$nr_receita_total   += $item['nr_receita'];
					$nr_despesa_total   += $item['nr_despesa'];
					$nr_diferenca_total += $item['nr_diferenca'];
					$nr_resultado_total += $item['nr_resultado'];
					$nr_meta_total      += $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$nr_resultado_percentual = 0;

				if($nr_resultado_total > 0)
				{
					$nr_resultado_percentual = ($nr_resultado_total / $nr_meta_total) * 100;
				}

				$args = array(
					'cd_administrativo_retorno_pessoa' => 0,
					'dt_referencia'                    => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_pessoa'                        => $nr_pessoa_total/$contador_ano_atual,
					'nr_receita'                       => $nr_receita_total/$contador_ano_atual,
					'nr_despesa'                       => $nr_despesa_total/$contador_ano_atual,
					'nr_meta'                          => $nr_meta_total/$contador_ano_atual,
					'nr_diferenca'                     => $nr_diferenca_total/$contador_ano_atual,
					'nr_resultado'                     => $nr_resultado_total/$contador_ano_atual,
					'nr_resultado_percentual'          => $nr_resultado_percentual,
					'ds_observacao'                    => '',
					'cd_indicador_tabela'              => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'                         => 'S',
				    'cd_usuario'	           		   => $this->cd_usuario
				);

				$this->administrativo_retorno_pessoa_model->salvar($args);
			}

			$this->administrativo_retorno_pessoa_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/administrativo_retorno_pessoa', 'refresh');
		}
	    else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

}
?>