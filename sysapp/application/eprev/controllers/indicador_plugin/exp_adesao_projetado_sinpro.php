<?php
class exp_adesao_projetado_sinpro extends Controller
{	
    var $enum_indicador = 0;
		
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::EXP_NUMERO_DE_ADESAO_X_NUMERO_PROJETADO_SINPRO);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{"label_".$ar_item['id_label']} = $ar_item['ds_label'];
		}	

		$this->cd_usuario = $this->session->userdata('codigo');	

		$this->load->model('indicador_plugin/exp_adesao_projetado_sinpro_model');
    }

    public function index()
    {
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GCM')) OR ($this->session->userdata('indic_12') == '*'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}

			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));	

			$this->load->view('indicador_plugin/exp_adesao_projetado_sinpro/index',$data);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	if((indicador_db::verificar_permissao($this->cd_usuario, 'GCM')) OR ($this->session->userdata('indic_12') == '*'))
        {
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	       
	        $data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));

			$data['collection'] = $this->exp_adesao_projetado_sinpro_model->listar($data['tabela'][0]['cd_indicador_tabela']);
	        
			$this->load->view('indicador_plugin/exp_adesao_projetado_sinpro/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_exp_adesao_projetado_sinpro = 0)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GCM')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
			
			if(intval($cd_exp_adesao_projetado_sinpro) == 0)
			{
				$row = $this->exp_adesao_projetado_sinpro_model->carrega_referencia();
								
				$data['row'] =array(
					'cd_exp_adesao_projetado_sinpro' => $cd_exp_adesao_projetado_sinpro,
					'dt_referencia'					   => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ""),
					'nr_valor_1'					   => (isset($row['nr_valor_1']) ? $row['nr_valor_1'] : ""),
					'nr_percentual_f'	      		   => 0,
					'fl_media'						   => '',
					'observacao'					   => ''		
				);
			}			
			else
			{
				$data['row'] = $this->exp_adesao_projetado_sinpro_model->carrega($cd_exp_adesao_projetado_sinpro);
			}

			$this->load->view('indicador_plugin/exp_adesao_projetado_sinpro/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function salvar()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GCM')) OR ($this->session->userdata('indic_12') == '*'))
		{		
			$nr_valor_1      = app_decimal_para_db($this->input->post('nr_valor_1', true));
			$nr_percentual_f = app_decimal_para_db($this->input->post('nr_percentual_f', true));
			$nr_meta		 = 0;

			if(floatval($nr_valor_1) > 0)
			{
				$nr_meta = (($nr_percentual_f/$nr_valor_1)*100);
			}

			$args = array(
				'cd_exp_adesao_projetado_sinpro' => $this->input->post('cd_exp_adesao_projetado_sinpro', true),
				'dt_referencia'					   => $this->input->post('dt_referencia', true),
				'cd_indicador_tabela'   		   => $this->input->post('cd_indicador_tabela', true),
				'fl_media' 						   => $this->input->post('fl_media', true),
				'nr_valor_1'            		   => $nr_valor_1,
				'nr_percentual_f'				   => $nr_percentual_f,
				'nr_meta'               		   => $nr_meta,
				'observacao'            		   => $this->input->post('observacao', true),
				'cd_usuario'					   => $this->cd_usuario
			);

			$this->exp_adesao_projetado_sinpro_model->salvar($args);
			
			$this->criar_indicador();
			
			redirect('indicador_plugin/exp_adesao_projetado_sinpro', 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}
	
	public function excluir($cd_exp_adesao_projetado_sinpro)
	{
		if((indicador_db::verificar_permissao($this->cd_usuario,'GCM')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$this->exp_adesao_projetado_sinpro_model->excluir($cd_exp_adesao_projetado_sinpro, $this->cd_usuario);
			
			$this->criar_indicador();
			
			redirect("indicador_plugin/exp_adesao_projetado_sinpro", "refresh");
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

	public function criar_indicador()
	{
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GCM')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
						
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
						
			$collection = $this->exp_adesao_projetado_sinpro_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador          = array();
			$linha              = 0;
			$contador_ano_atual = 0;
			$nr_valor_1         = 0;
			$nr_percentual_f    = 0;
			$nr_meta            = 0;

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
					$nr_valor_1      += intval($item['nr_valor_1']);
					$nr_percentual_f += intval($item['nr_percentual_f']);
				}

				$indicador[$linha][0] = $referencia;
				$indicador[$linha][1] = app_decimal_para_php($item['nr_valor_1']);
				$indicador[$linha][2] = app_decimal_para_php($item['nr_percentual_f']);
				$indicador[$linha][3] = app_decimal_para_php($item['nr_meta']);
				$indicador[$linha][4] = $item['observacao'];
				
				$linha++;
				
			}

			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{
				if(floatval($nr_valor_1) > 0)
				{
					$nr_meta = (($nr_percentual_f / $nr_valor_1) * 100);
				}

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				
				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_valor_1;
				$indicador[$linha][2] = $nr_percentual_f;
				$indicador[$linha][3] = $nr_meta;
				$indicador[$linha][4] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S' );
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, utf8_encode(nl2br($indicador[$i][4])), 'justify');
				
				$linha++;
			}

			// gerar gráfico
			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'1,1,0,0;2,2,0,0',
				"0,0,1,$linha_sem_media",
				"1,1,1,$linha_sem_media;2,2,1,$linha_sem_media-linha",
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
		if((indicador_db::verificar_permissao($this->cd_usuario, 'GCM')) OR ($this->session->userdata('indic_12') == '*'))
		{
			$tabela = indicador_tabela_aberta(intval($this->enum_indicador));

			$collection = $this->exp_adesao_projetado_sinpro_model->listar($tabela[0]['cd_indicador_tabela']);

			$contador_ano_atual = 0;
			$nr_valor_1         = 0;
			$nr_percentual_f    = 0;
			$nr_meta            = 0;

			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
				{
					$contador_ano_atual++;
					$nr_valor_1      += $item['nr_valor_1'];
					$nr_percentual_f += $item['nr_percentual_f'];
				}
			}

			if(intval($contador_ano_atual) > 0)
			{
				if(floatval($nr_valor_1) > 0)
				{
					$nr_meta = (($nr_percentual_f / $nr_valor_1) * 100);
				}


				$args = array(
					'cd_exp_adesao_projetado_sinpro' => 0,
					'dt_referencia'         		   => '01/01/'.intval($tabela[0]['nr_ano_referencia']),
					'cd_indicador_tabela'   		   => $tabela[0]['cd_indicador_tabela'],
				    'nr_valor_1'		    		   => $nr_valor_1,
				    'nr_percentual_f'       		   => $nr_percentual_f,
				    'nr_meta'               		   => $nr_meta,
				    'fl_media'			    		   => 'S',
				    'observacao'            		   => '',
				    'cd_usuario'            		   => $this->cd_usuario
				);

				$this->exp_adesao_projetado_sinpro_model->salvar($args);
			}

			$this->exp_adesao_projetado_sinpro_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/ ', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}
?>