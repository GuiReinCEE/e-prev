<?php
class Controladoria_obrigacoes_legais extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::CONTROLADORIA_OBRIGACOES_LEGAIS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}			

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/controladoria_obrigacoes_legais_model');
    }

    private function get_dropdown_value()
    {
    	return array(
    		array('value' => '2', 'text' => 'Sim'),
    		array('value' => '1', 'text' => 'Não'),
    		array('value' => '0', 'text' => 'Não se aplica')
    	);
    }

    public function index()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GFC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GGS')))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/controladoria_obrigacoes_legais/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GFC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GGS')))
		{
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;
			$data['label_10'] = $this->label_10;
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
			$data['label_14'] = $this->label_14;
			$data['label_15'] = $this->label_15;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->controladoria_obrigacoes_legais_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/controladoria_obrigacoes_legais/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_controladoria_obrigacoes_legais = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GFC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GGS')))
		{
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_10'] = $this->label_10;
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
            $data['label_14'] = $this->label_14;
            $data['label_15'] = $this->label_15;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			$data['drop'] = $this->get_dropdown_value();
			
			if(intval($cd_controladoria_obrigacoes_legais) == 0)
			{
				$row = $this->controladoria_obrigacoes_legais_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela'], intval($data['tabela'][0]['nr_ano_referencia']));

				$data['row'] = array(
					'cd_controladoria_obrigacoes_legais' => intval($cd_controladoria_obrigacoes_legais),
					'dt_referencia'                      => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_fgts'                            => '',
					'nr_inss'                            => '',
					'nr_balancete'                       => '',
					'nr_demostracoes'                    => '',
					'nr_dctf'                            => '',
					'nr_di'                              => '',
					'nr_raiz'                            => '',
					'nr_dirf'                            => '',
                    'nr_caged'                           => '',
                    'nr_tce'                             => '',
					'nr_meta'                            => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'                         => '',
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->controladoria_obrigacoes_legais_model->carrega(intval($cd_controladoria_obrigacoes_legais));
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/controladoria_obrigacoes_legais/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GFC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GGS')))
		{		
			$cd_controladoria_obrigacoes_legais = intval($this->input->post('cd_controladoria_obrigacoes_legais', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'nr_fgts'             => $this->input->post('nr_fgts', true),
				'nr_inss'             => $this->input->post('nr_inss', true),
				'nr_balancete'        => $this->input->post('nr_balancete', true),
				'nr_demostracoes'     => $this->input->post('nr_demostracoes', true),
				'nr_dctf'             => $this->input->post('nr_dctf', true),
				'nr_di'               => $this->input->post('nr_di', true),
				'nr_raiz'             => $this->input->post('nr_raiz', true),
				'nr_dirf'             => $this->input->post('nr_dirf', true),
                'nr_caged'            => $this->input->post('nr_caged', true),
                'nr_tce'              => $this->input->post('nr_tce', true),
				'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_controladoria_obrigacoes_legais) == 0)
			{
				$this->controladoria_obrigacoes_legais_model->salvar($args);
			}
			else
			{
				$this->controladoria_obrigacoes_legais_model->atualizar($cd_controladoria_obrigacoes_legais, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/controladoria_obrigacoes_legais', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_controladoria_obrigacoes_legais)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GFC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GGS')))
		{
			$this->controladoria_obrigacoes_legais_model->excluir(intval($cd_controladoria_obrigacoes_legais), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/controladoria_obrigacoes_legais', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

    public function criar_indicador()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GFC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GGS')))
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_4']  = $this->label_4;
			$data['label_5']  = $this->label_5;
			$data['label_6']  = $this->label_6;
			$data['label_7']  = $this->label_7;
			$data['label_8']  = $this->label_8;
			$data['label_9']  = $this->label_9;
			$data['label_10'] = $this->label_10;
			$data['label_11'] = $this->label_11;
			$data['label_12'] = $this->label_12;
			$data['label_13'] = $this->label_13;
            $data['label_14'] = $this->label_14;
            $data['label_15'] = $this->label_15;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_6']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_12']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, 0, utf8_encode($data['label_13']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, 0, utf8_encode($data['label_14']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10, 0, utf8_encode($data['label_15']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12, 0, utf8_encode($data['label_8']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13, 0, utf8_encode($data['label_9']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14, 0, utf8_encode($data['label_10']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 15, 0, utf8_encode($data['label_11']), 'background,center');

			$collection = $this->controladoria_obrigacoes_legais_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual         = 0;
			$obrigacoes_previstas_total = 0;
            $obrigacoes_cumpridas_total = 0;
			$nr_meta                    = 0;

			$linha           = 0;
			$linha_sem_media = 0;

			$referencia = '';

			foreach($collection as $key => $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-10)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = 'Resultado de '.intval($item['ano_referencia']);
					}
					else
					{
						$referencia = $item['mes_ano_referencia'];
					}

					if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
					{
						$contador_ano_atual++;

                        $obrigacoes_previstas_total += $item['nr_obr_previstas'];
                        $obrigacoes_cumpridas_total += $item['nr_obr_cumpridas'];
						$nr_meta                    = $item['nr_meta'];
					}

					$indicador[$linha][0]  = $referencia;
					$indicador[$linha][1]  = $item['fgts'];
					$indicador[$linha][2]  = $item['inss'];
					$indicador[$linha][3]  = $item['balancete'];
					$indicador[$linha][4]  = $item['demostracoes'];
					$indicador[$linha][5]  = $item['dctf'];
					$indicador[$linha][6]  = $item['di'];
					$indicador[$linha][7]  = $item['raiz'];
					$indicador[$linha][8]  = $item['dirf'];
					$indicador[$linha][9]  = $item['caged'];
					$indicador[$linha][10] = $item['nr_tce'];
					$indicador[$linha][11] = intval($item['nr_obr_previstas']);
					$indicador[$linha][12] = intval($item['nr_obr_cumpridas']);
					$indicador[$linha][13] = $item['nr_resultado'];
					$indicador[$linha][14] = $item['nr_meta'];
					$indicador[$linha][15] = $item['observacao'];


					$linha++;
				}
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$indicador[$linha][0]  = '';
				$indicador[$linha][1]  = '';
				$indicador[$linha][2]  = '';
				$indicador[$linha][3]  = '';
				$indicador[$linha][4]  = '';
				$indicador[$linha][5]  = '';
				$indicador[$linha][6]  = '';
				$indicador[$linha][7]  = '';
				$indicador[$linha][8]  = '';
				$indicador[$linha][9]  = '';
				$indicador[$linha][10] = '';
				$indicador[$linha][11] = '';
				$indicador[$linha][12] = '';
				$indicador[$linha][13] = '';
				$indicador[$linha][14] = '';
				$indicador[$linha][15] = '';

				$linha++;

				$indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1]  = '';
				$indicador[$linha][2]  = '';
				$indicador[$linha][3]  = '';
				$indicador[$linha][4]  = '';
				$indicador[$linha][5]  = '';
				$indicador[$linha][6]  = '';
				$indicador[$linha][7]  = '';
				$indicador[$linha][8]  = '';
				$indicador[$linha][9]  = '';
				$indicador[$linha][10]  = '';
				$indicador[$linha][11] = intval($obrigacoes_previstas_total);
				$indicador[$linha][12] = intval($obrigacoes_cumpridas_total);
				$indicador[$linha][13] = ($obrigacoes_previstas_total > 0 ? (($obrigacoes_cumpridas_total/$obrigacoes_previstas_total) * 100) : 0);
				$indicador[$linha][14] = $nr_meta;
				$indicador[$linha][15] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, utf8_encode($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, utf8_encode($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, utf8_encode($indicador[$i][4]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode($indicador[$i][6]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode($indicador[$i][7]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode($indicador[$i][8]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode($indicador[$i][9]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10, $linha, utf8_encode($indicador[$i][10]), 'center');

				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][11]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12, $linha, app_decimal_para_php($indicador[$i][12]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13, $linha, app_decimal_para_php($indicador[$i][13]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14, $linha, app_decimal_para_php($indicador[$i][14]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 15, $linha, utf8_encode($indicador[$i][15]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'11,11,0,0;12,12,0,0',
				'0,0,1,'.$linha_sem_media,
				'11,11,1,'.$linha_sem_media.';12,12,1,'.$linha_sem_media,
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
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GFC')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'GGS')))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->controladoria_obrigacoes_legais_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual         = 0;
			$obrigacoes_previstas_total = 0;
			$obrigacoes_cumpridas_total = 0;
			$nr_meta                    = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$obrigacoes_previstas_total += $item['nr_obr_previstas'];
					$obrigacoes_cumpridas_total += $item['nr_obr_cumpridas'];
					$nr_meta                    = $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media'            => 'S',
					'nr_obr_previstas'    => $obrigacoes_previstas_total,
					'nr_obr_cumpridas'    => $obrigacoes_cumpridas_total,
					'nr_meta'             => $nr_meta,
					'cd_usuario'          => $this->cd_usuario
				);	
				
				$this->controladoria_obrigacoes_legais_model->fechar_ano($args);
			}

			$this->controladoria_obrigacoes_legais_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/controladoria_obrigacoes_legais', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}