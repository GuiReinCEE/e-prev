<?php
class Controladoria_cobertura_benf_cd extends Controller
{	
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_COBERTURA_BENEFICIO_CD);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');		
		
		$this->load->model('indicador_plugin/controladoria_cobertura_benf_cd_model');
    }

    public function get_valores()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GP'))
		{
			$args = array(
				'nr_ano' => $this->input->post('nr_ano', true)
			);

			$result = $this->controladoria_cobertura_benf_cd_model->get_valores($args);

			$row = array(
				'nr_valor_1' => (count($result) > 0 ? number_format($result['nr_valor_1'], 2, ',', '.') : 0),
				'nr_valor_2' => (count($result) > 0 ? number_format($result['nr_valor_2'], 2, ',', '.') : 0)
			);
			
			echo json_encode($row);
		}
	}
	
	function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP'))
		{
			$data = array();

			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/controladoria_cobertura_benf_cd/index', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
	function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP'))
        {
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->controladoria_cobertura_benf_cd_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/controladoria_cobertura_benf_cd/index_result', $data);
        }
        else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

	function cadastro($cd_controladoria_cobertura_benf_cd = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP'))
		{
			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
	        
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_controladoria_cobertura_benf_cd) == 0)
			{
				$row = $this->controladoria_cobertura_benf_cd_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_controladoria_cobertura_benf_cd' => $cd_controladoria_cobertura_benf_cd,
					'nr_valor_1'            			 => 0,
					'nr_valor_2'            			 => 0,
					'nr_meta'               			 => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'            			 => '',
					'fl_media'              			 => '',
					'dt_referencia'                      => (isset($row['dt_referencia']) ? $row['dt_referencia'] : ""),
					'ano_referencia'                     => (isset($row['ano_referencia']) ? $row['ano_referencia'] : "")
				);
			}			
			else
			{
				$data['row'] = $this->controladoria_cobertura_benf_cd_model->carrega($cd_controladoria_cobertura_benf_cd);
			}

			$this->load->view('indicador_plugin/controladoria_cobertura_benf_cd/cadastro', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

	function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP'))
		{		
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$dt_referencia_db = $this->input->post('dt_referencia_db', true);
			
			$inpel     = $this->controladoria_cobertura_benf_cd_model->get_inpel($dt_referencia_db);
			$ceran       = $this->controladoria_cobertura_benf_cd_model->get_ceran($dt_referencia_db);
			$foz       = $this->controladoria_cobertura_benf_cd_model->get_foz($dt_referencia_db);
			$municipio = $this->controladoria_cobertura_benf_cd_model->get_municipio($dt_referencia_db);
			
			$inpel_valor_1 = 0;
			
			if(isset($inpel['nr_valor_1']))
			{
				$inpel_valor_1 = $inpel['nr_valor_1'];
			}
			
			$inpel_valor_2 = 0;
			
			if(isset($inpel['nr_valor_2']))
			{
				$inpel_valor_2 = $inpel['nr_valor_2'];
			}
			
			$ceran_valor_1 = 0;
			
			if(isset($ceran['nr_valor_1']))
			{
				$ceran_valor_1 = $ceran['nr_valor_1'];
			}
			
			$ceran_valor_2 = 0;
			
			if(isset($ceran['nr_valor_2']))
			{
				$ceran_valor_2 = $ceran['nr_valor_2'];
			}
			
			$foz_valor_1 = 0;
			
			if(isset($foz['nr_valor_1']))
			{
				$foz_valor_1 = $foz['nr_valor_1'];
			}
			
			$foz_valor_2 = 0;
			
			if(isset($foz['nr_valor_2']))
			{
				$foz_valor_2 = $foz['nr_valor_2'];
			}
			
			$municipio_valor_1 = 0;
			
			if(isset($municipio['nr_valor_1']))
			{
				$municipio_valor_1 = $municipio['nr_valor_1'];
			}
			
			$municipio_valor_2 = 0;
			
			if(isset($municipio['nr_valor_2']))
			{
				$municipio_valor_2 = $municipio['nr_valor_2'];
			}
			
			$nr_valor_1 = $inpel_valor_1 + $ceran_valor_1 + $foz_valor_1 + $municipio_valor_1;
			$nr_valor_2 = $inpel_valor_2 + $ceran_valor_2 + $foz_valor_2 + $municipio_valor_2;
		
			$calculos_planos = array(
				'inpel_valor_1'     => $inpel_valor_1,
				'inpel_valor_2'     => $inpel_valor_2,
				'ceran_valor_1'     => $ceran_valor_1,
				'ceran_valor_2'     => $ceran_valor_2,
				'foz_valor_1'       => $foz_valor_1,
				'foz_valor_2'       => $foz_valor_2,
				'municipio_valor_1' => $municipio_valor_1,
				'municipio_valor_2' => $municipio_valor_2,
			);

            $nr_percentual_f = 0;

            if(intval($nr_valor_2) >0)
            {
            	$nr_percentual_f = ($nr_valor_1/$nr_valor_2)*100;
            }

			$args = array(
				'cd_controladoria_cobertura_benf_cd' => intval($this->input->post('cd_controladoria_cobertura_benf_cd', true)),
				'dt_referencia'                      => $this->input->post('dt_referencia', true),
			    'cd_usuario'                         => $this->cd_usuario,
			    'cd_indicador_tabela'                => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'                           => 'N',
			    'nr_valor_1'                         => $nr_valor_1,
			    'nr_valor_2'                         => $nr_valor_2,
				'nr_percentual_f'                    => $nr_percentual_f,
			    'nr_meta'                            => app_decimal_para_db($this->input->post('nr_meta', true)),
                'observacao'                         => $this->input->post("observacao", true)
            );
			
			$args['obs_origem'] = $this->monta_tabela($calculos_planos);
			
			$this->controladoria_cobertura_benf_cd_model->salvar($args);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_cobertura_benf_cd", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	private function monta_tabela($calculos_planos)
    {
        $tabela = '<table class="sort-table sub-table" width="100%" cellspacing="2" cellpadding="2" align="center">';
       
        $tabela .= '<thead>';
        $tabela .= '<tr>';
        $tabela .= '<th>Plano</th>';
        $tabela .= '<th>Patrimônio (R$)</th>';
        $tabela .= '<th>Expectativa (R$)</th>';
        $tabela .= '</tr>';
        $tabela .= '</thead>';

        $tabela .= '<tbody>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">FAMÍLIA PREVIDÊNCIA Corporativo</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['inpel_valor_1'], 2, ',', '.').''.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['inpel_valor_2'], 2, ',', '.').''.'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">CeranPrev</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['ceran_valor_1'], 2, ',', '.').''.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['ceran_valor_2'], 2, ',', '.').''.'</td>';
        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">Foz do Chapecó Prev</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['foz_valor_1'], 2, ',', '.').''.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['foz_valor_2'], 2, ',', '.').''.'</td>';
        $tabela .= '</tr>';
		
		$tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;">FAMÍLIA PREVIDÊNCIA Municípios</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['municipio_valor_1'], 2, ',', '.').''.'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($calculos_planos['municipio_valor_2'], 2, ',', '.').''.'</td>';
        $tabela .= '</tr>';
        
        $tabela .= '</tbody>';

        $tabela .= '</table>';

        return $tabela;
    }
	
	function excluir($cd_controladoria_cobertura_benf_cd)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP'))
		{
			$this->controladoria_cobertura_benf_cd_model->excluir($cd_controladoria_cobertura_benf_cd, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/controladoria_cobertura_benf_cd", "refresh");
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GP'))
		{
			$args   = array();
					
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
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, '', 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_6']), 'background,center');
			
			$collection = $this->controladoria_cobertura_benf_cd_model->listar($tabela[0]['cd_indicador_tabela']);			 
			
			$indicador          = array();
			$linha              = 0;
			$media_ano          = array();
			$ar_tendencia       = array();
			$nr_meta            = 0;
			$contador_ano_atual = 0;
			$media              = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					$observacao = $item["observacao"];

					if(trim($item['fl_media']) == 'S')
					{
						$referencia = "Resultado de " . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['ano_referencia'];
					}
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_valor_1']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_valor_2']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_percentual_f']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][6] = $item['obs_origem'];
					$indicador[$linha][7] = nl2br($item['observacao']);

					$ar_tendencia[] = $item['nr_percentual_f'];
					
					$linha++;
				}
			}	
				
			// LINHA DE TENDÊNCIA - CURVA LOGARITMICA
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia );

			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][5] = $tend[$i];
			}

			$linha_sem_media = $linha;
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'center');
				$sql .= indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]) );
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='5';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media-linha",
				$this->cd_usuario,
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
}
?>