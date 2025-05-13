<?php
class Investimento_rentabilidade_carteira extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::INVESTIMENTO_RENTABILIDADE_CARTEIRA_DE_INVESTIMENTOS);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}	

		$this->cd_usuario = $this->session->userdata('codigo');		
		
		$this->load->model('indicador_plugin/investimento_rentabilidade_carteira_model');
    }

    public function index()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GIN')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'CQ')))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/investimento_rentabilidade_carteira/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GIN')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'CQ')))
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

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->investimento_rentabilidade_carteira_model->listar($data['tabela'][0]['cd_indicador_tabela']);

			$this->load->view('indicador_plugin/investimento_rentabilidade_carteira/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function cadastro($cd_investimento_rentabilidade_carteira = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GIN')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'CQ')))
		{
			$data = array();
			
			$data['label_0']  = $this->label_0;
			$data['label_1']  = $this->label_1;
			$data['label_2']  = $this->label_2;
			$data['label_3']  = $this->label_3;
			$data['label_8']  = $this->label_8;
			$data['label_11'] = $this->label_11;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_investimento_rentabilidade_carteira) == 0)
			{
				$row = $this->investimento_rentabilidade_carteira_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);

				$data['row'] = array(
					'cd_investimento_rentabilidade_carteira' => intval($cd_investimento_rentabilidade_carteira),
					'dt_referencia'                          => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_valor_1'                             => 0,
					'nr_inpc'                                => 0,
					'nr_atuarial_projetado'                  => (isset($row['nr_atuarial_projetado']) ? $row['nr_atuarial_projetado'] : 0),
					'nr_meta'                                => 0,
					'observacao'                             => ''
				);
			}			
			else
			{
				$data['row'] = $this->investimento_rentabilidade_carteira_model->carrega(intval($cd_investimento_rentabilidade_carteira));
			}

			$this->load->view('indicador_plugin/investimento_rentabilidade_carteira/cadastro', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GIN')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'CQ')))
		{		
			$cd_investimento_rentabilidade_carteira = intval($this->input->post('cd_investimento_rentabilidade_carteira', true));

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->investimento_rentabilidade_carteira_model->listar($tabela[0]['cd_indicador_tabela']);

			$mes_referencia = intval($this->input->post('mes_referencia', true));

			$nr_rentabilidade_acum = array();
			$nr_meta_acum 		   = array();
			$nr_inpc_acum          = array(); 
			$nr_poder_referencia   = array();

			foreach ($collection as $key => $item) 
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S') && ($mes_referencia > intval($item['mes_referencia'])))
				{
					$nr_rentabilidade_acum[] = $item['nr_valor_1'];
					$nr_meta_acum[] 		 = $item['nr_meta'];
					$nr_inpc_acum[]          = $item['nr_inpc'];
					$nr_poder_referencia[]   = $item['nr_poder_referencia'];
				}
			}

			$nr_atuarial_projetado = app_decimal_para_db($this->input->post('nr_atuarial_projetado', true));
			$nr_inpc               = app_decimal_para_db($this->input->post('nr_inpc', true));
			$nr_meta               = app_decimal_para_db($this->input->post('nr_meta', true));
			$nr_valor_1            = app_decimal_para_db($this->input->post('nr_valor_1', true));

			$nr_rentabilidade_acum[] = app_decimal_para_db($this->input->post('nr_valor_1', true));
			$nr_meta_acum[] 		 = app_decimal_para_db($this->input->post('nr_meta', true));
			$nr_inpc_acum[]          = app_decimal_para_db($this->input->post('nr_inpc', true));

			$nr_poder_referencia_calculado = ((((1+(calculo_projetado_mensal($nr_atuarial_projetado, 1) / 100)) * (1+($nr_inpc / 100))) - 1)*100);

			$nr_poder_referencia[] = $nr_poder_referencia_calculado;

			$nr_rentabilidade_acum_calculado_acumulado = calculo_acumulado($nr_rentabilidade_acum, $mes_referencia);
			
			$nr_meta_acum_calculado_acumulado = calculo_acumulado($nr_meta_acum, $mes_referencia);

			$nr_poder_resultado = ((($nr_rentabilidade_acum_calculado_acumulado / 100) - (calculo_acumulado($nr_poder_referencia, $mes_referencia) / 100))*100);

			$args = array(
				'cd_indicador_tabela'   => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'         => $this->input->post('dt_referencia', true),  
				'fl_media'              => 'N',
				'nr_valor_1'            => $nr_valor_1,
				'nr_meta'               => app_decimal_para_db($this->input->post('nr_meta', true)),
				'nr_inpc'               => $nr_inpc,
				'nr_atuarial_projetado' => $nr_atuarial_projetado,
				'nr_atuarial'           => calculo_projetado_mensal($nr_atuarial_projetado, $mes_referencia),
				'nr_rentabilidade_acum' => $nr_rentabilidade_acum_calculado_acumulado,
				'nr_meta_acum' 			=> $nr_meta_acum_calculado_acumulado,
				'nr_inpc_acum'          => calculo_acumulado($nr_inpc_acum, $mes_referencia),
				'nr_poder_meta'         => app_decimal_para_db($this->input->post('nr_poder_meta', true)),
				'nr_poder_referencia'   => $nr_poder_referencia_calculado,
				'nr_poder_resultado'    => $nr_poder_resultado,
				'observacao'            => $this->input->post('observacao', true),
				'cd_usuario'            => $this->cd_usuario
			);

			if(intval($cd_investimento_rentabilidade_carteira) == 0)
			{
				$this->investimento_rentabilidade_carteira_model->salvar($args);
			}
			else
			{
				$this->investimento_rentabilidade_carteira_model->atualizar($cd_investimento_rentabilidade_carteira, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/investimento_rentabilidade_carteira', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_investimento_rentabilidade_carteira)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GIN')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'CQ')))		
		{
			$this->investimento_rentabilidade_carteira_model->excluir(intval($cd_investimento_rentabilidade_carteira), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/investimento_rentabilidade_carteira', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function get_valores()
	{
		$this->load->library('integracao_caderno_cci_indicador');

		$this->integracao_caderno_cci_indicador->set_descricao('rentabilidade_carteira');
		$this->integracao_caderno_cci_indicador->set_ano($this->input->post('nr_ano', true));
		$this->integracao_caderno_cci_indicador->set_mes($this->input->post('nr_mes', true));
		
		$data = $this->integracao_caderno_cci_indicador->get_valores();

		echo json_encode($data);
	}
	
	public function criar_indicador()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GIN')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'CQ')))	
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
			$data['label_11'] = $this->label_11;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_6']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, 0, utf8_encode($data['label_8']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, 0, utf8_encode($data['label_11']), 'background,center');

			$collection = $this->investimento_rentabilidade_carteira_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;

			$linha           = 0;
			$linha_sem_media = 0;

			$referencia = '';

			$nr_valor_1            = 0;
			$nr_meta               = 0;
			$nr_atuarial_projetado = 0;
			$nr_inpc               = 0;
			$nr_atuarial           = 0;
			$nr_rentabilidade_acum = 0;
			$nr_inpc_acum          = 0;
			$nr_meta_acum          = 0;

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

						if($nr_valor_1 == 0)
						{
							$nr_valor_1 = ($item['nr_valor_1']/100)+1;
						}
						else
						{
							$nr_valor_1 *= ($item['nr_valor_1']/100)+1;
						}

						if($nr_meta == 0)
						{
							$nr_meta = ($item['nr_meta']/100)+1;
						}
						else
						{
							$nr_meta *= ($item['nr_meta']/100)+1;
						}

						if($nr_inpc == 0)
						{
							$nr_inpc = ($item['nr_inpc']/100)+1;
						}
						else
						{
							$nr_inpc *= ($item['nr_inpc']/100)+1;
						}

						$nr_atuarial_projetado = $item['nr_atuarial_projetado'];
						$nr_atuarial           = $item['nr_atuarial'];
						$nr_rentabilidade_acum = $item['nr_rentabilidade_acum'];
						$nr_meta_acum 		   = $item['nr_meta_acum'];
						$nr_inpc_acum          = $item['nr_inpc_acum'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_valor_1'];
					$indicador[$linha][2] = $item['nr_meta'];
					$indicador[$linha][3] = $item['nr_inpc'];
					$indicador[$linha][4] = $item['nr_atuarial'];
					$indicador[$linha][5] = $item['nr_rentabilidade_acum'];
					$indicador[$linha][6] = $item['nr_meta_acum'];
					$indicador[$linha][7] = $item['nr_inpc_acum'];
					$indicador[$linha][8] = $item['nr_atuarial_projetado'];
					$indicador[$linha][9] = $item['observacao'];

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

				$linha++;

				$indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1]  = ($nr_valor_1-1)*100;
				$indicador[$linha][2]  = ($nr_meta-1)*100;
				$indicador[$linha][3]  = ($nr_inpc-1)*100;
				$indicador[$linha][4]  = $nr_atuarial;
				$indicador[$linha][5]  = $nr_rentabilidade_acum;
				$indicador[$linha][6]  = $nr_meta_acum;
				$indicador[$linha][7]  = $nr_inpc_acum;
				$indicador[$linha][8]  = $nr_atuarial_projetado;
				$indicador[$linha][9]  = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center', 'S', 4);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, utf8_encode(nl2br($indicador[$i][9])), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'5,5,0,0;6,6,0,0',
				"0,0,1,$linha_sem_media",
				"5,5,1,$linha_sem_media-barra;6,6,1,$linha_sem_media-linha",
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
	
	public function fechar_periodo()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GIN')) OR (indicador_db::verificar_permissao($this->cd_usuario, 'CQ')))	
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->investimento_rentabilidade_carteira_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;

			$nr_valor_1            = 0;
			$nr_meta               = 0;
			$nr_atuarial_projetado = 0;
			$nr_inpc               = 0;
			$nr_atuarial           = 0;
			$nr_rentabilidade_acum = 0;
			$nr_meta_acum          = 0;
			$nr_inpc_acum          = 0;
			$nr_poder_resultado    = 0;
			$nr_poder_referencia   = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					if($nr_valor_1 == 0)
					{
						$nr_valor_1 = ($item['nr_valor_1']/100)+1;
					}
					else
					{
						$nr_valor_1 *= ($item['nr_valor_1']/100)+1;
					}

					if($nr_meta == 0)
					{
						$nr_meta = ($item['nr_meta']/100)+1;
					}
					else
					{
						$nr_meta *= ($item['nr_meta']/100)+1;
					}

					if($nr_inpc == 0)
					{
						$nr_inpc = ($item['nr_inpc']/100)+1;
					}
					else
					{
						$nr_inpc *= ($item['nr_inpc']/100)+1;
					}

					if($nr_poder_referencia == 0)
					{
						$nr_poder_referencia = ($item['nr_poder_referencia']/100)+1;
					}
					else
					{
						$nr_poder_referencia *= ($item['nr_poder_referencia']/100)+1;
					}

					$nr_atuarial_projetado = $item['nr_atuarial_projetado'];
					$nr_atuarial           = $item['nr_atuarial'];
					$nr_rentabilidade_acum = $item['nr_rentabilidade_acum'];
					$nr_meta_acum          = $item['nr_meta_acum'];
					$nr_inpc_acum          = $item['nr_inpc_acum'];
					$nr_poder_resultado    = $item['nr_poder_resultado'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_indicador_tabela'   => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'         => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media'              => 'S',
					'nr_valor_1'            => ($nr_valor_1-1)*100,
					'nr_meta'               => ($nr_meta-1)*100,
					'nr_inpc'               => ($nr_inpc-1)*100,
					'nr_atuarial_projetado' => $nr_atuarial_projetado,
					'nr_atuarial'           => $nr_atuarial,
					'nr_rentabilidade_acum' => $nr_rentabilidade_acum,
					'nr_meta_acum'          => $nr_meta_acum,
					'nr_inpc_acum'          => $nr_inpc_acum,
					'nr_poder_resultado'    => $nr_poder_resultado,
					'nr_poder_referencia'   => ($nr_poder_referencia-1)*100,
					'observacao'            => '',
					'cd_usuario'            => $this->cd_usuario
				);	
				
				$this->investimento_rentabilidade_carteira_model->fechar_ano($args);
			}

			$this->investimento_rentabilidade_carteira_model->fechar_periodo($result, $args);

			redirect('indicador_plugin/investimento_rentabilidade_carteira', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>