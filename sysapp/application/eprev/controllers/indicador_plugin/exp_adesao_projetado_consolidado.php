<?php
class Exp_adesao_projetado_consolidado  extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_CONSOLIDADO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}	

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/exp_adesao_projetado_consolidado_model');
    }

    public function index()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));	

			$this->load->view('indicador_plugin/exp_adesao_projetado_consolidado/index',$data);
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
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
	   	       
	        $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			$data['collection'] = $this->exp_adesao_projetado_consolidado_model->listar($data['tabela'][0]['cd_indicador_tabela']);
	        
			$this->load->view('indicador_plugin/exp_adesao_projetado_consolidado/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_exp_adesao_projetado_consolidado = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
						
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			if(intval($cd_exp_adesao_projetado_consolidado) == 0)
			{
				$row = $this->exp_adesao_projetado_consolidado_model->carrega_referencia(intval($data['tabela'][0]['nr_ano_referencia']));
							
				$data['row'] =array(
					'cd_exp_adesao_projetado_consolidado' => $cd_exp_adesao_projetado_consolidado,
					'dt_referencia'					   	  => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ""),
					'nr_meta'					          => (isset($row['nr_meta']) ? $row['nr_meta'] : ""),
					'fl_media'						      => '',
					'ds_observacao'					      => ''		,
				    'qt_ano'                                    => (isset($row['qt_ano']) ? $row['qt_ano'] : 0) 
				);
			}			
			else
			{
				$data['row'] = $this->exp_adesao_projetado_consolidado_model->carrega($cd_exp_adesao_projetado_consolidado);
				$data['row']['qt_ano'] = 1;
			}

			$this->load->view('indicador_plugin/exp_adesao_projetado_consolidado/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{		
			
			$args = array(
				'cd_exp_adesao_projetado_consolidado' => $this->input->post('cd_exp_adesao_projetado_consolidado', true),
				'dt_referencia'					   	  => $this->input->post('dt_referencia', true),
				'cd_indicador_tabela'   		   	  => $this->input->post('cd_indicador_tabela', true),
				'fl_media' 						  	  => $this->input->post('fl_media', true),
				'nr_resultado'					      => $this->input->post('nr_resultado', true),
			    'nr_percentual_f'				      => $this->input->post('nr_percentual_f', true),
				'nr_meta'               		   	  => $this->input->post('nr_meta', true),
				'ds_observacao'            		   	  => $this->input->post('ds_observacao', true),
				'ds_obs_origem'						  =>  $this->input->post('ds_obs_origem', true),
				'cd_usuario'					   	  => $this->cd_usuario
			);

			$this->exp_adesao_projetado_consolidado_model->salvar($args);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_adesao_projetado_consolidado', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_exp_adesao_projetado_consolidado )
	{
		if((indicador_db::verificar_permissao($this->cd_usuario,'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$this->exp_adesao_projetado_consolidado_model->excluir($cd_exp_adesao_projetado_consolidado, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_adesao_projetado_consolidado", "refresh");
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
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
									
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_5']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, utf8_encode($data['label_4']), 'background,center');
						
			$collection = $this->exp_adesao_projetado_consolidado_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;
			$nr_resultado       = 0;
			
			foreach($collection as $item)
			{
				if(trim($item['fl_media']) == 'S')
				{
					$referencia = ' Resultado de ' . $item['ano_referencia'];
				}
				else
				{
					$referencia = $item['mes_ano_referencia'];
				}
				
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					$nr_resultado      += $item['nr_resultado'];
				}

				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($item['nr_meta_ano']);
				$indicador[$linha][2] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][3] = app_decimal_para_php($item['nr_resultado']);				
				$indicador[$linha][4] = app_decimal_para_php($item['nr_percentual_f']);
				$indicador[$linha][5] = $item['ds_observacao'];
				
				$linha++;
				
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
								
				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $item['nr_meta_ano'];
				$indicador[$linha][2] = $item['nr_meta'];
				$indicador[$linha][3] = $item['nr_resultado'];				
				$indicador[$linha][4] = $item['nr_percentual_f'];
				$indicador[$linha][5] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode(nl2br($indicador[$i][5])), 'justify');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'2,2,0,0;3,3,0,0',
				"0,0,1,$linha_sem_media",
				"2,2,1,$linha_sem_media;3,3,1,$linha_sem_media-linha",
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
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GN')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->exp_adesao_projetado_consolidado_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;
			$nr_resultado       = 0;
			$nr_meta            = 0;

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				$args = array(
					'cd_exp_adesao_projetado_consolidado' => 0,
					'dt_referencia'         		      => '01/12/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela'   		      => $tabela[0]['cd_indicador_tabela'],
					'nr_resultado'					      => $item['nr_resultado'],
					'nr_percentual_f'				      => $item['nr_percentual_f'],
					'nr_meta'						      => $item['nr_meta'],
					'fl_media'			    		      => 'S',
				    'ds_observacao'            		      => '',
				    'ds_obs_origem'						  => trim($item['ds_observacao']),
				    'nr_ceeeprev_meta'                    => $item['nr_ceeeprev_meta'],
					'nr_ceeeprev_resultado'               => $item['nr_ceeeprev_resultado'],
					'nr_ceeeprev_atingido'                => $item['nr_ceeeprev_atingido'],
					'nr_crmprev_meta'                     => $item['nr_crmprev_meta'],
					'nr_crmprev_resultado'                => $item['nr_crmprev_resultado'],
					'nr_crmprev_atingido'                 => $item['nr_crmprev_atingido'],
					'nr_inpelprev_meta'                   => $item['nr_inpelprev_meta'],
					'nr_inpelprev_resultado'              => $item['nr_inpelprev_resultado'],
					'nr_inpelprev_atingido'               => $item['nr_inpelprev_atingido'],
					'nr_senge_meta'                       => $item['nr_senge_meta'],
				    'nr_senge_resultado'                  => $item['nr_senge_resultado'],  
					'nr_senge_atingido'                   => $item['nr_senge_atingido'],
					'nr_sinpro_meta'                      => $item['nr_sinpro_meta'],
					'nr_sinpro_resultado'                 => $item['nr_sinpro_resultado'],
					'nr_sinpro_atingido'                  => $item['nr_sinpro_atingido'],
					'nr_familia_meta'                     => $item['nr_familia_meta'],
					'nr_familia_resultado'                => $item['nr_familia_resultado'],
					'nr_familia_atingido'                 => $item['nr_familia_atingido'],
					'nr_fozprev_meta'                     => $item['nr_fozprev_meta'],
					'nr_fozprev_resultado'                => $item['nr_fozprev_resultado'],
					'nr_fozprev_atingido'                 => $item['nr_fozprev_atingido'],
					'nr_unico_meta'                       => $item['nr_unico_meta'],
					'nr_unico_resultado'                  => $item['nr_unico_resultado'],
					'nr_unico_atingido'                   => $item['nr_unico_atingido'],
					'nr_ceranprev_meta'                   => $item['nr_ceranprev_meta'],
					'nr_ceranprev_resultado'              => $item['nr_ceranprev_resultado'],
					'nr_ceranprev_atingido'               => $item['nr_ceranprev_atingido'],
				    'cd_usuario'            		      => $this->cd_usuario
				);

				$this->exp_adesao_projetado_consolidado_model->fechamento($args);
			}

			$this->exp_adesao_projetado_consolidado_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/exp_adesao_projetado_consolidado', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>