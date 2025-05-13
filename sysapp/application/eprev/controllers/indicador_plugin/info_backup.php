<?php
class Info_backup extends Controller
{
	var $enum_indicador = 0;

	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->enum_indicador = intval(enum_indicador::INFO_BACKUP);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);
		
		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}
		
		$this->cd_usuario = $this->session->userdata('codigo');
		
		$this->load->model('indicador_plugin/info_backup_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$data = array();
			
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}		
			
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);
			
	        $this->load->view('indicador_plugin/info_backup/index', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
        {
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->info_backup_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/info_backup/index_result', $data);
        }
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	public function cadastro($cd_info_backup = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$data = array();
			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;
		
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_info_backup) == 0)
			{
				$row = $this->info_backup_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_info_backup' => intval($cd_info_backup),
					'dt_referencia'  => (isset($row['mes_referencia']) ? $row['mes_referencia'] : '01/01/'.$data['tabela'][0]['nr_ano_referencia']),
					'nr_meta'        => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'nr_processo' 	 => 0,
					'nr_percentual'  => 0,
					'nr_soma' 		 => 0,
					'observacao'	 => ''
				);
			}			
			else
			{
				$data['row'] = $this->info_backup_model->carrega(intval($cd_info_backup));
			}

			$this->load->view('indicador_plugin/info_backup/cadastro', $data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$cd_info_backup = intval($this->input->post('cd_info_backup', true));
			
			$args = array( 
				'cd_indicador_tabela' => $this->input->post('cd_indicador_tabela', true),
				'dt_referencia'		  => $this->input->post('dt_referencia', true), 
				'fl_media'            => 'N',
				'nr_meta'             => app_decimal_para_db($this->input->post('nr_meta', true)),
				'nr_soma'             => app_decimal_para_db($this->input->post('nr_soma', true)),
				'nr_processo'         => app_decimal_para_db($this->input->post('nr_processo', true)),
				'nr_percentual'       => app_decimal_para_db($this->input->post('nr_percentual', true)),
				'observacao'          => $this->input->post('observacao', true),
				'cd_usuario'          => $this->cd_usuario,
			);
			
			if(intval($cd_info_backup) == 0)
			{
				$this->info_backup_model->salvar($args);
			}
			else
			{
				$this->info_backup_model->atualizar($cd_info_backup, $args);
			}
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/info_backup', 'refresh');
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function excluir($cd_info_backup)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$this->info_backup_model->excluir(intval($cd_info_backup), $this->cd_usuario);

			$this->criar_indicador();
			
			redirect('indicador_plugin/info_backup', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
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
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 1,0, '', 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula( $tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_6']), 'background,center');

			$collection = $this->info_backup_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador = array();
			$linha = 0;

			$nr_percentual = 0;

			$nr_meta = 0;
			
			$referencia = '';

			$contador_ano_atual = 0;
			
			foreach($collection as $key => $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) - 20)
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
						$nr_percentual += $item['nr_percentual'];

						$nr_meta = $item['nr_meta'];

						$contador_ano_atual++;
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($item["fl_meta"], $item["fl_direcao"], "S").'" border="0">';
					$indicador[$linha][2] = app_decimal_para_php($item['nr_soma']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_processo']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_percentual']);
					$indicador[$linha][5] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][6] = 0;
					$indicador[$linha][7] = $item['observacao'];
					$ar_tendencia[] = $item['nr_percentual'];

					$linha++;
				}
			}
			
			list($a,$b,$tend) = calcular_tendencia_logaritmica($ar_tendencia);
			for($i=0;$i<sizeof($ar_tendencia);$i++)
			{
				$indicador[$i][6] = $tend[$i];
			}				
			
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$nr_resultado = 0;

				if(intval($nr_percentual) > 0)
				{
					$nr_resultado = ($nr_percentual / $contador_ano_atual);
				}

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';

				$linha++;

				$ar_status = indicador_status_check($nr_resultado, 0, $item['nr_meta'], $item['tp_analise']);
				
				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = '<img src="{BASE_URL}/'.indicador_status($ar_status["fl_meta"], $ar_status["fl_direcao"], 'S').'" border="0">';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = $nr_resultado;
				$indicador[$linha][5] = $nr_meta;
				$indicador[$linha][6] = '';
				$indicador[$linha][7] = '';
			}

			$linha = 1;
			
			for($i = 0; $i < sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, utf8_encode($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 0);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode(nl2br($indicador[$i][7])), 'justify');
				
				$linha++;
			}

			$coluna_para_ocultar='6';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'4,4,0,0;5,5,0,0',
				'0,0,1,'.$linha_sem_media,
				'4,4,1,'.$linha_sem_media.';5,5,1,'.$linha_sem_media,
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GTI'))
		{
			$args = array();

			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$collection = $this->info_backup_model->listar($tabela[0]['cd_indicador_tabela']);

			$nr_percentual = 0;
			$nr_meta       = 0;
			
			$contador_ano_atual  = 0;
			
			foreach($collection as $key => $item)
			{
				if((intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia'])) && ($item['fl_media'] != 'S'))
				{
					$nr_percentual += $item['nr_percentual'];
					$nr_meta        = $item['nr_meta'];

					$contador_ano_atual++;
				}

			}
			
			if(intval($contador_ano_atual) > 0)
			{

				$nr_resultado = 0;

				if(intval($nr_percentual) > 0)
				{
					$nr_resultado = ($nr_percentual / $contador_ano_atual);
				}

				$args = array(
					'cd_indicador_tabela' => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia'       => '01/01/'.intval($tabela[0]['nr_ano_referencia']),  
					'fl_media'            => 'S',
					'nr_meta'             => $nr_meta,
					'nr_soma'			  => 0,
					'nr_processo' 		  => 0,
					'nr_percentual'		  => $nr_resultado,
					'observacao'          => '',
					'cd_usuario'          => $this->cd_usuario
				);
				
				$this->info_backup_model->salvar($args);
			}
				
			$this->info_backup_model->fechar_indicador($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);
			
			redirect('indicador_plugin/info_backup', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}	
}
?>