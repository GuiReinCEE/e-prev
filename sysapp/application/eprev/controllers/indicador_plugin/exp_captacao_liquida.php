<?php
class Exp_captacao_liquida extends Controller
{	
	var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::EXP_CAPTACAO_LIQUIDA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/exp_captacao_liquida_model');
    }
	
	public function index()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{
	        $data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/exp_captacao_liquida/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function listar()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
        {
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->exp_captacao_liquida_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/exp_captacao_liquida/index_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function cadastro($cd_exp_captacao_liquida = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_exp_captacao_liquida) == 0)
			{
				$row = $this->exp_captacao_liquida_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela'], intval($data['tabela'][0]['nr_ano_referencia']));
				
				$data['row'] = array(
					'cd_exp_captacao_liquida' => intval($cd_exp_captacao_liquida),
					'dt_referencia'           => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_resgate'              => 0,
					'nr_captacao'             => 0,
					'nr_quantidade'           => 0,
					'nr_meta'                 => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'              => '',
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->exp_captacao_liquida_model->carrega(intval($cd_exp_captacao_liquida));
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/exp_captacao_liquida/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GN'))
		{		
			$cd_exp_captacao_liquida = intval($this->input->post('cd_exp_captacao_liquida', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'nr_resgate'          => app_decimal_para_db($this->input->post('nr_resgate', true)),
				'nr_captacao'         => app_decimal_para_db($this->input->post('nr_captacao', true)),
				'nr_quantidade'       => app_decimal_para_db($this->input->post('nr_quantidade', true)),
				'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);

			if(intval($cd_exp_captacao_liquida) == 0)
			{
				$this->exp_captacao_liquida_model->salvar($args);
			}
			else
			{
				$this->exp_captacao_liquida_model->atualizar($cd_exp_captacao_liquida, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_captacao_liquida', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_exp_captacao_liquida)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GN'))
		{
			$this->exp_captacao_liquida_model->excluir(intval($cd_exp_captacao_liquida), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_captacao_liquida', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$this->load->helper(array('indicador'));

			$data = array();

			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			$data['label_7'] = $this->label_7;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = 'DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela = '.intval($tabela[0]['cd_indicador_tabela']).'; ';

			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, 0, utf8_encode($data['label_0']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, "", 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, 0, utf8_encode($data['label_1']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, 0, utf8_encode($data['label_2']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, 0, utf8_encode($data['label_3']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, 0, utf8_encode($data['label_4']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, 0, utf8_encode($data['label_5']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, 0, utf8_encode($data['label_7']), 'background,center');
			$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, 0, utf8_encode($data['label_6']), 'background,center');

			$collection = $this->exp_captacao_liquida_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual  = 0;
			$nr_contratado_total = 0;
			$nr_meta_total       = 0;

			$linha           = 0;
			$linha_sem_media = 0;

			$referencia = '';

			$nr_resgate_ano    = 0;
			$nr_captacao_ano   = 0;
			$nr_resultado_ano  = 0;
			$pr_resultado_ano  = 0;
			$nr_meta_ano       = 0;					
			$nr_quantidade_ano = 0;

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

						$nr_resgate_ano    += $item['nr_resgate'];
						$nr_captacao_ano   += $item['nr_captacao'];
						$nr_meta_ano       = $item['nr_meta'];					
						$nr_quantidade_ano += $item['nr_quantidade'];	
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
					$indicador[$linha][2] = $item['nr_resgate'];
					$indicador[$linha][3] = $item['nr_captacao'];
					$indicador[$linha][4] = $item['nr_resultado'];
					$indicador[$linha][5] = $item['pr_resultado'];
					$indicador[$linha][6] = $item['nr_meta'];
					$indicador[$linha][7] = $item['nr_quantidade'];
					$indicador[$linha][8] = $item['observacao'];

					$linha++;				
				}
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$nr_resultado_ano = $nr_captacao_ano - $nr_resgate_ano;
    			$pr_resultado_ano = (($nr_resultado_ano / $nr_resgate_ano) * 100);

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = $nr_resgate_ano;
				$indicador[$linha][3] = $nr_captacao_ano;
				$indicador[$linha][4] = $nr_resultado_ano;
				$indicador[$linha][5] = $pr_resultado_ano;
				$indicador[$linha][6] = $nr_meta_ano;
				$indicador[$linha][7] = $nr_quantidade_ano;
				$indicador[$linha][8] = '';
			}

			$linha = 1;

			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, utf8_encode($indicador[$i][8]), 'left');

				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'5,5,0,0;6,6,0,0',
				'0,0,1,'.$linha_sem_media,
				'5,5,1,'.$linha_sem_media.';6,6,1,'.$linha_sem_media,
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GN'))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->exp_captacao_liquida_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual  = 0;
			$nr_quantidade       = 0;
			$nr_resgate          = 0;
			$nr_captacao         = 0;
			$nr_meta             = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$contador_ano_atual++;

					$nr_quantidade += $item['nr_quantidade'];
					$nr_resgate    += $item['nr_resgate'];
					$nr_captacao   += $item['nr_captacao'];
					$nr_meta       = $item['nr_meta'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{

				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'fl_media'            => 'S',
					'nr_resgate'          => $nr_resgate,
					'nr_captacao'         => $nr_captacao,
					'nr_quantidade'       => $nr_quantidade,
					'nr_meta'             => $nr_meta,
					'observacao'          => '',
					'cd_usuario'          => $this->cd_usuario
				);

				$this->exp_captacao_liquida_model->fechar_ano($args);
			}

			$this->exp_captacao_liquida_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/exp_captacao_liquida', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

}
?>