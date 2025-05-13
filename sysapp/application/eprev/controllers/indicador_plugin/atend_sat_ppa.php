<?php
class Atend_sat_ppa extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_SATISFACAO_COM_O_CURSO_PREPARACAO_PARA_APOSENTADORIA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}	
		
		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/atend_sat_ppa_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

	        $this->load->view('indicador_plugin/atend_sat_ppa/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			$data['label_6'] = $this->label_6;
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->atend_sat_ppa_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/atend_sat_ppa/index_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function cadastro($cd_atend_sat_ppa = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
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

			if(intval($cd_atend_sat_ppa) == 0)
			{
				$row = $this->atend_sat_ppa_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_atend_sat_ppa' => intval($cd_atend_sat_ppa),
					'dt_referencia'    => (isset($row['mes_referencia']) ? $row['mes_referencia'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_meta'          => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'nr_valor_0' 	   => 0,
					'nr_valor_1'       => 0,
					'nr_valor_2' 	   => 0,
					'observacao'	   => ''
				);
			}
			else
			{
				$data['row'] = $this->atend_sat_ppa_model->carrega(intval($cd_atend_sat_ppa));
			}

			$this->load->view('indicador_plugin/atend_sat_ppa/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$cd_atend_sat_ppa = intval($this->input->post('cd_atend_sat_ppa', true));

			$args = array(
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'       => $this->input->post('dt_referencia', true),  
				'fl_media'            => 'N',
				'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
				'nr_valor_0'		  => app_decimal_para_db($this->input->post('nr_valor_0', true)),
				'nr_valor_1' 		  => app_decimal_para_db($this->input->post('nr_valor_1', true)),
				'nr_valor_2'		  => app_decimal_para_db($this->input->post('nr_valor_2', true)),
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario
			);
			
			if(intval($cd_atend_sat_ppa) == 0)
			{
				$this->atend_sat_ppa_model->salvar($args);
			}
			else
			{
				$this->atend_sat_ppa_model->atualizar($cd_atend_sat_ppa, $args);
			}

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_sat_ppa', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_atend_sat_ppa)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$this->atend_sat_ppa_model->excluir(intval($cd_atend_sat_ppa), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_sat_ppa', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
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

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = "DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');

			$collection = $this->atend_sat_ppa_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador = array();
			$linha = 0;

			$nr_valor_0 = 0;
			$nr_valor_1 = 0;
			$nr_valor_2 = 0;

			$nr_meta = 0;
			
			$referencia = '';

			$contador_ano_atual = 0;
						
			foreach($collection as $key => $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - 5)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = 'Resultado de '.intval($item['ano_referencia']);
					}
					else
					{
						$referencia = $item['mes_referencia'];
					}
					
					if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
					{
						$nr_valor_0 += $item['nr_valor_0'];
						$nr_valor_1 += $item['nr_valor_1'];
						$nr_valor_2 += $item['nr_valor_2'];

						$nr_meta = $item['nr_meta'];

						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_valor_0']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_valor_1']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_valor_2']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_percentual_f']);
					$indicador[$linha][5] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][6] = $item['observacao'];

					$linha++;
				}
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$nr_resultado = 0;

				if(intval($nr_valor_1) > 0)
				{
					$nr_resultado = (($nr_valor_1 / $nr_valor_2) * 100);
				}

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';

				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_valor_0;
				$indicador[$linha][2] = $nr_valor_1;
				$indicador[$linha][3] = $nr_valor_2;
				$indicador[$linha][4] = $nr_resultado;
				$indicador[$linha][5] = $nr_meta;
				$indicador[$linha][6] = '';
			}

			$linha = 1;
			
			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])), 'justify');

				$linha++;
			}

			$coluna_para_ocultar = '';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::BARRA_MULTIPLO,
				'4,4,0,0;5,5,0,0',
				'0,0,1,'.$linha_sem_media,
				'4,4,1,'.$linha_sem_media.';5,5,1,'.$linha_sem_media,
				$this->cd_usuario,
				$coluna_para_ocultar,
				1
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$args = array();
			
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$collection = $this->atend_sat_ppa_model->listar($tabela[0]['cd_indicador_tabela']);

			$nr_valor_0 = 0;
			$nr_valor_1 = 0;
			$nr_valor_2 = 0;
			$nr_meta    = 0;

			$contador_ano_atual  = 0;

			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$nr_valor_0 += $item['nr_valor_0'];
					$nr_valor_1 += $item['nr_valor_1'];
					$nr_valor_2 += $item['nr_valor_2'];
					$nr_meta    = $item['nr_meta'];

					$contador_ano_atual++;
				}
			}
			
			$args = array(
				'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
				'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),  
				'fl_media'            => 'S',
				'nr_meta'             => $nr_meta,
				'nr_valor_0'		  => $nr_valor_0,
				'nr_valor_1' 		  => $nr_valor_1,
				'nr_valor_2'		  => $nr_valor_2,
				'observacao'          => '',
				'cd_usuario'          => $this->cd_usuario
			);
			
			$this->atend_sat_ppa_model->salvar($args);	
					
			$this->atend_sat_ppa_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);
			
			redirect('indicador_plugin/atend_sat_ppa', 'refresh');
		} 
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}	
?>