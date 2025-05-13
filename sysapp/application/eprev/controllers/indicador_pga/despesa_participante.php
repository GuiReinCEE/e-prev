<?php
class Despesa_participante extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::PGA_DESPESA_PARTICIPANTE);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_pga/despesa_participante_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data = array();
		
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_pga/despesa_participante/index',$data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
		
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->despesa_participante_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_pga/despesa_participante/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function cadastro($cd_despesa_participante = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_despesa_participante) == 0)
			{
				$row = $this->despesa_participante_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_despesa_participante' => intval($cd_despesa_participante),
					'dt_referencia'           => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'ano_referencia'          => (isset($row['ano_referencia']) ? $row['ano_referencia'] : ''),
					'mes_referencia'          => (isset($row['mes_referencia']) ? $row['mes_referencia'] : ''),
					'nr_valor_1'              => 0,
					'nr_valor_2'              => 0,
					'nr_meta'                 => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'nr_meta_max'             => (isset($row['nr_meta_max']) ? $row['nr_meta_max'] : 0),
					'observacao'              => ''
				);
			}			
			else
			{
				$data['row'] = $this->despesa_participante_model->carrega(intval($cd_despesa_participante));
			}

			$this->load->view('indicador_pga/despesa_participante/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{		
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$nr_valor_1 = app_decimal_para_db($this->input->post('nr_valor_1', true));
			$nr_valor_2 = app_decimal_para_db($this->input->post('nr_valor_2', true));

			$nr_resultado = 0;

			if(floatval($nr_valor_2) > 0)
			{
				$nr_resultado = floatval($nr_valor_1) / floatval($nr_valor_2);
			}

			$args = array(
				'cd_despesa_participante' => intval($this->input->post('cd_despesa_participante', true)),
				'dt_referencia'           => $this->input->post('dt_referencia', true),
				'nr_valor_1'              => $nr_valor_1,
				'nr_valor_2'              => $nr_valor_2,
				'nr_resultado'            => $nr_resultado,
				'nr_meta'                 => app_decimal_para_db($this->input->post('nr_meta', true)),
				'nr_meta_max'             => app_decimal_para_db($this->input->post('nr_meta_max', true)),
				'observacao'              => $this->input->post('observacao', true),
				'cd_indicador_tabela'     => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'                => 'N',
			    'cd_usuario'	          => $this->cd_usuario
			);

			$this->despesa_participante_model->salvar($args);

			$this->criar_indicador();
			
			redirect('indicador_pga/despesa_participante', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_despesa_participante)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$this->despesa_participante_model->excluir($cd_despesa_participante, $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_pga/despesa_participante', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;
			
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			
			$collection = $this->despesa_participante_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador           = sizeof($collection);
			$indicador          = array();
			$a_data             = array(0, 0);
			$ar_tendencia       = array();
			$contador_ano_atual = 0;
			$linha              = 0;

			$nr_valor_1   = 0;
			$nr_valor_2   = 0;
			$nr_meta      = 0;
			$nr_meta_max  = 0;
			$nr_resultado = 0;

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
						
						$nr_valor_1   = $item['nr_valor_1'];
						$nr_valor_2   = $item['nr_valor_2'];
						$nr_meta      = $item['nr_meta'];
						$nr_meta_max  = $item['nr_meta_max'];
						$nr_resultado = $item['nr_resultado'];
					}


					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_valor_1'];
					$indicador[$linha][2] = $item['nr_valor_2'];
					$indicador[$linha][3] = $item['nr_resultado'];
					$ar_tendencia[]       = $item['nr_resultado'];
					$indicador[$linha][4] = $item['nr_meta'];
					$indicador[$linha][5] = $item['nr_meta_max'];
					$indicador[$linha][6] = $item['observacao'];

					$linha++;
				}
			}

			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica( $ar_tendencia );
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][7] = $tend[$i];
			}			
			
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = app_decimal_para_php($nr_valor_1);
				$indicador[$linha][2] = app_decimal_para_php($nr_valor_2);
				$indicador[$linha][3] = app_decimal_para_php($nr_resultado);
				$indicador[$linha][4] = app_decimal_para_php($nr_meta);
				$indicador[$linha][5] = app_decimal_para_php($nr_meta_max);
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';
			}

			$linha = 1;

			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'justify');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 2);

				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='7';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'3,3,0,0;4,4,0,0;5,5,0,0;7,7,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media-barra;4,4,1,$linha_sem_media-linha;5,5,1,$linha_sem_media-linha;7,7,1,$linha_sem_media-linha",
				$this->cd_usuario,
				$coluna_para_ocultar,
				1,
				3
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
		if(indicador_db::verificar_permissao($this->cd_usuario,'GC') OR indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
	        $collection = $this->despesa_participante_model->listar($tabela[0]['cd_indicador_tabela']);

	        $contador_ano_atual = 0;
			$nr_valor_1         = 0;
			$nr_valor_2         = 0;
			$nr_meta            = 0;
			$nr_meta_max        = 0;
			$nr_resultado       = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;

					$nr_valor_1   = $item['nr_valor_1'];
					$nr_valor_2   = $item['nr_valor_2'];
					$nr_meta      = $item['nr_meta'];
					$nr_meta_max  = $item['nr_meta_max'];
					$nr_resultado = $item['nr_resultado'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_despesa_participante' => 0,
					'dt_referencia'          => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'nr_valor_1'             => $nr_valor_1,
					'nr_valor_2'             => $nr_valor_2,
					'nr_resultado'           => $nr_resultado,
					'nr_meta'                => $nr_meta ,
					'nr_meta_max'            => $nr_meta_max,
					'observacao'             => '',
					'cd_indicador_tabela'    => $tabela[0]['cd_indicador_tabela'],
				    'fl_media'               => 'S',
				    'cd_usuario'	         => $this->cd_usuario
				);

				$this->despesa_participante_model->salvar($args);
			}

			$this->despesa_participante_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_pga/despesa_participante', 'refresh');
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
}
?>