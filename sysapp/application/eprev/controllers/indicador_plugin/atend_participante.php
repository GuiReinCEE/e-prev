<?php
class atend_participante extends Controller
{
    var $enum_indicador = 0;

	public function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::ATENDIMENTO_PARTICIPANTE);

		$this->load->helper(array('indicador'));

		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/atend_participante_model');
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

	        $this->load->view('indicador_plugin/atend_participante/index', $data);
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
        $data['label_9'] = $this->label_9;
        $data['label_10'] = $this->label_10;
        $data['label_11'] = $this->label_11;
        $data['label_12'] = $this->label_12;
        $data['label_13'] = $this->label_13;
        $data['label_14'] = $this->label_14;
        $data['label_15'] = $this->label_15;
        $data['label_16'] = $this->label_16;

		$data['tabela']  = indicador_tabela_aberta(intval($this->enum_indicador));
		
		$data['collection'] = $this->atend_participante_model->listar($data['tabela'][0]['cd_indicador_tabela']);

		$this->load->view('indicador_plugin/atend_participante/index_result', $data);        
    }

	public function cadastro($cd_atend_participante = 0)
	{
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
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
	        $data['label_10'] = $this->label_10;
	        $data['label_11'] = $this->label_11;
	        $data['label_12'] = $this->label_12;
	        $data['label_13'] = $this->label_13;
	        $data['label_14'] = $this->label_14;
	        $data['label_15'] = $this->label_15;
        	$data['label_16'] = $this->label_16;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_atend_participante) == 0)
			{
				$row = $this->atend_participante_model->carrega_referencia($data['tabela'][0]['cd_indicador_tabela']);
				
				$data['row'] = array(
					'cd_atend_participante' => $cd_atend_participante,
					'dt_referencia'         => (isset($row['dt_referencia']) ? $row['dt_referencia'] : "01/01/".$data['tabela'][0]['nr_ano_referencia']),
					'ano_referencia'        => (isset($row['ano_referencia']) ? $row['ano_referencia'] : ""),
					'mes_referencia'        => (isset($row['mes_referencia']) ? $row['mes_referencia'] : ""),
					'nr_ceee'               => 0,
					'nr_aes'                => 0,
					'nr_cgtee'              => 0,
					'nr_rge'				=> 0,
					'nr_crm'				=> 0,
					'nr_senge'				=> 0,
					'nr_sinpro'				=> 0,
					'nr_familia'			=> 0,
					'nr_inpel'				=> 0,
					'nr_ceran'				=> 0,
					'nr_foz'				=> 0,				
					'nr_familia_municipio'	=> 0,				
					'nr_ieabprev'	        => 0,				
					'nr_meta'               => (isset($row['nr_meta']) ? $row['nr_meta'] : 0),
					'observacao'            => ''
				);
			}			
			else
			{
				$data['row'] = $this->atend_participante_model->carrega($cd_atend_participante);
			}

			$this->load->view('indicador_plugin/atend_participante/cadastro', $data);
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
			$tabela = indicador_tabela_aberta($this->enum_indicador);

			$args = array(
				'cd_atend_participante' => $this->input->post('cd_atend_participante', true),
				'dt_referencia'         => $this->input->post('dt_referencia', true),
				'nr_ceee'               => app_decimal_para_db($this->input->post('nr_ceee', true)),
				'nr_aes'                => app_decimal_para_db($this->input->post('nr_aes', true)),
				'nr_cgtee'              => app_decimal_para_db($this->input->post('nr_cgtee', true)),
				'nr_rge'				=> app_decimal_para_db($this->input->post('nr_rge', true)),
				'nr_crm'				=> app_decimal_para_db($this->input->post('nr_crm', true)),
				'nr_senge'				=> app_decimal_para_db($this->input->post('nr_senge', true)),
				'nr_sinpro'				=> app_decimal_para_db($this->input->post('nr_sinpro', true)),
				'nr_familia'			=> app_decimal_para_db($this->input->post('nr_familia', true)),
				'nr_inpel'				=> app_decimal_para_db($this->input->post('nr_inpel', true)),
				'nr_ceran'				=> app_decimal_para_db($this->input->post('nr_ceran', true)),
				'nr_foz'				=> app_decimal_para_db($this->input->post('nr_foz', true)),		
				'nr_familia_municipio'  => app_decimal_para_db($this->input->post('nr_familia_municipio', true)),		
				'nr_ieabprev'           => app_decimal_para_db($this->input->post('nr_ieabprev', true)),		
				'nr_meta'               => app_decimal_para_db($this->input->post('nr_meta', true)),
				'observacao'            => $this->input->post('observacao', true),
				'cd_indicador_tabela'   => $tabela[0]['cd_indicador_tabela'],
			    'fl_media'              => 'N',
			    'nr_total_f'            =>
			    ( 
				    app_decimal_para_db($this->input->post('nr_ceee', true))+
				    app_decimal_para_db($this->input->post('nr_aes', true))+
				    app_decimal_para_db($this->input->post('nr_cgtee', true))+
				    app_decimal_para_db($this->input->post('nr_rge', true))+
				    app_decimal_para_db($this->input->post('nr_crm', true))+
				    app_decimal_para_db($this->input->post('nr_senge', true))+
				    app_decimal_para_db($this->input->post('nr_sinpro', true))+
			    	app_decimal_para_db($this->input->post('nr_familia', true))+
			    	app_decimal_para_db($this->input->post('nr_inpel', true))+
			    	app_decimal_para_db($this->input->post('nr_ceran', true))+
			    	app_decimal_para_db($this->input->post('nr_foz', true)) +
			    	app_decimal_para_db($this->input->post('nr_familia_municipio', true))+
			    	app_decimal_para_db($this->input->post('nr_ieabprev', true)) 
			    ),		
				'cd_usuario'			=> $this->session->userdata('codigo')
			);

			$this->atend_participante_model->salvar($args);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_participante', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');	
		}
	}

	public function excluir($cd_atend_participante)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'GCM'))
		{
			$this->atend_participante_model->excluir($cd_atend_participante, $this->session->userdata('codigo'));

			$this->criar_indicador();
			
			redirect('indicador_plugin/atend_participante', 'refresh');
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
	        $data['label_10'] = $this->label_10;
	        $data['label_11'] = $this->label_11;
	        $data['label_12'] = $this->label_12;
	        $data['label_13'] = $this->label_13;
	        $data['label_14'] = $this->label_14;
	        $data['label_15'] = $this->label_15;
	        $data['label_16'] = $this->label_16;

			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";

			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_4']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_8']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_9']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8,0, utf8_encode($data['label_10']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9,0, utf8_encode($data['label_11']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10,0, utf8_encode($data['label_15']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11,0, utf8_encode($data['label_16']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12,0, utf8_encode($data['label_12']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13,0, utf8_encode($data['label_13']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14,0, utf8_encode($data['label_14']), 'background,center');
                
			$collection = $this->atend_participante_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador              = sizeof($collection);
			$indicador             = array();
			$media_ano             = array();
			$a_data 			   = array(0, 0);
			$nr_acumulado_anterior = 0;
			$linha                 = 0;
			$contador_ano_atual    = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia'])-5)
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

						$media_ano[] = $item['nr_total_f'];
					}

					$indicador[$linha][0]  = $referencia;
					$indicador[$linha][1]  = app_decimal_para_php($item['nr_ceee']);
					$indicador[$linha][2]  = app_decimal_para_php($item['nr_aes']);
					$indicador[$linha][3]  = app_decimal_para_php($item['nr_rge']);
					$indicador[$linha][4]  = app_decimal_para_php($item['nr_crm']);
					$indicador[$linha][5]  = app_decimal_para_php($item['nr_senge']);
					$indicador[$linha][6]  = app_decimal_para_php($item['nr_familia']);
					$indicador[$linha][7]  = app_decimal_para_php($item['nr_inpel']);
					$indicador[$linha][8]  = app_decimal_para_php($item['nr_ceran']);
					$indicador[$linha][9]  = app_decimal_para_php($item['nr_foz']);
					$indicador[$linha][10] = app_decimal_para_php($item['nr_familia_municipio']);
					$indicador[$linha][11] = app_decimal_para_php($item['nr_ieabprev']);
					$indicador[$linha][12] = app_decimal_para_php($item["nr_total_f"]);
					$indicador[$linha][13] = app_decimal_para_php($item["nr_meta"]);
                    $indicador[$linha][14] = nl2br($item['observacao']);

					$linha++;
				}
			}

			$linha_sem_media = $linha;

			if(sizeof($media_ano) > 0)
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
				
				$linha++;
				
				$indicador[$linha][0]  = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1]  = app_decimal_para_php($item['nr_ceee']);
                $indicador[$linha][2]  = app_decimal_para_php($item['nr_aes']);
                $indicador[$linha][3]  = app_decimal_para_php($item['nr_rge']);
                $indicador[$linha][4]  = app_decimal_para_php($item['nr_crm']);
                $indicador[$linha][5]  = app_decimal_para_php($item['nr_senge']);
				$indicador[$linha][6]  = app_decimal_para_php($item['nr_familia']);
				$indicador[$linha][7]  = app_decimal_para_php($item['nr_inpel']);
				$indicador[$linha][8]  = app_decimal_para_php($item['nr_ceran']);
				$indicador[$linha][9]  = app_decimal_para_php($item['nr_foz']);
				$indicador[$linha][10] = app_decimal_para_php($item['nr_familia_municipio']);
				$indicador[$linha][11] = app_decimal_para_php($item['nr_ieabprev']);
				$indicador[$linha][12] = app_decimal_para_php($item["nr_total_f"]);
				$indicador[$linha][13] = app_decimal_para_php($item["nr_meta"]);
                $indicador[$linha][14] = '';

            }

			$linha = 1;

			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, app_decimal_para_php($indicador[$i][7]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 8, $linha, app_decimal_para_php($indicador[$i][8]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 9, $linha, app_decimal_para_php($indicador[$i][9]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 10, $linha, app_decimal_para_php($indicador[$i][10]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 11, $linha, app_decimal_para_php($indicador[$i][11]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 12, $linha, app_decimal_para_php($indicador[$i][12]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 13, $linha, app_decimal_para_php($indicador[$i][13]), 'center');
                $sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 14, $linha, utf8_encode($indicador[$i][14]), 'justify');

				$linha++;
			}
				
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'12,12,0,0;13,13,0,0',
				"0,0,1,$linha_sem_media",
				"12,12,1,$linha_sem_media;13,13,1,$linha_sem_media",
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
			$tabela = indicador_tabela_aberta(intval( $this->enum_indicador ));

			$collection = $this->atend_participante_model->listar($tabela[0]['cd_indicador_tabela']);

			$item = $collection[sizeof($collection)-1];

			$nr_total_f = floatval($item["nr_ceee"]) + floatval($item["nr_aes"]) + floatval($item["nr_cgtee"]) + floatval($item["nr_rge"]) + floatval($item["nr_crm"]) +
						  floatval($item["nr_senge"]) + floatval($item["nr_sinpro"]) + floatval($item["nr_familia"]) + floatval($item["nr_inpel"]) +
						  floatval($item["nr_foz"]) + floatval($item["nr_ceran"]) + floatval($item["nr_familia_municipio"]) + floatval($item["nr_ieabprev"]);

			if(floatval($nr_total_f) > 0)
			{
				$args = array(
					'cd_indicador_tabela'  => $tabela[0]['cd_indicador_tabela'],
					'dt_referencia' 	   => '01/01/'.$tabela[0]['nr_ano_referencia'],
					'cd_usuario'           => $this->session->userdata('codigo'),
					'nr_total_f'           => floatval($nr_total_f),
					'nr_meta'              => floatval($item["nr_meta"]),
					'nr_ceee'              => $item["nr_ceee"],
					'nr_aes'               => $item["nr_aes"],
					'nr_cgtee'             => $item["nr_cgtee"],
					'nr_rge'			   => $item["nr_rge"],
					'nr_crm'			   => $item["nr_crm"],
					'nr_senge'			   => $item["nr_senge"],
					'nr_sinpro'			   => $item["nr_sinpro"],
					'nr_familia'		   => $item["nr_familia"],
					'nr_inpel'			   => $item["nr_inpel"],
					'nr_ceran'			   => $item["nr_ceran"],
					'nr_foz'			   => $item["nr_foz"],	 
					'nr_familia_municipio' => $item["nr_familia_municipio"],	 
					'nr_ieabprev'          => $item["nr_ieabprev"]
				);

				$this->atend_participante_model->atualiza_fechar_periodo($args);
			}

			$this->atend_participante_model->fechar_periodo($args);

			redirect('indicador_plugin/atend_participante');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');	
		}
	}
}
?>