<?php
class Secretaria_atas_cd_assinadas_fora_prazo extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::SECRETARIA_ATAS_CD_ASSINADAS_FORA_PRAZO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}		

		$this->cd_usuario = $this->session->userdata('codigo');

		$this->load->model('indicador_plugin/secretaria_atas_cd_assinadas_fora_prazo_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario, 'SG'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/secretaria_atas_cd_assinadas_fora_prazo/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario, 'SG'))
        {			
		 	$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        $data['label_6'] = $this->label_6;
	        $data['label_7'] = $this->label_7;
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->secretaria_atas_cd_assinadas_fora_prazo_model->listar($data['tabela'][0]['cd_indicador_tabela'] );
			
			$this->load->view('indicador_plugin/secretaria_atas_cd_assinadas_fora_prazo/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_secretaria_atas_cd_assinadas_fora_prazo = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'SG'))
		{			
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_4'] = $this->label_4;
			$data['label_6'] = $this->label_6;
	        $data['label_7'] = $this->label_7;

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_secretaria_atas_cd_assinadas_fora_prazo) == 0)
			{
				$row = $this->secretaria_atas_cd_assinadas_fora_prazo_model->carrega_referencia();
				
				$data['row'] = array(
					'cd_secretaria_atas_cd_assinadas_fora_prazo' => intval($cd_secretaria_atas_cd_assinadas_fora_prazo),
				    'nr_valor_1'            					 => '',
				    'nr_valor_2'            					 => '',
				    'fl_media'             					     => '',
				    'observacao'            					 => '',
				    'nr_atas_disp_10_dias'  					 => '',
				    'dt_referencia'         					 => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ""),
				    'nr_meta'               					 => (isset($row['nr_meta']) ? $row['nr_meta'] : 0)
				);
			}			
			else
			{
				$data['row'] = $this->secretaria_atas_cd_assinadas_fora_prazo_model->carrega($cd_secretaria_atas_cd_assinadas_fora_prazo);
			}

			$this->load->view('indicador_plugin/secretaria_atas_cd_assinadas_fora_prazo/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'SG'))
		{		
			$nr_valor_1              = app_decimal_para_db($this->input->post('nr_valor_1', true));
			$nr_valor_2              = app_decimal_para_db($this->input->post('nr_valor_2', true));
			$nr_atas_disp_10_dias    = app_decimal_para_db($this->input->post('nr_atas_disp_10_dias', true));

			$nr_percentual_f         = 0; 
			$nr_percent_disp_10_dias = 0;

			if(floatval($nr_valor_1) > 0)
			{
				$nr_percentual_f         = ($nr_valor_2 / $nr_valor_1) * 100;
				$nr_percent_disp_10_dias = ($nr_atas_disp_10_dias / $nr_valor_1) * 100;
			}
				
			$args = array(
				'cd_secretaria_atas_cd_assinadas_fora_prazo' => intval($this->input->post('cd_secretaria_atas_cd_assinadas_fora_prazo', true)),
			    'cd_indicador_tabela'                        => $this->input->post('cd_indicador_tabela', true),			    
			    'dt_referencia'                              => $this->input->post('dt_referencia', true),
			    'fl_media'                                   => $this->input->post('fl_media', true),
			    'nr_valor_1'                                 => $nr_valor_1,
			    'nr_valor_2'                                 => $nr_valor_2,
			    'nr_meta'                                    => app_decimal_para_db($this->input->post('nr_meta', true)),
			    'nr_atas_disp_10_dias'                       => $nr_atas_disp_10_dias,
			    'nr_percentual_f'		                     => $nr_percentual_f,
			    'nr_percent_disp_10_dias'                    => $nr_percent_disp_10_dias,
                'observacao'                                 => $this->input->post('observacao', true),
			    'cd_usuario'                                 => $this->session->userdata('codigo')
			);

			$this->secretaria_atas_cd_assinadas_fora_prazo_model->salvar($args);
			
			$this->criar_indicador();
	
			redirect('indicador_plugin/secretaria_atas_cd_assinadas_fora_prazo', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_secretaria_atas_cd_assinadas_fora_prazo)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'SG'))
		{
			$this->secretaria_atas_cd_assinadas_fora_prazo_model->excluir($cd_secretaria_atas_cd_assinadas_fora_prazo, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/secretaria_atas_cd_assinadas_fora_prazo', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario, 'SG'))
		{
			$result = null;
		
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
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_6']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7,0, utf8_encode($data['label_7']), 'background,center');
			
			$collection = $this->secretaria_atas_cd_assinadas_fora_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

			$indicador          	 = array();
			$linha              	 = 0;
			$contador_ano_atual 	 = 0;
			$nr_valor_1              = 0;
			$nr_valor_2              = 0;
			$nr_atas_disp_10_dias    = 0;
			$nr_meta                 = 0;
				
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = ' Resultado de ' . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_ano_referencia'];
					}
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']) && (trim($item['fl_media']) != 'S'))
					{
						$contador_ano_atual++;

						$nr_valor_1           += $item['nr_valor_1'];
						$nr_valor_2           += $item['nr_valor_2'];
						$nr_atas_disp_10_dias += $item['nr_atas_disp_10_dias'];
						$nr_meta              = $item['nr_meta'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = app_decimal_para_php($item['nr_valor_1']);
					$indicador[$linha][2] = app_decimal_para_php($item['nr_atas_disp_10_dias']);
					$indicador[$linha][3] = app_decimal_para_php($item['nr_percent_disp_10_dias']);
					$indicador[$linha][4] = app_decimal_para_php($item['nr_valor_2']);
					$indicador[$linha][5] = app_decimal_para_php($item['nr_percentual_f']);
					$indicador[$linha][6] = app_decimal_para_php($item['nr_meta']);
					$indicador[$linha][7] = $item['observacao'];

					$linha++;
				}
			}	
				
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				$nr_percentual_f         = 0; 
				$nr_percent_disp_10_dias = 0;

				if(floatval($nr_valor_1) > 0)
				{
					$nr_percentual_f         = ($nr_valor_2 / $nr_valor_1) * 100;
					$nr_percent_disp_10_dias = ($nr_atas_disp_10_dias / $nr_valor_1) * 100;
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

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_valor_1;
				$indicador[$linha][2] = $nr_atas_disp_10_dias;
				$indicador[$linha][3] = $nr_percent_disp_10_dias;
				$indicador[$linha][4] = $nr_valor_2;
				$indicador[$linha][5] = $nr_percentual_f;
				$indicador[$linha][6] = $nr_meta;
				$indicador[$linha][7] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, app_decimal_para_php($indicador[$i][5]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, app_decimal_para_php($indicador[$i][6]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 7, $linha, utf8_encode(nl2br($indicador[$i][7])));
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;5,5,0,0;6,6,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;5,5,1,$linha_sem_media;6,6,1,$linha_sem_media",
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
		if(indicador_db::verificar_permissao($this->cd_usuario, 'SG'))
		{
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->secretaria_atas_cd_assinadas_fora_prazo_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual   = 0;
			$nr_valor_1           = 0;
			$nr_valor_2           = 0;
			$nr_atas_disp_10_dias = 0;
			$nr_meta              = 0; 

			foreach($collection as $item)
			{		 
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					$nr_valor_1           += $item['nr_valor_1'];
					$nr_valor_2           += $item['nr_valor_2'];
					$nr_atas_disp_10_dias += $item['nr_atas_disp_10_dias'];		
					$nr_meta			  = 0;					 
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$nr_percentual_f         = 0; 
				$nr_percent_disp_10_dias = 0;

				if(floatval($nr_valor_1) > 0)
				{
					$nr_percentual_f         = ($nr_valor_2 / $nr_valor_1) * 100;
					$nr_percent_disp_10_dias = ($nr_atas_disp_10_dias / $nr_valor_1) * 100;
				}
	
				$args = array(
					'cd_secretaria_atas_cd_assinadas_fora_prazo' => 0,
					'dt_referencia'                              => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela'                        => $tabela[0]['cd_indicador_tabela'],
				    'nr_valor_1'			                     => $nr_valor_1,
				    'nr_valor_2'			                     => $nr_valor_2,    
				    'nr_atas_disp_10_dias'                       => $nr_atas_disp_10_dias,
				    'nr_percentual_f'                            => $nr_percentual_f,
				    'nr_percent_disp_10_dias'                    => $nr_percent_disp_10_dias,
				    'nr_meta'                                    => $nr_meta,
				    'fl_media'				                     => 'S',
				    'observacao'                                 => '',
				    'cd_usuario'                                 => $this->cd_usuario
				);

				$this->secretaria_atas_cd_assinadas_fora_prazo_model->salvar($args);
			}

			$this->secretaria_atas_cd_assinadas_fora_prazo_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/secretaria_atas_cd_assinadas_fora_prazo', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>